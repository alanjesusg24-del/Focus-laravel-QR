<?php

/**
 * Company: CETAM
 * Project: FF
 * File: Controller.php (Base Controller)
 * Created on: 24/11/2025
 * Created by: Dafne Vanessa Castillo Moreo
 * Approved by: Dafne Vanessa Castillo Moreo
 *
 * Changelog:
 * - ID: 1 | Modified on: 16/12/2025 |
 *   Modified by: Dafne Vanessa Castillo Moreo |
 *   Description: Refactored Controller |
 */

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * Base Controller
 *
 * This is the base controller class that all other controllers extend.
 * It provides common functionality through Laravel traits:
 * - AuthorizesRequests: For authorization policies
 * - DispatchesJobs: For job dispatching
 * - ValidatesRequests: For request validation
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
