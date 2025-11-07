# Implementation Plan - Módulo de Compras

## Task List

- [x] 1. Configurar estructura base del proyecto



  - Crear estructura de carpetas en finanzas/captura/
  - Configurar archivos base (index.php, compras.js, ctrl-compras.php, mdl-compras.php)
  - Incluir dependencias de CoffeeSoft (coffeSoft.js, plugins.js)
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 2. Crear esquema de base de datos
  - [ ] 2.1 Crear tabla purchase con campos y relaciones
    - Definir estructura con campos: id, udn_id, product_class_id, product_id, purchase_type_id, supplier_id, method_pay_id, subtotal, tax, total, description, operation_date, active, created_at, updated_at
    - Establecer foreign keys con tablas relacionadas
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8_
  
  - [ ] 2.2 Crear tablas de catálogos (product_class, product, purchase_type, method_pay, supplier)
    - Crear tabla product_class con campos: id, udn_id, name, description, active
    - Crear tabla product con campos: id, product_class_id, name, active
    - Crear tabla purchase_type con datos iniciales (Fondo fijo, Corporativo, Crédito)
    - Crear tabla method_pay con datos iniciales (Efectivo, Tarjeta de crédito, Transferencia, Cheque)
    - Crear tabla supplier con campos: id, udn_id, name, active
    - _Requirements: 2.2, 2.3, 2.4_
  
  - [ ] 2.3 Crear tablas de control (module_unlock, monthly_module_lock, audit_log, file)
    - Crear tabla module_unlock para control de bloqueo de módulos
    - Crear tabla monthly_module_lock para bloqueo mensual
    - Crear tabla audit_log para registro de auditoría
    - Crear tabla file para archivos adjuntos
    - _Requirements: 6.4, 6.5, 6.6, 6.8_

- [ ] 3. Implementar modelo de datos (mdl-compras.php)
  - [ ] 3.1 Configurar clase base y propiedades
    - Extender clase CRUD
    - Configurar propiedad $bd = "rfwsmqex_finanzas."
    - Inicializar $util = new Utileria
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_
  
  - [ ] 3.2 Implementar métodos de consulta de compras
    - Crear método listCompras() con joins a todas las tablas relacionadas
    - Implementar filtros por fecha, tipo_compra, metodo_pago, udn, active
    - Crear método getCompraById() para obtener compra específica
    - _Requirements: 1.5, 4.1, 4.2, 4.3, 4.4, 4.5_
  
  - [ ] 3.3 Implementar métodos CRUD de compras
    - Crear método createCompra() para insertar nuevas compras
    - Crear método updateCompra() para actualizar compras existentes
    - Crear método deleteCompraById() para eliminar compras
    - _Requirements: 2.7, 3.3, 3.5_
  
  - [ ] 3.4 Implementar métodos de catálogos
    - Crear método lsProductClass() para listar clases de producto
    - Crear método lsProductByClass() para productos filtrados por clase
    - Crear método lsPurchaseType() para tipos de compra
    - Crear método lsMethodPay() para métodos de pago
    - Crear método lsSupplier() para proveedores
    - Crear método lsUDN() para unidades de negocio
    - _Requirements: 2.2, 2.3, 2.4_
  
  - [ ] 3.5 Implementar métodos de validación y cálculos
    - Crear método getSaldoFondoFijo() para calcular saldo actual
    - Crear método existsReembolso() para verificar reembolsos asociados
    - Crear método getModuleLockStatus() para verificar bloqueo de módulo
    - _Requirements: 2.8, 3.6, 6.5, 6.6, 6.7_
  
  - [ ] 3.6 Implementar métodos de reporte concentrado
    - Crear método listConcentrado() con agrupación por clase y día
    - Implementar cálculos de SUM(subtotal), SUM(tax), SUM(total)
    - _Requirements: 5.2, 5.3, 5.4_
  
  - [ ] 3.7 Implementar método de auditoría
    - Crear método createAuditLog() para registrar cambios
    - _Requirements: 6.8_

