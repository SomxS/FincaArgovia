# Design Document - Módulo de Clientes

## Overview

El módulo de Clientes es una aplicación web desarrollada con el framework CoffeeSoft que gestiona créditos de clientes en múltiples unidades de negocio. Utiliza arquitectura MVC con PHP en el backend y jQuery + TailwindCSS en el frontend.

### Objetivos del Diseño

- Proporcionar interfaz intuitiva para gestión de movimientos de crédito
- Mantener sincronización en tiempo real con el módulo de Ventas
- Implementar sistema de permisos por niveles de usuario
- Garantizar integridad de datos financieros
- Facilitar consultas y reportes con exportación a Excel

### Tecnologías Utilizadas

- **Frontend**: jQuery, TailwindCSS, CoffeeSoft Framework
- **Backend**: PHP 7.4+, MySQL
- **Componentes**: Templates, Components, Complements (CoffeeSoft)
- **Librerías**: Moment.js, DataTables, SweetAlert2, Bootbox

## Architecture

### Arquitectura General

```
┌─────────────────────────────────────────────────────────────┐
│                    MÓDULO DE CLIENTES                        │
├─────────────────────────────────────────────────────────────┤
│  Frontend (JavaScript)                                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   App        │  │  Dashboard   │  │ Concentrado  │      │
│  │  (Main)      │  │  (Captura)   │  │  (Reportes)  │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
├─────────────────────────────────────────────────────────────┤
│  Backend (PHP)                                               │
│  ┌──────────────┐  ┌──────────────┐                        │
│  │ ctrl-clientes│  │ mdl-clientes │                        │
│  │ (Controller) │  │   (Model)    │                        │
│  └──────────────┘  └──────────────┘                        │
├─────────────────────────────────────────────────────────────┤
│  Database (MySQL)                                            │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   clientes   │  │  movimientos │  │  audit_log   │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
└─────────────────────────────────────────────────────────────┘
```

### Flujo de Datos

1. Usuario interactúa con interfaz (clientes.js)
2. Petición AJAX a controlador (ctrl-clientes.php)
3. Controlador valida y procesa datos
4. Modelo ejecuta operaciones en base de datos (mdl-clientes.php)
5. Respuesta JSON regresa al frontend
6. Interfaz actualiza componentes visuales


## Components and Interfaces

### Frontend Components

#### 1. App (Clase Principal)
```javascript
class App extends Templates {
    constructor(link, div_modulo)
    PROJECT_NAME: "clientes"
    
    Methods:
    - render(): Inicializa el módulo
    - layout(): Crea estructura con primaryLayout
    - filterBar(): Barra de filtros con fecha y tipo de movimiento
}
```

#### 2. Dashboard (Gestión Diaria)
```javascript
class Dashboard extends App {
    Methods:
    - ls(): Lista movimientos del día
    - addMovimiento(): Modal para nuevo movimiento
    - editMovimiento(id): Modal para editar movimiento
    - deleteMovimiento(id): Confirmación y eliminación
    - viewDetalle(id): Modal con detalle completo
    - updateTotales(): Actualiza tarjetas de totales
    - jsonMovimiento(): Estructura del formulario
}
```

#### 3. Concentrado (Reportes)
```javascript
class Concentrado extends App {
    Methods:
    - lsConcentrado(): Tabla de balances por cliente
    - filterByDateRange(): Filtro de rango de fechas
    - exportExcel(): Genera archivo Excel
    - expandCliente(id): Muestra detalle de movimientos
}
```

### Backend Components

#### 1. Controller (ctrl-clientes.php)
```php
class ctrl extends mdl {
    Methods:
    - init(): Inicializa filtros (clientes, tipos, métodos)
    - ls(): Lista movimientos con filtros
    - getMovimiento(): Obtiene movimiento por ID
    - addMovimiento(): Crea nuevo movimiento
    - editMovimiento(): Actualiza movimiento existente
    - deleteMovimiento(): Elimina movimiento (soft delete)
    - lsConcentrado(): Genera reporte de balances
    - exportExcel(): Prepara datos para exportación
}
```

#### 2. Model (mdl-clientes.php)
```php
class mdl extends CRUD {
    Methods:
    - listMovimientos($params): Consulta movimientos
    - getMovimientoById($id): Obtiene movimiento específico
    - createMovimiento($data): Inserta nuevo movimiento
    - updateMovimiento($data): Actualiza movimiento
    - deleteMovimientoById($id): Elimina movimiento
    - lsClientes(): Lista clientes activos
    - getDeudaActual($clienteId): Calcula deuda actual
    - listConcentrado($params): Genera datos de concentrado
    - logAuditoria($data): Registra acciones en log
}
```


