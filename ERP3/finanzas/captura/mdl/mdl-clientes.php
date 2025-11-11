<?php
require_once '../../../conf/_CRUD3.php';
require_once '../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_gvsl_finanzas2.";
    }

    function lsClientes() {
        $query = "
            SELECT id, name
            FROM {$this->bd}clientes_credit_customers
            WHERE active = 1
            ORDER BY name ASC
        ";
        return $this->_Read($query, null);
    }

    function lsUDN() {
        $query = "
            SELECT idUDN AS id, UDN AS valor
            FROM udn
            WHERE Stado = 1 AND idUDN NOT IN (8, 10, 7)
            ORDER BY UDN ASC
        ";
        return $this->_Read($query, null);
    }

    function getClienteById($id) {
        $query = "
            SELECT *
            FROM {$this->bd}clientes_credit_customers
            WHERE id = ?
        ";
        $result = $this->_Read($query, [$id]);
        return $result[0] ?? null;
    }

    function getDeudaActualByID($clienteId) {
        $query = "
            SELECT 
                COALESCE(
                    (SELECT new_debt 
                     FROM {$this->bd}detail_credit_customer 
                     WHERE customer_id = ? 
                     AND active = 1 
                     ORDER BY id DESC 
                     LIMIT 1),
                    (SELECT initial_debt 
                     FROM {$this->bd}clientes_credit_customers 
                     WHERE id = ?)
                ) as deuda_actual
        ";

        $result = $this->_Read($query, [$clienteId, $clienteId]);
        return $result[0]['deuda_actual'] ?? 0;
    }

    function listMovimientos($params) {
        $whereType = '';
        $data = [$params['fecha']];

        if ($params['tipo'] !== 'todos') {
            $whereType = ' AND dcc.movement_type = ?';
            $data[] = $params['tipo'];
        }

        $query = "
            SELECT 
                dcc.*,
                ccc.name as cliente_nombre,
                u.nombre as usuario_nombre
            FROM {$this->bd}detail_credit_customer dcc
            LEFT JOIN {$this->bd}clientes_credit_customers ccc ON dcc.customer_id = ccc.id
            LEFT JOIN {$this->bd}usuarios u ON dcc.user_id = u.id
            WHERE dcc.capture_date = ? AND dcc.active = 1 {$whereType}
            ORDER BY dcc.id DESC
        ";

        return $this->_Read($query, $data);
    }

    function getMovimientoById($id) {
        $query = "
            SELECT 
                dcc.*,
                ccc.name as cliente_nombre,
                u.nombre as usuario_nombre
            FROM {$this->bd}detail_credit_customer dcc
            LEFT JOIN {$this->bd}clientes_credit_customers ccc ON dcc.customer_id = ccc.id
            LEFT JOIN {$this->bd}usuarios u ON dcc.user_id = u.id
            WHERE dcc.id = ?
        ";
        
        $result = $this->_Read($query, [$id]);
        return $result[0] ?? null;
    }

    function createMovimiento($data) {
        return $this->_Insert([
            'table' => $this->bd . 'detail_credit_customer',
            'values' => $data['values'],
            'data' => $data['data']
        ]);
    }

    function updateMovimiento($data) {
        return $this->_Update([
            'table' => $this->bd . 'detail_credit_customer',
            'values' => $data['values'],
            'where' => 'id = ?',
            'data' => $data['data']
        ]);
    }

    function deleteMovimientoById($data) {
        return $this->_Update([
            'table' => $this->bd . 'detail_credit_customer',
            'values' => 'active = 0',
            'where' => 'id = ?',
            'data' => $data
        ]);
    }

    function getTotalesPorFecha($fecha) {
        $query = "
            SELECT 
                SUM(CASE WHEN dcc.movement_type = 'consumo' THEN dcc.amount ELSE 0 END) as total_consumos,
                SUM(CASE WHEN dcc.movement_type != 'consumo' AND dcc.method_pay = 'efectivo' THEN dcc.amount ELSE 0 END) as total_pagos_efectivo,
                SUM(CASE WHEN dcc.movement_type != 'consumo' AND dcc.method_pay = 'banco' THEN dcc.amount ELSE 0 END) as total_pagos_banco
            FROM {$this->bd}daily_closure dc
            INNER JOIN {$this->bd}detail_credit_customer dcc ON dcc.daily_closure_id = dc.id
            WHERE dc.operation_date = ? 
        ";

        $result = $this->_Read($query, [$fecha]);
        return $result[0] ?? [];
    }

    function listConcentrado($params) {
        $whereUdn = '';
        $dataParams = [$params['fi'], $params['ff']];

        if (isset($params['udn']) && $params['udn'] !== 'todas') {
            $whereUdn = ' AND ccc.udn_id = ?';
            $dataParams[] = $params['udn'];
        }

        $query = "
            SELECT 
                ccc.id as cliente_id,
                ccc.name as cliente_nombre,
                ccc.initial_debt as saldo_inicial,
                COALESCE(SUM(CASE WHEN dcc.movement_type = 'consumo' THEN dcc.quantity ELSE 0 END), 0) as total_consumos,
                COALESCE(SUM(CASE WHEN dcc.movement_type != 'consumo' THEN dcc.quantity ELSE 0 END), 0) as total_pagos,
                (ccc.initial_debt + 
                 COALESCE(SUM(CASE WHEN dcc.movement_type = 'consumo' THEN dcc.quantity ELSE 0 END), 0) -
                 COALESCE(SUM(CASE WHEN dcc.movement_type != 'consumo' THEN dcc.quantity ELSE 0 END), 0)) as saldo_final
            FROM {$this->bd}clientes_credit_customers ccc
            LEFT JOIN {$this->bd}detail_credit_customer dcc ON ccc.id = dcc.customer_id 
                AND dcc.capture_date BETWEEN ? AND ?
                AND dcc.active = 1
            WHERE ccc.active = 1 {$whereUdn}
            GROUP BY ccc.id, ccc.name, ccc.initial_debt
            ORDER BY ccc.name
        ";

        return $this->_Read($query, $dataParams);
    }

    function logAuditoria($data) {
        $logData = [
            'movimiento_id' => $data['movimiento_id'] ?? null,
            'cliente_id' => $data['cliente_id'],
            'accion' => $data['accion'],
            'usuario_id' => $data['usuario_id'],
            'fecha_accion' => date('Y-m-d H:i:s'),
            'datos_anteriores' => json_encode($data['datos_anteriores'] ?? null),
            'datos_nuevos' => json_encode($data['datos_nuevos'] ?? null)
        ];

        return $this->_Insert([
            'table' => $this->bd . 'audit_log_clientes',
            'values' => implode(',', array_keys($logData)),
            'data' => array_values($logData)
        ]);
    }
}
