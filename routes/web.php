<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::get('/api/locations', [LocationController::class, 'index'])->name('locations.index');
Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/listings/{listing}', [CatalogController::class, 'show'])->whereNumber('listing')->name('listings.show');

Route::get('/dashboard', function () {
    return redirect()->route('catalog.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/my-listings', [ListingController::class, 'mine'])->name('listings.mine');
    Route::get('/listings/create', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/listings', [ListingController::class, 'store'])->name('listings.store');
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->whereNumber('listing')->name('listings.edit');
    Route::match(['put', 'patch'], '/listings/{listing}', [ListingController::class, 'update'])->whereNumber('listing')->name('listings.update');
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->whereNumber('listing')->name('listings.destroy');
    Route::patch('/listings/{listing}/status', [ListingController::class, 'status'])->whereNumber('listing')->name('listings.status');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
