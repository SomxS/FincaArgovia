# 📚 Ejemplos de Uso - Módulo de Compras

## 🎯 Casos de Uso Comunes

### 1. Registrar Compra de Fondo Fijo

**Escenario:** Compra de papelería con efectivo del fondo fijo

**Pasos:**
1. Click en "Nueva Compra"
2. Seleccionar:
   - Categoría: "Gastos de administración"
   - Producto: "Papelería"
   - Tipo de compra: "Fondo fijo"
   - Subtotal: 500.00
   - Impuesto: 80.00
   - Descripción: "Compra de hojas, plumas y folders"
3. Click en "Guardar Compra"

**Resultado esperado:**
- Compra registrada con folio #000001
- Total de fondo fijo actualizado: $580.00
- Saldo de fondo fijo reducido: $14,420.00

---

### 2. Registrar Compra Corporativa con Tarjeta

**Escenario:** Pago de servicio de internet con tarjeta de crédito corporativa

**Pasos:**
1. Click en "Nueva Compra"
2. Seleccionar:
   - Categoría: "Gastos de administración"
   - Producto: "Servicio de internet"
   - Tipo de compra: "Corporativo"
   - Método de pago: "Tarjeta de crédito" (aparece automáticamente)
   - Subtotal: 1,012.00
   - Impuesto: 88.00
   - Descripción: "Pago de Starlink"
3. Click en "Guardar Compra"

**Resultado esperado:**
- Compra registrada con folio #000002
- Total corporativo actualizado: $1,100.00
- Método de pago visible en tabla

---

### 3. Registrar Compra a Crédito

**Escenario:** Compra de fumigación a crédito con proveedor

**Pasos:**
1. Click en "Nueva Compra"
2. Seleccionar:
   - Categoría: "Gastos en mantenimiento"
   - Producto: "Fumigación"
   - Tipo de compra: "Crédito"
   - Proveedor: "Lima Lima" (aparece automáticamente)
   - Subtotal: 2,400.00
   - Impuesto: 358.12
   - Descripción: "Fumigación mensual"
3. Click en "Guardar Compra"

**Resultado esperado:**
- Compra registrada con folio #000003
- Total a crédito actualizado: $2,758.12
- Proveedor visible en detalle

---

### 4. Editar Compra Existente

**Escenario:** Corregir monto de impuesto en compra de papelería

