# Design Document - Módulo de Compras

## Overview

El módulo de Compras es un sistema integral de gestión financiera que permite registrar, consultar y administrar compras de tres tipos: Fondo fijo, Corporativo y Crédito. El sistema implementa una arquitectura MVC utilizando el framework CoffeeSoft, con componentes reutilizables de jQuery + TailwindCSS, controladores PHP y modelos que extienden la clase CRUD base.

El diseño se basa en el pivote "admin" existente, adaptándolo para manejar múltiples tipos de compras, validaciones dinámicas, control de accesos por perfil y generación de reportes consolidados.

## Architecture

### Tech Stack
- **Frontend**: JavaScript (jQuery), CoffeeSoft Framework, TailwindCSS
- **Backend**: PHP 7.4+
- **Database**: MySQL (rfwsmqex_finanzas)
- **Componentes**: Templates, Components, Complements (CoffeeSoft)

### Estructura de Archivos

```
finanzas/
├── captura/
│   ├── compras.js                    # Frontend principal
│   ├── ctrl/
│   │   └── ctrl-compras.php          # Controlador
│   └── mdl/
│       └── mdl-compras.php           # Modelo
└── index.php                         # Punto de entrada
```

### Flujo de Datos

```
Usuario → compras.js → ctrl-compras.php → mdl-compras.php → MySQL
                ↓                                              ↓
         CoffeeSoft Components                         CRUD Operations
```

## Components and Interfaces

### Frontend (compras.js)

#### Clase Principal: App
Extiende `Templates` del framework CoffeeSoft.

**Propiedades:**
- `PROJECT_NAME`: "compras"
- `_link`: "ctrl/ctrl-compras.php"
- `_div_modulo`: "root"

**Métodos principales:**

- `render()`: Inicializa el layout, filterBar y carga la tabla principal
- `layout()`: Crea la estructura visual usando `tabLayout` con 3 pestañas
- `filterBar()`: Genera filtros dinámicos (fecha, tipo de compra, método de pago)
- `ls()`: Lista todas las compras con filtros aplicados
- `lsConcentrado()`: Muestra el reporte concentrado de compras
- `addCompra()`: Abre modal para registrar nueva compra
- `editCompra(id)`: Abre modal para editar compra existente
- `deleteCompra(id)`: Elimina una compra con confirmación
- `viewDetails(id)`: Muestra detalles completos de una compra
- `toggleModuleLock()`: Bloquea/desbloquea el módulo (solo Contabilidad)

#### Estructura de Tabs

```javascript
tabLayout({
    json: [
        { id: "compras", tab: "Compras", active: true, onClick: () => this.ls() },
        { id: "concentrado", tab: "Concentrado de compras", onClick: () => this.lsConcentrado() },
        { id: "archivos", tab: "Archivos", onClick: () => this.lsArchivos() }
    ]
});
```

### Backend (ctrl-compras.php)

#### Clase: ctrl extends mdl

**Métodos del controlador:**

1. `init()`: Inicializa datos para filtros
   - Retorna: UDN, clases de producto, tipos de compra, métodos de pago, proveedores

2. `ls()`: Lista compras con filtros
   - Entrada: fi, ff, tipo_compra, metodo_pago, udn
   - Salida: Array de compras formateadas con dropdown de acciones

3. `lsConcentrado()`: Genera reporte concentrado
   - Entrada: fi, ff, tipo_compra, udn
   - Salida: Datos agrupados por clase de producto y día

4. `getCompra()`: Obtiene una compra por ID
   - Entrada: id
   - Salida: Datos completos de la compra

5. `addCompra()`: Registra nueva compra
   - Entrada: Datos del formulario
   - Validaciones: Saldo fondo fijo, campos requeridos
   - Salida: status, message

6. `editCompra()`: Actualiza compra existente
   - Entrada: id, datos modificados
   - Validaciones: Bloqueo de módulo, reembolsos
   - Salida: status, message

7. `deleteCompra()`: Elimina una compra
   - Entrada: id
   - Validaciones: Permisos, reembolsos asociados
   - Salida: status, message

8. `statusCompra()`: Cambia estado activo/inactivo
   - Entrada: id, active
   - Salida: status, message

9. `toggleModuleLock()`: Bloquea/desbloquea módulo
   - Entrada: lock_status
   - Validación: Perfil de Contabilidad
   - Salida: status, message

**Funciones auxiliares:**

