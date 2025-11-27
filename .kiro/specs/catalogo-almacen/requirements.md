# Requirements Document

## Introduction

El módulo de **Catálogo** es un componente fundamental del sistema ERP3 que permite administrar la estructura organizacional del almacén mediante la gestión de tres entidades principales: **Categorías**, **Áreas** y **Zonas**. Este módulo proporciona una interfaz unificada con pestañas para agregar, listar y eliminar elementos, manteniendo la consistencia visual con el resto del sistema y facilitando la organización física y lógica del inventario.

## Glossary

- **Sistema**: El sistema ERP3 de gestión empresarial
- **Módulo Catálogo**: Componente del sistema que gestiona categorías, áreas y zonas del almacén
- **Categoría**: Clasificación principal de materiales e insumos del inventario (tabla: mtto_categoria)
- **Área**: Espacio físico principal del almacén donde se organizan los materiales (tabla: mtto_almacen_area)
- **Zona**: Subdivisión dentro de un área del almacén (referenciada en mtto_almacen.id_zona)
- **Usuario**: Persona que interactúa con el sistema
- **Administrador**: Usuario con permisos para gestionar el catálogo del almacén
- **Interfaz de pestañas**: Componente de navegación que permite cambiar entre diferentes secciones sin recargar la página
- **Base de datos**: Sistema de almacenamiento MySQL que contiene las tablas del módulo
- **API REST**: Interfaz de comunicación entre el frontend y el backend mediante peticiones HTTP

## Requirements

### Requirement 1: Gestión de Categorías

**User Story:** Como administrador del sistema, quiero gestionar las categorías de materiales, para mantener organizado el inventario por tipos principales de artículos

#### Acceptance Criteria

1. WHEN el administrador accede al módulo Catálogo THEN el Sistema SHALL mostrar la pestaña "Categorías" como una de las opciones disponibles
2. WHEN el administrador selecciona la pestaña "Categorías" THEN el Sistema SHALL mostrar un formulario con un campo de texto para el nombre de la categoría y un botón "Agregar"
3. WHEN el administrador ingresa un nombre de categoría y presiona "Agregar" THEN el Sistema SHALL validar que el campo no esté vacío
4. WHEN el campo de nombre de categoría está vacío y el administrador intenta agregar THEN el Sistema SHALL mostrar un mensaje de error indicando que el campo es obligatorio
5. WHEN el administrador agrega una categoría válida THEN el Sistema SHALL insertar el registro en la tabla mtto_categoria con los campos idcategoria (auto-incremental) y nombreCategoria
6. WHEN una categoría se agrega exitosamente THEN el Sistema SHALL actualizar la lista de categorías mostrando el nuevo registro
7. WHEN el Sistema muestra la lista de categorías THEN el Sistema SHALL mostrar el ID y el nombre de cada categoría en formato tabular
8. WHEN el administrador hace clic en el botón eliminar de una categoría THEN el Sistema SHALL solicitar confirmación antes de proceder
9. WHEN el administrador confirma la eliminación de una categoría THEN el Sistema SHALL eliminar el registro de la tabla mtto_categoria
10. WHEN una categoría se elimina exitosamente THEN el Sistema SHALL actualizar la lista removiendo el elemento eliminado

### Requirement 2: Gestión de Áreas

**User Story:** Como administrador del almacén, quiero registrar y controlar las áreas del almacén, para organizar físicamente los espacios donde se clasifican los materiales

#### Acceptance Criteria

1. WHEN el administrador accede al módulo Catálogo THEN el Sistema SHALL mostrar la pestaña "Áreas" como una de las opciones disponibles
2. WHEN el administrador selecciona la pestaña "Áreas" THEN el Sistema SHALL mostrar un formulario con un campo de texto para el nombre del área y un botón "Agregar"
3. WHEN el administrador ingresa un nombre de área y presiona "Agregar" THEN el Sistema SHALL validar que el campo no esté vacío
4. WHEN el administrador agrega un área válida THEN el Sistema SHALL insertar el registro en la tabla mtto_almacen_area con los campos idArea (auto-incremental) y Nombre_Area
5. WHEN un área se agrega exitosamente THEN el Sistema SHALL actualizar la lista de áreas mostrando el nuevo registro
6. WHEN el Sistema muestra la lista de áreas THEN el Sistema SHALL mostrar el ID y el nombre de cada área en formato tabular
7. WHEN el administrador intenta agregar un área con un nombre que ya existe THEN el Sistema SHALL mostrar un mensaje indicando que el área ya está registrada
8. WHEN el administrador hace clic en el botón eliminar de un área THEN el Sistema SHALL solicitar confirmación antes de proceder
9. WHEN el administrador confirma la eliminación de un área THEN el Sistema SHALL eliminar el registro de la tabla mtto_almacen_area
10. WHEN un área se elimina exitosamente THEN el Sistema SHALL actualizar la lista removiendo el elemento eliminado

### Requirement 3: Gestión de Zonas

**User Story:** Como encargado de almacén, quiero administrar las zonas internas del almacén, para distribuir correctamente la ubicación de los insumos dentro de cada área

#### Acceptance Criteria

