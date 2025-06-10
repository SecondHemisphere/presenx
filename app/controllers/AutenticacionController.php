<?php
class AutenticacionController
{
    private $db;
    private $usuarioModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->usuarioModel = new Usuario($db);
    }

    // Mostrar formulario de login
    public function showLogin()
    {
        $datos = [
            'titulo' => 'Iniciar Sesión',
            'error' => $_SESSION['error_login'] ?? null,
        ];

        unset($_SESSION['error_login']);

        $esLogin = true;
        $vista = 'auth/login.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Mostrar formulario de registro
    public function showRegister()
    {
        $datos = [
            'titulo' => 'Registrar Nuevo Usuario',
            'usuario' => new stdClass(),
            'errores' => [],
        ];

        $esLogin = true;
        $vista = 'auth/register.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Procesar login
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $errores = [];

            // Validar email
            if (empty($email)) {
                $errores['email'] = 'El email es obligatorio.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores['email'] = 'El email no tiene un formato válido.';
            }

            // Validar contraseña
            if (empty($password)) {
                $errores['password'] = 'La contraseña es obligatoria.';
            }

            if (empty($errores)) {
                $userRecord = $this->usuarioModel->obtenerPorEmail($email);

                if (!$userRecord) {
                    $errores['email'] = 'No existe un usuario con ese email.';
                } elseif (!password_verify($password, $userRecord->password)) {
                    $errores['password'] = 'Contraseña incorrecta.';
                } elseif ($userRecord->estado !== 'activo') {
                    $errores['general'] = 'Usuario inactivo, contacte al administrador.';
                }
            }

            if (!empty($errores)) {
                // Mostrar el formulario con errores y valores previos
                $datos = [
                    'titulo' => 'Iniciar Sesión',
                    'errores' => $errores,
                    'email' => $email,
                ];
                $esLogin = true;
                $vista = 'auth/login.php';
                require_once __DIR__ . '/../views/include/layout.php';
                exit;
            }

            // Login exitoso
            $_SESSION['user_id'] = $userRecord->id;
            $_SESSION['user_nombre'] = $userRecord->nombre;
            $_SESSION['user_rol'] = $userRecord->rol;

            header('Location: /dashboard');
            exit;
        }
    }

    // Procesar registro
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $entrada = [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'password' => $_POST['password'],
                'password_confirm' => $_POST['password_confirm'],
            ];

            $errores = [];

            // Validaciones básicas
            if (empty($entrada['nombre'])) {
                $errores['nombre'] = 'El nombre es obligatorio.';
            } else {
                if ($this->usuarioModel->obtenerPorNombre($entrada['nombre'])) {
                    $errores['nombre'] = 'El nombre ya está registrado.';
                }
            }

            if (empty($entrada['email']) || !filter_var($entrada['email'], FILTER_VALIDATE_EMAIL)) {
                $errores['email'] = 'Email inválido o vacío.';
            } else {
                if ($this->usuarioModel->obtenerPorEmail($entrada['email'])) {
                    $errores['email'] = 'El email ya está registrado.';
                }
            }

            if (empty($entrada['password'])) {
                $errores['password'] = 'La contraseña es obligatoria.';
            } elseif (strlen($entrada['password']) < 6) {
                $errores['password'] = 'La contraseña debe tener al menos 6 caracteres.';
            }

            if ($entrada['password'] !== $entrada['password_confirm']) {
                $errores['password_confirm'] = 'Las contraseñas no coinciden.';
            }

            if (count($errores) === 0) {
                // Registrar usuario (estado por defecto: activo)
                $resultado = $this->usuarioModel->registrar([
                    'nombre' => $entrada['nombre'],
                    'email' => $entrada['email'],
                    'password' => $entrada['password'],
                    'rol' => 'Usuario',
                ]);

                if ($resultado['exito']) {
                    $_SESSION['mensaje_exito'] = 'Registro exitoso, ya puedes iniciar sesión.';
                    header('Location: /login');
                    exit;
                } else {
                    $errores = $resultado['errores'];
                }
            }

            // Si hay errores, mostrar formulario con mensajes
            $datos = [
                'titulo' => 'Registrar Nuevo Usuario',
                'usuario' => (object) $entrada,
                'errores' => $errores,
            ];

            $esLogin = true;
            $vista = 'auth/register.php';
            require_once __DIR__ . '/../views/include/layout.php';
        }
    }

    // Cerrar sesión
    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }

    // Verifica si el usuario está logueado
    public function estaLogueado()
    {
        return isset($_SESSION['user_id']);
    }
}
