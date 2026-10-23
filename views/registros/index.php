<?php /** @var App\Dto\RegistroDto[] $registros */ ?>
<h1>Registros</h1>

<?php if (!empty($flash)): ?>
    <div class="flash"><?= e($flash) ?></div>
<?php endif; ?>

<p><a class="btn btn-primary" href="index.php?action=create">+ Nuevo registro</a></p>

<div class="card" style="overflow-x:auto">
    <table>
        <thead>
        <tr>
            <th>ID</th><th>Nombre</th><th>Correo</th><th>País</th><th>Edad</th>
            <th>Nivel</th><th>Color</th><th>Intereses</th><th>Archivo</th><th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$registros): ?>
            <tr><td colspan="10">Aún no hay registros.</td></tr>
        <?php endif; ?>

        <?php foreach ($registros as $r): ?>
            <tr>
                <td><?= e($r->id) ?></td>
                <td><?= e($r->nombre) ?></td>
                <td><?= e($r->correo) ?></td>
                <td><?= e(App\Controllers\RegistroController::PAISES[$r->pais] ?? '—') ?></td>
                <td><?= e($r->edad) ?></td>
                <td><?= e($r->nivel) ?>/10</td>
                <td><span class="swatch" style="background:<?= e($r->color) ?>"></span></td>
                <td><?= e(implode(', ', $r->intereses) ?: '—') ?></td>
                <td>
                    <?php if ($r->archivo): ?>
                        <a href="<?= e($r->archivo) ?>" target="_blank" rel="noopener">Ver</a>
                    <?php else: ?>—<?php endif; ?>
                </td>
                <td class="actions">
                    <a class="btn" href="index.php?action=edit&id=<?= e($r->id) ?>">Editar</a>
                    <form class="inline" method="post" action="index.php?action=destroy"
                          onsubmit="return confirm('¿Eliminar este registro?');">
                        <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
                        <input type="hidden" name="id" value="<?= e($r->id) ?>">
                        <button class="btn btn-danger" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