- [ ] 4. Implementar controlador (ctrl-compras.php)
  - [ ] 4.1 Configurar clase base y método init()
    - Extender clase mdl
    - Crear método init() que retorne datos para filtros (UDN, clases de producto, tipos de compra, métodos de pago, proveedores)
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_
  
  - [ ] 4.2 Implementar método ls() para listar compras
    - Recibir parámetros: fi, ff, tipo_compra, metodo_pago, udn
    - Llamar a listCompras() del modelo
    - Formatear datos para tabla con dropdown de acciones
    - Aplicar funciones auxiliares (formatSpanishDate, evaluar, renderStatus)
    - _Requirements: 1.5, 4.4, 4.5_
  
  - [ ] 4.3 Implementar método getCompra()
    - Recibir id por POST
    - Llamar a getCompraById() del modelo
    - Retornar estructura con status, message, data
    - _Requirements: 3.7_
  
  - [ ] 4.4 Implementar método addCompra()
    - Recibir datos del formulario por POST
    - Validar saldo de fondo fijo si tipo_compra = 'Fondo fijo'
    - Llamar a createCompra() del modelo
    - Registrar en audit_log
    - Retornar status y message
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7, 2.8_
  
  - [ ] 4.5 Implementar método editCompra()
    - Recibir id y datos modificados por POST
    - Validar bloqueo de módulo
    - Validar si existe reembolso asociado
    - Llamar a updateCompra() del modelo
    - Registrar en audit_log
    - Retornar status y message
    - _Requirements: 3.2, 3.3, 3.4, 3.6, 3.7_
  
  - [ ] 4.6 Implementar método deleteCompra()
    - Recibir id por POST
    - Validar permisos de usuario
    - Validar que no tenga reembolsos asociados
    - Llamar a deleteCompraById() del modelo
    - Registrar en audit_log
    - Retornar status y message
    - _Requirements: 3.4, 3.5_
  
  - [ ] 4.7 Implementar método statusCompra()
    - Recibir id y active por POST
    - Llamar a updateCompra() del modelo
    - Retornar status y message
    - _Requirements: 3.3_
  
  - [ ] 4.8 Implementar método lsConcentrado()
    - Recibir parámetros: fi, ff, tipo_compra, udn
    - Llamar a listConcentrado() del modelo
    - Formatear datos para reporte
    - _Requirements: 5.1, 5.2, 5.3, 5.4_
  
  - [ ] 4.9 Implementar método toggleModuleLock()
    - Validar perfil de Contabilidad
    - Recibir lock_status por POST
    - Actualizar estado de bloqueo en module_unlock
    - Registrar en audit_log
    - _Requirements: 6.4, 6.5, 6.6_
  
  - [ ] 4.10 Crear funciones auxiliares
    - Implementar función dropdown() para generar opciones de acción
    - Implementar función renderStatus() para badges de estado
    - Implementar función formatPurchaseType() para formatear tipo de compra
    - _Requirements: 1.5, 3.1, 3.7_

- [ ] 5. Implementar frontend base (compras.js)
  - [ ] 5.1 Configurar clase App y propiedades
    - Extender clase Templates de CoffeeSoft
    - Definir PROJECT_NAME = "compras"
    - Configurar _link y _div_modulo
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_
  
  - [ ] 5.2 Implementar método render() y layout()
    - Crear método render() que ejecute layout(), filterBar() y ls()
    - Implementar layout() con primaryLayout y tabLayout
    - Configurar 3 tabs: Compras, Concentrado de compras, Archivos
    - _Requirements: 1.1, 1.2, 1.3, 1.4_
  
  - [ ] 5.3 Implementar filterBar() con filtros dinámicos
    - Crear filtro de rango de fechas con dataPicker
    - Crear filtro de tipo de compra (select)
    - Crear filtro de método de pago (select, visible solo para Corporativo)
    - Implementar lógica para mostrar/ocultar filtro de método de pago
    - _Requirements: 4.1, 4.2, 4.3, 4.4_
  
  - [ ] 5.4 Implementar método ls() para tabla de compras
    - Usar createTable() de CoffeeSoft
    - Configurar tema corporativo
    - Definir columnas: Folio, Clase producto, Producto, Tipo de compra, Total, Acciones
    - Implementar actualización en tiempo real
    - _Requirements: 1.5, 4.4, 4.5_

- [ ] 6. Implementar formularios de compras
  - [ ] 6.1 Crear método addCompra() con modal
    - Usar createModalForm() de CoffeeSoft
    - Definir campos: Categoría de producto, Producto, Tipo de compra, Proveedor, Método de pago, Subtotal, Impuesto, Descripción
    - Implementar lógica para mostrar/ocultar campos según tipo de compra
    - Configurar validaciones de campos requeridos
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_
  
  - [ ] 6.2 Implementar cálculo automático de totales
    - Agregar evento onkeyup en campo Subtotal
    - Calcular impuesto automáticamente (16%)
    - Calcular total (subtotal + impuesto)
    - Actualizar campos en tiempo real
    - _Requirements: 2.6, 2.7_
  
  - [ ] 6.3 Implementar validación de saldo de fondo fijo
    - Verificar saldo disponible antes de guardar
    - Mostrar mensaje de error si saldo insuficiente
    - Bloquear submit si no hay saldo
    - _Requirements: 2.8_
  
  - [ ] 6.4 Crear método editCompra() con modal
    - Usar createModalForm() con autofill
    - Cargar datos de la compra con useFetch()
    - Aplicar restricciones según bloqueo de módulo
    - Bloquear campos monto y tipo si existe reembolso
    - _Requirements: 3.1, 3.2, 3.3, 3.6_
  
  - [ ] 6.5 Implementar método jsonCompra() para estructura de formulario
    - Definir estructura JSON con todos los campos
    - Configurar tipos de input (select, input, textarea)
    - Establecer clases de Bootstrap para layout responsive
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_

