<?php

require_once __DIR__ . '/../models/Usuario.php';

class AuthController
{
    private $usuarioModel;

    public function __construct($db)
    {
        $this->usuarioModel = new Usuario($db);
    }

    public function showLogin()
    {
        $esLogin = true;
        $view = 'auth/login.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function showRegister()
    {
        $esLogin = true;
        $view = 'auth/register.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function login($data)
    {
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            header('Location: /login');
            exit;
        }

        $usuario = $this->usuarioModel->login($email, $password);

        if ($usuario) {
            $_SESSION['user_id'] = $usuario->id;
            $_SESSION['user_rol'] = $usuario->rol;
            $_SESSION['user_nombre'] = $usuario->nombre;
            $_SESSION['user_permisos'] = $usuario->permisos ?? [];

            header('Location: /dashboard');
        } else {
            $_SESSION['error'] = 'Credenciales incorrectas o cuenta inactiva';
            header('Location: /login');
        }
        exit;
    }

    public function register($data)
    {
        $nombre = trim($data['nombre'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $confirmar = $data['confirm_password'] ?? '';
        $rol = 'Administrador';

        // Validaciones
        if (empty($nombre) || empty($email) || empty($password) || empty($confirmar)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            header('Location: /register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'El correo no es válido';
            header('Location: /register');
            exit;
        }

        if ($password !== $confirmar) {
            $_SESSION['error'] = 'Las contraseñas no coinciden';
            header('Location: /register');
            exit;
        }

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'La contraseña debe tener al menos 6 caracteres';
            header('Location: /register');
            exit;
        }

        if ($this->usuarioModel->existeEmail($email)) {
            $_SESSION['error'] = 'El correo ya está registrado';
            header('Location: /register');
            exit;
        }

        $data['nombre'] = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        $data['email'] = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $data['password'] = $password;
        $data['rol'] = $rol;

        if ($this->usuarioModel->registrar($data)) {
            $_SESSION['success'] = 'Registro exitoso. Ahora puedes iniciar sesión';
            header('Location: /login');
        } else {
            $_SESSION['error'] = 'Error al registrar usuario';
            header('Location: /register');
        }
        exit;
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }

    public function showMiCuenta()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder a esta página.';
            header('Location: /login');
            exit;
        }

        $usuario_autenticado = $this->usuarioModel->getById($_SESSION['user_id']);

        if (!$usuario_autenticado) {
            session_unset();
            session_destroy();
            $_SESSION['error'] = 'Tu sesión ha expirado o el usuario no existe. Por favor, inicia sesión de nuevo.';
            header('Location: /login');
            exit;
        }

        $view = 'mi_cuenta.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function updateProfile($data)
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para actualizar tu perfil.';
            header('Location: /login');
            exit;
        }

        $id_usuario = $_SESSION['user_id'];
        $nuevo_nombre = trim($data['nombre'] ?? '');
        $nuevo_email = trim($data['email'] ?? '');

        // Validaciones
        if (empty($nuevo_nombre) || empty($nuevo_email)) {
            $_SESSION['error'] = 'Nombre y correo electrónico son obligatorios.';
            header('Location: /mi-cuenta');
            exit;
        }
        if (!filter_var($nuevo_email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'El formato del correo electrónico no es válido.';
            header('Location: /mi-cuenta');
            exit;
        }

        $usuario_con_email = $this->usuarioModel->obtenerPorId($nuevo_email);
        if ($usuario_con_email && $usuario_con_email->id != $id_usuario) {
            $_SESSION['error'] = 'El correo electrónico ya está en uso por otra cuenta.';
            header('Location: /mi-cuenta');
            exit;
        }

        $data_update = [
            'id' => $id_usuario,
            'nombre' => htmlspecialchars($nuevo_nombre, ENT_QUOTES, 'UTF-8'),
            'email' => htmlspecialchars($nuevo_email, ENT_QUOTES, 'UTF-8')
        ];

        if ($this->usuarioModel->actualizar($data_update)) {
            $_SESSION['success'] = '¡Tu perfil ha sido actualizado exitosamente!';
            $_SESSION['user_nombre'] = $data_update['nombre'];
        } else {
            $_SESSION['error'] = 'Error al actualizar el perfil. Inténtalo de nuevo.';
        }

        header('Location: /mi-cuenta');
        exit;
    }

    public function changePassword($data)
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para cambiar tu contraseña.';
            header('Location: /login');
            exit;
        }

        $id_usuario = $_SESSION['user_id'];
        $current_password = $data['current_password'] ?? '';
        $new_password = $data['new_password'] ?? '';
        $confirm_new_password = $data['confirm_new_password'] ?? '';

        // Validaciones
        if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
            $_SESSION['error'] = 'Todos los campos de contraseña son obligatorios.';
            header('Location: /mi-cuenta');
            exit;
        }
        if (strlen($new_password) < 8) {
            $_SESSION['error'] = 'La nueva contraseña debe tener al menos 8 caracteres.';
            header('Location: /mi-cuenta');
            exit;
        }
        if ($new_password !== $confirm_new_password) {
            $_SESSION['error'] = 'La nueva contraseña y la confirmación no coinciden.';
            header('Location: /mi-cuenta');
            exit;
        }

        $usuario = $this->usuarioModel->obtenerPorId($id_usuario);
        if (!$usuario) {
            $_SESSION['error'] = 'Error: Usuario no encontrado.';
            header('Location: /mi-cuenta');
            exit;
        }

        if (!password_verify($current_password, $usuario->password)) {
            $_SESSION['error'] = 'La contraseña actual es incorrecta.';
            header('Location: /mi-cuenta');
            exit;
        }

        $nueva_contrasena_hasheada = password_hash($new_password, PASSWORD_DEFAULT);

        if ($this->usuarioModel->updatePassword($id_usuario, $nueva_contrasena_hasheada)) {
            $_SESSION['success'] = '¡Tu contraseña ha sido actualizada exitosamente!';
        } else {
            $_SESSION['error'] = 'Error al cambiar la contraseña. Inténtalo de nuevo.';
        }

        header('Location: /mi-cuenta');
        exit;
    }
}
