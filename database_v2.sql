-- ============================================================
-- SEMILLITAS DEL FUTURO — Migración v2
-- Roles, Usuarios (reemplaza docentes) y Plantas
-- Motor: MySQL 5.7+ / MariaDB 10.3+
-- ============================================================

USE semillitas_db;

-- 1. Tabla: roles
CREATE TABLE IF NOT EXISTS roles (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(50)   NOT NULL UNIQUE,
  descripcion VARCHAR(255)  DEFAULT NULL,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO roles (id, nombre, descripcion) VALUES
  (1, 'Administrador', 'Acceso total: gestión de docentes, materiales y plantas.'),
  (2, 'Docente', 'Acceso al repositorio: subir, editar y eliminar sus propios materiales.');

-- 2. Tabla: usuarios (con índices integrados)
CREATE TABLE IF NOT EXISTS usuarios (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  rol_id        INT UNSIGNED  NOT NULL DEFAULT 2,
  nombre        VARCHAR(100)  NOT NULL,
  apellido      VARCHAR(100)  NOT NULL,
  email         VARCHAR(150)  NOT NULL UNIQUE,
  password      VARCHAR(255)  NOT NULL COMMENT 'Hash bcrypt via password_hash()',
  especialidad  VARCHAR(100)  DEFAULT NULL,
  avatar        VARCHAR(255)  DEFAULT NULL,
  activo        TINYINT(1)    NOT NULL DEFAULT 1,
  created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_usuarios_rol   (rol_id),
  INDEX idx_usuarios_email (email),
  CONSTRAINT fk_usuarios_rol
    FOREIGN KEY (rol_id) REFERENCES roles(id)
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Migrar datos de docentes si la tabla existe
SET @tabla_docentes = (SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'docentes');
SET @sql_migracion = IF(@tabla_docentes > 0,
  'INSERT IGNORE INTO usuarios (id, rol_id, nombre, apellido, email, password, especialidad, avatar, activo, created_at, updated_at)
   SELECT id, 2, nombre, apellido, email, password, especialidad, avatar, activo, created_at, updated_at FROM docentes',
  'SELECT 1'
);
PREPARE stmt FROM @sql_migracion;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. Usuario Admin de prueba
INSERT IGNORE INTO usuarios (rol_id, nombre, apellido, email, password, especialidad)
VALUES (
  1,
  'Admin',
  'Sistema',
  'admin@semillitas.edu',
  '$2y$12$b2XQ49W.fVSHpOhjDmO9lOMxTwhvGi.KHs1TSDd5a4ir4EM0uHLe2',
  'Administración General'
);

-- 5. Tabla: plantas
CREATE TABLE IF NOT EXISTS plantas (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre          VARCHAR(150)  NOT NULL,
  descripcion     TEXT          DEFAULT NULL,
  ruta_foto       VARCHAR(500)  DEFAULT NULL COMMENT 'Ruta de la imagen de la planta',
  ruta_qr         VARCHAR(500)  DEFAULT NULL COMMENT 'Ruta del código QR generado',
  fecha_registro  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
