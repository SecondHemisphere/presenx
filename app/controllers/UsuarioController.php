<?php
class UsuarioController
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

    // Mostrar todos los usuarios
    public function index()
    {
        $usuarios = $this->usuarioModel->obtenerTodos();

        $datos = [
            'titulo' => 'Listado de Usuarios',
            'usuarios' => $usuarios,
            'mensaje_exito' => $_SESSION['mensaje_exito'] ?? null,
            'mensaje_error' => $_SESSION['mensaje_error'] ?? null,
            'pagina_actual' => 'usuarios'
        ];

        unset($_SESSION['mensaje_exito'], $_SESSION['mensaje_error']);

        $vista = 'admin/usuarios/index.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Mostrar formulario creación
    public function create()
    {
        $datos = [
            'titulo' => 'Registrar Nuevo Usuario',
            'usuario' => new stdClass(),
            'errores' => [],
            'accion_formulario' => '/usuarios/store',
            'pagina_actual' => 'usuarios'
        ];

        $vista = 'admin/usuarios/create.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Guardar usuario nuevo
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $entrada = [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'rol' => trim($_POST['rol'] ?? 'Usuario'),
                // 'estado' no se pasa, se asigna en el modelo como 'activo'
            ];

            $resultado = $this->usuarioModel->registrar($entrada);

            if ($resultado['exito']) {
                $_SESSION['mensaje_exito'] = 'Usuario registrado correctamente.';
                header('Location: /usuarios');
                exit;
            } else {
                // En caso de errores los mostramos en el formulario
                $datos = [
                    'titulo' => 'Registrar Nuevo Usuario',
                    'usuario' => (object) $entrada,
                    'errores' => $resultado['errores'],
                    'accion_formulario' => '/usuarios/guardar',
                    'pagina_actual' => 'usuarios'
                ];

                $vista = 'admin/usuarios/create.php';
                require_once __DIR__ . '/../views/include/layout.php';
            }
        }
    }

    // Mostrar formulario edición
    public function edit($id)
    {
        $usuario = $this->usuarioModel->obtenerPorId($id);

        if (!$usuario) {
            $_SESSION['mensaje_error'] = 'Usuario no encontrado.';
            header('Location: /usuarios');
            exit;
        }

        $datos = [
            'titulo' => 'Editar Usuario',
            'usuario' => $usuario,
            'errores' => [],
            'accion_formulario' => "/usuarios/update/$id",
            'pagina_actual' => 'usuarios'
        ];

        $vista = 'admin/usuarios/edit.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Actualizar usuario
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $entrada = [
                'nombre' => trim($_POST['nombre']),
                'email' => trim($_POST['email']),
                'rol' => trim($_POST['rol']),
                'estado' => trim($_POST['estado']),
            ];

            $resultado = $this->usuarioModel->actualizar($id, $entrada);

            if ($resultado['exito']) {
                $_SESSION['mensaje_exito'] = 'Usuario actualizado correctamente.';
                header('Location: /usuarios');
                exit;
            } else {
                $entrada['id'] = $id;
                $datos = [
                    'titulo' => 'Editar Usuario',
                    'usuario' => (object) $entrada,
                    'errores' => $resultado['errores'],
                    'accion_formulario' => "/usuarios/update/$id",
                    'pagina_actual' => 'usuarios'
                ];

                $vista = 'admin/usuarios/edit.php';
                require_once __DIR__ . '/../views/include/layout.php';
            }
        }
    }

    // Eliminar usuario (estado inactivo)
    public function delete($id)
    {
        if ($this->usuarioModel->eliminar($id)) {
            $_SESSION['mensaje_exito'] = 'Usuario eliminado correctamente.';
        } else {
            $_SESSION['mensaje_error'] = 'No se pudo eliminar el usuario.';
        }

        header('Location: /usuarios');
        exit;
    }

    private function estaLogueado()
    {
        return isset($_SESSION['user_id']);
    }
}
