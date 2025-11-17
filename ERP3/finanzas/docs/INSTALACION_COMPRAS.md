# 🚀 Instalación del Módulo de Compras

## 📋 Requisitos Previos

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)
- Framework CoffeeSoft instalado
- jQuery 3.x
- TailwindCSS 2.x

## 📁 Archivos del Módulo

```
finanzas/captura/
├── compras.php                    # Vista principal
├── js/compras.js                  # Frontend
├── ctrl/ctrl-compras.php          # Controlador
└── mdl/mdl-compras.php            # Modelo
```

## 🗄️ Paso 1: Crear Tablas en la Base de Datos

Ejecuta el siguiente script SQL en tu base de datos:

```sql
USE rfwsmqex_finanzas;

-- 1. Tabla: product_class (Categorías de productos)
CREATE TABLE IF NOT EXISTS product_class (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    description TEXT NULL,
    active TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_udn (udn_id),
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabla: product (Productos)
CREATE TABLE IF NOT EXISTS product (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_class_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    active TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_class_id) REFERENCES product_class(id),
    INDEX idx_class (product_class_id),
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla: purchase_type (Tipos de compra)
CREATE TABLE IF NOT EXISTS purchase_type (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    active TINYINT DEFAULT 1,
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabla: supplier (Proveedores)
CREATE TABLE IF NOT EXISTS supplier (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    active TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_udn (udn_id),
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabla: method_pay (Métodos de pago)
CREATE TABLE IF NOT EXISTS method_pay (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    active TINYINT DEFAULT 1,
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tabla: purchase (Compras)
CREATE TABLE IF NOT EXISTS purchase (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    product_class_id INT NOT NULL,
    product_id INT NOT NULL,
    purchase_type_id INT NOT NULL,
    supplier_id INT NULL,
    method_pay_id INT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    tax DECIMAL(12,2) NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    description TEXT NULL,
    operation_date DATE NOT NULL,
    active TINYINT DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_class_id) REFERENCES product_class(id),
    FOREIGN KEY (product_id) REFERENCES product(id),
    FOREIGN KEY (purchase_type_id) REFERENCES purchase_type(id),
    FOREIGN KEY (supplier_id) REFERENCES supplier(id),
    FOREIGN KEY (method_pay_id) REFERENCES method_pay(id),
    INDEX idx_operation_date (operation_date),
    INDEX idx_purchase_type (purchase_type_id),
    INDEX idx_udn (udn_id),
    INDEX idx_active (active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 📊 Paso 2: Insertar Datos de Prueba

```sql
-- Tipos de compra
INSERT INTO purchase_type (id, name, active) VALUES
(1, 'Fondo fijo', 1),
(2, 'Corporativo', 1),
(3, 'Crédito', 1);

-- Métodos de pago
INSERT INTO method_pay (id, name, active) VALUES
(1, 'Efectivo', 1),
(2, 'Tarjeta de crédito', 1),
(3, 'Transferencia', 1),
(4, 'Cheque', 1);

-- Categorías de productos (ejemplo para UDN 4)
INSERT INTO product_class (udn_id, name, description, active) VALUES
(4, 'Alimentos', 'Productos alimenticios', 1),
(4, 'Bebidas', 'Bebidas y refrescos', 1),
(4, 'Limpieza', 'Productos de limpieza', 1),
(4, 'Mantenimiento', 'Materiales de mantenimiento', 1),
(4, 'Oficina', 'Artículos de oficina', 1);

-- Productos de ejemplo
INSERT INTO product (product_class_id, name, active) VALUES
(1, 'Arroz', 1),
(1, 'Frijol', 1),
(1, 'Aceite', 1),
(2, 'Agua', 1),
(2, 'Refresco', 1),
(3, 'Cloro', 1),
(3, 'Jabón', 1),
(4, 'Pintura', 1),
(5, 'Papel', 1);

-- Proveedores de ejemplo
INSERT INTO supplier (udn_id, name, active) VALUES
(4, 'Proveedor A', 1),
(4, 'Proveedor B', 1),
(4, 'Proveedor C', 1);
```

## ⚙️ Paso 3: Configurar Permisos de Usuario

Asegúrate de que la tabla de usuarios tenga un campo `nivel_usuario` o similar:

```sql
-- Ejemplo de estructura de usuarios
ALTER TABLE usuarios ADD COLUMN nivel_usuario INT DEFAULT 1;

