# Design Document - Módulo de Almacén

## Overview

El módulo de Almacén es un sistema de gestión de salidas de almacén construido con el framework CoffeeSoft. Utiliza arquitectura MVC con PHP en el backend y jQuery + TailwindCSS en el frontend. El sistema implementa control de acceso por niveles, trazabilidad completa de movimientos y generación de reportes consolidados.

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Frontend Layer (JS)                      │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   App Class  │  │ AlmacenDash  │  │ Concentrado  │      │
│  │  (Templates) │  │    board     │  │    Class     │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
                            ↕ AJAX (useFetch)
┌─────────────────────────────────────────────────────────────┐
│                   Controller Layer (PHP)                     │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              ctrl-almacen.php                        │   │
│  │  init() | ls() | addOutput() | editOutput()         │   │
│  │  deleteOutput() | getConcentrado() | uploadFile()   │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↕ SQL Queries
┌─────────────────────────────────────────────────────────────┐
│                     Model Layer (PHP)                        │
│  ┌──────────────────────────────────────────────────────┐   │
│  │              mdl-almacen.php (extends CRUD)          │   │
│  │  listOutputs() | createOutput() | updateOutput()     │   │
│  │  deleteOutputById() | getOutputById()                │   │
│  │  listConcentrado() | lsSupplyItems()                 │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            ↕
┌─────────────────────────────────────────────────────────────┐
│                      Database (MySQL)                        │
│  warehouse_output | product | product_class | file          │
│  module | module_unlock | monthly_module_lock | audit_log   │
└─────────────────────────────────────────────────────────────┘
```

### Technology Stack

- **Frontend**: jQuery 3.x, TailwindCSS 2.x, CoffeeSoft Framework
- **Backend**: PHP 7.4+, CoffeeSoft CRUD Class
- **Database**: MySQL 5.7+
- **Additional Libraries**: 
  - Moment.js (date handling)
  - DataTables (table pagination)
  - SweetAlert2 (confirmations)
  - Bootbox (modals)

## Components and Interfaces

### Frontend Components

#### 1. App Class (Main Module Controller)

**Purpose**: Orchestrates the main warehouse module interface with tabs and navigation.

**Key Methods**:
- `render()`: Initializes the complete module interface
- `layout()`: Creates the primary layout with tabs
- `filterBar()`: Renders date picker and filters
- `lsOutputs()`: Displays warehouse outputs table for selected date

**Dependencies**: Extends `Templates` from CoffeeSoft

#### 2. AlmacenDashboard Class

**Purpose**: Manages the dashboard view with totals and quick actions.

**Key Methods**:
- `renderDashboard()`: Displays total outputs and action buttons
- `showTotalOutputs()`: Calculates and displays daily total
- `uploadFileModal()`: Opens file upload interface

**UI Components**:
- Total outputs card (green background)
- Three action buttons (Concentrado, Subir archivos, Registrar salida)
- Quick stats display

#### 3. Concentrado Class

**Purpose**: Handles consolidated reports with inputs/outputs balance.

**Key Methods**:
- `renderConcentrado()`: Displays consolidated table
- `filterByDateRange()`: Applies date range filter
- `exportToExcel()`: Generates Excel export
- `expandWarehouseDetails()`: Shows detailed movements

**UI Components**:
- Date range picker
- Expandable table with color-coded columns
- Export button
- Balance totals footer

### Backend Components

#### 1. Controller (ctrl-almacen.php)

**Class**: `ctrl extends mdl`

**Methods**:

```php
init()
// Returns: ['supplyItems' => array, 'udn' => array]
// Purpose: Initialize filters and selectors

ls()
// Input: $_POST['date'], $_POST['udn']
// Returns: ['row' => array, 'total' => float]
// Purpose: List warehouse outputs for date

addOutput()
// Input: $_POST['warehouse_id', 'amount', 'description']
// Returns: ['status' => int, 'message' => string]
// Purpose: Create new warehouse output

editOutput()
// Input: $_POST['id', 'warehouse_id', 'amount', 'description']
// Returns: ['status' => int, 'message' => string]
// Purpose: Update existing output

getOutput()
// Input: $_POST['id']
// Returns: ['status' => int, 'data' => array]
// Purpose: Retrieve single output details

deleteOutput()
// Input: $_POST['id']
// Returns: ['status' => int, 'message' => string]
// Purpose: Delete output and log to audit

getConcentrado()
// Input: $_POST['fi', 'ff', 'udn']
// Returns: ['row' => array, 'totals' => array]
// Purpose: Generate consolidated report

