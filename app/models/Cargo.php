<?php
class Cargo
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function obtenerTodos()
    {
        $this->db->query('SELECT * FROM cargos ORDER BY id DESC');
        return $this->db->resultSet();
    }

    public function obtenerPorId($id)
    {
        $this->db->query('SELECT * FROM cargos WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function registrar($datos)
    {
        $validacion = $this->validarDatos($datos);
        if ($validacion !== true) {
            return ['exito' => false, 'errores' => $validacion];
        }

        // Verificar si el nombre ya existe
        if ($this->existePorNombre($datos['nombre'])) {
            return ['exito' => false, 'errores' => ['nombre' => 'Ya existe un cargo con ese nombre.']];
        }

        $this->db->query('INSERT INTO cargos (nombre, descripcion) VALUES (:nombre, :descripcion)');
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':descripcion', $datos['descripcion']);

        $exito = $this->db->execute();
        return [
            'exito' => $exito,
            'id' => $exito ? $this->db->lastInsertId() : null
        ];
    }

    public function actualizar($id, $datos)
    {
        $cargo = $this->obtenerPorId($id);
        if (!$cargo) {
            return ['exito' => false, 'errores' => ['general' => 'Cargo no encontrado']];
        }

        $validacion = $this->validarDatos($datos);
        if ($validacion !== true) {
            return ['exito' => false, 'errores' => $validacion];
        }

        // Verificar si el nombre ya existe en otro cargo
        if ($this->existePorNombre($datos['nombre'], $id)) {
            return ['exito' => false, 'errores' => ['nombre' => 'Ya existe otro cargo con ese nombre.']];
        }

        $this->db->query('UPDATE cargos SET nombre = :nombre, descripcion = :descripcion WHERE id = :id');
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':descripcion', $datos['descripcion']);
        $this->db->bind(':id', $id);

        $exito = $this->db->execute();
        return ['exito' => $exito];
    }

    public function eliminar($id)
    {
        $this->db->query('DELETE FROM cargos WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function validarDatos($datos)
    {
        $errores = [];

        if (empty($datos['nombre'])) {
            $errores['nombre'] = 'El nombre del cargo es obligatorio.';
        } elseif (strlen($datos['nombre']) > 100) {
            $errores['nombre'] = 'El nombre no debe exceder los 100 caracteres.';
        }

        if (!empty($datos['descripcion']) && strlen($datos['descripcion']) > 1000) {
            $errores['descripcion'] = 'La descripción no debe exceder los 1000 caracteres.';
        }

        return empty($errores) ? true : $errores;
    }

    /**
     * Verificar si ya existe un cargo con el mismo nombre.
     * Si se pasa un ID, se excluye ese ID de la verificación (para actualizar).
     * @param string $nombre Nombre del cargo.
     * @param int|null $excluirId ID a excluir de la verificación (opcional).
     * @return bool
     */
    public function existePorNombre($nombre, $excluirId = null)
    {
        $query = 'SELECT * FROM cargos WHERE nombre = :nombre';
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

    public function obtenerTotal()
    {
        $this->db->query('SELECT COUNT(*) as total FROM cargos');
        $resultado = $this->db->single();
        return $resultado->total;
    }
}
