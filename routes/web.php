<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\FrotaController;
use Illuminate\Support\Facades\Route;

// Página inicial
Route::view('/', 'home')->name('landing');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas autenticadas
Route::middleware('auth')->group(function () {
    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 🚗 Veículos
    Route::resource('veiculo', VeiculoController::class);


    // 🚌 Frotas
    Route::resource('frota', FrotaController::class)
        ->parameters(['frota' => 'frota']);
});

require __DIR__ . '/auth.php';
