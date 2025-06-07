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
        $validacion = $this->validarDatos($datos);
        if ($validacion !== true) {
            return ['exito' => false, 'errores' => $validacion];
        }

        $this->db->query("
            INSERT INTO asistencias (id_empleado, entrada, salida, estado)
            VALUES (:id_empleado, :entrada, :salida, :estado)
        ");
        $this->db->bind(':id_empleado', $datos['id_empleado']);
        $this->db->bind(':entrada', $datos['entrada'] ?? null);
        $this->db->bind(':salida', $datos['salida'] ?? null);
        $this->db->bind(':estado', $datos['estado'] ?? null);

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
                estado = :estado
            WHERE id = :id
        ");
        $this->db->bind(':entrada', $datos['entrada'] ?? null);
        $this->db->bind(':salida', $datos['salida'] ?? null);
        $this->db->bind(':estado', $datos['estado'] ?? null);
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
                e.apellido AS apellido_empleado
            FROM asistencias a
            JOIN empleados e ON a.id_empleado = e.id
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

        if (empty($datos['id_empleado']) || !is_numeric($datos['id_empleado'])) {
            $errores['id_empleado'] = 'Debe seleccionar un empleado válido.';
        }

        if (!empty($datos['entrada']) && !empty($datos['salida'])) {
            $entradaTime = strtotime($datos['entrada']);
            $salidaTime  = strtotime($datos['salida']);
            if ($entradaTime && $salidaTime && $salidaTime <= $entradaTime) {
                $errores['salida'] = 'La hora de salida debe ser posterior a la de entrada.';
            }
        }

        $estadosValidos = ['puntual', 'ausente', 'tarde'];
        if (!empty($datos['estado']) && !in_array($datos['estado'], $estadosValidos)) {
            $errores['estado'] = 'El estado debe ser: ' . implode(', ', $estadosValidos) . '.';
        }

        return empty($errores) ? true : $errores;
    }

    public function obtenerTotal()
    {
        $this->db->query('SELECT COUNT(*) as total FROM asistencias');
        $resultado = $this->db->single();
        return $resultado->total;
    }

    public function contarDeHoy()
    {
        $this->db->query("
            SELECT COUNT(*) AS total
            FROM asistencias
            WHERE fecha = CURDATE()
        ");
        $resultado = $this->db->single();
        return $resultado->total ?? 0;
    }

    public function contarPorEstadoHoy()
    {
        $this->db->query("
            SELECT estado, COUNT(*) as cantidad
            FROM asistencias
            WHERE fecha = CURDATE()
            GROUP BY estado
        ");
        $resultados = $this->db->resultSet();

        $conteo = ['puntual' => 0, 'tarde' => 0, 'ausente' => 0];
        foreach ($resultados as $row) {
            $estado = strtolower($row->estado);
            if (isset($conteo[$estado])) {
                $conteo[$estado] = $row->cantidad;
            }
        }

        return $conteo;
    }

    public function ultimasEntradas($limite = 5)
    {
        $this->db->query("
            SELECT a.entrada, a.estado, e.nombre, e.apellido
            FROM asistencias a
            JOIN empleados e ON a.id_empleado = e.id
            WHERE a.fecha = CURDATE()
            ORDER BY a.entrada DESC
            LIMIT :limite
        ");
        $this->db->bind(':limite', $limite, PDO::PARAM_INT);
        return $this->db->resultSet();
    }
}
