# Implementation Plan - Módulo Catálogo

- [x] 1. Crear estructura de base de datos



  - Crear script SQL para la tabla mtto_almacen_zona
  - Verificar que las tablas mtto_categoria y mtto_almacen_area existan
  - Agregar índices para optimización de consultas
  - _Requirements: 5.1, 5.2, 5.3_



- [ ] 2. Implementar capa de modelo (mdl-catalogo.php)
  - [x] 2.1 Crear clase base y configuración

    - Extender de CRUD y configurar conexión a base de datos
    - Inicializar clase Utileria para sanitización
    - _Requirements: 5.4, 5.5_
  
  - [ ] 2.2 Implementar métodos para Categorías
    - Implementar listCategorias() para obtener todas las categorías
    - Implementar getCategoriaById() para obtener categoría específica
    - Implementar existsCategoria() para verificar duplicados

    - Implementar createCategoria() para insertar nueva categoría
    - Implementar deleteCategoriaById() para eliminar categoría
    - Implementar countCategoriasUsage() para verificar dependencias
    - _Requirements: 1.5, 1.9, 2.7_
  
  - [ ] 2.3 Implementar métodos para Áreas
    - Implementar listAreas() para obtener todas las áreas
    - Implementar getAreaById() para obtener área específica

    - Implementar existsArea() para verificar duplicados
    - Implementar createArea() para insertar nueva área
    - Implementar deleteAreaById() para eliminar área
    - Implementar countAreasUsage() para verificar dependencias
    - _Requirements: 2.4, 2.9, 2.7_
  
  - [ ] 2.4 Implementar métodos para Zonas
    - Implementar listZonas() para obtener todas las zonas
    - Implementar getZonaById() para obtener zona específica


    - Implementar existsZona() para verificar duplicados
    - Implementar createZona() para insertar nueva zona
    - Implementar deleteZonaById() para eliminar zona
    - Implementar countZonasUsage() para verificar dependencias

    - _Requirements: 3.4, 3.8, 2.7_

- [ ] 3. Implementar capa de controlador (ctrl-catalogo.php)
  - [ ] 3.1 Configurar controlador base
    - Configurar headers CORS
    - Implementar validación de sesión

    - Extender de mdl y configurar routing
    - _Requirements: 7.1, 7.7_
  
  - [ ] 3.2 Implementar endpoints para Categorías
    - Implementar lsCategorias() con formato para tabla
    - Implementar addCategoria() con validación y sanitización

    - Implementar deleteCategoria() con verificación de dependencias
    - Implementar manejo de errores específico
    - _Requirements: 1.3, 1.5, 1.6, 1.7, 1.9, 1.10, 5.6, 7.2_
  
  - [ ] 3.3 Implementar endpoints para Áreas
    - Implementar lsAreas() con formato para tabla

    - Implementar addArea() con validación de duplicados
    - Implementar deleteArea() con verificación de dependencias
    - Implementar manejo de errores específico


    - _Requirements: 2.3, 2.4, 2.5, 2.6, 2.7, 2.9, 2.10, 5.6, 7.2_
  
  - [ ] 3.4 Implementar endpoints para Zonas
    - Implementar lsZonas() con formato para tabla
    - Implementar addZona() con validación y sanitización
    - Implementar deleteZona() con verificación de dependencias


    - Implementar manejo de errores específico
    - _Requirements: 3.3, 3.4, 3.5, 3.6, 3.8, 3.9, 5.6, 7.2_
  

  - [ ] 3.5 Implementar método init()
    - Retornar datos iniciales necesarios para el frontend
    - _Requirements: 5.5_


- [ ] 4. Implementar capa de vista (catalogo.php)
  - Crear estructura HTML base
  - Incluir referencias a CSS (Bootstrap 5, DataTables, SweetAlert2)
  - Incluir referencias a JavaScript (jQuery, plugins, catalogo.js)
  - Crear div root para renderizado dinámico

  - _Requirements: 4.1, 6.1, 6.2_