uploadFile()
// Input: $_FILES['file'], $_POST['date']
// Returns: ['status' => int, 'message' => string]
// Purpose: Upload backup file (max 20MB)
```

**Helper Functions**:
```php
renderStatus($active)
// Returns: HTML badge with status color

formatOutputRow($data)
// Returns: Formatted table row with actions

calculateDailyTotal($outputs)
// Returns: Sum of all outputs for the day
```

#### 2. Model (mdl-almacen.php)

**Class**: `mdl extends CRUD`

**Properties**:
```php
$bd = "rfwsmqex_finanzas.";
$util; // Utileria instance
```

**Methods**:

```php
listOutputs($array)
// Input: [date, udn_id]
// Returns: Array of warehouse outputs
// Query: SELECT with JOIN to product and product_class

createOutput($array)
// Input: ['values' => string, 'data' => array]
// Returns: Boolean success
// Uses: _Insert method

updateOutput($array)
// Input: ['values' => string, 'where' => string, 'data' => array]
// Returns: Boolean success
// Uses: _Update method

deleteOutputById($array)
// Input: [id]
// Returns: Boolean success
// Uses: _Delete method

getOutputById($array)
// Input: [id]
// Returns: Single output record
// Uses: _Select method

lsSupplyItems($array)
// Input: [active]
// Returns: Array of supply items for selector
// Query: SELECT id, name AS valor FROM product WHERE active = ?

listConcentrado($array)
// Input: [fi, ff, udn_id]
// Returns: Array with grouped inputs/outputs
// Query: Complex query with UNION for inputs and outputs

getBalanceByWarehouse($array)
// Input: [warehouse_id, fi, ff]
// Returns: Initial balance, inputs, outputs, final balance
// Query: Calculates balance from historical data

logAuditDelete($array)
// Input: [user_id, record_id, table_name, amount]
// Returns: Boolean success
// Uses: _Insert to audit_log table
```

## Data Models

### Database Schema

#### warehouse_output (Salidas de almacén)
```sql
CREATE TABLE warehouse_output (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    description TEXT,
    operation_date DATE NOT NULL,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (product_id) REFERENCES product(id)
);
```

#### product (Productos/Insumos)
```sql
CREATE TABLE product (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_class_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    active TINYINT DEFAULT 1,
    FOREIGN KEY (product_class_id) REFERENCES product_class(id)
);
```

#### product_class (Clasificación de productos)
```sql
CREATE TABLE product_class (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    active TINYINT DEFAULT 1
);
```

#### file (Archivos de respaldo)
```sql
CREATE TABLE file (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    user_id INT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    upload_date DATETIME NOT NULL,
    size_bytes TEXT,
    path TEXT,
    extension CHAR(5),
    operation_date DATE
);
```

#### module (Módulos del sistema)
```sql
CREATE TABLE module (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    active TINYINT DEFAULT 1,
    description TEXT
);
```

#### module_unlock (Desbloqueo de módulos)
```sql
CREATE TABLE module_unlock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    udn_id INT NOT NULL,
    module_id INT NOT NULL,
    unlock_date DATETIME NOT NULL,
    lock_date DATETIME,
    lock_reason TEXT,
    operation_date DATE,
    active TINYINT DEFAULT 1
);
```

#### monthly_module_lock (Bloqueo mensual)
```sql
CREATE TABLE monthly_module_lock (
    id INT PRIMARY KEY AUTO_INCREMENT,
    module_id INT NOT NULL,
    month VARCHAR(30) NOT NULL,
    lock_time TIME NOT NULL
);
```

#### audit_log (Bitácora de auditoría)
```sql
CREATE TABLE audit_log (
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
    creation_date DATETIME NOT NULL
);
```

### Data Flow Diagrams

#### Add Warehouse Output Flow
```
User → Click "Registrar nueva salida"
  ↓
Frontend → Display modal form
  ↓
User → Fill form (warehouse, amount, description)
  ↓
Frontend → Validate all fields required
  ↓
Frontend → useFetch({ opc: 'addOutput', ... })
  ↓
Controller → addOutput()
  ↓
Model → createOutput()
  ↓
Database → INSERT INTO warehouse_output
  ↓
Controller → Return { status: 200, message: 'Success' }
  ↓
Frontend → Show success alert
  ↓
Frontend → Refresh table with lsOutputs()
```

#### Delete Warehouse Output Flow
```
User → Click delete button
  ↓
Frontend → Show confirmation modal (SweetAlert)
  ↓
User → Confirm deletion
  ↓
