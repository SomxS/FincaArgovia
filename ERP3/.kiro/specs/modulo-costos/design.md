# Design Document - Módulo de Costos

## Overview

El módulo de Costos es un sistema de consulta financiera de solo lectura que consolida información de costos directos (módulo Compras) y salidas de almacén (módulo Almacén) en un concentrado diario. La arquitectura sigue el patrón MVC de CoffeeSoft con componentes reutilizables de jQuery + TailwindCSS.

**Características principales:**
- Consulta de concentrado diario por rango de fechas
- Filtrado por UDN (solo usuarios nivel 3)
- Exportación a Excel con filtros aplicados
- Modo de solo lectura estricto (sin edición)
- Integración con módulos Compras y Almacén
- Interfaz responsive con pestañas de navegación

## Architecture

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend (Browser)                        │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  costos.js (extends Templates)                         │ │
│  │  - App class (main controller)                         │ │
│  │  - Layout management                                   │ │
│  │  - Filter bar (date range, UDN)                        │ │
│  │  - Table rendering                                     │ │
│  │  - Excel export trigger                                │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ↓ AJAX (useFetch)
┌─────────────────────────────────────────────────────────────┐
│                    Backend (PHP)                             │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  ctrl-costos.php (Controller)                          │ │
│  │  - init(): Load filters (UDN list)                     │ │
│  │  - ls(): Get daily cost summary                        │ │
│  │  - exportExcel(): Generate Excel file                  │ │
│  └────────────────────────────────────────────────────────┘ │
│                            ↓                                 │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  mdl-costos.php (Model)                                │ │
│  │  - lsUDN(): Get business units                         │ │
│  │  - listCostos(): Query consolidated costs              │ │
│  │  - getCostosDirectos(): From purchases module          │ │
│  │  - getSalidasAlmacen(): From warehouse module          │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ↓ SQL Queries
┌─────────────────────────────────────────────────────────────┐
│                    Database (MySQL)                          │
│  - purchases (compras)                                       │
│  - warehouse_output (salidas_almacen)                        │
│  - usuarios (user permissions)                               │
│  - product_class (clasificación productos)                   │
└─────────────────────────────────────────────────────────────┘
```

### Component Hierarchy

```
App (extends Templates)
├── layout()
│   └── primaryLayout()
│       ├── filterBar (date range, UDN, export button)
│       └── container (data table)
├── filterBar()
│   ├── dataPicker (date range selector)
│   ├── select UDN (conditional: level 3 only)
│   └── button "Exportar a Excel"
├── ls()
│   └── createTable()
│       ├── Grouped rows by product_class
│       ├── Daily totals per date
│       └── Grand totals
└── exportExcel()
    └── Trigger backend export with current filters
```

## Components and Interfaces

### Frontend Components (costos.js)

#### Class: App

```javascript
class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "costos";
    }

    // Main methods
    render()           // Initialize module
    layout()           // Create UI structure
    filterBar()        // Setup filters (date range, UDN)
    ls()              // Load and display cost summary table
    exportExcel()     // Trigger Excel export
}
```

#### Key Methods

**render()**
- Calls `layout()`, `filterBar()`, `ls()`
- Initializes the module on page load

**layout()**
- Uses `primaryLayout()` from Templates
- Creates filterBar and container sections
- Applies responsive classes

**filterBar()**
- Implements `createfilterBar()` component
- Date range picker (dataPicker)
- UDN select (conditional based on user level)
- Export button

**ls()**
- Calls backend with `{ opc: 'ls', fi, ff, udn }`
- Uses `createTable()` to render results
- Groups data by product_class
- Shows daily totals and grand totals
- Read-only mode (no action buttons)

**exportExcel()**
- Validates date range
- Calls backend with `{ opc: 'exportExcel', fi, ff, udn }`
- Downloads generated Excel file

### Backend Components

#### Controller (ctrl-costos.php)

```php
class ctrl extends mdl {
    
    function init() {
        // Load filter options
        return [
            'udn' => $this->lsUDN(),
            'userLevel' => $_SESSION['nivel']
        ];
    }

    function ls() {
        // Get consolidated cost summary
        $fi = $_POST['fi'];
        $ff = $_POST['ff'];
        $udn = $_POST['udn'] ?? null;
        
        $data = $this->listCostos([$fi, $ff, $udn]);
        
        // Format rows for table
        $rows = [];
        foreach ($data as $item) {
            $rows[] = [
                'Categoria' => $item['product_class'],
                'Costo_Directo' => formatPrice($item['costo_directo']),
                'Salida_Almacen' => formatPrice($item['salida_almacen']),
                'Total' => formatPrice($item['total']),
                'opc' => 0 // Read-only
            ];
        }
        
        return [
            'row' => $rows,
            'thead' => ''
        ];
    }

    function exportExcel() {
        // Generate Excel file with current filters
        $fi = $_POST['fi'];
        $ff = $_POST['ff'];
        $udn = $_POST['udn'] ?? null;
        
        $data = $this->listCostos([$fi, $ff, $udn]);
        
        // Use PHPSpreadsheet or similar
        $excel = $this->generateExcelFile($data, $fi, $ff);
        
        return [
            'status' => 200,
            'file' => $excel
        ];
    }
}
```

#### Model (mdl-costos.php)

```php
class mdl extends CRUD {
    