- [ ] 5. Implementar frontend - Clase CatalogoManager
  - [ ] 5.1 Crear clase base CatalogoManager
    - Extender de Templates

    - Implementar constructor con configuración
    - _Requirements: 4.1, 5.5_
  
  - [ ] 5.2 Implementar método render()
    - Inicializar módulo y componentes
    - Llamar a layout() y layoutHeader()

    - _Requirements: 4.1_
  
  - [ ] 5.3 Implementar método layout()
    - Crear estructura principal con primaryLayout

    - Crear contenedores para filterBar y container
    - Llamar a layoutTabs()
    - _Requirements: 4.1, 4.2_
  

  - [ ] 5.4 Implementar método layoutHeader()
    - Crear encabezado con título "Catálogo"
    - Agregar descripción del módulo
    - Agregar botón de regreso al menú principal
    - _Requirements: 4.1_

  
  - [ ] 5.5 Implementar método layoutTabs()
    - Crear tres pestañas: Categorías, Áreas, Zonas
    - Configurar pestaña Categorías como activa por defecto

    - Configurar onClick handlers para cada pestaña
    - _Requirements: 4.2, 4.6, 4.7_

- [ ] 6. Implementar frontend - Clase CategoriasManager
  - [ ] 6.1 Crear clase CategoriasManager
    - Extender de Templates

    - Implementar constructor
    - _Requirements: 1.1_
  
  - [ ] 6.2 Implementar método render()
    - Llamar a layout()
    - Llamar a lsCategorias()
    - _Requirements: 1.1_

  
  - [ ] 6.3 Implementar método layout()
    - Crear estructura HTML para sección de categorías
    - Crear contenedores para filterbar y tabla
    - Llamar a filterBar()
    - _Requirements: 1.2_

  
  - [ ] 6.4 Implementar método filterBar()
    - Crear botón "Agregar Categoría"
    - Configurar onClick para addCategoria()
    - _Requirements: 1.2_
  
  - [ ] 6.5 Implementar método lsCategorias()
    - Hacer petición AJAX a lsCategorias endpoint
    - Renderizar tabla con DataTables

    - Mostrar ID, Nombre y botón Eliminar
    - Configurar paginación y búsqueda
    - _Requirements: 1.7, 6.2_
  

  - [ ] 6.6 Implementar método addCategoria()
    - Crear modal con formulario usando createModalForm
    - Implementar validación de campo vacío
    - Enviar petición POST a addCategoria endpoint

    - Manejar respuesta y actualizar lista
    - Mostrar mensajes de éxito/error
    - _Requirements: 1.2, 1.3, 1.4, 1.5, 1.6, 6.3, 6.6_
  
  - [x] 6.7 Implementar método deleteCategoria(id)

    - Mostrar diálogo de confirmación con swalQuestion
    - Enviar petición POST a deleteCategoria endpoint
    - Manejar respuesta y actualizar lista
    - Mostrar mensajes de éxito/error

    - _Requirements: 1.8, 1.9, 1.10, 6.3, 6.4_
  
  - [ ] 6.8 Implementar método jsonCategoriaForm()
    - Definir estructura del formulario con campo nombreCategoria
    - Configurar validación required
    - _Requirements: 1.2, 1.3_

  
  - [ ]* 6.9 Escribir test de propiedad para validación de nombres de categorías
    - **Property 1: Entity name validation**
    - **Validates: Requirements 1.3**

