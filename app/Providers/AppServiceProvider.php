<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notificacao;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartilha a contagem de notificações pendentes com todas as views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $pendentes = Notificacao::where('usuario_destinatario_id', Auth::id())
                    ->where('status', 0)
                    ->count();

                $view->with('notificacoesPendentes', $pendentes);
            }
        });
    }
}
