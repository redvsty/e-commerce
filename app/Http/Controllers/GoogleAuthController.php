<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use SebastianBergmann\CodeCoverage\Driver\Driver;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle()
    {
        try {

            $google_user = Socialite::driver('google')->user();
            Log::info("......................>>>>>>>");
            Log::info("id google " . $google_user->getId());
            Log::info("email google " . $google_user->getEmail());

            $user = User::where('google_id', $google_user->getId())->first();
            Log::info("----------------------------->");
            Log::info(var_dump($google_user));

            if (!$user) {
                $new_user = User::create([
                    'name' => $google_user->getName(),
                    'email' => $google_user->getEmail(),
                    'google_id' => $google_user->getId(),
                    'balance' => '5000',
                ]);
                Auth::login($new_user);

                return redirect()->intended('/admin');
            } else {
                Auth::login($user);

                return redirect()->intended('/');
            }


        } catch (\Throwable $th) {
            dd("Something went wrong!", $th->getMessage());
        }
    }
}