CREATE DATABASE IF NOT EXISTS crud_demo
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE crud_demo;

CREATE TABLE IF NOT EXISTS registros (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(100)  NOT NULL,            -- text
    contrasena       VARCHAR(255)  NOT NULL,            -- password (hash)
    correo           VARCHAR(150)  NOT NULL,            -- email
    sitio_web        VARCHAR(255)  NULL,                -- url
    telefono         VARCHAR(30)   NULL,                -- tel
    busqueda         VARCHAR(150)  NULL,                -- search
    edad             TINYINT UNSIGNED NULL,             -- number
    nivel            TINYINT UNSIGNED NOT NULL DEFAULT 5, -- range
    fecha_nacimiento DATE          NULL,                -- date
    hora_preferida   TIME          NULL,                -- time
    fecha_hora       DATETIME      NULL,                -- datetime-local
    mes              VARCHAR(7)    NULL,                -- month   (2026-10)
    semana           VARCHAR(8)    NULL,                -- week    (2026-W43)
    color            CHAR(7)       NOT NULL DEFAULT '#000000', -- color
    acepta_terminos  TINYINT(1)    NOT NULL DEFAULT 0,  -- checkbox
    genero           VARCHAR(20)   NULL,                -- radio
    pais             VARCHAR(50)   NULL,                -- select
    intereses        JSON          NULL,                -- select multiple / checkboxes
    biografia        TEXT          NULL,                -- textarea
    archivo          VARCHAR(255)  NULL,                -- file
    created_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
