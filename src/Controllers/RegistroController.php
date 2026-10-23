<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Dto\RegistroDto;
use App\Models\RegistroModel;

final class RegistroController
{
    public const GENEROS   = ['masculino' => 'Masculino', 'femenino' => 'Femenino', 'otro' => 'Otro'];
    public const PAISES    = ['CO' => 'Colombia', 'MX' => 'México', 'AR' => 'Argentina', 'PE' => 'Perú', 'CL' => 'Chile'];
    public const INTERESES = ['musica' => 'Música', 'tecnologia' => 'Tecnología', 'deporte' => 'Deporte', 'arte' => 'Arte'];

    private const UPLOAD_DIR   = __DIR__ . '/../../public/uploads/';
    private const MAX_FILE     = 2 * 1024 * 1024; // 2 MB
    private const ALLOWED_MIME = [
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
        'application/pdf' => 'pdf',
    ];

    public function __construct(private readonly RegistroModel $model)
    {
    }

    // ---------- READ ----------
    public function index(): void
    {
        $this->render('registros/index', [
            'registros' => $this->model->findAll(),
            'flash'     => $this->pullFlash(),
        ]);
    }

    // ---------- CREATE ----------
    public function create(): void
    {
        $this->renderForm(new RegistroDto(), [], false);
    }

    public function store(): void
    {
        $this->verifyCsrf();

        [$archivo, $uploadError] = $this->handleUpload();
        $dto    = RegistroDto::fromRequest($_POST, $archivo);
        $errors = $this->validate($dto, true);
        if ($uploadError) {
            $errors['archivo'] = $uploadError;
        }

        if ($errors) {
            $this->discardUpload($archivo);
            $dto->archivo = null;
            $this->renderForm($dto, $errors, false);
            return;
        }

        $this->model->create($dto);
        $this->redirect('Registro creado correctamente.');
    }

    // ---------- UPDATE ----------
    public function edit(): void
    {
        $dto = $this->model->findById((int) ($_GET['id'] ?? 0));
        if (!$dto) {
            $this->redirect('Registro no encontrado.');
        }
        $this->renderForm($dto, [], true);
    }

    public function update(): void
    {
        $this->verifyCsrf();

        $actual = $this->model->findById((int) ($_POST['id'] ?? 0));
        if (!$actual) {
            $this->redirect('Registro no encontrado.');
        }

        [$archivo, $uploadError] = $this->handleUpload();
        $dto    = RegistroDto::fromRequest($_POST, $archivo);
        $errors = $this->validate($dto, false);
        if ($uploadError) {
            $errors['archivo'] = $uploadError;
        }

        if ($errors) {
            $this->discardUpload($archivo);
            $dto->archivo = $actual->archivo;
            $this->renderForm($dto, $errors, true);
            return;
        }

        $this->model->update($dto);
        if ($archivo !== null) {
            $this->discardUpload($actual->archivo); // borra el archivo anterior
        }
        $this->redirect('Registro actualizado correctamente.');
    }

    // ---------- DELETE ----------
    public function destroy(): void
    {
        $this->verifyCsrf();

        $id  = (int) ($_POST['id'] ?? 0);
        $dto = $this->model->findById($id);
        if ($dto && $this->model->delete($id)) {
            $this->discardUpload($dto->archivo);
            $this->redirect('Registro eliminado.');
        }
        $this->redirect('No se pudo eliminar el registro.');
    }

    // ================= Helpers =================

