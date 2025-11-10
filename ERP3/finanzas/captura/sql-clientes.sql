-- Script SQL para Módulo de Clientes
-- Base de datos: rfwsmqex_erp

-- Tabla: clientes
CREATE TABLE IF NOT EXISTS `clientes` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `udn_id` INT NOT NULL,
    `deuda_inicial` DECIMAL(10,2) DEFAULT 0.00,
    `active` TINYINT(1) DEFAULT 1,
    `date_create` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_clientes_active` (`active`),
    INDEX `idx_clientes_udn` (`udn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: movimientos_credito
CREATE TABLE IF NOT EXISTS `movimientos_credito` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `cliente_id` INT NOT NULL,
    `udn_id` INT NOT NULL,
    `tipo_movimiento` ENUM('consumo', 'abono_parcial', 'pago_total') NOT NULL,
    `metodo_pago` ENUM('N/A', 'efectivo', 'banco') NOT NULL,
    `cantidad` DECIMAL(10,2) NOT NULL,
    `descripcion` TEXT,
    `deuda_anterior` DECIMAL(10,2) NOT NULL,
    `deuda_nueva` DECIMAL(10,2) NOT NULL,
    `fecha_captura` DATE NOT NULL,
    `hora_captura` TIME NOT NULL,
    `usuario_id` INT NOT NULL,
    `active` TINYINT(1) DEFAULT 1,
    `date_create` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `date_update` DATETIME ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_movimientos_fecha_udn` (`fecha_captura`, `udn_id`, `active`),
    INDEX `idx_movimientos_cliente` (`cliente_id`, `active`),
    INDEX `idx_movimientos_tipo` (`tipo_movimiento`),
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: audit_log_clientes
CREATE TABLE IF NOT EXISTS `audit_log_clientes` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `movimiento_id` INT,
    `cliente_id` INT NOT NULL,
    `accion` ENUM('create', 'update', 'delete') NOT NULL,
    `usuario_id` INT NOT NULL,
    `fecha_accion` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `datos_anteriores` JSON,
    `datos_nuevos` JSON,
    INDEX `idx_audit_fecha` (`fecha_accion`),
    INDEX `idx_audit_cliente` (`cliente_id`),
    FOREIGN KEY (`movimiento_id`) REFERENCES `movimientos_credito`(`id`),
    FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de ejemplo para clientes
INSERT INTO `clientes` (`name`, `udn_id`, `deuda_inicial`, `active`) VALUES
('American Express', 1, 9500.00, 1),
('API', 1, 3500.00, 1),
('Asociación de plataneros', 1, 0.00, 1),
('Cliente no frecuente', 1, 0.00, 1),
('Eventos', 1, 0.00, 1),
('Marina Chiapas', 1, 1500.00, 1);

-- Verificar estructura
SHOW TABLES LIKE '%clientes%';
SHOW TABLES LIKE '%movimientos_credito%';
SHOW TABLES LIKE '%audit_log%';
