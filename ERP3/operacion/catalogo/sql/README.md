# Base de Datos - Módulo Catálogo

## Descripción

Este directorio contiene los scripts SQL necesarios para la creación y mantenimiento de la estructura de base de datos del módulo de Catálogo del almacén.

## Archivos

### schema.sql
Script principal que crea las tablas necesarias para el módulo:
- `mtto_categoria`: Categorías de materiales
- `mtto_almacen_area`: Áreas físicas del almacén
- `mtto_almacen_zona`: Zonas dentro de las áreas

## Estructura de Tablas

### mtto_categoria
| Campo | Tipo | Descripción |
|-------|------|-------------|
| idcategoria | INT (PK, AUTO_INCREMENT) | Identificador único |
| nombreCategoria | VARCHAR(255) | Nombre de la categoría |

**Índices:**
- PRIMARY KEY: idcategoria
- INDEX: idx_nombre_categoria (nombreCategoria)

### mtto_almacen_area
| Campo | Tipo | Descripción |
|-------|------|-------------|
| idArea | INT (PK, AUTO_INCREMENT) | Identificador único |
| Nombre_Area | VARCHAR(255) | Nombre del área |

**Índices:**
- PRIMARY KEY: idArea
- INDEX: idx_nombre_area (Nombre_Area)

### mtto_almacen_zona
| Campo | Tipo | Descripción |
|-------|------|-------------|
| idZona | INT (PK, AUTO_INCREMENT) | Identificador único |
| nombreZona | VARCHAR(255) | Nombre de la zona |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de actualización |

**Índices:**
- PRIMARY KEY: idZona
- INDEX: idx_nombre_zona (nombreZona)

## Instalación

### Opción 1: Desde línea de comandos MySQL

```bash
mysql -u usuario -p rfwsmqex_gvsl_finanzas2 < schema.sql
```

### Opción 2: Desde phpMyAdmin

1. Acceder a phpMyAdmin
2. Seleccionar la base de datos `rfwsmqex_gvsl_finanzas2`
3. Ir a la pestaña "SQL"
4. Copiar y pegar el contenido de `schema.sql`
5. Ejecutar

### Opción 3: Desde cliente MySQL

```sql
USE rfwsmqex_gvsl_finanzas2;
SOURCE /ruta/completa/a/schema.sql;
```

## Datos de Prueba

El script incluye datos de ejemplo que se insertan automáticamente si las tablas están vacías:

**Categorías:**
- Herramientas
- Materiales de Construcción
- Equipos de Seguridad

**Áreas:**
- Almacén Principal
- Almacén Secundario

**Zonas:**
- Zona A
- Zona B
- Zona C

**Nota:** Para producción, se recomienda comentar la sección de datos de prueba en el script.

## Verificación

Después de ejecutar el script, puedes verificar que las tablas se crearon correctamente:

```sql
-- Ver estructura de las tablas
SHOW TABLES LIKE 'mtto_%';

-- Ver detalles de cada tabla
DESCRIBE mtto_categoria;
DESCRIBE mtto_almacen_area;
DESCRIBE mtto_almacen_zona;

-- Ver índices
SHOW INDEX FROM mtto_categoria;
SHOW INDEX FROM mtto_almacen_area;
SHOW INDEX FROM mtto_almacen_zona;

-- Contar registros
SELECT COUNT(*) FROM mtto_categoria;
SELECT COUNT(*) FROM mtto_almacen_area;
SELECT COUNT(*) FROM mtto_almacen_zona;
```

## Mantenimiento

### Backup

Antes de realizar cambios en la estructura, siempre hacer backup:

```bash
mysqldump -u usuario -p rfwsmqex_gvsl_finanzas2 mtto_categoria mtto_almacen_area mtto_almacen_zona > backup_catalogo.sql
```

### Restauración

Para restaurar desde un backup:

```bash
mysql -u usuario -p rfwsmqex_gvsl_finanzas2 < backup_catalogo.sql
```

## Notas Importantes

1. **Charset**: Todas las tablas usan `utf8mb4` para soportar caracteres especiales y emojis
2. **Collation**: Se usa `utf8mb4_unicode_ci` para comparaciones case-insensitive
3. **Engine**: InnoDB para soporte de transacciones y claves foráneas
4. **Índices**: Se crean índices en los campos de nombre para optimizar búsquedas
5. **Timestamps**: La tabla de zonas incluye campos de auditoría (created_at, updated_at)

## Troubleshooting

### Error: Table already exists
Si recibes este error, las tablas ya existen. El script usa `CREATE TABLE IF NOT EXISTS` para evitar este problema.

### Error: Access denied
Verifica que el usuario tenga permisos CREATE, ALTER, INSERT en la base de datos.

### Error: Unknown database
Verifica que la base de datos `rfwsmqex_gvsl_finanzas2` exista.

## Contacto

Para reportar problemas o sugerencias sobre la estructura de base de datos, contactar al equipo de desarrollo.
