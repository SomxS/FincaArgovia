# Design Document - Módulo Catálogo

## Overview

El módulo de Catálogo es un componente del sistema ERP3 que proporciona una interfaz unificada para gestionar la estructura organizacional del almacén. El módulo implementa un patrón MVC (Model-View-Controller) siguiendo la arquitectura existente del proyecto, con tres componentes principales:

1. **Frontend (JavaScript)**: Interfaz de usuario con pestañas para gestionar Categorías, Áreas y Zonas
2. **Controller (PHP)**: Capa de lógica de negocio que procesa peticiones y coordina operaciones
3. **Model (PHP)**: Capa de acceso a datos que interactúa con la base de datos MySQL

El módulo se integrará en la ruta `ERP3/operacion/catalogo/` y seguirá los patrones establecidos en el módulo de administración existente, utilizando las mismas librerías y componentes visuales (Bootstrap 5, DataTables, SweetAlert2, Bootbox).

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Browser (Cliente)                         │
│  ┌──────────────────────────────────────────────────────┐   │
│  │         catalogo.js (Frontend Logic)                  │   │
│  │  - CatalogoManager (Main Controller)                  │   │
│  │  - CategoriasManager (Categorías CRUD)                │   │
│  │  - AreasManager (Áreas CRUD)                          │   │
│  │  - ZonasManager (Zonas CRUD)                          │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ HTTP/AJAX (JSON)
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                  Web Server (Apache/PHP)                     │
│  ┌──────────────────────────────────────────────────────┐   │
│  │      ctrl-catalogo.php (Controller Layer)             │   │
│  │  - Routing & Request Handling                         │   │
│  │  - Business Logic                                     │   │
│  │  - Response Formatting                                │   │
│  └──────────────────────────────────────────────────────┘   │
│                            │                                 │
│                            ▼                                 │
│  ┌──────────────────────────────────────────────────────┐   │
│  │       mdl-catalogo.php (Model Layer)                  │   │
│  │  - Database Queries                                   │   │
│  │  - Data Validation                                    │   │
│  │  - CRUD Operations                                    │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                            │
                            │ PDO/MySQL
                            ▼
┌─────────────────────────────────────────────────────────────┐
│                    MySQL Database                            │
│  - mtto_categoria                                            │
│  - mtto_almacen_area                                         │
│  - mtto_almacen_zona (nueva tabla)                           │
└─────────────────────────────────────────────────────────────┘
```

### Component Interaction Flow

1. **User Interaction**: El usuario interactúa con la interfaz (pestañas, formularios, botones)
2. **Frontend Processing**: JavaScript captura el evento y prepara la petición AJAX
3. **HTTP Request**: Se envía una petición POST al controlador con datos JSON
4. **Controller Processing**: El controlador valida, procesa y delega al modelo
5. **Model Execution**: El modelo ejecuta queries usando CRUD3 y retorna resultados
6. **Response**: El controlador formatea la respuesta JSON
7. **UI Update**: JavaScript actualiza la interfaz con los resultados

## Components and Interfaces

### 1. Frontend Components (catalogo.js)

#### 1.1 CatalogoManager Class

Clase principal que gestiona el módulo completo.

```javascript
class CatalogoManager extends Templates {
    constructor(link, div_modulo)
    render()                    // Inicializa el módulo
    layout()                    // Crea la estructura HTML principal
    layoutHeader()              // Renderiza el encabezado
    layoutTabs()                // Crea las pestañas de navegación
}
```

**Responsabilidades:**
- Inicializar el módulo y sus componentes
- Crear la estructura de pestañas
- Coordinar entre los sub-módulos (Categorías, Áreas, Zonas)

#### 1.2 CategoriasManager Class

Gestiona las operaciones CRUD de categorías.

```javascript
class CategoriasManager extends Templates {
    constructor(link, div_modulo)
    render()                    // Renderiza la sección de categorías
    layout()                    // Crea la estructura HTML
    filterBar()                 // Crea la barra de acciones
    lsCategorias()              // Lista todas las categorías
    addCategoria()              // Muestra formulario y agrega categoría
    deleteCategoria(id)         // Elimina una categoría
    jsonCategoriaForm()         // Define estructura del formulario
}
```

#### 1.3 AreasManager Class

Gestiona las operaciones CRUD de áreas.

```javascript
class AreasManager extends Templates {
    constructor(link, div_modulo)
    render()                    // Renderiza la sección de áreas
    layout()                    // Crea la estructura HTML
    filterBar()                 // Crea la barra de acciones
    lsAreas()                   // Lista todas las áreas
    addArea()                   // Muestra formulario y agrega área
    deleteArea(id)              // Elimina un área
    jsonAreaForm()              // Define estructura del formulario
}
```

#### 1.4 ZonasManager Class

Gestiona las operaciones CRUD de zonas.

```javascript
class ZonasManager extends Templates {
    constructor(link, div_modulo)
    render()                    // Renderiza la sección de zonas
    layout()                    // Crea la estructura HTML
    filterBar()                 // Crea la barra de acciones
    lsZonas()                   // Lista todas las zonas
    addZona()                   // Muestra formulario y agrega zona
    deleteZona(id)              // Elimina una zona
    jsonZonaForm()              // Define estructura del formulario
}
```

### 2. Backend Components

#### 2.1 Controller (ctrl-catalogo.php)

```php
class ctrl extends mdl {
    // Categorías
    function lsCategorias()         // Lista categorías con formato para tabla
    function addCategoria()         // Valida y crea nueva categoría
    function deleteCategoria()      // Elimina categoría con validaciones
    
