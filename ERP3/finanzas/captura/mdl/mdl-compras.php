<?php
require_once '../../../conf/_CRUD.php';
require_once '../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_finanzas.";
    }

    function listCompras($array) {
        $leftjoin = [
            $this->bd . 'product_class' => 'purchase.product_class_id = product_class.id',
            $this->bd . 'product' => 'purchase.product_id = product.id',
            $this->bd . 'purchase_type' => 'purchase.purchase_type_id = purchase_type.id',
            $this->bd . 'method_pay' => 'purchase.method_pay_id = method_pay.id',
            $this->bd . 'supplier' => 'purchase.supplier_id = supplier.id'
        ];

        $where = "purchase.operation_date BETWEEN ? AND ? AND purchase.active = ?";
        $data = [$array[0], $array[1], $array[4]];

        if (!empty($array[2])) {
            $where .= " AND purchase.purchase_type_id = ?";
            $data[] = $array[2];
        }

        if (!empty($array[3])) {
            $where .= " AND purchase.method_pay_id = ?";
            $data[] = $array[3];
        }

        return $this->_Select([
            'table' => $this->bd . 'purchase',
            'values' => "
                purchase.id,
                purchase.operation_date,
                product_class.name as product_class_name,
                product.name as product_name,
                purchase_type.name as purchase_type_name,
                method_pay.name as method_pay_name,
                supplier.name as supplier_name,
                purchase.subtotal,
                purchase.tax,
                purchase.total,
                purchase.description,
                purchase.purchase_type_id
            ",
            'leftjoin' => $leftjoin,
            'where' => $where,
            'order' => ['DESC' => 'purchase.operation_date'],
            'data' => $data
        ]);
    }

    function lsProductClass($array) {
        return $this->_Select([
            'table' => $this->bd . 'product_class',
            'values' => "id, name as valor",
            'where' => 'active = ?',
            'order' => ['ASC' => 'name'],
            'data' => $array
        ]);
    }

    function lsPurchaseType($array) {
        return $this->_Select([
            'table' => $this->bd . 'purchase_type',
            'values' => "id, name as valor",
            'where' => 'active = ?',
            'order' => ['ASC' => 'id'],
            'data' => $array
        ]);
    }

    function lsMethodPay($array) {
        return $this->_Select([
            'table' => $this->bd . 'method_pay',
            'values' => "id, name as valor",
            'where' => 'active = ?',
            'order' => ['ASC' => 'name'],
            'data' => $array
        ]);
    }

    function lsSupplier($array) {
        return $this->_Select([
            'table' => $this->bd . 'supplier',
            'values' => "id, name as valor",
            'where' => 'active = ?',
            'order' => ['ASC' => 'name'],
            'data' => $array
        ]);
    }

    function lsUDN($array) {
        return $this->_Select([
            'table' => "rfwsmqex_erp.udn",
            'values' => "idUDN as id, nombre as valor",
            'where' => 'activo = ?',
            'order' => ['ASC' => 'nombre'],
            'data' => $array
        ]);
    }
}
