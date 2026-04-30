<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SsoController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('sipetra')->redirect();
    }

    public function callback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('login')->with('error', 'Login SSO Dibatalkan');
        }

        try {
            $ssoUser = Socialite::driver('sipetra')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal mengambil data user: ' . $e->getMessage());
        }

        $rawData = $ssoUser->getRaw();

        // Cari user berdasarkan sipetra_id atau email
        $user = User::where('sipetra_id', $ssoUser->getId())->first()
             ?? User::where('email', $ssoUser->getEmail())->first();

        $data = [
            'sipetra_id'    => $ssoUser->getId(),
            'name'          => $ssoUser->getName(),
            'email'         => $ssoUser->getEmail(),
            'sipetra_token' => $ssoUser->token,
            'nip'           => $rawData['nip'] ?? null,
            'jabatan'       => $rawData['jabatan'] ?? null,
        ];

        if ($user) {
            $user->update($data);
        } else {
            $data['password'] = null; // Or bcrypt(Str::random(16))
            $user = User::create($data);
            
            // Assign role default (jika pakai Spatie Permission)
            if (method_exists($user, 'assignRole')) {
                // Check if 'pegawai' role exists, if not maybe just 'panel_user' or whatever is available
                try {
                    $user->assignRole('pegawai');
                } catch (\Exception $e) {
                    // Handle if role doesn't exist
                }
            }
        }

        Auth::login($user);
        return redirect()->intended('/admin');
    }
}
