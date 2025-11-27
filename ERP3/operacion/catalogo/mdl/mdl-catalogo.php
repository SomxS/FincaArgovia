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

    function listCategorias() {
        return $this->_Select([
            'table' => $this->bd . 'mtto_categoria',
            'values' => 'idcategoria, nombreCategoria',
            'order' => ['ASC' => 'nombreCategoria']
        ]);
    }

    function getCategoriaById($id) {
        $result = $this->_Select([
            'table' => $this->bd . 'mtto_categoria',
            'values' => '*',
            'where' => 'idcategoria = ?',
            'data' => [$id]
        ]);
        return !empty($result) ? $result[0] : null;
    }

    function existsCategoria($nombre) {
        $query = "
            SELECT idcategoria
            FROM {$this->bd}mtto_categoria
            WHERE LOWER(nombreCategoria) = LOWER(?)
        ";
        $result = $this->_Read($query, [$nombre]);
        return count($result) > 0;
    }

    function createCategoria($array) {
        return $this->_Insert([
            'table' => $this->bd . 'mtto_categoria',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function deleteCategoriaById($id) {
        return $this->_Delete([
            'table' => $this->bd . 'mtto_categoria',
            'where' => 'idcategoria',
            'data' => [$id]
        ]);
    }

    function countCategoriasUsage($id) {
        $query = "
            SELECT COUNT(*) as total
            FROM {$this->bd}mtto_almacen
            WHERE id_categoria = ?
        ";
        $result = $this->_Read($query, [$id]);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    function listAreas() {
        return $this->_Select([
            'table' => $this->bd . 'mtto_almacen_area',
            'values' => 'idArea, Nombre_Area',
            'order' => ['ASC' => 'Nombre_Area']
        ]);
    }

    function getAreaById($id) {
        $result = $this->_Select([
            'table' => $this->bd . 'mtto_almacen_area',
            'values' => '*',
            'where' => 'idArea = ?',
            'data' => [$id]
        ]);
        return !empty($result) ? $result[0] : null;
    }

    function existsArea($nombre) {
        $query = "
            SELECT idArea
            FROM {$this->bd}mtto_almacen_area
            WHERE LOWER(Nombre_Area) = LOWER(?)
        ";
        $result = $this->_Read($query, [$nombre]);
        return count($result) > 0;
    }

    function createArea($array) {
        return $this->_Insert([
            'table' => $this->bd . 'mtto_almacen_area',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function deleteAreaById($id) {
        return $this->_Delete([
            'table' => $this->bd . 'mtto_almacen_area',
            'where' => 'idArea',
            'data' => [$id]
        ]);
    }

    function countAreasUsage($id) {
        $query = "
            SELECT COUNT(*) as total
            FROM {$this->bd}mtto_almacen
            WHERE Area = ?
        ";
        $result = $this->_Read($query, [$id]);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }

    function listZonas() {
        return $this->_Select([
            'table' => $this->bd . 'mtto_almacen_zona',
            'values' => 'idZona, nombreZona, created_at, updated_at',
            'order' => ['ASC' => 'nombreZona']
        ]);
    }

    function getZonaById($id) {
        $result = $this->_Select([
            'table' => $this->bd . 'mtto_almacen_zona',
            'values' => '*',
            'where' => 'idZona = ?',
            'data' => [$id]
        ]);
        return !empty($result) ? $result[0] : null;
    }

    function existsZona($nombre) {
        $query = "
            SELECT idZona
            FROM {$this->bd}mtto_almacen_zona
            WHERE LOWER(nombreZona) = LOWER(?)
        ";
        $result = $this->_Read($query, [$nombre]);
        return count($result) > 0;
    }

    function createZona($array) {
        return $this->_Insert([
            'table' => $this->bd . 'mtto_almacen_zona',
            'values' => $array['values'],
            'data' => $array['data']
        ]);
    }

    function deleteZonaById($id) {
        return $this->_Delete([
            'table' => $this->bd . 'mtto_almacen_zona',
            'where' => 'idZona',
            'data' => [$id]
        ]);
    }

    function countZonasUsage($id) {
        $query = "
            SELECT COUNT(*) as total
            FROM {$this->bd}mtto_almacen
            WHERE id_zona = ?
        ";
        $result = $this->_Read($query, [$id]);
        return !empty($result) ? (int)$result[0]['total'] : 0;
    }
}