## Data Models

### Database Schema

#### Tabla: clientes
```sql
CREATE TABLE clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    udn_id INT NOT NULL,
    deuda_inicial DECIMAL(10,2) DEFAULT 0.00,
    active TINYINT(1) DEFAULT 1,
    date_create DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (udn_id) REFERENCES udn(id)
);
```

#### Tabla: movimientos_credito
```sql
CREATE TABLE movimientos_credito (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cliente_id INT NOT NULL,
    udn_id INT NOT NULL,
    tipo_movimiento ENUM('consumo', 'abono_parcial', 'pago_total') NOT NULL,
    metodo_pago ENUM('N/A', 'efectivo', 'banco') NOT NULL,
    cantidad DECIMAL(10,2) NOT NULL,
    descripcion TEXT,
    deuda_anterior DECIMAL(10,2) NOT NULL,
    deuda_nueva DECIMAL(10,2) NOT NULL,
    fecha_captura DATE NOT NULL,
    hora_captura TIME NOT NULL,
    usuario_id INT NOT NULL,
    active TINYINT(1) DEFAULT 1,
    date_create DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_update DATETIME ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (udn_id) REFERENCES udn(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
```

#### Tabla: audit_log_clientes
```sql
CREATE TABLE audit_log_clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    movimiento_id INT,
    cliente_id INT NOT NULL,
    accion ENUM('create', 'update', 'delete') NOT NULL,
    usuario_id INT NOT NULL,
    fecha_accion DATETIME DEFAULT CURRENT_TIMESTAMP,
    datos_anteriores JSON,
    datos_nuevos JSON,
    FOREIGN KEY (movimiento_id) REFERENCES movimientos_credito(id),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
```

### Data Transfer Objects

#### MovimientoDTO
```javascript
{
    id: number,
    cliente_id: number,
    cliente_nombre: string,
    tipo_movimiento: string,
    metodo_pago: string,
    cantidad: number,
    descripcion: string,
    deuda_anterior: number,
    deuda_nueva: number,
    fecha_captura: string,
    hora_captura: string,
    usuario_nombre: string
}
```

#### ConcentradoDTO
```javascript
{
    cliente_id: number,
    cliente_nombre: string,
    saldo_inicial: number,
    consumos: {
        [fecha: string]: number
    },
    pagos: {
        [fecha: string]: number
    },
    saldo_final: number
}
```


## Error Handling

### Frontend Error Handling

#### Validación de Formularios
```javascript
// Validación automática con CoffeeSoft
this.createModalForm({
    json: [...],
    success: (response) => {
        if (response.status === 200) {
            alert({ icon: "success", text: response.message });
            this.ls();
        } else {
            alert({ icon: "error", text: response.message });
        }
    }
});
```

#### Manejo de Errores AJAX
```javascript
try {
    const data = await useFetch({
        url: api,
        data: { opc: 'addMovimiento', ...formData }
    });
    
    if (data.status !== 200) {
        throw new Error(data.message);
    }
} catch (error) {
    alert({
        icon: "error",
        title: "Error",
        text: error.message || "Ocurrió un error inesperado"
    });
}
```

### Backend Error Handling

#### Validación de Datos
```php
function addMovimiento() {
    $status = 500;
    $message = 'Error al registrar movimiento';
    
    // Validar campos obligatorios
    if (empty($_POST['cliente_id']) || empty($_POST['tipo_movimiento'])) {
        return [
            'status' => 400,
            'message' => 'Todos los campos son obligatorios'
        ];
    }
    
    // Validar tipo de movimiento y método de pago
    if ($_POST['tipo_movimiento'] === 'consumo' && $_POST['metodo_pago'] !== 'N/A') {
        return [
            'status' => 400,
            'message' => 'El tipo consumo debe tener método de pago N/A'
        ];
    }
    
    try {
        $create = $this->createMovimiento($this->util->sql($_POST));
        if ($create) {
            $status = 200;
            $message = 'Movimiento registrado correctamente';
        }
    } catch (Exception $e) {
        $status = 500;
        $message = 'Error en la base de datos: ' . $e->getMessage();
    }
    
    return [
        'status' => $status,
        'message' => $message
    ];
}
```

