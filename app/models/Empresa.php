<?php
class Empresa
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function obtenerTodos()
    {
        $this->db->query("SELECT * FROM empresas ORDER BY id DESC");
        return $this->db->resultSet();
    }

    public function obtenerPorId($id)
    {
        $this->db->query("SELECT * FROM empresas WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function registrar($datos)
    {
        $errores = $this->validarDatos($datos);
        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        // Verificar RUC duplicado
        if ($this->existeRuc($datos['ruc'])) {
            return ['exito' => false, 'errores' => ['ruc' => 'El RUC ya está registrado.']];
        }

        $this->db->query("
            INSERT INTO empresas (nombre, telefono, email, ubicacion, ruc)
            VALUES (:nombre, :telefono, :email, :ubicacion, :ruc)
        ");

        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':telefono', $datos['telefono']);
        $this->db->bind(':email', $datos['email']);
        $this->db->bind(':ubicacion', $datos['ubicacion']);
        $this->db->bind(':ruc', $datos['ruc']);

        $exito = $this->db->execute();
        return [
            'exito' => $exito,
            'id' => $exito ? $this->db->lastInsertId() : null
        ];
    }

    public function actualizar($id, $datos)
    {
        $empresa = $this->obtenerPorId($id);
        if (!$empresa) {
            return ['exito' => false, 'errores' => ['general' => 'Empresa no encontrada']];
        }

        $errores = $this->validarDatos($datos, true);
        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
        }

        // Verificar RUC duplicado en otra empresa
        if ($this->existeRuc($datos['ruc'], $id)) {
            return ['exito' => false, 'errores' => ['ruc' => 'Ya existe otra empresa con ese RUC.']];
        }

        $this->db->query("
            UPDATE empresas SET
                nombre = :nombre,
                telefono = :telefono,
                email = :email,
                ubicacion = :ubicacion,
                ruc = :ruc
            WHERE id = :id
        ");

        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':telefono', $datos['telefono']);
        $this->db->bind(':email', $datos['email']);
        $this->db->bind(':ubicacion', $datos['ubicacion']);
        $this->db->bind(':ruc', $datos['ruc']);
        $this->db->bind(':id', $id);

        $exito = $this->db->execute();
        return ['exito' => $exito];
    }

    public function eliminar($id)
    {
        $this->db->query("DELETE FROM empresas WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function existeRuc($ruc, $excluirId = null)
    {
        $query = "SELECT id FROM empresas WHERE ruc = :ruc";
        if ($excluirId !== null) {
            $query .= " AND id != :id";
        }

        $this->db->query($query);
        $this->db->bind(':ruc', $ruc);
        if ($excluirId !== null) {
            $this->db->bind(':id', $excluirId);
        }

        return $this->db->single() !== false;
    }

    public function validarDatos($datos, $esActualizacion = false)
    {
        $errores = [];

        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio.';
        }

        if (empty($datos['ruc'])) {
            $errores['ruc'] = 'El RUC es obligatorio.';
        } elseif (!preg_match('/^\d{13}$/', $datos['ruc'])) {
            $errores['ruc'] = 'El RUC debe tener 13 dígitos.';
        }

        if (!empty($datos['email']) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores['email'] = 'El email no es válido.';
        }

        return $errores;
    }
}