    function lsUDN() {
        // Get business units for filter
        $query = "
            SELECT id, name AS valor
            FROM {$this->bd}usuarios
            WHERE active = 1 AND nivel = 3
            ORDER BY name ASC
        ";
        return $this->_Read($query, []);
    }

    function listCostos($array) {
        // Consolidate costs from purchases and warehouse
        $fi = $array[0];
        $ff = $array[1];
        $udn = $array[2] ?? null;
        
        $query = "
            SELECT 
                pc.classification AS product_class,
                DATE(p.operation_date) AS fecha,
                SUM(CASE WHEN p.type = 'compra' THEN p.amount ELSE 0 END) AS costo_directo,
                SUM(CASE WHEN wo.type = 'salida' THEN wo.amount ELSE 0 END) AS salida_almacen,
                SUM(p.amount + COALESCE(wo.amount, 0)) AS total
            FROM {$this->bd}product_class pc
            LEFT JOIN {$this->bd}purchases p ON p.product_class_id = pc.id
                AND p.operation_date BETWEEN ? AND ?
                " . ($udn ? "AND p.udn_id = ?" : "") . "
            LEFT JOIN {$this->bd}warehouse_output wo ON wo.product_id = p.product_id
                AND wo.operation_date BETWEEN ? AND ?
                " . ($udn ? "AND wo.udn_id = ?" : "") . "
            WHERE pc.active = 1
            GROUP BY pc.id, DATE(p.operation_date)
            ORDER BY fecha DESC, pc.classification ASC
        ";
        
        $params = $udn 
            ? [$fi, $ff, $udn, $fi, $ff, $udn]
            : [$fi, $ff, $fi, $ff];
        
        return $this->_Read($query, $params);
    }

    function getCostosDirectos($array) {
        // Get direct costs from purchases module
        $query = "
            SELECT 
                product_class_id,
                DATE(operation_date) AS fecha,
                SUM(amount) AS total
            FROM {$this->bd}purchases
            WHERE operation_date BETWEEN ? AND ?
                AND active = 1
                " . ($array[2] ? "AND udn_id = ?" : "") . "
            GROUP BY product_class_id, DATE(operation_date)
        ";
        return $this->_Read($query, $array);
    }

    function getSalidasAlmacen($array) {
        // Get warehouse outputs
        $query = "
            SELECT 
                p.product_class_id,
                DATE(wo.operation_date) AS fecha,
                SUM(wo.amount) AS total
            FROM {$this->bd}warehouse_output wo
            INNER JOIN {$this->bd}product p ON wo.product_id = p.id
            WHERE wo.operation_date BETWEEN ? AND ?
                AND wo.active = 1
                " . ($array[2] ? "AND wo.udn_id = ?" : "") . "
            GROUP BY p.product_class_id, DATE(wo.operation_date)
        ";
        return $this->_Read($query, $array);
    }
}
```

## Data Models

### Database Schema

#### Table: product_class
```sql
CREATE TABLE product_class (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_class_id INT,
    name VARCHAR(50) NOT NULL,
    active TINYINT DEFAULT 1
);
```

#### Table: purchases (existing - from Compras module)
```sql
CREATE TABLE purchases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT,
    product_id INT,
    product_class_id INT,
    amount DECIMAL(12,2),
    operation_date DATE,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (product_class_id) REFERENCES product_class(id)
);
```

#### Table: warehouse_output (existing - from Almacén module)
```sql
CREATE TABLE warehouse_output (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT,
    amount DECIMAL(12,2),
    description TEXT,
    operation_date DATE,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (product_id) REFERENCES product(id)
);
```

#### Table: product (existing)
```sql
CREATE TABLE product (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_class_id INT,
    name VARCHAR(50),
    active TINYINT DEFAULT 1,
    FOREIGN KEY (product_class_id) REFERENCES product_class(id)
);
```

### Data Flow

```
User Input (Date Range + UDN)
    ↓
Frontend (costos.js)
    ↓ useFetch({ opc: 'ls', fi, ff, udn })
Controller (ctrl-costos.php::ls())
    ↓
Model (mdl-costos.php::listCostos())
    ↓ SQL JOIN
Database (purchases + warehouse_output + product_class)
    ↓ Result Set
Model (format and aggregate)
    ↓
Controller (format for table)
    ↓ JSON Response
Frontend (createTable())
    ↓
User sees consolidated cost table
```

## Error Handling

### Frontend Validation

```javascript
// Date range validation
if (fi > ff) {
    alert({
        icon: "error",
        text: "La fecha inicial debe ser menor o igual a la fecha final"
    });
    return;
}

// Max range validation (12 months)
const diffMonths = moment(ff).diff(moment(fi), 'months');
if (diffMonths > 12) {
    alert({
        icon: "warning",
        text: "El rango de fechas no puede exceder 12 meses"
    });
    return;
}
```

### Backend Validation

```php
// Validate date format
if (!validateDate($fi) || !validateDate($ff)) {
    return [
        'status' => 400,
        'message' => 'Formato de fecha inválido'
    ];
}

