<?php
class Database
{
    // Propiedades para la conexión a la base de datos, definidas como constantes en un archivo de configuración
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $name = DB_NAME;

    // Manejador de base de datos (PDO), statement preparado y mensajes de error
    private $dbh;
    private $stmt;
    private $error;

    // Constructor: establece la conexión a la base de datos al crear la instancia
    public function __construct()
    {
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->name;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    // Prepara una consulta SQL
    public function query($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Enlaza los valores a los parámetros de la consulta de forma segura
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            // Detecta automáticamente el tipo del valor si no se especifica
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }

    // Ejecuta la consulta preparada
    public function execute()
    {
        return $this->stmt->execute();
    }

    // Ejecuta y devuelve todos los resultados como objetos
    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Ejecuta y devuelve un solo resultado como objeto
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Devuelve la cantidad de filas afectadas por la última operación
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    // Devuelve el último ID insertado en una tabla con columna autoincremental
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }
}
