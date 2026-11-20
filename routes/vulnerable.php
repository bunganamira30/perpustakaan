<?php

use App\Http\Controllers\Vulnerable\VulnerableCirculationController;
use Illuminate\Support\Facades\Route;

// Rute Modul Demo Celah Rentan Terisolasi (Hanya Localhost Loopback 127.0.0.1:8001)
Route::prefix('vuln')->name('vuln.')->group(function () {
    // 1. Celah SQL Injection (Syarat 8 / SEC-08)
    Route::get('/books', [VulnerableCirculationController::class, 'booksIndex'])->name('books.index');

    // 2. Celah Stored XSS (Syarat 9 / SEC-09)
    Route::get('/books/{id}', [VulnerableCirculationController::class, 'bookShow'])->name('books.show');
    Route::post('/books/{id}/reviews', [VulnerableCirculationController::class, 'reviewStore'])->name('reviews.store');

    // 3. Celah Insecure Direct Object Reference (IDOR - Syarat 10 / SEC-10)
    Route::get('/borrowings/{id}', [VulnerableCirculationController::class, 'borrowingShow'])->name('borrowings.show');
});
