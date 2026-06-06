<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;

class OAuthController extends BaseApiController
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function redirectToGoogle()
    {
        $url = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        
        return $this->success([
            'url' => $url
        ], 'Redirect URL generated successfully');
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // Find user by email or google_id
            $user = User::where('email', $googleUser->getEmail())
                        ->orWhere('google_id', $googleUser->getId())
                        ->first();
                        
            if ($user) {
                // If user exists but doesn't have google_id, link it
                if (!$user->google_id) {
                    $user->google_id = $googleUser->getId();
                    // Auto-verify email if not verified since Google verified it
                    if (!$user->email_verified_at) {
                        $user->email_verified_at = now();
                    }
                    $user->save();
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName() ?? 'Google User',
                    'email' => $googleUser->getEmail(),
                    'username' => strtolower(str_replace(' ', '', $googleUser->getName())) . rand(1000, 9999),
                    'password' => null, // No password for OAuth users
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(), // Auto-verified
                    'is_profile_complete' => false,
                    'is_active' => true,
                ]);

                // Create empty profile
                Profile::create([
                    'user_id' => $user->id,
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            // Generate JWT token
            $token = auth('api')->login($user);
            
            // Redirect to frontend with token
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/auth/oauth-callback?token=' . $token);

        } catch (\Exception $e) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/auth/login?error=oauth_failed');
        }
    }
}
