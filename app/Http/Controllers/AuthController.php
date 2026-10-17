<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Kredensial yang dimasukkan tidak cocok dengan catatan kami.',
        ])->onlyInput('email');
    }

    public function quickLogin($role)
    {
        $allowedRoles = ['admin', 'pm', 'developer', 'finance', 'client'];
        if (!in_array($role, $allowedRoles)) {
            return redirect()->route('login')->with('error', 'Role tidak valid.');
        }

        $user = User::where('role', $role)->first();
        if (!$user) {
            return redirect()->route('login')->with('error', "User dengan role {$role} belum dibuat.");
        }

        Auth::login($user);
        request()->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar.');
    }

    protected function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->route('dashboard.admin'),
            'pm' => redirect()->route('dashboard.pm'),
            'developer' => redirect()->route('dashboard.developer'),
            'finance' => redirect()->route('dashboard.finance'),
            'client' => redirect()->route('dashboard.client'),
            default => redirect()->route('home'),
        };
    }
}
