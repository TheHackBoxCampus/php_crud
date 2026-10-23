<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * Data Transfer Object: viaja entre formulario, controller y modelo.
 */
final class RegistroDto
{
    public function __construct(
        public ?int $id = null,
        public string $nombre = '',
        public ?string $contrasena = null,      // texto plano solo al venir del form; nunca se lee de la BD
        public string $correo = '',
        public string $sitioWeb = '',
        public string $telefono = '',
        public string $busqueda = '',
        public int $edad = 18,
        public int $nivel = 5,
        public string $fechaNacimiento = '',
        public string $horaPreferida = '',
        public string $fechaHora = '',          // formato Y-m-d H:i:s
        public string $mes = '',
        public string $semana = '',
        public string $color = '#000000',
        public bool $aceptaTerminos = false,
        public string $genero = '',
        public string $pais = '',
        public array $intereses = [],
        public string $biografia = '',
        public ?string $archivo = null
    ) {
    }

    /** Construye el DTO desde $_POST. */
    public static function fromRequest(array $d, ?string $archivo = null): self
    {
        $fh = trim((string) ($d['fecha_hora'] ?? ''));
        if ($fh !== '') {
            $fh = str_replace('T', ' ', $fh);
            if (strlen($fh) === 16) {
                $fh .= ':00';
            }
        }

        $pass = (string) ($d['contrasena'] ?? '');

        return new self(
            id: isset($d['id']) && $d['id'] !== '' ? (int) $d['id'] : null,
            nombre: trim((string) ($d['nombre'] ?? '')),
            contrasena: $pass !== '' ? $pass : null,
            correo: trim((string) ($d['correo'] ?? '')),
            sitioWeb: trim((string) ($d['sitio_web'] ?? '')),
            telefono: trim((string) ($d['telefono'] ?? '')),
            busqueda: trim((string) ($d['busqueda'] ?? '')),
            edad: (int) ($d['edad'] ?? 0),
            nivel: (int) ($d['nivel'] ?? 5),
            fechaNacimiento: trim((string) ($d['fecha_nacimiento'] ?? '')),
            horaPreferida: trim((string) ($d['hora_preferida'] ?? '')),
            fechaHora: $fh,
            mes: trim((string) ($d['mes'] ?? '')),
            semana: trim((string) ($d['semana'] ?? '')),
            color: trim((string) ($d['color'] ?? '#000000')),
            aceptaTerminos: isset($d['acepta_terminos']),
            genero: trim((string) ($d['genero'] ?? '')),
            pais: trim((string) ($d['pais'] ?? '')),
            intereses: array_values(array_filter((array) ($d['intereses'] ?? []), 'is_string')),
            biografia: trim((string) ($d['biografia'] ?? '')),
            archivo: $archivo
        );
    }

    /** Construye el DTO desde una fila de la BD. */
    public static function fromRow(array $r): self
    {
        return new self(
            id: (int) $r['id'],
            nombre: (string) $r['nombre'],
            contrasena: null,
            correo: (string) $r['correo'],
            sitioWeb: (string) ($r['sitio_web'] ?? ''),
            telefono: (string) ($r['telefono'] ?? ''),
            busqueda: (string) ($r['busqueda'] ?? ''),
            edad: (int) ($r['edad'] ?? 0),
            nivel: (int) $r['nivel'],
            fechaNacimiento: (string) ($r['fecha_nacimiento'] ?? ''),
            horaPreferida: (string) ($r['hora_preferida'] ?? ''),
            fechaHora: (string) ($r['fecha_hora'] ?? ''),
            mes: (string) ($r['mes'] ?? ''),
            semana: (string) ($r['semana'] ?? ''),
            color: (string) $r['color'],
            aceptaTerminos: (bool) $r['acepta_terminos'],
            genero: (string) ($r['genero'] ?? ''),
            pais: (string) ($r['pais'] ?? ''),
            intereses: json_decode((string) ($r['intereses'] ?? '[]'), true) ?: [],
            biografia: (string) ($r['biografia'] ?? ''),
            archivo: $r['archivo'] ?? null
        );
    }
}
