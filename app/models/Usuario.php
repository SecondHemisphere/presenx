<?php

class Usuario
{
    private $db;
    private $table_name = "usuarios";

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Registrar nuevo usuario
    public function registrar($data)
    {
        $this->db->query('
            INSERT INTO ' . $this->table_name . ' (nombre, email, password, rol, estado)
            VALUES (:nombre, :email, :password, :rol, :estado)
        ');
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':rol', $data['rol'] ?? 'Usuario');
        $this->db->bind(':estado', 'activo');

        return $this->db->execute();
    }

    // Iniciar sesión
    public function login($email, $password)
    {
        $this->db->query('SELECT * FROM ' . $this->table_name . ' WHERE email = :email AND estado = "activo" LIMIT 1');
        $this->db->bind(':email', $email);
        $usuario = $this->db->single();

        if ($usuario && password_verify($password, $usuario->password)) {
            return $usuario;
        }

        return false;
    }

    // Verificar si ya existe un correo
    public function existeEmail($email)
    {
        $this->db->query('SELECT id FROM ' . $this->table_name . ' WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->rowCount() > 0;
    }

    // Obtener usuario por ID
    public function obtenerPorId($id)
    {
        $this->db->query('SELECT * FROM ' . $this->table_name . ' WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Obtener todos los usuarios
    public function obtenerTodos()
    {
        $this->db->query('SELECT * FROM ' . $this->table_name . ' ORDER BY id DESC');
        return $this->db->resultSet();
    }

    // Actualizar usuario (sin cambiar password)
    public function actualizar($id, $data)
    {
        $this->db->query('
            UPDATE ' . $this->table_name . '
            SET nombre = :nombre, email = :email, rol = :rol, estado = :estado
            WHERE id = :id
        ');
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':rol', $data['rol']);
        $this->db->bind(':estado', $data['estado']);
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    // Actualizar contraseña
    public function updatePassword($id, $new_hashed_password)
    {
        $this->db->query('
            UPDATE ' . $this->table_name . '
            SET password = :password
            WHERE id = :id
        ');
        $this->db->bind(':password', $new_hashed_password);
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    // Eliminar usuario (desactiva el estado)
    public function eliminar($id)
    {
        $this->db->query('UPDATE ' . $this->table_name . ' SET estado = "inactivo" WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Obtener total de usuarios
    public function obtenerTotal()
    {
        $this->db->query('SELECT COUNT(*) as total FROM ' . $this->table_name);
        $resultado = $this->db->single();
        return $resultado->total;
    }
}