Frontend → useFetch({ opc: 'deleteOutput', id: X })
  ↓
Controller → deleteOutput()
  ↓
Model → getOutputById() to retrieve data
  ↓
Model → logAuditDelete() to record in audit_log
  ↓
Model → deleteOutputById()
  ↓
Database → DELETE FROM warehouse_output WHERE id = X
  ↓
Controller → Return { status: 200, message: 'Deleted' }
  ↓
Frontend → Show success message
  ↓
Frontend → Refresh table
```

#### Generate Consolidated Report Flow
```
User → Click "Concentrado de almacén"
  ↓
Frontend → Display date range picker
  ↓
User → Select date range (fi, ff)
  ↓
Frontend → useFetch({ opc: 'getConcentrado', fi, ff, udn })
  ↓
Controller → getConcentrado()
  ↓
Model → listConcentrado([fi, ff, udn])
  ↓
Database → Complex query with UNION:
           - SELECT inputs from purchases
           - SELECT outputs from warehouse_output
           - GROUP BY product_id
  ↓
Model → getBalanceByWarehouse() for each product
  ↓
Controller → Format data with totals
  ↓
Controller → Return { row: [...], totals: {...} }
  ↓
Frontend → Render table with color-coded columns
  ↓
Frontend → Display totals footer
  ↓
User → Click "Exportar a Excel" (optional)
  ↓
Frontend → Generate Excel file from table data
```

## Error Handling

### Frontend Error Handling

**Validation Errors**:
```javascript
// Form validation before submit
if (!warehouse_id || !amount || !description) {
    alert({
        icon: "warning",
        text: "Todos los campos son obligatorios"
    });
    return false;
}

// Amount validation
if (amount <= 0) {
    alert({
        icon: "error",
        text: "La cantidad debe ser mayor a cero"
    });
    return false;
}
```

**AJAX Error Handling**:
```javascript
useFetch({
    url: api,
    data: { opc: 'addOutput', ... },
    success: (response) => {
        if (response.status === 200) {
            alert({ icon: "success", text: response.message });
            this.lsOutputs();
        } else {
            alert({ icon: "error", text: response.message });
        }
    },
    error: (error) => {
        alert({
            icon: "error",
            text: "Error de conexión con el servidor"
        });
        console.error(error);
    }
});
```

### Backend Error Handling

**Controller Error Responses**:
```php
function addOutput() {
    $status = 500;
    $message = 'Error al crear la salida de almacén';
    
    try {
        // Validate required fields
        if (empty($_POST['warehouse_id']) || 
            empty($_POST['amount']) || 
            empty($_POST['description'])) {
            return [
                'status' => 400,
                'message' => 'Todos los campos son obligatorios'
            ];
        }
        
        // Validate amount
        if ($_POST['amount'] <= 0) {
            return [
                'status' => 400,
                'message' => 'La cantidad debe ser mayor a cero'
            ];
        }
        
        $_POST['operation_date'] = date('Y-m-d');
        $_POST['active'] = 1;
        
        $create = $this->createOutput($this->util->sql($_POST));
        
        if ($create) {
            $status = 200;
            $message = 'Salida de almacén registrada correctamente';
        }
        
    } catch (Exception $e) {
        $status = 500;
        $message = 'Error interno del servidor';
        error_log($e->getMessage());
    }
    
    return [
        'status' => $status,
        'message' => $message
    ];
}
```

**Model Error Handling**:
```php
function createOutput($array) {
    try {
        return $this->_Insert([
            'table' => "{$this->bd}warehouse_output",
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    } catch (PDOException $e) {
        error_log("Database error in createOutput: " . $e->getMessage());
        return false;
    }
}
```

### File Upload Error Handling

```php
function uploadFile() {
    $status = 500;
    $message = 'Error al subir el archivo';
    
    // Validate file exists
    if (!isset($_FILES['file'])) {
        return ['status' => 400, 'message' => 'No se recibió ningún archivo'];
    }
    
    $file = $_FILES['file'];
    
    // Validate file size (20MB max)
    $maxSize = 20 * 1024 * 1024; // 20MB in bytes
    if ($file['size'] > $maxSize) {
        return [
            'status' => 400,
            'message' => 'El archivo excede el tamaño máximo de 20MB'
        ];
    }
    
    // Validate file extension
    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'xlsx', 'xls'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedExtensions)) {
        return [
            'status' => 400,
            'message' => 'Tipo de archivo no permitido'
        ];
    }
    
    // Upload logic...
    
    return ['status' => $status, 'message' => $message];
}
```

## Testing Strategy

### Unit Testing

**Frontend Unit Tests**:
```javascript
// Test: Form validation
describe('AlmacenForm Validation', () => {
    test('should reject empty warehouse field', () => {
        const result = validateForm({ warehouse_id: '', amount: 100, description: 'Test' });
        expect(result.valid).toBe(false);
        expect(result.error).toBe('Todos los campos son obligatorios');
    });
    
    test('should reject zero or negative amount', () => {
        const result = validateForm({ warehouse_id: 1, amount: 0, description: 'Test' });
        expect(result.valid).toBe(false);
        expect(result.error).toBe('La cantidad debe ser mayor a cero');
    });
    
    test('should accept valid form data', () => {
        const result = validateForm({ warehouse_id: 1, amount: 100, description: 'Test' });
        expect(result.valid).toBe(true);
    });
});

