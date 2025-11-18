<?php
require_once '../../../conf/_CRUD.php';
require_once '../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_gvsl_finanzas2.";
    }

    function lsUDN() {
        $query = "
            SELECT DISTINCT id, name AS valor
            FROM {$this->bd}usuarios
            WHERE active = 1 AND nivel IN (2, 3)
            ORDER BY name ASC
        ";
        return $this->_Read($query, []);
    }

    function listCostos($array) {
        $fi = $array[0];
        $ff = $array[1];
        $udn = $array[2] ?? null;

        $udnFilter = $udn ? "AND (c.udn = ? OR sa.udn = ?)" : "";
        $params = $udn ? [$fi, $ff, $udn, $udn] : [$fi, $ff];

        $query = "
            SELECT 
                pc.classification AS product_class,
                DATE(COALESCE(c.operation_date, sa.operation_date)) AS fecha,
                COALESCE(SUM(c.amount), 0) AS costo_directo,
                COALESCE(SUM(sa.amount), 0) AS salida_almacen
            FROM {$this->bd}product_class pc
            LEFT JOIN (
                SELECT 
                    product_class_id,
                    operation_date,
                    udn,
                    SUM(amount) AS amount
                FROM {$this->bd}purchases
                WHERE operation_date BETWEEN ? AND ?
                    AND active = 1
                GROUP BY product_class_id, operation_date, udn
            ) c ON pc.id = c.product_class_id
            LEFT JOIN (
                SELECT 
                    p.product_class_id,
                    wo.operation_date,
                    wo.udn,
                    SUM(wo.amount) AS amount
                FROM {$this->bd}warehouse_output wo
                INNER JOIN {$this->bd}product p ON wo.product_id = p.id
                WHERE wo.operation_date BETWEEN ? AND ?
                    AND wo.active = 1
                GROUP BY p.product_class_id, wo.operation_date, wo.udn
            ) sa ON pc.id = sa.product_class_id 
                AND c.operation_date = sa.operation_date
            WHERE pc.active = 1
                AND (c.operation_date IS NOT NULL OR sa.operation_date IS NOT NULL)
                {$udnFilter}
            GROUP BY pc.id, DATE(COALESCE(c.operation_date, sa.operation_date))
            ORDER BY pc.classification ASC, fecha DESC
        ";

        return $this->_Read($query, $params);
    }

    function getCostosDirectos($array) {
        $fi = $array[0];
        $ff = $array[1];
        $udn = $array[2] ?? null;

        $udnFilter = $udn ? "AND udn = ?" : "";
        $params = $udn ? [$fi, $ff, $udn] : [$fi, $ff];

        $query = "
            SELECT 
                product_class_id,
                DATE(operation_date) AS fecha,
                SUM(amount) AS total
            FROM {$this->bd}purchases
            WHERE operation_date BETWEEN ? AND ?
                AND active = 1
                {$udnFilter}
            GROUP BY product_class_id, DATE(operation_date)
            ORDER BY fecha DESC
        ";

        return $this->_Read($query, $params);
    }

    function getSalidasAlmacen($array) {
        $fi = $array[0];
        $ff = $array[1];
        $udn = $array[2] ?? null;

        $udnFilter = $udn ? "AND wo.udn = ?" : "";
        $params = $udn ? [$fi, $ff, $udn] : [$fi, $ff];

        $query = "
            SELECT 
                p.product_class_id,
                DATE(wo.operation_date) AS fecha,
                SUM(wo.amount) AS total
            FROM {$this->bd}warehouse_output wo
            INNER JOIN {$this->bd}product p ON wo.product_id = p.id
            WHERE wo.operation_date BETWEEN ? AND ?
                AND wo.active = 1
                {$udnFilter}
            GROUP BY p.product_class_id, DATE(wo.operation_date)
            ORDER BY fecha DESC
        ";

        return $this->_Read($query, $params);
    }
}