    // Áreas
    function lsAreas()              // Lista áreas con formato para tabla
    function addArea()              // Valida y crea nueva área
    function deleteArea()           // Elimina área con validaciones
    
    // Zonas
    function lsZonas()              // Lista zonas con formato para tabla
    function addZona()              // Valida y crea nueva zona
    function deleteZona()           // Elimina zona con validaciones
    
    // Utilidades
    function init()                 // Inicializa datos necesarios
}
```

**Response Format:**
```json
{
    "status": 200,
    "message": "Operación exitosa",
    "data": {...},
    "row": [...]
}
```

#### 2.2 Model (mdl-catalogo.php)

```php
class mdl extends CRUD {
    protected $util;
    public $bd;
    
    // Categorías
    function listCategorias()                   // SELECT todas las categorías
    function getCategoriaById($id)              // SELECT categoría por ID
    function existsCategoria($nombre)           // Verifica si existe categoría
    function createCategoria($array)            // INSERT nueva categoría
    function deleteCategoriaById($id)           // DELETE categoría
    function countCategoriasUsage($id)          // Cuenta usos en mtto_almacen
    
    // Áreas
    function listAreas()                        // SELECT todas las áreas
    function getAreaById($id)                   // SELECT área por ID
    function existsArea($nombre)                // Verifica si existe área
    function createArea($array)                 // INSERT nueva área
    function deleteAreaById($id)                // DELETE área
    function countAreasUsage($id)               // Cuenta usos en mtto_almacen
    
    // Zonas
    function listZonas()                        // SELECT todas las zonas
    function getZonaById($id)                   // SELECT zona por ID
    function existsZona($nombre)                // Verifica si existe zona
    function createZona($array)                 // INSERT nueva zona
    function deleteZonaById($id)                // DELETE zona
    function countZonasUsage($id)               // Cuenta usos en mtto_almacen
}
```

### 3. View Component (catalogo.php)

Página principal que carga el módulo.

```php
<!DOCTYPE html>
<html>
<head>
    <!-- Meta tags, CSS includes -->
</head>
<body>
    <div id="root"></div>
    
    <!-- JavaScript includes -->
    <script src="ctrl/ctrl-catalogo.php"></script>
    <script src="js/catalogo.js"></script>
