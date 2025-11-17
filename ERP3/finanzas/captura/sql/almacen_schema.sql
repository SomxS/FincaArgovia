-- ============================================
-- Módulo de Almacén - Database Schema
-- Sistema: CoffeeSoft Finanzas
-- Fecha: 2025-11-16
-- ============================================

-- Tabla: warehouse_output (Salidas de almacén)
CREATE TABLE IF NOT EXISTS warehouse_output (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description TEXT,
    operation_date DATE NOT NULL,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (product_id) REFERENCES product(id),
    INDEX idx_warehouse_output_date (operation_date),
    INDEX idx_warehouse_output_product (product_id),
    INDEX idx_warehouse_output_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: product (Productos/Insumos)
CREATE TABLE IF NOT EXISTS product (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    product_class_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (product_class_id) REFERENCES product_class(id),
    INDEX idx_product_active (active),
    INDEX idx_product_class (product_class_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: product_class (Clasificación de productos)
CREATE TABLE IF NOT EXISTS product_class (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    active TINYINT DEFAULT 1,
    INDEX idx_product_class_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: file (Archivos de respaldo)
CREATE TABLE IF NOT EXISTS file (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    user_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    upload_date DATETIME NOT NULL,
    size_bytes TEXT,
    path TEXT,
    extension CHAR(5),
    operation_date DATE,
    INDEX idx_file_date (operation_date),
    INDEX idx_file_udn (udn_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: module (Módulos del sistema)
CREATE TABLE IF NOT EXISTS module (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    active TINYINT DEFAULT 1,
    description TEXT,
    INDEX idx_module_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: module_unlock (Desbloqueo de módulos)
CREATE TABLE IF NOT EXISTS module_unlock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    module_id INT NOT NULL,
    unlock_date DATETIME NOT NULL,
    lock_date DATETIME,
    lock_reason TEXT,
    operation_date DATE,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (module_id) REFERENCES module(id),
    INDEX idx_module_unlock_date (operation_date),
    INDEX idx_module_unlock_module (module_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: monthly_module_lock (Bloqueo mensual)
CREATE TABLE IF NOT EXISTS monthly_module_lock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    month VARCHAR(30) NOT NULL,
    lock_time TIME NOT NULL,
    FOREIGN KEY (module_id) REFERENCES module(id),
    INDEX idx_monthly_lock_module (module_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: audit_log (Bitácora de auditoría)
CREATE TABLE IF NOT EXISTS audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    user_id INT NOT NULL,
    record_id INT NOT NULL,
    name_table VARCHAR(255),
    name_user VARCHAR(50),
    name_udn VARCHAR(50),
    name_collaborator VARCHAR(255),
    action ENUM('insert', 'update', 'delete', 'view'),
    change_items LONGTEXT,
    creation_date DATETIME NOT NULL,
    INDEX idx_audit_log_date (creation_date),
    INDEX idx_audit_log_table (name_table),
    INDEX idx_audit_log_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Datos iniciales
-- ============================================

-- Insertar módulo de Almacén
INSERT INTO module (name, active, description) 
VALUES ('Almacén', 1, 'Módulo de gestión de salidas de almacén')
ON DUPLICATE KEY UPDATE name = name;

-- Insertar clasificaciones de productos básicas
INSERT INTO product_class (name, description, active) VALUES
('Alimentos', 'Insumos alimenticios', 1),
('Bebidas', 'Bebidas y líquidos', 1),
('Diversos', 'Productos diversos', 1)
ON DUPLICATE KEY UPDATE name = name;

-- ============================================
-- Verificación de integridad
-- ============================================

-- Verificar que las tablas se crearon correctamente
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    CREATE_TIME
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME IN (
    'warehouse_output',
    'product',
    'product_class',
    'file',
    'module',
    'module_unlock',
    'monthly_module_lock',
    'audit_log'
)
ORDER BY TABLE_NAME;
