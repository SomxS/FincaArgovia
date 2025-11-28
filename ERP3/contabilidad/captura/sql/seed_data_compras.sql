-- Script de datos iniciales para el Módulo de Compras
-- Base de datos: rfwsmqex_finanzas

USE rfwsmqex_finanzas;

-- Insertar tipos de compra
INSERT INTO purchase_type (id, name, active) VALUES
(1, 'Fondo fijo', 1),
(2, 'Corporativo', 1),
(3, 'Crédito', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insertar métodos de pago
INSERT INTO method_pay (name, active) VALUES
('Tarjeta de crédito', 1),
('Transferencia', 1),
('Efectivo', 1),
('Cheque', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insertar módulo de compras
INSERT INTO module (id, name, description, active) VALUES
(1, 'Compras', 'Módulo de gestión de compras', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insertar categorías de productos de ejemplo (ajustar según necesidad)
INSERT INTO product_class (udn_id, name, description, active) VALUES
(1, 'Costo directo', 'Productos de costo directo', 1),
(1, 'Costo indirecto', 'Productos de costo indirecto', 1),
(1, 'Gastos operativos', 'Gastos operativos', 1),
(1, 'Gastos de administración', 'Gastos administrativos', 1),
(1, 'Gastos de mantenimiento', 'Gastos de mantenimiento', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insertar productos de ejemplo para Costo directo
INSERT INTO product (product_class_id, name, active) VALUES
(1, 'Alimentos', 1),
(1, 'Bebidas', 1),
(1, 'Utensilios de producción', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insertar productos de ejemplo para Costo indirecto
INSERT INTO product (product_class_id, name, active) VALUES
(2, 'Agua', 1),
(2, 'Luz', 1),
(2, 'Gas', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insertar productos de ejemplo para Gastos operativos
INSERT INTO product (product_class_id, name, active) VALUES
(3, 'Gasolina', 1),
(3, 'Publicidad', 1),
(3, 'Papelería', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insertar productos de ejemplo para Gastos de administración
INSERT INTO product (product_class_id, name, active) VALUES
(4, 'Servicio de internet', 1),
(4, 'Telefonía', 1),
(4, 'Software', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insertar productos de ejemplo para Gastos de mantenimiento
INSERT INTO product (product_class_id, name, active) VALUES
(5, 'Fumigación', 1),
(5, 'Limpieza', 1),
(5, 'Reparaciones', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Insertar proveedores de ejemplo
INSERT INTO supplier (udn_id, name, active) VALUES
(1, 'Proveedor A', 1),
(1, 'Proveedor B', 1),
(1, 'Proveedor C', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Mensaje de confirmación
SELECT 'Datos iniciales insertados correctamente' AS mensaje;