- [ ] 7. Implementar acciones de compras
  - [ ] 7.1 Crear método deleteCompra() con confirmación
    - Usar swalQuestion() de CoffeeSoft
    - Mostrar mensaje "¿Esta seguro de querer eliminar la compra?"
    - Enviar petición al backend con opc: "deleteCompra"
    - Actualizar tabla en tiempo real después de eliminar
    - _Requirements: 3.4, 3.5_
  
  - [ ] 7.2 Crear método viewDetails() para modal de detalles
    - Usar detailCard() de CoffeeSoft
    - Mostrar información completa de la compra
    - Incluir secciones: Información del producto, Tipo de compra, Método de pago, Información de facturación, Descripción, Resumen financiero
    - _Requirements: 3.7_
  
  - [ ] 7.3 Implementar método statusCompra() para activar/desactivar
    - Usar swalQuestion() para confirmación
    - Enviar petición con opc: "statusCompra"
    - Actualizar tabla después de cambiar estado
    - _Requirements: 3.3_

- [ ] 8. Implementar reporte concentrado
  - [ ] 8.1 Crear método lsConcentrado() para vista de reporte
    - Usar createTable() con configuración especial
    - Agrupar datos por clase de producto y día
    - Mostrar columnas dinámicas por cada día del rango
    - _Requirements: 5.1, 5.2, 5.3_
  
  - [ ] 8.2 Implementar cálculo de totales por día y clase
    - Calcular subtotales por clase de producto
    - Calcular impuestos por clase de producto
    - Calcular totales por día
    - Mostrar gran total del período
    - _Requirements: 5.3_
  
  - [ ] 8.3 Implementar visualización de saldo de fondo fijo
    - Mostrar saldo inicial del fondo fijo
    - Mostrar salidas del período
    - Calcular y mostrar saldo final
    - _Requirements: 5.4_
  
  - [ ] 8.4 Crear funcionalidad de exportación a Excel
    - Agregar botón "Exportar a Excel"
    - Implementar generación de archivo Excel con datos del concentrado
    - Incluir formato y estilos en el archivo exportado
    - _Requirements: 5.5_
  
  - [ ] 8.5 Implementar modal de detalles por clase y día
    - Crear modal que muestre compras específicas al hacer clic en un total
    - Mostrar lista detallada de compras de esa clase en ese día
    - _Requirements: 5.6_

- [ ] 9. Implementar sistema de permisos
  - [ ] 9.1 Configurar validación de perfiles en frontend
    - Verificar perfil de usuario al cargar módulo
    - Mostrar/ocultar botones según permisos
    - Deshabilitar acciones no permitidas
    - _Requirements: 6.1, 6.2, 6.3, 6.4_
  
  - [ ] 9.2 Implementar validación de permisos en backend
    - Verificar perfil antes de ejecutar operaciones críticas
    - Retornar error 403 si no tiene permisos
    - _Requirements: 6.1, 6.2, 6.3, 6.4_
  
  - [ ] 9.3 Crear método toggleModuleLock() para bloqueo de módulo
    - Mostrar botón solo para perfil de Contabilidad
    - Implementar toggle para bloquear/desbloquear
    - Mostrar estado actual del módulo
    - Registrar acción en audit_log
    - _Requirements: 6.4, 6.5, 6.6_
  
  - [ ] 9.4 Implementar restricciones de edición según bloqueo
    - Verificar estado de bloqueo antes de editar
    - Bloquear campos monto y tipo de compra si módulo bloqueado
    - Mostrar mensaje informativo sobre restricciones
    - _Requirements: 6.5, 6.6, 6.7_

- [ ] 10. Implementar sistema de auditoría
  - [ ] 10.1 Configurar registro automático en audit_log
    - Registrar todas las operaciones de insert, update, delete
    - Capturar usuario, fecha, tabla, registro afectado
    - Guardar cambios realizados en formato JSON
    - _Requirements: 6.8_
  
  - [ ] 10.2 Implementar visualización de historial de cambios
    - Crear modal para ver historial de una compra
    - Mostrar lista de cambios con fecha, usuario y acción
    - Permitir filtrar por tipo de acción
    - _Requirements: 6.8_

