<?php
class Asistencia
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function registrar($datos)
    {
        // Validar datos
        $validacion = $this->validarDatos($datos);
        if ($validacion !== true) {
            return ['exito' => false, 'errores' => $validacion];
        }

        $this->db->query("
            INSERT INTO asistencias (id_empleado, entrada, salida, observaciones, estado, registrado_por)
            VALUES (:id_empleado, :entrada, :salida, :observaciones, :estado, :registrado_por)
        ");
        $this->db->bind(':id_empleado', $datos['id_empleado']);
        $this->db->bind(':entrada', $datos['entrada'] ?? null);
        $this->db->bind(':salida', $datos['salida'] ?? null);
        $this->db->bind(':observaciones', $datos['observaciones'] ?? null);
        $this->db->bind(':estado', $datos['estado'] ?? null);
        $this->db->bind(':registrado_por', $datos['registrado_por'] ?? null);

        $exito = $this->db->execute();

        return [
            'exito' => $exito,
            'id' => $exito ? $this->db->lastInsertId() : null
        ];
    }

    public function actualizar($id, $datos)
    {
        $this->db->query("
            UPDATE asistencias SET
                entrada = :entrada,
                salida = :salida,
                observaciones = :observaciones,
                estado = :estado,
                registrado_por = :registrado_por
            WHERE id = :id
        ");
        $this->db->bind(':entrada', $datos['entrada'] ?? null);
        $this->db->bind(':salida', $datos['salida'] ?? null);
        $this->db->bind(':observaciones', $datos['observaciones'] ?? null);
        $this->db->bind(':estado', $datos['estado'] ?? null);
        $this->db->bind(':registrado_por', $datos['registrado_por'] ?? null);
        $this->db->bind(':id', $id);

        return ['exito' => $this->db->execute()];
    }

    public function eliminar($id)
    {
        $this->db->query("DELETE FROM asistencias WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function obtenerTodos()
    {
        $this->db->query("
            SELECT 
                a.*, 
                e.nombre AS nombre_empleado, 
                e.apellido AS apellido_empleado,
                u.name AS registrado_por_nombre
            FROM asistencias a
            JOIN empleados e ON a.id_empleado = e.id
            LEFT JOIN usuarios u ON a.registrado_por = u.id
            ORDER BY a.fecha DESC, a.entrada DESC
        ");
        return $this->db->resultSet();
    }

    public function obtenerPorId($id)
    {
        $this->db->query("SELECT * FROM asistencias WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function obtenerPorEmpleadoYFecha($idEmpleado, $fecha)
    {
        $this->db->query("
            SELECT * FROM asistencias
            WHERE id_empleado = :id_empleado AND fecha = :fecha
        ");
        $this->db->bind(':id_empleado', $idEmpleado);
        $this->db->bind(':fecha', $fecha);
        return $this->db->single();
    }

    public function obtenerPorRangoFecha($desde, $hasta)
    {
        $this->db->query("
            SELECT 
                a.*, 
                e.nombre AS nombre_empleado, 
                e.apellido AS apellido_empleado
            FROM asistencias a
            JOIN empleados e ON a.id_empleado = e.id
            WHERE a.fecha BETWEEN :desde AND :hasta
            ORDER BY a.fecha DESC
        ");
        $this->db->bind(':desde', $desde);
        $this->db->bind(':hasta', $hasta);
        return $this->db->resultSet();
    }

    public function validarDatos($datos)
    {
        $errores = [];

        // Validar ID de empleado
        if (empty($datos['id_empleado']) || !is_numeric($datos['id_empleado'])) {
            $errores['id_empleado'] = 'Debe seleccionar un empleado válido.';
        }

        // Validar que salida sea posterior a entrada
        if (!empty($datos['entrada']) && !empty($datos['salida'])) {
            $entradaTime = strtotime($datos['entrada']);
            $salidaTime  = strtotime($datos['salida']);
            if ($entradaTime && $salidaTime && $salidaTime <= $entradaTime) {
                $errores['salida'] = 'La hora de salida debe ser posterior a la de entrada.';
            }
        }

        // Validar estado (si se proporciona)
        $estadosValidos = ['puntual', 'ausente', 'tarde'];
        if (!empty($datos['estado']) && !in_array($datos['estado'], $estadosValidos)) {
            $errores['estado'] = 'El estado debe ser: ' . implode(', ', $estadosValidos) . '.';
        }

        return empty($errores) ? true : $errores;
    }
}
