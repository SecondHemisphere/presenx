<?php

class UserProfileController
{
    private $usuarioModel;

    public function __construct($db)
    {
        $this->usuarioModel = new Usuario($db);
    }

    public function mostrarMiCuenta()
    {
        // 1. Verificar si el usuario está logueado. Si no, redirigir a la página de login.
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para acceder a tu perfil.';
            header('Location: /login');
            exit;
        }

        // 2. Obtener los datos actuales del usuario desde el modelo.
        $usuario_autenticado = $this->usuarioModel->obtenerPorId($_SESSION['user_id']);

        // 3. Manejar el caso si el usuario no se encuentra (ej. ID de sesión inválido o usuario eliminado).
        if (!$usuario_autenticado) {
            session_unset(); // Limpiar variables de sesión
            session_destroy(); // Destruir la sesión
            $_SESSION['error'] = 'Tu sesión ha expirado o el usuario no existe. Por favor, inicia sesión de nuevo.';
            header('Location: /login');
            exit;
        }

        $view = 'mi-cuenta.php';
        require_once __DIR__ . '/../views/include/layout.php'; // Tu archivo de layout principal
    }

    public function actualizarPerfil($data)
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

        $usuario_actual = $this->usuarioModel->obtenerPorId($id_usuario);
        if (!$usuario_actual) {
            $_SESSION['error'] = 'Usuario no encontrado para actualizar.';
            header('Location: /mi-cuenta');
            exit;
        }

        if ($nuevo_email !== $usuario_actual->email && $this->usuarioModel->existeEmail($nuevo_email)) {
            $_SESSION['error'] = 'El correo electrónico ya está en uso por otra cuenta.';
            header('Location: /mi-cuenta');
            exit;
        }

        $data_update = [
            'nombre' => htmlspecialchars($nuevo_nombre, ENT_QUOTES, 'UTF-8'),
            'email' => htmlspecialchars($nuevo_email, ENT_QUOTES, 'UTF-8'),
            'rol' => $usuario_actual->rol,
            'estado' => $usuario_actual->estado
        ];

        if ($this->usuarioModel->actualizar($id_usuario, $data_update)) {
            $_SESSION['success'] = '¡Tu perfil ha sido actualizado exitosamente!';
            $_SESSION['user_nombre'] = $data_update['nombre'];
        } else {
            $_SESSION['error'] = 'Error al actualizar el perfil. Inténtalo de nuevo.';
        }

        header('Location: /mi-cuenta');
        exit;
    }

    public function cambiarContrasena($data)
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
