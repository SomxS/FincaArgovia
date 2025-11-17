# Requirements Document - Módulo de Almacén

## Introduction

El módulo de Almacén es un sistema de gestión de salidas de almacén que permite la captura, consulta, modificación y control de movimientos de insumos. El sistema garantiza la trazabilidad de los movimientos mediante validación con archivos de respaldo y control de acceso por niveles de usuario.

## Glossary

- **System**: El módulo de Almacén dentro del sistema CoffeeSoft
- **User**: Usuario autenticado en el sistema con nivel de acceso asignado
- **Warehouse Output**: Salida de almacén registrada con almacén, cantidad y descripción
- **Warehouse Input**: Entrada de almacén registrada desde el módulo de compras
- **Business Unit**: Unidad de negocio (UDN) a la que pertenece el movimiento
- **Consolidated Report**: Reporte de balances con entradas y salidas por almacén
- **Access Level**: Nivel de permisos del usuario (Captura, Gerencia, Contabilidad, Administración)
- **Backup File**: Archivo de respaldo asociado a movimientos de almacén
- **Supply Item**: Insumo del costo directo disponible en el almacén
- **Audit Log**: Registro de bitácora de operaciones realizadas

## Requirements

### Requirement 1: Interfaz inicial del módulo

**User Story:** Como usuario del sistema, quiero acceder a la interfaz del módulo de Almacén con pestañas organizadas, para visualizar el total de salidas, registrar nuevas salidas y gestionar archivos asociados.

#### Acceptance Criteria

1. WHEN the User accesses the Warehouse module, THE System SHALL display a tabbed interface with organized sections
2. WHEN the User views the main dashboard, THE System SHALL display the total warehouse outputs for the selected date
3. WHEN the User views the main interface, THE System SHALL display three primary action buttons: "Concentrado de almacén", "Subir archivos de almacén", and "Registrar nueva salida de almacén"
4. WHEN the User views the outputs table, THE System SHALL display columns for Almacén, Monto, Descripción, and action buttons (edit and delete)
5. WHEN the User changes the selected date, THE System SHALL automatically update the outputs table with data for the new date
6. WHEN the User views the interface, THE System SHALL display the sum total of all warehouse outputs for the current day
7. WHEN the User uploads a Backup File, THE System SHALL validate that the file size does not exceed 20 MB

### Requirement 2: Registro y edición de salidas

**User Story:** Como usuario de nivel captura, quiero registrar y modificar las salidas de almacén, para mantener actualizada la información de los movimientos diarios de cada unidad de negocio.

#### Acceptance Criteria

1. WHEN the User clicks "Registrar nueva salida de almacén", THE System SHALL display a modal form with fields for Almacén, Cantidad, and Descripción
2. WHEN the User views the Almacén selector, THE System SHALL populate it with Supply Items from the direct cost catalog
3. WHEN the User attempts to submit the form, THE System SHALL validate that all fields (Almacén, Cantidad, Descripción) are filled
4. WHEN the User successfully saves a Warehouse Output, THE System SHALL automatically update the outputs table for the current day
5. WHEN the User clicks the edit button on an existing output, THE System SHALL display a modal with the same fields pre-filled with current values
6. WHEN the User successfully saves or updates data, THE System SHALL display a visual confirmation message

### Requirement 3: Eliminación y visualización de descripción

**User Story:** Como usuario del sistema, quiero eliminar salidas de almacén o visualizar la descripción detallada, para mantener el control y claridad sobre los movimientos registrados.

#### Acceptance Criteria

1. WHEN the User clicks the delete button on a Warehouse Output, THE System SHALL display a confirmation modal before proceeding with deletion
2. WHEN the User confirms deletion of a Warehouse Output, THE System SHALL record in the Audit Log the user ID, date, and amount of the deleted output
3. WHEN the User clicks on a Warehouse Output description, THE System SHALL display a modal with the complete description details
4. WHEN the User completes a delete or view action, THE System SHALL display a success or error message based on the operation result
5. WHEN the User views a Warehouse Input description, THE System SHALL display purchase details in the modal
6. WHEN the User views a Warehouse Output description, THE System SHALL display the description field content in the modal

### Requirement 4: Niveles de acceso del módulo

**User Story:** Como administrador del sistema, quiero definir los niveles de acceso del módulo de Almacén, para controlar qué operaciones puede realizar cada usuario según su rol.

#### Acceptance Criteria

1. WHEN a User with Access Level "Captura" accesses the module, THE System SHALL allow capture, modification, and consultation of warehouse outputs for the current day
2. WHEN a User with Access Level "Gerencia" accesses the module, THE System SHALL allow consultation of daily consolidated reports and individual or general balances with Excel export option
3. WHEN a User with Access Level "Contabilidad" or "Dirección" accesses the module, THE System SHALL allow filtering by Business Unit without modification permissions
4. WHEN a User with Access Level "Administración" accesses the module, THE System SHALL allow management of supply item classes, products, and module lock/unlock functionality
5. WHEN a User with Access Level "Captura" views the module, THE System SHALL display the module name as "Salidas de almacén"
6. WHEN a User with Access Level other than "Captura" views the module, THE System SHALL display the module name as "Almacén"
7. WHEN the System loads the module interface, THE System SHALL dynamically adjust visible buttons and views based on the User's Access Level

### Requirement 5: Concentrado y balances de almacén

**User Story:** Como usuario de nivel gerencia o superior, quiero visualizar el concentrado de entradas y salidas del almacén, para analizar los balances diarios e históricos por unidad de negocio.

#### Acceptance Criteria

1. WHEN the User with appropriate Access Level clicks "Concentrado de almacén", THE System SHALL display a table with balances per warehouse separating Warehouse Inputs and Warehouse Outputs in distinct columns with different colors
2. WHEN the User views the Consolidated Report, THE System SHALL allow selection of a configurable date range
3. WHEN the User views the Consolidated Report, THE System SHALL display general totals for inputs, outputs, and final balance
4. WHEN the User clicks "Exportar a Excel" on the Consolidated Report, THE System SHALL generate and download an Excel file with the displayed data
5. WHEN the User views the Consolidated Report table, THE System SHALL provide expandable rows to show detailed movements for each warehouse
6. WHEN the User views the Consolidated Report, THE System SHALL display initial balance, total inputs, total outputs, and final balance for each Supply Item
7. WHEN the User filters the Consolidated Report by date range, THE System SHALL recalculate all balances and totals based on the selected period
