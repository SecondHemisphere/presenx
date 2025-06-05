<?php
class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function register($data)
    {
        $this->db->query('INSERT INTO usuarios (name, email, password, rol) VALUES (:name, :email, :password, :rol)');
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':rol', isset($data['rol']) ? $data['rol'] : 'Estudiante');

        return $this->db->execute();
    }

    public function login($email, $password)
    {
        // Trae también el rol desde la tabla usuarios
        $this->db->query('SELECT * FROM usuarios WHERE email = :email');
        $this->db->bind(':email', $email);
        $user = $this->db->single();

        if ($user && password_verify($password, $user->password)) {
            // Obtener los permisos asociados desde la tabla detalle_permisos
            $this->db->query('
                SELECT p.nombre
                FROM detalle_permisos dp
                INNER JOIN permisos p ON dp.id_permiso = p.id
                WHERE dp.id_usuario = :user_id
            ');
            $this->db->bind(':user_id', $user->id);
            $permisos = $this->db->resultSet();

            // Convertir los permisos a un array simple
            $user->permisos = array_map(fn($permiso) => $permiso->nombre, $permisos);

            return $user;
        }

        return false;
    }

    public function findUserByEmail($email)
    {
        $this->db->query('SELECT * FROM usuarios WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->rowCount() > 0;
    }

    public function getUserById($id)
    {
        $this->db->query('SELECT * FROM usuarios WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function getPermisos($userId)
    {
        $this->db->query('
        SELECT p.nombre
        FROM detalle_permisos dp
        INNER JOIN permisos p ON dp.id_permiso = p.id
        WHERE dp.id_usuario = :user_id
    ');
        $this->db->bind(':user_id', $userId);
        return array_column($this->db->resultSet(), 'nombre');
    }
}
