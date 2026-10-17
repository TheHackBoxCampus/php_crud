<?php
// ============================================================================
// ARCHIVO: index.php (Front Controller)
// OBJETIVO: Punto de entrada único. Lee el parámetro ?accion= y delega al controlador.
// ============================================================================

require_once __DIR__ . "/controllers/EstudianteController.php";

// Instanciamos el controlador principal
$controller = new EstudianteController();

// Leemos la acción solicitada desde la URL (por defecto carga 'index')
$accion = $_GET['accion'] ?? 'index';

// Enrutamos según la acción solicitada
switch ($accion) {
    case 'crear':
        $controller->crear();
        break;
    case 'editar':
        $controller->editar();
        break;
    case 'eliminar':
        $controller->eliminar();
        break;
    case 'index':
    default:
        $controller->index();
        break;
}