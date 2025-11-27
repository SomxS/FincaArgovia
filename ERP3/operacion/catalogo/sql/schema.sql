-- ============================================================================
-- Script de Base de Datos - Módulo Catálogo
-- Sistema: ERP3
-- Módulo: Catálogo de Almacén
-- Descripción: Gestión de Categorías, Áreas y Zonas del almacén
-- ============================================================================

-- Usar la base de datos del proyecto
USE rfwsmqex_gvsl_finanzas2;

-- ============================================================================
-- Tabla: mtto_categoria (Verificación de existencia)
-- Descripción: Almacena las categorías de materiales e insumos
-- ============================================================================
CREATE TABLE IF NOT EXISTS mtto_categoria (
    idcategoria INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identificador único de la categoría',
    nombreCategoria VARCHAR(255) NOT NULL COMMENT 'Nombre descriptivo de la categoría',
    INDEX idx_nombre_categoria (nombreCategoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Categorías de materiales del almacén';

-- ============================================================================
-- Tabla: mtto_almacen_area (Verificación de existencia)
-- Descripción: Almacena las áreas físicas del almacén
-- ============================================================================
CREATE TABLE IF NOT EXISTS mtto_almacen_area (
    idArea INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identificador único del área',
    Nombre_Area VARCHAR(255) NOT NULL COMMENT 'Nombre descriptivo del área',
    INDEX idx_nombre_area (Nombre_Area)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Áreas físicas del almacén';

-- ============================================================================
-- Tabla: mtto_almacen_zona (Nueva tabla)
-- Descripción: Almacena las zonas dentro de las áreas del almacén
-- ============================================================================
CREATE TABLE IF NOT EXISTS mtto_almacen_zona (
    idZona INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identificador único de la zona',
    nombreZona VARCHAR(255) NOT NULL COMMENT 'Nombre descriptivo de la zona',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del registro',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',
    INDEX idx_nombre_zona (nombreZona)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Zonas internas del almacén';

-- ============================================================================
-- Verificación de índices adicionales para optimización
-- ============================================================================

-- Verificar y crear índice en mtto_categoria si no existe
SELECT COUNT(*) INTO @index_exists 
FROM information_schema.statistics 
WHERE table_schema = 'rfwsmqex_gvsl_finanzas2' 
  AND table_name = 'mtto_categoria' 
  AND index_name = 'idx_nombre_categoria';

SET @sql = IF(@index_exists = 0, 
    'ALTER TABLE mtto_categoria ADD INDEX idx_nombre_categoria (nombreCategoria)',
    'SELECT "Index idx_nombre_categoria already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar y crear índice en mtto_almacen_area si no existe
SELECT COUNT(*) INTO @index_exists 
FROM information_schema.statistics 
WHERE table_schema = 'rfwsmqex_gvsl_finanzas2' 
  AND table_name = 'mtto_almacen_area' 
  AND index_name = 'idx_nombre_area';

SET @sql = IF(@index_exists = 0, 
    'ALTER TABLE mtto_almacen_area ADD INDEX idx_nombre_area (Nombre_Area)',
    'SELECT "Index idx_nombre_area already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- Datos de prueba (Opcional - Comentar en producción)
-- ============================================================================

-- Insertar categorías de ejemplo si la tabla está vacía
INSERT INTO mtto_categoria (nombreCategoria)
SELECT * FROM (SELECT 'Herramientas') AS tmp
WHERE NOT EXISTS (
    SELECT nombreCategoria FROM mtto_categoria WHERE nombreCategoria = 'Herramientas'
) LIMIT 1;

INSERT INTO mtto_categoria (nombreCategoria)
SELECT * FROM (SELECT 'Materiales de Construcción') AS tmp
WHERE NOT EXISTS (
    SELECT nombreCategoria FROM mtto_categoria WHERE nombreCategoria = 'Materiales de Construcción'
) LIMIT 1;

INSERT INTO mtto_categoria (nombreCategoria)
SELECT * FROM (SELECT 'Equipos de Seguridad') AS tmp
WHERE NOT EXISTS (
    SELECT nombreCategoria FROM mtto_categoria WHERE nombreCategoria = 'Equipos de Seguridad'
) LIMIT 1;

-- Insertar áreas de ejemplo si la tabla está vacía
INSERT INTO mtto_almacen_area (Nombre_Area)
SELECT * FROM (SELECT 'Almacén Principal') AS tmp
WHERE NOT EXISTS (
    SELECT Nombre_Area FROM mtto_almacen_area WHERE Nombre_Area = 'Almacén Principal'
) LIMIT 1;

INSERT INTO mtto_almacen_area (Nombre_Area)
SELECT * FROM (SELECT 'Almacén Secundario') AS tmp
WHERE NOT EXISTS (
    SELECT Nombre_Area FROM mtto_almacen_area WHERE Nombre_Area = 'Almacén Secundario'
) LIMIT 1;

-- Insertar zonas de ejemplo si la tabla está vacía
INSERT INTO mtto_almacen_zona (nombreZona)
SELECT * FROM (SELECT 'Zona A') AS tmp
WHERE NOT EXISTS (
    SELECT nombreZona FROM mtto_almacen_zona WHERE nombreZona = 'Zona A'
) LIMIT 1;

INSERT INTO mtto_almacen_zona (nombreZona)
SELECT * FROM (SELECT 'Zona B') AS tmp
WHERE NOT EXISTS (
    SELECT nombreZona FROM mtto_almacen_zona WHERE nombreZona = 'Zona B'
) LIMIT 1;

INSERT INTO mtto_almacen_zona (nombreZona)
SELECT * FROM (SELECT 'Zona C') AS tmp
WHERE NOT EXISTS (
    SELECT nombreZona FROM mtto_almacen_zona WHERE nombreZona = 'Zona C'
) LIMIT 1;

-- ============================================================================
-- Verificación final
-- ============================================================================

-- Mostrar información de las tablas creadas
SELECT 
    'mtto_categoria' AS tabla,
    COUNT(*) AS total_registros
FROM mtto_categoria
UNION ALL
SELECT 
    'mtto_almacen_area' AS tabla,
    COUNT(*) AS total_registros
FROM mtto_almacen_area
UNION ALL
SELECT 
    'mtto_almacen_zona' AS tabla,
    COUNT(*) AS total_registros
FROM mtto_almacen_zona;

-- ============================================================================
-- Fin del script
-- ============================================================================
