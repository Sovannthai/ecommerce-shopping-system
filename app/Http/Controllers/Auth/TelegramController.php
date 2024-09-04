<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Notifications\TelegramNotification;
use Illuminate\Support\Facades\Notification;

class TelegramController extends Controller
{
    public function telegramLogin(Request $request)
    {
        // Log::info($request->all());
        $user = User::updateOrCreate(
            [
                'phone' => $request->phone,
                'telegram_id' => $request->telegram_id
            ],
            [
                'name' => $request->name,
                'username' => '@' . $request->username,
                'avatar' => $request->avatar,
                'access_token' => $request->hash,
                'password' => bcrypt($request->phone),
                'phone' => $request->phone,
                'user_type' => 'login_with_telegram',
                'email' => $request->email ?? $request->phone . '@placeholder.com',
            ]
        );
        if ($user->wasRecentlyCreated) {
            $role = Role::findOrFail(8);
            $user->assignRole($role->name);
        }
        if (Auth::attempt(['email' => $request->email ?? $request->phone . '@placeholder.com', 'password' => $request->phone])) {
            return redirect()->route('home');
        } else {
            return response()->json(['message' => 'Login failed.'], 401);
        }
    }
    public function telegramAuthCallback(Request $request)
    {
        $telegram_user = Socialite::driver('telegram')->user();
        $user = User::where('telegram_id', $telegram_user->id)->first();
        if ($user) {
            Auth::attempt(['email' => $user->email, 'password' => $user->phone]);
            return redirect()->route('home');
        } else {
            Notification::route('telegram', $telegram_user->id)
                ->notify(new TelegramNotification());
            return view('auth.telegram_confirm_login', compact('telegram_user'));
        }
    }


}
