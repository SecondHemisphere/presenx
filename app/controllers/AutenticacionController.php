<?php

require_once __DIR__ . '/../models/Usuario.php';

class AutenticacionController
{
    private $usuarioModelo;

    public function __construct($db)
    {
        $this->usuarioModelo = new Usuario($db);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Mostrar formulario de login
    public function showLogin()
    {
        $esLogin = true;
        $vista = 'auth/login.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Mostrar formulario de registro
    public function showRegister()
    {
        $esLogin = true;
        $vista = 'auth/register.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Procesar login
    public function login($datos)
    {
        $email = trim($datos['email'] ?? '');
        $contrasena = $datos['password'] ?? '';

        if (empty($email) || empty($contrasena)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: /login');
            exit;
        }

        $usuario = $this->usuarioModelo->iniciarSesion($email, $contrasena);

        if ($usuario) {
            $_SESSION['user_id'] = $usuario->id;
            $_SESSION['user_rol'] = $usuario->rol;
            $_SESSION['user_nombre'] = $usuario->nombre;
            header('Location: /dashboard');
        } else {
            $_SESSION['error'] = 'Credenciales incorrectas o cuenta inactiva.';
            header('Location: /login');
        }
        exit;
    }

    // Procesar registro
    public function register($datos)
    {
        $resultado = $this->usuarioModelo->registrar($datos);

        if ($resultado['exito']) {
            $_SESSION['exito'] = 'Registro exitoso. Ahora puedes iniciar sesión.';
            header('Location: /login');
        } else {
            $mensajes = [];
            foreach ($resultado['errores'] as $campo => $mensaje) {
                $mensajes[] = ucfirst($campo) . ': ' . $mensaje;
            }
            $_SESSION['error'] = implode('<br>', $mensajes);
            header('Location: /register');
        }
        exit;
    }

    // Cerrar sesión
    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }

    // Mostrar perfil del usuario logueado
    public function mostrarPerfil()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $usuario = $this->usuarioModelo->obtenerPorId($_SESSION['user_id']);
        if (!$usuario) {
            header('Location: /login');
            exit;
        }

        $vista = 'auth/profile.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Actualizar datos del perfil (sin contraseña)
    public function actualizarPerfil($datos)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = $_SESSION['user_id'];
        $resultado = $this->usuarioModelo->actualizar($id, $datos);

        if ($resultado['exito']) {
            $_SESSION['exito'] = 'Perfil actualizado correctamente.';
        } else {
            $mensajes = [];
            foreach ($resultado['errores'] as $campo => $mensaje) {
                $mensajes[] = ucfirst($campo) . ': ' . $mensaje;
            }
            $_SESSION['error'] = implode('<br>', $mensajes);
        }
        header('Location: /perfil');
        exit;
    }

    // Cambiar contraseña
    public function cambiarContrasena($id, $contrasenaActual, $contrasenaNueva, $contrasenaNuevaConfirm)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $id) {
            header('Location: /login');
            exit;
        }

        $usuario = $this->usuarioModelo->obtenerPorId($id);

        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado.';
            header('Location: /perfil');
            exit;
        }

        if (!password_verify($contrasenaActual, $usuario->password)) {
            $_SESSION['error'] = 'La contraseña actual es incorrecta.';
            header('Location: /perfil');
            exit;
        }

        if ($contrasenaNueva !== $contrasenaNuevaConfirm) {
            $_SESSION['error'] = 'La nueva contraseña y la confirmación no coinciden.';
            header('Location: /perfil');
            exit;
        }

        if (strlen($contrasenaNueva) < 6) {
            $_SESSION['error'] = 'La nueva contraseña debe tener al menos 6 caracteres.';
            header('Location: /perfil');
            exit;
        }

        $exito = $this->usuarioModelo->actualizarPassword($id, $contrasenaNueva);

        if ($exito) {
            $_SESSION['exito'] = 'Contraseña cambiada correctamente.';
        } else {
            $_SESSION['error'] = 'Error al cambiar la contraseña.';
        }

        header('Location: /perfil');
        exit;
    }
}
