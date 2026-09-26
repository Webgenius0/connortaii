<?php

namespace App\Services;

use App\Models\EmailOtp;
use App\Models\User;

class UserRegistrationService
{
    public function createFromOtp(EmailOtp $tempUser): User
    {
        return User::create([
            'first_name'        => $tempUser->first_name,
            'last_name'         => $tempUser->last_name,
            'email'             => $tempUser->email,
            'password'          => $tempUser->password,
            'email_verified_at' => now(),
            'agree_to_terms' =>  $tempUser->agree_to_terms,
            'avatar'            => $tempUser->avatar,
        ]);
    }

    public function generateToken(User $user): string
    {
        return $user->createToken('auth_token')->plainTextToken;
    }
}
