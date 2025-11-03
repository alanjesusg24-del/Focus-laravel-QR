<?php

namespace App\View\Components\Layouts;

use Illuminate\View\Component;

class Base extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // Opcional: Puedes registrar este componente con un prefijo genérico
    // para su uso como <x-proj-layouts.base>. Sustituir 'proj' por el código real del proyecto.
    // Ver: AppServiceProvider::boot() -> Blade::component('proj-layouts-base', Base::class)

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('layouts.base');
    }
}
