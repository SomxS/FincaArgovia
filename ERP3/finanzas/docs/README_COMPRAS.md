# 🛒 Módulo de Compras - CoffeeSoft ERP

## 📋 Descripción General

El módulo de Compras permite capturar, consultar y administrar las diferentes compras realizadas por la unidad de negocio, aplicando filtros dinámicos según clase de insumo o departamento, tipo de compra, proveedor y forma de pago.

## 🎯 Características Principales

### ✅ Funcionalidades Implementadas

1. **Interfaz Principal**
   - Dashboard con totales de compras por tipo
   - Tabla dinámica con filtros avanzados
   - Visualización en tiempo real de saldos

2. **Registro de Compras**
   - Formulario modal con validaciones
   - Selección dinámica de productos por categoría
   - Cálculo automático de totales (subtotal + impuesto)
   - Campos condicionales según tipo de compra

3. **Gestión de Compras**
   - Edición de compras existentes
   - Visualización detallada de compras
   - Cambio de estado (activar/desactivar)
   - Validaciones de permisos

4. **Filtros Dinámicos**
   - Por rango de fechas
   - Por tipo de compra (Fondo fijo, Corporativo, Crédito)
   - Por método de pago
   - Actualización en tiempo real

5. **Reportes**
   - Concentrado de compras por clase de producto
   - Balance de fondo fijo
   - Totales por tipo de compra

## 📁 Estructura de Archivos

```
finanzas/
├── captura/
│   ├── ctrl/
│   │   └── ctrl-compras.php      # Controlador principal
│   ├── mdl/
│   │   └── mdl-compras.php       # Modelo de datos
│   ├── js/
│   │   └── compras.js            # Frontend JavaScript
│   └── compras.php               # Vista principal
└── docs/
    ├── compras_database.sql      # Estructura de BD
    └── README_COMPRAS.md         # Esta documentación
```

## 🗄️ Base de Datos

### Tablas Principales

1. **purchase** - Registro de compras
   - `id` - Identificador único
   - `udn_id` - Unidad de negocio
   - `product_class_id` - Categoría del producto
   - `product_id` - Producto comprado
   - `supplier_id` - Proveedor (opcional)
   - `purchase_type_id` - Tipo de compra (1=Fondo fijo, 2=Corporativo, 3=Crédito)
   - `method_pay_id` - Método de pago (opcional)
   - `subtotal` - Subtotal de la compra
   - `tax` - Impuesto aplicado
   - `total` - Total de la compra
   - `description` - Descripción de la compra
   - `operation_date` - Fecha de operación
   - `active` - Estado (1=Activo, 0=Inactivo)

2. **product_class** - Categorías de productos
3. **product** - Productos disponibles
4. **purchase_type** - Tipos de compra
5. **method_pay** - Métodos de pago
6. **supplier** - Proveedores

## 🔧 Instalación

### 1. Importar Base de Datos

```sql
mysql -u usuario -p nombre_bd < finanzas/docs/compras_database.sql
```

### 2. Configurar Conexión

Editar `conf/_Conect.php` con las credenciales correctas:

```php
$this->bd = "rfwsmqex_erp.";
```

### 3. Acceder al Módulo

```
http://tu-dominio/finanzas/captura/compras.php
```

## 📝 Uso del Módulo

### Registrar Nueva Compra

1. Click en botón "Nueva Compra"
2. Seleccionar categoría de producto
3. Seleccionar producto (se carga dinámicamente)
4. Seleccionar tipo de compra:
   - **Fondo fijo**: No requiere campos adicionales
   - **Corporativo**: Mostrar método de pago
   - **Crédito**: Mostrar proveedor
5. Ingresar subtotal e impuesto
6. Agregar descripción (opcional)
7. Click en "Guardar Compra"

### Editar Compra

1. Click en ícono de editar (lápiz) en la tabla
2. Modificar campos necesarios
3. Click en "Guardar Compra"

### Ver Detalle

1. Click en ícono de ver (ojo) en la tabla
2. Se muestra modal con información completa

### Eliminar/Reactivar Compra

1. Click en ícono de eliminar (basura)
2. Confirmar acción
3. La compra cambia de estado

## 🎨 Componentes CoffeeSoft Utilizados

### Frontend (JavaScript)

- `Templates` - Clase base del framework
- `primaryLayout()` - Layout principal
- `createfilterBar()` - Barra de filtros
- `createTable()` - Tabla dinámica
- `createModalForm()` - Formularios modales
- `swalQuestion()` - Confirmaciones
- `useFetch()` - Peticiones AJAX

### Backend (PHP)

- `CRUD` - Clase base para operaciones de BD
- `_Select()` - Consultas SELECT
- `_Insert()` - Inserción de registros
- `_Update()` - Actualización de registros
- `_Read()` - Consultas SQL personalizadas

## 🔐 Niveles de Acceso

### 1. Captura
- Registrar nuevas compras
- Ver listado de compras

### 2. Gerencia
- Todas las funciones de Captura
- Editar compras
- Ver reportes

### 3. Dirección
- Todas las funciones de Gerencia
- Acceso a reportes avanzados

### 4. Contabilidad
- Todas las funciones anteriores
- Bloquear/desbloquear módulo
- Acceso completo a auditoría

## 📊 Reportes Disponibles

### 1. Concentrado de Compras
- Agrupación por clase de producto y fecha
- Subtotales, impuestos y totales diarios
- Filtros por rango de fechas y tipo de compra

### 2. Balance de Fondo Fijo
- Saldo inicial: $15,000.00
- Salidas del período
- Saldo final calculado

## 🚀 Próximas Mejoras

- [ ] Exportación a Excel de reportes
- [ ] Carga masiva de compras desde archivo
- [ ] Integración con módulo de inventarios
- [ ] Notificaciones automáticas
- [ ] Dashboard con gráficas
- [ ] Historial de cambios por compra
- [ ] Validación de presupuestos

## 🐛 Solución de Problemas

### Error: "No se puede conectar a la base de datos"
- Verificar credenciales en `conf/_Conect.php`
- Verificar que el servidor MySQL esté activo

### Error: "No se cargan los productos"
- Verificar que existan registros en `product_class` y `product`
- Revisar consola del navegador para errores JavaScript

### Error: "No se guardan las compras"
- Verificar permisos de escritura en la base de datos
- Revisar logs de PHP en `error.log`

## 📞 Soporte

Para soporte técnico o reportar bugs:
- Email: soporte@coffeesoft.com
- Documentación: https://docs.coffeesoft.com

## 📄 Licencia

© 2025 CoffeeSoft ERP System. Todos los derechos reservados.

---

**Versión:** 1.0.0  
**Última actualización:** Enero 2025  
**Desarrollado con:** CoffeeSoft Framework
