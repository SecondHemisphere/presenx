<?php
class AuthController
{
    private $db;
    private $userModel;

    public function __construct($db)
    {
        session_start();
        $this->db = $db;
        $this->userModel = new User($db);
    }

    public function showLogin()
    {
        if ($this->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }

        $esLogin = true;
        $view = __DIR__ . '/../views/auth/login.php';
        require_once __DIR__ . '/../views/layouts/layout.php';
    }

    public function showRegister()
    {
        if ($this->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }

        $esLogin = true;
        $view = __DIR__ . '/../views/auth/register.php';
        require_once __DIR__ . '/../views/layouts/layout.php';
    }

    public function login($data)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
            $password = trim($data['password']);

            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
                $_SESSION['error'] = 'Credenciales inválidas.';
                header('Location: /login');
                exit;
            }

            $user = $this->userModel->login($email, $password);

            if ($user) {
                $user->permisos = $this->userModel->getPermisos($user->id);
                $this->createUserSession($user);
                header('Location: /dashboard');
                exit;
            } else {
                $_SESSION['error'] = 'Email o contraseña incorrectos.';
                header('Location: /login');
                exit;
            }
        }
    }

    public function register($data)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = htmlspecialchars(trim($data['name']));
            $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
            $password = trim($data['password']);
            $confirmPassword = trim($data['confirm_password']);

            if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
                $_SESSION['error'] = 'Complete todos los campos correctamente.';
                header('Location: /register');
                exit;
            }

            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Las contraseñas no coinciden.';
                header('Location: /register');
                exit;
            }

            if ($this->userModel->findUserByEmail($email)) {
                $_SESSION['error'] = 'El email ya está registrado.';
                header('Location: /register');
                exit;
            }

            if ($this->userModel->register([
                'name' => $name,
                'email' => $email,
                'password' => $password
            ])) {
                $_SESSION['success'] = 'Registro exitoso. Inicie sesión.';
                header('Location: /login');
                exit;
            } else {
                $_SESSION['error'] = 'Error al registrar. Intente nuevamente.';
                header('Location: /register');
                exit;
            }
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }

    private function createUserSession($user)
    {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = htmlspecialchars($user->name);
        $_SESSION['user_rol'] = $user->rol ?? 'Estudiante';
        $_SESSION['user_permisos'] = $user->permisos ?? [];
    }

    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
