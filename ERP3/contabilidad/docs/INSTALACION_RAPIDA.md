# 🚀 Instalación Rápida - Módulo de Compras

## ⚡ Pasos de Instalación

### 1️⃣ Importar Base de Datos

```bash
# Acceder a MySQL
mysql -u root -p

# Seleccionar base de datos
USE rfwsmqex_erp;

# Importar estructura
source finanzas/docs/compras_database.sql;
```

O desde phpMyAdmin:
1. Seleccionar base de datos `rfwsmqex_erp`
2. Ir a pestaña "Importar"
3. Seleccionar archivo `compras_database.sql`
4. Click en "Continuar"

### 2️⃣ Verificar Configuración

Editar `finanzas/captura/mdl/mdl-compras.php`:

```php
public function __construct() {
    $this->util = new Utileria;
    $this->bd = "rfwsmqex_erp.";  // ← Verificar nombre de BD
}
```

### 3️⃣ Verificar Rutas

Editar `finanzas/captura/compras.php`:

```php
require_once '../../conf/_conf.php';  // ← Verificar ruta correcta
```

```html
<script src="../../src/js/coffeSoft.js"></script>  <!-- ← Verificar ruta -->
<script src="../../src/js/plugins.js"></script>    <!-- ← Verificar ruta -->
```

### 4️⃣ Datos de Prueba (Opcional)

```sql
-- Insertar categorías de productos
INSERT INTO product_class (name, description, active) VALUES
('Activo fijo', 'Equipos y mobiliario', 1),
('Costo directo', 'Insumos de producción', 1),
('Costo indirecto', 'Gastos operativos', 1),
('Alimentos', 'Productos alimenticios', 1),
('Bebidas', 'Bebidas y líquidos', 1),
('Gastos de administración', 'Gastos administrativos', 1),
('Lavandería', 'Servicios de lavandería', 1),
('Gastos operativos', 'Gastos de operación', 1),
('Gastos en mantenimiento', 'Mantenimiento y reparaciones', 1),
('Gastos en publicidad', 'Marketing y publicidad', 1);

-- Insertar productos de ejemplo
INSERT INTO product (product_class_id, name, active) VALUES
(1, 'Computadora', 1),
(1, 'Escritorio', 1),
(2, 'Materia prima', 1),
(3, 'Papelería', 1),
(4, 'Alimentos varios', 1),
(5, 'Bebidas', 1),
(6, 'Servicio de internet', 1),
(7, 'Agua', 1),
(8, 'Gasolina', 1),
(9, 'Fumigación', 1),
(10, 'Publicidad', 1);

-- Insertar proveedores de ejemplo
INSERT INTO supplier (name, active) VALUES
('Proveedor A', 1),
('Proveedor B', 1),
('Proveedor C', 1);
```

### 5️⃣ Acceder al Módulo

```
http://localhost/tu-proyecto/finanzas/captura/compras.php
```

O si usas WAMP/XAMPP:

```
http://localhost:8080/tu-proyecto/finanzas/captura/compras.php
```

## ✅ Verificación de Instalación

### Checklist

- [ ] Base de datos importada correctamente
- [ ] Tablas creadas (purchase, product_class, product, etc.)
- [ ] Datos iniciales insertados (purchase_type, method_pay)
- [ ] Archivo `compras.php` accesible desde navegador
- [ ] No hay errores en consola del navegador (F12)
- [ ] Filtros se cargan correctamente
- [ ] Botón "Nueva Compra" abre modal
- [ ] Selector de categoría funciona
- [ ] Selector de producto se carga dinámicamente

## 🐛 Solución de Problemas Comunes

### Error: "Cannot read property 'productClass' of undefined"

**Causa:** El método `init()` no está devolviendo datos

**Solución:**
1. Verificar que `ctrl-compras.php` esté en la ruta correcta
2. Verificar conexión a base de datos en `mdl-compras.php`
3. Revisar consola de red (F12 → Network) para ver respuesta del servidor

### Error: "Table 'rfwsmqex_erp.purchase' doesn't exist"

**Causa:** Base de datos no importada

**Solución:**
1. Importar `compras_database.sql`
2. Verificar nombre de base de datos en `mdl-compras.php`

### Error: "Failed to load resource: 404"

**Causa:** Rutas incorrectas en archivos

**Solución:**
1. Verificar rutas en `compras.php`:
   - `../../conf/_conf.php`
   - `../../src/js/coffeSoft.js`
   - `../../src/js/plugins.js`
2. Verificar ruta de API en `compras.js`:
   - `ctrl/ctrl-compras.php`

### Modal no se abre al click en "Nueva Compra"

**Causa:** Librerías no cargadas

**Solución:**
1. Verificar que jQuery esté cargado
2. Verificar que Bootbox esté cargado
3. Verificar que CoffeeSoft esté cargado
4. Revisar consola del navegador (F12) para errores

### Productos no se cargan al seleccionar categoría

**Causa:** Evento change no configurado

**Solución:**
1. Verificar que `setupFormEvents()` se ejecute después de crear el modal
2. Verificar que existan productos en la base de datos para esa categoría
3. Revisar consola de red (F12 → Network) para ver respuesta de `getProductsByClass`

## 📞 Soporte

Si después de seguir estos pasos aún tienes problemas:

1. Revisar logs de PHP: `error.log`
2. Revisar consola del navegador: F12 → Console
3. Revisar red del navegador: F12 → Network
4. Contactar soporte: soporte@coffeesoft.com

## 🎉 ¡Listo!

Si todo funciona correctamente, deberías ver:

✅ Dashboard con totales en $0.00  
✅ Tabla vacía (sin compras registradas)  
✅ Filtros funcionando  
✅ Botón "Nueva Compra" abriendo modal  
✅ Formulario con todos los campos  

Ahora puedes empezar a registrar compras.

---

**Tiempo estimado de instalación:** 10-15 minutos  
**Dificultad:** Fácil  
**Requisitos:** PHP 7.4+, MySQL 5.7+, Apache/Nginx
