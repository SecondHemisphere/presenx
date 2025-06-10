<?php
class EmpresaController
{
    private $db;
    private $empresaModel;

    public function __construct($db)
    {
        $this->db = $db;
        $this->empresaModel = new Empresa($db);

        if (!$this->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }

    public function configuracion()
    {
        $empresa = $this->empresaModel->obtenerPorId(1);
        if (!$empresa) {
            $_SESSION['error_message'] = 'Empresa no encontrada.';
            header('Location: /dashboard');
            exit;
        }

        $modo = isset($_GET['edit']) ? 'editar' : 'ver';
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => trim($_POST['nombre']),
                'ruc' => trim($_POST['ruc']),
                'telefono' => trim($_POST['telefono']),
                'email' => trim($_POST['email']),
                'ubicacion' => trim($_POST['ubicacion']),
            ];

            $resultado = $this->empresaModel->actualizar(1, $datos);

            if ($resultado['exito']) {
                $_SESSION['success_message'] = 'Datos actualizados correctamente.';
                header('Location: /empresa/configuracion');
                exit;
            } else {
                $errors = $resultado['errores'];
                $empresa = (object) $datos;
                $modo = 'editar';
            }
        }

        $data = [
            'title' => 'Configuración de Empresa',
            'empresa' => $empresa,
            'errors' => $errors,
            'modo' => $modo,
            'form_action' => '/empresa/configuracion',
            'current_page' => 'empresas'
        ];

        $view = 'admin/empresa/configuracion.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
