# 📦 Módulo de Compras - CoffeeSoft

## 🎯 Descripción General

El módulo de Compras permite capturar, consultar y administrar las diferentes compras realizadas por la unidad de negocio, aplicando filtros dinámicos según clase de insumo, tipo de compra, proveedor y forma de pago.

## 📁 Estructura de Archivos

```
finanzas/captura/
├── compras.php                    # Vista principal
├── js/
│   └── compras.js                 # Frontend (jQuery + CoffeeSoft)
├── ctrl/
│   └── ctrl-compras.php           # Controlador PHP
└── mdl/
    └── mdl-compras.php            # Modelo PHP
```

## 🗄️ Estructura de Base de Datos

### Tablas Principales

1. **product_class** - Categorías de productos
   - id, udn_id, name, description, active

2. **product** - Productos
   - id, product_class_id, name, active

3. **purchase_type** - Tipos de compra
   - id, name, active
   - Valores: Fondo fijo, Corporativo, Crédito

4. **supplier** - Proveedores
   - id, udn_id, name, active

5. **method_pay** - Métodos de pago
   - id, name, active

6. **purchase** - Compras
   - id, udn_id, product_class_id, product_id
   - purchase_type_id, supplier_id, method_pay_id
   - subtotal, tax, total, description
   - operation_date, active

## 🎨 Características Principales

### Dashboard de Compras

- ✅ Visualización de compras diarias
- ✅ Filtros por tipo de compra y método de pago
- ✅ Tarjetas con totales:
  - Total de compras
  - Compras fondo fijo
  - Compras corporativo
  - Compras a crédito

### Registro de Compras

**Campos del formulario:**
- Categoría de producto (select dinámico)
- Producto (carga según categoría)
- Tipo de compra (Fondo fijo, Corporativo, Crédito)
- Proveedor (solo para crédito)
- Método de pago (solo para corporativo)
- Subtotal
- Impuesto
- Total (calculado automáticamente)
- Descripción

**Lógica de campos:**
- **Fondo fijo**: Oculta método de pago y proveedor
- **Corporativo**: Muestra método de pago, oculta proveedor
- **Crédito**: Muestra proveedor, oculta método de pago

### Concentrado de Compras

- ✅ Vista por rango de fechas
- ✅ Agrupación por clase de producto
- ✅ Totales por día y por clase
- ✅ Tarjetas con:
  - Saldo inicial fondo fijo
  - Total compras
  - Salidas fondo fijo
  - Saldo final fondo fijo

## 👥 Niveles de Acceso

### Nivel 1 - Captura
- ✅ Ver dashboard
- ✅ Registrar compra
- ✅ Editar compra
- ✅ Eliminar compra
- ❌ Ver concentrado
- ❌ Exportar Excel

### Nivel 2 - Gerencia
- ✅ Ver dashboard
- ✅ Ver concentrado
- ✅ Exportar Excel
- ❌ Registrar/Editar/Eliminar

### Nivel 3 - Dirección
- ✅ Ver dashboard
- ✅ Ver concentrado
- ✅ Exportar Excel
- ✅ Filtrar por UDN
- ❌ Registrar/Editar/Eliminar

### Nivel 4 - Contabilidad
- ✅ Todos los permisos
- ✅ Gestionar productos
- ✅ Bloquear/Desbloquear módulo

## 🔧 Métodos Principales

### Frontend (compras.js)

**Clase App:**
- `render()` - Renderiza layout principal
- `layout()` - Crea estructura con tabs
- `filterBar()` - Barra de filtros con calendario
- `renderDaily()` - Actualiza vista según fecha
- `checkPermiso(accion)` - Valida permisos de usuario

**Clase Compras:**
- `render()` - Renderiza dashboard de compras
- `lsCompras()` - Lista compras del día
- `addCompra()` - Modal para nueva compra
- `editCompra(id)` - Modal para editar compra
- `deleteCompra(id)` - Elimina compra con confirmación
- `viewDetalle(id)` - Muestra detalle de compra
- `updateTotales()` - Actualiza tarjetas de totales
- `setupCompraLogic()` - Configura lógica de formulario

**Clase Concentrado:**
- `render()` - Renderiza concentrado
- `lsConcentrado()` - Genera tabla de concentrado
- `updateTotalesConcentrado()` - Actualiza totales del periodo

### Backend (ctrl-compras.php)

- `init()` - Inicializa datos para filtros
- `ls()` - Lista compras con filtros
- `getCompra()` - Obtiene compra por ID
- `addCompra()` - Registra nueva compra
- `editCompra()` - Edita compra existente
- `deleteCompra()` - Elimina compra (soft delete)
- `getTotales()` - Obtiene totales del día
- `getTotalesConcentrado()` - Obtiene totales del periodo
- `lsConcentrado()` - Genera datos para concentrado
- `getProductsByClass()` - Obtiene productos por categoría

### Modelo (mdl-compras.php)

