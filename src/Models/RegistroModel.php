<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\ConnectionPool;
use App\Dto\RegistroDto;
use PDO;

final class RegistroModel
{
    /** @return RegistroDto[] */
    public function findAll(): array
    {
        return $this->withConnection(function (PDO $pdo): array {
            $rows = $pdo->query('SELECT * FROM registros ORDER BY id DESC')->fetchAll();
            return array_map([RegistroDto::class, 'fromRow'], $rows);
        });
    }

    public function findById(int $id): ?RegistroDto
    {
        return $this->withConnection(function (PDO $pdo) use ($id): ?RegistroDto {
            $stmt = $pdo->prepare('SELECT * FROM registros WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch();
            return $row ? RegistroDto::fromRow($row) : null;
        });
    }

    public function create(RegistroDto $dto): int
    {
        return $this->withConnection(function (PDO $pdo) use ($dto): int {
            $sql = 'INSERT INTO registros
                    (nombre, contrasena, correo, sitio_web, telefono, busqueda, edad, nivel,
                     fecha_nacimiento, hora_preferida, fecha_hora, mes, semana, color,
                     acepta_terminos, genero, pais, intereses, biografia, archivo)
                    VALUES
                    (:nombre, :contrasena, :correo, :sitio_web, :telefono, :busqueda, :edad, :nivel,
                     :fecha_nacimiento, :hora_preferida, :fecha_hora, :mes, :semana, :color,
                     :acepta_terminos, :genero, :pais, :intereses, :biografia, :archivo)';

            $params = $this->baseParams($dto);
            $params[':contrasena'] = password_hash((string) $dto->contrasena, PASSWORD_DEFAULT);
            $params[':archivo']    = $dto->archivo;

            $pdo->prepare($sql)->execute($params);
            return (int) $pdo->lastInsertId();
        });
    }

    public function update(RegistroDto $dto): bool
    {
        return $this->withConnection(function (PDO $pdo) use ($dto): bool {
            $sets = [
                'nombre = :nombre', 'correo = :correo', 'sitio_web = :sitio_web',
                'telefono = :telefono', 'busqueda = :busqueda', 'edad = :edad', 'nivel = :nivel',
                'fecha_nacimiento = :fecha_nacimiento', 'hora_preferida = :hora_preferida',
                'fecha_hora = :fecha_hora', 'mes = :mes', 'semana = :semana', 'color = :color',
                'acepta_terminos = :acepta_terminos', 'genero = :genero', 'pais = :pais',
                'intereses = :intereses', 'biografia = :biografia',
            ];
            $params = $this->baseParams($dto);
            $params[':id'] = $dto->id;

            // Contraseña y archivo solo se cambian si el usuario envió algo nuevo
            if ($dto->contrasena !== null) {
                $sets[] = 'contrasena = :contrasena';
                $params[':contrasena'] = password_hash($dto->contrasena, PASSWORD_DEFAULT);
            }
            if ($dto->archivo !== null) {
                $sets[] = 'archivo = :archivo';
                $params[':archivo'] = $dto->archivo;
            }

            $sql = 'UPDATE registros SET ' . implode(', ', $sets) . ' WHERE id = :id';
            return $pdo->prepare($sql)->execute($params);
        });
    }

    public function delete(int $id): bool
    {
        return $this->withConnection(function (PDO $pdo) use ($id): bool {
            $stmt = $pdo->prepare('DELETE FROM registros WHERE id = :id');
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount() > 0;
        });
    }

    // ------------------------------------------------------------------

    /** Toma una conexión del pool, ejecuta el callback y SIEMPRE la devuelve. */
    private function withConnection(callable $fn): mixed
    {
        $pool = ConnectionPool::getInstance();
        $pdo  = $pool->acquire();
        try {
            return $fn($pdo);
        } finally {
            $pool->release($pdo);
        }
    }

    /** Parámetros comunes a INSERT y UPDATE ('' => NULL en campos opcionales). */
    private function baseParams(RegistroDto $d): array
    {
        $nullIfEmpty = static fn(string $v): ?string => $v === '' ? null : $v;

        return [
            ':nombre'           => $d->nombre,
            ':correo'           => $d->correo,
            ':sitio_web'        => $nullIfEmpty($d->sitioWeb),
            ':telefono'         => $nullIfEmpty($d->telefono),
            ':busqueda'         => $nullIfEmpty($d->busqueda),
            ':edad'             => $d->edad,
            ':nivel'            => $d->nivel,
            ':fecha_nacimiento' => $nullIfEmpty($d->fechaNacimiento),
            ':hora_preferida'   => $nullIfEmpty($d->horaPreferida),
            ':fecha_hora'       => $nullIfEmpty($d->fechaHora),
            ':mes'              => $nullIfEmpty($d->mes),
            ':semana'           => $nullIfEmpty($d->semana),
            ':color'            => $d->color,
            ':acepta_terminos'  => $d->aceptaTerminos ? 1 : 0,
            ':genero'           => $nullIfEmpty($d->genero),
            ':pais'             => $nullIfEmpty($d->pais),
            ':intereses'        => json_encode($d->intereses, JSON_UNESCAPED_UNICODE),
            ':biografia'        => $nullIfEmpty($d->biografia),
        ];
    }
}
