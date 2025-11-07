-- Base de datos para el módulo de Compras
-- CoffeeSoft ERP System

-- Tabla: product_class (Categorías de productos)
CREATE TABLE IF NOT EXISTS `product_class` (
  `id` int NOT NULL AUTO_INCREMENT,
  `udn_id` int DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `description` text,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: product (Productos)
CREATE TABLE IF NOT EXISTS `product` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_class_id` int NOT NULL,
  `name` varchar(50) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `product_class_id` (`product_class_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`product_class_id`) REFERENCES `product_class` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: purchase_type (Tipos de compra)
CREATE TABLE IF NOT EXISTS `purchase_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar tipos de compra predefinidos
INSERT INTO `purchase_type` (`id`, `name`, `active`) VALUES
(1, 'Fondo fijo', 1),
(2, 'Corporativo', 1),
(3, 'Crédito', 1);

-- Tabla: method_pay (Métodos de pago)
CREATE TABLE IF NOT EXISTS `method_pay` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar métodos de pago predefinidos
INSERT INTO `method_pay` (`name`, `active`) VALUES
('Efectivo', 1),
('Tarjeta de débito', 1),
('Tarjeta de crédito', 1),
('Transferencia bancaria', 1),
('Cheque', 1);

-- Tabla: supplier (Proveedores)
CREATE TABLE IF NOT EXISTS `supplier` (
  `id` int NOT NULL AUTO_INCREMENT,
  `udn_id` int DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: purchase (Compras)
CREATE TABLE IF NOT EXISTS `purchase` (
  `id` int NOT NULL AUTO_INCREMENT,
  `udn_id` int NOT NULL,
  `product_class_id` int NOT NULL,
  `product_id` int NOT NULL,
  `supplier_id` int DEFAULT NULL,
  `purchase_type_id` int NOT NULL,
  `method_pay_id` int DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `tax` decimal(12,2) DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL,
  `description` text,
  `operation_date` date NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `udn_id` (`udn_id`),
  KEY `product_class_id` (`product_class_id`),
  KEY `product_id` (`product_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `purchase_type_id` (`purchase_type_id`),
  KEY `method_pay_id` (`method_pay_id`),
  CONSTRAINT `purchase_ibfk_1` FOREIGN KEY (`product_class_id`) REFERENCES `product_class` (`id`),
  CONSTRAINT `purchase_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`),
  CONSTRAINT `purchase_ibfk_3` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`id`),
  CONSTRAINT `purchase_ibfk_4` FOREIGN KEY (`purchase_type_id`) REFERENCES `purchase_type` (`id`),
  CONSTRAINT `purchase_ibfk_5` FOREIGN KEY (`method_pay_id`) REFERENCES `method_pay` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: module (Módulos del sistema)
CREATE TABLE IF NOT EXISTS `module` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: module_unlock (Control de bloqueo de módulos)
CREATE TABLE IF NOT EXISTS `module_unlock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `udn_id` int NOT NULL,
  `module_id` int NOT NULL,
  `unlock_date` datetime NOT NULL,
  `lock_date` datetime DEFAULT NULL,
  `lock_reason` text,
  `operation_date` date NOT NULL,
  `active` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `module_unlock_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `module` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: monthly_module_lock (Bloqueo mensual de módulos)
CREATE TABLE IF NOT EXISTS `monthly_module_lock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `month` varchar(50) NOT NULL,
  `lock_time` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: file (Archivos adjuntos)
CREATE TABLE IF NOT EXISTS `file` (
  `id` int NOT NULL AUTO_INCREMENT,
  `udn_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `upload_date` datetime NOT NULL,
  `size_bytes` text,
  `path` text NOT NULL,
  `extension` char(5) DEFAULT NULL,
  `operation_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla: audit_log (Registro de auditoría)
CREATE TABLE IF NOT EXISTS `audit_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `udn_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `record_id` int DEFAULT NULL,
  `name_table` varchar(255) DEFAULT NULL,
  `name_user` varchar(50) DEFAULT NULL,
  `name_udn` varchar(50) DEFAULT NULL,
  `name_collaborator` varchar(255) DEFAULT NULL,
  `action` enum('INSERT','UPDATE','DELETE','VIEW') NOT NULL,
  `change_items` longtext,
  `creation_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
