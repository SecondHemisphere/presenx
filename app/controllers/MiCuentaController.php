<?php
class MiCuentaController
{
    private $db;
    private $usuarioModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->usuarioModel = new Usuario($db);

        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    // Mostrar formulario con datos actuales del usuario
    public function index()
    {
        $usuario = $this->usuarioModel->obtenerPorId($_SESSION['user_id']);

        $data = [
            'title' => 'Mi Cuenta',
            'usuario' => $usuario,
            'errores' => $_SESSION['errores'] ?? [],
            'success_message' => $_SESSION['success_message'] ?? null,
        ];

        unset($_SESSION['errores'], $_SESSION['success_message']);

        $view = 'mi-cuenta.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Actualiza nombre y email
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
            } elseif ($this->usuarioModel->existeEmail($email, $_SESSION['user_id'])) {
                // El método existeEmail debe aceptar un parámetro para excluir al usuario actual
                $errores['email'] = 'Este correo ya está registrado';
            }

            if (!empty($errores)) {
                $_SESSION['errores'] = $errores;
                $_SESSION['old_data'] = ['nombre' => $nombre, 'email' => $email];
                header('Location: /mi-cuenta');
                exit;
            }

            $this->usuarioModel->actualizar($_SESSION['user_id'], [
                'nombre' => $nombre,
                'email' => $email,
            ]);

            $_SESSION['success_message'] = 'Datos actualizados correctamente';
            header('Location: /mi-cuenta');
            exit;
        }
    }

    // Cambiar contraseña
    public function cambiarContrasena()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $actual = $_POST['actual_password'] ?? '';
            $nueva = $_POST['nueva_password'] ?? '';
            $confirmar = $_POST['confirmar_password'] ?? '';
            $errores = [];

            $usuario = $this->usuarioModel->obtenerPorId($_SESSION['user_id']);

            if (empty($actual) || empty($nueva) || empty($confirmar)) {
                $errores['password'] = 'Todos los campos de contraseña son obligatorios';
            } elseif (!password_verify($actual, $usuario->password)) {
                $errores['actual_password'] = 'La contraseña actual es incorrecta';
            } elseif (strlen($nueva) < 8) {
                $errores['nueva_password'] = 'La nueva contraseña debe tener al menos 8 caracteres';
            } elseif ($nueva !== $confirmar) {
                $errores['confirmar_password'] = 'Las contraseñas no coinciden';
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

            $_SESSION['success_message'] = 'Contraseña cambiada correctamente';
            header('Location: /mi-cuenta');
            exit;
        }
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
