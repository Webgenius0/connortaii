<?php

namespace App\Http\Controllers\Api\Auth;

use App\Contracts\AuthServiceInterface;
use App\Contracts\OtpServiceInterface;
use App\Contracts\UserOtpInterface;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordOtp;
use App\Mail\RegistrationOtp;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    use ApiResponse;

    public function __construct(
        private UserOtpInterface  $otpService,
        private AuthServiceInterface $authService,
    ) {
    }

    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->error([], $validator->errors()->first(), 422);
        }

        $user = $this->authService->attemptLogin($request->email, $request->password);

        if (!$user) {
            return $this->error([], 'Invalid credentials', 401);
        }

        if ($request->filled('device_token')) {
            $user->device_token = $request->device_token;
            $user->save();
        }

        // Email verify হয়নি
        if (is_null($user->email_verified_at)) {
            $code = $this->otpService->generateForUser($user);
            Mail::to($user->email)->send(new RegistrationOtp($user, $code));
            $user->setAttribute('token', null);
            return $this->success($user, 'User authenticated successfully', 200);
        }

        $token = $this->authService->generateToken($user);
        $user->setAttribute('token', $token);

        return $this->success($user, 'User authenticated successfully', 200);
    }
    public function emailVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }

        $user = User::where('email', $request->email)->first();
        $code = $this->otpService->generateForUser($user);
        // Mail::to($user->email)->send(new ForgotPasswordOtp($user, $code));

        return $this->success(['otp' => $code], 'OTP has been sent successfully.', 200);
    }

    public function otpResend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }

        $user = User::where('email', $request->email)->first();
        $code = $this->otpService->generateForUser($user);
        // Mail::to($user->email)->send(new ForgotPasswordOtp($user, $code));

        return $this->success(['otp' => $code], 'OTP has been sent successfully.', 200);
    }

    public function otpVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp'   => 'required|numeric|digits:4',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }

        $user = User::where('email', $request->email)->first();
        $isValid = $this->otpService->verifyForUser($user, $request->otp);

        if (!$isValid) {
            return $this->error([], 'Invalid or expired OTP', 400);
        }

        // email_verified_at এখানে সেট করবেন না — এটা registration এর জন্য, forgot-password এর জন্য না
        $resetToken = \Illuminate\Support\Str::random(60);

        \App\Models\EmailOtp::where('user_id', $user->id)->update([
            'reset_token' => $resetToken,
        ]);

        return $this->success(['reset_token' => $resetToken], 'OTP Verified Successfully', 200);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'       => 'required|email|exists:users,email',
            'reset_token' => 'required|string',
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'The password must be at least 8 characters long.',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }

        $user = User::where('email', $request->email)->first();

        $validOtp = \App\Models\EmailOtp::where('user_id', $user->id)
            ->where('reset_token', $request->reset_token)
            ->first();

        if (!$validOtp) {
            return $this->error([], 'Invalid or expired reset token', 400);
        }

        $this->authService->resetPassword($user, $request->password);

        // token একবার ব্যবহারের পর invalidate করে দিন
        $validOtp->update(['reset_token' => null]);

        return $this->success([], 'Password Reset successfully.', 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success([], 'Logged out successfully', 200);
    }
}
