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

    function lsClientes($array) {
        $query = "
            SELECT id, name AS valor
            FROM {$this->bd}customer
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

    function getClienteById($id) {
        $query = "
            SELECT *
            FROM {$this->bd}customer
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
                     ORDER BY id DESC 
                     LIMIT 1),
                    0
                ) as deuda_actual
        ";

        $result = $this->_Read($query, [$clienteId]);
        return $result[0]['deuda_actual'] ?? 0;
    }

    function listMovimientos($params) {
        $whereType = '';
        $data = [$params['daily_closure_id']];

        // if ($params['tipo'] !== 'todos') {
        //     $whereType = ' AND dcc.movement_type = ?';
        //     $data[] = $params['tipo'];
        // }

        //

        $query = "
            SELECT 
                dcc.*,
                c.name as cliente_nombre,
                e.Nombres as usuario_nombre
            FROM {$this->bd}detail_credit_customer dcc
            LEFT JOIN {$this->bd}customer c ON dcc.customer_id = c.id
            LEFT JOIN {$this->bd}daily_closure dc ON dcc.daily_closure_id = dc.id
            LEFT JOIN rfwsmqex_gvsl_rrhh.empleados e ON dc.employee_id = e.idEmpleado
            WHERE dcc.daily_closure_id = ?  {$whereType}
            ORDER BY dcc.id DESC
        ";

        return $this->_Read($query, $data);
    }

    function getMovimientoById($id) {
        $query = "
            SELECT 
                dcc.*,
                c.name as cliente_nombre,
                e.Nombres as usuario_nombre
            FROM {$this->bd}detail_credit_customer dcc
            LEFT JOIN {$this->bd}customer c ON dcc.customer_id = c.id
            LEFT JOIN {$this->bd}daily_closure dc ON dcc.daily_closure_id = dc.id
            LEFT JOIN empleados e ON dc.employee_id = e.idEmpleado
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
            $whereUdn = ' AND c.udn_id = ?';
            $dataParams[] = $params['udn'];
        }

        $query = "
            SELECT 
                c.id as cliente_id,
                c.name as cliente_nombre,
                COALESCE(
                    (SELECT new_debt 
                     FROM {$this->bd}detail_credit_customer 
                     WHERE customer_id = c.id 
                     AND daily_closure_id IN (
                         SELECT id FROM {$this->bd}daily_closure 
                         WHERE operation_date < ?
                     )
                     ORDER BY id DESC 
                     LIMIT 1),
                    0
                ) as saldo_inicial,
                COALESCE(SUM(CASE WHEN dcc.movement_type = 'consumo' THEN dcc.amount ELSE 0 END), 0) as total_consumos,
                COALESCE(SUM(CASE WHEN dcc.movement_type != 'consumo' THEN dcc.amount ELSE 0 END), 0) as total_pagos,
                COALESCE(
                    (SELECT new_debt 
                     FROM {$this->bd}detail_credit_customer 
                     WHERE customer_id = c.id 
                     AND daily_closure_id IN (
                         SELECT id FROM {$this->bd}daily_closure 
                         WHERE operation_date <= ?
                     )
                     ORDER BY id DESC 
                     LIMIT 1),
                    0
                ) as saldo_final
            FROM {$this->bd}customer c
            LEFT JOIN {$this->bd}detail_credit_customer dcc ON c.id = dcc.customer_id 
                AND dcc.daily_closure_id IN (
                    SELECT id FROM {$this->bd}daily_closure 
                    WHERE operation_date BETWEEN ? AND ?
                )
            WHERE c.active = 1 {$whereUdn}
            GROUP BY c.id, c.name
            ORDER BY c.name
        ";

        return $this->_Read($query, $dataParams);
    }

    function getDailyClosureByDate($fecha, $udnId) {
        $query = "
            SELECT id
            FROM {$this->bd}daily_closure
            WHERE operation_date = ? AND udn_id = ?
            LIMIT 1
        ";
        
        $result = $this->_Read($query, [$fecha, $udnId]);
        return $result[0]['id'] ?? null;
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
