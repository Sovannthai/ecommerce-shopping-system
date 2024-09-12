<?php
namespace App\Http\Controllers\Backends;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $findUser = User::where('google_id', $googleUser->id)
                ->orWhere('email', $googleUser->email)
                ->first();

            if ($findUser) {
                // Log::info($request->all());
                Auth::login($findUser);
                return redirect()->intended('home');
            } else {
                // Log::info($request->all());
                $newUser = User::updateOrCreate(
                    ['email' => $googleUser->email],
                    [
                        'name' => $googleUser->name,
                        'google_id' => $googleUser->id,
                        'avatar' => $googleUser->avatar,   // Store Google avatar
                        'token' => $googleUser->token,     // Store Google token
                        'password' => encrypt('123456dummy')  // Set a dummy password
                    ]
                );
                Auth::login($newUser);
                return redirect()->intended('home');
            }

        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}
