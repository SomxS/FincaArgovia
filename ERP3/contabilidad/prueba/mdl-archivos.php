<?php

require_once '../../conf/_CRUD.php';
require_once '../../conf/_Utileria.php';

class mdl extends CRUD {
    public $util;
    public $bd;

    public function __construct() {
        parent::__construct();
        $this->util = new Utileria();
        $this->bd = "erp_";
    }

    public function getFilesByDateRange($array) {
        $query = "
            SELECT f.id, f.upload_date, f.file_name, f.extension, f.size_bytes, u.user_name, u.user_perfil AS module
            FROM {$this->bd}file f
            INNER JOIN {$this->bd}usuarios u ON f.user_id = u.idUser
            WHERE f.upload_date BETWEEN ? AND ?
        ";
        return $this->_Read($query, $array);
    }

    public function getFileById($array) {
        $query = "
            SELECT f.id, f.upload_date, f.file_name, f.extension, f.size_bytes, f.path, u.user_name, u.user_perfil AS module
            FROM {$this->bd}file f
            INNER JOIN {$this->bd}usuarios u ON f.user_id = u.idUser
            WHERE f.id = ?
        ";
        return $this->_Read($query, $array);
    }

    public function fileExists($array) {
        $query = "
            SELECT COUNT(*) AS count
            FROM {$this->bd}file
            WHERE file_name = ?
        ";
        $result = $this->_Read($query, $array);
        return $result[0]['count'] > 0;
    }

    public function createFile($array) {
        return $this->_Insert([
            'table' => "{$this->bd}file",
            'values' => "file_name, user_id, upload_date, size_bytes, path, extension",
            'data' => $array,
        ]);
    }

    public function updateFile($array) {
        return $this->_Update([
            'table' => "{$this->bd}file",
            'values' => "file_name = ?, size_bytes = ?, path = ?, extension = ?",
            'where' => "id = ?",
            'data' => $array,
        ]);
    }

    public function deleteFile($array) {
        return $this->_Delete([
            'table' => "{$this->bd}file",
            'where' => "id = ?",
            'data' => $array,
        ]);
    }
}