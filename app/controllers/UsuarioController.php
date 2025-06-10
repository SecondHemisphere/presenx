<?php
class UsuarioController
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

    // Lista todos los usuarios
    public function index()
    {
        $usuarios = $this->usuarioModel->obtenerTodos();

        $data = [
            'title' => 'Listado de Usuarios',
            'usuarios' => $usuarios,
            'success_message' => $_SESSION['success_message'] ?? null,
            'error_message' => $_SESSION['error_message'] ?? null,
            'current_page' => 'usuarios'
        ];

        unset($_SESSION['success_message'], $_SESSION['error_message']);

        $view = 'admin/usuarios/index.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Muestra el formulario de creación
    public function create()
    {
        $data = [
            'title' => 'Registrar Nuevo Usuario',
            'usuario' => new stdClass(),
            'errors' => [],
            'form_action' => '/usuarios/store',
            'current_page' => 'usuarios'
        ];

        $view = 'admin/usuarios/create.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Guarda un nuevo usuario
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'rol' => trim($_POST['rol'] ?? 'Usuario'),
            ];

            $errores = [];

            // Validar nombre
            if (empty($inputData['nombre'])) {
                $errores['nombre'] = 'El nombre es obligatorio.';
            }

            // Validar email
            if (empty($inputData['email'])) {
                $errores['email'] = 'El correo es obligatorio.';
            } elseif (!filter_var($inputData['email'], FILTER_VALIDATE_EMAIL)) {
                $errores['email'] = 'El correo no es válido.';
            } elseif ($this->usuarioModel->existeEmail($inputData['email'])) {
                $errores['email'] = 'Este correo ya está registrado.';
            }

            // Validar password
            if (strlen($inputData['password']) < 6) {
                $errores['password'] = 'La contraseña debe tener al menos 6 caracteres.';
            }

            // Validar rol (sólo Administrador o Usuario)
            if (!in_array($inputData['rol'], ['Administrador', 'Usuario'])) {
                $inputData['rol'] = 'Usuario';
            }

            if (empty($errores)) {
                $inputData['estado'] = 'activo';
                $this->usuarioModel->registrar($inputData);
                $_SESSION['success_message'] = 'Usuario registrado correctamente';
                header('Location: /usuarios');
                exit;
            } else {
                $data = [
                    'title' => 'Registrar Nuevo Usuario',
                    'usuario' => (object) $inputData,
                    'errors' => $errores,
                    'form_action' => '/usuarios/store',
                    'current_page' => 'usuarios'
                ];

                $view = 'admin/usuarios/create.php';
                require_once __DIR__ . '/../views/include/layout.php';
            }
        }
    }

    // Mostrar formulario para editar usuario
    public function edit($id)
    {
        $usuario = $this->usuarioModel->obtenerPorId($id);

        if (!$usuario) {
            $_SESSION['error_message'] = 'Usuario no encontrado';
            header('Location: /usuarios');
            exit;
        }

        $data = [
            'title' => 'Editar Usuario',
            'usuario' => $usuario,
            'errors' => [],
            'form_action' => "/usuarios/update/$id",
            'current_page' => 'usuarios'
        ];

        $view = 'admin/usuarios/edit.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Actualiza usuario (sin cambiar contraseña aquí)
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'rol' => trim($_POST['rol']),
                'estado' => trim($_POST['estado']),
            ];

            $errores = [];

            // Validaciones básicas
            if (empty($inputData['nombre'])) {
                $errores['nombre'] = 'El nombre es obligatorio.';
            }

            if (empty($inputData['email'])) {
                $errores['email'] = 'El correo es obligatorio.';
            } elseif (!filter_var($inputData['email'], FILTER_VALIDATE_EMAIL)) {
                $errores['email'] = 'El correo no es válido.';
            } else {
                // Validar que el email no esté registrado por otro usuario
                $usuarioExistente = $this->usuarioModel->obtenerPorId($id);
                if ($usuarioExistente && $usuarioExistente->email !== $inputData['email']) {
                    if ($this->usuarioModel->existeEmail($inputData['email'])) {
                        $errores['email'] = 'Este correo ya está registrado.';
                    }
                }
            }

            if (!in_array($inputData['rol'], ['Administrador', 'Usuario'])) {
                $inputData['rol'] = 'Usuario';
            }

            if (!in_array($inputData['estado'], ['activo', 'inactivo'])) {
                $inputData['estado'] = 'activo';
            }

            if (empty($errores)) {
                $this->usuarioModel->actualizar($id, $inputData);
                $_SESSION['success_message'] = 'Usuario actualizado correctamente';
                header('Location: /usuarios');
                exit;
            } else {
                $usuario = (object) $inputData;
                $usuario->id = $id;
                $data = [
                    'title' => 'Editar Usuario',
                    'usuario' => $usuario,
                    'errors' => $errores,
                    'form_action' => "/usuarios/update/$id",
                    'current_page' => 'usuarios'
                ];
                $view = 'admin/usuarios/edit.php';
                require_once __DIR__ . '/../views/include/layout.php';
            }
        }
    }

    // Cambiar estado a inactivo (eliminar usuario "lógicamente")
    public function delete($id)
    {
        $this->usuarioModel->eliminar($id);
        $_SESSION['success_message'] = 'Usuario eliminado correctamente';
        header('Location: /usuarios');
        exit;
    }

    // Verifica si el usuario está logueado
    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