- `lsProductClass()` - Lista categorías de productos
- `lsProduct()` - Lista productos
- `lsProductByClass()` - Lista productos por categoría
- `lsPurchaseType()` - Lista tipos de compra
- `lsSupplier()` - Lista proveedores
- `lsMethodPay()` - Lista métodos de pago
- `listCompras()` - Lista compras con joins
- `getCompraById()` - Obtiene compra completa
- `createCompra()` - Inserta nueva compra
- `updateCompra()` - Actualiza compra
- `deleteCompraById()` - Elimina compra
- `getTotalesPorFecha()` - Calcula totales del día
- `getTotalesConcentradoPeriodo()` - Calcula totales del periodo
- `listProductClass()` - Lista clases con compras
- `getComprasPorClaseYFecha()` - Obtiene total por clase y fecha

## 🎨 Componentes Visuales

### Tarjetas de Información (infoCard)
```javascript
this.infoCard({
    parent: 'showCards',
    theme: 'light',
    json: [
        {
            title: 'Total de compras',
            data: {
                value: formatPrice(data.totalCompras),
                color: 'text-blue-600'
            }
        }
    ]
});
```

### Tabla de Compras (createTable)
```javascript
this.createTable({
    parent: "containerCompras",
    data: { opc: 'ls', fecha: fecha },
    coffeesoft: true,
    conf: { datatable: true, pag: 15 },
    attr: {
        id: 'tbCompras',
        theme: 'corporativo'
    }
});
```

### Modal de Formulario (createModalForm)
```javascript
this.createModalForm({
    id: 'formCompraAdd',
    data: { opc: 'addCompra' },
    bootbox: { title: 'Nueva Compra' },
    json: this.jsonCompra(),
    success: (response) => { ... }
});
```

## 🔄 Flujo de Datos

### Registro de Compra
1. Usuario hace clic en "Registrar nueva compra"
2. Se abre modal con formulario
3. Usuario selecciona categoría → carga productos
4. Usuario selecciona tipo de compra → muestra/oculta campos
5. Usuario ingresa subtotal e impuesto → calcula total
6. Al guardar: `addCompra()` → `createCompra()` → Base de datos
7. Actualiza tabla y totales

### Consulta de Concentrado
1. Usuario selecciona rango de fechas y UDN
2. `lsConcentrado()` genera estructura de tabla
3. Por cada clase de producto:
   - Obtiene compras por día
   - Calcula totales
4. Genera fila de totales generales
5. Renderiza tabla con `createCoffeTable()`

## 📊 Badges de Estado

```php
function renderPurchaseType($purchaseType) {
    switch ($purchaseType) {
        case 'Fondo fijo':
            return '<span class="bg-green-100 text-green-800">
                <i class="icon-money"></i> Fondo fijo
            </span>';
        case 'Corporativo':
            return '<span class="bg-blue-100 text-blue-800">
                <i class="icon-briefcase"></i> Corporativo
            </span>';
        case 'Crédito':
            return '<span class="bg-orange-100 text-orange-800">
                <i class="icon-credit-card"></i> Crédito
            </span>';
    }
}
```

## 🚀 Uso del Módulo

### Acceso
```
URL: finanzas/captura/compras.php
```

### Inicialización
```javascript
$(async () => {
    const data = await useFetch({ 
        url: api, 
        data: { opc: "init" } 
    });
    
    // Cargar datos iniciales
    lsProductClass = data.productClass;
    lsProduct = data.product;
    lsPurchaseType = data.purchaseType;
    
    // Inicializar aplicación
    app = new App(api, "root");
    app.render();
});
```

## 🔐 Seguridad

- ✅ Validación de permisos por nivel de usuario
- ✅ Soft delete (active = 0)
- ✅ Validación de campos requeridos
- ✅ Sanitización de datos con `$this->util->sql()`
- ✅ Prepared statements en todas las consultas

## 📝 Notas Técnicas

1. **Cálculo automático de total**: El total se calcula en tiempo real sumando subtotal + impuesto
2. **Campos dinámicos**: Los campos se muestran/ocultan según el tipo de compra seleccionado
3. **Productos por categoría**: Al seleccionar una categoría, se cargan solo los productos de esa categoría
4. **Concentrado expandible**: Cada fila del concentrado puede expandirse para ver detalles
5. **Saldo fondo fijo**: El saldo inicial es fijo (15,000) y se resta las salidas del periodo

## 🎯 Próximas Mejoras

- [ ] Exportar concentrado a Excel
- [ ] Subir archivos de comprobantes
- [ ] Bloqueo mensual del módulo
- [ ] Historial de cambios (audit log)
- [ ] Notificaciones de compras pendientes
- [ ] Dashboard con gráficas
- [ ] Reportes personalizados

---

**Desarrollado con CoffeeSoft Framework**  
**Versión:** 1.0  
**Fecha:** Noviembre 2025
