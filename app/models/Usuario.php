<?php
class Usuario
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function obtenerTodos()
    {
        $this->db->query('SELECT * FROM usuarios ORDER BY id DESC');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id)
    {
        $this->db->query('SELECT * FROM usuarios WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function obtenerPorEmail($email)
    {
        $this->db->query('SELECT * FROM usuarios WHERE email = :email');
        $this->db->bind(':email', $email);
        return $this->db->single();
    }

    public function obtenerPorNombre($nombre)
    {
        $this->db->query('SELECT * FROM usuarios WHERE nombre = :nombre');
        $this->db->bind('nombre', $nombre);
        return $this->db->single();
    }

    public function registrar($datos)
    {
        $validacion = $this->validarDatos($datos);
        if ($validacion !== true) {
            return ['exito' => false, 'errores' => $validacion];
        }

        if ($this->existePorNombre($datos['nombre'])) {
            return ['exito' => false, 'errores' => ['nombre' => 'Ya existe un usuario con ese nombre.']];
        }

        if ($this->existePorEmail($datos['email'])) {
            return ['exito' => false, 'errores' => ['email' => 'Ya existe un usuario con ese correo.']];
        }

        $this->db->query('INSERT INTO usuarios (nombre, email, password, rol, estado) VALUES (:nombre, :email, :password, :rol, :estado)');
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':email', $datos['email']);
        $this->db->bind(':password', password_hash($datos['password'], PASSWORD_DEFAULT));
        $this->db->bind(':rol', $datos['rol'] ?? 'Usuario');
        $this->db->bind(':estado', 'activo');

        $exito = $this->db->execute();
        return [
            'exito' => $exito,
            'id' => $exito ? $this->db->lastInsertId() : null
        ];
    }

    public function actualizar($id, $datos)
    {
        $usuario = $this->obtenerPorId($id);
        if (!$usuario) {
            return ['exito' => false, 'errores' => ['general' => 'Usuario no encontrado']];
        }

        $validacion = $this->validarDatos($datos, false);
        if ($validacion !== true) {
            return ['exito' => false, 'errores' => $validacion];
        }

        // Verificar nombre y email duplicados en otros registros
        if ($this->existePorNombre($datos['nombre'], $id)) {
            return ['exito' => false, 'errores' => ['nombre' => 'Ya existe otro usuario con ese nombre.']];
        }

        if ($this->existePorEmail($datos['email'], $id)) {
            return ['exito' => false, 'errores' => ['email' => 'Ya existe otro usuario con ese correo.']];
        }

        $this->db->query('UPDATE usuarios SET nombre = :nombre, email = :email, rol = :rol, estado = :estado WHERE id = :id');
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':email', $datos['email']);
        $this->db->bind(':rol', $datos['rol']);
        $this->db->bind(':estado', $datos['estado']);
        $this->db->bind(':id', $id);

        $exito = $this->db->execute();
        return ['exito' => $exito];
    }

    public function actualizarPassword($id, $nuevaPassword)
    {
        $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        $this->db->query('UPDATE usuarios SET password = :password WHERE id = :id');
        $this->db->bind(':password', $hash);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function eliminar($id)
    {
        $this->db->query("UPDATE usuarios SET estado = 'inactivo' WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function validarDatos($datos, $validarPassword = true)
    {
        $errores = [];

        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio.';
        } elseif (strlen($datos['nombre']) > 100) {
            $errores['nombre'] = 'El nombre no debe exceder los 100 caracteres.';
        }

        if (empty($datos['email'])) {
            $errores['email'] = 'El correo es obligatorio.';
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'El correo no es válido.';
        } elseif (strlen($datos['email']) > 150) {
            $errores['email'] = 'El correo no debe exceder los 150 caracteres.';
        }

        if ($validarPassword) {
            if (empty($datos['password'])) {
                $errores['password'] = 'La contraseña es obligatoria.';
            } elseif (strlen($datos['password']) < 8) {
                $errores['password'] = 'La contraseña debe tener al menos 8 caracteres.';
            }
        }

        if (isset($datos['rol']) && !in_array($datos['rol'], ['Administrador', 'Usuario'])) {
            $errores['rol'] = 'Rol no válido.';
        }

        if (isset($datos['estado']) && !in_array($datos['estado'], ['activo', 'inactivo'])) {
            $errores['estado'] = 'Estado no válido.';
        }

        return empty($errores) ? true : $errores;
    }

    public function existePorNombre($nombre, $excluirId = null)
    {
        $query = 'SELECT id FROM usuarios WHERE nombre = :nombre';
        if ($excluirId !== null) {
            $query .= ' AND id != :id';
        }

        $this->db->query($query);
        $this->db->bind(':nombre', $nombre);
        if ($excluirId !== null) {
            $this->db->bind(':id', $excluirId);
        }

        return $this->db->single() !== false;
    }

    public function existePorEmail($email, $excluirId = null)
    {
        $query = 'SELECT id FROM usuarios WHERE email = :email';
        if ($excluirId !== null) {
            $query .= ' AND id != :id';
        }

        $this->db->query($query);
        $this->db->bind(':email', $email);
        if ($excluirId !== null) {
            $this->db->bind(':id', $excluirId);
        }

        return $this->db->single() !== false;
    }

    public function obtenerTotal()
    {
        $this->db->query('SELECT COUNT(*) as total FROM usuarios');
        $resultado = $this->db->single();
        return $resultado->total;
    }
}
