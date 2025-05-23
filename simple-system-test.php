<?php

/*
|--------------------------------------------------------------------------
| Test Simple del Sistema - Sin dependencias Laravel
|--------------------------------------------------------------------------
|
| Ejecutar con: php simple-system-test.php
|
*/

$baseUrl = 'http://localhost:8000';

echo "\n🚀 TEST SIMPLE DEL SISTEMA DE AGENCIA DE VIAJES\n";
echo "===============================================\n\n";

$passedTests = 0;
$failedTests = 0;
$warnings = 0;

function testResult($name, $passed, $message = '') {
    global $passedTests, $failedTests, $warnings;
    
    if ($passed === true) {
        echo "✅ $name\n";
        $passedTests++;
    } elseif ($passed === false) {
        echo "❌ $name" . ($message ? " - $message" : "") . "\n";
        $failedTests++;
    } else {
        echo "⚠️  $name" . ($message ? " - $message" : "") . "\n";
        $warnings++;
    }
}

function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'success' => $httpCode >= 200 && $httpCode < 400,
        'status' => $httpCode,
        'body' => $response,
        'error' => $error
    ];
}

// ============================================================================
// TEST 1: VERIFICACIÓN DE ARCHIVOS
// ============================================================================

echo "📁 TEST 1: ESTRUCTURA DE ARCHIVOS\n";
echo "=================================\n";

$requiredFiles = [
    'app/Models/Activity.php' => 'Modelo Activity',
    'app/Models/Booking.php' => 'Modelo Booking',
    'app/Http/Controllers/HomeController.php' => 'HomeController',
    'routes/web.php' => 'Rutas web',
    'resources/views/home/index.blade.php' => 'Vista home principal',
    '.env' => 'Archivo de configuración',
];

foreach ($requiredFiles as $file => $description) {
    testResult($description, file_exists($file));
}

echo "\n";

// ============================================================================
// TEST 2: SERVIDOR WEB
// ============================================================================

echo "🌐 TEST 2: SERVIDOR WEB\n";
echo "======================\n";

// Test página principal
$response = makeRequest($baseUrl);
testResult('Servidor Laravel corriendo', $response['success'], 
          $response['success'] ? "HTTP {$response['status']}" : "Error: {$response['error']}");

if ($response['success']) {
    $content = $response['body'];
    testResult('Página principal carga HTML', !empty($content) && strpos($content, '<html') !== false);
    testResult('Formulario de búsqueda presente', strpos($content, 'name="date"') !== false);
    testResult('Campo personas presente', strpos($content, 'name="people"') !== false);
    testResult('Botón buscar presente', strpos($content, 'Buscar') !== false || strpos($content, 'Search') !== false);
    testResult('CSS Bootstrap cargando', strpos($content, 'bootstrap') !== false);
    testResult('JavaScript presente', strpos($content, '<script') !== false);
}

echo "\n";

// ============================================================================
// TEST 3: RUTAS PRINCIPALES
// ============================================================================

echo "🔗 TEST 3: RUTAS PRINCIPALES\n";
echo "===========================\n";

$routes = [
    '/' => 'Página principal',
    '/activities' => 'Listado de actividades',
    '/category/popular' => 'Actividades populares',
    '/category/today' => 'Actividades de hoy',
    '/about' => 'Página acerca de',
    '/contact' => 'Página de contacto',
];

foreach ($routes as $route => $description) {
    $response = makeRequest($baseUrl . $route);
    testResult($description, $response['success'], "HTTP {$response['status']}");
}

echo "\n";

// ============================================================================
// TEST 4: FUNCIONALIDAD DE BÚSQUEDA
// ============================================================================

echo "🔍 TEST 4: BÚSQUEDA DE ACTIVIDADES\n";
echo "=================================\n";

$searchData = [
    'date' => date('Y-m-d', strtotime('+1 day')),
    'people' => 2
];

$response = makeRequest($baseUrl . '/search', 'POST', $searchData);
testResult('Formulario de búsqueda procesa datos', $response['success'], "HTTP {$response['status']}");

if ($response['success']) {
    $content = $response['body'];
    testResult('Página de resultados carga', strpos($content, 'resultado') !== false || strpos($content, 'result') !== false);
    testResult('Actividades mostradas', strpos($content, 'activity') !== false || strpos($content, 'actividad') !== false);
    testResult('Precios mostrados', strpos($content, '€') !== false || strpos($content, '$') !== false || strpos($content, 'precio') !== false);
    testResult('Botones comprar presentes', strpos($content, 'Comprar') !== false || strpos($content, 'Buy') !== false);
}

echo "\n";

// ============================================================================
// TEST 5: BASE DE DATOS (usando Artisan)
// ============================================================================

echo "🗄️  TEST 5: BASE DE DATOS\n";
echo "========================\n";

// Verificar conexión a BD
$output = shell_exec('php artisan tinker --execute="try { DB::connection()->getPdo(); echo \'OK\'; } catch(Exception \$e) { echo \'ERROR\'; }" 2>&1');
testResult('Conexión a base de datos', strpos($output, 'OK') !== false, 'Verificar .env y BD');

