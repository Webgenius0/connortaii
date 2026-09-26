<?php

namespace App\Http\Controllers\Api\Auth;

use App\Contracts\RegistrationOtpInterface;
use App\Http\Controllers\Controller;
use App\Mail\RegistrationOtp;
use App\Models\EmailOtp;
use App\Models\User;
use App\Services\AvatarUploadService;
use App\Services\NotificationService;
use App\Services\UserRegistrationService;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use ApiResponse;

    public function __construct(
        private RegistrationOtpInterface $otpService,
        private AvatarUploadService    $avatarService,
        private UserRegistrationService $registrationService,
    ) {
    }

    public function userRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email',
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'agree_to_terms' => 'required|boolean',
            'avatar'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }

        if (User::where('email', $request->email)->exists()) {
            return $this->error(['email' => ['The email has already been taken.']], "Validation Error", 422);
        }
        $avatarPath = $this->avatarService->upload($request->file('avatar'));
        $data = $request->only('first_name', 'last_name', 'email', 'password', 'phone_code', 'phone', 'agree_to_terms');
        $data['avatar'] = $avatarPath;

        $code = $this->otpService->generate($request->email, $data);

        // Mail::to($request->email)->send(new RegistrationOtp(..., $code));

        return $this->success(['otp' => $code], 'An OTP has been sent to your email', 200);
    }

    public function otpVerify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'        => 'required|email|exists:email_otps,email',
            'otp'          => 'required|numeric|digits:4',
            'device_token' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }

        $isValid = $this->otpService->verify($request->email, $request->otp);
        if (!$isValid) {
            return $this->error([], 'Invalid or expired OTP', 400);
        }

        $tempUser = EmailOtp::where('email', $request->email)->first();
        $user = $this->registrationService->createFromOtp($tempUser);

        if ($request->filled('device_token')) {
            $user->device_token = $request->device_token;
            $user->save();
            NotificationService::sendWelcomeNotification($user);
        }

        $tempUser->delete();

        $token = $this->registrationService->generateToken($user);
        $user->setAttribute('token', $token);

        return $this->success($user, 'OTP verified successfully', 200);
    }

    public function otpResend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:email_otps,email',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }

        $tempUser = EmailOtp::where('email', $request->email)->first();
        $code = $this->otpService->resend($request->email);
        // Mail::to($request->email)->send(new RegistrationOtp($tempUser, $code));

        return $this->success(['otp' => $code], 'OTP resent successfully.', 200);
    }

    public function emailExists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), "Validation Error", 422);
        }

        $taken = User::where('email', $request->email)->exists()
            || $this->otpService->getActiveOtp($request->email) !== null;

        if ($taken) {
            return $this->error(['email' => ['The email has already been taken.']], "Validation Error", 422);
        }

        return $this->success([], 'Email is available', 200);
    }
}
