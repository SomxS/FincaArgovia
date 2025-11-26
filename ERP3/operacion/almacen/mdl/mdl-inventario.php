<?php
require_once '../../../conf/_CRUD.php';
require_once '../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_mtto.";
    }

    function listMovimientos($array) {
        $query = "
            SELECT 
                id_movimiento,
                folio,
                DATE_FORMAT(fecha, '%d/%m/%Y') as fecha,
                tipo_movimiento,
                total_productos,
                total_unidades,
                estado,
                DATE_FORMAT(fecha_creacion, '%d/%m/%Y %H:%i') as fecha_creacion
            FROM {$this->bd}mtto_inventario_movimientos
            WHERE fecha BETWEEN ? AND ?
            AND (? = 'Todos' OR tipo_movimiento = ?)
            ORDER BY id_movimiento DESC
        ";
        return $this->_Read($query, $array);
    }

    function getMovimientoById($id) {
        $query = "
            SELECT *
            FROM {$this->bd}mtto_inventario_movimientos
            WHERE id_movimiento = ?
        ";
        $result = $this->_Read($query, [$id]);
        return $result[0] ?? null;
    }

    function getMaxFolio() {
        $query = "
            SELECT MAX(CAST(SUBSTRING(folio, 5) AS UNSIGNED)) as max_numero
            FROM {$this->bd}mtto_inventario_movimientos
        ";
        $result = $this->_Read($query, []);
        return $result[0]['max_numero'] ?? 0;
    }

    function createMovimiento($array) {
        return $this->_Insert([
            'table'  => "{$this->bd}mtto_inventario_movimientos",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function updateMovimiento($array) {
        return $this->_Update([
            'table'  => "{$this->bd}mtto_inventario_movimientos",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function listDetalleMovimiento($array) {
        $query = "
            SELECT 
                d.id_detalle,
                d.id_movimiento,
                d.id_producto,
                a.Equipo as nombre_producto,
                d.cantidad,
                d.stock_anterior,
                d.stock_resultante,
                a.cantidad as stock_actual
            FROM {$this->bd}mtto_inventario_detalle d
            INNER JOIN {$this->bd}mtto_almacen a ON d.id_producto = a.idAlmacen
            WHERE d.id_movimiento = ?
            ORDER BY d.id_detalle ASC
        ";
        return $this->_Read($query, $array);
    }

    function createDetalleMovimiento($array) {
        return $this->_Insert([
            'table'  => "{$this->bd}mtto_inventario_detalle",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function deleteDetalleMovimientoById($id) {
        return $this->_Delete([
            'table' => "{$this->bd}mtto_inventario_detalle",
            'where' => 'id_detalle = ?',
            'data'  => [$id]
        ]);
    }

    function getStockProducto($idProducto) {
        $query = "
            SELECT cantidad as stock_actual
            FROM {$this->bd}mtto_almacen
            WHERE idAlmacen = ?
        ";
        $result = $this->_Read($query, [$idProducto]);
        return $result[0]['stock_actual'] ?? 0;
    }

    function updateStockProducto($array) {
        return $this->_Update([
            'table'  => "{$this->bd}mtto_almacen",
            'values' => $array['values'],
            'where'  => 'idAlmacen = ?',
            'data'   => $array['data']
        ]);
    }

    function lsTipoMovimiento() {
        $query = "
            SELECT 'Entrada' as id, 'Entrada' as valor
            UNION ALL
            SELECT 'Salida' as id, 'Salida' as valor
        ";
        return $this->_Read($query, []);
    }

    function lsProductos() {
        $query = "
            SELECT 
                idAlmacen as id,
                Equipo as valor,
                cantidad as stock_actual
            FROM {$this->bd}mtto_almacen
            WHERE Estado = 1
            ORDER BY Equipo ASC
        ";
        return $this->_Read($query, []);
    }
}