#### Manejo de Transacciones
```php
function deleteMovimiento() {
    try {
        $this->_conn->beginTransaction();
        
        // Registrar en log de auditoría
        $this->logAuditoria([
            'movimiento_id' => $_POST['id'],
            'accion' => 'delete',
            'usuario_id' => $_SESSION['usuario_id']
        ]);
        
        // Eliminar movimiento (soft delete)
        $delete = $this->deleteMovimientoById([$_POST['id']]);
        
        $this->_conn->commit();
        
        return [
            'status' => 200,
            'message' => 'Movimiento eliminado correctamente'
        ];
    } catch (Exception $e) {
        $this->_conn->rollBack();
        return [
            'status' => 500,
            'message' => 'Error al eliminar: ' . $e->getMessage()
        ];
    }
}
```

### Error Messages

#### Códigos de Estado HTTP
- **200**: Operación exitosa
- **400**: Datos inválidos o faltantes
- **401**: No autorizado (permisos insuficientes)
- **404**: Recurso no encontrado
- **409**: Conflicto (registro duplicado)
- **500**: Error interno del servidor

#### Mensajes de Usuario
```javascript
const ERROR_MESSAGES = {
    CAMPOS_OBLIGATORIOS: 'Todos los campos son obligatorios',
    MOVIMIENTO_NO_ENCONTRADO: 'El movimiento no existe',
    PERMISOS_INSUFICIENTES: 'No tiene permisos para realizar esta acción',
    ERROR_CONEXION: 'Error de conexión con el servidor',
    ERROR_DESCONOCIDO: 'Ocurrió un error inesperado'
};
```


## Testing Strategy

### Unit Testing

#### Frontend Tests
```javascript
// Test: Validación de tipo de movimiento
describe('Dashboard.addMovimiento', () => {
    it('debe deshabilitar método de pago cuando tipo es consumo', () => {
        const tipoMovimiento = 'consumo';
        const metodoPago = $('#metodo_pago');
        
        $('#tipo_movimiento').val(tipoMovimiento).trigger('change');
        
        expect(metodoPago.val()).toBe('N/A');
        expect(metodoPago.prop('disabled')).toBe(true);
    });
    
    it('debe habilitar método de pago cuando tipo es abono o pago', () => {
        const tipoMovimiento = 'abono_parcial';
        const metodoPago = $('#metodo_pago');
        
        $('#tipo_movimiento').val(tipoMovimiento).trigger('change');
        
        expect(metodoPago.prop('disabled')).toBe(false);
    });
});

// Test: Cálculo de nueva deuda
describe('Dashboard.calcularNuevaDeuda', () => {
    it('debe sumar cantidad a deuda cuando es consumo', () => {
        const deudaActual = 1500.00;
        const cantidad = 782.00;
        const tipo = 'consumo';
        
        const nuevaDeuda = calcularNuevaDeuda(deudaActual, cantidad, tipo);
        
        expect(nuevaDeuda).toBe(2282.00);
    });
    
    it('debe restar cantidad a deuda cuando es pago', () => {
        const deudaActual = 1500.00;
        const cantidad = 500.00;
        const tipo = 'pago_total';
        
        const nuevaDeuda = calcularNuevaDeuda(deudaActual, cantidad, tipo);
        
        expect(nuevaDeuda).toBe(1000.00);
    });
});
```

#### Backend Tests
```php
// Test: Validación de movimientos
class CtrlClientesTest extends PHPUnit\Framework\TestCase {
    public function testAddMovimientoConsumo() {
        $_POST = [
            'cliente_id' => 1,
            'tipo_movimiento' => 'consumo',
            'metodo_pago' => 'N/A',
            'cantidad' => 500.00
        ];
        
        $ctrl = new ctrl();
        $result = $ctrl->addMovimiento();
        
        $this->assertEquals(200, $result['status']);
    }
    
    public function testAddMovimientoInvalido() {
        $_POST = [
            'cliente_id' => 1,
            'tipo_movimiento' => 'consumo',
            'metodo_pago' => 'efectivo', // Inválido para consumo
            'cantidad' => 500.00
        ];
        
        $ctrl = new ctrl();
        $result = $ctrl->addMovimiento();
        
        $this->assertEquals(400, $result['status']);
    }
}
```

### Integration Testing

