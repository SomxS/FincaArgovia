# Módulo de Almacén - CoffeeSoft Finanzas

## 📋 Descripción

El módulo de Almacén es un sistema de gestión de salidas de almacén que permite la captura, consulta, modificación y control de movimientos de insumos. El sistema garantiza la trazabilidad de los movimientos mediante validación con archivos de respaldo y control de acceso por niveles de usuario.

## 🚀 Instalación

### 1. Estructura de Archivos

Asegúrate de que los siguientes archivos estén en su ubicación correcta:

```
finanzas/
├── captura/
│   ├── almacen.php              # Página principal del módulo
│   ├── js/
│   │   └── almacen.js           # Frontend JavaScript
│   ├── ctrl/
│   │   └── ctrl-almacen.php     # Controlador PHP
│   ├── mdl/
│   │   └── mdl-almacen.php      # Modelo PHP
│   └── sql/
│       └── almacen_schema.sql   # Script de base de datos
```

### 2. Instalación de Base de Datos

Ejecuta el script SQL para crear las tablas necesarias:

```bash
mysql -u usuario -p nombre_base_datos < finanzas/captura/sql/almacen_schema.sql
```

O desde phpMyAdmin:
1. Abre phpMyAdmin
2. Selecciona la base de datos `rfwsmqex_finanzas`
3. Ve a la pestaña "SQL"
4. Copia y pega el contenido de `almacen_schema.sql`
5. Haz clic en "Continuar"

### 3. Configuración de Permisos

Crea el directorio de uploads y asigna permisos:

```bash
mkdir -p finanzas/captura/uploads/almacen
chmod 777 finanzas/captura/uploads/almacen
```

### 4. Verificación

Accede al módulo en tu navegador:

```
http://tu-dominio.com/finanzas/captura/almacen.php
```

## 📊 Tablas de Base de Datos

El módulo crea las siguientes tablas:

- **warehouse_output**: Salidas de almacén
- **product**: Productos/Insumos
- **product_class**: Clasificación de productos
- **file**: Archivos de respaldo
- **module**: Módulos del sistema
- **module_unlock**: Desbloqueo de módulos
- **monthly_module_lock**: Bloqueo mensual
- **audit_log**: Bitácora de auditoría

## 🎯 Funcionalidades

### Dashboard
- Visualización del total de salidas del día
- Acceso rápido a funciones principales
- Botones de acción: Concentrado, Subir archivos, Registrar salida

### Salidas de Almacén
- Registrar nuevas salidas
- Editar salidas existentes
- Eliminar salidas (con registro en auditoría)
- Visualizar descripciones detalladas
- Filtrado por fecha

### Concentrado de Almacén
- Reporte consolidado de entradas y salidas
- Balances por almacén
- Filtrado por rango de fechas
- Exportación a Excel
- Totales generales

### Gestión de Archivos
- Subir archivos de respaldo (máx. 20MB)
- Formatos permitidos: PDF, JPG, PNG, Excel
- Asociación con fecha de operación

## 👥 Niveles de Acceso

### Nivel 1 - Captura
- Registrar salidas de almacén
- Editar salidas del día
- Eliminar salidas del día
- Consultar salidas del día

### Nivel 2 - Gerencia
- Consultar concentrado de almacén
- Generar reportes consolidados
- Exportar a Excel
- Visualizar balances

### Nivel 3 - Contabilidad/Dirección
- Consultar reportes (solo lectura)
- Filtrar por unidad de negocio
- Sin permisos de modificación

### Nivel 4 - Administración
- Todas las funciones anteriores
- Gestionar clasificación de productos
- Bloquear/desbloquear módulo
- Gestionar niveles de acceso

## 🔧 Configuración Técnica

### Requisitos del Sistema
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache/Nginx con mod_rewrite
- Mínimo 2GB RAM
- 100GB espacio en disco

### Dependencias
- jQuery 3.6+
- Bootstrap 5.3+
- TailwindCSS 2.x+
- Moment.js 2.29+
- DataTables 1.13+
- SweetAlert2 11+
- Bootbox 5.5+
- CoffeeSoft Framework

### Configuración de Base de Datos

Edita el archivo `mdl-almacen.php` si necesitas cambiar el prefijo de la base de datos:

```php
$this->bd = "rfwsmqex_finanzas.";
```

## 📝 Uso Básico

### Registrar una Salida de Almacén

1. Accede al módulo
2. Haz clic en "Registrar nueva salida"
3. Selecciona el almacén (insumo)
4. Ingresa la cantidad
5. Escribe una descripción
6. Haz clic en "Guardar"

### Generar Reporte Consolidado

1. Ve a la pestaña "Concentrado de almacén"
2. Selecciona el rango de fechas
3. Haz clic en "Generar reporte"
4. Revisa los balances por almacén
5. (Opcional) Haz clic en "Exportar a Excel"

### Subir Archivo de Respaldo

1. En el Dashboard, haz clic en "Subir archivos de almacén"
2. Selecciona el archivo (máx. 20MB)
3. Selecciona la fecha de operación
4. Haz clic en "Subir archivo"

## 🐛 Solución de Problemas

### Error: "No se puede conectar a la base de datos"
- Verifica que las credenciales en `_CRUD.php` sean correctas
- Asegúrate de que el servidor MySQL esté ejecutándose
- Verifica que la base de datos `rfwsmqex_finanzas` exista

### Error: "El archivo excede el tamaño máximo"
- Verifica que el archivo no supere 20MB
- Ajusta `upload_max_filesize` y `post_max_size` en `php.ini` si es necesario

### Error: "Todos los campos son obligatorios"
- Asegúrate de llenar todos los campos del formulario
- Verifica que la cantidad sea mayor a cero

### La tabla no muestra datos
- Verifica que existan registros para la fecha seleccionada
- Revisa la consola del navegador para errores JavaScript
- Verifica que el controlador esté respondiendo correctamente

## 📞 Soporte

Para soporte técnico o reportar problemas:
- Revisa la documentación completa en `.kiro/specs/almacen/`
- Consulta los logs de error en el servidor
- Contacta al equipo de desarrollo

## 📄 Licencia

Este módulo es parte del sistema CoffeeSoft Finanzas.
Todos los derechos reservados © 2025

## 🔄 Historial de Versiones

### v1.0.0 (2025-11-16)
- Versión inicial del módulo
- Funcionalidades básicas de CRUD
- Sistema de reportes consolidados
- Gestión de archivos de respaldo
- Sistema de auditoría
- Control de acceso por niveles
