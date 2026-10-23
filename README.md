# CRUD PHP + PDO (MVC con DTO y pool de conexiones)

## Requisitos
- PHP 8.1+ con extensiones `pdo_mysql` y `fileinfo`
- MySQL 5.7+ / MariaDB 10.2+ (se usa el tipo JSON)

## Instalación
1. Importa la base de datos:
   ```bash
   mysql -u root -p < database.sql
   ```
2. Ajusta credenciales en `config/database.php`.
3. Levanta el servidor embebido:
   ```bash
   php -S localhost:8000 -t public
   ```
4. Abre http://localhost:8000

## Estructura
```
config/database.php                    credenciales y tamaño del pool
database.sql                           tabla `registros`
public/index.php                       router + autoload + sesión CSRF
public/uploads/                        archivos subidos (debe ser escribible)
src/Core/ConnectionPool.php            pool de conexiones PDO
src/Dto/RegistroDto.php                DTO (form <-> controller <-> modelo)
src/Models/RegistroModel.php           consultas con sentencias preparadas
src/Controllers/RegistroController.php validación, uploads, CRUD
views/layout.php                       layout base
views/registros/index.php              listado
views/registros/form.php               formulario con todos los input
```

## Notas
- PHP clásico no comparte memoria entre peticiones, así que el "pool" vive por proceso.
  Con `persistent => true` PDO reutiliza las conexiones TCP entre peticiones, que es
  lo más cercano a un pool real. Si necesitas un pool compartido de verdad, usa
  ProxySQL/PgBouncer o Swoole/RoadRunner.
- Contraseñas con `password_hash`, CSRF en todos los POST, consultas preparadas
  y validación de MIME real en las subidas.