- [ ] 7. Implementar frontend - Clase AreasManager
  - [x] 7.1 Crear clase AreasManager

    - Extender de Templates
    - Implementar constructor
    - _Requirements: 2.1_
  
  - [ ] 7.2 Implementar método render()
    - Llamar a layout()

    - Llamar a lsAreas()
    - _Requirements: 2.1_
  
  - [ ] 7.3 Implementar método layout()
    - Crear estructura HTML para sección de áreas
    - Crear contenedores para filterbar y tabla
    - Llamar a filterBar()
    - _Requirements: 2.2_
  

  - [ ] 7.4 Implementar método filterBar()
    - Crear botón "Agregar Área"
    - Configurar onClick para addArea()
    - _Requirements: 2.2_

  
  - [ ] 7.5 Implementar método lsAreas()
    - Hacer petición AJAX a lsAreas endpoint
    - Renderizar tabla con DataTables

    - Mostrar ID, Nombre y botón Eliminar
    - Configurar paginación y búsqueda
    - _Requirements: 2.6, 6.2_
  
  - [x] 7.6 Implementar método addArea()

    - Crear modal con formulario usando createModalForm
    - Implementar validación de campo vacío
    - Enviar petición POST a addArea endpoint
    - Manejar respuesta (incluyendo duplicados)

    - Actualizar lista y mostrar mensajes
    - _Requirements: 2.2, 2.3, 2.4, 2.5, 2.7, 6.3, 6.6_
  
  - [ ] 7.7 Implementar método deleteArea(id)
    - Mostrar diálogo de confirmación con swalQuestion
    - Enviar petición POST a deleteArea endpoint

    - Manejar respuesta y actualizar lista
    - Mostrar mensajes de éxito/error
    - _Requirements: 2.8, 2.9, 2.10, 6.3, 6.4_
  
  - [ ] 7.8 Implementar método jsonAreaForm()
    - Definir estructura del formulario con campo Nombre_Area
    - Configurar validación required

    - _Requirements: 2.2, 2.3_
  
  - [ ]* 7.9 Escribir test de propiedad para prevención de duplicados
    - **Property 5: Duplicate prevention**
    - **Validates: Requirements 2.7, 7.6**


- [ ] 8. Implementar frontend - Clase ZonasManager
  - [ ] 8.1 Crear clase ZonasManager
    - Extender de Templates
    - Implementar constructor


    - _Requirements: 3.1_
  
  - [ ] 8.2 Implementar método render()
    - Llamar a layout()
    - Llamar a lsZonas()
    - _Requirements: 3.1_
  
  - [ ] 8.3 Implementar método layout()
    - Crear estructura HTML para sección de zonas
    - Crear contenedores para filterbar y tabla
    - Llamar a filterBar()
    - _Requirements: 3.2_
  
  - [ ] 8.4 Implementar método filterBar()
    - Crear botón "Agregar Zona"
    - Configurar onClick para addZona()
    - _Requirements: 3.2_
  
  - [ ] 8.5 Implementar método lsZonas()
    - Hacer petición AJAX a lsZonas endpoint
    - Renderizar tabla con DataTables
    - Mostrar ID, Nombre y botón Eliminar
    - Configurar paginación y búsqueda
    - _Requirements: 3.6, 6.2_
  
  - [ ] 8.6 Implementar método addZona()
    - Crear modal con formulario usando createModalForm
    - Implementar validación de campo vacío
    - Enviar petición POST a addZona endpoint
    - Manejar respuesta y actualizar lista
    - Mostrar mensajes de éxito/error
    - _Requirements: 3.2, 3.3, 3.4, 3.5, 6.3, 6.6_
  
  - [ ] 8.7 Implementar método deleteZona(id)
    - Mostrar diálogo de confirmación con swalQuestion
    - Enviar petición POST a deleteZona endpoint
    - Manejar respuesta y actualizar lista
    - Mostrar mensajes de éxito/error
    - _Requirements: 3.7, 3.8, 3.9, 6.3, 6.4_
  
  - [ ] 8.8 Implementar método jsonZonaForm()
    - Definir estructura del formulario con campo nombreZona
    - Configurar validación required
    - _Requirements: 3.2, 3.3_

- [ ] 9. Implementar inicialización del módulo
  - Crear función async IIFE en catalogo.js
  - Inicializar CatalogoManager
  - Inicializar CategoriasManager, AreasManager, ZonasManager
  - Configurar API endpoints
  - _Requirements: 4.1, 4.2, 4.6_

- [ ]* 10. Escribir tests de propiedad para CRUD
  - [ ]* 10.1 Escribir test de propiedad para round-trip de categorías
    - **Property 2: CRUD round-trip consistency**
    - **Validates: Requirements 1.5, 1.6**
  
  - [ ]* 10.2 Escribir test de propiedad para round-trip de áreas
    - **Property 2: CRUD round-trip consistency**
    - **Validates: Requirements 2.4, 2.5**
  
  - [ ]* 10.3 Escribir test de propiedad para round-trip de zonas
    - **Property 2: CRUD round-trip consistency**
    - **Validates: Requirements 3.4, 3.5**

