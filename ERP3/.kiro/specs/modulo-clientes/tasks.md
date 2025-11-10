# Implementation Plan - Módulo de Clientes

## 1. Configuración inicial del proyecto

- [ ] 1.1 Crear estructura de directorios del módulo
  - Crear carpeta `finanzas/captura/` si no existe
  - Crear subcarpetas `ctrl/`, `mdl/`, `js/`
  - _Requirements: 1.1, 1.2_

- [ ] 1.2 Crear archivo index principal
  - Crear `finanzas/captura/clientes.php` con estructura HTML base
  - Incluir referencias a CoffeeSoft (`src/js/coffeSoft.js`, `src/js/plugins.js`)
  - Agregar `<div id="root"></div>` como contenedor principal
  - _Requirements: 1.1_

- [ ] 1.3 Crear tablas de base de datos
  - Ejecutar script SQL para crear tabla `clientes`
  - Ejecutar script SQL para crear tabla `movimientos_credito`
  - Ejecutar script SQL para crear tabla `audit_log_clientes`
  - Crear índices recomendados para optimización
  - _Requirements: 2.5, 3.4, 4.6_

## 2. Implementar modelo de datos (mdl-clientes.php)

- [ ] 2.1 Crear clase base del modelo
  - Crear archivo `finanzas/captura/mdl/mdl-clientes.php`
  - Extender clase CRUD
  - Configurar propiedades `$bd` y `$util`
  - _Requirements: 2.5, 3.2_

- [ ] 2.2 Implementar métodos de consulta de clientes
  - Crear método `lsClientes()` para listar clientes activos
  - Crear método `getClienteById($id)` para obtener cliente específico
  - Crear método `getDeudaActual($clienteId)` para calcular deuda actual
  - _Requirements: 2.1, 4.2_

- [ ] 2.3 Implementar métodos CRUD de movimientos
  - Crear método `listMovimientos($params)` con filtros de fecha y UDN
  - Crear método `getMovimientoById($id)` para obtener movimiento específico
  - Crear método `createMovimiento($data)` para insertar nuevo movimiento
  - Crear método `updateMovimiento($data)` para actualizar movimiento
  - Crear método `deleteMovimientoById($id)` para soft delete
  - _Requirements: 2.5, 3.2, 3.5_

- [ ] 2.4 Implementar métodos de concentrado y reportes
  - Crear método `listConcentrado($params)` para generar datos de reporte
  - Crear método `getTotalesPorFecha($fecha, $udn)` para calcular totales
  - _Requirements: 6.1, 6.3_

- [ ] 2.5 Implementar métodos de auditoría
  - Crear método `logAuditoria($data)` para registrar acciones
  - _Requirements: 3.4_

## 3. Implementar controlador (ctrl-clientes.php)

- [ ] 3.1 Crear clase base del controlador
  - Crear archivo `finanzas/captura/ctrl/ctrl-clientes.php`
  - Extender clase mdl
  - Configurar validación de `$_POST['opc']`
  - _Requirements: 2.5, 3.2_

- [ ] 3.2 Implementar método init()
  - Crear método `init()` que retorne listas de clientes, tipos de movimiento y métodos de pago
  - _Requirements: 2.1_

- [ ] 3.3 Implementar métodos de listado
  - Crear método `ls()` para listar movimientos del día con filtros
  - Formatear datos para tabla con columnas: Cliente, Tipo, Método, Monto, Acciones
  - Incluir botones de acción (ver, editar, eliminar)
  - _Requirements: 1.4, 1.5, 1.6_

- [ ] 3.4 Implementar métodos de gestión de movimientos
  - Crear método `getMovimiento()` para obtener datos de un movimiento
  - Crear método `addMovimiento()` con validación de campos obligatorios
  - Crear método `editMovimiento()` con validación de datos
  - Crear método `deleteMovimiento()` con registro en auditoría
  - _Requirements: 2.1, 2.5, 2.7, 3.1, 3.2, 3.4_

- [ ] 3.5 Implementar métodos de concentrado
  - Crear método `lsConcentrado()` para generar reporte de balances
  - Crear método `exportExcel()` para preparar datos de exportación
  - _Requirements: 6.1, 6.4, 6.5_

- [ ] 3.6 Implementar funciones auxiliares
  - Crear función `dropdown($id, $status)` para menú de acciones
  - Crear función `renderStatus($status)` para badges de estado
  - Crear función `checkUserLevel($level)` para validación de permisos
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

## 4. Implementar frontend principal (clientes.js)

- [ ] 4.1 Crear clase App principal
  - Crear archivo `finanzas/captura/js/clientes.js`
  - Extender clase Templates de CoffeeSoft
  - Definir `PROJECT_NAME = "clientes"`
  - Implementar constructor con `link` y `div_modulo`
  - _Requirements: 1.1_

- [ ] 4.2 Implementar método render() y layout()
  - Crear método `render()` que inicialice layout, filterBar y dashboard
  - Crear método `layout()` usando `primaryLayout` con filterBar y container
  - Implementar `tabLayout` con pestañas: Dashboard, Concentrado
  - _Requirements: 1.1, 1.2_

- [ ] 4.3 Implementar filterBar principal
  - Crear método `filterBar()` con selector de fecha usando `dataPicker`
  - Agregar filtro de tipo de movimiento (consumos, pagos, anticipos)
  - Agregar botones "Concentrado de clientes" y "Registrar nuevo movimiento"
  - _Requirements: 1.3, 1.5_

## 5. Implementar Dashboard de captura

- [ ] 5.1 Crear método ls() para tabla de movimientos
  - Implementar método `ls()` usando `createTable` de CoffeeSoft
  - Configurar columnas: Cliente, Tipo de movimiento, Método de pago, Monto, Acciones
  - Aplicar tema corporativo con TailwindCSS
  - Habilitar DataTables con paginación de 15 registros
  - _Requirements: 1.4, 1.6_

