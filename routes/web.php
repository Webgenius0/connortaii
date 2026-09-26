<?php

use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');

// Route::get('/dashboard', [DashBoardController::class, 'index'])
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/delete-account', function () {
        return view('delete-account');
    })->name('delete.account');
    Route::post('/delete-account', function () {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user->delete();
        return redirect()->route('login');
    })->name('delete.account.post');

});

require __DIR__ . '/auth.php';
