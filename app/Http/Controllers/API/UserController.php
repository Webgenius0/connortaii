<?php

namespace App\Http\Controllers\Api;

use App\Contracts\AuthServiceInterface;
use App\Http\Controllers\Controller;
use App\Services\AvatarUploadService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ApiResponse;

    public function __construct(
        private AvatarUploadService  $avatarService,
        private AuthServiceInterface $authService,
    ) {
    }

    public function userDetails(Request $request)
    {
        return $this->success(Auth::user(), 'User Data fetch Successful!', 200);
    }

    public function updateUser(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'sometimes|nullable|string|max:255',
            'last_name'  => 'sometimes|nullable|string|max:255',
            'avatar'     => 'nullable|image|mimes:jpeg,png,jpg,svg|max:20480',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        if ($request->filled('first_name')) {
            $user->first_name = $request->first_name;
        }

        if ($request->filled('last_name')) {
            $user->last_name = $request->last_name;
        }

        if ($request->hasFile('avatar')) {
            $user->avatar = $this->avatarService->upload($request->file('avatar'));
        }

        $user->save();

        return $this->success($user, 'Profile updated successfully', 200);
    }
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation Error', 422);
        }

        if (!$this->authService->checkPassword($user, $request->old_password)) {
            return $this->error([], 'Old password is incorrect', 400);
        }

        $this->authService->resetPassword($user, $request->new_password);

        return $this->success([], 'Password updated successfully', 200);
    }

    public function logoutUser(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success([], 'User logged out successfully', 200);
    }

    public function deleteAccount(Request $request)
    {
        $user = Auth::user();

        $user->delete();

        return $this->success([], 'Your account has been deleted successfully', 200);
    }
}
