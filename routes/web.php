<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BlindboxController;
use App\Http\Controllers\SerieController;
use Illuminate\Support\Facades\Route;

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get('/', function () {
    return view('about');
})->name('about');

Route::resource('blindboxes', BlindboxController::class);
Route::post('blindboxes/{blindbox}/favorite', [BlindboxController::class, 'toggleFavorite'])
    ->name('blindboxes.favorite');
Route::resource('series', SerieController::class);

Route::get('/dashboard', function () {
    return redirect()->route('blindboxes.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/blindboxes', [App\Http\Controllers\AdminBlindboxController::class, 'index'])
        ->name('admin.blindboxes.index');

    Route::post('/admin/blindboxes/{blindbox}/toggle', [App\Http\Controllers\AdminBlindboxController::class, 'togglePublic'])
        ->name('admin.blindboxes.toggle');
});

require __DIR__ . '/auth.php';
