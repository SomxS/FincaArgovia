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

    function listCurrencies($array) {
        $query = "
            SELECT 
                id,
                name,
                symbol,
                exchange_rate,
                active
            FROM {$this->bd}foreing_currency
            WHERE active = ?
            ORDER BY id DESC
        ";

        return $this->_Read($query, [$array['active']]);
    }

    function getCurrencyById($array) {
        return $this->_Select([
            'table' => $this->bd . 'foreing_currency',
            'values' => '*',
            'where' => 'id = ?',
            'data' => $array
        ])[0];
    }

    function createCurrency($array) {
        return $this->_Insert([
            'table' => $this->bd . 'foreing_currency',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function updateCurrency($array) {
        return $this->_Update([
            'table' => $this->bd . 'foreing_currency',
            'values' => $array['values'],
            'where' => $array['where'],
            'data' => $array['data']
        ]);
    }

    function existsCurrencyByName($array) {
        $query = "
            SELECT id
            FROM {$this->bd}foreing_currency
            WHERE LOWER(name) = LOWER(?)
            AND active = 1
        ";
        $exists = $this->_Read($query, [$array[0]]);
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
