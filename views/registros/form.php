<?php
/**
 * @var App\Dto\RegistroDto $dto
 * @var array<string,string> $errors
 * @var bool $isEdit
 * @var array<string,string> $generos
 * @var array<string,string> $paises
 * @var array<string,string> $intereses
 */

$action = $isEdit ? 'update' : 'store';
$err = static fn(string $k): string => isset($errors[$k]) ? '<span class="error">' . e($errors[$k]) . '</span>' : '';

// datetime-local necesita "Y-m-d\TH:i"
$fechaHoraLocal = $dto->fechaHora !== '' ? substr(str_replace(' ', 'T', $dto->fechaHora), 0, 16) : '';
// time necesita "H:i"
$horaLocal = $dto->horaPreferida !== '' ? substr($dto->horaPreferida, 0, 5) : '';
?>
<h1><?= $isEdit ? 'Editar registro #' . e($dto->id) : 'Nuevo registro' ?></h1>

<form class="card" method="post" action="index.php?action=<?= $action ?>" enctype="multipart/form-data" novalidate>
    <!-- input type="hidden" -->
    <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= e($dto->id) ?>">
    <?php endif; ?>

    <fieldset>
        <legend>Texto</legend>
        <div class="grid">
            <label class="field">Nombre (text)
                <input type="text" name="nombre" value="<?= e($dto->nombre) ?>" maxlength="100" required>
                <?= $err('nombre') ?>
            </label>

            <label class="field">Contraseña (password)<?= $isEdit ? ' — vacío = no cambiar' : '' ?>
                <input type="password" name="contrasena" minlength="8" autocomplete="new-password" <?= $isEdit ? '' : 'required' ?>>
                <?= $err('contrasena') ?>
            </label>

            <label class="field">Correo (email)
                <input type="email" name="correo" value="<?= e($dto->correo) ?>" required>
                <?= $err('correo') ?>
            </label>

            <label class="field">Sitio web (url)
                <input type="url" name="sitio_web" value="<?= e($dto->sitioWeb) ?>" placeholder="https://...">
                <?= $err('sitio_web') ?>
            </label>

            <label class="field">Teléfono (tel)
                <input type="tel" name="telefono" value="<?= e($dto->telefono) ?>" placeholder="+57 300 000 0000">
                <?= $err('telefono') ?>
            </label>

            <label class="field">Búsqueda (search)
                <input type="search" name="busqueda" value="<?= e($dto->busqueda) ?>">
            </label>
        </div>
    </fieldset>

    <fieldset>
        <legend>Números</legend>
        <div class="grid">
            <label class="field">Edad (number)
                <input type="number" name="edad" value="<?= e($dto->edad) ?>" min="0" max="120" step="1">
                <?= $err('edad') ?>
            </label>

            <label class="field">Nivel (range): <output id="nivelOut"><?= e($dto->nivel) ?></output>/10
                <input type="range" name="nivel" min="0" max="10" step="1" value="<?= e($dto->nivel) ?>"
                       oninput="document.getElementById('nivelOut').value = this.value">
                <?= $err('nivel') ?>
            </label>
        </div>
    </fieldset>

    <fieldset>
        <legend>Fechas y horas</legend>
        <div class="grid">
            <label class="field">Fecha de nacimiento (date)
                <input type="date" name="fecha_nacimiento" value="<?= e($dto->fechaNacimiento) ?>">
                <?= $err('fecha_nacimiento') ?>
            </label>

            <label class="field">Hora preferida (time)
                <input type="time" name="hora_preferida" value="<?= e($horaLocal) ?>">
                <?= $err('hora_preferida') ?>
            </label>

            <label class="field">Fecha y hora (datetime-local)
                <input type="datetime-local" name="fecha_hora" value="<?= e($fechaHoraLocal) ?>">
                <?= $err('fecha_hora') ?>
            </label>

            <label class="field">Mes (month)
                <input type="month" name="mes" value="<?= e($dto->mes) ?>">
                <?= $err('mes') ?>
            </label>

            <label class="field">Semana (week)
                <input type="week" name="semana" value="<?= e($dto->semana) ?>">
                <?= $err('semana') ?>
            </label>
        </div>
    </fieldset>

    <fieldset>
        <legend>Selección</legend>
        <div class="grid">
            <label class="field">Color favorito (color)
                <input type="color" name="color" value="<?= e($dto->color) ?>">
                <?= $err('color') ?>
            </label>

            <label class="field">País (select)
                <select name="pais">
                    <option value="">— Selecciona —</option>
                    <?php foreach ($paises as $k => $label): ?>
                        <option value="<?= e($k) ?>" <?= $dto->pais === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= $err('pais') ?>
            </label>

            <label class="field">Intereses (select multiple)
                <select name="intereses[]" multiple size="4">
                    <?php foreach ($intereses as $k => $label): ?>
                        <option value="<?= e($k) ?>" <?= in_array($k, $dto->intereses, true) ? 'selected' : '' ?>><?= e($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <?= $err('intereses') ?>
            </label>
        </div>

        <p class="field inline-opts"><strong style="font-size:.85rem">Género (radio)</strong><br>
            <?php foreach ($generos as $k => $label): ?>
                <label>
                    <input type="radio" name="genero" value="<?= e($k) ?>" <?= $dto->genero === $k ? 'checked' : '' ?>>
                    <?= e($label) ?>
                </label>
            <?php endforeach; ?>
            <?= $err('genero') ?>
        </p>

        <p class="field">
            <label>
                <input type="checkbox" name="acepta_terminos" value="1" <?= $dto->aceptaTerminos ? 'checked' : '' ?>>
                Acepto los términos y condiciones (checkbox)
            </label>
            <?= $err('acepta_terminos') ?>
        </p>
    </fieldset>

    <fieldset>
        <legend>Texto largo y archivo</legend>
        <label class="field">Biografía (textarea)
            <textarea name="biografia" rows="4"><?= e($dto->biografia) ?></textarea>
        </label>
        <br>
        <label class="field">Archivo JPG/PNG/PDF, máx. 2 MB (file)
            <input type="file" name="archivo" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf">
            <?= $err('archivo') ?>
        </label>
        <?php if ($dto->archivo): ?>
            <small>Actual: <a href="<?= e($dto->archivo) ?>" target="_blank" rel="noopener"><?= e(basename($dto->archivo)) ?></a></small>
        <?php endif; ?>
    </fieldset>

    <fieldset>
        <legend>Acciones</legend>
        <div class="actions">
            <!-- input type="submit" -->
            <input class="btn btn-primary" type="submit" value="<?= $isEdit ? 'Actualizar' : 'Guardar' ?>">

            <!-- input type="reset" -->
            <input class="btn" type="reset" value="Restablecer">

            <!-- input type="button" -->
            <input class="btn" type="button" value="Volver al listado" onclick="location.href='index.php'">

            <!-- input type="image" (también envía el formulario) -->
            <input type="image" alt="Guardar (image)" width="36" height="36"
                   src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 36 36'><rect width='36' height='36' rx='8' fill='%234f46e5'/><path d='M10 19l6 6 11-13' stroke='white' stroke-width='3' fill='none'/></svg>">
        </div>
    </fieldset>
</form>
