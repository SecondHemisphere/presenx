<?php
session_start();

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '12345678');
define('DB_NAME', 'presenx');

// Configuración de la aplicación
define('APP_ROOT', dirname(dirname(__FILE__)));
define('URL_ROOT', 'http://localhost/presenx/public');
define('SITE_NAME', 'Presenx');

// Constantes
define('AAA', 'a');

?>