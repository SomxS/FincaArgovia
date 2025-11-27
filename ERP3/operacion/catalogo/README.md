# Módulo Catálogo - ERP3

## Descripción

El módulo de Catálogo permite administrar la estructura organizacional del almacén mediante la gestión de tres entidades principales:

- **Categorías**: Clasificación principal de materiales e insumos
- **Áreas**: Espacios físicos del almacén
- **Zonas**: Subdivisiones dentro de las áreas

## Estructura de Archivos

```
ERP3/operacion/catalogo/
├── catalogo.php              # Vista principal
├── ctrl/
│   └── ctrl-catalogo.php     # Controlador (API REST)
├── mdl/
│   └── mdl-catalogo.php      # Modelo (acceso a datos)
├── js/
│   └── catalogo.js           # Frontend (JavaScript)
├── layout/
│   ├── head.php              # Encabezado HTML
│   └── core-libraries.php    # Librerías JavaScript
├── sql/
│   ├── schema.sql            # Script de base de datos
│   └── README.md             # Documentación de BD
└── README.md                 # Este archivo
```

## Instalación

### 1. Base de Datos

Ejecutar el script SQL para crear las tablas necesarias:

```bash
mysql -u usuario -p rfwsmqex_gvsl_finanzas2 < sql/schema.sql
```

O desde phpMyAdmin:
1. Seleccionar la base de datos `rfwsmqex_gvsl_finanzas2`
2. Ir a la pestaña "SQL"
3. Copiar y pegar el contenido de `sql/schema.sql`
4. Ejecutar

### 2. Archivos

Todos los archivos ya están en su ubicación correcta en:
```
ERP3/operacion/catalogo/
```

### 3. Acceso

Acceder al módulo desde:
```
http://tu-dominio/ERP3/operacion/catalogo/catalogo.php
```

## Uso

### Gestión de Categorías

1. Acceder a la pestaña "Categorías"
2. Hacer clic en "Agregar Categoría"
3. Ingresar el nombre de la categoría
4. Guardar

Para eliminar:
- Hacer clic en el botón de eliminar (🗑️) en la tabla
- Confirmar la eliminación

### Gestión de Áreas

1. Acceder a la pestaña "Áreas"
2. Hacer clic en "Agregar Área"
3. Ingresar el nombre del área
4. Guardar

Para eliminar:
- Hacer clic en el botón de eliminar (🗑️) en la tabla
- Confirmar la eliminación

### Gestión de Zonas

1. Acceder a la pestaña "Zonas"
2. Hacer clic en "Agregar Zona"
3. Ingresar el nombre de la zona
4. Guardar

Para eliminar:
- Hacer clic en el botón de eliminar (🗑️) en la tabla
- Confirmar la eliminación

## Características

### Validaciones

- **Campos vacíos**: No se permiten nombres vacíos o solo con espacios
- **Duplicados**: No se permiten nombres duplicados (case-insensitive)
- **Dependencias**: No se puede eliminar una entidad si está siendo utilizada

### Interfaz

- **Pestañas**: Navegación fluida entre Categorías, Áreas y Zonas
- **Tablas**: DataTables con búsqueda, ordenamiento y paginación
- **Modales**: Formularios en ventanas modales para agregar
- **Confirmaciones**: Diálogos de confirmación para eliminaciones
- **Mensajes**: Notificaciones de éxito y error con SweetAlert2

### Seguridad

- Sanitización de inputs con clase Utileria
- Prepared statements para prevenir SQL injection
- Validación de sesión (comentada por defecto)
- Manejo de errores sin exponer información sensible

## API Endpoints

### Categorías

- `lsCategorias`: Lista todas las categorías
- `addCategoria`: Agrega una nueva categoría
- `deleteCategoria`: Elimina una categoría

### Áreas

- `lsAreas`: Lista todas las áreas
- `addArea`: Agrega una nueva área
- `deleteArea`: Elimina un área

### Zonas

- `lsZonas`: Lista todas las zonas
- `addZona`: Agrega una nueva zona
- `deleteZona`: Elimina una zona

## Formato de Respuesta API

### Éxito
```json
{
    "status": 200,
    "message": "Operación exitosa",
    "row": [...],
    "ls": [...]
}
```

### Error
```json
{
    "status": 400|404|409|500,
    "message": "Descripción del error"
}
```

## Códigos de Estado

- `200`: Operación exitosa
- `400`: Error de validación (campo vacío, etc.)
- `404`: Entidad no encontrada
- `409`: Conflicto (duplicado, en uso, etc.)
- `500`: Error del servidor

## Tecnologías Utilizadas

### Backend
- PHP 7.4+
- MySQL 5.7+
- PDO para acceso a datos

### Frontend
- JavaScript ES6+
- jQuery 3.7.0
- Bootstrap 5
- DataTables
- SweetAlert2
- Bootbox
- Moment.js
- Tailwind CSS

### Framework
- CoffeeSoft (framework interno)
- Templates class (componentes reutilizables)

## Dependencias

El módulo requiere las siguientes clases del sistema:

- `CRUD` (ERP3/conf/_CRUD3.php)
- `Utileria` (ERP3/conf/_Utileria.php)
- `Templates` (CoffeeSoft framework)

## Troubleshooting

### Error: "No se puede conectar a la base de datos"
- Verificar que la base de datos `rfwsmqex_gvsl_finanzas2` existe
- Verificar credenciales en `ERP3/conf/_Conect2.php`

### Error: "Table doesn't exist"
- Ejecutar el script `sql/schema.sql`
- Verificar que las tablas se crearon correctamente

### Error: "No se puede eliminar"
- La entidad está siendo utilizada por registros en `mtto_almacen`
- Eliminar primero las dependencias o cambiar la referencia

### La interfaz no carga
- Verificar que todos los archivos JavaScript están en su lugar
- Revisar la consola del navegador para errores
- Verificar que las rutas en `catalogo.php` son correctas

## Mantenimiento

### Logs

Los errores se registran en el log de PHP con el prefijo `[CATALOGO]`:

```php
error_log("[CATALOGO] Error en operación: " . $mensaje);
```

### Backup

Hacer backup regular de las tablas:

```bash
mysqldump -u usuario -p rfwsmqex_gvsl_finanzas2 mtto_categoria mtto_almacen_area mtto_almacen_zona > backup_catalogo.sql
```

## Soporte

Para reportar problemas o solicitar mejoras, contactar al equipo de desarrollo.

## Versión

- **Versión**: 1.0.0
- **Fecha**: 2024
- **Autor**: Equipo de Desarrollo ERP3
