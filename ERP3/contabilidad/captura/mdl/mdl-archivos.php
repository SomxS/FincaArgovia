<?php
require_once '../../../conf/_CRUD3.php';
require_once '../../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public    $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd   = "rfwsmqex_gvsl_finanzas3.";
    }

    function listFiles($array) {
        $where = '1=1';
        $data  = [];

        if (!empty($array['fi']) && !empty($array['ff'])) {
            $where  .= ' AND f.operation_date BETWEEN ? AND ?';
            $data[]  = $array['fi'];
            $data[]  = $array['ff'];
        }

        if (!empty($array['module_id'])) {
            $where  .= ' AND f.module_id = ?';
            $data[]  = $array['module_id'];
        }

        if (!empty($array['udn_id'])) {
            $where  .= ' AND f.udn_id = ?';
            $data[]  = $array['udn_id'];
        }

        $query = "
            SELECT 
                f.id,
                f.udn_id,
                f.user_id,
                f.file_name,
                f.upload_date,
                f.size_bytes,
                f.path,
                f.extension,
                DATE_FORMAT(f.operation_date, '%d/%m/%Y') AS operation_date,
                f.module_id,
                m.name AS module_name,
                u.usser AS uploaded_by,
                udn.UDN AS udn_name
            FROM {$this->bd}file f
            LEFT JOIN {$this->bd}module m ON f.module_id = m.id
            LEFT JOIN usuarios u ON f.user_id = u.idUser
            LEFT JOIN udn ON f.udn_id = udn.idUDN
            WHERE {$where}
            ORDER BY f.upload_date DESC
        ";

        return $this->_Read($query, $data);
    }

    function getFileById($array) {
        $result = $this->_Select([
            'table'  => $this->bd . 'file',
            'values' => '*',
            'where'  => 'id = ?',
            'data'   => $array
        ]);
        
        return !empty($result) ? $result[0] : null;
    }

    function deleteFileById($array) {
        return $this->_Delete([
            'table' => $this->bd . 'file',
            'where' => 'id = ?',
            'data'  => $array
        ]);
    }

    function createFileLog($array) {
        return $this->_Insert([
            'table'  => $this->bd . 'file_logs',
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function lsModules() {
        $query = "
            SELECT id, name AS valor
            FROM {$this->bd}module
            WHERE active = 1
            ORDER BY name ASC
        ";
        return $this->_Read($query, []);
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

    function getUserLevel($array) {
        $query = "
            SELECT usr_perfil
            FROM usuarios
            WHERE idUser = ?
        ";
        
        $result = $this->_Read($query, $array);
        
        if (!empty($result)) {
            $perfil = $result[0]['usr_perfil'];
            if ($perfil == 1) return 3;
            if ($perfil == 2) return 2;
            return 1;
        }
        
        return 1;
    }

    function getFileCountsByModule() {
        $query = "
            SELECT 
                m.name AS module_name,
                COUNT(f.id) AS count
            FROM {$this->bd}module m
            LEFT JOIN {$this->bd}file f ON m.id = f.module_id
            WHERE m.active = 1
            GROUP BY m.id, m.name
        ";
        
        $results = $this->_Read($query, []);
        $counts  = ['total' => 0];
        
        if (is_array($results) && !empty($results)) {
            foreach ($results as $row) {
                $key              = strtolower(str_replace(['á','é','í','ó','ú',' '], ['a','e','i','o','u','_'], $row['module_name']));
                $counts[$key]     = (int)$row['count'];
                $counts['total'] += (int)$row['count'];
            }
        }
        
        return $counts;
    }
}
