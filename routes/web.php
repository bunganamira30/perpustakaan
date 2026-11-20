<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

// Beranda: Arahkan ke katalog buku
Route::get('/', function () {
    return redirect()->route('books.index');
});

// Route books.index (akan dikembangkan lengkap pada TKT-007)
Route::get('/books', function () {
    return view('layouts.app', [
        'title' => 'Katalog Pustaka'
    ]);
})->name('books.index');

// Rute Tamu (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rute Terautentikasi (Auth Only)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
