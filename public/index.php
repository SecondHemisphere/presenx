<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/autoload.php';

// Configurar zona horaria para Ecuador
date_default_timezone_set('America/Guayaquil');

// Inicia la sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// Rutas públicas: accesibles sin iniciar sesión
$publicRoutes = [
    '/',
    '/login',
    '/register',
    '/auth/login',
    '/auth/register',
    '/bienvenida',
    '/storeAsistencia'
];

// Rutas de administrador
$adminRoutes = [
    '/dashboard',
    '/cargos',
    '/cargos/create',
    '/cargos/store',
    '/empleados',
    '/empleados/create',
    '/empleados/store',
    '/asistencias',
    '/asistencias/store',
    '/usuarios',
    '/usuarios/create',
    '/usuarios/store',
    '/empresa/configuracion',
];

// Rutas dinámicas que también requieren admin
$adminPrefixRoutes = [
    '/cargos/edit/',
    '/cargos/update/',
    '/cargos/delete/',
    '/empleados/edit/',
    '/empleados/update/',
    '/empleados/delete/',
    '/asistencias/delete/',
    '/usuarios/edit/',
    '/usuarios/update/',
    '/usuarios/delete/'
];

// Mapeo de rutas estáticas
$routeMap = [
    '/' => ['HomeController', 'index'],
    '/storeAsistencia' => ['AsistenciaController', 'storeUsuario'],

    '/login' => ['AuthController', 'showLogin'],
    '/register' => ['AuthController', 'showRegister'],
    '/auth/login' => ['AuthController', 'login'],
    '/auth/register' => ['AuthController', 'register'],
    '/logout' => ['AuthController', 'logout'],

    '/dashboard' => ['DashboardController', 'index'],

    '/cargos' => ['CargoController', 'index'],
    '/cargos/create' => ['CargoController', 'create'],
    '/cargos/store' => ['CargoController', 'store'],

    '/empleados' => ['EmpleadoController', 'index'],
    '/empleados/create' => ['EmpleadoController', 'create'],
    '/empleados/store' => ['EmpleadoController', 'store'],

    '/asistencias' => ['AsistenciaController', 'index'],
    '/asistencias/create' => ['AsistenciaController', 'create'],
    '/asistencias/store' => ['AsistenciaController', 'store'],

    '/usuarios' => ['UsuarioController', 'index'],
    '/usuarios/create' => ['UsuarioController', 'create'],
    '/usuarios/store' => ['UsuarioController', 'store'],

    '/empresa/configuracion' => ['EmpresaController', 'configuracion'],

    '/mi-cuenta' => ['UserProfileController', 'mostrarMiCuenta'],
    '/mi-cuenta/actualizar-perfil' => ['CUserProfileController', 'actualizarPerfil'],
    '/mi-cuenta/cambiar-contrasena' => ['UserProfileController', 'cambiarContrasena'],

    // Otras rutas de la aplicación
];

// Detectar ruta dinámica
if (array_key_exists($path, $routeMap)) {
    list($controllerName, $method) = $routeMap[$path];
} elseif (preg_match('#^/cargos/(edit|update|delete)/(\d+)$#', $path, $matches)) {
    $controllerName = 'CargoController';
    $method = $matches[1];
    $_GET['id'] = $matches[2];
} elseif (preg_match('#^/empleados/(edit|update|delete)/(\d+)$#', $path, $matches)) {
    $controllerName = 'EmpleadoController';
    $method = $matches[1];
    $_GET['id'] = $matches[2];
} elseif (preg_match('#^/asistencias/(delete)/(\d+)$#', $path, $matches)) {
    $controllerName = 'AsistenciaController';
    $method = $matches[1];
    $_GET['id'] = $matches[2];
} elseif (preg_match('#^/usuarios/(edit|update|delete)/(\d+)$#', $path, $matches)) {
    $controllerName = 'UsuarioController';
    $method = $matches[1];
    $_GET['id'] = $matches[2];
} else {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

// Verificación de acceso
function isAdmin()
{
    return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'Administrador';
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

$requiresLogin = !in_array($path, $publicRoutes);
$requiresAdmin = in_array($path, $adminRoutes) || array_filter($adminPrefixRoutes, fn($prefix) => str_starts_with($path, $prefix));

// Protección por login y rol
if ($requiresAdmin && !isAdmin()) {
    header('Location: /login');
    exit;
} elseif ($requiresLogin && !isLoggedIn()) {
    header('Location: /login');
    exit;
}

// Carga del controlador
$controllerFile = __DIR__ . "/../app/controllers/{$controllerName}.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName($db);

    // Ejecutar método del controlador
    if (isset($_GET['id'])) {
        $controller->$method($_GET['id']);
    } else {
        $controller->$method($_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : []);
    }
} else {
    http_response_code(500);
    die('Controlador no encontrado');
}