- [ ] 11. Implementar gestión de archivos
  - [ ] 11.1 Crear pestaña de Archivos
    - Implementar layout para tab de archivos
    - Mostrar tabla de archivos subidos
    - _Requirements: 1.1_
  
  - [ ] 11.2 Implementar subida de archivos
    - Crear botón "Subir archivos de compras"
    - Implementar modal con input de tipo file
    - Validar tipos de archivo permitidos (PDF, Excel, imágenes)
    - Guardar archivo en servidor y registro en tabla file
    - _Requirements: 1.4_
  
  - [ ] 11.3 Implementar descarga y visualización de archivos
    - Agregar botón de descarga en tabla de archivos
    - Implementar preview para imágenes y PDFs
    - _Requirements: 1.4_

- [ ] 12. Implementar visualización de totales en tiempo real
  - [ ] 12.1 Crear cards de totales en header
    - Mostrar total de compras del día
    - Mostrar total de compras de fondo fijo
    - Mostrar total de compras corporativas
    - Mostrar total de compras a crédito
    - Mostrar saldo actual de fondo fijo
    - _Requirements: 1.2, 1.3_
  
  - [ ] 12.2 Implementar actualización automática de totales
    - Recalcular totales al agregar/editar/eliminar compra
    - Actualizar cards sin recargar página
    - Aplicar animación de actualización
    - _Requirements: 1.3, 1.5_

- [ ] 13. Implementar validaciones y manejo de errores
  - [ ] 13.1 Configurar validaciones de frontend
    - Validar campos requeridos en formularios
    - Validar formato de números y fechas
    - Mostrar mensajes de error claros
    - _Requirements: 2.7, 2.8_
  
  - [ ] 13.2 Implementar manejo de errores del backend
    - Capturar errores de validación
    - Mostrar mensajes de error con alert() de CoffeeSoft
    - Manejar errores de conexión
    - _Requirements: 2.7, 2.8, 3.3, 3.5_

- [ ] 14. Optimización y refinamiento
  - [ ] 14.1 Optimizar consultas de base de datos
    - Agregar índices en columnas de búsqueda frecuente
    - Optimizar joins en consultas complejas
    - _Requirements: 1.5, 4.4, 4.5, 5.2_
  
  - [ ] 14.2 Implementar carga lazy de datos
    - Cargar datos de tabla con paginación
    - Implementar scroll infinito si es necesario
    - _Requirements: 1.5_
  
  - [ ] 14.3 Mejorar experiencia de usuario
    - Agregar loaders durante peticiones AJAX
    - Implementar animaciones suaves en transiciones
    - Mejorar feedback visual de acciones
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 15. Testing y validación final
  - [ ] 15.1 Realizar pruebas de integración
    - Probar flujo completo de registro de compra
    - Probar flujo de edición con restricciones
    - Probar filtros dinámicos
    - Probar generación de reporte concentrado
    - _Requirements: Todos_
  
  - [ ] 15.2 Validar permisos por perfil
    - Probar con usuario de Captura
    - Probar con usuario de Gerencia
    - Probar con usuario de Dirección
    - Probar con usuario de Contabilidad
    - _Requirements: 6.1, 6.2, 6.3, 6.4_
  
  - [ ] 15.3 Validar cálculos financieros
    - Verificar cálculo de impuestos
    - Verificar cálculo de totales
    - Verificar cálculo de saldo de fondo fijo
    - Verificar totales en reporte concentrado
    - _Requirements: 2.6, 2.7, 2.8, 5.3, 5.4_
  
  - [ ] 15.4 Realizar pruebas de responsive
    - Probar en desktop (1920x1080, 1366x768)
    - Probar en tablet (768x1024)
    - Probar en móvil (375x667, 414x896)
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

- [ ] 16. Documentación y entrega
  - [ ] 16.1 Documentar código
    - Agregar comentarios en funciones complejas
    - Documentar estructura de base de datos
    - Crear guía de uso del módulo
    - _Requirements: Todos_
  
  - [ ] 16.2 Preparar datos de prueba
    - Insertar datos de ejemplo en catálogos
    - Crear compras de prueba de cada tipo
    - Configurar perfiles de usuario de prueba
    - _Requirements: Todos_
  
  - [ ] 16.3 Realizar demo con usuario final
    - Presentar funcionalidades principales
    - Recopilar feedback
    - Realizar ajustes finales
    - _Requirements: Todos_
