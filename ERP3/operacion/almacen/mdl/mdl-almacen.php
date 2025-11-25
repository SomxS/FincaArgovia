<?php

require_once '../../../conf/_CRUD.php';
require_once '../../../conf/_Utileria.php';

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_mtto.";
    }

    function lsZones() {
        $query = "
            SELECT idArea as id, Nombre_Area AS valor
            FROM {$this->bd}mtto_almacen_area
            ORDER BY Nombre_Area ASC
        ";
        return $this->_Read($query, []);
    }

    function lsCategories() {
        $query = "
            SELECT idcategoria as id, nombreCategoria AS valor
            FROM {$this->bd}mtto_categoria
            ORDER BY nombreCategoria ASC
        ";
        return $this->_Read($query, []);
    }

    function lsAreas() {
        $query = "
            SELECT idArea as id, Nombre_Area AS valor
            FROM {$this->bd}mtto_almacen_area
            ORDER BY Nombre_Area ASC
        ";
        return $this->_Read($query, []);
    }

    function listMateriales($filters) {
        $query = "
            SELECT 
                a.idAlmacen as id,
                a.CodigoEquipo,
                a.Equipo,
                a.cantidad,
                a.Costo,
                a.Estado,
                a.rutaImagen,
                ar.Nombre_Area as area,
                c.nombreCategoria as categoria,
                z.Nombre_Area as zona
            FROM {$this->bd}mtto_almacen a
            LEFT JOIN {$this->bd}mtto_almacen_area ar ON a.Area = ar.idArea
            LEFT JOIN {$this->bd}mtto_categoria c ON a.id_categoria = c.idcategoria
            LEFT JOIN {$this->bd}mtto_almacen_area z ON a.id_zona = z.idArea
          
        ";

        $params = [];

        if (!empty($filters['zone'])) {
            $query .= " AND a.id_zona = ?";
            $params[] = $filters['zone'];
        }

        if (!empty($filters['category'])) {
            $query .= " AND a.id_categoria = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['area'])) {
            $query .= " AND a.Area = ?";
            $params[] = $filters['area'];
        }

      

        $query .= " ORDER BY a.idAlmacen DESC";

        return $this->_Read($query, $params);
    }

    function getMaterialById($id) {
        $query = "
            SELECT *
            FROM {$this->bd}mtto_almacen
            WHERE idAlmacen = ?
        ";
        $result = $this->_Read($query, [$id]);
        return $result[0] ?? null;
    }

    function existsMaterialByCode($code) {
        $query = "
            SELECT COUNT(*) as count
            FROM {$this->bd}mtto_almacen
            WHERE CodigoEquipo = ? AND Estado = 1
        ";
        $result = $this->_Read($query, [$code]);
        return $result[0]['count'] > 0;
    }

    function createMaterial($data) {
        return $this->_Insert([
            'table'  => "{$this->bd}mtto_almacen",
            'values' => $data['values'],
            'data'   => $data['data']
        ]);
    }

    function updateMaterial($data) {
        return $this->_Update([
            'table'  => "{$this->bd}mtto_almacen",
            'values' => $data['values'],
            'where'  => $data['where'],
            'data'   => $data['data']
        ]);
    }

    function deleteMaterialById($id) {
        return $this->_Delete([
            'table' => "{$this->bd}mtto_almacen",
            'where' => 'idAlmacen = ?',
            'data'  => [$id]
        ]);
    }
}