</body>
</html>
```

## Data Models

### Database Schema

#### Tabla: mtto_categoria (Existente)

```sql
CREATE TABLE IF NOT EXISTS mtto_categoria (
    idcategoria INT PRIMARY KEY AUTO_INCREMENT,
    nombreCategoria VARCHAR(255) NOT NULL,
    INDEX idx_nombre (nombreCategoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Campos:**
- `idcategoria`: Identificador único de la categoría
- `nombreCategoria`: Nombre descriptivo de la categoría

#### Tabla: mtto_almacen_area (Existente)

```sql
CREATE TABLE IF NOT EXISTS mtto_almacen_area (
    idArea INT PRIMARY KEY AUTO_INCREMENT,
    Nombre_Area VARCHAR(255) NOT NULL,
    INDEX idx_nombre (Nombre_Area)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Campos:**
- `idArea`: Identificador único del área
- `Nombre_Area`: Nombre descriptivo del área

#### Tabla: mtto_almacen_zona (Nueva)

```sql
CREATE TABLE IF NOT EXISTS mtto_almacen_zona (
    idZona INT PRIMARY KEY AUTO_INCREMENT,
    nombreZona VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombreZona)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Campos:**
- `idZona`: Identificador único de la zona
- `nombreZona`: Nombre descriptivo de la zona
- `created_at`: Fecha de creación del registro
- `updated_at`: Fecha de última actualización

### Data Transfer Objects (DTOs)

#### CategoriaDTO
```javascript
{
    id: number,
    nombre: string
}
```

#### AreaDTO
```javascript
{
    id: number,
    nombre: string
}
```

#### ZonaDTO
```javascript
{
    id: number,
    nombre: string,
    created_at: string,
    updated_at: string
}
```

### API Request/Response Formats

#### Request Format (Generic)
```json
{
    "opc": "lsCategorias|addCategoria|deleteCategoria|...",
    "id": 123,
    "nombreCategoria": "Herramientas",
    "Nombre_Area": "Almacén Principal",
    "nombreZona": "Zona A"
}
```

#### Response Format (List)
```json
{
    "status": 200,
    "message": "Datos obtenidos correctamente",
    "row": [
        {
            "ID": 1,
            "Nombre": "Categoría 1",
            "a": [
                {
                    "class": "btn btn-sm btn-danger",
                    "html": "<i class='icon-trash'></i>",
                    "onclick": "categorias.deleteCategoria(1)"
                }
            ]
        }
    ],
    "ls": [
        {
            "idcategoria": 1,
            "nombreCategoria": "Categoría 1"
        }
    ]
}
```

#### Response Format (Create/Update/Delete)
```json
{
    "status": 200,
    "message": "Operación exitosa"
}
```

#### Error Response Format
```json
{
    "status": 400|404|409|500,
    "message": "Descripción del error"
}
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*


### Property Reflection

After analyzing all testable criteria, I've identified several areas where properties can be consolidated:

**Redundancy Analysis:**
1. Properties 1.3, 2.3, and 3.3 (input validation) can be combined into a single comprehensive property about entity name validation
2. Properties 1.5, 2.4, and 3.4 (database insertion) can be combined into a property about CRUD round-trip consistency
3. Properties 1.6, 2.5, and 3.5 (UI update after insertion) can be combined into a property about UI-database synchronization
4. Properties 1.7, 2.6, and 3.6 (list rendering) can be combined into a property about complete data display
5. Properties 1.9, 2.9, and 3.8 (deletion) can be combined into a property about deletion consistency
6. Properties 1.10, 2.10, and 3.9 (UI update after deletion) are covered by the UI-database synchronization property
7. Property 2.7 (duplicate detection) is a specific case that should remain separate
8. Properties 4.3, 4.5, and 4.7 (tab behavior) can be combined into a property about SPA navigation consistency

**Consolidated Properties:**
- Entity name validation (covers 1.3, 2.3, 3.3)
- CRUD round-trip consistency (covers 1.5, 1.6, 2.4, 2.5, 3.4, 3.5, 1.10, 2.10, 3.9)
- Complete data rendering (covers 1.7, 2.6, 3.6)
- Deletion consistency (covers 1.9, 2.9, 3.8)
- Duplicate prevention (covers 2.7)
- SPA navigation consistency (covers 4.3, 4.5, 4.7)
- Security properties (covers 5.7, 7.1, 7.2, 7.3, 7.4, 7.5, 7.6)
- User feedback properties (covers 6.3, 6.4, 6.6, 6.7)

### Correctness Properties

Property 1: Entity name validation
*For any* entity type (category, area, or zone) and any input string, when attempting to add an entity, the system should reject empty or whitespace-only strings and accept non-empty strings with valid characters
**Validates: Requirements 1.3, 2.3, 3.3**

Property 2: CRUD round-trip consistency
*For any* entity type (category, area, or zone) and any valid entity name, after adding the entity to the system, querying the database should return an entity with the same name, and the UI list should display that entity
**Validates: Requirements 1.5, 1.6, 2.4, 2.5, 3.4, 3.5**

Property 3: Complete data rendering
*For any* set of entities (categories, areas, or zones) in the database, the rendered list should display all entities with their complete information (ID and name)
**Validates: Requirements 1.7, 2.6, 3.6**

Property 4: Deletion consistency
*For any* existing entity (category, area, or zone), after confirming deletion, the entity should no longer exist in the database and should not appear in the UI list
**Validates: Requirements 1.9, 1.10, 2.9, 2.10, 3.8, 3.9**

Property 5: Duplicate prevention
*For any* entity type (category, area, or zone) and any entity name that already exists in the database, attempting to add another entity with the same name (case-insensitive) should be rejected with an appropriate error message
**Validates: Requirements 2.7, 7.6**

Property 6: SPA navigation consistency
*For any* tab navigation action, the content should update without full page reload, the record count should match the database count, and the module header and tabs should remain visible
**Validates: Requirements 4.3, 4.5, 4.7**

Property 7: SQL injection prevention
*For any* input containing SQL injection attempts (e.g., "'; DROP TABLE--", "1' OR '1'='1"), the system should sanitize the input and prevent execution of malicious SQL
**Validates: Requirements 5.7, 7.2, 7.4**

Property 8: Session validation
*For any* HTTP request to the backend, if the user session is not active or has expired, the system should reject the request with an appropriate authentication error
**Validates: Requirements 7.1**

Property 9: Referential integrity protection
*For any* entity (category, area, or zone) that is referenced by records in the mtto_almacen table, attempting to delete that entity should be prevented with an error message indicating the entity is in use
**Validates: Requirements 7.3**

Property 10: User feedback consistency
*For any* successful operation (add, delete), the system should display a success message, and for any failed operation, the system should display an error message with appropriate details
**Validates: Requirements 6.3, 5.6, 7.5**

Property 11: Client-side validation
*For any* form submission, if required fields are empty or invalid, the form should not be submitted to the server and should display validation errors
**Validates: Requirements 6.6**

Property 12: Loading state indication
*For any* asynchronous operation (loading lists, adding, deleting), the system should display a loading indicator while the operation is in progress
**Validates: Requirements 6.7**

Property 13: Confirmation dialog for destructive actions
*For any* delete operation, before executing the deletion, the system should display a confirmation dialog requiring explicit user confirmation
**Validates: Requirements 6.4**

## Error Handling

### Error Categories

#### 1. Validation Errors (400 Bad Request)
- Empty or whitespace-only entity names
- Invalid characters in entity names
- Missing required fields
- Duplicate entity names

**Handling Strategy:**
- Validate on client-side before submission
- Re-validate on server-side
- Return descriptive error messages
- Display errors using SweetAlert2

**Example Response:**
```json
{
    "status": 400,
    "message": "El nombre de la categoría no puede estar vacío"
}
```

#### 2. Not Found Errors (404 Not Found)
- Attempting to delete non-existent entity
- Attempting to retrieve non-existent entity

**Handling Strategy:**
- Check entity existence before operations
- Return appropriate 404 status
- Log unexpected 404s for investigation

**Example Response:**
```json
{
    "status": 404,
    "message": "La categoría solicitada no existe"
}
```

#### 3. Conflict Errors (409 Conflict)
- Duplicate entity names
- Attempting to delete entity with dependencies

**Handling Strategy:**
- Check for duplicates before insertion
- Check for dependencies before deletion
- Return descriptive conflict messages

**Example Response:**
```json
{
    "status": 409,
    "message": "Ya existe una categoría con ese nombre"
}
```

#### 4. Authentication Errors (401 Unauthorized)
- Expired session
- Missing session
- Invalid session

**Handling Strategy:**
- Validate session on every request
- Redirect to login page
- Clear client-side state

**Example Response:**
```json
{
    "status": 401,
    "message": "Sesión expirada. Por favor, inicie sesión nuevamente"
}
```

#### 5. Server Errors (500 Internal Server Error)
- Database connection failures
- Unexpected exceptions
- Query execution errors

**Handling Strategy:**
- Log full error details server-side
- Return generic error message to client
- Alert system administrators for critical errors

**Example Response:**
```json
{
    "status": 500,
    "message": "Error del servidor. Por favor, intente nuevamente"
}
```

### Error Logging

All errors should be logged using PHP's error_log() function:

```php
try {
    // Operation
} catch (PDOException $e) {
    $message = "[CATALOGO] Error en operación: " . $e->getMessage();
    $message .= "\nQuery: " . $query;
    $message .= "\nData: " . json_encode($data);
    error_log($message);
    
    return [
        'status' => 500,
        'message' => 'Error del servidor'
    ];
}
```

### Client-Side Error Handling

```javascript
async function handleOperation(operation) {
    try {
        const response = await useFetch({
            url: api,
            data: operation
        });
        
        if (response.status === 200) {
            // Success handling
            alert({
                icon: "success",
                text: response.message,
                btn1: true
            });
        } else {
            // Error handling
            alert({
                icon: "error",
                title: "Error",
                text: response.message,
                btn1: true
            });
        }
    } catch (error) {
        // Network or unexpected errors
        alert({
            icon: "error",
            title: "Error de conexión",
            text: "No se pudo conectar con el servidor",
            btn1: true
        });
    }
}
```

## Testing Strategy

### Unit Testing

Unit tests will verify specific functionality of individual components:

**Backend Unit Tests (PHP):**
1. **Model Layer Tests:**
   - Test each CRUD operation independently
   - Test duplicate detection logic
   - Test dependency checking logic
   - Test SQL query generation

2. **Controller Layer Tests:**
   - Test request validation
   - Test response formatting
   - Test error handling paths
   - Test session validation

**Frontend Unit Tests (JavaScript):**
1. **Form Validation Tests:**
   - Test empty input rejection
   - Test whitespace-only input rejection
   - Test valid input acceptance

2. **UI Component Tests:**
   - Test tab switching functionality
   - Test table rendering with sample data
   - Test button click handlers

3. **API Integration Tests:**
   - Test successful API responses
   - Test error API responses
   - Test network failure handling

### Property-Based Testing

Property-based tests will verify universal properties across many randomly generated inputs using a JavaScript PBT library (fast-check for JavaScript, or PHPUnit with property testing extensions for PHP).

**Configuration:**
- Minimum 100 iterations per property test
- Use fast-check library for JavaScript tests
- Each property test must reference its design document property

**Property Test Implementation:**

1. **Property 1: Entity name validation**
   - Generate random strings (empty, whitespace, valid, special characters)
   - Verify validation behavior matches specification
   - **Feature: catalogo-almacen, Property 1: Entity name validation**

2. **Property 2: CRUD round-trip consistency**
   - Generate random valid entity names
   - Add entity, query database, verify presence
   - Verify UI list contains entity
   - **Feature: catalogo-almacen, Property 2: CRUD round-trip consistency**

3. **Property 3: Complete data rendering**
   - Generate random sets of entities
   - Insert into database
   - Verify all entities appear in rendered list
   - **Feature: catalogo-almacen, Property 3: Complete data rendering**

4. **Property 4: Deletion consistency**
   - Generate random entities
   - Add to database
   - Delete and verify removal from database and UI
   - **Feature: catalogo-almacen, Property 4: Deletion consistency**

5. **Property 5: Duplicate prevention**
   - Generate random entity names
   - Add entity
   - Attempt to add duplicate (with case variations)
   - Verify rejection
   - **Feature: catalogo-almacen, Property 5: Duplicate prevention**

6. **Property 6: SPA navigation consistency**
   - Simulate tab clicks
   - Verify no page reload
   - Verify counts match database
   - **Feature: catalogo-almacen, Property 6: SPA navigation consistency**

7. **Property 7: SQL injection prevention**
   - Generate SQL injection payloads
   - Attempt operations with malicious inputs
   - Verify sanitization and prevention
   - **Feature: catalogo-almacen, Property 7: SQL injection prevention**

8. **Property 8: Session validation**
   - Generate requests with and without valid sessions
   - Verify authentication enforcement
   - **Feature: catalogo-almacen, Property 8: Session validation**

9. **Property 9: Referential integrity protection**
   - Create entities with dependencies
   - Attempt deletion
   - Verify prevention
   - **Feature: catalogo-almacen, Property 9: Referential integrity protection**

10. **Property 10: User feedback consistency**
    - Perform various operations (success and failure)
    - Verify appropriate messages displayed
    - **Feature: catalogo-almacen, Property 10: User feedback consistency**

11. **Property 11: Client-side validation**
    - Generate invalid form inputs
    - Attempt submission
    - Verify client-side prevention
    - **Feature: catalogo-almacen, Property 11: Client-side validation**

12. **Property 12: Loading state indication**
    - Trigger async operations
    - Verify loading indicators appear
    - **Feature: catalogo-almacen, Property 12: Loading state indication**

13. **Property 13: Confirmation dialog for destructive actions**
    - Trigger delete operations
    - Verify confirmation dialog appears
    - **Feature: catalogo-almacen, Property 13: Confirmation dialog for destructive actions**

### Integration Testing

Integration tests will verify the interaction between components:

1. **End-to-End Workflow Tests:**
   - Complete add-list-delete cycle for each entity type
   - Tab navigation with data persistence
   - Error recovery scenarios

2. **Database Integration Tests:**
   - Verify CRUD operations against real database
   - Test transaction rollback on errors
   - Test concurrent operations

3. **API Integration Tests:**
   - Test complete request-response cycles
   - Test error responses
   - Test session handling

### Test Environment Setup

**Requirements:**
- PHP 7.4+ with PDO MySQL extension
- MySQL 5.7+ or MariaDB 10.3+
- Node.js 14+ for JavaScript testing
- fast-check library for property-based testing

**Database Setup:**
```sql
-- Create test database
CREATE DATABASE IF NOT EXISTS erp3_test;

-- Create test tables
USE erp3_test;
-- (Include table creation scripts)
```

**Test Execution:**
```bash
# PHP Unit Tests
phpunit tests/

# JavaScript Tests
npm test

# Property-Based Tests
npm run test:properties
```

## Security Considerations

### Input Sanitization

All user inputs must be sanitized using the Utileria class:

```php
$sanitized = $this->util->sql($_POST);
```

### SQL Injection Prevention

Use prepared statements for all database queries:

```php
// Good - Using prepared statements
$this->_Insert([
    'table' => 'mtto_categoria',
    'values' => 'nombreCategoria',
    'data' => [$nombre]
]);

// Bad - Direct string concatenation (NEVER DO THIS)
$query = "INSERT INTO mtto_categoria (nombreCategoria) VALUES ('$nombre')";
```

### Session Management

Validate session on every request:

```php
session_start();
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode([
        'status' => 401,
        'message' => 'Sesión no válida'
    ]);
    exit;
}
```

### CORS Headers

Include appropriate CORS headers:

```php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
```

### XSS Prevention

Escape output when rendering user-generated content:

```javascript
// Use text() instead of html() for user content
$('#element').text(userInput);

// Or escape HTML entities
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}
```

### Error Message Security

Never expose sensitive information in error messages:

```php
// Good - Generic error message
return [
    'status' => 500,
    'message' => 'Error del servidor'
];