```php
function dropdown($id, $tipo_compra, $tiene_reembolso) {
    // Genera opciones de acción según contexto
}

function renderStatus($active) {
    // Retorna badge HTML según estado
}

function formatPurchaseType($type) {
    // Formatea tipo de compra para visualización
}
```

### Modelo (mdl-compras.php)

#### Clase: mdl extends CRUD

**Propiedades:**
- `$bd`: "rfwsmqex_finanzas."
- `$util`: Instancia de Utileria

**Métodos del modelo:**

1. `listCompras($array)`: Lista compras con joins
   - Joins: product_class, product, purchase_type, method_pay, supplier
   - Filtros: fecha, tipo_compra, metodo_pago, udn, active
   - Order: DESC por operation_date

2. `listConcentrado($array)`: Datos para reporte concentrado
   - Agrupación: product_class_id, DATE(operation_date)
   - Cálculos: SUM(subtotal), SUM(tax), SUM(total)
   - Filtros: rango de fechas, tipo_compra, udn

3. `getCompraById($id)`: Obtiene compra específica
   - Joins: Todas las tablas relacionadas
   - Where: purchase.id = ?

4. `createCompra($array)`: Inserta nueva compra
   - Tabla: purchase
   - Campos: udn_id, product_class_id, product_id, purchase_type_id, supplier_id, method_pay_id, subtotal, tax, total, description, operation_date, active

5. `updateCompra($array)`: Actualiza compra
   - Tabla: purchase
   - Where: id = ?

6. `deleteCompraById($id)`: Elimina compra
   - Tabla: purchase
   - Where: id = ?

7. `lsProductClass()`: Lista clases de producto
   - Tabla: product_class
   - Where: active = 1
   - Order: name ASC

8. `lsProductByClass($class_id)`: Lista productos por clase
   - Tabla: product
   - Where: product_class_id = ? AND active = 1

9. `lsPurchaseType()`: Lista tipos de compra
   - Tabla: purchase_type
   - Where: active = 1

10. `lsMethodPay()`: Lista métodos de pago
    - Tabla: method_pay
    - Where: active = 1

11. `lsSupplier()`: Lista proveedores
    - Tabla: supplier
    - Where: active = 1

12. `lsUDN()`: Lista unidades de negocio
    - Tabla: udn
    - Where: active = 1

13. `getSaldoFondoFijo($udn_id)`: Calcula saldo actual
    - Cálculo: saldo_inicial - SUM(salidas) + SUM(reembolsos)
    - Filtros: udn_id, tipo_compra = 'Fondo fijo'

14. `existsReembolso($compra_id)`: Verifica si tiene reembolso
    - Tabla: reembolsos
    - Where: compra_id = ?

15. `getModuleLockStatus($udn_id, $month)`: Estado de bloqueo
    - Tabla: monthly_module_lock
    - Where: module_id = 'compras' AND udn_id = ? AND month = ?

16. `createAuditLog($array)`: Registra auditoría
    - Tabla: audit_log
    - Campos: udn_id, user_id, record_id, name_table, name_user, name_udn, name_collaborator, action, change_items, creation_date

## Data Models

### Tabla: purchase

```sql
CREATE TABLE purchase (
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
    FOREIGN KEY (udn_id) REFERENCES udn(idUDN),
    FOREIGN KEY (product_class_id) REFERENCES product_class(id),
    FOREIGN KEY (product_id) REFERENCES product(id),
    FOREIGN KEY (purchase_type_id) REFERENCES purchase_type(id),
    FOREIGN KEY (supplier_id) REFERENCES supplier(id),
    FOREIGN KEY (method_pay_id) REFERENCES method_pay(id)
);
```

### Tabla: product_class

```sql
CREATE TABLE product_class (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    description TEXT NULL,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (udn_id) REFERENCES udn(idUDN)
);
```

### Tabla: product

```sql
CREATE TABLE product (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_class_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (product_class_id) REFERENCES product_class(id)
);
```

### Tabla: purchase_type

```sql
CREATE TABLE purchase_type (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    active TINYINT DEFAULT 1
);

-- Datos iniciales
INSERT INTO purchase_type (name) VALUES 
('Fondo fijo'),
('Corporativo'),
('Crédito');
```

### Tabla: method_pay

```sql
CREATE TABLE method_pay (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    active TINYINT DEFAULT 1
);

-- Datos iniciales
INSERT INTO method_pay (name) VALUES 
('Efectivo'),
('Tarjeta de crédito'),
('Transferencia'),
('Cheque');
```

### Tabla: supplier

```sql
CREATE TABLE supplier (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (udn_id) REFERENCES udn(idUDN)
);
```