- [ ] 5.2 Implementar tarjetas de totales
  - Crear método `updateTotales()` para mostrar tarjetas con totales
  - Mostrar: Total de consumos, Total pagos efectivo, Total pagos banco
  - Usar componente `infoCard` de CoffeeSoft
  - _Requirements: 1.2_

- [ ] 5.3 Implementar formulario de nuevo movimiento
  - Crear método `addMovimiento()` usando `createModalForm`
  - Implementar método `jsonMovimiento()` con estructura del formulario
  - Campos: Cliente (select), Deuda actual (readonly), Tipo movimiento (select), Método pago (select), Cantidad (input), Descripción (textarea)
  - _Requirements: 2.1, 2.2, 2.3, 2.4_

- [ ] 5.4 Implementar lógica de tipo de movimiento
  - Agregar evento onChange en selector de tipo de movimiento
  - Si tipo es "Consumo", establecer método de pago como "N/A" y deshabilitar campo
  - Si tipo es "Abono parcial" o "Pago total", habilitar selector con opciones "Efectivo" y "Banco"
  - _Requirements: 2.2, 2.3_

- [ ] 5.5 Implementar formulario de edición
  - Crear método `editMovimiento(id)` usando `createModalForm`
  - Obtener datos del movimiento con `useFetch` y `opc: 'getMovimiento'`
  - Precargar formulario con `autofill`
  - _Requirements: 3.1, 3.2_

- [ ] 5.6 Implementar eliminación de movimientos
  - Crear método `deleteMovimiento(id)` usando `swalQuestion`
  - Mostrar confirmación con mensaje "¿Esta seguro de querer eliminar el movimiento a crédito?"
  - Enviar petición con `opc: 'deleteMovimiento'`
  - _Requirements: 3.3, 3.4, 3.5_

- [ ] 5.7 Implementar vista de detalle
  - Crear método `viewDetalle(id)` para mostrar modal con información completa
  - Secciones: Información del cliente, Detalles del movimiento, Descripción, Resumen financiero
  - Incluir información de auditoría (última actualización y usuario)
  - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_

## 6. Implementar módulo de Concentrado

- [ ] 6.1 Crear clase Concentrado
  - Crear clase `Concentrado extends App` en clientes.js
  - Implementar método `render()` y `layout()`
  - _Requirements: 6.1_

- [ ] 6.2 Implementar filterBar de concentrado
  - Crear método `filterBarConcentrado()` con selector de rango de fechas
  - Agregar filtro de unidad de negocio (para usuarios nivel 3+)
  - Agregar botón "Exportar a Excel"
  - _Requirements: 6.4, 6.7_

- [ ] 6.3 Implementar tabla de concentrado
  - Crear método `lsConcentrado()` usando `createTable`
  - Columnas: Cliente, Saldo inicial, Consumos por día (fondo verde), Pagos por día (fondo naranja), Saldo final
  - Implementar filas expandibles por cliente
  - _Requirements: 6.1, 6.2, 6.6_

- [ ] 6.4 Implementar filas de resumen
  - Agregar fila de totales: Saldo inicial, Total consumos, Total pagos, Saldo final
  - Aplicar estilos diferenciados para resumen
  - _Requirements: 6.3_

- [ ] 6.5 Implementar exportación a Excel
  - Crear método `exportExcel()` que llame al backend
  - Generar descarga automática del archivo
  - _Requirements: 6.5_

## 7. Implementar sistema de permisos

- [ ] 7.1 Implementar validación de niveles en frontend
  - Crear objeto `PERMISOS` con matriz de permisos por nivel
  - Implementar método `checkPermiso(accion)` en clase App
  - Ocultar/deshabilitar botones según permisos del usuario
  - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

- [ ] 7.2 Implementar validación de niveles en backend
  - Crear método `checkUserLevel($level)` en controlador
  - Aplicar validación en métodos críticos (add, edit, delete)
  - Retornar error 401 si permisos insuficientes
  - _Requirements: 5.5, 5.6_

## 8. Integración y pruebas

- [ ] 8.1 Integrar con módulo de Ventas
  - Verificar sincronización de totales de consumos y pagos
  - Actualizar módulo de Ventas al registrar movimientos
  - _Requirements: 1.7_

- [ ] 8.2 Pruebas de validación de formularios
  - Verificar validación de campos obligatorios
  - Probar lógica de tipo de movimiento y método de pago
  - Validar cálculo de nueva deuda
  - _Requirements: 2.4, 2.7, 2.8_

- [ ] 8.3 Pruebas de permisos por nivel
  - Probar acceso con usuario nivel 1 (Captura)
  - Probar acceso con usuario nivel 2 (Gerencia)
  - Probar acceso con usuario nivel 3 (Contabilidad)
  - Probar acceso con usuario nivel 4 (Administración)
  - _Requirements: 5.1, 5.2, 5.3, 5.4_

- [ ] 8.4 Pruebas de concentrado y exportación
  - Verificar cálculo de balances por cliente
  - Probar filtro de rango de fechas
  - Validar exportación a Excel
  - _Requirements: 6.1, 6.3, 6.4, 6.5_

- [ ]* 8.5 Pruebas de performance
  - Medir tiempo de carga de tabla con 100+ registros
  - Verificar tiempo de respuesta de peticiones AJAX
  - Optimizar consultas SQL si es necesario
  - _Requirements: Performance_

- [ ]* 8.6 Pruebas de seguridad
  - Verificar sanitización de inputs
  - Probar prevención de SQL injection
  - Validar protección XSS
  - Verificar registro de auditoría
  - _Requirements: Security_