// Validate UDN exists (if provided)
if ($udn && !$this->udnExists($udn)) {
    return [
        'status' => 404,
        'message' => 'Unidad de negocio no encontrada'
    ];
}

// Validate user permissions
if ($udn && $_SESSION['nivel'] != 3) {
    return [
        'status' => 403,
        'message' => 'No tiene permisos para filtrar por UDN'
    ];
}
```

### Error Responses

```javascript
// Handle backend errors
success: (response) => {
    if (response.status !== 200) {
        alert({
            icon: "error",
            title: "Error",
            text: response.message
        });
        return;
    }
    // Process data
}
```

## Testing Strategy

### Unit Tests

**Frontend (costos.js)**
- Test date range validation
- Test UDN filter visibility based on user level
- Test Excel export trigger
- Test table rendering with mock data

**Backend (ctrl-costos.php)**
- Test `init()` returns correct filter options
- Test `ls()` with valid date range
- Test `ls()` with UDN filter
- Test `exportExcel()` generates valid file

**Model (mdl-costos.php)**
- Test `listCostos()` with various date ranges
- Test `listCostos()` with UDN filter
- Test `getCostosDirectos()` returns correct data
- Test `getSalidasAlmacen()` returns correct data

### Integration Tests

1. **Full flow test**: User selects date range → Backend queries data → Table displays results
2. **UDN filter test**: Level 3 user filters by UDN → Data updates correctly
3. **Excel export test**: User clicks export → File downloads with correct data
4. **Permission test**: Level 2 user cannot see UDN filter
5. **Read-only test**: No edit buttons or actions available

### Manual Testing Checklist

- [ ] Date range picker works correctly
- [ ] UDN filter appears only for level 3 users
- [ ] Table displays grouped data by product_class
- [ ] Daily totals calculate correctly
- [ ] Grand totals calculate correctly
- [ ] Excel export includes all filtered data
- [ ] No edit/delete buttons appear
- [ ] Responsive design works on mobile
- [ ] Error messages display correctly
- [ ] Loading states show during data fetch

## Security Considerations

### Authentication & Authorization

```php
// Check user is logged in
if (!isset($_SESSION['idUser'])) {
    return ['status' => 401, 'message' => 'No autenticado'];
}

// Check user level for UDN filter
if (isset($_POST['udn']) && $_SESSION['nivel'] != 3) {
    return ['status' => 403, 'message' => 'Acceso denegado'];
}
```

### Input Sanitization

```php
// Sanitize all inputs
$fi = $this->util->sanitize($_POST['fi']);
$ff = $this->util->sanitize($_POST['ff']);
$udn = isset($_POST['udn']) ? $this->util->sanitize($_POST['udn']) : null;

// Use prepared statements
$query = "SELECT ... WHERE date BETWEEN ? AND ?";
$this->_Read($query, [$fi, $ff]);
```

### Read-Only Enforcement

```php
// Block any modification attempts
$allowedOperations = ['init', 'ls', 'exportExcel'];
if (!in_array($_POST['opc'], $allowedOperations)) {
    return ['status' => 403, 'message' => 'Operación no permitida'];
}
```

### SQL Injection Prevention

- Use prepared statements with `_Read()` method
- Sanitize all user inputs
- Validate data types before queries

### XSS Prevention

```javascript
// Escape HTML in table cells
Nombre: $('<div>').text(item.name).html()
```

## Performance Optimization

### Database Optimization

```sql
-- Add indexes for faster queries
CREATE INDEX idx_purchases_date ON purchases(operation_date);
CREATE INDEX idx_purchases_udn ON purchases(udn_id);
CREATE INDEX idx_warehouse_date ON warehouse_output(operation_date);
CREATE INDEX idx_product_class ON product(product_class_id);
```

### Query Optimization

- Use LEFT JOIN instead of multiple queries
- Aggregate data in SQL (SUM, GROUP BY)
- Limit date range to 12 months maximum
- Cache UDN list in frontend

### Frontend Optimization

- Use DataTables pagination for large datasets
- Lazy load table rows
- Debounce filter changes
- Cache filter selections in localStorage

## Deployment Notes

### File Structure

```
finanzas/
├── captura/
│   └── costos.js          # Frontend module
├── ctrl/
│   └── ctrl-costos.php    # Controller
└── mdl/
    └── mdl-costos.php     # Model
```

### Dependencies

- jQuery 3.x
- TailwindCSS 2.x
- CoffeeSoft framework (coffeSoft.js, plugins.js)
- Moment.js (date handling)
- DataTables (table pagination)
- PHPSpreadsheet (Excel export)

### Configuration

```php
// In mdl-costos.php
$this->bd = "rfwsmqex_finanzas.";
```

### Database Migrations

No new tables required - uses existing tables:
- `product_class`
- `purchases`
- `warehouse_output`
- `product`
- `usuarios`

### Environment Variables

```php
// Session variables required
$_SESSION['idUser']  // User ID
$_SESSION['nivel']   // User level (2 or 3)
$_SESSION['udn']     // User's business unit
```
