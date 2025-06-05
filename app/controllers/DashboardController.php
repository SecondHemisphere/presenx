<?php
class DashboardController
{
    private $db;
    private $userModel;

    // Constructor: inicializa la conexión a la base de datos y el modelo de usuario.
    // Si el usuario no ha iniciado sesión, lo redirige al login.
    public function __construct($db)
    {
        $this->db = $db;
        $this->userModel = new User($db);

        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    // Muestra la vista del panel de control (dashboard)
    public function index()
    {
        // Obtiene los datos del usuario actual desde la sesión
        $user = $this->userModel->getUserById($_SESSION['user_id']);

        // Datos que se pasan a la vista
        $data = [
            'title' => 'Dashboard',
            'user' => $user,
            'total_usuarios' => 2,
            'total_libros' => 4,
            'total_autores' => 1,
            'total_editoriales' => 8,
            'total_estudiantes' => 1,
            'total_prestamos' => 0,
            'total_materias' => 5,
        ];

        // Variable opcional para marcar el menú activo en la vista
        $current_page = 'dashboard';

        // Carga la vista del dashboard dentro del layout general
        $view = __DIR__ . '/../views/dashboard/index.php';
        require_once __DIR__ . '/../views/layouts/layout.php';
    }

    // Verifica si el usuario ha iniciado sesión
    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
