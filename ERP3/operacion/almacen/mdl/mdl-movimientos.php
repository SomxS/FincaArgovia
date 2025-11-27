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
        $mes = $array[0];
        $anio = $array[1];
        $almacen = $array[2];

        $whereAlmacen = ($almacen == 'Todos') ? '' : 'AND area.idArea = ?';
        $params = ($almacen == 'Todos') ? [$mes, $anio] : [$mes, $anio, $almacen];

        $query = "
            SELECT 
                m.id_movimiento,
                m.folio,
                DATE_FORMAT(m.fecha, '%d/%m/%Y') as fecha,
                m.tipo_movimiento,
                d.id_producto,
                a.Equipo as nombre_producto,
                d.cantidad,
                area.Nombre_Area as nombre_almacen,
                m.fecha_creacion,
                m.estado
            FROM {$this->bd}mtto_inventario_movimientos m
            INNER JOIN {$this->bd}mtto_inventario_detalle d ON m.id_movimiento = d.id_movimiento
            INNER JOIN {$this->bd}mtto_almacen a ON d.id_producto = a.idAlmacen
            LEFT JOIN {$this->bd}mtto_almacen_area area ON a.Area = area.idArea
            WHERE MONTH(m.fecha) = ? 
            AND YEAR(m.fecha) = ?
            $whereAlmacen
            AND m.estado = 'Activa'
            ORDER BY m.fecha DESC, m.id_movimiento DESC
        ";
        
        return $this->_Read($query, $params);
    }

    function getResumenMovimientos($array) {
        $mes = $array[0];
        $anio = $array[1];
        $almacen = $array[2];

        $whereAlmacen = ($almacen == 'Todos') ? '' : 'AND area.idArea = ?';
        $params = ($almacen == 'Todos') ? [$mes, $anio] : [$mes, $anio, $almacen];

        $query = "
            SELECT 
                m.tipo_movimiento,
                COUNT(DISTINCT m.id_movimiento) as total_movimientos,
                SUM(d.cantidad) as total_unidades
            FROM {$this->bd}mtto_inventario_movimientos m
            INNER JOIN {$this->bd}mtto_inventario_detalle d ON m.id_movimiento = d.id_movimiento
            INNER JOIN {$this->bd}mtto_almacen a ON d.id_producto = a.idAlmacen
            LEFT JOIN {$this->bd}mtto_almacen_area area ON a.Area = area.idArea
            WHERE MONTH(m.fecha) = ? 
            AND YEAR(m.fecha) = ?
            $whereAlmacen
            AND m.estado = 'Activa'
            GROUP BY m.tipo_movimiento
        ";
        
        return $this->_Read($query, $params);
    }

    function lsAlmacenes() {
        $query = "
            SELECT 
                idArea as id,
                Nombre_Area as valor
            FROM {$this->bd}mtto_almacen_area
            WHERE Estado = 1
            ORDER BY Nombre_Area ASC
        ";
        return $this->_Read($query, []);
    }

    function lsMeses() {
        return [
            ['id' => '1', 'valor' => 'Enero'],
            ['id' => '2', 'valor' => 'Febrero'],
            ['id' => '3', 'valor' => 'Marzo'],
            ['id' => '4', 'valor' => 'Abril'],
            ['id' => '5', 'valor' => 'Mayo'],
            ['id' => '6', 'valor' => 'Junio'],
            ['id' => '7', 'valor' => 'Julio'],
            ['id' => '8', 'valor' => 'Agosto'],
            ['id' => '9', 'valor' => 'Septiembre'],
            ['id' => '10', 'valor' => 'Octubre'],
            ['id' => '11', 'valor' => 'Noviembre'],
            ['id' => '12', 'valor' => 'Diciembre']
        ];
    }

    function lsAnios() {
        $anioActual = date('Y');
        $anios = [];
        
        for ($i = 0; $i < 5; $i++) {
            $anio = $anioActual - $i;
            $anios[] = ['id' => $anio, 'valor' => $anio];
        }
        
        return $anios;
    }
}
