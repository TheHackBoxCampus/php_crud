<!-- ==========================================================================
     VISTA: views/listar.php
     OBJETIVO: Renderizar la tabla de estudiantes y el buscador en tiempo real.
     ========================================================================== -->
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Gestión de Estudiantes - MVC PHP</title>
  <link rel="stylesheet" href="public/css/estilos.css">
</head>
<body>

<div class="contenedor-principal">

  <header class="encabezado-vista">
    <div>
      <h1>Panel de Estudiantes Matriculados</h1>
      <p>Arquitectura MVC en PHP: Modelo (SQL) &bull; Controlador &bull; Vista (HTML)</p>
    </div>
    <!-- Botón que activa la acción 'crear' en el Front Controller -->
    <a href="index.php?accion=crear" class="btn btn-nuevo">+ Nuevo Registro</a>
  </header>

  <!-- MENSAJES DE RETROALIMENTACIÓN -->
  <?php if (isset($_GET['msg'])): ?>
    <?php if ($_GET['msg'] === 'guardado'): ?>
      <div class="alerta alerta-exito">Registro almacenado exitosamente en la base de datos.</div>
    <?php elseif ($_GET['msg'] === 'actualizado'): ?>
      <div class="alerta alerta-exito">Registro modificado correctamente.</div>
    <?php elseif ($_GET['msg'] === 'eliminado'): ?>
      <div class="alerta alerta-advertencia">El registro fue eliminado de MySQL.</div>
    <?php endif; ?>
  <?php endif; ?>

  <!-- BARRA DE BÚSQUEDA -->
  <div class="barra-acciones">
    <form action="index.php" method="GET" class="form-busqueda">
      <input type="hidden" name="accion" value="index">
      <input 
        type="text" 
        name="buscar" 
        placeholder="Buscar por nombre o cédula..." 
        value="<?= htmlspecialchars($busqueda ?? '') ?>"
      >
      <button type="submit" class="btn btn-buscar">Buscar</button>
      <?php if (!empty($busqueda)): ?>
        <a href="index.php" class="btn btn-secundario">Limpiar Filtro</a>
      <?php endif; ?>
    </form>
    <span class="total-registros">Total registros: <strong><?= count($estudiantes) ?></strong></span>
  </div>

  <!-- TABLA DE DATOS -->
  <table class="tabla-datos">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre Completo</th>
        <th>Tipo de documento</th>
        <th>Cédula (CC)</th>
        <th>Correo Institucional</th>
        <th>Asignatura</th>
        <th>Teléfono</th>
        <th style="text-align: center;">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($estudiantes)): ?>
        <?php foreach ($estudiantes as $est): ?>
          <tr>
            <td><strong>#<?= $est['id'] ?></strong></td>
            <td><?= htmlspecialchars($est['nombre']) ?></td>
            <td><?= htmlspecialchars($est['tipo_documento']) ?></td>
            <td><?= htmlspecialchars($est['cedula']) ?></td>
            <td><?= htmlspecialchars($est['correo']) ?></td>
            <td><?= htmlspecialchars($est['materia']) ?></td>
            <td><?= htmlspecialchars($est['telefono'] ?: 'N/A') ?></td>
            <td class="acciones-celda">
              <!-- Enlace a la acción 'editar' pasando el ID -->
              <a href="index.php?accion=editar&id=<?= $est['id'] ?>" class="btn-accion btn-editar">Editar</a>
              <!-- Enlace a la acción 'eliminar' con confirmación nativa JS -->
              <a href="index.php?accion=eliminar&id=<?= $est['id'] ?>" 
                 class="btn-accion btn-eliminar"
                 onclick="return confirm('¿Seguro que deseas eliminar a <?= htmlspecialchars($est['nombre']) ?>?');">
                 Eliminar
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" class="sin-datos">No hay registros que coincidan con la búsqueda.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <footer class="pie-vista">
    <p>Docente Titular: Ing. Nelber Andreiv Montaguth Useche &bull; Coordinación de Telecomunicaciones (UTS)</p>
  </footer>

</div>

</body>
</html>