<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('components.icon', \App\View\Components\Icon::class);
        // Opcional: Prefijo genérico para componentes Blade
        // Descomenta y ajusta el prefijo 'proj' por el código real del proyecto.
        // use Illuminate\Support\Facades\Blade;
        // Blade::component('proj-layouts-base', \App\View\Components\Layouts\Base::class);
        // Ahora invocable como: <x-proj-layouts-base>
    }
    
}
