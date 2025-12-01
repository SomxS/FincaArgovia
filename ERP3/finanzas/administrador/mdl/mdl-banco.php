<?php
require_once '../../../conf/_CRUD.php';
require_once '../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_gvsl_finanzas3.";
    }

    function listBanks($array) {
        return $this->_Select([
            'table' => $this->bd . 'bank',
            'values' => 'id, name as valor, active',
            'where' => 'active = ?',
            'order' => ['ASC' => 'name'],
            'data' => $array
        ]);
    }

    function listBankAccounts($array) {
        $where = 'bank_account.active = ?';
        $data = [$array['active']];

        if (!empty($array['udn_id'])) {
            $where .= ' AND bank_account.udn_id = ?';
            $data[] = $array['udn_id'];
        }

        $query = "
            SELECT 
                bank_account.id,
                bank_account.name,
                bank_account.account,
                bank_account.udn_id,
                bank_account.bank_id,
                bank_account.active,
                bank.name as bank_name,
                udn.UDN as udn_name
            FROM {$this->bd}bank_account
            INNER JOIN {$this->bd}bank ON bank_account.bank_id = bank.id
            LEFT JOIN udn ON bank_account.udn_id = udn.idUDN
            WHERE {$where}
            ORDER BY bank_account.id DESC
        ";

        return $this->_Read($query, $data);
    }

    function getBankById($array) {
        return $this->_Select([
            'table' => $this->bd . 'bank',
            'values' => '*',
            'where' => 'id = ?',
            'data' => $array
        ])[0];
    }

    function getBankAccountById($array) {
        return $this->_Select([
            'table' => $this->bd . 'bank_account',
            'values' => '*',
            'where' => 'id = ?',
            'data' => $array
        ])[0];
    }

    function createBank($array) {
        return $this->_Insert([
            'table' => $this->bd . 'bank',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function createBankAccount($array) {
        return $this->_Insert([
            'table' => $this->bd . 'bank_account',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function updateBank($array) {
        return $this->_Update([
            'table' => $this->bd . 'bank',
            'values' => $array['values'],
            'where' => $array['where'],
            'data' => $array['data']
        ]);
    }

    function updateBankAccount($array) {
        return $this->_Update([
            'table' => $this->bd . 'bank_account',
            'values' => $array['values'],
            'where' => $array['where'],
            'data' => $array['data']
        ]);
    }

    function existsBankByName($array) {
        $query = "
            SELECT id
            FROM {$this->bd}bank
            WHERE LOWER(name) = LOWER(?)
            AND active = 1
        ";
        $exists = $this->_Read($query, $array);
        return count($exists) > 0;
    }

    function lsUDN() {
        $query = "
            SELECT idUDN AS id, UDN AS valor
            FROM udn
            WHERE Stado = 1 AND idUDN NOT IN (8, 10, 7)
            ORDER BY UDN DESC
        ";
        return $this->_Read($query, null);
    }

    function lsPaymentMethods() {
        return $this->_Select([
            'table' => $this->bd . 'payment_methods',
            'values' => 'id, name as valor',
            'where' => 'active = 1',
            'order' => ['ASC' => 'name']
        ]);
    }
}
