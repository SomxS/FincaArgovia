<?php
require_once '../../../conf/_CRUD3.php';
require_once '../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_gvsl_finanzas3.";
    }

    function listFormasPago($array) {
        $query = "
            SELECT 
                id,
                name,
                active
            FROM {$this->bd}method_pay
            WHERE active = ?
            ORDER BY id DESC
        ";
        
        return $this->_Read($query, $array);
    }

    function getFormaPagoById($array) {
        $query = "
            SELECT *
            FROM {$this->bd}method_pay
            WHERE id = ?
        ";
        
        return $this->_Read($query, $array)[0];
    }

    function existsFormaPagoByName($array) {
        $query = "
            SELECT id
            FROM {$this->bd}method_pay
            WHERE LOWER(name) = LOWER(?)
            AND active = 1
        ";
        $exists = $this->_Read($query, $array);
        return count($exists) > 0;
    }

    function createFormaPago($array) {
        return $this->_Insert([
            'table' => $this->bd . 'method_pay',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function updateFormaPago($array) {
        return $this->_Update([
            'table' => $this->bd . 'method_pay',
            'values' => $array['values'],
            'where' => $array['where'],
            'data' => $array['data']
        ]);
    }
}