-- Niveles:
-- 1 = Captura
-- 2 = Gerencia
-- 3 = Dirección
-- 4 = Contabilidad
```

## 🔧 Paso 4: Verificar Configuración

### 1. Verificar rutas en los archivos

**compras.js:**
```javascript
let api = 'ctrl/ctrl-compras.php'; // ✅ Verificar ruta
```

**ctrl-compras.php:**
```php
require_once '../mdl/mdl-compras.php'; // ✅ Verificar ruta
```

**mdl-compras.php:**
```php
require_once '../../../conf/_CRUD3.php';      // ✅ Verificar ruta
require_once '../../../conf/_Utileria.php';   // ✅ Verificar ruta
require_once '../../../conf/coffeSoft.php';   // ✅ Verificar ruta
```

### 2. Verificar nombre de base de datos

En `mdl-compras.php`, línea 11:
```php
$this->bd = "rfwsmqex_finanzas."; // ✅ Cambiar si es necesario
```

### 3. Verificar inclusión de librerías

En `compras.php`:
```php
<?php include 'layout/head.php'; ?>           // ✅ Verificar
<?php include 'layout/core-libraries.php'; ?> // ✅ Verificar
```

## 🌐 Paso 5: Acceder al Módulo

1. Abre tu navegador
2. Navega a: `http://tu-dominio/finanzas/captura/compras.php`
3. Deberías ver el módulo de compras cargado

## ✅ Paso 6: Verificar Funcionamiento

### Prueba 1: Dashboard
- ✅ Verifica que se muestren las 4 tarjetas de totales
- ✅ Verifica que el calendario funcione
- ✅ Verifica que la tabla se cargue (puede estar vacía)

### Prueba 2: Registrar Compra
1. Haz clic en "Registrar nueva compra"
2. Selecciona una categoría
3. Verifica que se carguen los productos
4. Selecciona un tipo de compra
5. Verifica que los campos se muestren/oculten correctamente
6. Ingresa subtotal e impuesto
7. Verifica que el total se calcule automáticamente
8. Guarda la compra

### Prueba 3: Concentrado
1. Haz clic en la pestaña "Concentrado"
2. Selecciona un rango de fechas
3. Verifica que se genere la tabla
4. Verifica que se muestren los totales

## 🐛 Solución de Problemas

### Error: "No se puede conectar a la base de datos"
- Verifica las credenciales en `_CRUD3.php`
- Verifica que la base de datos exista
- Verifica que el usuario tenga permisos

### Error: "Cannot find module"
- Verifica las rutas de los archivos
- Verifica que todos los archivos existan
- Verifica los permisos de lectura

### Error: "Undefined function"
- Verifica que `coffeSoft.js` esté cargado
- Verifica que `plugins.js` esté cargado
- Verifica el orden de carga de scripts

### La tabla no se carga
- Abre la consola del navegador (F12)
- Verifica si hay errores JavaScript
- Verifica la respuesta del servidor en la pestaña Network

### Los productos no se cargan al seleccionar categoría
- Verifica que existan productos en la base de datos
- Verifica que el método `getProductsByClass()` funcione
- Verifica la consola del navegador

## 📝 Configuración Adicional

### Cambiar saldo inicial del fondo fijo

En `mdl-compras.php`, método `getTotalesConcentradoPeriodo()`:
```php
$data['saldo_inicial'] = 15000; // ✅ Cambiar aquí
```

### Cambiar UDN por defecto

En `compras.js`, método `addCompra()`:
```javascript
$_POST['udn_id'] = $_POST['udn'] ?? 4; // ✅ Cambiar aquí
```

### Personalizar permisos

En `compras.js`, constante `PERMISOS`:
```javascript
const PERMISOS = {
    1: { ... }, // ✅ Modificar según necesidades
    2: { ... },
    3: { ... },
    4: { ... }
};
```

## 🎨 Personalización de Estilos

Los estilos están basados en TailwindCSS. Para personalizarlos:

1. Modifica las clases de TailwindCSS en `compras.js`
2. Agrega estilos personalizados en `head.php`
3. Modifica los temas en los componentes:
   - `theme: 'corporativo'` → Azul corporativo
   - `theme: 'light'` → Fondo blanco
   - `theme: 'dark'` → Fondo oscuro

## 📚 Documentación Adicional

- `MODULO_COMPRAS.md` - Documentación completa del módulo
- `RESUMEN_COMPRAS.md` - Resumen de implementación
- `create_tables_compras.sql` - Script de creación de tablas
- `seed_data_compras.sql` - Datos de prueba

## 🆘 Soporte

Si encuentras problemas:
1. Revisa la consola del navegador (F12)
2. Revisa los logs de PHP
3. Verifica las consultas SQL
4. Consulta la documentación de CoffeeSoft

---

**¡Listo! El módulo de compras está instalado y funcionando.**

**Desarrollado con CoffeeSoft Framework**
