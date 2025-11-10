# Requirements Document - Módulo de Clientes

## Introduction

El módulo de Clientes es un sistema integral para la gestión de créditos otorgados a clientes activos de cada unidad de negocio. Permite registrar, consultar, modificar y eliminar movimientos de crédito (consumos, abonos parciales y pagos totales), visualizar balances individuales y generales, y mantener sincronización con el módulo de Ventas.

## Glossary

- **Sistema**: Plataforma ERP CoffeeSoft para gestión financiera
- **Módulo de Clientes**: Componente del sistema para gestión de créditos
- **Movimiento de Crédito**: Registro de consumo, abono o pago asociado a un cliente
- **UDN**: Unidad de Negocio
- **Balance**: Resumen financiero de consumos y pagos de un cliente
- **Concentrado**: Vista consolidada de balances por cliente en un rango de fechas
- **Usuario de Captura**: Usuario con permisos de nivel 1 (registro y modificación diaria)
- **Usuario de Gerencia**: Usuario con permisos de nivel 2 (consulta y exportación)
- **Usuario de Contabilidad**: Usuario con permisos de nivel 3 (consulta sin modificación)
- **Usuario Administrador**: Usuario con permisos de nivel 4 (gestión completa)

## Requirements

### Requirement 1

**User Story:** Como usuario del sistema, quiero acceder a la interfaz del módulo de Clientes con pestañas organizadas, para visualizar los consumos y pagos de crédito registrados, así como el balance general del día

#### Acceptance Criteria

1. WHEN el usuario accede al módulo de Clientes, THE Sistema SHALL mostrar una interfaz con pestañas organizadas que incluyan: Dashboard principal, Concentrado de clientes y Administración
2. WHEN el usuario visualiza el dashboard principal, THE Sistema SHALL mostrar tres tarjetas con totales: Total de consumos, Total de pagos en efectivo y Total de pagos en banco
3. WHEN el usuario visualiza el dashboard principal, THE Sistema SHALL mostrar dos botones principales: "Concentrado de clientes" y "Registrar nuevo movimiento"
4. WHEN el usuario visualiza el dashboard principal, THE Sistema SHALL mostrar una tabla con las columnas: Cliente, Tipo de movimiento, Método de pago, Monto y Botones de acción
5. WHEN el usuario selecciona un filtro de tipo de movimiento, THE Sistema SHALL actualizar la tabla mostrando únicamente los registros del tipo seleccionado (consumos, pagos o anticipos)
6. WHEN el usuario cambia la fecha de captura, THE Sistema SHALL actualizar automáticamente todos los datos mostrados en el dashboard
7. WHEN se registra un nuevo movimiento de crédito, THE Sistema SHALL reflejar la suma total de consumos y pagos en el módulo de Ventas

### Requirement 2

**User Story:** Como usuario de nivel captura, quiero registrar nuevos movimientos asociados al crédito de un cliente, para mantener actualizado su saldo y reflejar los consumos o pagos realizados durante el día

#### Acceptance Criteria

1. WHEN el usuario hace clic en "Registrar nuevo movimiento", THE Sistema SHALL mostrar un formulario modal con los campos: Selector de Cliente, Deuda actual (solo lectura), Tipo de movimiento, Método de pago, Cantidad y Descripción (opcional)
2. WHEN el usuario selecciona el tipo de movimiento "Consumo", THE Sistema SHALL establecer automáticamente el método de pago como "N/A (No aplica)" y deshabilitar la edición de este campo
3. WHEN el usuario selecciona el tipo de movimiento "Abono parcial" o "Pago total", THE Sistema SHALL habilitar el selector de método de pago con las opciones "Efectivo" y "Banco"
4. WHEN el usuario intenta guardar el formulario con campos obligatorios vacíos, THE Sistema SHALL mostrar mensajes de validación indicando los campos requeridos
5. WHEN el usuario guarda un movimiento válido, THE Sistema SHALL registrar el movimiento en la base de datos con fecha, hora, usuario y cliente asociado
6. WHEN se guarda exitosamente un movimiento, THE Sistema SHALL actualizar automáticamente la tabla de movimientos del día y los totales del dashboard
7. WHEN se guarda exitosamente un movimiento, THE Sistema SHALL mostrar un mensaje de confirmación visual al usuario
8. WHEN ocurre un error al guardar, THE Sistema SHALL mostrar un mensaje de error descriptivo al usuario

### Requirement 3

**User Story:** Como usuario del sistema, quiero editar o eliminar movimientos de crédito registrados, para mantener la información de los clientes precisa y actualizada

#### Acceptance Criteria

