<?php
class EmpleadoController
{
    private $db;
    private $empleadoModel;
    private $cargoModel;
    private $empresaModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->empleadoModel = new Empleado($db);
        $this->cargoModel = new Cargo($db);
        $this->empresaModel = new Empresa($db);

        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function index()
    {
        $empleados = $this->empleadoModel->obtenerTodos();

        $data = [
            'title' => 'Listado de Empleados',
            'empleados' => $empleados,
            'success_message' => $_SESSION['success_message'] ?? null,
            'error_message' => $_SESSION['error_message'] ?? null,
            'current_page' => 'empleados'
        ];

        unset($_SESSION['success_message'], $_SESSION['error_message']);

        $view = __DIR__ . '/../views/empleados/index.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function create()
    {
        $cargos = $this->cargoModel->obtenerTodos();

        $data = [
            'title' => 'Registrar Nuevo Empleado',
            'empleado' => new stdClass(),
            'errors' => [],
            'form_action' => '/empleados/store',
            'current_page' => 'empleados',
            'cargos' => $cargos,
        ];

        $view = __DIR__ . '/../views/empleados/create.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = $_POST;
            $inputData['foto'] = $_FILES['foto']['name'] ?? null;

            $result = $this->empleadoModel->registrar($inputData);

            if ($result['exito']) {
                $_SESSION['success_message'] = 'Empleado registrado correctamente';
                header('Location: /empleados');
                exit;
            } else {
                $data = [
                    'title' => 'Registrar Nuevo Empleado',
                    'empleado' => (object) $inputData,
                    'errors' => $result['errores'],
                    'form_action' => '/empleados/store',
                    'current_page' => 'empleados'
                ];

                $view = __DIR__ . '/../views/empleados/create.php';
                require_once __DIR__ . '/../views/include/layout.php';
            }
        }
    }

    public function edit($id)
    {
        $empleado = $this->empleadoModel->obtenerPorId($id);

        $cargos = $this->cargoModel->obtenerTodos();

        if (!$empleado) {
            $_SESSION['error_message'] = 'Empleado no encontrado';
            header('Location: /empleados');
            exit;
        }

        $data = [
            'title' => 'Editar Empleado',
            'empleado' => $empleado,
            'errors' => [],
            'form_action' => "/empleados/update/$id",
            'current_page' => 'empleados',
            'cargos' => $cargos,
        ];

        $view = __DIR__ . '/../views/empleados/edit.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = $_POST;
            $inputData['foto'] = $_FILES['foto']['name'] ?? null;

            $result = $this->empleadoModel->actualizar($id, $inputData);

            if ($result['exito']) {
                $_SESSION['success_message'] = 'Empleado actualizado correctamente';
                header('Location: /empleados');
                exit;
            } else {
                $original = $this->empleadoModel->obtenerPorId($id);

                $data = [
                    'title' => 'Editar Empleado',
                    'empleado' => (object) array_merge((array) $original, $inputData),
                    'errors' => $result['errores'],
                    'form_action' => "/empleados/update/$id",
                    'current_page' => 'empleados'
                ];

                $view = __DIR__ . '/../views/empleados/edit.php';
                require_once __DIR__ . '/../views/include/layout.php';
            }
        }
    }

    public function delete($id)
    {
        $_SESSION['success_message'] = $this->empleadoModel->eliminar($id)
            ? 'Empleado eliminado correctamente'
            : 'Error al eliminar el empleado';

        header('Location: /empleados');
        exit;
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
