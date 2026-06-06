<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class EmailVerificationController extends BaseApiController
{
    public function verify(Request $request, $id, $hash)
    {
        $user = \App\Models\User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return $this->error('Invalid verification link.', 403);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->success(null, 'Email already verified.');
        }

        $user->markEmailAsVerified();

        return $this->success(null, 'Email successfully verified.');
    }

    public function resend(Request $request)
    {
        $user = auth('api')->user();

        if ($user->hasVerifiedEmail()) {
            return $this->error('Email already verified.', 400);
        }

        // Generate verification link and send email manually or using notification
        // For simplicity, we can send a custom mailable VerifyEmail
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );

        // Replace frontend base URL if necessary
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        $parsedUrl = parse_url($verificationUrl);
        $customVerificationUrl = $frontendUrl . '/verify-email?' . $parsedUrl['query'] . '&id=' . $user->getKey() . '&hash=' . sha1($user->getEmailForVerification());


        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\VerifyEmail($user, $customVerificationUrl));

        return $this->success(null, 'Verification link sent!');
    }
}
