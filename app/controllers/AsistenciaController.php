<?php
class AsistenciaController
{
    private $db;
    private $asistenciaModel;
    private $empleadoModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->asistenciaModel = new Asistencia($db);
        $this->empleadoModel = new Empleado($db);

        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function index()
    {
        $asistencias = $this->asistenciaModel->obtenerTodos();

        $data = [
            'title' => 'Listado de Asistencias',
            'asistencias' => $asistencias,
            'success_message' => $_SESSION['success_message'] ?? null,
            'error_message' => $_SESSION['error_message'] ?? null,
            'current_page' => 'asistencias'
        ];

        unset($_SESSION['success_message'], $_SESSION['error_message']);

        $view = __DIR__ . '/../views/asistencias/index.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function create()
    {
        $empleados = $this->empleadoModel->obtenerTodos();

        $data = [
            'title' => 'Registrar Asistencia',
            'asistencia' => new stdClass(),
            'errors' => [],
            'form_action' => '/asistencias/store',
            'empleados' => $empleados,
            'current_page' => 'asistencias'
        ];

        $view = __DIR__ . '/../views/asistencias/create.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = $_POST;

            // Construcción de timestamps si se da solo la hora
            $hoy = date('Y-m-d');
            if (!empty($inputData['entrada']) && preg_match('/^\d{2}:\d{2}$/', $inputData['entrada'])) {
                $inputData['entrada'] = "$hoy {$inputData['entrada']}:00";
            }
            if (!empty($inputData['salida']) && preg_match('/^\d{2}:\d{2}$/', $inputData['salida'])) {
                $inputData['salida'] = "$hoy {$inputData['salida']}:00";
            }

            // Eliminar campos innecesarios
            unset($inputData['observaciones'], $inputData['registrado_por']);

            $result = $this->asistenciaModel->registrar($inputData);

            if ($result['exito']) {
                $_SESSION['success_message'] = 'Asistencia registrada correctamente';
                header('Location: /asistencias');
                exit;
            } else {
                $empleados = $this->empleadoModel->obtenerTodos();

                $data = [
                    'title' => 'Registrar Asistencia',
                    'asistencia' => (object) $inputData,
                    'errors' => $result['errores'] ?? [],
                    'form_action' => '/asistencias/store',
                    'empleados' => $empleados,
                    'current_page' => 'asistencias'
                ];

                $view = __DIR__ . '/../views/asistencias/create.php';
                require_once __DIR__ . '/../views/include/layout.php';
            }
        }
    }

    public function edit($id)
    {
        $asistencia = $this->asistenciaModel->obtenerPorId($id);

        if (!$asistencia) {
            $_SESSION['error_message'] = 'Asistencia no encontrada';
            header('Location: /asistencias');
            exit;
        }

        $empleados = $this->empleadoModel->obtenerTodos();

        $data = [
            'title' => 'Editar Asistencia',
            'asistencia' => $asistencia,
            'errors' => [],
            'form_action' => "/asistencias/update/$id",
            'empleados' => $empleados,
            'current_page' => 'asistencias'
        ];

        $view = __DIR__ . '/../views/asistencias/edit.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = $_POST;

            $hoy = date('Y-m-d');
            if (!empty($inputData['entrada']) && preg_match('/^\d{2}:\d{2}$/', $inputData['entrada'])) {
                $inputData['entrada'] = "$hoy {$inputData['entrada']}:00";
            }
            if (!empty($inputData['salida']) && preg_match('/^\d{2}:\d{2}$/', $inputData['salida'])) {
                $inputData['salida'] = "$hoy {$inputData['salida']}:00";
            }

            // Eliminar campos innecesarios
            unset($inputData['observaciones'], $inputData['registrado_por']);

            $result = $this->asistenciaModel->actualizar($id, $inputData);

            if ($result['exito']) {
                $_SESSION['success_message'] = 'Asistencia actualizada correctamente';
                header('Location: /asistencias');
                exit;
            } else {
                $original = $this->asistenciaModel->obtenerPorId($id);
                $empleados = $this->empleadoModel->obtenerTodos();

                $data = [
                    'title' => 'Editar Asistencia',
                    'asistencia' => (object) array_merge((array) $original, $inputData),
                    'errors' => $result['errores'] ?? [],
                    'form_action' => "/asistencias/update/$id",
                    'empleados' => $empleados,
                    'current_page' => 'asistencias'
                ];

                $view = __DIR__ . '/../views/asistencias/edit.php';
                require_once __DIR__ . '/../views/include/layout.php';
            }
        }
    }

    public function delete($id)
    {
        $_SESSION['success_message'] = $this->asistenciaModel->eliminar($id)
            ? 'Asistencia eliminada correctamente'
            : 'Error al eliminar la asistencia';

        header('Location: /asistencias');
        exit;
    }

    public function obtenerTotal()
    {
        $this->db->query("SELECT COUNT(*) as total FROM asistencias");
        $resultado = $this->db->single();
        return $resultado->total;
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