    /** @return array<string,string> errores por campo */
    private function validate(RegistroDto $d, bool $isCreate): array
    {
        $e = [];

        if ($d->nombre === '' || mb_strlen($d->nombre) > 100) {
            $e['nombre'] = 'El nombre es obligatorio (máx. 100 caracteres).';
        }
        if ($isCreate && ($d->contrasena === null || strlen($d->contrasena) < 8)) {
            $e['contrasena'] = 'La contraseña debe tener mínimo 8 caracteres.';
        } elseif ($d->contrasena !== null && strlen($d->contrasena) < 8) {
            $e['contrasena'] = 'La nueva contraseña debe tener mínimo 8 caracteres.';
        }
        if (!filter_var($d->correo, FILTER_VALIDATE_EMAIL)) {
            $e['correo'] = 'Correo no válido.';
        }
        if ($d->sitioWeb !== '' && !filter_var($d->sitioWeb, FILTER_VALIDATE_URL)) {
            $e['sitio_web'] = 'URL no válida.';
        }
        if ($d->telefono !== '' && !preg_match('/^[0-9+\-\s()]{7,30}$/', $d->telefono)) {
            $e['telefono'] = 'Teléfono no válido.';
        }
        if ($d->edad < 0 || $d->edad > 120) {
            $e['edad'] = 'Edad entre 0 y 120.';
        }
        if ($d->nivel < 0 || $d->nivel > 10) {
            $e['nivel'] = 'Nivel entre 0 y 10.';
        }
        if ($d->fechaNacimiento !== '' && !$this->validDate($d->fechaNacimiento, 'Y-m-d')) {
            $e['fecha_nacimiento'] = 'Fecha no válida.';
        }
        if ($d->horaPreferida !== '' && !preg_match('/^([01]\d|2[0-3]):[0-5]\d(:[0-5]\d)?$/', $d->horaPreferida)) {
            $e['hora_preferida'] = 'Hora no válida.';
        }
        if ($d->fechaHora !== '' && !$this->validDate($d->fechaHora, 'Y-m-d H:i:s')) {
            $e['fecha_hora'] = 'Fecha y hora no válidas.';
        }
        if ($d->mes !== '' && !preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $d->mes)) {
            $e['mes'] = 'Mes no válido.';
        }
        if ($d->semana !== '' && !preg_match('/^\d{4}-W(0[1-9]|[1-4]\d|5[0-3])$/', $d->semana)) {
            $e['semana'] = 'Semana no válida.';
        }
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $d->color)) {
            $e['color'] = 'Color no válido.';
        }
        if (!$d->aceptaTerminos) {
            $e['acepta_terminos'] = 'Debes aceptar los términos.';
        }
        if ($d->genero !== '' && !isset(self::GENEROS[$d->genero])) {
            $e['genero'] = 'Género no válido.';
        }
        if ($d->pais !== '' && !isset(self::PAISES[$d->pais])) {
            $e['pais'] = 'País no válido.';
        }
        foreach ($d->intereses as $i) {
            if (!isset(self::INTERESES[$i])) {
                $e['intereses'] = 'Interés no válido.';
                break;
            }
        }

        return $e;
    }

    private function validDate(string $value, string $format): bool
    {
        $dt = \DateTime::createFromFormat($format, $value);
        return $dt !== false && $dt->format($format) === $value;
    }

    /** @return array{0:?string,1:?string} [ruta relativa, error] */
    private function handleUpload(): array
    {
        $f = $_FILES['archivo'] ?? null;
        if (!$f || $f['error'] === UPLOAD_ERR_NO_FILE) {
            return [null, null];
        }
        if ($f['error'] !== UPLOAD_ERR_OK) {
            return [null, 'Error al subir el archivo.'];
        }
        if ($f['size'] > self::MAX_FILE) {
            return [null, 'El archivo supera 2 MB.'];
        }

        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
        if (!isset(self::ALLOWED_MIME[$mime])) {
            return [null, 'Solo se permiten JPG, PNG o PDF.'];
        }

        $name = bin2hex(random_bytes(16)) . '.' . self::ALLOWED_MIME[$mime];
        if (!move_uploaded_file($f['tmp_name'], self::UPLOAD_DIR . $name)) {
            return [null, 'No se pudo guardar el archivo.'];
        }
        return ['uploads/' . $name, null];
    }

    private function discardUpload(?string $relativePath): void
    {
        if ($relativePath && str_starts_with($relativePath, 'uploads/')) {
            $full = self::UPLOAD_DIR . basename($relativePath);
            if (is_file($full)) {
                unlink($full);
            }
        }
    }

    // ---------- CSRF / flash / render ----------
    private function verifyCsrf(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(419);
            exit('Token CSRF inválido.');
        }
    }

    private function redirect(string $message): never
    {
        $_SESSION['flash'] = $message;
        header('Location: index.php');
        exit;
    }

    private function pullFlash(): ?string
    {
        $m = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $m;
    }

    private function renderForm(RegistroDto $dto, array $errors, bool $isEdit): void
    {
        $this->render('registros/form', [
            'dto'       => $dto,
            'errors'    => $errors,
            'isEdit'    => $isEdit,
            'generos'   => self::GENEROS,
            'paises'    => self::PAISES,
            'intereses' => self::INTERESES,
        ]);
    }

    private function render(string $view, array $data = []): void
    {
        $data['csrf'] = $_SESSION['csrf_token'];
        extract($data, EXTR_SKIP);

        ob_start();
        require __DIR__ . '/../../views/' . $view . '.php';
        $content = ob_get_clean();

        require __DIR__ . '/../../views/layout.php';
    }
}
