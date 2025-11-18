# Requirements Document - Módulo de Costos

## Introduction

El módulo de Costos es un sistema de consulta y análisis financiero que permite visualizar el concentrado diario de costos directos y salidas de almacén dentro de un rango de fechas específico. Este módulo está diseñado exclusivamente para consulta (solo lectura), sin capacidad de edición, garantizando la integridad de la información financiera. Incluye funcionalidad de exportación a Excel y filtrado por unidad de negocio para usuarios de nivel 3.

## Glossary

- **Sistema**: Plataforma web CoffeeSoft para gestión financiera
- **Usuario**: Persona autenticada con acceso al módulo de Costos
- **Nivel_Usuario**: Clasificación de permisos (nivel 2: consulta básica, nivel 3: consulta con filtros avanzados)
- **UDN**: Unidad de Negocio, área operativa específica de la organización
- **Costo_Directo**: Gasto asociado directamente a la operación (proveniente del módulo Compras)
- **Salida_Almacen**: Movimiento de productos desde el almacén (proveniente del módulo Almacén)
- **Concentrado_Diario**: Tabla resumen que agrupa costos directos y salidas de almacén por fecha
- **Rango_Fechas**: Período definido por fecha inicial y fecha final para la consulta
- **Exportacion_Excel**: Funcionalidad para generar archivo Excel con los datos consultados
- **Modo_Lectura**: Estado del módulo que impide cualquier modificación de datos

## Requirements

### Requirement 1

**User Story:** Como usuario de segundo o tercer nivel, quiero visualizar el concentrado diario de costos directos y salidas de almacén dentro de un rango de fechas, para analizar la operación diaria sin modificar información.

#### Acceptance Criteria

1. WHEN el Usuario accede al módulo de Costos, THE Sistema SHALL mostrar la interfaz con pestañas de navegación y componentes de filtrado
2. WHEN el Usuario selecciona un Rango_Fechas válido, THE Sistema SHALL consultar y mostrar el Concentrado_Diario correspondiente
3. THE Sistema SHALL mostrar en la tabla los Costo_Directo, Salida_Almacen, totales diarios y totales generales
4. THE Sistema SHALL impedir cualquier acción de edición mediante Modo_Lectura activo
5. THE Sistema SHALL mantener consistencia visual con los módulos existentes del sistema

### Requirement 2

**User Story:** Como usuario del sistema, quiero exportar la información consultada a un archivo Excel, para generar reportes, conciliaciones y análisis externos.

#### Acceptance Criteria

1. THE Sistema SHALL incluir un botón visible "Exportar a Excel" en la interfaz del módulo
2. WHEN el Usuario presiona el botón de Exportacion_Excel, THE Sistema SHALL generar un archivo con los datos actualmente filtrados
3. THE Sistema SHALL mantener el formato de tabla y encabezados en el archivo exportado
4. THE Sistema SHALL incluir en la Exportacion_Excel los filtros aplicados (Rango_Fechas y UDN si aplica)
5. THE Sistema SHALL descargar el archivo Excel automáticamente al navegador del Usuario

### Requirement 3

**User Story:** Como usuario de tercer nivel (Contabilidad o Dirección), quiero filtrar los costos por unidad de negocio, para analizar operaciones específicas de cada área.

#### Acceptance Criteria

1. WHERE el Nivel_Usuario es igual a 3, THE Sistema SHALL mostrar el filtro de UDN en la interfaz
2. WHERE el Nivel_Usuario es igual a 2, THE Sistema SHALL ocultar el filtro de UDN
3. WHEN el Usuario de nivel 3 selecciona una UDN específica, THE Sistema SHALL actualizar el Concentrado_Diario con datos filtrados
4. THE Sistema SHALL mantener los filtros activos durante la Exportacion_Excel
5. THE Sistema SHALL validar los permisos del Nivel_Usuario antes de aplicar filtros de UDN

### Requirement 4

**User Story:** Como usuario del sistema, quiero tener acceso únicamente de consulta sin posibilidad de modificar datos, para asegurar la integridad financiera y operativa del sistema.

#### Acceptance Criteria

1. THE Sistema SHALL deshabilitar cualquier acción de edición (inputs, botones de guardado, modales de modificación)
2. THE Sistema SHALL validar en el backend que el Usuario no pueda modificar información mediante URL o parámetros
3. THE Sistema SHALL garantizar que el módulo opere estrictamente en Modo_Lectura
4. IF el Usuario intenta realizar una acción de modificación, THEN THE Sistema SHALL denegar la operación y registrar el intento
5. THE Sistema SHALL mostrar únicamente controles de consulta y Exportacion_Excel

### Requirement 5

**User Story:** Como sistema, quiero integrar datos de los módulos Compras y Almacén, para consolidar la información de costos en un solo concentrado.

#### Acceptance Criteria

1. THE Sistema SHALL obtener los Costo_Directo desde el módulo Compras
2. THE Sistema SHALL obtener las Salida_Almacen desde el módulo Almacén
3. THE Sistema SHALL consolidar ambas fuentes de datos en el Concentrado_Diario agrupado por fecha
4. THE Sistema SHALL calcular totales diarios y totales generales automáticamente
5. THE Sistema SHALL actualizar los datos en tiempo real cuando se consulte un nuevo Rango_Fechas

### Requirement 6

**User Story:** Como desarrollador, quiero implementar la estructura MVC del módulo siguiendo los estándares de CoffeeSoft, para mantener la consistencia arquitectónica del sistema.

#### Acceptance Criteria

1. THE Sistema SHALL crear el archivo JavaScript `costos.js` extendiendo la clase Templates
2. THE Sistema SHALL crear el controlador PHP `ctrl-costos.php` con métodos de consulta
3. THE Sistema SHALL crear el modelo PHP `mdl-costos.php` con acceso a las tablas de base de datos
4. THE Sistema SHALL implementar la nomenclatura estándar: `ls()` para listar, `init()` para inicializar filtros
5. THE Sistema SHALL utilizar componentes de CoffeeSoft (createTable, createfilterBar, tabLayout)

### Requirement 7

**User Story:** Como sistema, quiero validar los datos de entrada del usuario, para prevenir consultas inválidas o errores de procesamiento.

#### Acceptance Criteria

1. WHEN el Usuario selecciona un Rango_Fechas, THE Sistema SHALL validar que la fecha inicial sea menor o igual a la fecha final
2. THE Sistema SHALL validar que el Rango_Fechas no exceda un período máximo de 12 meses
3. IF el Rango_Fechas es inválido, THEN THE Sistema SHALL mostrar un mensaje de error descriptivo
4. THE Sistema SHALL validar que la UDN seleccionada exista en la base de datos
5. THE Sistema SHALL sanitizar todos los parámetros de entrada antes de ejecutar consultas SQL
