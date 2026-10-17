<?php
// ============================================================================
// ARCHIVO: models/EstudianteModel.php
// OBJETIVO: Contener únicamente las operaciones SQL (CRUD) sobre la tabla 'estudiantes'.
// ============================================================================

require_once __DIR__ . "/../config/conexion.php";

class EstudianteModel {
    private $pdo;

    // Al instanciar el modelo, obtiene la conexión PDO activa
    public function __construct() {
        $this->pdo = Conexion::conectar();
    }

    // ------------------------------------------------------------------------
    // READ: Consultar todos los estudiantes o filtrar por término de búsqueda (LIKE)
    // ------------------------------------------------------------------------
    public function listar($busqueda = "") {
        if (!empty($busqueda)) {
            // Consulta preparada con filtro LIKE para nombre o cédula
            $sql = "SELECT * FROM estudiantes 
                    WHERE nombre LIKE :name OR cedula LIKE :dni 
                    ORDER BY id DESC";
            $stmt = $this->pdo->prepare($sql);
            // El comodín % busca cualquier coincidencia antes o después del texto
            $filter = "%{$busqueda}%";
            $stmt->execute([":name" => $filter, ":dni" => $filter]);
            return $stmt->fetchAll();
        } else {
            // Consulta directa cuando no hay filtro
            $sql = "SELECT * FROM estudiantes ORDER BY id DESC";
            $stmt = $this->pdo->query($sql);
            return $stmt->fetchAll();
        }
    }

    // ------------------------------------------------------------------------
    // READ: Obtener un solo estudiante por su clave primaria (ID)
    // ------------------------------------------------------------------------
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM estudiantes WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([":id" => $id]);
        // fetch() retorna solo un registro asociativo o false si no existe
        return $stmt->fetch();
    }

    // ------------------------------------------------------------------------
    // CREATE: Insertar un nuevo estudiante con sentencia preparada
    // ------------------------------------------------------------------------
    public function guardar($datos) {
        $sql = "INSERT INTO estudiantes (nombre, cedula, correo, tipo_documento, materia, telefono) 
                VALUES (:nombre, :cedula, :correo, :tipo_documento, :materia, :telefono)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ":nombre"   => $datos['nombre'],
            ":cedula"   => $datos['cedula'],
            ":tipo_documento"   => $datos['tipo_documento'],
            ":correo"   => $datos['correo'],
            ":materia"  => $datos['materia'],
            ":telefono" => $datos['telefono']
        ]);
    }

    // ------------------------------------------------------------------------
    // UPDATE: Modificar los datos de un estudiante existente
    // ------------------------------------------------------------------------
    public function actualizar($datos) {
        $sql = "UPDATE estudiantes 
                SET nombre = :nombre, tipo_documento = :tipo_documento, cedula = :cedula, correo = :correo, 
                    materia = :materia, telefono = :telefono 
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ":nombre"   => $datos['nombre'],
            ":tipo_documento"   => $datos['tipo_documento'],
            ":cedula"   => $datos['cedula'],
            ":correo"   => $datos['correo'],
            ":materia"  => $datos['materia'],
            ":telefono" => $datos['telefono'],
            ":id"       => $datos['id']
        ]);
    }

    // ------------------------------------------------------------------------
    // DELETE: Eliminar físicamente un registro por su ID
    // ------------------------------------------------------------------------
    public function eliminar($id) {
        $sql = "DELETE FROM estudiantes WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([":id" => $id]);
    }
}