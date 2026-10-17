<!-- ==========================================================================
     VISTA: views/formulario.php
     OBJETIVO: Formulario reutilizable tanto para Crear (INSERT) como Editar (UPDATE).
     ========================================================================== -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($estudiante) ? "Editar Estudiante" : "Nuevo Estudiante" ?></title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>

<div class="contenedor-formulario">

  <div class="navegacion-retorno">
    <a href="index.php">&larr; Volver al Listado</a>
  </div>

  <h2><?= isset($estudiante) ? "Editar Datos del Estudiante" : "Registrar Nuevo Estudiante" ?></h2>
  <p class="subtitulo-form">
    <?= isset($estudiante) 
        ? "Modifica la información y presiona actualizar." 
        : "Diligencia los datos obligatorios (*) para almacenar en la base de datos." ?>
  </p>

  <!-- MENSAJES DE ERROR DEL CONTROLADOR -->
  <?php if (!empty($error)): ?>
    <div class="alerta alerta-advertencia"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <!-- FORMULARIO: Enruta a la acción correspondiente según el modo -->
  <form action="index.php?accion=<?= isset($estudiante) ? 'editar' : 'crear' ?>" method="POST" class="formulario-estilo">
    
    <!-- Si estamos editando, enviamos el ID oculto -->
    <?php if (isset($estudiante)): ?>
      <input type="hidden" name="id" value="<?= $estudiante['id'] ?>">
    <?php endif; ?>

    <div class="campo">
      <label for="nombre">Nombre Completo: *</label>
      <input type="text" id="nombre" name="nombre" 
             value="<?= htmlspecialchars($estudiante['nombre'] ?? '') ?>" required>
    </div>

    <div class="campo">
      <label for="type_doc">Tipo de documento:</label>
      <select id="type_doc" name="tipo_documento">
        <option value="RG" <?= isset($estudiante['tipo_documento']) && $estudiante['tipo_documento'] === 'RG' ? 'selected' : '' ?>>Registro civil</option>
        <option value="TI" <?= isset($estudiante['tipo_documento']) && $estudiante['tipo_documento'] === 'TI' ? 'selected' : '' ?>>Tarjeta de identidad</option>
        <option value="CC" <?= isset($estudiante['tipo_documento']) && $estudiante['tipo_documento'] === 'CC' ? 'selected' : '' ?>>Cedula de ciudadania</option>
      </select>
    </div>
    
    <div class="campo">
      <label for="cedula">Cédula de Ciudadanía: *</label>
      <input type="text" id="cedula" name="cedula" 
             value="<?= htmlspecialchars($estudiante['cedula'] ?? '') ?>" required>
    </div>

    <div class="campo">
      <label for="correo">Correo Electrónico: *</label>
      <input type="email" id="correo" name="correo" 
             value="<?= htmlspecialchars($estudiante['correo'] ?? '') ?>" required>
    </div>

    <div class="campo">
      <label for="materia">Asignatura Matriculada: *</label>
      <input type="text" id="materia" name="materia" 
             value="<?= htmlspecialchars($estudiante['materia'] ?? '') ?>" required>
    </div>

    <div class="campo">
      <label for="telefono">Teléfono / WhatsApp:</label>
      <input type="text" id="telefono" name="telefono" 
             value="<?= htmlspecialchars($estudiante['telefono'] ?? '') ?>">
    </div>

    <div class="botones-form">
      <button type="submit" class="btn btn-guardar">
        <?= isset($estudiante) ? "Actualizar Registro" : "Guardar Estudiante" ?>
      </button>
      <a href="index.php" class="btn btn-secundario">Cancelar</a>
    </div>

  </form>

</div>

</body>
</html>