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

        $view = 'admin/asistencias/index.php';
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

        $view = 'admin/asistencias/create.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function storeUsuario()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /');
            exit;
        }

        $cedula = trim($_POST['cedula'] ?? '');
        $tipo = $_POST['tipo'] ?? '';

        // Validaciones básicas
        if (empty($cedula) || !preg_match('/^\d{10}$/', $cedula)) {
            $_SESSION['error_message'] = 'Cédula inválida. Debe tener 10 dígitos.';
            header('Location: /');
            exit;
        }

        if (!in_array($tipo, ['entrada', 'salida'])) {
            $_SESSION['error_message'] = 'Tipo de registro inválido.';
            header('Location: /');
            exit;
        }

        $empleado = $this->empleadoModel->obtenerPorCedula($cedula);
        if (!$empleado) {
            $_SESSION['error_message'] = 'Empleado no encontrado con esa cédula.';
            header('Location: /');
            exit;
        }

        $hoy = date('Y-m-d');
        $ahora = date('Y-m-d H:i:s');

        $registroHoy = $this->asistenciaModel->obtenerPorEmpleadoYFecha($empleado->id, $hoy);

        if ($tipo === 'entrada') {
            if ($registroHoy && !empty($registroHoy->entrada)) {
                $_SESSION['error_message'] = 'Ya se ha registrado la entrada hoy.';
            } else {
                $inputData = [
                    'id_empleado' => $empleado->id,
                    'entrada' => $ahora,
                    'salida' => null
                ];

                $result = $this->asistenciaModel->registrar($inputData);

                if ($result['exito']) {
                    $_SESSION['success_message'] = 'Entrada registrada correctamente.';
                } else {
                    $_SESSION['error_message'] = $result['errores'][0] ?? 'Error al registrar la entrada.';
                }
            }
        }

        if ($tipo === 'salida') {
            if (!$registroHoy) {
                $_SESSION['error_message'] = 'No se ha registrado entrada hoy.';
            } elseif (is_null($registroHoy->entrada) || $registroHoy->entrada === '') {
                $_SESSION['error_message'] = 'No se ha registrado entrada hoy.';
            } elseif (!is_null($registroHoy->salida) && $registroHoy->salida !== '') {
                $_SESSION['error_message'] = 'Ya se ha registrado la salida hoy.';
            } else {
                $result = $this->asistenciaModel->actualizarSalida($registroHoy->id, $ahora);

                if ($result['exito']) {
                    $_SESSION['success_message'] = 'Salida registrada correctamente.';
                } else {
                    $_SESSION['error_message'] = $result['errores'][0] ?? 'Error al registrar la salida.';
                }
            }
        }

        header('Location: /');
        exit;
    }

    public function store()
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

            // Eliminar campos inexistentes por seguridad
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

                $view = 'admin/asistencias/create.php';
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
