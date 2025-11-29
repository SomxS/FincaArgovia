<?php
require_once '../../../conf/_CRUD3.php';
require_once '../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd   = "rfwsmqex_gvsl_finanzas3.";
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

    function listSupplier($array) {
        $query = "
            SELECT 
                supplier.id,
                supplier.name,
                supplier.active,
                udn.UDN AS udn_name
            FROM {$this->bd}supplier
            LEFT JOIN rfwsmqex_erp.udn ON supplier.udn_id = udn.idUDN
            WHERE supplier.udn_id = ?
            AND supplier.active = ?
            ORDER BY supplier.id DESC
        ";
        
        return $this->_Read($query, $array);
    }

    function getSupplierById($array) {
        $query = "
            SELECT 
                supplier.*,
                udn.UDN AS udn_name
            FROM {$this->bd}supplier
            LEFT JOIN udn ON supplier.udn_id = udn.idUDN
            WHERE supplier.id = ?
        ";
        
        return $this->_Read($query, $array)[0];
    }

    function createSupplier($array) {
        return $this->_Insert([
            'table'  => $this->bd . 'supplier',
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function updateSupplier($array) {
        return $this->_Update([
            'table'  => $this->bd . 'supplier',
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function existsSupplierByName($array) {
        $query = "
            SELECT id
            FROM {$this->bd}supplier
            WHERE LOWER(name) = LOWER(?)
            AND udn_id = ?
            AND active = 1
        ";
        
        $exists = $this->_Read($query, $array);
        return count($exists) > 0;
    }

    function lsSuppliers2($array = []) {
        if (empty($array)) {
            $query = "
                SELECT id, name AS valor
                FROM {$this->bd}supplier
                WHERE active = 1
                ORDER BY name ASC
            ";
            return $this->_Read($query, null);
        }
        
        $query = "
            SELECT id, name AS valor
            FROM {$this->bd}supplier
            WHERE udn_id = ? AND active = 1
            ORDER BY name ASC
        ";
        
        return $this->_Read($query, $array);
    }
}
