<?php

use App\Http\Controllers\ComparacaoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\FrotaController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\NotificacaoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PainelController;


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

    // Listar apenas veículos de uma frota específica
    Route::get('frota/{frota}/veiculos', [App\Http\Controllers\VeiculoController::class, 'indexPorFrota'])
        ->name('frota.veiculos.index');


    Route::post('/notificacao/enviar', [NotificacaoController::class, 'enviar'])->name('notificacao.enviar');
    Route::post('/notificacao/aceitar/{id}', [NotificacaoController::class, 'aceitar'])->name('notificacao.aceitar');
    Route::post('/notificacao/recusar/{id}', [NotificacaoController::class, 'recusar'])->name('notificacao.recusar');
    // routes/web.php (dentro do middleware auth)
    Route::match(['POST', 'DELETE'], '/notificacao/{notificacao}/cancelar', [NotificacaoController::class, 'cancelar'])
        ->name('notificacao.cancelar');
    Route::get('/notificacao', [NotificacaoController::class, 'index'])->name('notificacao.index');


    // buscar por email no campo de add responsável
    Route::get('/buscar-usuario', [UserController::class, 'buscar'])->name('usuario.buscar');

    // CRUD padrão de gastos (geral)
    Route::resource('gastos', GastoController::class);

    Route::get('frota/{frota}/gasto', [GastoController::class, 'indexPorFrota'])->name('frota.gasto.index');

    // Gastos vinculados a um veículo específico
    Route::prefix('veiculo/{veiculo}')->group(function () {
        Route::get('gastos', [GastoController::class, 'indexPorVeiculo'])->name('veiculo.gastos.index');
        Route::get('gastos/create', [GastoController::class, 'createPorVeiculo'])->name('veiculo.gastos.create');
    });

    Route::get('/publico', [App\Http\Controllers\PublicoController::class, 'index'])->name('publico.index');

    Route::get('/publico/comparar', [App\Http\Controllers\PublicoController::class, 'comparar'])->name('publico.comparar');

    Route::get('/frotas/{frota}/gastos/create', [GastoController::class, 'createPorFrota'])
        ->name('frota.gasto.create');

    // Linha do tempo de gastos por veículo
    Route::get('/veiculos/{veiculo}/gastos/linha-tempo', [GastoController::class, 'linhaTempoVeiculo'])
        ->name('veiculo.gastos.linha-tempo');

    // Linha do tempo de gastos por frota
    Route::get('/frotas/{frota}/gastos/linha-tempo', [GastoController::class, 'linhaTempoFrota'])
        ->name('frota.gastos.linha-tempo');

    Route::get('/dashboard', [PainelController::class, 'index'])
        ->middleware(['auth'])
        ->name('dashboard');

    Route::post('/frota/{frota}/abandonar', [FrotaController::class, 'abandonarResponsabilidade'])
        ->name('frota.abandonar');

    Route::post('/notificacao/lida/{id}', [NotificacaoController::class, 'marcarComoLida'])
        ->name('notificacao.lida');

    Route::get('/notificacao/avisos', [NotificacaoController::class, 'avisos'])
        ->name('notificacao.avisos');
});

require __DIR__ . '/auth.php';