1. WHEN el usuario hace clic en el botón de editar de un movimiento, THE Sistema SHALL mostrar un modal con el formulario precargado con los datos actuales del movimiento
2. WHEN el usuario modifica los datos y guarda los cambios, THE Sistema SHALL actualizar el registro en la base de datos y refrescar la tabla de movimientos
3. WHEN el usuario hace clic en el botón de eliminar de un movimiento, THE Sistema SHALL mostrar un diálogo de confirmación con el mensaje "¿Esta seguro de querer eliminar el movimiento a crédito?"
4. WHEN el usuario confirma la eliminación, THE Sistema SHALL registrar la fecha, hora, usuario y cliente asociado al movimiento eliminado en un log de auditoría
5. WHEN se elimina exitosamente un movimiento, THE Sistema SHALL actualizar automáticamente la tabla de movimientos y los totales del dashboard
6. WHEN se completa una acción de edición o eliminación exitosa, THE Sistema SHALL mostrar un mensaje de confirmación al usuario
7. WHEN ocurre un error en la edición o eliminación, THE Sistema SHALL mostrar un mensaje de error descriptivo al usuario

### Requirement 4

**User Story:** Como usuario del sistema, quiero consultar el detalle completo de un movimiento de crédito, para revisar la información del cliente, el tipo de movimiento, método de pago y saldo actualizado

#### Acceptance Criteria

1. WHEN el usuario hace clic en el botón de ver detalle de un movimiento, THE Sistema SHALL mostrar un modal con la información completa del movimiento
2. WHEN se muestra el detalle del movimiento, THE Sistema SHALL incluir la sección "INFORMACIÓN DEL CLIENTE" con el nombre del cliente
3. WHEN se muestra el detalle del movimiento, THE Sistema SHALL incluir la sección "DETALLES DEL MOVIMIENTO" con el tipo de movimiento y método de pago
4. WHEN se muestra el detalle del movimiento, THE Sistema SHALL incluir la sección "DESCRIPCIÓN" con el texto descriptivo del movimiento o "Ninguna" si no existe
5. WHEN se muestra el detalle del movimiento, THE Sistema SHALL incluir la sección "RESUMEN FINANCIERO" con: Deuda actual, Consumo o Pago (según tipo), y Nueva deuda calculada
6. WHEN se muestra el detalle del movimiento, THE Sistema SHALL incluir información de auditoría mostrando "Actualizado por última vez: [fecha], Por: [nombre usuario]"
7. WHEN el usuario cierra el modal de detalle, THE Sistema SHALL regresar a la vista principal sin realizar cambios en los datos

### Requirement 5

**User Story:** Como administrador del sistema, quiero definir los niveles de acceso del módulo de Clientes, para controlar qué acciones puede realizar cada usuario según su rol

#### Acceptance Criteria

1. WHEN un usuario de nivel 1 (Captura) accede al módulo, THE Sistema SHALL permitir registrar, modificar y consultar movimientos únicamente del día actual
2. WHEN un usuario de nivel 2 (Gerencia) accede al módulo, THE Sistema SHALL permitir consultar el concentrado y balances individuales o generales con opción de exportar a Excel
3. WHEN un usuario de nivel 3 (Contabilidad/Dirección) accede al módulo, THE Sistema SHALL permitir filtrar por unidad de negocio sin permitir modificar registros
4. WHEN un usuario de nivel 4 (Administración) accede al módulo, THE Sistema SHALL permitir gestionar clientes y controlar el bloqueo/desbloqueo del módulo completo
5. WHEN el sistema carga el módulo, THE Sistema SHALL aplicar dinámicamente los permisos según el nivel del usuario autenticado
6. WHEN un usuario intenta realizar una acción no permitida por su nivel, THE Sistema SHALL mostrar un mensaje indicando que no tiene permisos suficientes
7. WHEN un usuario administrador bloquea el módulo, THE Sistema SHALL deshabilitar todas las funciones de captura y modificación para usuarios de nivel inferior

### Requirement 6

**User Story:** Como usuario de nivel gerencia o superior, quiero visualizar el concentrado de consumos y pagos de los clientes dentro de un rango de fechas, para obtener el balance individual y general de las unidades de negocio

#### Acceptance Criteria

1. WHEN el usuario accede a la vista de concentrado, THE Sistema SHALL mostrar una tabla con columnas de cliente, deuda inicial, consumos por día, pagos por día y saldo final
2. WHEN se muestran los datos del concentrado, THE Sistema SHALL diferenciar visualmente las columnas de consumos (fondo verde) y pagos (fondo naranja/salmón)
3. WHEN se muestra el concentrado, THE Sistema SHALL incluir filas de resumen mostrando: Saldo inicial, Total de consumos, Total de pagos y Saldo final
4. WHEN el usuario selecciona un rango de fechas, THE Sistema SHALL actualizar automáticamente todos los datos del concentrado según el período seleccionado
5. WHEN el usuario hace clic en el botón "Exportar a Excel", THE Sistema SHALL generar un archivo Excel con todos los datos del concentrado visible
6. WHEN se muestra el concentrado, THE Sistema SHALL incluir filas expandibles por cliente mostrando el detalle de sus movimientos
7. WHEN el usuario cambia el filtro de unidad de negocio, THE Sistema SHALL actualizar el concentrado mostrando únicamente los datos de la UDN seleccionada
