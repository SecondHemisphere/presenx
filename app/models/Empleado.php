<?php
class Empleado
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function obtenerTodos()
    {
        $this->db->query("
            SELECT e.*, c.nombre AS nombre_cargo, em.nombre AS nombre_empresa
            FROM empleados e
            JOIN cargos c ON e.id_cargo = c.id
            JOIN empresas em ON e.id_empresa = em.id
            ORDER BY e.id DESC
        ");
        return $this->db->resultSet();
    }

    public function obtenerPorId($id)
    {
        $this->db->query("SELECT * FROM empleados WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function registrar($datos)
    {
        $validacion = $this->validarDatos($datos);
        if ($validacion !== true) {
            return ['exito' => false, 'errores' => $validacion];
        }

        // Verificar duplicado por cédula
        if ($this->existeCedula($datos['cedula'])) {
            return ['exito' => false, 'errores' => ['cedula' => 'La cédula ya está registrada.']];
        }

        $this->db->query("
            INSERT INTO empleados
            (nombre, apellido, cedula, genero, fecha_nacimiento, email, telefono, direccion, fecha_ingreso, id_cargo, id_empresa, estado, foto)
            VALUES
            (:nombre, :apellido, :cedula, :genero, :fecha_nacimiento, :email, :telefono, :direccion, :fecha_ingreso, :id_cargo, :id_empresa, :estado, :foto)
        ");
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':apellido', $datos['apellido']);
        $this->db->bind(':cedula', $datos['cedula']);
        $this->db->bind(':genero', $datos['genero']);
        $this->db->bind(':fecha_nacimiento', $datos['fecha_nacimiento']);
        $this->db->bind(':email', $datos['email']);
        $this->db->bind(':telefono', $datos['telefono']);
        $this->db->bind(':direccion', $datos['direccion']);
        $this->db->bind(':fecha_ingreso', $datos['fecha_ingreso']);
        $this->db->bind(':id_cargo', $datos['id_cargo']);
        $this->db->bind(':id_empresa', $datos['id_empresa']);
        $this->db->bind(':estado', $datos['estado'] ?? 'activo');

        $foto = $this->subirFoto($_FILES['foto'] ?? null);
        $this->db->bind(':foto', $foto);

        $exito = $this->db->execute();
        return [
            'exito' => $exito,
            'id' => $exito ? $this->db->lastInsertId() : null
        ];
    }

    public function actualizar($id, $datos)
    {
        $empleado = $this->obtenerPorId($id);
        if (!$empleado) {
            return ['exito' => false, 'errores' => ['general' => 'Empleado no encontrado']];
        }

        $validacion = $this->validarDatos($datos, true);
        if ($validacion !== true) {
            return ['exito' => false, 'errores' => $validacion];
        }

        // Verificar si la cédula ya existe en otro empleado
        if ($this->existeCedula($datos['cedula'], $id)) {
            return ['exito' => false, 'errores' => ['cedula' => 'Ya existe otro empleado con esa cédula.']];
        }

        $this->db->query("
            UPDATE empleados SET
                nombre = :nombre,
                apellido = :apellido,
                cedula = :cedula,
                genero = :genero,
                fecha_nacimiento = :fecha_nacimiento,
                email = :email,
                telefono = :telefono,
                direccion = :direccion,
                fecha_ingreso = :fecha_ingreso,
                id_cargo = :id_cargo,
                id_empresa = :id_empresa,
                estado = :estado,
                foto = :foto
            WHERE id = :id
        ");
        $this->db->bind(':nombre', $datos['nombre']);
        $this->db->bind(':apellido', $datos['apellido']);
        $this->db->bind(':cedula', $datos['cedula']);
        $this->db->bind(':genero', $datos['genero']);
        $this->db->bind(':fecha_nacimiento', $datos['fecha_nacimiento']);
        $this->db->bind(':email', $datos['email']);
        $this->db->bind(':telefono', $datos['telefono']);
        $this->db->bind(':direccion', $datos['direccion']);
        $this->db->bind(':fecha_ingreso', $datos['fecha_ingreso']);
        $this->db->bind(':id_cargo', $datos['id_cargo']);
        $this->db->bind(':id_empresa', $datos['id_empresa']);
        $this->db->bind(':estado', $datos['estado']);

        $nuevaFoto = $this->subirFoto($_FILES['foto'] ?? null);
        $fotoFinal = $nuevaFoto ?? $empleado->foto;

        if ($nuevaFoto && $empleado->foto && file_exists(__DIR__ . '/../../public/uploads/' . $empleado->foto)) {
            unlink(__DIR__ . '/../../public/uploads/' . $empleado->foto);
        }

        $this->db->bind(':foto', $fotoFinal);

        $this->db->bind(':id', $id);

        $exito = $this->db->execute();
        return ['exito' => $exito];
    }

    public function eliminar($id)
    {
        $this->db->query("DELETE FROM empleados WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function existeCedula($cedula, $excluirId = null)
    {
        $query = "SELECT id FROM empleados WHERE cedula = :cedula";
        if ($excluirId !== null) {
            $query .= " AND id != :id";
        }
        $this->db->query($query);
        $this->db->bind(':cedula', $cedula);
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

        if (empty($datos['apellido'])) {
            $errores['apellido'] = 'El apellido es obligatorio.';
        }

        if (empty($datos['cedula'])) {
            $errores['cedula'] = 'La cédula es obligatoria.';
        } elseif (!preg_match('/^\d{10}$/', $datos['cedula'])) {
            $errores['cedula'] = 'La cédula debe tener 10 dígitos.';
        }

        if (!in_array($datos['genero'], ['M', 'F', 'Otro'])) {
            $errores['genero'] = 'Género inválido.';
        }

        if (empty($datos['fecha_ingreso'])) {
            $errores['fecha_ingreso'] = 'La fecha de ingreso es obligatoria.';
        }

        if (empty($datos['id_cargo']) || !is_numeric($datos['id_cargo']) || !$this->existeCargo($datos['id_cargo'])) {
            $errores['id_cargo'] = 'Seleccione un cargo.';
        }

        if (empty($datos['id_empresa']) || !is_numeric($datos['id_empresa']) || !$this->existeEmpresa($datos['id_empresa'])) {
            $errores['id_empresa'] = 'Seleccione una empresa.';
        }

        return empty($errores) ? true : $errores;
    }

    public function existeCargo($id)
    {
        $this->db->query("SELECT id FROM cargos WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single() !== false;
    }

    public function existeEmpresa($id)
    {
        $this->db->query("SELECT id FROM empresas WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single() !== false;
    }


    public function obtenerTotal()
    {
        $this->db->query("SELECT COUNT(*) as total FROM empleados");
        $resultado = $this->db->single();
        return $resultado->total;
    }

    private function subirFoto($archivo)
    {
        if (isset($archivo) && $archivo['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = uniqid() . '_' . basename($archivo['name']);
            $rutaDestino = __DIR__ . '/../../public/uploads/' . $nombreArchivo;

            if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                return $nombreArchivo;
            }
        }

        return null;
    }
}