- [ ]* 11. Escribir tests de propiedad para renderizado
  - [ ]* 11.1 Escribir test de propiedad para renderizado completo de categorías
    - **Property 3: Complete data rendering**
    - **Validates: Requirements 1.7**
  
  - [ ]* 11.2 Escribir test de propiedad para renderizado completo de áreas
    - **Property 3: Complete data rendering**
    - **Validates: Requirements 2.6**
  
  - [ ]* 11.3 Escribir test de propiedad para renderizado completo de zonas
    - **Property 3: Complete data rendering**
    - **Validates: Requirements 3.6**

- [ ]* 12. Escribir tests de propiedad para eliminación
  - [ ]* 12.1 Escribir test de propiedad para consistencia de eliminación
    - **Property 4: Deletion consistency**
    - **Validates: Requirements 1.9, 1.10, 2.9, 2.10, 3.8, 3.9**

- [ ]* 13. Escribir tests de propiedad para navegación SPA
  - [ ]* 13.1 Escribir test de propiedad para consistencia de navegación
    - **Property 6: SPA navigation consistency**
    - **Validates: Requirements 4.3, 4.5, 4.7**

- [ ]* 14. Escribir tests de propiedad para seguridad
  - [ ]* 14.1 Escribir test de propiedad para prevención de SQL injection
    - **Property 7: SQL injection prevention**
    - **Validates: Requirements 5.7, 7.2, 7.4**
  
  - [ ]* 14.2 Escribir test de propiedad para validación de sesión
    - **Property 8: Session validation**
    - **Validates: Requirements 7.1**
  
  - [ ]* 14.3 Escribir test de propiedad para protección de integridad referencial
    - **Property 9: Referential integrity protection**
    - **Validates: Requirements 7.3**

- [ ]* 15. Escribir tests de propiedad para feedback de usuario
  - [ ]* 15.1 Escribir test de propiedad para consistencia de mensajes
    - **Property 10: User feedback consistency**
    - **Validates: Requirements 6.3, 5.6, 7.5**
  
  - [ ]* 15.2 Escribir test de propiedad para validación client-side
    - **Property 11: Client-side validation**
    - **Validates: Requirements 6.6**
  
  - [ ]* 15.3 Escribir test de propiedad para indicadores de carga
    - **Property 12: Loading state indication**
    - **Validates: Requirements 6.7**
  
  - [ ]* 15.4 Escribir test de propiedad para diálogos de confirmación
    - **Property 13: Confirmation dialog for destructive actions**
    - **Validates: Requirements 6.4**

- [ ] 16. Checkpoint - Verificar funcionalidad básica
  - Ensure all tests pass, ask the user if questions arise.

- [ ]* 17. Escribir tests unitarios para casos específicos
  - [ ]* 17.1 Escribir test unitario para carga inicial del módulo
    - Verificar que las tres pestañas se renderizan
    - Verificar que Categorías es la pestaña activa por defecto
    - _Requirements: 4.2, 4.6_
  
  - [ ]* 17.2 Escribir test unitario para validación de campos vacíos
    - Verificar rechazo de strings vacíos
    - Verificar rechazo de strings solo con espacios
    - _Requirements: 1.4_
  
  - [ ]* 17.3 Escribir test unitario para manejo de errores del servidor
    - Simular error 500 del servidor
    - Verificar que se muestra mensaje apropiado
    - _Requirements: 5.6_

- [ ] 18. Integración y pruebas finales
  - Verificar integración con el menú principal del sistema
  - Probar flujo completo de cada entidad (agregar, listar, eliminar)
  - Verificar navegación entre pestañas
  - Probar manejo de errores en diferentes escenarios
  - Verificar responsividad en diferentes tamaños de pantalla
  - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [ ] 19. Documentación y deployment
  - Crear archivo README con instrucciones de uso
  - Documentar estructura de archivos
  - Crear script de migración de base de datos
  - Preparar checklist de deployment
  - _Requirements: All_

- [ ] 20. Checkpoint final
  - Ensure all tests pass, ask the user if questions arise.
