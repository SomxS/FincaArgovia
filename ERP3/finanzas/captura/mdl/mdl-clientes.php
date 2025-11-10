<?php
require_once '../../../conf/_CRUD.php';
require_once '../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_erp.";
    }

    function lsClientes() {
        return $this->_Select([
            'table' => $this->bd . 'clientes',
            'values' => 'id, name',
            'where' => 'active = 1',
            'order' => ['ASC' => 'name']
        ]);
    }

    function getClienteById($id) {
        return $this->_Select([
            'table' => $this->bd . 'clientes',
            'values' => '*',
            'where' => 'id = ?',
            'data' => [$id]
        ])[0] ?? null;
    }

    function getDeudaActual($clienteId) {
        $query = "
            SELECT 
                COALESCE(
                    (SELECT deuda_nueva 
                     FROM {$this->bd}movimientos_credito 
                     WHERE cliente_id = ? 
                     AND active = 1 
                     ORDER BY id DESC 
                     LIMIT 1),
                    (SELECT deuda_inicial 
                     FROM {$this->bd}clientes 
                     WHERE id = ?)
                ) as deuda_actual
        ";

        $result = $this->_Read($query, [$clienteId, $clienteId]);
        return $result[0]['deuda_actual'] ?? 0;
    }

    function listMovimientos($params) {
        $where = 'movimientos_credito.fecha_captura = ? AND movimientos_credito.active = 1';
        $data = [$params['fecha']];

        if ($params['tipo'] !== 'todos') {
            $where .= ' AND movimientos_credito.tipo_movimiento = ?';
            $data[] = $params['tipo'];
        }

        return $this->_Select([
            'table' => $this->bd . 'movimientos_credito',
            'values' => 
                "movimientos_credito.*,
                clientes.name as cliente_nombre,
                usuarios.nombre as usuario_nombre",
            'leftjoin' => [
                $this->bd . 'clientes' => 'movimientos_credito.cliente_id = clientes.id',
                $this->bd . 'usuarios' => 'movimientos_credito.usuario_id = usuarios.id'
            ],
            'where' => $where,
            'order' => ['DESC' => 'movimientos_credito.id'],
            'data' => $data
        ]);
    }

    function getMovimientoById($id) {
        return $this->_Select([
            'table' => $this->bd . 'movimientos_credito',
            'values' => 
                "movimientos_credito.*,
                clientes.name as cliente_nombre,
                usuarios.nombre as usuario_nombre",
            'leftjoin' => [
                $this->bd . 'clientes' => 'movimientos_credito.cliente_id = clientes.id',
                $this->bd . 'usuarios' => 'movimientos_credito.usuario_id = usuarios.id'
            ],
            'where' => 'movimientos_credito.id = ?',
            'data' => [$id]
        ])[0] ?? null;
    }

    function createMovimiento($data) {
        return $this->_Insert([
            'table' => $this->bd . 'movimientos_credito',
            'values' => $data['values'],
            'data' => $data['data']
        ]);
    }

    function updateMovimiento($data) {
        return $this->_Update([
            'table' => $this->bd . 'movimientos_credito',
            'values' => $data['values'],
            'where' => 'id = ?',
            'data' => $data['data']
        ]);
    }

    function deleteMovimientoById($data) {
        return $this->_Update([
            'table' => $this->bd . 'movimientos_credito',
            'values' => 'active = 0',
            'where' => 'id = ?',
            'data' => $data
        ]);
    }

    function getTotalesPorFecha($fecha) {
        $query = "
            SELECT 
                SUM(CASE WHEN tipo_movimiento = 'consumo' THEN cantidad ELSE 0 END) as total_consumos,
                SUM(CASE WHEN tipo_movimiento != 'consumo' AND metodo_pago = 'efectivo' THEN cantidad ELSE 0 END) as total_pagos_efectivo,
                SUM(CASE WHEN tipo_movimiento != 'consumo' AND metodo_pago = 'banco' THEN cantidad ELSE 0 END) as total_pagos_banco
            FROM {$this->bd}movimientos_credito
            WHERE fecha_captura = ? AND active = 1
        ";

        $result = $this->_Read($query, [$fecha]);
        return $result[0] ?? [];
    }

    function listConcentrado($params) {
        $query = "
            SELECT 
                c.id as cliente_id,
                c.name as cliente_nombre,
                c.deuda_inicial as saldo_inicial,
                COALESCE(SUM(CASE WHEN m.tipo_movimiento = 'consumo' THEN m.cantidad ELSE 0 END), 0) as total_consumos,
                COALESCE(SUM(CASE WHEN m.tipo_movimiento != 'consumo' THEN m.cantidad ELSE 0 END), 0) as total_pagos,
                (c.deuda_inicial + 
                 COALESCE(SUM(CASE WHEN m.tipo_movimiento = 'consumo' THEN m.cantidad ELSE 0 END), 0) -
                 COALESCE(SUM(CASE WHEN m.tipo_movimiento != 'consumo' THEN m.cantidad ELSE 0 END), 0)) as saldo_final
            FROM {$this->bd}clientes c
            LEFT JOIN {$this->bd}movimientos_credito m ON c.id = m.cliente_id 
                AND m.fecha_captura BETWEEN ? AND ?
                AND m.active = 1
            WHERE c.active = 1
            GROUP BY c.id, c.name, c.deuda_inicial
            ORDER BY c.name
        ";

        return $this->_Read($query, [$params['fi'], $params['ff']]);
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
