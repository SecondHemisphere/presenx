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
    public function mostrarLogin()
    {
        $esLogin = true;
        $vista = 'auth/login.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Mostrar formulario de registro
    public function mostrarRegistro()
    {
        $esLogin = true;
        $vista = 'auth/register.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Procesar login
    public function login($datos)
    {
        $email = trim($datos['email'] ?? '');
        $password = $datos['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Todos los campos son obligatorios';
            header('Location: /login');
            exit;
        }

        $usuario = $this->usuarioModelo->iniciarSesion($email, $password);

        if ($usuario) {
            $_SESSION['user_id'] = $usuario->id;
            $_SESSION['user_rol'] = $usuario->rol;
            $_SESSION['user_nombre'] = $usuario->nombre;
            header('Location: /dashboard');
        } else {
            $_SESSION['error'] = 'Credenciales incorrectas o cuenta inactiva';
            header('Location: /login');
        }
        exit;
    }

    // Procesar registro
    public function registrar($datos)
    {
        $resultado = $this->usuarioModelo->registrar($datos);

        if ($resultado['exito']) {
            $_SESSION['exito'] = 'Registro exitoso. Ahora puedes iniciar sesión';
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

    // Actualizar datos del perfil (excepto password)
    public function actualizarPerfil($datos)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = $_SESSION['user_id'];
        // No validar password en este caso (falso)
        $resultado = $this->usuarioModelo->actualizar($id, $datos);

        if ($resultado['exito']) {
            $_SESSION['exito'] = 'Perfil actualizado correctamente';
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
    public function cambiarPassword($id, $passwordActual, $passwordNuevo, $passwordNuevoConfirm)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $id) {
            header('Location: /login');
            exit;
        }

        $usuario = $this->usuarioModelo->obtenerPorId($id);

        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado';
            header('Location: /perfil');
            exit;
        }

        if (!password_verify($passwordActual, $usuario->password)) {
            $_SESSION['error'] = 'La contraseña actual es incorrecta';
            header('Location: /perfil');
            exit;
        }

        if ($passwordNuevo !== $passwordNuevoConfirm) {
            $_SESSION['error'] = 'La nueva contraseña y la confirmación no coinciden';
            header('Location: /perfil');
            exit;
        }

        if (strlen($passwordNuevo) < 6) {
            $_SESSION['error'] = 'La nueva contraseña debe tener al menos 6 caracteres';
            header('Location: /perfil');
            exit;
        }

        $exito = $this->usuarioModelo->actualizarPassword($id, $passwordNuevo);

        if ($exito) {
            $_SESSION['exito'] = 'Contraseña cambiada correctamente';
        } else {
            $_SESSION['error'] = 'Error al cambiar la contraseña';
        }

        header('Location: /perfil');
        exit;
    }
}
