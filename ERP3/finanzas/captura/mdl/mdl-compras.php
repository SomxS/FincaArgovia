<?php
require_once '../../../conf/_CRUD3.php';
require_once '../../../conf/_Utileria.php';
require_once '../../../conf/coffeSoft.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_finanzas.";
    }

    function lsProductClass($array) {
        $query = "
            SELECT id, name, description
            FROM {$this->bd}product_class
            WHERE active = ?
            ORDER BY name ASC
        ";
        return $this->_Read($query, $array);
    }

    function lsProduct($array) {
        $query = "
            SELECT id, name
            FROM {$this->bd}product
            WHERE active = ?
            ORDER BY name ASC
        ";
        return $this->_Read($query, $array);
    }

    function lsProductByClass($array) {
        $query = "
            SELECT id, name AS valor
            FROM {$this->bd}product
            WHERE product_class_id = ? AND active = 1
            ORDER BY name ASC
        ";
        return $this->_Read($query, $array);
    }

    function lsPurchaseType($array) {
        $query = "
            SELECT id, name
            FROM {$this->bd}purchase_type
            WHERE active = ?
            ORDER BY name ASC
        ";
        return $this->_Read($query, $array);
    }

    function lsSupplier($array) {
        $query = "
            SELECT id, name
            FROM {$this->bd}supplier
            WHERE active = ?
            ORDER BY name ASC
        ";
        return $this->_Read($query, $array);
    }

    function lsMethodPay($array) {
        $query = "
            SELECT id, name AS valor
            FROM {$this->bd}method_pay
            WHERE active = ?
            ORDER BY name ASC
        ";
        return $this->_Read($query, $array);
    }

    function lsUDN() {
        $query = "
            SELECT idUDN AS id, UDN AS valor
            FROM udn
            WHERE Stado = 1 AND idUDN NOT IN (8, 10, 7)
            ORDER BY UDN ASC
        ";
        return $this->_Read($query, []);
    }

    function listCompras($params) {
        $whereType = '';
        $data      = [$params['fecha']];

        if ($params['tipo'] !== 'todos') {
            $whereType = ' AND p.purchase_type_id = ?';
            $data[]    = $params['tipo'];
        }

        $query = "
            SELECT 
                p.*,
                pc.name as product_class_name,
                pr.name as product_name,
                pt.name as purchase_type_name,
                s.name as supplier_name,
                mp.name as method_pay_name
            FROM {$this->bd}purchase p
            LEFT JOIN {$this->bd}product_class pc ON p.product_class_id = pc.id
            LEFT JOIN {$this->bd}product pr ON p.product_id = pr.id
            LEFT JOIN {$this->bd}purchase_type pt ON p.purchase_type_id = pt.id
            LEFT JOIN {$this->bd}supplier s ON p.supplier_id = s.id
            LEFT JOIN {$this->bd}method_pay mp ON p.method_pay_id = mp.id
            WHERE p.operation_date = ? {$whereType} AND p.active = 1
            ORDER BY p.id DESC
        ";

        return $this->_Read($query, $data);
    }

    function getCompraById($id) {
        $query = "
            SELECT 
                p.*,
                pc.name as product_class_name,
                pr.name as product_name,
                pt.name as purchase_type_name,
                s.name as supplier_name,
                mp.name as method_pay_name
            FROM {$this->bd}purchase p
            LEFT JOIN {$this->bd}product_class pc ON p.product_class_id = pc.id
            LEFT JOIN {$this->bd}product pr ON p.product_id = pr.id
            LEFT JOIN {$this->bd}purchase_type pt ON p.purchase_type_id = pt.id
            LEFT JOIN {$this->bd}supplier s ON p.supplier_id = s.id
            LEFT JOIN {$this->bd}method_pay mp ON p.method_pay_id = mp.id
            WHERE p.id = ?
        ";

        $result = $this->_Read($query, [$id]);
        return $result[0] ?? null;
    }

    function createCompra($data) {
        return $this->_Insert([
            'table'  => $this->bd . 'purchase',
            'values' => $data['values'],
            'data'   => $data['data']
        ]);
    }

    function updateCompra($data) {
        return $this->_Update([
            'table'  => $this->bd . 'purchase',
            'values' => $data['values'],
            'where'  => 'id = ?',
            'data'   => $data['data']
        ]);
    }

    function deleteCompraById($array) {
        return $this->_Update([
            'table'  => $this->bd . 'purchase',
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function getTotalesPorFecha($fecha) {
        $query = "
            SELECT 
                SUM(p.total) as total_compras,
                SUM(CASE WHEN p.purchase_type_id = 1 THEN p.total ELSE 0 END) as total_fondo_fijo,
                SUM(CASE WHEN p.purchase_type_id = 2 THEN p.total ELSE 0 END) as total_corporativo,
                SUM(CASE WHEN p.purchase_type_id = 3 THEN p.total ELSE 0 END) as total_credito
            FROM {$this->bd}purchase p
            WHERE p.operation_date = ? AND p.active = 1
        ";

        $result = $this->_Read($query, [$fecha]);
        return $result[0] ?? [];
    }

    function getTotalesConcentradoPeriodo($params) {
        $query = "
            SELECT 
                SUM(p.total) as total_compras,
                SUM(CASE WHEN p.purchase_type_id = 1 THEN p.total ELSE 0 END) as salidas_fondo_fijo
            FROM {$this->bd}purchase p
            WHERE p.operation_date BETWEEN ? AND ?
              AND p.udn_id = ?
              AND p.active = 1
        ";

        $result = $this->_Read($query, [$params['fi'], $params['ff'], $params['udn']]);
        $data   = $result[0] ?? [];
        $data['saldo_inicial'] = 15000;

        return $data;
    }

    function listProductClass($array) {
        $query = "
            SELECT DISTINCT
                pc.id,
                pc.name
            FROM {$this->bd}purchase p
            INNER JOIN {$this->bd}product_class pc ON p.product_class_id = pc.id
            WHERE p.operation_date BETWEEN ? AND ?
              AND p.udn_id = ?
              AND p.active = 1
            ORDER BY pc.name
        ";

        return $this->_Read($query, $array);
    }

    function getComprasPorClaseYFecha($claseId, $fecha) {
        $query = "
            SELECT 
                COALESCE(SUM(p.total), 0) as total
            FROM {$this->bd}purchase p
            WHERE p.product_class_id = ?
              AND p.operation_date = ?
              AND p.active = 1
        ";

        $result = $this->_Read($query, [$claseId, $fecha]);
        return $result[0] ?? ['total' => 0];
    }

    function getComprasPorPeriodo($params) {
        $query = "
            SELECT 
                p.operation_date as fecha,
                pc.name as clase_producto,
                COALESCE(SUM(p.total), 0) as total
            FROM {$this->bd}purchase p
            INNER JOIN {$this->bd}product_class pc ON p.product_class_id = pc.id
            WHERE p.operation_date BETWEEN ? AND ?
              AND p.udn_id = ?
              AND p.active = 1
            GROUP BY p.operation_date, pc.name
            ORDER BY p.operation_date, pc.name
        ";

        return $this->_Read($query, [$params['fi'], $params['ff'], $params['udn']]);
    }
}
