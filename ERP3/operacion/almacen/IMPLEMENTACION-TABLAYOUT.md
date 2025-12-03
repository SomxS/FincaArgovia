# Implementación de tabLayout con Lucide Icons - Módulo Catálogo

## ✅ Archivos Modificados

### 1. `operacion/almacen/index.php`
**Cambios realizados:**
- ✅ Agregada librería Lucide Icons
- ✅ Incluido componente tabLayout.js
- ✅ Cargado script catalogo.js

```php
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>

<!-- CoffeeSoft Framework -->
<script src="../../src/js/coffeeSoft.js"></script>
<script src="https://rawcdn.githack.com/SomxS/Grupo-Varoch/refs/heads/main/src/js/plugins.js"></script>
<script src="https://www.plugins.erp-varoch.com/ERP/JS/complementos.js"></script>

<!-- Componente tabLayout -->
<script src="../../src/js/components/tabLayout.js?t=<?php echo time(); ?>"></script>

<!-- Módulo de Catálogo -->
<script src="js/catalogo.js?t=<?php echo time(); ?>"></script>
```

### 2. `operacion/almacen/js/catalogo.js`
**Cambios realizados:**
- ✅ Implementado tabLayout con iconos de Lucide
- ✅ Configurado tema dark
- ✅ Agregados iconos específicos para cada tab

```javascript
tabLayout({
    parent: `container${this.PROJECT_NAME}`,
    id: `tabs${this.PROJECT_NAME}`,
    theme: "dark",
    type: "button",
    json: [
        {
            id: "categorias",
            tab: "Categorías",
            icon: "folder-tree",  // 📁 Icono de árbol de carpetas
            class: "mb-1",
            active: true,
            onClick: () => category.lsCategory()
        },
        {
            id: "areas",
            tab: "Áreas",
            icon: "map-pin",  // 📍 Icono de pin de ubicación
            onClick: () => area.lsArea()
        },
        {
            id: "zonas",
            tab: "Zonas",
            icon: "map",  // 🗺️ Icono de mapa
            onClick: () => zone.lsZone()
        }
    ]
});
```

### 3. `src/js/components/tabLayout.js` (NUEVO)
**Componente creado con:**
- ✅ Soporte completo para iconos Lucide
- ✅ Temas light y dark
- ✅ Renderizado automático de contenedores
- ✅ Inicialización automática de iconos
- ✅ Transiciones suaves
- ✅ Responsive design con TailwindCSS

### 4. `src/js/components/README-tabLayout.md` (NUEVO)
**Documentación completa con:**
- ✅ Guía de instalación
- ✅ Ejemplos de uso
- ✅ Tabla de opciones
- ✅ Lista de iconos disponibles
- ✅ Configuración de temas

## 🎨 Iconos Implementados

| Tab | Icono | Nombre Lucide | Descripción |
|-----|-------|---------------|-------------|
| Categorías | 📁 | `folder-tree` | Árbol de carpetas para clasificación |
| Áreas | 📍 | `map-pin` | Pin de ubicación para espacios físicos |
| Zonas | 🗺️ | `map` | Mapa para subdivisiones |

## 🎯 Características del Componente

### Tema Dark (Implementado)
- Fondo oscuro: `#1F2A37`
- Tab activo: `#374151`
- Texto claro: `text-gray-400`
- Transiciones suaves

### Funcionalidades
- ✅ Cambio automático de tabs
- ✅ Iconos renderizados con Lucide
- ✅ Contenedores generados automáticamente
- ✅ Primer tab activo por defecto
- ✅ Callbacks onClick personalizados

## 📋 Estructura de Contenedores Generados

El componente genera automáticamente:

```html
<div id="tabscatalogo">
  <!-- Tabs -->
  <div class="flex gap-2 border-b">
    <button data-tab="categorias">
      <i data-lucide="folder-tree"></i>
      <span>Categorías</span>
    </button>
    <!-- ... más tabs -->
  </div>
  
  <!-- Contenedores -->
  <div id="content-tabscatalogo">
    <div id="container-categorias"></div>
    <div id="container-areas" style="display: none;"></div>
    <div id="container-zonas" style="display: none;"></div>
  </div>
</div>
```

## 🔧 Cómo Usar en Otros Módulos

### Paso 1: Incluir Lucide Icons
```html
<script src="https://unpkg.com/lucide@latest"></script>
```

### Paso 2: Incluir el Componente
```html
<script src="../../src/js/components/tabLayout.js"></script>
```

### Paso 3: Implementar en tu Código
```javascript
tabLayout({
    parent: "miContenedor",
    id: "misTabs",
    theme: "dark", // o "light"
    json: [
        {
            id: "tab1",
            tab: "Mi Tab",
            icon: "home", // Cualquier icono de Lucide
            active: true,
            onClick: () => console.log("Tab 1")
        }
    ]
});
```

## 🌐 Iconos Lucide Disponibles

### Navegación
- `home`, `menu`, `settings`, `search`, `filter`

### Almacén
- `warehouse`, `box`, `package`, `truck`, `layers`

### Ubicación
- `map`, `map-pin`, `navigation`, `compass`

### Organización
- `folder`, `folder-tree`, `grid-3x3`, `list`

### Acciones
- `plus`, `edit`, `trash`, `eye`, `download`

**Ver todos:** https://lucide.dev/icons/

## ✨ Ventajas de la Implementación

1. **Modular**: Componente reutilizable en cualquier módulo
2. **Flexible**: Fácil cambio de iconos y temas
3. **Ligero**: Lucide es una librería optimizada
4. **Mantenible**: Código limpio y documentado
5. **Escalable**: Fácil agregar más tabs

## 🚀 Próximos Pasos

Para agregar más tabs:

```javascript
json: [
    // ... tabs existentes
    {
        id: "productos",
        tab: "Productos",
        icon: "package",
        onClick: () => productos.lsProductos()
    }
]
```

## 📝 Notas Importantes

- Los iconos se inicializan automáticamente con `lucide.createIcons()`
- Los contenedores siguen el patrón `container-{id}`
- El tema se puede cambiar entre "light" y "dark"
- Compatible con TailwindCSS
- No requiere CSS adicional

## 🐛 Troubleshooting

### Los iconos no se muestran
- Verificar que Lucide esté cargado antes del componente
- Revisar la consola del navegador
- Confirmar que el nombre del icono sea válido

### Los tabs no cambian
- Verificar que los callbacks onClick estén definidos
- Revisar que los IDs de los tabs sean únicos
- Confirmar que los contenedores existan

### Estilos no se aplican
- Verificar que TailwindCSS esté cargado
- Revisar que no haya conflictos de CSS
- Confirmar que las clases de Tailwind sean válidas

---

**Implementado por:** CoffeeIA ☕  
**Fecha:** 2025-01-23  
**Versión:** 1.0  
**Estado:** ✅ Completado y Funcional
