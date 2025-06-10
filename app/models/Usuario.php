<?php

class Usuario
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Registrar nuevo usuario
    public function registrar($data)
    {
        $this->db->query('
            INSERT INTO usuarios (nombre, email, password, rol, estado)
            VALUES (:nombre, :email, :password, :rol, :estado)
        ');
        $this->db->bind(':nombre', $data['nombre']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':rol', $data['rol'] ?? 'Administrador');
        $this->db->bind(':estado', 'activo');

        return $this->db->execute();
    }

    // Iniciar sesión
    public function login($email, $password)
    {
        $this->db->query('SELECT * FROM usuarios WHERE email = :email AND estado = "activo" LIMIT 1');
        $this->db->bind(':email', $email);
        $usuario = $this->db->single();

        if ($usuario && password_verify($password, $usuario->password)) {
            $usuario->permisos = $this->obtenerPermisos($usuario->id);
            return $usuario;
        }

        return false;
    }

    // Verificar si ya existe un correo
    public function existeEmail($email)
    {
        $this->db->query('SELECT id FROM usuarios WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->rowCount() > 0;
    }

    // Obtener usuario por ID
    public function obtenerPorId($id)
    {
        $this->db->query('SELECT * FROM usuarios WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Obtener todos los usuarios
    public function obtenerTodos()
    {
        $this->db->query('SELECT * FROM usuarios ORDER BY id DESC');
        return $this->db->resultSet();
    }

    // Actualizar usuario
    public function actualizar($id, $data)
    {
        $this->db->query('
            UPDATE usuarios
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

    // Eliminar usuario (desactiva el estado)
    public function eliminar($id)
    {
        $this->db->query('UPDATE usuarios SET estado = "inactivo" WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Obtener permisos del usuario
    public function obtenerPermisos($usuarioId)
    {
        $this->db->query('
            SELECT p.nombre
            FROM detalle_permisos dp
            INNER JOIN permisos p ON dp.id_permiso = p.id
            WHERE dp.id_usuario = :usuario_id
        ');
        $this->db->bind(':usuario_id', $usuarioId);
        return array_column($this->db->resultSet(), 'nombre');
    }

    private $table_name = "usuarios"; // Asegúrate de que este sea el nombre de tu tabla de usuarios

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

    public function obtenerTotal()
    {
        $this->db->query('SELECT COUNT(*) as total FROM usuarios');
        $resultado = $this->db->single();
        return $resultado->total;
    }
}
