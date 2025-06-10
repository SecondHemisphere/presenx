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
}
