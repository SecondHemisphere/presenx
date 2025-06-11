<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/autoload.php';

// Configurar zona horaria para Ecuador
date_default_timezone_set('America/Guayaquil');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();

// Obtener la ruta solicitada
$rutaSolicitada = $_SERVER['REQUEST_URI'];
$ruta = parse_url($rutaSolicitada, PHP_URL_PATH);

// Rutas públicas: accesibles sin iniciar sesión
$rutasPublicas = [
    '/',
    '/login',
    '/register',
    '/auth/login',
    '/auth/register',
    '/bienvenida',
    '/storeAsistencia'
];

// Rutas que requieren rol administrador
$rutasAdmin = [
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

// Prefijos de rutas dinámicas que también requieren administrador
$prefijosRutasAdmin = [
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

// Mapeo de rutas estáticas a controladores y métodos
$mapaRutas = [
    '/' => ['HomeController', 'index'],
    '/storeAsistencia' => ['AsistenciaController', 'storeUsuario'],

    '/login' => ['AutenticacionController', 'showLogin'],
    '/register' => ['AutenticacionController', 'showRegister'],
    '/auth/login' => ['AutenticacionController', 'login'],
    '/auth/register' => ['AutenticacionController', 'register'],
    '/logout' => ['AutenticacionController', 'logout'],

    '/cuenta' => ['AutenticacionController', 'index'],
    '/cuenta/actualizar' => ['AutenticacionController', 'actualizar'],
    '/cuenta/cambiar-contrasena' => ['AutenticacionController', 'cambiarContrasena'],

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
];

// Detectar ruta dinámica y extraer parámetros
if (array_key_exists($ruta, $mapaRutas)) {
    list($controlador, $metodo) = $mapaRutas[$ruta];
} elseif (preg_match('#^/cargos/(edit|update|delete)/(\d+)$#', $ruta, $coincidencias)) {
    $controlador = 'CargoController';
    $metodo = $coincidencias[1];
    $_GET['id'] = $coincidencias[2];
} elseif (preg_match('#^/empleados/(edit|update|delete)/(\d+)$#', $ruta, $coincidencias)) {
    $controlador = 'EmpleadoController';
    $metodo = $coincidencias[1];
    $_GET['id'] = $coincidencias[2];
} elseif (preg_match('#^/asistencias/(delete)/(\d+)$#', $ruta, $coincidencias)) {
    $controlador = 'AsistenciaController';
    $metodo = $coincidencias[1];
    $_GET['id'] = $coincidencias[2];
} elseif (preg_match('#^/usuarios/(edit|update|delete)/(\d+)$#', $ruta, $coincidencias)) {
    $controlador = 'UsuarioController';
    $metodo = $coincidencias[1];
    $_GET['id'] = $coincidencias[2];
} else {
    http_response_code(404);
    echo '404 No Encontrado';
    exit;
}

// Función para verificar si el usuario es administrador
function esAdministrador()
{
    return isset($_SESSION['user_rol']) && $_SESSION['user_rol'] === 'Administrador';
}

// Función para verificar si el usuario está autenticado
function estaLogueado()
{
    return isset($_SESSION['user_id']);
}

// Definir si la ruta requiere login o admin
$requiereLogin = !in_array($ruta, $rutasPublicas);
$requiereAdmin = in_array($ruta, $rutasAdmin) || array_filter($prefijosRutasAdmin, fn($prefijo) => str_starts_with($ruta, $prefijo));

// Proteger rutas según permisos
if ($requiereAdmin && !esAdministrador()) {
    header('Location: /login');
    exit;
} elseif ($requiereLogin && !estaLogueado()) {
    header('Location: /login');
    exit;
}

// Cargar el controlador correspondiente
$archivoControlador = __DIR__ . "/../app/controllers/{$controlador}.php";

if (file_exists($archivoControlador)) {
    require_once $archivoControlador;
    $instanciaControlador = new $controlador($db);

    // Ejecutar método con parámetro id o con datos POST/GET según corresponda
    if (isset($_GET['id'])) {
        $instanciaControlador->$metodo($_GET['id']);
    } else {
        $datos = $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : [];
        $instanciaControlador->$metodo($datos);
    }
} else {
    http_response_code(500);
    die('Controlador no encontrado');
}
