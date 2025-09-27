<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\FrotaController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Página inicial
Route::view('/', 'home')->name('landing');

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rotas autenticadas
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('veiculo', VeiculoController::class);

    Route::resource('frota', FrotaController::class)
        ->parameters(['frota' => 'frota']);

    Route::post('/notificacao/enviar', [NotificacaoController::class, 'enviar'])->name('notificacao.enviar');
    Route::post('/notificacao/aceitar/{id}', [NotificacaoController::class, 'aceitar'])->name('notificacao.aceitar');
    Route::post('/notificacao/recusar/{id}', [NotificacaoController::class, 'recusar'])->name('notificacao.recusar');
    // routes/web.php (dentro do middleware auth)
    Route::match(['POST', 'DELETE'], '/notificacao/{notificacao}/cancelar', [NotificacaoController::class, 'cancelar'])
        ->name('notificacao.cancelar');



    // buscar por email no campo de add responsável
    Route::get('/buscar-usuario', [UserController::class, 'buscar'])->name('usuario.buscar');
});

require __DIR__ . '/auth.php';
