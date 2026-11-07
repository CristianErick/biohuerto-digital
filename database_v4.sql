-- ============================================================
-- SEMILLITAS DEL FUTURO — Migración v4
-- Columnas de cuidados en tabla plantas (riego, luz)
-- Motor: MySQL 5.7+ / MariaDB 10.3+ / TiDB Serverless
-- ============================================================

-- Ejecutar una sola vez sobre la base de datos existente:
--   mysql -h HOST -P PORT -u USER -p NOMBRE_DE_LA_BD < database_v4.sql
-- ============================================================

ALTER TABLE plantas
    ADD COLUMN riego VARCHAR(100) DEFAULT NULL COMMENT 'Régimen de riego (ej: Frecuente, Moderado, Espaciado, Poco)' AFTER descripcion,
    ADD COLUMN luz VARCHAR(100) DEFAULT NULL COMMENT 'Luz requerida (ej: Sol Directo, Sol Parcial, Sombra)' AFTER riego;

-- Índices opcionales para filtrar plantas por cuidados (útil en el biohuerto)
CREATE INDEX idx_plantas_riego ON plantas (riego);
CREATE INDEX idx_plantas_luz   ON plantas (luz);