**Pasos:**
1. Localizar compra en tabla (Folio #000001)
2. Click en ícono de editar (lápiz)
3. Modificar:
   - Impuesto: 90.00 (antes era 80.00)
4. Click en "Guardar Compra"

**Resultado esperado:**
- Compra actualizada
- Total recalculado: $590.00
- Totales generales actualizados
- Tabla actualizada automáticamente

---

### 5. Ver Detalle de Compra

**Escenario:** Revisar información completa de una compra

**Pasos:**
1. Localizar compra en tabla
2. Click en ícono de ver (ojo)

**Resultado esperado:**
- Modal con información completa:
  - Información del producto
  - Tipo de compra y método de pago
  - Número de ticket/factura
  - Descripción
  - Resumen financiero (subtotal, impuesto, total)

---

### 6. Eliminar Compra

**Escenario:** Eliminar compra registrada por error

**Pasos:**
1. Localizar compra en tabla
2. Click en ícono de eliminar (basura)
3. Confirmar acción en diálogo

**Resultado esperado:**
- Compra marcada como inactiva
- Desaparece de la tabla
- Totales actualizados
- Puede reactivarse después

---

### 7. Filtrar Compras por Tipo

**Escenario:** Ver solo compras de fondo fijo

**Pasos:**
1. En barra de filtros, seleccionar:
   - Tipo de compra: "Fondo fijo"
2. Tabla se actualiza automáticamente

**Resultado esperado:**
- Solo se muestran compras de fondo fijo
- Totales se mantienen globales
- Filtro permanece activo hasta cambiar

---

### 8. Filtrar Compras por Fecha

**Escenario:** Ver compras de noviembre 2025

**Pasos:**
1. Click en selector de fechas
2. Seleccionar:
   - Fecha inicial: 01/11/2025
   - Fecha final: 30/11/2025
3. Click en "Aplicar"

**Resultado esperado:**
- Solo se muestran compras del rango seleccionado
- Totales calculados solo para ese período
- Balance de fondo fijo del período

---

### 9. Filtrar por Método de Pago

**Escenario:** Ver solo compras pagadas con tarjeta de crédito

**Pasos:**
1. Seleccionar:
   - Tipo de compra: "Corporativo"
   - Método de pago: "Tarjeta de crédito"

**Resultado esperado:**
- Solo se muestran compras corporativas con tarjeta
- Filtro de método de pago solo visible para corporativo

---

### 10. Consultar Balance de Fondo Fijo

**Escenario:** Verificar saldo disponible del fondo fijo

**Pasos:**
1. Observar tarjetas de totales en dashboard
2. Verificar:
   - Saldo inicial: $15,000.00
   - Total de compras fondo fijo: $X,XXX.XX
   - Saldo final: $XX,XXX.XX

**Resultado esperado:**
- Saldo final = Saldo inicial - Total compras fondo fijo
- Actualización en tiempo real

---

## 🔄 Flujos de Trabajo Completos

### Flujo 1: Registro Diario de Compras

```
1. Acceder al módulo
   ↓
2. Verificar fecha en filtro (hoy)
   ↓
3. Registrar compras del día:
   - Compra 1: Fondo fijo
   - Compra 2: Corporativo
   - Compra 3: Crédito
   ↓
4. Verificar totales actualizados
   ↓
5. Revisar saldo de fondo fijo
   ↓
6. Generar reporte si es necesario
```

### Flujo 2: Revisión Mensual

```
1. Acceder al módulo
   ↓
2. Seleccionar rango de fechas (mes completo)
   ↓
3. Revisar totales por tipo de compra
   ↓
4. Filtrar por tipo para análisis detallado
   ↓
5. Verificar balance de fondo fijo
   ↓
6. Exportar reporte (próximamente)
```

### Flujo 3: Corrección de Errores

```
1. Localizar compra incorrecta
   ↓
2. Click en editar
   ↓
3. Corregir datos
   ↓
4. Guardar cambios
   ↓
5. Verificar actualización en tabla
   ↓
6. Verificar totales recalculados
```

---

## 💡 Tips y Mejores Prácticas

### ✅ Recomendaciones

1. **Registro Diario**
   - Registrar compras el mismo día que se realizan
   - Incluir descripción detallada
   - Verificar totales antes de guardar

2. **Categorización Correcta**
   - Seleccionar la categoría apropiada
   - Usar productos específicos, no genéricos
   - Mantener consistencia en clasificación

3. **Documentación**
   - Incluir número de ticket/factura cuando aplique
   - Describir claramente la compra
   - Mencionar proveedor en descripción si es relevante

4. **Verificación de Saldos**
   - Revisar saldo de fondo fijo diariamente
   - Alertar cuando saldo sea menor a $5,000
   - Solicitar reembolso oportunamente

5. **Filtros Efectivos**
   - Usar filtros para análisis específicos
   - Combinar filtros para búsquedas precisas
   - Limpiar filtros después de consultas

### ❌ Errores Comunes a Evitar

1. **No seleccionar categoría antes de producto**
   - Resultado: Lista de productos vacía
   - Solución: Siempre seleccionar categoría primero

2. **Olvidar método de pago en compras corporativas**
   - Resultado: Información incompleta
   - Solución: Verificar que campo esté visible y lleno

3. **No incluir impuesto**
   - Resultado: Total incorrecto
   - Solución: Calcular y registrar impuesto siempre

4. **Descripción genérica**
   - Resultado: Difícil identificar compra después
   - Solución: Ser específico en descripción

5. **No verificar totales**
   - Resultado: Errores en cálculos
   - Solución: Revisar preview de total antes de guardar

---

## 📊 Ejemplos de Reportes

### Reporte Diario

```
Fecha: 29/10/2025

Compras del día:
- Fondo fijo: $580.00
- Corporativo: $1,100.00
- Crédito: $2,758.12

Total del día: $4,438.12

Balance fondo fijo:
- Saldo inicial: $15,000.00
- Salidas: $580.00
- Saldo final: $14,420.00
```

### Reporte Mensual

```
Período: Octubre 2025

Resumen por tipo:
- Fondo fijo: $1,635.31
- Corporativo: $9,432.70
- Crédito: $2,758.12

Total del mes: $13,826.13

Balance fondo fijo:
- Saldo inicial: $15,000.00
- Salidas: $1,635.31
- Saldo final: $13,364.69
```

### Reporte por Categoría

```
Período: Octubre 2025

Gastos por categoría:
- Activo fijo: $14,500.00
- Costo directo: $475.00
- Costo indirecto: $33.00
- Alimentos: $475.00
- Bebidas: $350.00
- Gastos de administración: $3,294.00
- Lavandería: $3,294.00
- Gastos operativos: $322.00
- Gastos en mantenimiento: $2,758.12
- Gastos en publicidad: $4,415.14

Total: $13,826.13
```

---

## 🎓 Casos de Estudio

### Caso 1: Control de Fondo Fijo

**Situación:** El fondo fijo se agota rápidamente

**Análisis:**
1. Filtrar compras de fondo fijo del mes
2. Identificar categorías con mayor gasto
3. Revisar si compras son necesarias
4. Evaluar si algunas deberían ser corporativas

**Acción:**
- Reclasificar compras recurrentes grandes como corporativas
- Establecer límites por categoría
- Solicitar reembolso cuando saldo < $5,000

### Caso 2: Auditoría de Compras

**Situación:** Auditoría requiere detalle de compras

**Proceso:**
1. Seleccionar período a auditar
2. Exportar listado completo (próximamente)
3. Revisar cada compra:
   - Ver detalle
   - Verificar documentación
   - Confirmar clasificación
4. Generar reporte concentrado
5. Presentar balance de fondo fijo

### Caso 3: Optimización de Gastos

**Situación:** Reducir gastos operativos

**Análisis:**
1. Filtrar por categoría "Gastos operativos"
2. Identificar compras recurrentes
3. Comparar con meses anteriores
4. Buscar alternativas más económicas

**Resultado:**
- Identificación de gastos innecesarios
- Negociación con proveedores
- Ahorro mensual del 15%

---

## 🔗 Integración con Otros Módulos

### Con Módulo de Inventarios (Futuro)

```javascript
// Al registrar compra de insumos
// Automáticamente actualizar inventario
if (product_class_id === 2) { // Costo directo
    updateInventory({
        product_id: product_id,
        quantity: quantity,
        cost: subtotal
    });
}
```

### Con Módulo de Presupuestos (Futuro)

```javascript
// Validar contra presupuesto antes de guardar
if (total > budget_available) {
    alert({
        icon: 'warning',
        text: 'Compra excede presupuesto disponible'
    });
}
```

### Con Módulo de Contabilidad (Futuro)

```javascript
// Generar póliza contable automáticamente
createAccountingEntry({
    type: 'expense',
    amount: total,
    account: getAccountByCategory(product_class_id),
    description: description
});
```

---

**Última actualización:** Enero 2025  
**Versión:** 1.0.0  
**Desarrollado con:** CoffeeSoft Framework
