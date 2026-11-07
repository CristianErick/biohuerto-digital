-- ============================================================
-- SEMILLITAS DEL FUTURO - Esquema para la nube
-- Importar DESPUES de seleccionar/crear la base de datos en el proveedor.
-- No incluye CREATE DATABASE ni USE para ser compatible con Aiven, Railway,
-- TiDB Cloud, etc.
-- ============================================================

-- ============================================================
-- 2. Tabla: roles
-- ============================================================
CREATE TABLE IF NOT EXISTS roles (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(50)   NOT NULL UNIQUE,
  descripcion VARCHAR(255)  DEFAULT NULL,
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles predeterminados (INSERT IGNORE evita error 1062 si ya existen)
INSERT IGNORE INTO roles (id, nombre, descripcion) VALUES
  (1, 'Administrador', 'Acceso total: gestión de docentes, materiales y plantas.'),
  (2, 'Docente', 'Acceso al repositorio: subir, editar y eliminar sus propios materiales.');

-- ============================================================
-- 3. Tabla: usuarios (Administradores y Docentes)
-- ============================================================
CREATE TABLE IF NOT EXISTS usuarios (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  rol_id        INT UNSIGNED  NOT NULL DEFAULT 2,
  nombre        VARCHAR(100)  NOT NULL,
  apellido      VARCHAR(100)  NOT NULL,
  email         VARCHAR(150)  NOT NULL UNIQUE,
  password      VARCHAR(255)  NOT NULL COMMENT 'Hash generado con password_hash()',
  especialidad  VARCHAR(100)  DEFAULT NULL,
  avatar        VARCHAR(255)  DEFAULT NULL,
  activo        TINYINT(1)    NOT NULL DEFAULT 1,
  created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  INDEX idx_usuarios_rol (rol_id),
  INDEX idx_usuarios_email (email),
  CONSTRAINT fk_usuarios_rol
    FOREIGN KEY (rol_id) REFERENCES roles(id)
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuarios de prueba (Admin y Docente)
-- Contraseña para ambos: Semillitas2025
INSERT IGNORE INTO usuarios (id, rol_id, nombre, apellido, email, password, especialidad) VALUES
  (1, 1, 'Admin', 'Sistema', 'admin@semillitas.edu', '$2y$12$b2XQ49W.fVSHpOhjDmO9lOMxTwhvGi.KHs1TSDd5a4ir4EM0uHLe2', 'Administración General'),
  (2, 2, 'María', 'García', 'maria.garcia@semillitas.edu', '$2y$12$b2XQ49W.fVSHpOhjDmO9lOMxTwhvGi.KHs1TSDd5a4ir4EM0uHLe2', 'Ciencias Naturales');

-- ============================================================
-- 4. Tabla: materiales (Repositorio Docente)
--    Los índices se declaran DENTRO del CREATE TABLE para
--    evitar el error #1061 (Duplicate key name).
-- ============================================================
CREATE TABLE IF NOT EXISTS materiales (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id    INT UNSIGNED  NOT NULL,
  titulo        VARCHAR(200)  NOT NULL,
  descripcion   TEXT          DEFAULT NULL,
  area          VARCHAR(80)   NOT NULL COMMENT 'Ej: Ciencias, Matemáticas, Comunicación',
  grado         VARCHAR(20)   NOT NULL COMMENT 'Ej: 1°, 2°, 3°, 4°, 5°, 6°',
  nombre_archivo VARCHAR(255) NOT NULL COMMENT 'Nombre original del archivo',
  ruta_archivo  VARCHAR(500)  NOT NULL COMMENT 'Ruta en el servidor',
  tipo_archivo  VARCHAR(50)   NOT NULL COMMENT 'Extensión: pdf, docx, pptx, etc.',
  tamanio       INT UNSIGNED  NOT NULL DEFAULT 0 COMMENT 'Tamaño en bytes',
  descargas     INT UNSIGNED  NOT NULL DEFAULT 0 COMMENT 'Contador de descargas',
  activo        TINYINT(1)    NOT NULL DEFAULT 1,
  created_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  -- Índices declarados dentro de la tabla
  INDEX idx_materiales_area    (area),
  INDEX idx_materiales_grado   (grado),
  INDEX idx_materiales_usuario (usuario_id),

  -- Clave foránea hacia usuarios
  CONSTRAINT fk_materiales_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. Tabla: plantas (Biohuerto y QR)
-- ============================================================
CREATE TABLE IF NOT EXISTS plantas (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre          VARCHAR(150)  NOT NULL,
  descripcion     TEXT          DEFAULT NULL,
  ruta_foto       VARCHAR(500)  DEFAULT NULL COMMENT 'Ruta de la imagen de la planta',
  ruta_qr         VARCHAR(500)  DEFAULT NULL COMMENT 'Ruta del código QR generado',
  fecha_registro  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
