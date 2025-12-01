<?php

/**
 * Script para Agregar Ubicaciones de Prueba
 *
 * Uso: php agregar_ubicaciones_prueba.php
 *
 * Este script agrega ubicaciones a los negocios existentes
 * para poder probar el endpoint /businesses/nearby
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Business;

echo "===========================================\n";
echo "  Agregar Ubicaciones de Prueba\n";
echo "===========================================\n\n";

// Ubicaciones de prueba en la CDMX
$locations = [
    [
        'name' => 'Centro Histórico',
        'lat' => 19.432608,
        'lng' => -99.133209,
        'city' => 'Ciudad de México',
        'state' => 'CDMX',
        'postal_code' => '06000',
        'details' => 'Cerca del Zócalo',
    ],
    [
        'name' => 'Polanco',
        'lat' => 19.433871,
        'lng' => -99.191040,
        'city' => 'Ciudad de México',
        'state' => 'CDMX',
        'postal_code' => '11560',
        'details' => 'Av. Presidente Masaryk',
    ],
    [
        'name' => 'Condesa',
        'lat' => 19.410700,
        'lng' => -99.171400,
        'city' => 'Ciudad de México',
        'state' => 'CDMX',
        'postal_code' => '06140',
        'details' => 'Cerca del Parque México',
    ],
    [
        'name' => 'Roma Norte',
        'lat' => 19.418456,
        'lng' => -99.157234,
        'city' => 'Ciudad de México',
        'state' => 'CDMX',
        'postal_code' => '06700',
        'details' => 'Zona de restaurantes',
    ],
    [
        'name' => 'Coyoacán',
        'lat' => 19.350360,
        'lng' => -99.161990,
        'city' => 'Ciudad de México',
        'state' => 'CDMX',
        'postal_code' => '04000',
        'details' => 'Cerca del centro de Coyoacán',
    ],
];

try {
    // Obtener negocios existentes
    $businesses = Business::all();

    if ($businesses->isEmpty()) {
        echo "❌ No hay negocios en la base de datos.\n";
        echo "\nPrimero crea algunos negocios en el sistema.\n";
        exit(1);
    }

    echo "📊 Negocios encontrados: " . $businesses->count() . "\n\n";

    $updated = 0;
    foreach ($businesses as $index => $business) {
        if ($index >= count($locations)) {
            break;
        }

        $location = $locations[$index];

        echo "Actualizando: {$business->business_name}\n";
        echo "  └─ Ubicación: {$location['name']}\n";
        echo "  └─ Coordenadas: {$location['lat']}, {$location['lng']}\n";

        $business->latitude = $location['lat'];
        $business->longitude = $location['lng'];
        $business->city = $location['city'];
        $business->state = $location['state'];
        $business->postal_code = $location['postal_code'];
        $business->address_details = $location['details'];
        $business->is_location_public = true;
        $business->save();

        echo "  └─ ✅ Actualizado\n\n";
        $updated++;
    }

    echo "===========================================\n";
    echo "✅ COMPLETADO\n";
    echo "===========================================\n\n";
    echo "📍 Negocios actualizados con ubicación: $updated\n\n";

    echo "PRÓXIMO PASO:\n";
    echo "Prueba el endpoint:\n\n";
    echo "curl \"http://localhost:8000/api/v1/businesses/nearby?latitude=19.432847&longitude=-99.133208&radius=10\"\n\n";
    echo "O ejecuta:\n";
    echo "php test_nearby_endpoint.php\n\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n\n";
    echo "POSIBLES CAUSAS:\n";
    echo "1. MySQL no está corriendo\n";
    echo "2. Las columnas de ubicación no existen (ejecuta: php artisan migrate)\n";
    echo "3. Error de conexión a la base de datos\n\n";
    exit(1);
}
