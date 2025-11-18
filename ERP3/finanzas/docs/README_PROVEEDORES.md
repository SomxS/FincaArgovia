# Módulo de Proveedores - CoffeeSoft ERP

## 📋 Descripción General

El módulo de Proveedores es un sistema integral para gestionar pagos a proveedores de cada unidad de negocio. Permite la captura, modificación y consulta de pagos según el nivel de acceso del usuario, así como visualizar balances detallados y exportar información en formato Excel.

## 🎯 Características Principales

### Niveles de Acceso

**Nivel 1 - Captura:**
- ✅ Registrar pagos a proveedores
- ✅ Editar pagos del día actual
- ✅ Eliminar pagos del día actual
- ✅ Ver totales en tiempo real

**Nivel 2 - Gerencia:**
- ✅ Consultar concentrado de proveedores
- ✅ Ver balances detallados
- ✅ Exportar a Excel
- ❌ No puede capturar ni modificar datos

**Nivel 3 - Contabilidad/Dirección:**
- ✅ Filtrar por unidad de negocio
- ✅ Consultar concentrado
- ✅ Exportar a Excel
- ❌ No puede capturar ni modificar datos

**Nivel 4 - Administración:**
- ✅ Gestionar proveedores
- ✅ Todas las funciones de consulta
- ❌ No puede capturar pagos

## 📁 Estructura de Archivos

```
finanzas/captura/
├── proveedor.php           # Página principal del módulo
├── js/
│   └── proveedor.js        # Lógica frontend (3 clases: App, Captura, Concentrado)
├── ctrl/
│   └── ctrl-proveedor.php  # Controlador backend
├── mdl/
│   └── mdl-proveedor.php   # Modelo de datos
└── sql/
    └── proveedor_database.sql  # Esquema de base de datos
```

## 🗄️ Base de Datos

### Tablas Principales

**payment_type** - Tipos de pago
- id (PK)
- name (Corporativo, Fondo fijo)
- active

**payment_supplier** - Pagos a proveedores
- id (PK)
- supplier_id (FK → supplier)
- payment_type_id (FK → payment_type)
- amount
- description
- payment_date
- udn_id
- user_id
- created_at
- updated_at
- active

**supplier** - Proveedores (tabla compartida con módulo de Compras)
- id (PK)
- name
- active

## 🔌 API Endpoints

### Inicialización
```javascript
{ opc: 'init', udn: 4 }
// Retorna: suppliers, paymentTypes, udn, userLevel
```

### Captura de Pagos

**Listar pagos del día:**
```javascript
{ opc: 'ls', fecha: '2025-11-25', udn: 4 }
```

**Obtener totales:**
```javascript
{ opc: 'getTotales', fecha: '2025-11-25', udn: 4 }
```

**Agregar pago:**
```javascript
{
    opc: 'addPayment',
    supplier_id: 5,
    payment_type_id: 1,
    amount: 500.00,
    description: 'Pago de flete',
    fecha: '2025-11-25',
    udn_id: 4
}
```

**Editar pago:**
```javascript
{
    opc: 'editPayment',
    id: 123,
    supplier_id: 5,
    payment_type_id: 1,
    amount: 600.00,
    description: 'Pago actualizado'
}
```

**Eliminar pago:**
```javascript
{ opc: 'deletePayment', id: 123 }
```

**Obtener pago:**
```javascript
{ opc: 'getPayment', id: 123 }
```

### Concentrado

**Listar balance:**
```javascript
{
    opc: 'lsBalance',
    fi: '2025-11-01',
    ff: '2025-11-30',
    udn: 4
}
```

**Exportar Excel:**
```javascript
{
    opc: 'exportExcel',
    fi: '2025-11-01',
    ff: '2025-11-30',
    udn: 4
}
```

## 🚀 Instalación

### 1. Crear Base de Datos

```sql
-- Ejecutar el archivo SQL
source finanzas/captura/sql/proveedor_database.sql
```

