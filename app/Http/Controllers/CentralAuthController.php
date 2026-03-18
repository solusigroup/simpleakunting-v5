<?php

namespace App\Http\Controllers;

use App\Models\CentralUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CentralAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('central.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nama_user' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $user = CentralUser::where('nama_user', $credentials['nama_user'])->first();

        if ($user && Hash::check($credentials['password'], $user->password_hash)) {
            Auth::guard('central')->login($user);
            $request->session()->regenerate();

            // Redirect to the central tenants index (dashboard for superuser based on web.php)
            return redirect()->route('central.tenants.index');
        }

        return back()->withErrors([
            'nama_user' => 'Kredensial yang diberikan tidak cocok dengan data kami.',
        ])->onlyInput('nama_user');
    }

    public function logout(Request $request)
    {
        Auth::guard('central')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('central.landing');
    }
}
