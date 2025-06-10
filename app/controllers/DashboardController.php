<?php
class DashboardController
{
    private $db;
    private $userModel;
    private $cargoModel;
    private $empleadoModel;
    private $asistenciaModel;

    public function __construct($db)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->db = $db;
        $this->userModel = new Usuario($db);
        $this->cargoModel = new Cargo($db);
        $this->empleadoModel = new Empleado($db);
        $this->asistenciaModel = new Asistencia($db);

        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function index()
    {
        $user = $this->userModel->obtenerPorId($_SESSION['user_id']);

        $data = [
            'title' => 'Dashboard',
            'user' => $user,
            'total_cargos' => $this->cargoModel->obtenerTotal(),
            'total_empleados' => $this->empleadoModel->obtenerTotal(),
            'total_asistencias' => $this->asistenciaModel->obtenerTotal(),
            'asistencias_hoy' => $this->asistenciaModel->contarDeHoy(),
            'estado_asistencias' => $this->asistenciaModel->contarPorEstadoHoy(),
            'ultimasEntradas' => $this->asistenciaModel->ultimasEntradas()
        ];

        $current_page = 'dashboard';
        
        $view = 'admin/dashboard/index.php';

        require_once __DIR__ . '/../views/include/layout.php';
    }


    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