### 2. Configurar Permisos

Asegúrate de que los usuarios tengan los niveles de acceso correctos en la tabla de usuarios.

### 3. Acceder al Módulo

```
http://tu-dominio.com/finanzas/captura/proveedor.php
```

## 💡 Uso del Sistema

### Captura de Pagos (Nivel 1)

1. Seleccionar fecha
2. Hacer clic en "Nuevo Pago"
3. Completar formulario:
   - Seleccionar proveedor
   - Seleccionar tipo de pago
   - Ingresar cantidad
   - Agregar descripción (opcional)
4. Guardar

**Restricciones:**
- Solo se pueden editar/eliminar pagos del día actual
- Los pagos de días anteriores son de solo lectura

### Consulta de Concentrado (Nivel 2+)

1. Seleccionar rango de fechas
2. Ver balance por proveedor:
   - Saldo inicial
   - Compras a crédito (verde)
   - Pagos de crédito (rojo)
   - Saldo final
3. Expandir detalles de cada proveedor
4. Exportar a Excel si es necesario

### Filtro por UDN (Nivel 3+)

1. Seleccionar unidad de negocio en el filtro superior
2. Todos los listados se actualizan automáticamente

## 🔒 Seguridad

### Validaciones Frontend
- Campos requeridos
- Cantidad mayor a 0
- Fecha no futura

### Validaciones Backend
- Verificación de nivel de usuario
- Solo nivel 1 puede capturar/editar/eliminar
- Solo se pueden editar pagos del día actual
- Validación de permisos en cada endpoint

### Protección contra Ataques
- Validación de entrada SQL
- Escape de caracteres especiales
- Verificación de sesión activa
- Logs de auditoría

## 🔗 Integración con Otros Módulos

### Módulo de Compras
- Comparte la tabla `supplier`
- Consulta compras a crédito para el balance
- Sincronización automática de proveedores

### Módulo de Administración
- Gestión centralizada de proveedores
- Cambios reflejados automáticamente

## 📊 Reportes

### Balance de Proveedores
- Agrupa por proveedor
- Muestra compras y pagos por fecha
- Calcula saldo inicial y final
- Código de colores:
  - Verde: Compras a crédito
  - Rojo: Pagos de crédito
  - Gris: Totales

### Exportación Excel
- Formato compatible con Excel
- Incluye todos los datos del balance
- Mantiene formato de colores
- Nombre de archivo con fecha

## 🐛 Troubleshooting

### Error: "No tiene permisos para realizar esta acción"
**Solución:** Verificar nivel de usuario en sesión

### Error: "Solo puede editar pagos del día actual"
**Solución:** Los pagos históricos no se pueden modificar

### No se muestran proveedores en el select
**Solución:** Verificar que existan proveedores activos en la base de datos

### Excel no se descarga
**Solución:** Verificar permisos de escritura en carpeta temporal

## 📝 Notas Técnicas

### Framework
- **Frontend:** CoffeeSoft (jQuery + TailwindCSS)
- **Backend:** PHP 7.4+ con arquitectura MVC
- **Base de datos:** MySQL 5.7+

### Dependencias
- jQuery 3.6+
- Moment.js 2.29+
- SweetAlert2
- Bootbox
- DataTables
- Date Range Picker

### Compatibilidad
- Chrome, Firefox, Safari, Edge (últimas 2 versiones)
- Responsive design para móviles y tablets

## 🔄 Changelog

### Versión 1.0.0 (2025-11-18)
- ✅ Implementación inicial
- ✅ Captura de pagos con validaciones
- ✅ Concentrado de proveedores
- ✅ Integración con módulo de Compras
- ✅ Exportación a Excel
- ✅ Control de permisos por nivel
- ✅ Restricción de edición por fecha

## 👥 Soporte

Para soporte técnico o reportar bugs, contactar al equipo de desarrollo de CoffeeSoft.

---

**Desarrollado con ☕ por CoffeeSoft ERP System**
