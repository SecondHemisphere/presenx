<?php
class HomeController
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function index()
    {
        $current_page = 'home';
        $esLogin = true;
        $view = 'home.php';

        require_once __DIR__ . '/../views/include/layout.php';
    }


    private function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}
