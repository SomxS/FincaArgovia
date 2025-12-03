# Paleta de Colores - Sistema ERP

## 🎨 Colores Principales

### Primary Blue - #2563EB
**Uso:** Botones principales, enlaces, elementos destacados
```css
background: #2563EB;
```
- **Hover:** #1d4ed8
- **Light:** #3b82f6
- **Dark:** #1e40af

**Clases disponibles:**
- `.bg-primary` - Fondo azul primario
- `.text-primary` - Texto azul primario
- `.btn-primary` - Botón azul primario
- `.badge-primary` - Badge azul
- `.border-primary` - Borde azul

---

### Secondary - Dark Slate #0F172A
**Uso:** Fondos secundarios, headers, navegación
```css
background: #0F172A;
```
- **Hover:** #1e293b
- **Light:** #334155
- **Lighter:** #475569

**Clases disponibles:**
- `.bg-secondary` - Fondo oscuro
- `.text-secondary` - Texto oscuro
- `.btn-secondary` - Botón oscuro
- `.badge-secondary` - Badge oscuro
- `.border-secondary` - Borde oscuro

---

### Success Green - #22C55E
**Uso:** Mensajes de éxito, estados activos, confirmaciones
```css
background: #22C55E;
```
- **Hover:** #16a34a
- **Light:** #4ade80
- **Dark:** #15803d

**Clases disponibles:**
- `.bg-success` - Fondo verde
- `.text-success` - Texto verde
- `.btn-success` - Botón verde
- `.badge-success` - Badge verde
- `.alert-success` - Alerta de éxito

---

### Danger Red - #EF4444
**Uso:** Errores, alertas, acciones destructivas
```css
background: #EF4444;
```
- **Hover:** #dc2626
- **Light:** #f87171
- **Dark:** #b91c1c

**Clases disponibles:**
- `.bg-danger` - Fondo rojo
- `.text-danger` - Texto rojo
- `.btn-danger` - Botón rojo
- `.badge-danger` - Badge rojo
- `.alert-danger` - Alerta de error

---

## 📦 Componentes

### Botones

```html
<!-- Botón Primary -->
<button class="btn-primary">Guardar</button>

<!-- Botón Secondary -->
<button class="btn-secondary">Cancelar</button>

<!-- Botón Success -->
<button class="btn-success">Confirmar</button>

<!-- Botón Danger -->
<button class="btn-danger">Eliminar</button>
```

### Badges

```html
<!-- Badge Primary -->
<span class="badge-primary">Nuevo</span>

<!-- Badge Success -->
<span class="badge-success">Activo</span>

<!-- Badge Danger -->
<span class="badge-danger">Inactivo</span>

<!-- Badge Secondary -->
<span class="badge-secondary">Pendiente</span>
```

### Alertas

```html
<!-- Alerta Primary -->
<div class="alert-primary">
    Información importante
</div>

<!-- Alerta Success -->
<div class="alert-success">
    Operación exitosa
</div>

<!-- Alerta Danger -->
<div class="alert-danger">
    Error en la operación
</div>

<!-- Alerta Secondary -->
<div class="alert-secondary">
    Nota informativa
</div>
```

### Cards

```html
<!-- Card Primary -->
<div class="card-primary p-4">
    <h3>Título</h3>
    <p>Contenido</p>
</div>

<!-- Card Success -->
<div class="card-success p-4">
    <h3>Título</h3>
    <p>Contenido</p>
</div>
```

---

## 🎯 Uso en Tablas

### Tema Primary
```javascript
this.createTable({
    attr: {
        theme: "light",
        // Los headers usarán automáticamente los colores de la paleta
    }
});
```

### Headers personalizados
```css
.table-primary thead {
    background-color: #2563EB;
    color: white;
}

.table-secondary thead {
    background-color: #0F172A;
    color: white;
}
```

---

## 🔧 Variables CSS

Todas las variables están definidas en `:root`:

```css
:root {
    /* Primary Blue */
    --primary-blue: #2563EB;
    --primary-blue-hover: #1d4ed8;
    
    /* Secondary Dark */
    --secondary-dark: #0F172A;
    --secondary-dark-hover: #1e293b;
    
    /* Success Green */
    --success-green: #22C55E;
    --success-green-hover: #16a34a;
    
    /* Danger Red */
    --danger-red: #EF4444;
    --danger-red-hover: #dc2626;
}
```

### Uso de variables

```css
.mi-elemento {
    background-color: var(--primary-blue);
    color: white;
}

.mi-elemento:hover {
    background-color: var(--primary-blue-hover);
}
```

---

## 📱 Responsive

Todas las clases son responsive y funcionan con TailwindCSS:

```html
<div class="bg-primary md:bg-secondary lg:bg-success">
    Responsive background
</div>
```

---

## ✨ Efectos y Animaciones

### Hover Effects

```html
<div class="hover-primary p-4">
    Hover para ver efecto
</div>
```

### Shadows

```html
<div class="shadow-primary p-4">
    Con sombra azul
</div>

<div class="shadow-success p-4">
    Con sombra verde
</div>
```

### Gradientes

```html
<div class="gradient-primary p-4">
    Gradiente azul
</div>

<div class="gradient-success p-4">
    Gradiente verde
</div>
```

---

## 📋 Checklist de Implementación

- [x] Variables CSS definidas
- [x] Clases de utilidad creadas
- [x] Botones estilizados
- [x] Badges implementados
- [x] Alertas configuradas
- [x] Cards con bordes de color
- [x] Efectos hover
- [x] Sombras personalizadas
- [x] Gradientes disponibles

---

## 🔗 Archivos Relacionados

- `src/css/color-palette.css` - Paleta completa de colores
- `src/css/style.css` - Estilos principales actualizados
- `src/js/coffeeSoft.js` - Temas de tablas

---

## 📝 Notas

- **Primary Blue (#2563EB)**: Color principal para acciones importantes
- **Dark Slate (#0F172A)**: Color secundario para fondos y navegación
- **Success Green (#22C55E)**: Para estados positivos y confirmaciones
- **Danger Red (#EF4444)**: Para errores y acciones destructivas

Todos los colores cumplen con los estándares de accesibilidad WCAG 2.1 AA.

---

**Actualizado:** 2025-01-23  
**Versión:** 1.0  
**Autor:** CoffeeIA ☕
