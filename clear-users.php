<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== LIMPIAR TABLA DE USUARIOS ===\n\n";

// Contar usuarios antes
$totalUsers = User::count();
echo "Total de usuarios antes: {$totalUsers}\n\n";

if ($totalUsers === 0) {
    echo "✅ No hay usuarios para eliminar.\n";
    exit(0);
}

// Confirmar
echo "⚠️  ADVERTENCIA: Esto eliminará TODOS los usuarios y sus tokens.\n";
echo "Nota: Los user_id en orders se establecerán como NULL.\n";
echo "¿Deseas continuar? (escribe 'SI' para confirmar): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$confirmation = trim($line);
fclose($handle);

if ($confirmation !== 'SI') {
    echo "\n❌ Operación cancelada.\n";
    exit(0);
}

echo "\n🗑️  Eliminando usuarios...\n";

try {
    // Desactivar verificaciones de foreign key temporalmente
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

    // Eliminar tokens primero
    DB::table('personal_access_tokens')->delete();
    echo "✅ Tokens eliminados\n";

    // Truncar tabla de usuarios
    DB::table('users')->truncate();
    echo "✅ Usuarios eliminados\n";

    // Reactivar verificaciones de foreign key
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // Verificar
    $totalUsersAfter = User::count();
    echo "\nTotal de usuarios después: {$totalUsersAfter}\n";

    echo "\n✅ Tabla de usuarios limpiada exitosamente.\n";
    echo "Ahora puedes hacer pruebas con usuarios nuevos.\n";

} catch (\Exception $e) {
    // Asegurarse de reactivar foreign key checks incluso si hay error
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    echo "\n❌ Error al limpiar datos: " . $e->getMessage() . "\n";
    exit(1);
}