#### Test de Flujo Completo
```javascript
describe('Flujo completo de movimiento', () => {
    it('debe registrar, editar y eliminar un movimiento', async () => {
        // 1. Registrar movimiento
        const nuevoMovimiento = {
            cliente_id: 1,
            tipo_movimiento: 'consumo',
            metodo_pago: 'N/A',
            cantidad: 500.00
        };
        
        const responseAdd = await useFetch({
            url: api,
            data: { opc: 'addMovimiento', ...nuevoMovimiento }
        });
        
        expect(responseAdd.status).toBe(200);
        const movimientoId = responseAdd.id;
        
        // 2. Editar movimiento
        const responseEdit = await useFetch({
            url: api,
            data: { 
                opc: 'editMovimiento', 
                id: movimientoId,
                cantidad: 600.00
            }
        });
        
        expect(responseEdit.status).toBe(200);
        
        // 3. Eliminar movimiento
        const responseDelete = await useFetch({
            url: api,
            data: { opc: 'deleteMovimiento', id: movimientoId }
        });
        
        expect(responseDelete.status).toBe(200);
    });
});
```

### Performance Testing

#### Métricas Objetivo
- Tiempo de carga inicial: < 2 segundos
- Tiempo de respuesta AJAX: < 500ms
- Renderizado de tabla (100 registros): < 1 segundo
- Exportación a Excel (1000 registros): < 3 segundos

#### Test de Carga
```javascript
// Simular 50 usuarios concurrentes
describe('Performance Tests', () => {
    it('debe manejar 50 peticiones concurrentes', async () => {
        const promises = [];
        
        for (let i = 0; i < 50; i++) {
            promises.push(
                useFetch({
                    url: api,
                    data: { opc: 'ls', fecha: '2025-01-12' }
                })
            );
        }
        
        const startTime = Date.now();
        await Promise.all(promises);
        const endTime = Date.now();
        
        const totalTime = endTime - startTime;
        expect(totalTime).toBeLessThan(5000); // < 5 segundos
    });
});
```

### User Acceptance Testing

#### Escenarios de Prueba
1. **Registro de Consumo**
   - Usuario selecciona cliente
   - Ingresa cantidad
   - Verifica que método de pago sea N/A
   - Guarda y verifica actualización de tabla

2. **Registro de Pago**
   - Usuario selecciona cliente
   - Selecciona tipo "Pago total"
   - Selecciona método "Efectivo"
   - Verifica cálculo de nueva deuda
   - Guarda y verifica actualización

3. **Consulta de Concentrado**
   - Usuario selecciona rango de fechas
   - Verifica datos por cliente
   - Expande detalle de cliente
   - Exporta a Excel
   - Verifica archivo descargado


## Security Considerations

### Authentication & Authorization

#### Sistema de Niveles de Acceso
```php
// Verificación de nivel de usuario
function checkUserLevel($requiredLevel) {
    $userLevel = $_SESSION['nivel_usuario'];
    
    if ($userLevel < $requiredLevel) {
        return [
            'status' => 401,
            'message' => 'No tiene permisos para realizar esta acción'
        ];
    }
    
    return true;
}

// Aplicación en controlador
function deleteMovimiento() {
    $check = $this->checkUserLevel(1); // Nivel 1 mínimo
    if ($check !== true) return $check;
    
    // Continuar con la operación
}
```

#### Matriz de Permisos
```javascript
const PERMISOS = {
    NIVEL_1_CAPTURA: {
        ver_dashboard: true,
        registrar_movimiento: true,
        editar_movimiento: true,
        eliminar_movimiento: true,
        ver_concentrado: false,
        exportar_excel: false,
        gestionar_clientes: false
    },
    NIVEL_2_GERENCIA: {
        ver_dashboard: true,
        registrar_movimiento: false,
        editar_movimiento: false,
        eliminar_movimiento: false,
        ver_concentrado: true,
        exportar_excel: true,
        gestionar_clientes: false
    },
    NIVEL_3_CONTABILIDAD: {
        ver_dashboard: true,
        registrar_movimiento: false,
        editar_movimiento: false,
        eliminar_movimiento: false,
        ver_concentrado: true,
        exportar_excel: true,
        gestionar_clientes: false,
        filtrar_udn: true
    },
    NIVEL_4_ADMIN: {
        ver_dashboard: true,
        registrar_movimiento: true,
        editar_movimiento: true,
        eliminar_movimiento: true,
        ver_concentrado: true,
        exportar_excel: true,
        gestionar_clientes: true,
        bloquear_modulo: true
    }
};
```

### Input Validation

