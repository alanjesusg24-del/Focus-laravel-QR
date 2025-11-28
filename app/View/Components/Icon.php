<?php

/**
 * ============================================
 * CETAM - Icon Component
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        Icon.php
 * @description Componente Blade para renderizar iconos Font Awesome
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @version     1.0.0
 *
 * ============================================
 */

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Icon extends Component
{
    public string $iconClass;
    public string $additionalClasses;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public ?string $class = ''
    ) {
        // Obtener iconos del catálogo (sin anidación)
        $icons = config('icons', []);
        $this->iconClass = $icons[$name] ?? 'fas fa-question-circle';
        $this->additionalClasses = $class ?? '';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.icon');
    }
}
