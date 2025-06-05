<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/autoload.php';

$db = new Database();

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Mapeo de rutas estáticas
$routeMap = [
    '/' => ['AuthController', 'showLogin'],
    '/login' => ['AuthController', 'showLogin'],
    '/register' => ['AuthController', 'showRegister'],
    '/dashboard' => ['DashboardController', 'index'],
    '/auth/login' => ['AuthController', 'login'],
    '/auth/register' => ['AuthController', 'register'],
    '/logout' => ['AuthController', 'logout'],

    '/cargos' => ['CargoController', 'index'],
    '/cargos/create' => ['CargoController', 'create'],
    '/cargos/store' => ['CargoController', 'store'],

    '/empleados' => ['EmpleadoController', 'index'],
    '/empleados/create' => ['EmpleadoController', 'create'],
    '/empleados/store' => ['EmpleadoController', 'store'],

    '/asistencias' => ['AsistenciaController', 'index'],
    '/asistencias/create' => ['AsistenciaController', 'create'],
    '/asistencias/store' => ['AsistenciaController', 'store'],
];

// Soporte para rutas dinámicas
if (array_key_exists($path, $routeMap)) {
    list($controllerName, $method) = $routeMap[$path];
} elseif (preg_match('#^/cargos/edit/(\d+)$#', $path, $matches)) {
    $controllerName = 'CargoController';
    $method = 'edit';
    $_GET['id'] = $matches[1];
} elseif (preg_match('#^/cargos/update/(\d+)$#', $path, $matches)) {
    $controllerName = 'CargoController';
    $method = 'update';
    $_GET['id'] = $matches[1];
} elseif (preg_match('#^/cargos/delete/(\d+)$#', $path, $matches)) {
    $controllerName = 'CargoController';
    $method = 'delete';
    $_GET['id'] = $matches[1];
} elseif (preg_match('#^/empleados/edit/(\d+)$#', $path, $matches)) {
    $controllerName = 'EmpleadoController';
    $method = 'edit';
    $_GET['id'] = $matches[1];
} elseif (preg_match('#^/empleados/update/(\d+)$#', $path, $matches)) {
    $controllerName = 'EmpleadoController';
    $method = 'update';
    $_GET['id'] = $matches[1];
} elseif (preg_match('#^/empleados/delete/(\d+)$#', $path, $matches)) {
    $controllerName = 'EmpleadoController';
    $method = 'delete';
    $_GET['id'] = $matches[1];
} elseif (preg_match('#^/asistencias/edit/(\d+)$#', $path, $matches)) {
    $controllerName = 'AsistenciaController';
    $method = 'edit';
    $_GET['id'] = $matches[1];
} elseif (preg_match('#^/asistencias/update/(\d+)$#', $path, $matches)) {
    $controllerName = 'AsistenciaController';
    $method = 'update';
    $_GET['id'] = $matches[1];
} elseif (preg_match('#^/asistencias/delete/(\d+)$#', $path, $matches)) {
    $controllerName = 'AsistenciaController';
    $method = 'delete';
    $_GET['id'] = $matches[1];
} else {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

$controllerFile = __DIR__ . "/../app/controllers/{$controllerName}.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName($db);

    require_once __DIR__ . '/../app/views/include/head.php';

    if (isset($_GET['id'])) {
        $controller->$method($_GET['id']);
    } else {
        $controller->$method($_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : []);
    }
} else {
    http_response_code(500);
    die('Controlador no encontrado');
}
