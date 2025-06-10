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
        $this->db->query("SELECT * FROM usuarios ORDER BY id DESC");
        return $this->db->resultSet();
    }

    public function obtenerPorId($id)
    {
        $this->db->query("SELECT * FROM usuarios WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function registrar($datos)
    {
        $errores = $this->validarDatos($datos);
        if ($errores !== true) {
            return ['exito' => false, 'errores' => $errores];
        }

        if ($this->emailExiste($datos['email'])) {
            return ['exito' => false, 'errores' => ['email' => 'Ya existe un usuario con ese correo.']];
        }

        $this->db->query("
            INSERT INTO usuarios (nombre, email, password, rol, estado)
            VALUES (:nombre, :email, :password, :rol, :estado)
        ");
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
            return ['exito' => false, 'errores' => ['general' => 'Usuario no encontrado.']];
        }

        $errores = $this->validarDatos($datos, false);
        if ($errores !== true) {
            return ['exito' => false, 'errores' => $errores];
        }

        if ($this->emailExiste($datos['email'], $id)) {
            return ['exito' => false, 'errores' => ['email' => 'Ya existe otro usuario con ese correo.']];
        }

        $this->db->query("
            UPDATE usuarios
            SET nombre = :nombre, email = :email, rol = :rol, estado = :estado
            WHERE id = :id
        ");
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':email', $datos['email']);
        $this->db->bind(':rol', $datos['rol']);
        $this->db->bind(':estado', $datos['estado']);
        $this->db->bind(':id', $id);

        return ['exito' => $this->db->execute()];
    }

    public function actualizarPassword($id, $nuevaPassword)
    {
        $hash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
        $this->db->query("UPDATE usuarios SET password = :password WHERE id = :id");
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

    public function iniciarSesion($email, $password)
    {
        $this->db->query("SELECT * FROM usuarios WHERE email = :email AND estado = 'activo' LIMIT 1");
        $this->db->bind(':email', $email);
        $usuario = $this->db->single();

        if ($usuario && password_verify($password, $usuario->password)) {
            return $usuario;
        }

        return false;
    }

    public function emailExiste($email, $excluirId = null)
    {
        $consulta = "SELECT id FROM usuarios WHERE email = :email";
        if ($excluirId !== null) {
            $consulta .= " AND id != :id";
        }

        $this->db->query($consulta);
        $this->db->bind(':email', $email);
        if ($excluirId !== null) {
            $this->db->bind(':id', $excluirId);
        }

        return $this->db->rowCount() > 0;
    }

    public function contarUsuarios()
    {
        $this->db->query("SELECT COUNT(*) as total FROM usuarios");
        $resultado = $this->db->single();
        return $resultado->total;
    }

    public function validarDatos($datos, $validarPassword = true)
    {
        $errores = [];

        if ($error = $this->validarNombre($datos['nombre'] ?? '')) {
            $errores['nombre'] = $error;
        }

        if ($error = $this->validarEmail($datos['email'] ?? '')) {
            $errores['email'] = $error;
        }

        if ($validarPassword && ($error = $this->validarPassword($datos['password'] ?? ''))) {
            $errores['password'] = $error;
        }

        if (isset($datos['rol']) && ($error = $this->validarRol($datos['rol']))) {
            $errores['rol'] = $error;
        }

        if (isset($datos['estado']) && ($error = $this->validarEstado($datos['estado']))) {
            $errores['estado'] = $error;
        }

        return empty($errores) ? true : $errores;
    }

    public function validarNombre($nombre)
    {
        if (empty($nombre)) return 'El nombre es obligatorio.';
        if (strlen($nombre) > 100) return 'El nombre no debe exceder los 100 caracteres.';
        return null;
    }

    public function validarEmail($email)
    {
        if (empty($email)) return 'El correo es obligatorio.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return 'El correo no es válido.';
        if (strlen($email) > 150) return 'El correo no debe exceder los 150 caracteres.';
        return null;
    }

    public function validarPassword($password)
    {
        if (empty($password)) return 'La contraseña es obligatoria.';
        if (strlen($password) < 8) return 'La contraseña debe tener al menos 8 caracteres.';
        return null;
    }

    public function validarRol($rol)
    {
        if (!in_array($rol, ['Administrador', 'Usuario'])) return 'Rol no válido.';
        return null;
    }

    public function validarEstado($estado)
    {
        if (!in_array($estado, ['activo', 'inactivo'])) return 'Estado no válido.';
        return null;
    }
}
