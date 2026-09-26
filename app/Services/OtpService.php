<?php

namespace App\Services;

use App\Contracts\RegistrationOtpInterface;
use App\Contracts\UserOtpInterface;
use App\Models\EmailOtp;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OtpService implements RegistrationOtpInterface, UserOtpInterface
{
    public function generate(string $email, array $data): int
    {
        $code = rand(1000, 9999);

        EmailOtp::updateOrCreate(
            ['email' => $email],
            [
                'first_name'        => $data['first_name'],
                'last_name'         => $data['last_name'],
                'password'          => Hash::make($data['password']),
                'phone_code'        => $data['phone_code'] ?? null,
                'phone'             => $data['phone'] ?? null,
                'avatar'            => $data['avatar'] ?? null,
                'agree_to_terms'    => $data['agree_to_terms'] ?? null,
                'verification_code' => $code,
                'expires_at'        => Carbon::now('UTC')->addYears(10),
                'user_id'           => null,
            ]
        );

        return $code;
    }

    public function verify(string $email, int $code): bool
    {
        return EmailOtp::where('email', $email)
            ->where('verification_code', $code)
            ->exists();
    }

    public function resend(string $email): int
    {
        $code = rand(1000, 9999);

        EmailOtp::where('email', $email)->update([
            'verification_code' => $code,
        ]);

        return $code;
    }

    public function getActiveOtp(string $email): ?EmailOtp
    {
        return EmailOtp::where('email', $email)
            ->first();
    }

    public function generateForUser(User $user): int
    {
        $code = rand(1000, 9999);

        EmailOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'first_name'        => $user->first_name,
                'last_name'         => $user->last_name,
                'email'             => $user->email,
                'password'          => $user->password,
                'avatar'            => $user->avatar ?? null,
                'verification_code' => $code,
                'expires_at'        => Carbon::now('UTC')->addYears(10),
            ]
        );

        return $code;
    }

    public function verifyForUser(User $user, int $code): bool
    {
        return EmailOtp::where('user_id', $user->id)
            ->where('verification_code', $code)
            ->exists();
    }
}
