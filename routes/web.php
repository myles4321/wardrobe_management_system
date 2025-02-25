<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ClothingController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/clothings', [ClothingController::class, 'store'])->name('clothings.store');
    Route::get('/clothings', [ClothingController::class, 'index'])->name('clothings.index');
    Route::put('/clothings/{clothing}', [ClothingController::class, 'update'])->name('clothings.update');
    Route::delete('/clothings/{clothing}', [ClothingController::class, 'destroy'])->name('clothings.destroy');


    
});

Route::get('/homepage', function() {
    return Inertia::render('Homepage', [
        'clothings' => Auth::user()->clothings
    ]);
})->middleware('auth')->name('homepage');

require __DIR__.'/auth.php';