1. WHEN el administrador accede al módulo Catálogo THEN el Sistema SHALL mostrar la pestaña "Zonas" como una de las opciones disponibles
2. WHEN el administrador selecciona la pestaña "Zonas" THEN el Sistema SHALL mostrar un formulario con un campo de texto para el nombre de la zona y un botón "Agregar"
3. WHEN el administrador ingresa un nombre de zona y presiona "Agregar" THEN el Sistema SHALL validar que el campo no esté vacío
4. WHEN el administrador agrega una zona válida THEN el Sistema SHALL insertar el registro en una tabla de zonas con ID auto-incremental y nombre de zona
5. WHEN una zona se agrega exitosamente THEN el Sistema SHALL actualizar la lista de zonas mostrando el nuevo registro
6. WHEN el Sistema muestra la lista de zonas THEN el Sistema SHALL mostrar el ID y el nombre de cada zona en formato tabular
7. WHEN el administrador hace clic en el botón eliminar de una zona THEN el Sistema SHALL solicitar confirmación antes de proceder
8. WHEN el administrador confirma la eliminación de una zona THEN el Sistema SHALL eliminar el registro de la tabla de zonas
9. WHEN una zona se elimina exitosamente THEN el Sistema SHALL actualizar la lista removiendo el elemento eliminado

### Requirement 4: Estructura General del Módulo

**User Story:** Como usuario del sistema, quiero acceder a un módulo organizado en pestañas, para navegar entre categorías, áreas y zonas sin perder la estructura visual

#### Acceptance Criteria

1. WHEN el usuario accede al módulo Catálogo THEN el Sistema SHALL mostrar un encabezado con el título "Catálogo" y una descripción del módulo
2. WHEN el módulo Catálogo se carga THEN el Sistema SHALL mostrar tres pestañas: "Categorías", "Áreas" y "Zonas"
3. WHEN el usuario hace clic en una pestaña THEN el Sistema SHALL cambiar el contenido visible sin recargar toda la página
4. WHEN el usuario navega entre pestañas THEN el Sistema SHALL mantener la consistencia visual con el template del sistema ERP3
5. WHEN el Sistema muestra cada pestaña THEN el Sistema SHALL mostrar el total de registros existentes para esa sección
6. WHEN el módulo se carga por primera vez THEN el Sistema SHALL mostrar la pestaña "Categorías" como pestaña activa por defecto
7. WHEN el usuario está en cualquier pestaña THEN el Sistema SHALL mantener visible el encabezado del módulo y las pestañas de navegación

### Requirement 5: Integración con Base de Datos

**User Story:** Como desarrollador del sistema, quiero que el módulo se integre correctamente con la base de datos existente, para mantener la consistencia de datos y seguir los estándares del proyecto

#### Acceptance Criteria

1. WHEN el Sistema realiza operaciones de categorías THEN el Sistema SHALL utilizar la tabla mtto_categoria con los campos idcategoria (INT, PK, AUTO_INCREMENT) y nombreCategoria (VARCHAR)
2. WHEN el Sistema realiza operaciones de áreas THEN el Sistema SHALL utilizar la tabla mtto_almacen_area con los campos idArea (INT, PK, AUTO_INCREMENT) y Nombre_Area (VARCHAR)
3. WHEN el Sistema realiza operaciones de zonas THEN el Sistema SHALL crear y utilizar una tabla mtto_almacen_zona con campos apropiados
4. WHEN el Sistema ejecuta operaciones de base de datos THEN el Sistema SHALL utilizar la clase CRUD3 existente en el proyecto
5. WHEN el Sistema procesa peticiones THEN el Sistema SHALL seguir el patrón MVC utilizado en el proyecto (ctrl, mdl, js)
6. WHEN el Sistema maneja errores de base de datos THEN el Sistema SHALL retornar mensajes de error apropiados al usuario
7. WHEN el Sistema realiza inserciones o actualizaciones THEN el Sistema SHALL utilizar prepared statements para prevenir inyección SQL

### Requirement 6: Interfaz de Usuario y Experiencia

**User Story:** Como usuario del sistema, quiero una interfaz intuitiva y consistente, para poder gestionar el catálogo de manera eficiente

#### Acceptance Criteria

1. WHEN el Sistema muestra formularios de entrada THEN el Sistema SHALL utilizar los estilos de Bootstrap 5 consistentes con el resto del sistema
2. WHEN el Sistema muestra listas de datos THEN el Sistema SHALL utilizar tablas con DataTables para paginación y búsqueda
3. WHEN el usuario realiza una acción exitosa THEN el Sistema SHALL mostrar un mensaje de confirmación usando SweetAlert2
4. WHEN el usuario intenta eliminar un registro THEN el Sistema SHALL mostrar un diálogo de confirmación con Bootbox
5. WHEN el Sistema muestra mensajes de error THEN el Sistema SHALL utilizar iconos y colores apropiados para indicar el tipo de mensaje
6. WHEN el usuario interactúa con formularios THEN el Sistema SHALL validar los campos en tiempo real antes de enviar al servidor
7. WHEN el Sistema carga datos THEN el Sistema SHALL mostrar indicadores de carga apropiados durante las peticiones asíncronas

### Requirement 7: Seguridad y Validación

**User Story:** Como administrador del sistema, quiero que el módulo valide y proteja los datos, para mantener la integridad del sistema

#### Acceptance Criteria

1. WHEN el Sistema recibe peticiones HTTP THEN el Sistema SHALL validar que la sesión del usuario esté activa
2. WHEN el Sistema procesa datos de entrada THEN el Sistema SHALL sanitizar todos los inputs usando la clase Utileria
3. WHEN el Sistema ejecuta operaciones de eliminación THEN el Sistema SHALL verificar que el registro no tenga dependencias en otras tablas
4. WHEN el Sistema detecta intentos de inyección SQL THEN el Sistema SHALL rechazar la petición y registrar el intento
5. WHEN el Sistema maneja errores THEN el Sistema SHALL registrar los errores en logs sin exponer información sensible al usuario
6. WHEN el Sistema valida nombres duplicados THEN el Sistema SHALL realizar búsquedas case-insensitive en la base de datos
7. WHEN el Sistema procesa peticiones THEN el Sistema SHALL incluir headers CORS apropiados para permitir comunicación entre frontend y backend
