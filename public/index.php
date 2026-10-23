<?php

declare(strict_types=1);

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Autoload PSR-4 simple: App\Foo\Bar => src/Foo/Bar.php
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

use App\Controllers\RegistroController;
use App\Models\RegistroModel;

/** Escapa HTML para imprimir seguro en las vistas. */
function e(mixed $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$controller = new RegistroController(new RegistroModel());
$action     = $_GET['action'] ?? 'index';
$isPost     = $_SERVER['REQUEST_METHOD'] === 'POST';

try {
    match (true) {
        $action === 'index'                => $controller->index(),
        $action === 'create'               => $controller->create(),
        $action === 'store'   && $isPost   => $controller->store(),
        $action === 'edit'                 => $controller->edit(),
        $action === 'update'  && $isPost   => $controller->update(),
        $action === 'destroy' && $isPost   => $controller->destroy(),
        default                            => (function () {
            http_response_code(404);
            echo 'Página no encontrada';
        })(),
    };
} catch (Throwable $ex) {
    http_response_code(500);
    // En producción: loguear $ex y mostrar un mensaje genérico.
    echo '<pre>Error: ' . e($ex->getMessage()) . '</pre>';
}
