<?php
// ============================================================================
// ARCHIVO: controllers/EstudianteController.php
// OBJETIVO: Recibir la acción del usuario, llamar al Modelo y cargar la Vista.
// ============================================================================

require_once __DIR__ . "/../models/EstudianteModel.php";

class EstudianteController {
    private $modelo;

    // Inicializamos el modelo para usarlo en todos los métodos del controlador
    public function __construct() {
        $this->modelo = new EstudianteModel();
    }

    // ------------------------------------------------------------------------
    // ACCIÓN: Listar y Buscar estudiantes
    // ------------------------------------------------------------------------
    public function index() {
        // Lee el término del buscador si viene en la URL por GET
        $busqueda = trim($_GET["buscar"] ?? "");
        
        // Pide los datos al modelo
        $estudiantes = $this->modelo->listar($busqueda);
        
        // Carga la vista de la tabla y le comparte las variables
        require_once __DIR__ . "/../views/listar.php";
    }

    // ------------------------------------------------------------------------
    // ACCIÓN: Crear nuevo estudiante (Formulario y Guardado)
    // ------------------------------------------------------------------------
    public function crear() {
        // Si el formulario se envió por POST, procesamos los datos
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $datos = [
                'nombre'   => trim($_POST['nombre'] ?? ''),
                'cedula'   => trim($_POST['cedula'] ?? ''),
                'correo'   => trim($_POST['correo'] ?? ''),
                'tipo_documento' => trim($_POST['tipo_documento'] ?? ''),
                'materia'  => trim($_POST['materia'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? '')
            ];

            // Validación básica en servidor
            if (empty($datos['nombre']) || empty($datos['cedula']) || empty($datos['correo']) || empty($datos['materia'])) {
                $error = "Todos los campos obligatorios (*) deben ser completados.";
                require_once __DIR__ . "/../views/formulario.php";
                return;
            }

            try {
                // Guarda en la BD
                $this->modelo->guardar($datos);
                // Redirige al listado con mensaje de confirmación
                header("Location: index.php?msg=guardado");
                exit();
            } catch (PDOException $e) {
                // Detecta cédula duplicada (código SQL 23000)
                if ($e->getCode() == 23000) {
                    $error = "La cédula ingresada ya está registrada en el sistema.";
                } else {
                    $error = "Error al registrar: " . $e->getMessage();
                }
                require_once __DIR__ . "/../views/formulario.php";
                return;
            }
        }

        // Si se solicitó por GET, simplemente muestra el formulario vacío
        $estudiante = null; // Indica que es modo creación
        require_once __DIR__ . "/../views/formulario.php";
    }

    // ------------------------------------------------------------------------
    // ACCIÓN: Editar un estudiante existente
    // ------------------------------------------------------------------------
    public function editar() {
        // Si viene por POST, actualizamos los datos en BD
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $datos = [
                'id'       => filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT),
                'nombre'   => trim($_POST['nombre'] ?? ''),
                'tipo_documento' => trim($_POST['tipo_documento'] ?? ''),
                'cedula'   => trim($_POST['cedula'] ?? ''),
                'correo'   => trim($_POST['correo'] ?? ''),
                'materia'  => trim($_POST['materia'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? '')
            ];

            if ($this->modelo->actualizar($datos)) {
                header("Location: index.php?msg=actualizado");
                exit();
            }
        }

        // Si viene por GET, buscamos el registro para cargar sus valores
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $estudiante = $this->modelo->obtenerPorId($id);

        if (!$estudiante) {
            header("Location: index.php?error=no_encontrado");
            exit();
        }

        // Carga el formulario con los datos prellenados
        require_once __DIR__ . "/../views/formulario.php";
    }

    // ------------------------------------------------------------------------
    // ACCIÓN: Eliminar registro
    // ------------------------------------------------------------------------
    public function eliminar() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $this->modelo->eliminar($id);
            header("Location: index.php?msg=eliminado");
            exit();
        }
        header("Location: index.php?error=id_invalido");
        exit();
    }
}