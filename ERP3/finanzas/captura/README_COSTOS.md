# Módulo de Costos - Concentrado Diario

## 📋 Descripción

Módulo de consulta financiera de **solo lectura** que consolida información de costos directos (del módulo Compras) y salidas de almacén (del módulo Almacén) en un concentrado diario.

## ✅ Funcionalidades Implementadas

### 🔍 Consulta de Concentrado Diario
- Visualización de costos directos y salidas de almacén por rango de fechas
- Agrupación por categoría de producto (product_class)
- Cálculo automático de totales por categoría
- Cálculo de total general
- Validación de rango de fechas (máximo 12 meses)

### 🔐 Control de Permisos
- **Nivel 2**: Consulta básica sin filtro de UDN
- **Nivel 3**: Consulta con filtro por Unidad de Negocio (UDN)
- Modo de solo lectura estricto (sin botones de edición/eliminación)

### 📊 Exportación a Excel
- Generación de archivo Excel con datos filtrados
- Formato profesional con encabezados
- Formato de moneda en columnas de importes
- Nombre de archivo con timestamp

### 🛡️ Seguridad
- Validación de operaciones permitidas (init, ls, exportExcel)
- Sanitización de parámetros de entrada
- Validación de formato de fechas
- Validación de existencia de UDN
- Prevención de SQL injection con prepared statements

## 📁 Estructura de Archivos

```
finanzas/captura/
├── costos.php              # Punto de entrada del módulo
├── js/
│   └── costos.js          # Frontend (App class)
├── ctrl/
│   └── ctrl-costos.php    # Controlador
└── mdl/
    └── mdl-costos.php     # Modelo
```

## 🗄️ Tablas de Base de Datos Utilizadas

- `product_class` - Clasificación de productos
- `purchases` - Compras (costos directos)
- `warehouse_output` - Salidas de almacén
- `product` - Productos
- `usuarios` - Usuarios (para filtro UDN)

## 🚀 Uso del Módulo

### Acceso
Navegar a: `finanzas/captura/costos.php`

### Flujo de Uso
1. Seleccionar rango de fechas (máximo 12 meses)
2. (Opcional - Nivel 3) Seleccionar Unidad de Negocio
3. Ver concentrado diario en tabla
4. Exportar a Excel si es necesario

### Validaciones Automáticas
- Fecha inicial ≤ Fecha final
- Rango máximo de 12 meses
- Formato de fecha válido (YYYY-MM-DD)
- UDN existente (si aplica)

## 🔧 Configuración

### Variables de Sesión Requeridas
```php
$_SESSION['idUser']   // ID del usuario
$_SESSION['nivel']    // Nivel de usuario (2 o 3)
```

### Base de Datos
```php
$this->bd = "rfwsmqex_finanzas.";
```

### Dependencias PHP
- PHPSpreadsheet (para exportación a Excel)
- Composer autoloader

## 📊 Formato de Datos

### Respuesta de Consulta (ls)
```json
{
  "row": [
    {
      "Categoría": "Alimentos",
      "Fecha": "25 de noviembre del 2025",
      "Costo Directo": {
        "html": "$11,870.00",
        "class": "text-end"
      },
      "Salida Almacén": {
        "html": "$2,170.00",
        "class": "text-end"
      },
      "Total": {
        "html": "$14,040.00",
        "class": "text-end"
      },
      "opc": 0
    }
  ],
  "thead": ""
}
```

### Respuesta de Exportación
```json
{
  "status": 200,
  "file": "temp/concentrado_costos_20251117123045.xlsx",
  "message": "Archivo Excel generado correctamente"
}
```

## 🎨 Componentes CoffeeSoft Utilizados

- `primaryLayout()` - Layout principal
- `createfilterBar()` - Barra de filtros
- `dataPicker()` - Selector de rango de fechas
- `createTable()` - Tabla de datos
- `useFetch()` - Peticiones AJAX
- `alert()` - Mensajes al usuario

## 🔒 Modo de Solo Lectura

El módulo está diseñado para **consulta únicamente**:
- ❌ Sin botones de edición
- ❌ Sin botones de eliminación
- ❌ Sin formularios de captura
- ❌ Operaciones de modificación bloqueadas en backend
- ✅ Solo operaciones: init, ls, exportExcel

## 📝 Notas Técnicas

### Agrupación de Datos
Los datos se agrupan por `product_class` y se ordenan por fecha descendente dentro de cada categoría.

### Cálculo de Totales
- **Totales por categoría**: Suma de todos los registros de la misma categoría
- **Total general**: Suma de todos los registros del período

### Optimización
- Uso de LEFT JOIN para consolidar datos
- Agregación en SQL (SUM, GROUP BY)
- Índices recomendados (ver tasks.md - tarea 9.1)

## 🐛 Troubleshooting

### Error: "Formato de fecha inválido"
- Verificar que las fechas estén en formato YYYY-MM-DD
- Verificar que el rango de fechas sea válido

### Error: "Unidad de negocio no encontrada"
- Verificar que la UDN exista en la tabla usuarios
- Verificar que el usuario tenga nivel 3

### Error: "Operación no permitida"
- El módulo solo permite operaciones de consulta
- Verificar que la operación sea: init, ls o exportExcel

### No se genera el Excel
- Verificar que PHPSpreadsheet esté instalado
- Verificar que la carpeta temp/ tenga permisos de escritura
- Verificar que el autoloader de Composer esté configurado

## 📚 Referencias

- Especificación completa: `.kiro/specs/modulo-costos/`
- Pivote de referencia: `pivote analitycs.md`
- Documentación CoffeeSoft: `DOC COFFEESOFT.md`

## ✨ Próximas Mejoras (Opcionales)

- [ ] Índices de base de datos para mejor rendimiento
- [ ] Optimización de queries SQL
- [ ] Documentación de usuario con screenshots
- [ ] Documentación técnica detallada

---

**Versión**: 1.0.0  
**Fecha**: Noviembre 2025  
**Estado**: ✅ Implementación Core Completada
