<?php
class DashboardController
{
    private $db;
    private $usuarioModel;
    private $cargoModel;
    private $empleadoModel;
    private $asistenciaModel;

    public function __construct($db)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = $db;
        $this->usuarioModel = new Usuario($db);
        $this->cargoModel = new Cargo($db);
        $this->empleadoModel = new Empleado($db);
        $this->asistenciaModel = new Asistencia($db);

        if (!$this->estaLogueado()) {
            header('Location: /login');
            exit;
        }
    }

    public function index()
    {
        $usuario = $this->usuarioModel->obtenerPorId($_SESSION['user_id']);

        $total_empleados = $this->empleadoModel->obtenerTotal();
        $asistencias_hoy = $this->asistenciaModel->contarDeHoy();

        $porcentaje = 0;
        if ($total_empleados > 0) {
            $porcentaje = ($asistencias_hoy / $total_empleados) * 100;
            $porcentaje = min($porcentaje, 100);
        }

        $datos = [
            'title' => 'Dashboard',
            'user' => $usuario,
            'total_usuarios' => $this->usuarioModel->obtenerTotal(),
            'total_cargos' => $this->cargoModel->obtenerTotal(),
            'total_empleados' => $total_empleados,
            'porcentaje' => $porcentaje,
            'total_asistencias' => $this->asistenciaModel->obtenerTotal(),
            'asistencias_hoy' => $asistencias_hoy,
            'estado_asistencias' => $this->asistenciaModel->contarPorEstadoHoy(),
            'ultimasEntradas' => $this->asistenciaModel->ultimasEntradas()
        ];

        $pagina_actual = 'dashboard';

        $vista = 'admin/dashboard/index.php';

        require_once __DIR__ . '/../views/include/layout.php';
    }

    private function estaLogueado()
    {
        return isset($_SESSION['user_id']);
    }
}
