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

    public function showLogin($errores = [], $datos = [])
    {
        $esLogin = true;
        $vista = 'auth/login.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function showRegister($errores = [], $datos = [])
    {
        $esLogin = true;
        $vista = 'auth/register.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function login($datos)
    {
        $email = trim($datos['email'] ?? '');
        $contrasena = $datos['password'] ?? '';

        $errores = [];

        if (empty($email)) $errores['email'] = 'El email es obligatorio.';
        if (empty($contrasena)) $errores['password'] = 'La contraseña es obligatoria.';

        if (!empty($errores)) {
            $this->showLogin($errores, $datos);
            return;
        }

        $usuario = $this->usuarioModelo->iniciarSesion($email, $contrasena);

        if ($usuario) {
            $_SESSION['user_id'] = $usuario->id;
            $_SESSION['user_rol'] = $usuario->rol;
            $_SESSION['user_nombre'] = $usuario->nombre;
            header('Location: /dashboard');
            exit;
        }

        $errores['general'] = 'Credenciales incorrectas o cuenta inactiva.';
        $this->showLogin($errores, $datos);
    }

    public function register($datos)
    {
        $nombre = trim($datos['nombre'] ?? '');
        $email = trim($datos['email'] ?? '');
        $password = trim($datos['password'] ?? '');

        $errores = [];

        $valNombre = $this->usuarioModelo->validarNombre($nombre);
        if ($valNombre !== true) $errores['nombre'] = $valNombre;

        $valEmail = $this->usuarioModelo->validarEmail($email);
        if ($valEmail !== true) $errores['email'] = $valEmail;

        $valPassword = $this->usuarioModelo->validarPassword($password);
        if ($valPassword !== true) $errores['password'] = $valPassword;

        if (!empty($errores)) {
            $this->showRegister($errores, $datos);
            return;
        }

        $entrada = [
            'nombre' => $nombre,
            'email' => $email,
            'password' => $password,
        ];

        $resultado = $this->usuarioModelo->registrar($entrada);

        if ($resultado['exito']) {
            $_SESSION['exito'] = 'Registro exitoso. Ahora puedes iniciar sesión.';
            header('Location: /login');
            exit;
        } else {
            $this->showRegister($resultado['errores'], $datos);
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }

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

        $datos = [
            'usuario' => $usuario
        ];

        $vista = 'auth/profile.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function actualizarPerfil($datos)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $id = $_SESSION['user_id'];
        $errores = [];

        if (isset($datos['nombre'])) {
            $val = $this->usuarioModelo->validarNombre(trim($datos['nombre']));
            if ($val !== true) $errores['nombre'] = $val;
        }

        if (isset($datos['email'])) {
            $val = $this->usuarioModelo->validarEmail(trim($datos['email']), $id);
            if ($val !== true) $errores['email'] = $val;
        }

        if (!empty($errores)) {
            $usuario = (object) array_merge((array)$this->usuarioModelo->obtenerPorId($id), $datos);
            $vista = 'auth/profile.php';
            require __DIR__ . '/../views/include/layout.php';
            return;
        }

        $resultado = $this->usuarioModelo->actualizar($id, $datos);

        if ($resultado['exito']) {
            $_SESSION['exito'] = 'Perfil actualizado correctamente.';
        } else {
            $_SESSION['error'] = implode('<br>', $resultado['errores']);
        }

        $this->mostrarPerfil();
    }

    public function cambiarContrasena($id, $contrasenaActual, $contrasenaNueva, $contrasenaNuevaConfirm)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $id) {
            header('Location: /login');
            exit;
        }

        $usuario = $this->usuarioModelo->obtenerPorId($id);
        if (!$usuario) {
            $_SESSION['error'] = 'Usuario no encontrado.';
            $this->mostrarPerfil();
            return;
        }

        $errores = [];

        if (!password_verify($contrasenaActual, $usuario->password)) {
            $errores['contrasenaActual'] = 'La contraseña actual es incorrecta.';
        }

        if ($contrasenaNueva !== $contrasenaNuevaConfirm) {
            $errores['contrasenaNuevaConfirm'] = 'La nueva contraseña y la confirmación no coinciden.';
        }

        $val = $this->usuarioModelo->validarPassword($contrasenaNueva);
        if ($val !== true) {
            $errores['contrasenaNueva'] = $val;
        }

        if (!empty($errores)) {
            $_SESSION['error'] = implode('<br>', $errores);
            $this->mostrarPerfil();
            return;
        }

        $exito = $this->usuarioModelo->actualizarPassword($id, $contrasenaNueva);

        if ($exito) {
            $_SESSION['exito'] = 'Contraseña cambiada correctamente.';
        } else {
            $_SESSION['error'] = 'Error al cambiar la contraseña.';
        }

        $this->mostrarPerfil();
    }
}