// Test: Daily total calculation
describe('Daily Total Calculation', () => {
    test('should calculate correct sum of outputs', () => {
        const outputs = [
            { amount: 100.50 },
            { amount: 250.75 },
            { amount: 50.25 }
        ];
        const total = calculateDailyTotal(outputs);
        expect(total).toBe(401.50);
    });
});
```

**Backend Unit Tests** (PHPUnit):
```php
class AlmacenControllerTest extends TestCase {
    
    public function testAddOutputWithValidData() {
        $_POST = [
            'warehouse_id' => 1,
            'amount' => 100.50,
            'description' => 'Test output'
        ];
        
        $ctrl = new ctrl();
        $result = $ctrl->addOutput();
        
        $this->assertEquals(200, $result['status']);
        $this->assertStringContainsString('correctamente', $result['message']);
    }
    
    public function testAddOutputWithMissingFields() {
        $_POST = [
            'warehouse_id' => 1,
            'amount' => ''
        ];
        
        $ctrl = new ctrl();
        $result = $ctrl->addOutput();
        
        $this->assertEquals(400, $result['status']);
        $this->assertStringContainsString('obligatorios', $result['message']);
    }
    
    public function testDeleteOutputLogsToAudit() {
        $_POST = ['id' => 1];
        
        $ctrl = new ctrl();
        $result = $ctrl->deleteOutput();
        
        // Verify audit log entry was created
        $auditLog = $ctrl->getLastAuditEntry();
        $this->assertEquals('delete', $auditLog['action']);
        $this->assertEquals('warehouse_output', $auditLog['name_table']);
    }
}
```

### Integration Testing

**Test Scenarios**:

1. **Complete Output Registration Flow**:
   - User opens form
   - Fills all fields
   - Submits form
   - Verify database record created
   - Verify table refreshes with new data
   - Verify daily total updates

2. **Edit and Delete Flow**:
   - Create test output
   - Edit the output
   - Verify changes in database
   - Delete the output
   - Verify audit log entry
   - Verify output removed from table

3. **Consolidated Report Generation**:
   - Create test inputs and outputs
   - Generate consolidated report
   - Verify balance calculations
   - Verify totals accuracy
   - Test Excel export

4. **Access Level Restrictions**:
   - Test each access level (Captura, Gerencia, Contabilidad, Administración)
   - Verify correct buttons/views displayed
   - Verify restricted actions blocked
   - Verify module name changes based on level

### End-to-End Testing

**User Scenarios**:

1. **Daily Operations (Captura Level)**:
   ```
   - Login as captura user
   - Navigate to "Salidas de almacén"
   - Register 3 new outputs
   - Edit one output
   - Delete one output
   - Verify daily total is correct
   - Upload backup file
   ```

2. **Report Generation (Gerencia Level)**:
   ```
   - Login as gerencia user
   - Navigate to "Almacén"
   - Click "Concentrado de almacén"
   - Select date range (last 7 days)
   - Verify balance calculations
   - Expand warehouse details
   - Export to Excel
   - Verify Excel file contents
   ```

3. **Multi-UDN Filtering (Contabilidad Level)**:
   ```
   - Login as contabilidad user
   - Select different UDN from filter
   - Verify data changes
   - Attempt to edit (should be blocked)
   - Generate consolidated report
   - Verify UDN-specific data
   ```

### Performance Testing

**Load Testing Targets**:
- Table load time: < 2 seconds for 1000 records
- Form submission: < 1 second response time
- Consolidated report generation: < 5 seconds for 30-day range
- Excel export: < 10 seconds for 1000 records

**Optimization Strategies**:
- Database indexing on `operation_date`, `product_id`, `active`
- Pagination for large datasets (15 records per page)
- Lazy loading for consolidated report details
- Caching for supply items selector

## Design Decisions and Rationales

### 1. Tab-Based Navigation

**Decision**: Use `tabLayout` component for module organization

**Rationale**: 
- Provides clear separation between daily operations and reporting
- Reduces cognitive load by showing only relevant interface
- Aligns with existing CoffeeSoft patterns (see pivote analitycs)
- Allows dynamic content loading per tab

### 2. Modal Forms for CRUD Operations

**Decision**: Use `createModalForm` for add/edit operations

**Rationale**:
- Keeps user in context without page navigation
- Reduces visual clutter on main interface
- Provides clear focus on data entry
- Consistent with CoffeeSoft framework patterns

### 3. Separate Classes for Dashboard and Consolidated Report

**Decision**: Create `AlmacenDashboard` and `Concentrado` as separate classes

**Rationale**:
- Follows Single Responsibility Principle
- Allows independent development and testing
- Facilitates code reuse
- Matches pivote analitycs structure with multiple classes

### 4. Audit Logging for Deletions

**Decision**: Log all deletions to `audit_log` table before removing records

**Rationale**:
- Provides traceability for financial operations
- Meets compliance requirements
- Allows recovery of accidentally deleted data
- Supports accountability and security audits

### 5. Access Level-Based UI Rendering

**Decision**: Dynamically adjust UI based on user access level

**Rationale**:
- Single codebase for all user types
- Reduces maintenance overhead
- Prevents unauthorized actions at UI level
- Provides better user experience (no disabled buttons)

### 6. Color-Coded Consolidated Report

**Decision**: Use green for inputs, orange for outputs in consolidated table

**Rationale**:
- Provides immediate visual distinction
- Reduces cognitive load when analyzing data
- Follows accounting conventions (green = positive, orange = outflow)
- Matches reference design from admin-flex.png

### 7. File Size Limit (20MB)

**Decision**: Enforce 20MB maximum file size for uploads

**Rationale**:
- Prevents server overload
- Reasonable size for PDF/Excel backup files
- Balances storage costs with functionality
- Provides clear user feedback on limit

### 8. Date-Based Filtering

**Decision**: Use date picker with automatic table refresh

**Rationale**:
- Aligns with daily operations workflow
- Reduces data load (only show relevant date)
- Improves performance
- Matches user mental model (daily accounting)

## Security Considerations

### Authentication and Authorization

- All endpoints require authenticated session
- Access level validation on every controller method
- SQL injection prevention via prepared statements (`$this->util->sql()`)
- XSS prevention via output escaping in templates

### Data Validation

- Server-side validation for all inputs
- Type checking for numeric fields
- Length limits on text fields
- File type and size validation for uploads

### Audit Trail

- All deletions logged with user ID, timestamp, and affected data
- Modification history tracked via `operation_date` field
- User actions traceable through session data

## Deployment Considerations

### File Structure
```
finanzas/
├── captura/
│   └── almacen.js          # Frontend main file
├── ctrl/
│   └── ctrl-almacen.php    # Controller
└── mdl/
    └── mdl-almacen.php     # Model