// Verificar tablas
$output = shell_exec('php artisan tinker --execute="echo Schema::hasTable(\'activities\') ? \'YES\' : \'NO\';" 2>&1');
testResult('Tabla activities existe', strpos($output, 'YES') !== false);

$output = shell_exec('php artisan tinker --execute="echo Schema::hasTable(\'bookings\') ? \'YES\' : \'NO\';" 2>&1');
testResult('Tabla bookings existe', strpos($output, 'YES') !== false);

// Verificar datos
$output = shell_exec('php artisan tinker --execute="echo App\\\\Models\\\\Activity::count();" 2>&1');
if (preg_match('/(\d+)/', $output, $matches)) {
    $activityCount = intval($matches[1]);
    testResult('Actividades en base de datos', $activityCount > 0, "Encontradas: $activityCount");
} else {
    testResult('Actividades en base de datos', false, 'No se pudo verificar');
}

echo "\n";

// ============================================================================
// TEST 6: ARTISAN COMMANDS
// ============================================================================

echo "⚙️  TEST 6: COMANDOS ARTISAN\n";
echo "==========================\n";

$commands = [
    'php artisan --version' => 'Laravel instalado',
    'php artisan route:list --compact' => 'Rutas definidas',
    'php artisan config:show app.name' => 'Configuración accesible',
];

foreach ($commands as $command => $description) {
    $output = shell_exec("$command 2>&1");
    $success = !empty($output) && strpos($output, 'Error') === false && strpos($output, 'Exception') === false;
    testResult($description, $success, $success ? 'OK' : 'Error en comando');
}

echo "\n";

// ============================================================================
// TEST 7: ARCHIVOS CRÍTICOS
// ============================================================================

echo "📄 TEST 7: ARCHIVOS CRÍTICOS\n";
echo "============================\n";

$criticalFiles = [
    '.env' => 'Configuración environment',
    'composer.json' => 'Dependencias composer',
    'package.json' => 'Dependencias NPM',
    'webpack.mix.js' => 'Configuración Mix',
    'database/migrations' => 'Directorio migraciones',
    'database/seeders' => 'Directorio seeders',
];

foreach ($criticalFiles as $file => $description) {
    testResult($description, file_exists($file));
}

// Verificar contenido de .env
if (file_exists('.env')) {
    $envContent = file_get_contents('.env');
    testResult('BD configurada en .env', strpos($envContent, 'DB_DATABASE') !== false);
    testResult('APP_KEY configurada', strpos($envContent, 'APP_KEY=base64:') !== false);
}

echo "\n";

// ============================================================================
// RESUMEN FINAL
// ============================================================================

echo "📊 RESUMEN FINAL\n";
echo "================\n\n";

$totalTests = $passedTests + $failedTests + $warnings;

echo "✅ Tests exitosos: $passedTests\n";
echo "❌ Tests fallidos: $failedTests\n";
echo "⚠️  Advertencias: $warnings\n";
echo "📊 Total tests: $totalTests\n\n";

$successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 1) : 0;
echo "🎯 Tasa de éxito: $successRate%\n\n";

// Evaluación final
if ($failedTests === 0) {
    echo "🎉 ¡EXCELENTE! El sistema está funcionando correctamente.\n";
    echo "✨ Todas las funcionalidades principales están operativas.\n\n";
    
    echo "🚀 SISTEMA LISTO:\n";
    echo "• ✅ Servidor web funcionando\n";
    echo "• ✅ Base de datos conectada\n";
    echo "• ✅ Rutas funcionando\n";
    echo "• ✅ Formularios procesando\n";
    echo "• ✅ Archivos en su lugar\n\n";
    
} elseif ($failedTests <= 2) {
    echo "⚠️  El sistema funciona con algunos problemas menores.\n";
    echo "🔧 Revisa los tests fallidos para optimizar.\n\n";
    
} else {
    echo "🚨 Se encontraron varios problemas importantes.\n";
    echo "🔧 Soluciona los tests fallidos antes de continuar.\n\n";
}

echo "🔧 COMANDOS ÚTILES:\n";
echo "==================\n";
echo "• Servidor: php artisan serve\n";
echo "• Migraciones: php artisan migrate:fresh --seed\n";
echo "• Caché: php artisan config:clear\n";
echo "• Autoload: composer dump-autoload\n\n";

echo "🌐 PROBAR MANUALMENTE:\n";
echo "======================\n";
echo "1. Ir a: $baseUrl\n";
echo "2. Llenar formulario de búsqueda\n";
echo "3. Ver resultados\n";
echo "4. Intentar hacer una reserva\n\n";

echo "✨ Test simple completado!\n\n";

// Mostrar siguiente paso
if ($failedTests === 0) {
    echo "🎯 SIGUIENTE PASO: Test manual completo\n";
    echo "Abre tu navegador y prueba toda la funcionalidad manualmente.\n\n";
} else {
    echo "🔧 SIGUIENTE PASO: Solucionar problemas\n";
    echo "Ejecuta los comandos sugeridos y vuelve a ejecutar este test.\n\n";
}