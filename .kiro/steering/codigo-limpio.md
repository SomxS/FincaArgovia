# Reglas de Código Limpio

## Comentarios

- NO agregar comentarios de documentación tipo PHPDoc o JSDoc innecesarios
- NO agregar comentarios que expliquen código obvio
- NO agregar separadores decorativos con símbolos (===, ---, etc.)
- NO agregar comentarios de sección o encabezados decorativos
- SOLO agregar comentarios cuando el código sea complejo o no obvio
- El código debe ser auto-explicativo con nombres descriptivos

## Ejemplos de lo que NO hacer:

```php
// ❌ MAL
/**
 * Lista todas las categorías
 * 
 * @return array Lista de categorías
 */
function listCategorias() {
    // código
}

// ❌ MAL
// ========================================================================
// MÉTODOS PARA CATEGORÍAS
// ========================================================================

// ❌ MAL
// Validar que el nombre no esté vacío
if (empty($nombre)) {
    // código
}
```

## Ejemplos de lo que SÍ hacer:

```php
// ✅ BIEN - Sin comentarios innecesarios
function listCategorias() {
    return $this->_Select([
        'table' => $this->bd . 'mtto_categoria',
        'values' => 'idcategoria, nombreCategoria',
        'order' => ['ASC' => 'nombreCategoria']
    ]);
}

// ✅ BIEN - Solo comentarios cuando sea necesario
function processComplexAlgorithm($data) {
    // Aplicar algoritmo de Dijkstra para encontrar la ruta más corta
    // debido a la complejidad del grafo de dependencias
    $result = $this->dijkstra($data);
    return $result;
}
```

## Formato de Código

- Usar nombres de variables y funciones descriptivos
- Mantener funciones pequeñas y con una sola responsabilidad
- Evitar código duplicado
- Usar constantes en lugar de valores mágicos

## Aplicación

Estas reglas aplican para:
- Archivos PHP (*.php)
- Archivos JavaScript (*.js)
- Archivos TypeScript (*.ts)
- Cualquier código del proyecto