### Tabla: module_unlock

```sql
CREATE TABLE module_unlock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    module_id INT NOT NULL,
    unlock_date DATETIME NOT NULL,
    lock_date DATETIME NULL,
    lock_reason TEXT NULL,
    operation_date DATE NOT NULL,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (udn_id) REFERENCES udn(idUDN),
    FOREIGN KEY (module_id) REFERENCES module(id)
);
```

### Tabla: monthly_module_lock

```sql
CREATE TABLE monthly_module_lock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    month VARCHAR(50) NOT NULL,
    lock_time TIME NOT NULL,
    INDEX idx_month (month)
);
```

### Tabla: audit_log

```sql
CREATE TABLE audit_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    user_id INT NOT NULL,
    record_id INT NOT NULL,
    name_table VARCHAR(255) NOT NULL,
    name_user VARCHAR(50) NOT NULL,
    name_udn VARCHAR(50) NOT NULL,
    name_collaborator VARCHAR(255) NULL,
    action ENUM('insert', 'update', 'delete', 'lock', 'unlock') NOT NULL,
    change_items LONGTEXT NULL,
    creation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (udn_id) REFERENCES udn(idUDN)
);
```

### Tabla: file (para archivos adjuntos)

```sql
CREATE TABLE file (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    user_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    size_bytes TEXT NULL,
    path TEXT NOT NULL,
    extension CHAR(5) NOT NULL,
    operation_date DATE NOT NULL,
    FOREIGN KEY (udn_id) REFERENCES udn(idUDN)
);
```

## Error Handling

### Validaciones Frontend

1. **Campos requeridos**: Validación automática con `required` en formularios
2. **Saldo insuficiente**: Verificar antes de guardar compra de fondo fijo
3. **Formato de números**: Validación con `validationInputForNumber()`
4. **Fechas**: Validación de rango de fechas en filtros

### Validaciones Backend

1. **Existencia de registros**: Verificar que existan product_class, product, etc.
2. **Permisos de usuario**: Validar perfil antes de operaciones críticas
3. **Bloqueo de módulo**: Verificar estado antes de editar/eliminar
4. **Reembolsos**: Verificar si existe reembolso antes de modificar monto
5. **Integridad referencial**: Validar foreign keys antes de insertar

### Manejo de Errores

```php
// Estructura estándar de respuesta
return [
    'status' => 200,  // 200: éxito, 400: validación, 500: error servidor
    'message' => 'Mensaje descriptivo',
    'data' => []  // Datos adicionales si aplica
];
```

### Mensajes de Error

- **400**: "El saldo del fondo fijo es insuficiente"
- **400**: "No se puede modificar una compra con reembolso asociado"
- **403**: "No tiene permisos para realizar esta acción"
- **404**: "Compra no encontrada"
- **409**: "El módulo está bloqueado por Contabilidad"
- **500**: "Error al guardar la compra"

## Testing Strategy

### Unit Tests

1. **Modelo (mdl-compras.php)**
   - Test: `listCompras()` retorna array con estructura correcta
   - Test: `getSaldoFondoFijo()` calcula correctamente el saldo
   - Test: `existsReembolso()` detecta reembolsos asociados
   - Test: `createCompra()` inserta registro correctamente

2. **Controlador (ctrl-compras.php)**
   - Test: `init()` retorna todos los datos de filtros
   - Test: `addCompra()` valida saldo de fondo fijo
   - Test: `editCompra()` respeta bloqueo de módulo
   - Test: `deleteCompra()` verifica permisos

3. **Frontend (compras.js)**
   - Test: `filterBar()` muestra/oculta campos según tipo de compra
   - Test: `addCompra()` valida campos requeridos
   - Test: Cálculo automático de impuesto y total

### Integration Tests

1. **Flujo completo de registro de compra**
   - Usuario selecciona tipo de compra → campos dinámicos se muestran
   - Usuario completa formulario → validación frontend
   - Submit → controlador valida → modelo inserta
   - Tabla se actualiza en tiempo real

2. **Flujo de edición con restricciones**
   - Usuario edita compra → verificar bloqueo de módulo
   - Si tiene reembolso → campos monto/tipo bloqueados
   - Guardar cambios → auditoría registrada

3. **Flujo de filtros dinámicos**
   - Usuario selecciona tipo de compra → tabla se filtra
   - Usuario selecciona método de pago → tabla se actualiza
   - Totales se recalculan correctamente

### Manual Testing

