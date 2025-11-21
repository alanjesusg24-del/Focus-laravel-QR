<?php

/**
 * ============================================
 * CETAM - Alert Component
 * ============================================
 *
 * @project     Centro de Servicios (CS)
 * @file        Alert.php
 * @description Componente de alertas Bootstrap
 * @author      CETAM Dev Team
 * @created     2025-11-20
 * @version     1.0.0
 *
 * ============================================
 */

namespace App\View\Components\CS;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public string $alertClass;
    public string $iconName;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $type = 'info',
        public string $message = '',
        public bool $dismissible = true
    ) {
        $this->alertClass = match($type) {
            'success' => 'alert-success',
            'danger', 'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info',
            default => 'alert-info',
        };

        $this->iconName = match($type) {
            'success' => 'success',
            'danger', 'error' => 'error',
            'warning' => 'warning',
            'info' => 'info',
            default => 'info',
        };
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.cs.alert');
    }
}
