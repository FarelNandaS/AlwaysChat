<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [IndexController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('api')->middleware(['api', 'throttle:60,1'])->group(function () {
    Route::middleware('auth')->get('/user/keys', [ApiController::class, 'userKeys'])->name('api.user.keys');
    Route::middleware('auth')->get('/user/public_key', [ApiController::class, 'getPublicKey'])->name('api.user.public-key');

    Route::middleware('auth')->post('/add-conversation', [ApiController::class, 'addConversation'])->name('api.add-conversation');
    Route::middleware('auth')->post('/send-message', [ApiController::class, 'sendMessage'])->name('api.send-message');

    Route::middleware('auth')->get('/conversations/{conversation}/messages', [ApiController::class, 'getMessages'])->name('api.getMessages');
});

require __DIR__ . '/auth.php';