#### Sanitización de Datos
```php
// Uso de utilería para sanitizar
$data = $this->util->sql($_POST);

// Validación adicional
function validateMovimiento($data) {
    $errors = [];
    
    // Validar cliente_id
    if (!is_numeric($data['cliente_id']) || $data['cliente_id'] <= 0) {
        $errors[] = 'ID de cliente inválido';
    }
    
    // Validar cantidad
    if (!is_numeric($data['cantidad']) || $data['cantidad'] <= 0) {
        $errors[] = 'La cantidad debe ser mayor a 0';
    }
    
    // Validar tipo de movimiento
    $tiposValidos = ['consumo', 'abono_parcial', 'pago_total'];
    if (!in_array($data['tipo_movimiento'], $tiposValidos)) {
        $errors[] = 'Tipo de movimiento inválido';
    }
    
    // Validar método de pago según tipo
    if ($data['tipo_movimiento'] === 'consumo' && $data['metodo_pago'] !== 'N/A') {
        $errors[] = 'El consumo debe tener método de pago N/A';
    }
    
    return $errors;
}
```

#### Prevención de SQL Injection
```php
// Uso de prepared statements en CRUD
function listMovimientos($params) {
    return $this->_Select([
        'table' => $this->bd . 'movimientos_credito',
        'values' => '*',
        'where' => 'fecha_captura = ? AND udn_id = ? AND active = 1',
        'data' => [$params['fecha'], $params['udn_id']]
    ]);
}
```

### XSS Prevention

#### Escape de Datos en Frontend
```javascript
// Función para escapar HTML
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

// Uso en renderizado
const clienteNombre = escapeHtml(data.cliente_nombre);
$('#cliente-nombre').text(clienteNombre);
```

### CSRF Protection

#### Token CSRF en Formularios
```php
// Generar token
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Validar token
function validateCsrfToken() {
    if (!isset($_POST['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        return [
            'status' => 403,
            'message' => 'Token CSRF inválido'
        ];
    }
    return true;
}
```

### Audit Logging

#### Registro de Acciones Críticas
```php
function logAuditoria($data) {
    $logData = [
        'movimiento_id' => $data['movimiento_id'],
        'cliente_id' => $data['cliente_id'],
        'accion' => $data['accion'],
        'usuario_id' => $_SESSION['usuario_id'],
        'fecha_accion' => date('Y-m-d H:i:s'),
        'datos_anteriores' => json_encode($data['datos_anteriores'] ?? null),
        'datos_nuevos' => json_encode($data['datos_nuevos'] ?? null)
    ];
    
    return $this->_Insert([
        'table' => $this->bd . 'audit_log_clientes',
        'values' => implode(',', array_keys($logData)),
        'data' => array_values($logData)
    ]);
}
```

## Performance Optimization

### Database Optimization

#### Índices Recomendados
```sql
-- Índice para búsquedas por fecha y UDN
CREATE INDEX idx_movimientos_fecha_udn 
ON movimientos_credito(fecha_captura, udn_id, active);

-- Índice para búsquedas por cliente
CREATE INDEX idx_movimientos_cliente 
ON movimientos_credito(cliente_id, active);

-- Índice para auditoría
CREATE INDEX idx_audit_fecha 
ON audit_log_clientes(fecha_accion);
```

#### Query Optimization
```php
// Usar LEFT JOIN para evitar múltiples consultas
function listMovimientos($params) {
    return $this->_Select([
        'table' => $this->bd . 'movimientos_credito',
        'values' => 
            "movimientos_credito.*,
            clientes.name as cliente_nombre,
            usuarios.nombre as usuario_nombre",
        'leftjoin' => [
            $this->bd . 'clientes' => 'movimientos_credito.cliente_id = clientes.id',
            $this->bd . 'usuarios' => 'movimientos_credito.usuario_id = usuarios.id'
        ],
        'where' => 'movimientos_credito.fecha_captura = ? AND movimientos_credito.active = 1',
        'data' => [$params['fecha']]
    ]);
}
```

### Frontend Optimization

#### Lazy Loading de Tablas
```javascript
// Cargar datos solo cuando sea necesario
ls() {
    if (this.dataCache && this.dataCache.fecha === this.currentDate) {
        this.renderTable(this.dataCache.data);
        return;
    }
    
    // Cargar datos del servidor
    this.fetchData();
}
```

#### Debouncing de Búsquedas
```javascript
// Evitar múltiples peticiones en búsquedas
let searchTimeout;
$('#search-input').on('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        app.ls();
    }, 300);
});
```

