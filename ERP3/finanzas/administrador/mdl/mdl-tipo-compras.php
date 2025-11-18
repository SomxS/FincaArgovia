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

    function lsUDN() {
        $query = "
            SELECT idUDN AS id, UDN AS valor
            FROM udn
            WHERE Stado = 1 AND idUDN NOT IN (8, 10, 7)
            ORDER BY UDN DESC
        ";
        return $this->_Read($query, null);
    }

    function listPurchaseType($array) {
        $where = 'active = ?';
        $data = [$array['active']];

       

        $query = "
            SELECT 
                id,
                name,
                active
            FROM {$this->bd}purchase_type
            WHERE {$where}
            ORDER BY id DESC
        ";

        return $this->_Read($query, $data);
    }

    function getPurchaseTypeById($array) {
        return $this->_Select([
            'table' => $this->bd . 'purchase_type',
            'values' => '*',
            'where' => 'id = ?',
            'data' => $array
        ])[0];
    }

    function existsPurchaseTypeByName($array) {
        $query = "
            SELECT id
            FROM {$this->bd}purchase_type
            WHERE LOWER(name) = LOWER(?)
            AND udn_id = ?
            AND active = 1
        ";
        $exists = $this->_Read($query, $array);
        return count($exists) > 0;
    }

    function createPurchaseType($array) {
        return $this->_Insert([
            'table' => $this->bd . 'purchase_type',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function updatePurchaseType($array) {
        return $this->_Update([
            'table' => $this->bd . 'purchase_type',
            'values' => $array['values'],
            'where' => $array['where'],
            'data' => $array['data']
        ]);
    }
}
