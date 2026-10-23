<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Pool de conexiones PDO (singleton).
 *
 * - Mantiene hasta `pool_size` conexiones abiertas y las reutiliza.
 * - Con `persistent => true` PDO además reutiliza las conexiones TCP
 *   entre peticiones (en PHP clásico es lo más cercano a un pool real,
 *   porque cada petición vive en un proceso/hilo aislado).
 */
final class ConnectionPool
{
    private static ?self $instance = null;

    /** @var PDO[] conexiones libres */
    private array $available = [];
    private int $inUse = 0;

    private function __construct(
        private readonly array $config,
        private readonly int $maxSize
    ) {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';
            self::$instance = new self($config, (int) $config['pool_size']);
        }
        return self::$instance;
    }

    /** Toma una conexión del pool (o crea una nueva si hay cupo). */
    public function acquire(): PDO
    {
        if ($this->available) {
            $pdo = array_pop($this->available);
        } elseif ($this->total() < $this->maxSize) {
            $pdo = $this->createConnection();
        } else {
            throw new RuntimeException('Pool de conexiones agotado.');
        }

        $this->inUse++;
        return $pdo;
    }

    /** Devuelve la conexión al pool. */
    public function release(PDO $pdo): void
    {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $this->inUse = max(0, $this->inUse - 1);
        $this->available[] = $pdo;
    }

    public function total(): int
    {
        return count($this->available) + $this->inUse;
    }

    private function createConnection(): PDO
    {
        $c = $this->config;
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $c['host'], $c['port'], $c['dbname'], $c['charset']
        );

        try {
            return new PDO($dsn, $c['user'], $c['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => (bool) $c['persistent'],
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('No se pudo conectar a la BD: ' . $e->getMessage(), 0, $e);
        }
    }
}
