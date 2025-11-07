# Requirements Document - Módulo de Compras

## Introduction

El módulo de Compras es un sistema integral para la gestión y administración de compras realizadas por la unidad de negocio. Permite capturar, consultar y administrar diferentes tipos de compras (Fondo fijo, Corporativo, Crédito) con filtros dinámicos, control de accesos por perfil de usuario, y actualización de totales en tiempo real. El sistema incluye gestión de proveedores, métodos de pago, categorías de productos y generación de reportes concentrados.

## Glossary

- **Sistema**: Módulo de Compras completo integrado en el ERP CoffeeSoft
- **UDN**: Unidad de Negocio
- **Fondo Fijo**: Tipo de compra con presupuesto limitado y control de saldo
- **Compra Corporativa**: Compra realizada con métodos de pago corporativos (tarjeta de crédito, transferencia, etc.)
- **Compra a Crédito**: Compra realizada con proveedores que otorgan crédito
- **Clase de Insumo**: Categoría de producto (Alimentos, Bebidas, Gastos administrativos, etc.)
- **Método de Pago**: Forma de pago utilizada (Efectivo, Tarjeta de crédito, Transferencia, etc.)
- **Proveedor**: Entidad que suministra productos o servicios
- **Reembolso**: Devolución de dinero del fondo fijo
- **Bloqueo de Módulo**: Restricción temporal de edición controlada por Contabilidad
- **Concentrado**: Reporte consolidado de compras por rango de fechas

## Requirements

### Requirement 1

**User Story:** Como usuario del sistema, quiero acceder al módulo de compras con sus pestañas y componentes principales, para registrar, consultar y administrar las compras realizadas en la unidad de negocio.

#### Acceptance Criteria

1. WHEN el usuario accede al módulo de compras, THE Sistema SHALL mostrar la interfaz principal con las pestañas: Compras, Concentrado de compras, y Archivos
2. WHEN el usuario visualiza la pestaña principal, THE Sistema SHALL mostrar los totales de compras generales, por tipo de compra (Fondo fijo, Corporativo, Crédito) y saldo del fondo fijo
3. WHILE el usuario permanece en el módulo, THE Sistema SHALL mantener visible en todo momento la suma total de compras y el saldo actual del fondo fijo
4. WHEN el usuario visualiza la interfaz principal, THE Sistema SHALL incluir los botones de acción "Subir archivos de compras" y "Registrar nueva compra"
5. WHEN el usuario modifica la fecha de consulta o agrega un nuevo registro, THE Sistema SHALL actualizar en tiempo real la tabla de compras y los totales mostrados

### Requirement 2

**User Story:** Como usuario con acceso de captura, quiero registrar una nueva compra en el sistema, para mantener actualizada la información financiera y de insumos.

#### Acceptance Criteria

1. WHEN el usuario hace clic en "Registrar nueva compra", THE Sistema SHALL mostrar un formulario modal con los campos: Categoría de producto, Producto, Tipo de compra, Proveedor, Método de pago, Subtotal, Impuesto, y Descripción
2. WHEN el usuario selecciona una clase de insumo en el campo Categoría de producto, THE Sistema SHALL mostrar únicamente los productos relacionados con esa categoría en el campo Producto
3. WHEN el usuario selecciona el tipo de compra "Corporativo", THE Sistema SHALL desplegar el campo Método de pago con las formas de pago disponibles (Efectivo, Tarjeta de crédito, Transferencia)
4. WHEN el usuario selecciona el tipo de compra "Crédito", THE Sistema SHALL mostrar el campo Proveedor con los proveedores que otorgan crédito
5. WHEN el usuario selecciona el tipo de compra "Fondo fijo", THE Sistema SHALL ocultar los campos Método de pago y Proveedor
6. WHEN el usuario ingresa el subtotal, THE Sistema SHALL calcular automáticamente el impuesto y el total
7. WHEN el usuario completa todos los campos requeridos y hace clic en "Guardar compra", THE Sistema SHALL validar los datos, guardar el registro en la base de datos y actualizar la tabla de compras en tiempo real
8. IF el saldo del fondo fijo es insuficiente para una compra de tipo "Fondo fijo", THEN THE Sistema SHALL mostrar un mensaje de error y no permitir guardar el registro

### Requirement 3

**User Story:** Como usuario del sistema, quiero editar o eliminar compras registradas, para corregir errores o actualizar la información registrada.

#### Acceptance Criteria

