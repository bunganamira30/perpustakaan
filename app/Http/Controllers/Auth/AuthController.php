<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Member;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View
    {
        return view('auth.login', ['title' => 'Masuk']);
    }

    /**
     * Handle an authentication attempt.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $throttleKey = $request->throttleKey();

        // Rate limiting: max 5 attempts per minute
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withInput($request->only('username', 'remember'))->withErrors([
                'username' => "Terlalu banyak percobaan login. Silakan tunggu {$seconds} detik.",
            ]);
        }

        $input = $request->input('username');
        $loginField = filter_var($input, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginField => $input,
            'password' => $request->input('password'),
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            return redirect()->intended('/books')->with(
                'success',
                'Selamat datang kembali, ' . Auth::user()->name . '!'
            );
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withInput($request->only('username', 'remember'))->withErrors([
            'username' => 'Kredensial yang Anda masukkan tidak cocok dengan data kami.',
        ]);
    }

    /**
     * Show the registration form.
     */
    public function showRegisterForm(): View
    {
        return view('auth.register', ['title' => 'Pendaftaran Anggota']);
    }

    /**
     * Handle registration of a new user and member profile.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->input('full_name'),
                'username' => $request->input('username'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password')),
                'role' => 'member',
            ]);

            Member::create([
                'user_id' => $user->id,
                'full_name' => $request->input('full_name'),
                'phone_encrypted' => $request->input('phone'),
                'address' => $request->input('address'),
            ]);

            Auth::login($user);
            $request->session()->regenerate();
        });

        return redirect('/books')->with(
            'success',
            'Pendaftaran anggota berhasil! Nomor telepon Anda telah diamankan dengan enkripsi AES-256-GCM.'
        );
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('info', 'Anda telah berhasil keluar dari sistem perpustakaan.');
    }
}
