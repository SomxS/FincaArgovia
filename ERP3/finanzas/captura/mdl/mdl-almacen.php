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

    function listOutputs($array) {
        $query = "
            SELECT 
                wo.id,
                wo.product_id,
                wo.amount,
                wo.description,
                wo.operation_date,
                p.name AS product_name,
                pc.name AS product_class_name
            FROM {$this->bd}warehouse_output wo
            LEFT JOIN {$this->bd}product p ON wo.product_id = p.id
            LEFT JOIN {$this->bd}product_class pc ON p.product_class_id = pc.id
            WHERE wo.operation_date = ?
            AND wo.active = 1
            ORDER BY wo.id DESC
        ";
        return $this->_Read($query, $array);
    }

    function getOutputById($array) {
        return $this->_Select([
            'table'  => "{$this->bd}warehouse_output",
            'values' => '*',
            'where'  => 'id = ?',
            'data'   => $array
        ])[0] ?? null;
    }

    function createOutput($array) {
        return $this->_Insert([
            'table'  => "{$this->bd}warehouse_output",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function updateOutput($array) {
        return $this->_Update([
            'table'  => "{$this->bd}warehouse_output",
            'values' => $array['values'],
            'where'  => 'id = ?',
            'data'   => $array['data']
        ]);
    }

    function deleteOutputById($array) {
        return $this->_Delete([
            'table' => "{$this->bd}warehouse_output",
            'where' => 'id = ?',
            'data'  => $array
        ]);
    }

    function lsSupplyItems($array) {
        $query = "
            SELECT 
                p.id,
                p.name AS valor,
                pc.name AS classification
            FROM {$this->bd}product p
            LEFT JOIN {$this->bd}product_class pc ON p.product_class_id = pc.id
            WHERE p.active = ?
            ORDER BY pc.name, p.name ASC
        ";
        return $this->_Read($query, $array);
    }

    function lsUDN() {
        $query = "
            SELECT 
                id,
                name AS valor
            FROM {$this->bd}usuarios
            WHERE active = 1
            ORDER BY name ASC
        ";
        return $this->_Read($query, []);
    }

    function lsProductClass($array) {
        $query = "
            SELECT 
                id,
                name AS valor,
                description
            FROM {$this->bd}product_class
            WHERE active = ?
            ORDER BY name ASC
        ";
        return $this->_Read($query, $array);
    }

    function listConcentrado($array) {
        $query = "
            SELECT 
                p.id,
                p.name AS product_name,
                pc.name AS product_class_name,
                COALESCE(
                    (SELECT SUM(wo2.amount) 
                     FROM {$this->bd}warehouse_output wo2 
                     WHERE wo2.product_id = p.id 
                     AND wo2.operation_date < ? 
                     AND wo2.active = 1), 0
                ) AS initial_balance,
                COALESCE(
                    (SELECT SUM(wo3.amount) 
                     FROM {$this->bd}warehouse_output wo3 
                     WHERE wo3.product_id = p.id 
                     AND wo3.operation_date BETWEEN ? AND ? 
                     AND wo3.active = 1), 0
                ) AS total_outputs,
                0 AS total_inputs,
                COALESCE(
                    (SELECT SUM(wo2.amount) 
                     FROM {$this->bd}warehouse_output wo2 
                     WHERE wo2.product_id = p.id 
                     AND wo2.operation_date < ? 
                     AND wo2.active = 1), 0
                ) - COALESCE(
                    (SELECT SUM(wo3.amount) 
                     FROM {$this->bd}warehouse_output wo3 
                     WHERE wo3.product_id = p.id 
                     AND wo3.operation_date BETWEEN ? AND ? 
                     AND wo3.active = 1), 0
                ) AS final_balance
            FROM {$this->bd}product p
            LEFT JOIN {$this->bd}product_class pc ON p.product_class_id = pc.id
            WHERE p.active = 1
            GROUP BY p.id, p.name, pc.name
            HAVING total_outputs > 0 OR initial_balance > 0
            ORDER BY pc.name, p.name ASC
        ";
        return $this->_Read($query, [$array[0], $array[0], $array[1], $array[0], $array[0], $array[1]]);
    }

    function getBalanceByWarehouse($array) {
        $query = "
            SELECT 
                p.id,
                p.name AS product_name,
                COALESCE(
                    (SELECT SUM(wo.amount) 
                     FROM {$this->bd}warehouse_output wo 
                     WHERE wo.product_id = ? 
                     AND wo.operation_date < ? 
                     AND wo.active = 1), 0
                ) AS initial_balance,
                COALESCE(
                    (SELECT SUM(wo.amount) 
                     FROM {$this->bd}warehouse_output wo 
                     WHERE wo.product_id = ? 
                     AND wo.operation_date BETWEEN ? AND ? 
                     AND wo.active = 1), 0
                ) AS total_outputs,
                0 AS total_inputs
            FROM {$this->bd}product p
            WHERE p.id = ?
        ";
        return $this->_Read($query, [
            $array['product_id'], 
            $array['fi'], 
            $array['product_id'], 
            $array['fi'], 
            $array['ff'],
            $array['product_id']
        ])[0] ?? null;
    }

    function getWarehouseDetails($array) {
        $query = "
            SELECT 
                wo.id,
                wo.amount,
                wo.description,
                wo.operation_date,
                DATE_FORMAT(wo.operation_date, '%d/%m/%Y') AS formatted_date
            FROM {$this->bd}warehouse_output wo
            WHERE wo.product_id = ?
            AND wo.operation_date BETWEEN ? AND ?
            AND wo.active = 1
            ORDER BY wo.operation_date DESC, wo.id DESC
        ";
        return $this->_Read($query, [$array['product_id'], $array['fi'], $array['ff']]);
    }

    function logAuditDelete($array) {
        $data = [
            'udn_id'        => $array['udn_id'] ?? 1,
            'user_id'       => $array['user_id'],
            'record_id'     => $array['record_id'],
            'name_table'    => $array['table_name'],
            'name_user'     => $array['name_user'] ?? 'Usuario',
            'name_udn'      => $array['name_udn'] ?? 'UDN',
            'action'        => 'delete',
            'change_items'  => json_encode(['amount' => $array['amount'], 'description' => $array['description'] ?? '']),
            'creation_date' => date('Y-m-d H:i:s')
        ];

        return $this->_Insert([
            'table'  => "{$this->bd}audit_log",
            'values' => implode(', ', array_keys($data)),
            'data'   => array_values($data)
        ]);
    }

    function createFile($array) {
        return $this->_Insert([
            'table'  => "{$this->bd}file",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function getModuleLockStatus($array) {
        $query = "
            SELECT 
                mu.id,
                mu.lock_date,
                mu.lock_reason,
                mu.active
            FROM {$this->bd}module_unlock mu
            WHERE mu.module_id = ?
            AND mu.udn_id = ?
            AND mu.active = 1
            AND mu.lock_date IS NOT NULL
            ORDER BY mu.lock_date DESC
            LIMIT 1
        ";
        return $this->_Read($query, [$array['module_id'], $array['udn_id']])[0] ?? null;
    }

    function lockModule($array) {
        $data = [
            'udn_id'         => $array['udn_id'],
            'module_id'      => $array['module_id'],
            'unlock_date'    => date('Y-m-d H:i:s'),
            'lock_date'      => date('Y-m-d H:i:s'),
            'lock_reason'    => $array['lock_reason'],
            'operation_date' => date('Y-m-d'),
            'active'         => 1
        ];

        return $this->_Insert([
            'table'  => "{$this->bd}module_unlock",
            'values' => implode(', ', array_keys($data)),
            'data'   => array_values($data)
        ]);
    }

    function unlockModule($array) {
        return $this->_Update([
            'table'  => "{$this->bd}module_unlock",
            'values' => 'lock_date = NULL, active = 0',
            'where'  => 'id = ?',
            'data'   => [$array['id']]
        ]);
    }

    function getMonthlyLockTime($array) {
        $query = "
            SELECT 
                lock_time
            FROM {$this->bd}monthly_module_lock
            WHERE module_id = ?
            AND month = ?
            LIMIT 1
        ";
        return $this->_Read($query, [$array['module_id'], $array['month']])[0] ?? null;
    }
}
