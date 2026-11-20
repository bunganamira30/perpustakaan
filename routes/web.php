<?php

use App\Http\Controllers\Admin\AdminBookController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BookController;
use Illuminate\Support\Facades\Route;

// Beranda: Arahkan ke katalog buku
Route::get('/', function () {
    return redirect()->route('books.index');
});

// Katalog Buku Publik & Pencarian Aman (SEC-08 Safe)
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');

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

    // Placeholder rute anggota (akan diimplementasikan penuh pada TKT-008 & TKT-009)
    Route::get('/my-borrowings', function () {
        return redirect()->route('books.index');
    })->name('my-borrowings');
});

// Panel Manajemen Khusus Administrator (Role Admin Only)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/books', [AdminBookController::class, 'index'])->name('books.index');
    Route::get('/books/create', [AdminBookController::class, 'create'])->name('books.create');
    Route::post('/books', [AdminBookController::class, 'store'])->name('books.store');
    Route::get('/books/{id}/edit', [AdminBookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{id}', [AdminBookController::class, 'update'])->name('books.update');
    Route::delete('/books/{id}', [AdminBookController::class, 'archive'])->name('books.archive');
});
