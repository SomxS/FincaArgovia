<?php
require_once '../../conf/_CRUD.php';
require_once '../../conf/_Utileria.php';
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
            SELECT id, name AS valor, description
            FROM {$this->bd}product_class
            WHERE active = 1
            ORDER BY name ASC
        ";
        return $this->_Read($query, []);
    }

    function lsProduct($array) {
        $query = "
            SELECT id, name AS valor
            FROM {$this->bd}product
            WHERE product_class_id = ? AND active = 1
            ORDER BY name ASC
        ";
        return $this->_Read($query, $array);
    }

    function lsPurchaseType() {
        $query = "
            SELECT id, name AS valor
            FROM {$this->bd}purchase_type
            WHERE active = 1
            ORDER BY id ASC
        ";
        return $this->_Read($query, []);
    }

    function lsSupplier($array) {
        $query = "
            SELECT id, name AS valor
            FROM {$this->bd}supplier
            WHERE udn_id = ? AND active = 1
            ORDER BY name ASC
        ";
        return $this->_Read($query, $array);
    }

    function lsMethodPay() {
        $query = "
            SELECT id, name AS valor
            FROM {$this->bd}method_pay
            WHERE active = 1
            ORDER BY name ASC
        ";
        return $this->_Read($query, []);
    }

    function listPurchases($array) {
        $query = "
            SELECT 
                p.id,
                p.operation_date,
                pc.name AS product_class,
                pr.name AS product,
                pt.name AS purchase_type,
                s.name AS supplier,
                mp.name AS method_pay,
                p.subtotal,
                p.tax,
                p.total,
                p.description,
                p.active
            FROM {$this->bd}purchase p
            LEFT JOIN {$this->bd}product_class pc ON p.product_class_id = pc.id
            LEFT JOIN {$this->bd}product pr ON p.product_id = pr.id
            LEFT JOIN {$this->bd}purchase_type pt ON p.purchase_type_id = pt.id
            LEFT JOIN {$this->bd}supplier s ON p.supplier_id = s.id
            LEFT JOIN {$this->bd}method_pay mp ON p.method_pay_id = mp.id
            WHERE p.udn_id = ? 
            AND p.operation_date BETWEEN ? AND ?
            AND p.active = 1
            ORDER BY p.operation_date DESC, p.id DESC
        ";
        return $this->_Read($query, $array);
    }

    function createPurchase($array) {
        return $this->_Insert([
            'table'  => "{$this->bd}purchase",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function updatePurchase($array) {
        return $this->_Update([
            'table'  => "{$this->bd}purchase",
            'values' => $array['values'],
            'where'  => 'id = ?',
            'data'   => $array['data']
        ]);
    }

    function deletePurchaseById($array) {
        return $this->_Update([
            'table'  => "{$this->bd}purchase",
            'values' => 'active = ?',
            'where'  => 'id = ?',
            'data'   => [0, $array[0]]
        ]);
    }

    function getPurchaseById($array) {
        $query = "
            SELECT *
            FROM {$this->bd}purchase
            WHERE id = ?
        ";
        $result = $this->_Read($query, $array);
        return $result[0] ?? null;
    }

    function getTotalsByType($array) {
        $query = "
            SELECT 
                pt.id AS type_id,
                pt.name AS type_name,
                COALESCE(SUM(p.total), 0) AS total
            FROM {$this->bd}purchase_type pt
            LEFT JOIN {$this->bd}purchase p ON pt.id = p.purchase_type_id 
                AND p.udn_id = ? 
                AND p.operation_date BETWEEN ? AND ?
                AND p.active = 1
            WHERE pt.active = 1
            GROUP BY pt.id, pt.name
            ORDER BY pt.id
        ";
        return $this->_Read($query, $array);
    }

    function getBalanceFondoFijo($array) {
        $query = "
            SELECT 
                15000.00 AS saldo_inicial,
                COALESCE(SUM(p.total), 0) AS salidas,
                (15000.00 - COALESCE(SUM(p.total), 0)) AS saldo_final
            FROM {$this->bd}purchase p
            WHERE p.udn_id = ?
            AND p.purchase_type_id = 1
            AND p.operation_date BETWEEN ? AND ?
            AND p.active = 1
        ";
        $result = $this->_Read($query, $array);
        return $result[0] ?? ['saldo_inicial' => 15000, 'salidas' => 0, 'saldo_final' => 15000];
    }

    function listConcentrado($array) {
        $query = "
            SELECT 
                DATE_FORMAT(p.operation_date, '%d/%m/%Y') AS fecha,
                DAYNAME(p.operation_date) AS dia,
                pc.name AS clase_producto,
                SUM(p.subtotal) AS subtotal,
                SUM(p.tax) AS impuesto,
                SUM(p.total) AS total
            FROM {$this->bd}purchase p
            LEFT JOIN {$this->bd}product_class pc ON p.product_class_id = pc.id
            WHERE p.udn_id = ?
            AND p.operation_date BETWEEN ? AND ?
            AND p.active = 1
            GROUP BY p.operation_date, pc.name
            ORDER BY p.operation_date ASC, pc.name ASC
        ";
        return $this->_Read($query, $array);
    }
}