```

### Database Migration Script
```sql
-- Run this script to set up the warehouse module
-- Tables are already defined in the database schema section
-- Add indexes for performance
CREATE INDEX idx_warehouse_output_date ON warehouse_output(operation_date);
CREATE INDEX idx_warehouse_output_product ON warehouse_output(product_id);
CREATE INDEX idx_product_active ON product(active);
CREATE INDEX idx_audit_log_date ON audit_log(creation_date);
```

### Configuration Requirements

- PHP 7.4+ with PDO extension
- MySQL 5.7+ with InnoDB engine
- Web server with mod_rewrite enabled
- Minimum 2GB RAM for server
- 100GB storage for file uploads

### Dependencies

- CoffeeSoft framework files:
  - `src/js/coffeSoft.js`
  - `src/js/plugins.js`
  - `conf/_CRUD.php`
  - `conf/_Utileria.php`

## Future Enhancements

1. **Inventory Tracking**: Add real-time inventory levels with alerts for low stock
2. **Barcode Scanning**: Integrate barcode scanner for faster data entry
3. **Mobile App**: Develop mobile interface for on-the-go warehouse management
4. **Advanced Analytics**: Add predictive analytics for inventory forecasting
5. **Multi-Currency Support**: Handle international suppliers and conversions
6. **Batch Operations**: Allow bulk import/export of warehouse movements
7. **Photo Attachments**: Enable photo uploads for physical verification
8. **Approval Workflow**: Add multi-level approval for large outputs
