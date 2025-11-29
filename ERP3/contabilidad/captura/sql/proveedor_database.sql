-- Base de datos para el módulo de Proveedores
-- CoffeeSoft ERP System

-- Tabla: payment_type (Tipos de pago a proveedores)
CREATE TABLE IF NOT EXISTS `payment_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar tipos de pago predefinidos
INSERT INTO `payment_type` (`id`, `name`, `active`) VALUES
(1, 'Corporativo', 1),
(2, 'Fondo fijo', 1);

-- Tabla: payment_supplier (Pagos a proveedores)
CREATE TABLE IF NOT EXISTS `payment_supplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `payment_type_id` int NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `description` text,
  `payment_date` date NOT NULL,
  `udn_id` int NOT NULL,
  `user_id` int NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `payment_type_id` (`payment_type_id`),
  KEY `udn_id` (`udn_id`),
  KEY `payment_date` (`payment_date`),
  KEY `active` (`active`),
  CONSTRAINT `payment_supplier_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`),
  CONSTRAINT `payment_supplier_ibfk_2` FOREIGN KEY (`payment_type_id`) REFERENCES `payment_type` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Índices adicionales para optimizar consultas
CREATE INDEX idx_payment_date_udn ON payment_supplier(payment_date, udn_id);
CREATE INDEX idx_supplier_date ON payment_supplier(supplier_id, payment_date);
CREATE INDEX idx_udn_active ON payment_supplier(udn_id, active);

-- Nota: La tabla 'supplier' ya existe en el módulo de Compras
-- Se reutiliza para mantener consistencia en el sistema
