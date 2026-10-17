<?php
// ============================================================================
// ARCHIVO: config/conexion.php
// OBJETIVO: Crear y retornar una instancia única de PDO conectada a MySQL.
// ============================================================================

class Conexion {
    // Método estático para conectar sin necesidad de instanciar la clase varias veces
    public static function conectar() {
        // Parámetros de conexión al servidor MySQL local (XAMPP / Laragon)
        $host     = "localhost";
        $db_name  = "db_estudiantes";
        $usuario  = "root";
        $password = ""; // Ajustar si tienes contraseña en tu entorno
        $charset  = "utf8mb4";

        // DSN: Data Source Name (Cadena que define el driver, host, BD y codificación)
        $dsn = "mysql:host={$host};dbname={$db_name};charset={$charset}";

        // Opciones de configuración de seguridad y rendimiento de PDO
        $opciones = [
            // Activa el modo de excepciones para capturar cualquier error SQL en un try/catch
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Devuelve los resultados como arreglos asociativos ($fila['nombre'])
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Desactiva la emulación de sentencias preparadas para blindar contra inyecciones SQL
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            // Retorna el objeto PDO conectado
            return new PDO($dsn, $usuario, $password, $opciones);
        } catch (PDOException $e) {
            // Si falla la conexión, detiene la ejecución y muestra el mensaje
            die("Error crítico al conectar con la base de datos: " . $e->getMessage());
        }
    }
}