// Bad - Exposing database details
return [
    'status' => 500,
    'message' => 'MySQL Error: Table mtto_categoria not found in database erp3_production'
];
```

## Performance Considerations

### Database Optimization

1. **Indexes:**
   - Add indexes on frequently queried columns (nombreCategoria, Nombre_Area, nombreZona)
   - Use composite indexes for multi-column queries

2. **Query Optimization:**
   - Use SELECT with specific columns instead of SELECT *
   - Limit result sets when appropriate
   - Use JOIN instead of multiple queries

3. **Connection Pooling:**
   - Reuse database connections
   - Close connections properly

### Frontend Optimization

1. **Lazy Loading:**
   - Load data only when tab is activated
   - Implement pagination for large datasets

2. **Caching:**
   - Cache frequently accessed data
   - Invalidate cache on updates

3. **Debouncing:**
   - Debounce search inputs
   - Throttle rapid API calls

### Monitoring

1. **Performance Metrics:**
   - Track API response times
   - Monitor database query performance
   - Log slow queries (> 1 second)

2. **Error Tracking:**
   - Log all errors with context
   - Monitor error rates
   - Alert on critical errors

## Deployment Considerations

### File Structure

```
ERP3/operacion/catalogo/
├── catalogo.php              # Main view file
├── ctrl/
│   └── ctrl-catalogo.php     # Controller
├── mdl/
│   └── mdl-catalogo.php      # Model
├── js/
│   └── catalogo.js           # Frontend logic
└── sql/
    └── schema.sql            # Database schema
```

### Database Migration

Create migration script for new zona table:

```sql
-- sql/schema.sql
CREATE TABLE IF NOT EXISTS mtto_almacen_zona (
    idZona INT PRIMARY KEY AUTO_INCREMENT,
    nombreZona VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre (nombreZona)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Configuration

Update configuration files if needed:

```php
// conf/_conf.php
define('DB_NAME', 'rfwsmqex_gvsl_finanzas2');
define('DB_PREFIX', 'mtto_');
```

### Access Control

Ensure proper permissions are set:

```php
// Check user role/permissions
if (!hasPermission($_SESSION['usuario'], 'catalogo_manage')) {
    http_response_code(403);
    echo json_encode([
        'status' => 403,
        'message' => 'No tiene permisos para esta operación'
    ]);
    exit;
}
```

### Backup Strategy

1. **Before Deployment:**
   - Backup existing database
   - Backup existing files

2. **After Deployment:**
   - Verify all functionality
   - Test rollback procedure

### Rollback Plan

1. Restore database from backup
2. Restore previous file versions
3. Clear application cache
4. Verify system functionality
