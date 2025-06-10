<?php
class MiCuentaController
{
    private $db;
    private $usuarioModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->usuarioModel = new Usuario($db);

        if (!$this->estaLogueado()) {
            header('Location: /login');
            exit;
        }
    }

    // Mostrar formulario con datos actuales del usuario
    public function index()
    {
        $usuario = $this->usuarioModel->obtenerPorId($_SESSION['user_id']);

        $datos = [
            'titulo' => 'Mi Cuenta',
            'usuario' => $usuario,
            'errores' => $_SESSION['errores'] ?? [],
            'mensaje_exito' => $_SESSION['mensaje_exito'] ?? null,
        ];

        unset($_SESSION['errores'], $_SESSION['mensaje_exito']);

        $vista = 'mi-cuenta.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Actualizar nombre y correo electrónico
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $errores = [];

            // Validaciones
            if ($nombre === '') {
                $errores['nombre'] = 'El nombre es obligatorio';
            } elseif (strlen($nombre) > 150) {
                $errores['nombre'] = 'El nombre no debe superar 150 caracteres';
            }

            if ($email === '') {
                $errores['email'] = 'El correo electrónico es obligatorio';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores['email'] = 'El correo electrónico no es válido';
            } elseif ($this->usuarioModel->emailExiste($email, $_SESSION['user_id'])) {
                $errores['email'] = 'Este correo ya está registrado';
            }

            if (!empty($errores)) {
                $_SESSION['errores'] = $errores;
                $_SESSION['datos_anteriores'] = ['nombre' => $nombre, 'email' => $email];
                header('Location: /mi-cuenta');
                exit;
            }

            $this->usuarioModel->actualizar($_SESSION['user_id'], [
                'nombre' => $nombre,
                'email' => $email,
            ]);

            $_SESSION['mensaje_exito'] = 'Datos actualizados correctamente';
            header('Location: /mi-cuenta');
            exit;
        }
    }

    // Cambiar contraseña
    public function cambiarContrasena()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $actual = $_POST['password_actual'] ?? '';
            $nueva = $_POST['password_nueva'] ?? '';
            $confirmar = $_POST['password_confirmar'] ?? '';
            $errores = [];

            $usuario = $this->usuarioModel->obtenerPorId($_SESSION['user_id']);

            if (empty($actual) || empty($nueva) || empty($confirmar)) {
                $errores['password'] = 'Todos los campos de contraseña son obligatorios';
            } elseif (!password_verify($actual, $usuario->password)) {
                $errores['password_actual'] = 'La contraseña actual es incorrecta';
            } elseif (strlen($nueva) < 8) {
                $errores['password_nueva'] = 'La nueva contraseña debe tener al menos 8 caracteres';
            } elseif ($nueva !== $confirmar) {
                $errores['password_confirmar'] = 'Las contraseñas no coinciden';
            }

            if (!empty($errores)) {
                $_SESSION['errores'] = $errores;
                header('Location: /mi-cuenta');
                exit;
            }

            // Actualizar contraseña (hasheada)
            $this->usuarioModel->actualizar($_SESSION['user_id'], [
                'password' => password_hash($nueva, PASSWORD_DEFAULT),
            ]);

            $_SESSION['mensaje_exito'] = 'Contraseña cambiada correctamente';
            header('Location: /mi-cuenta');
            exit;
        }
    }

    private function estaLogueado()
    {
        return isset($_SESSION['user_id']);
    }
}
