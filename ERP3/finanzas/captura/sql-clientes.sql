-- Script SQL para Módulo de Clientes
-- Base de datos: rfwsmqex_erp

-- Tabla: clientes_credit_customers
CREATE TABLE IF NOT EXISTS `clientes_credit_customers` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `udn_id` INT NOT NULL,
    `initial_debt` DECIMAL(10,2) DEFAULT 0.00,
    `active` TINYINT(1) DEFAULT 1,
    `date_create` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_clientes_active` (`active`),
    INDEX `idx_clientes_udn` (`udn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: detail_credit_customer
CREATE TABLE IF NOT EXISTS `detail_credit_customer` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `customer_id` INT NOT NULL,
    `udn_id` INT NOT NULL,
    `movement_type` ENUM('consumo', 'abono_parcial', 'pago_total') NOT NULL,
    `payment_method` ENUM('N/A', 'efectivo', 'banco') NOT NULL,
    `quantity` DECIMAL(10,2) NOT NULL,
    `description` TEXT,
    `previous_debt` DECIMAL(10,2) NOT NULL,
    `new_debt` DECIMAL(10,2) NOT NULL,
    `capture_date` DATE NOT NULL,
    `capture_time` TIME NOT NULL,
    `user_id` INT NOT NULL,
    `active` TINYINT(1) DEFAULT 1,
    `date_create` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `date_update` DATETIME ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_movimientos_fecha_udn` (`capture_date`, `udn_id`, `active`),
    INDEX `idx_movimientos_cliente` (`customer_id`, `active`),
    INDEX `idx_movimientos_tipo` (`movement_type`),
    FOREIGN KEY (`customer_id`) REFERENCES `clientes_credit_customers`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: audit_log
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `movement_id` INT,
    `customer_id` INT NOT NULL,
    `action` ENUM('create', 'update', 'delete') NOT NULL,
    `user_id` INT NOT NULL,
    `action_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `previous_data` JSON,
    `new_data` JSON,
    INDEX `idx_audit_fecha` (`action_date`),
    INDEX `idx_audit_cliente` (`customer_id`),
    FOREIGN KEY (`movement_id`) REFERENCES `detail_credit_customer`(`id`),
    FOREIGN KEY (`customer_id`) REFERENCES `clientes_credit_customers`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Datos de ejemplo para clientes
INSERT INTO `clientes_credit_customers` (`name`, `udn_id`, `initial_debt`, `active`) VALUES
('American Express', 1, 9500.00, 1),
('API', 1, 3500.00, 1),
('Asociación de plataneros', 1, 0.00, 1),
('Cliente no frecuente', 1, 0.00, 1),
('Eventos', 1, 0.00, 1),
('Marina Chiapas', 1, 1500.00, 1);

-- Verificar estructura
SHOW TABLES LIKE '%clientes%';
SHOW TABLES LIKE '%credit%';
SHOW TABLES LIKE '%audit_log%';
