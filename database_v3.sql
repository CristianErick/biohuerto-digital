-- ============================================================
-- SEMILLITAS DEL FUTURO — Migración v3
-- Archivos en MySQL, almacén clave/valor y sesiones compartidas
-- Motor: MySQL 5.7+ / MariaDB 10.3+ / TiDB Serverless
--
-- Ejecutar una sola vez sobre la base de datos existente:
--   mysql -h HOST -u USER -p semillitas_db < database_v3.sql
-- o importarlo desde phpMyAdmin / DBeaver / TablePlus.
-- ============================================================

-- 1. Archivos binarios (fotos de plantas, QR, materiales)
CREATE TABLE IF NOT EXISTS archivos (
  id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre      VARCHAR(255)    NOT NULL,
  mime        VARCHAR(150)    NOT NULL DEFAULT 'application/octet-stream',
  tamanio     INT UNSIGNED    NOT NULL DEFAULT 0,
  datos       LONGBLOB        NOT NULL,
  creado_en   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Almacén clave/valor (Biblioteca pedagógica, configuraciones)
CREATE TABLE IF NOT EXISTS app_data (
  clave          VARCHAR(100) NOT NULL,
  valor          LONGTEXT     NOT NULL,
  actualizado_en DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (clave)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Sesiones compartidas (necesario en Vercel / serverless)
CREATE TABLE IF NOT EXISTS sessions (
  id            VARCHAR(128) NOT NULL,
  data          MEDIUMTEXT   NOT NULL,
  last_activity INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY idx_sessions_last_activity (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