1. **Permisos por perfil**
   - Captura: puede registrar, editar, eliminar
   - Gerencia: puede ver todo, acceder a concentrado
   - Dirección: puede ver y descargar reportes
   - Contabilidad: puede bloquear/desbloquear módulo

2. **Validaciones de negocio**
   - Saldo de fondo fijo insuficiente → error
   - Editar compra con reembolso → campos bloqueados
   - Módulo bloqueado → edición restringida

3. **Interfaz responsive**
   - Probar en desktop, tablet, móvil
   - Modales se ajustan correctamente
   - Tablas con scroll horizontal en móvil

## Design Decisions

### 1. Uso del Framework CoffeeSoft

**Decisión**: Utilizar CoffeeSoft como base del frontend
**Razón**: 
- Componentes reutilizables ya probados (createTable, createForm, swalQuestion)
- Consistencia con otros módulos del ERP
- Reducción de tiempo de desarrollo
- Mantenimiento simplificado

### 2. Arquitectura MVC

**Decisión**: Separar lógica en ctrl, mdl y js
**Razón**:
- Separación de responsabilidades
- Facilita testing y mantenimiento
- Reutilización de código
- Escalabilidad del sistema

### 3. Filtros Dinámicos

**Decisión**: Mostrar/ocultar campos según tipo de compra
**Razón**:
- Mejora UX al mostrar solo campos relevantes
- Reduce errores de captura
- Interfaz más limpia y enfocada

### 4. Cálculo Automático de Totales

**Decisión**: Calcular impuesto y total automáticamente en frontend
**Razón**:
- Feedback inmediato al usuario
- Reduce errores de cálculo manual
- Mejora experiencia de usuario

### 5. Sistema de Auditoría

**Decisión**: Registrar todas las operaciones en audit_log
**Razón**:
- Trazabilidad completa de cambios
- Cumplimiento de requisitos contables
- Facilita resolución de conflictos
- Permite análisis de uso del sistema

### 6. Bloqueo de Módulo por Mes

**Decisión**: Permitir a Contabilidad bloquear el módulo mensualmente
**Razón**:
- Control de cierre contable
- Previene modificaciones post-cierre
- Flexibilidad para desbloquear si es necesario
- Cumple con procesos contables estándar

### 7. Restricción de Edición con Reembolsos

**Decisión**: No permitir modificar monto/tipo si existe reembolso
**Razón**:
- Mantiene integridad de datos financieros
- Evita descuadres en fondo fijo
- Protege información ya procesada

### 8. Tres Tipos de Compra

**Decisión**: Separar en Fondo fijo, Corporativo y Crédito
**Razón**:
- Diferentes flujos de aprobación
- Diferentes controles financieros
- Facilita reportes específicos por tipo
- Refleja procesos reales de negocio

### 9. Concentrado de Compras

**Decisión**: Vista separada para reporte consolidado
**Razón**:
- Análisis de gastos por período
- Visualización de tendencias
- Facilita toma de decisiones gerenciales
- Exportación a Excel para análisis externo

### 10. Uso de Pivote Admin

**Decisión**: Basar diseño en pivote admin existente
**Razón**:
- Aprovecha estructura probada
- Consistencia visual con otros módulos
- Reduce curva de aprendizaje
- Acelera desarrollo

## UI/UX Considerations

### Paleta de Colores

- **Azul corporativo**: `#103B60` (botones principales, headers)
- **Verde acción**: `#8CC63F` (estados activos, confirmaciones)
- **Rojo alerta**: `#DC2626` (eliminaciones, errores)
- **Gris claro**: `#EAEAEA` (fondos, separadores)
- **Fondo oscuro**: `#1F2A37` (modales, cards)

### Componentes Visuales

1. **Totales destacados**: Cards con fondo de color según tipo
   - Fondo fijo: Verde claro
   - Corporativo: Azul claro
   - Crédito: Naranja claro

2. **Tabla de compras**: Tema corporativo con:
   - Filas alternadas para mejor lectura
   - Iconos de acción en última columna
   - Badges de colores para estados

3. **Modales**: Fondo oscuro con:
   - Título descriptivo
   - Campos agrupados lógicamente
   - Botones de acción destacados

4. **Filtros**: Barra superior con:
   - Selectores desplegables
   - Calendario de rango de fechas
   - Botones de acción rápida

### Responsive Design

- **Desktop**: Layout completo con todas las columnas
- **Tablet**: Ocultar columnas secundarias, mantener acciones
- **Móvil**: Vista de cards en lugar de tabla, scroll horizontal si necesario
