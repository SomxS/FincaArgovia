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
        $this->bd = "rfwsmqex_gvsl_finanzas2.";
    }

    function lsSuppliers($array) {
        $query = "
            SELECT id, name as valor
            FROM {$this->bd}supplier
            WHERE active = ?
            ORDER BY name ASC
        ";
        return $this->_Read($query, $array);
    }

    function lsPaymentTypes($array) {
        $query = "
            SELECT id, name as valor
            FROM {$this->bd}payment_type
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

    function listPayments($params) {
        $whereUdn = '';
        $data     = [$params['fecha']];

        if (!empty($params['udn'])) {
            $whereUdn = ' AND ps.udn_id = ?';
            $data[]   = $params['udn'];
        }

        $query = "
            SELECT 
                ps.*,
                s.name as supplier_name,
                pt.name as payment_type_name
            FROM {$this->bd}payment_supplier ps
            LEFT JOIN {$this->bd}supplier s ON ps.supplier_id = s.id
            LEFT JOIN {$this->bd}payment_type pt ON ps.payment_type_id = pt.id
            WHERE ps.payment_date = ? {$whereUdn} AND ps.active = 1
            ORDER BY ps.id DESC
        ";

        return $this->_Read($query, $data);
    }

    function getPaymentById($id) {
        $query = "
            SELECT 
                ps.*,
                s.name as supplier_name,
                pt.name as payment_type_name
            FROM {$this->bd}payment_supplier ps
            LEFT JOIN {$this->bd}supplier s ON ps.supplier_id = s.id
            LEFT JOIN {$this->bd}payment_type pt ON ps.payment_type_id = pt.id
            WHERE ps.id = ?
        ";

        $result = $this->_Read($query, [$id]);
        return $result[0] ?? null;
    }

    function createPayment($data) {
        return $this->_Insert([
            'table'  => $this->bd . 'payment_supplier',
            'values' => $data['values'],
            'data'   => $data['data']
        ]);
    }

    function updatePayment($data) {
        return $this->_Update([
            'table'  => $this->bd . 'payment_supplier',
            'values' => $data['values'],
            'where'  => 'id = ?',
            'data'   => $data['data']
        ]);
    }

    function deletePaymentById($array) {
        return $this->_Update([
            'table'  => $this->bd . 'payment_supplier',
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function getTotalesPorFecha($params) {
        $whereUdn = '';
        $data     = [$params['fecha']];

        if (!empty($params['udn'])) {
            $whereUdn = ' AND ps.udn_id = ?';
            $data[]   = $params['udn'];
        }

        $query = "
            SELECT 
                SUM(ps.amount) as total_pagos,
                SUM(CASE WHEN ps.payment_type_id = 1 THEN ps.amount ELSE 0 END) as total_corporativo,
                SUM(CASE WHEN ps.payment_type_id = 2 THEN ps.amount ELSE 0 END) as total_fondo_fijo
            FROM {$this->bd}payment_supplier ps
            WHERE ps.payment_date = ? {$whereUdn} AND ps.active = 1
        ";

        $result = $this->_Read($query, $data);
        return $result[0] ?? [];
    }

    function listBalance($params) {
        $whereUdn = '';
        $data     = [$params['fi'], $params['ff']];

        if (!empty($params['udn'])) {
            $whereUdn = ' AND ps.udn_id = ?';
            $data[]   = $params['udn'];
        }

        $query = "
            SELECT 
                ps.supplier_id,
                s.name as supplier_name,
                ps.payment_date,
                SUM(ps.amount) as total_pagos
            FROM {$this->bd}payment_supplier ps
            LEFT JOIN {$this->bd}supplier s ON ps.supplier_id = s.id
            WHERE ps.payment_date BETWEEN ? AND ? {$whereUdn} AND ps.active = 1
            GROUP BY ps.supplier_id, ps.payment_date, s.name
            ORDER BY s.name, ps.payment_date
        ";

        return $this->_Read($query, $data);
    }

    function getPurchasesBySupplier($params) {
        require_once 'mdl-compras.php';
        $comprasModel = new mdl();

        $whereUdn = '';
        $data     = [$params['fi'], $params['ff'], 3];

        if (!empty($params['udn'])) {
            $whereUdn = ' AND p.udn_id = ?';
            $data[]   = $params['udn'];
        }

        $query = "
            SELECT 
                p.supplier_id,
                s.name as supplier_name,
                p.operation_date as purchase_date,
                SUM(p.total) as total_compras
            FROM {$this->bd}purchase p
            LEFT JOIN {$this->bd}supplier s ON p.supplier_id = s.id
            WHERE p.operation_date BETWEEN ? AND ?
              AND p.purchase_type_id = ? {$whereUdn}
              AND p.active = 1
            GROUP BY p.supplier_id, p.operation_date, s.name
            ORDER BY s.name, p.operation_date
        ";

        return $this->_Read($query, $data);
    }

    function getSupplierBalance($params) {
        $payments  = $this->listBalance($params);
        $purchases = $this->getPurchasesBySupplier($params);

        $suppliers = [];

        foreach ($purchases as $purchase) {
            $supplierId = $purchase['supplier_id'];
            if (!isset($suppliers[$supplierId])) {
                $suppliers[$supplierId] = [
                    'supplier_id'      => $supplierId,
                    'supplier_name'    => $purchase['supplier_name'],
                    'initial_balance'  => 0,
                    'credit_purchases' => 0,
                    'credit_payments'  => 0,
                    'final_balance'    => 0,
                    'details'          => []
                ];
            }

            $suppliers[$supplierId]['credit_purchases'] += $purchase['total_compras'];
            $suppliers[$supplierId]['details'][] = [
                'date'        => $purchase['purchase_date'],
                'type'        => 'purchase',
                'amount'      => $purchase['total_compras'],
                'description' => 'Compra a crédito'
            ];
        }

        foreach ($payments as $payment) {
            $supplierId = $payment['supplier_id'];
            if (!isset($suppliers[$supplierId])) {
                $suppliers[$supplierId] = [
                    'supplier_id'      => $supplierId,
                    'supplier_name'    => $payment['supplier_name'],
                    'initial_balance'  => 0,
                    'credit_purchases' => 0,
                    'credit_payments'  => 0,
                    'final_balance'    => 0,
                    'details'          => []
                ];
            }

            $suppliers[$supplierId]['credit_payments'] += $payment['total_pagos'];
            $suppliers[$supplierId]['details'][] = [
                'date'        => $payment['payment_date'],
                'type'        => 'payment',
                'amount'      => $payment['total_pagos'],
                'description' => 'Pago de crédito'
            ];
        }

        foreach ($suppliers as &$supplier) {
            $supplier['final_balance'] = $supplier['initial_balance'] + 
                                        $supplier['credit_purchases'] - 
                                        $supplier['credit_payments'];

            usort($supplier['details'], function($a, $b) {
                return strcmp($a['date'], $b['date']);
            });
        }

        return array_values($suppliers);
    }
}
