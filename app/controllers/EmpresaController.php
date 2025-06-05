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

    public function index()
    {
        $empresas = $this->empresaModel->obtenerTodos();

        $data = [
            'title' => 'Listado de Empresas',
            'empresas' => $empresas,
            'success_message' => $_SESSION['success_message'] ?? null,
            'error_message' => $_SESSION['error_message'] ?? null,
            'current_page' => 'empresas'
        ];

        unset($_SESSION['success_message'], $_SESSION['error_message']);

        $view = __DIR__ . '/../views/empresas/index.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function create()
    {
        $data = [
            'title' => 'Registrar Nueva Empresa',
            'empresa' => new stdClass(),
            'errors' => [],
            'form_action' => '/empresas/store',
            'current_page' => 'empresas'
        ];

        $view = __DIR__ . '/../views/empresas/create.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = $_POST;

            $result = $this->empresaModel->registrar($inputData);

            if ($result['exito']) {
                $_SESSION['success_message'] = 'Empresa registrada correctamente';
                header('Location: /empresas');
                exit;
            } else {
                $data = [
                    'title' => 'Registrar Nueva Empresa',
                    'empresa' => (object) $inputData,
                    'errors' => $result['errores'],
                    'form_action' => '/empresas/store',
                    'current_page' => 'empresas'
                ];

                $view = __DIR__ . '/../views/empresas/create.php';
                require_once __DIR__ . '/../views/include/layout.php';
            }
        }
    }

    public function edit($id)
    {
        $empresa = $this->empresaModel->obtenerPorId($id);

        if (!$empresa) {
            $_SESSION['error_message'] = 'Empresa no encontrada';
            header('Location: /empresas');
            exit;
        }

        $data = [
            'title' => 'Editar Empresa',
            'empresa' => $empresa,
            'errors' => [],
            'form_action' => "/empresas/update/$id",
            'current_page' => 'empresas'
        ];

        $view = __DIR__ . '/../views/empresas/edit.php';
        require_once __DIR__ . '/../views/include/layout.php';
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inputData = $_POST;

            $result = $this->empresaModel->actualizar($id, $inputData);

            if ($result['exito']) {
                $_SESSION['success_message'] = 'Empresa actualizada correctamente';
                header('Location: /empresas');
                exit;
            } else {
                $original = $this->empresaModel->obtenerPorId($id);

                $data = [
                    'title' => 'Editar Empresa',
                    'empresa' => (object) array_merge((array) $original, $inputData),
                    'errors' => $result['errores'],
                    'form_action' => "/empresas/update/$id",
                    'current_page' => 'empresas'
                ];

                $view = __DIR__ . '/../views/empresas/edit.php';
                require_once __DIR__ . '/../views/include/layout.php';
            }
        }
    }

    public function delete($id)
    {
        $_SESSION['success_message'] = $this->empresaModel->eliminar($id)
            ? 'Empresa eliminada correctamente'
            : 'Error al eliminar la empresa';

        header('Location: /empresas');
        exit;
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
