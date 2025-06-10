<?php
class CargoController
{
    private $db;
    private $cargoModel;

    // Constructor: inicializa el modelo y verifica si el usuario está autenticado
    public function __construct($db)
    {
        $this->db = $db;
        $this->cargoModel = new Cargo($db);

        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    // Muestra el listado de cargos
    public function index()
    {
        $cargos = $this->cargoModel->obtenerTodos();

        $data = [
            'title' => 'Listado de Cargos',
            'cargos' => $cargos,
            'success_message' => $_SESSION['success_message'] ?? null,
            'error_message' => $_SESSION['error_message'] ?? null,
            'current_page' => 'cargos'
        ];

        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);

        $view = 'admin/cargos/index.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Muestra el formulario para registrar un nuevo cargo
    public function create()
    {
        $data = [
            'title' => 'Registrar Nuevo Cargo',
            'cargo' => new stdClass(),
            'errors' => [],
            'form_action' => '/cargos/store',
            'current_page' => 'cargos'
        ];

        $view = 'admin/cargos/create.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Procesa el formulario de registro de cargo
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'])
            ];

            $result = $this->cargoModel->registrar($inputData);

            if ($result['exito']) {
                $_SESSION['success_message'] = 'Cargo registrado correctamente';
                header('Location: /cargos');
                exit;
            } else {
                $data = [
                    'title' => 'Registrar Nuevo Cargo',
                    'cargo' => (object) $inputData,
                    'errors' => $result['errores'],
                    'form_action' => '/cargos/store',
                    'current_page' => 'cargos'
                ];
                $view = 'admin/cargos/create.php';
                require_once __DIR__ . '/../views/include/layout.php';
            }
        }
    }

    // Muestra el formulario de edición de un cargo
    public function edit($id)
    {
        $cargo = $this->cargoModel->obtenerPorId($id);

        if (!$cargo) {
            $_SESSION['error_message'] = 'Cargo no encontrado';
            header('Location: /cargos');
            exit;
        }

        $data = [
            'title' => 'Editar Cargo',
            'cargo' => $cargo,
            'errors' => [],
            'form_action' => "/cargos/update/$id",
            'current_page' => 'cargos'
        ];

        $view = 'admin/cargos/edit.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    // Procesa la actualización de un cargo
    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = [
                'nombre' => trim($_POST['nombre']),
                'descripcion' => trim($_POST['descripcion'])
            ];

            $result = $this->cargoModel->actualizar($id, $inputData);

            if ($result['exito']) {
                $_SESSION['success_message'] = 'Cargo actualizado correctamente';
                header('Location: /cargos');
                exit;
            } else {
                $originalCargo = $this->cargoModel->obtenerPorId($id);

                $data = [
                    'title' => 'Editar Cargo',
                    'cargo' => (object) array_merge((array) $originalCargo, $inputData),
                    'errors' => $result['errores'],
                    'form_action' => "/cargos/update/$id",
                    'current_page' => 'cargos'
                ];

                $view = 'admin/cargos/edit.php';
                require_once __DIR__ . '/../views/include/layout.php';
            }
        }
    }

    // Elimina un cargo
    public function delete($id)
    {
        $_SESSION['success_message'] = $this->cargoModel->eliminar($id)
            ? 'Cargo eliminado correctamente'
            : 'Error al eliminar el cargo';

        header('Location: /cargos');
        exit;
    }

    // Verifica si el usuario está autenticado
    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