1. WHEN el usuario hace clic en el botón de editar de una compra, THE Sistema SHALL mostrar un modal con el formulario de edición precargado con los datos actuales de la compra
2. WHILE el módulo está bloqueado por Contabilidad, THE Sistema SHALL permitir editar todos los campos excepto monto y tipo de compra
3. WHEN el usuario modifica los datos y hace clic en "Guardar cambios", THE Sistema SHALL validar los datos, actualizar el registro en la base de datos y refrescar la tabla en tiempo real
4. WHEN el usuario hace clic en el botón de eliminar de una compra, THE Sistema SHALL mostrar un mensaje de confirmación "¿Esta seguro de querer eliminar la compra?"
5. WHEN el usuario confirma la eliminación, THE Sistema SHALL eliminar el registro de la base de datos y actualizar la tabla en tiempo real
6. IF la compra tiene un reembolso asociado del fondo fijo, THEN THE Sistema SHALL no permitir modificar el monto ni el tipo de compra y mostrar un mensaje informativo
7. WHEN el usuario hace clic en el botón de detalles de una compra, THE Sistema SHALL mostrar un modal con toda la información del registro incluyendo: Categoría de producto, Producto, Tipo de compra, Método de pago, Proveedor, Número de Ticket/Factura, Descripción, Subtotal, Impuesto y Total

### Requirement 4

**User Story:** Como usuario del sistema, quiero filtrar las compras registradas según tipo y método de pago, para consultar fácilmente la información específica de cada tipo de compra.

#### Acceptance Criteria

1. WHEN el usuario visualiza la tabla de compras, THE Sistema SHALL incluir un filtro desplegable de tipo de compra con las opciones: Todas las compras, Compras de fondo fijo, Compras de corporativo, Compras a crédito
2. WHEN el usuario selecciona "Compras de corporativo" en el filtro de tipo de compra, THE Sistema SHALL mostrar un segundo filtro desplegable de método de pago con las opciones disponibles (Efectivo, Tarjeta de crédito, Transferencia, Todos los métodos de pago)
3. WHEN el usuario selecciona un tipo de compra diferente a "Corporativo", THE Sistema SHALL ocultar el filtro de método de pago
4. WHEN el usuario aplica un filtro, THE Sistema SHALL actualizar dinámicamente la tabla mostrando únicamente las compras que cumplan con los criterios seleccionados
5. WHEN el usuario aplica filtros, THE Sistema SHALL actualizar los totales mostrados para reflejar únicamente las compras filtradas

### Requirement 5

**User Story:** Como usuario con nivel de gerencia, quiero visualizar un concentrado de compras dentro de un rango de fechas, para analizar los gastos generales y balance del fondo fijo.

#### Acceptance Criteria

1. WHEN el usuario accede a la pestaña "Concentrado de compras", THE Sistema SHALL mostrar un selector de rango de fechas y un filtro de tipo de compra
2. WHEN el usuario selecciona un rango de fechas y hace clic en "Consultar", THE Sistema SHALL mostrar una tabla comparativa con las compras agrupadas por clase de producto y día
3. WHEN el Sistema muestra el concentrado, THE Sistema SHALL incluir columnas para cada día del rango seleccionado con subtotales, impuestos y totales diarios
4. WHEN el usuario visualiza el concentrado de compras de fondo fijo, THE Sistema SHALL mostrar el saldo inicial del fondo fijo, las salidas del período y el saldo final
5. WHEN el usuario hace clic en el botón "Exportar a Excel", THE Sistema SHALL generar un archivo Excel con los datos del concentrado mostrado
6. WHEN el usuario hace clic en un ícono de información junto a una clase de producto, THE Sistema SHALL mostrar un modal con el detalle de todas las compras de esa categoría en el día seleccionado

### Requirement 6

**User Story:** Como administrador del sistema, quiero gestionar los niveles de acceso del módulo de compras, para asegurar que cada usuario opere según su rol y permisos definidos.

#### Acceptance Criteria

1. WHEN un usuario con perfil de Captura accede al módulo, THE Sistema SHALL permitir registrar, editar y eliminar compras sin restricciones de bloqueo
2. WHEN un usuario con perfil de Gerencia accede al módulo, THE Sistema SHALL permitir visualizar todas las compras y acceder al concentrado de compras
3. WHEN un usuario con perfil de Dirección accede al módulo, THE Sistema SHALL permitir visualizar todas las compras, acceder al concentrado y descargar reportes
4. WHEN un usuario con perfil de Contabilidad accede al módulo, THE Sistema SHALL mostrar un botón adicional para bloquear o desbloquear el módulo de compras
5. WHEN el usuario de Contabilidad bloquea el módulo, THE Sistema SHALL restringir la edición de monto y tipo de compra para todos los usuarios
6. WHEN el usuario de Contabilidad desbloquea el módulo, THE Sistema SHALL permitir la edición completa de todos los campos de las compras
7. IF una compra tiene un reembolso asociado del fondo fijo, THEN THE Sistema SHALL no permitir modificar el monto ni el tipo de compra independientemente del estado de bloqueo del módulo
8. WHEN el Sistema registra una acción de edición, eliminación o bloqueo, THE Sistema SHALL guardar un registro en la tabla audit_log con el usuario, fecha, acción realizada y datos modificados
