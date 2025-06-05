<?php
class Cargo
{
    private $db;

    /**
     * Constructor de la clase Cargo.
     * @param Database $db Instancia de la conexión a la base de datos.
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Obtener todos los cargos.
     * @return array Lista de cargos.
     */
    public function obtenerTodos()
    {
        $this->db->query('SELECT * FROM cargos ORDER BY id DESC');
        return $this->db->resultSet();
    }

    /**
     * Obtener un cargo por su ID.
     * @param int $id ID del cargo.
     * @return object|false Objeto cargo o false si no existe.
     */
    public function obtenerPorId($id)
    {
        $this->db->query('SELECT * FROM cargos WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Registrar un nuevo cargo.
     * @param array $datos Datos del cargo.
     * @return array Resultado de la operación.
     */
    public function registrar($datos)
    {
        $validacion = $this->validarDatos($datos);
        if ($validacion !== true) {
            return ['exito' => false, 'errores' => $validacion];
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

    /**
     * Actualizar un cargo existente.
     * @param int $id ID del cargo.
     * @param array $datos Datos actualizados.
     * @return array Resultado de la operación.
     */
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

        $this->db->query('UPDATE cargos SET nombre = :nombre, descripcion = :descripcion WHERE id = :id');
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':descripcion', $datos['descripcion']);
        $this->db->bind(':id', $id);

        $exito = $this->db->execute();
        return ['exito' => $exito];
    }

    /**
     * Eliminar un cargo por su ID.
     * @param int $id ID del cargo.
     * @return bool True si se eliminó correctamente.
     */
    public function eliminar($id)
    {
        $this->db->query('DELETE FROM cargos WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Validar los datos de un cargo.
     * @param array $datos Datos a validar.
     * @return array|true Lista de errores o true si es válido.
     */
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
     * @param string $nombre Nombre del cargo.
     * @return bool True si existe, False si no.
     */
    public function existePorNombre($nombre)
    {
        $this->db->query('SELECT * FROM cargos WHERE nombre = :nombre');
        $this->db->bind(':nombre', $nombre);
        return $this->db->single() !== false;
    }

    /**
     * Obtener el total de cargos registrados.
     * @return int Total de cargos.
     */
    public function obtenerTotal()
    {
        $this->db->query('SELECT COUNT(*) as total FROM cargos');
        $resultado = $this->db->single();
        return $resultado->total;
    }
}
