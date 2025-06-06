<?php
class Empresa
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function obtenerPorId($id)
    {
        $this->db->query("SELECT * FROM empresas WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function actualizar($id, $datos)
    {
        $empresa = $this->obtenerPorId($id);
        if (!$empresa) {
            return ['exito' => false, 'errores' => ['general' => 'Empresa no encontrada.']];
        }

        $errores = $this->validarDatos($datos, $id);
        if (!empty($errores)) {
            return ['exito' => false, 'errores' => $errores];
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

    public function validarDatos($datos, $excluirId = null)
    {
        $errores = [];

        // Nombre
        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre es obligatorio.';
        } elseif (mb_strlen($datos['nombre']) > 255) {
            $errores['nombre'] = 'El nombre no debe exceder 255 caracteres.';
        }

        // RUC
        if (empty($datos['ruc'])) {
            $errores['ruc'] = 'El RUC es obligatorio.';
        } elseif (!preg_match('/^\d{13}$/', $datos['ruc'])) {
            $errores['ruc'] = 'El RUC debe tener exactamente 13 dígitos.';
        } elseif ($this->existeRuc($datos['ruc'], $excluirId)) {
            $errores['ruc'] = 'El RUC ya está registrado en otra empresa.';
        }

        // Teléfono
        if (!empty($datos['telefono']) && mb_strlen($datos['telefono']) > 20) {
            $errores['telefono'] = 'El teléfono no debe exceder 20 caracteres.';
        }

        // Email
        if (!empty($datos['email'])) {
            if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
                $errores['email'] = 'El correo electrónico no es válido.';
            } elseif (mb_strlen($datos['email']) > 100) {
                $errores['email'] = 'El correo no debe exceder 100 caracteres.';
            }
        }

        // Ubicación
        if (!empty($datos['ubicacion']) && mb_strlen($datos['ubicacion']) > 255) {
            $errores['ubicacion'] = 'La ubicación no debe exceder 255 caracteres.';
        }

        return $errores;
    }
}
