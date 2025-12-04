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

    /*
     * Índices recomendados para optimización:
     * 
     * ALTER TABLE mtto_almacen ADD INDEX idx_categoria (id_categoria);
     * ALTER TABLE mtto_inventario_movimientos ADD INDEX idx_fecha_estado (fecha, estado);
     * ALTER TABLE mtto_inventario_detalle ADD INDEX idx_movimiento_producto (id_movimiento, id_producto);
     */

    function listMovimientos($array) {
        $mes       = $array[0];
        $anio      = $array[1];
        $categoria = $array[2];

        $whereCategoria = ($categoria == 'Todos') ? '' : 'AND a.id_categoria = ?';
        $params = ($categoria == 'Todos') ? [$mes, $anio] : [$mes, $anio, $categoria];

        $query = "
            SELECT 
                m.id_movimiento,
                m.folio,
                DATE_FORMAT(m.fecha, '%d/%m/%Y') as fecha,
                m.tipo_movimiento,
                d.id_producto,
                a.Equipo as nombre_producto,
                d.cantidad,
                a.id_categoria as categoria_id,
                COALESCE(cat.nombre_categoria, 'Sin categoría') AS nombre_grupo,
                area.Nombre_Area as nombre_area,
                u.user as responsable,
                u.usr_estado as responsable_estado,
                m.fecha_creacion,
                m.estado
            FROM {$this->bd}mtto_inventario_movimientos m
            INNER JOIN {$this->bd}mtto_inventario_detalle d ON m.id_movimiento = d.id_movimiento
            INNER JOIN {$this->bd}mtto_almacen a ON d.id_producto = a.idAlmacen
            LEFT JOIN {$this->bd}mtto_almacen_area area ON a.Area = area.idArea
            LEFT JOIN {$this->bd}categorias cat ON a.id_categoria = cat.id
            LEFT JOIN {$this->bd}usuarios u ON m.user_id = u.idUser
            WHERE MONTH(m.fecha) = ? 
            AND YEAR(m.fecha) = ?
            $whereCategoria
            AND m.estado = 'Activa'
            ORDER BY m.fecha DESC, m.id_movimiento DESC
        ";
        
        return $this->_Read($query, $params);
    }

    function getResumenMovimientos($array) {
        $mes       = $array[0];
        $anio      = $array[1];
        $categoria = $array[2];

        $whereCategoria = ($categoria == 'Todos') ? '' : 'AND a.id_categoria = ?';
        $params = ($categoria == 'Todos') ? [$mes, $anio] : [$mes, $anio, $categoria];

        $query = "
            SELECT 
                m.tipo_movimiento,
                COUNT(DISTINCT m.id_movimiento) as total_movimientos,
                SUM(d.cantidad) as total_unidades
            FROM {$this->bd}mtto_inventario_movimientos m
            INNER JOIN {$this->bd}mtto_inventario_detalle d ON m.id_movimiento = d.id_movimiento
            INNER JOIN {$this->bd}mtto_almacen a ON d.id_producto = a.idAlmacen
            WHERE MONTH(m.fecha) = ? 
            AND YEAR(m.fecha) = ?
            $whereCategoria
            AND m.estado = 'Activa'
            GROUP BY m.tipo_movimiento
        ";
        
        return $this->_Read($query, $params);
    }

    function lsCategorias() {
        $query = "
            SELECT  
                idcategoria AS id,
                nombreCategoria as valor
            FROM {$this->bd}mtto_categoria  
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
