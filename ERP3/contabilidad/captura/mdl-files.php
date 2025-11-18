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

    // Módulo: Archivos
    public function getFilesByPermission($array) {
        $user_id = $array[0];
        $module_id = $array[2];
        $date_from = $array[4];
        $date_to = $array[5];
        
        // Obtener nivel de permiso del usuario
        $permissionQuery = "
            SELECT permission_level, can_view_all_units 
            FROM user_permissions 
            WHERE user_id = ? 
            ORDER BY 
                CASE permission_level 
                    WHEN 'Direccion' THEN 1 
                    WHEN 'Contabilidad' THEN 2 
                    WHEN 'Gerencia' THEN 3 
                    WHEN 'Captura' THEN 4 
                END 
            LIMIT 1
        ";
        $userPermission = $this->_Read($permissionQuery, [$user_id]);
        $permissionLevel = $userPermission[0]['permission_level'] ?? 'Captura';
        $canViewAllUnits = $userPermission[0]['can_view_all_units'] ?? 0;
        
        // Construir consulta base
        $query = "
            SELECT 
                fv.*,
                up.permission_level,
                up.can_view,
                up.can_download,
                up.can_delete
            FROM file_view fv
            LEFT JOIN user_permissions up ON up.user_id = ? AND up.module_id = fv.module_id
            WHERE 
                fv.is_deleted = 0
                AND (? IS NULL OR fv.module_id = ?)
        ";
        
        // Aplicar restricciones según nivel de permiso
        if ($permissionLevel !== 'Direccion' && $canViewAllUnits != 1) {
            $query .= " AND fv.business_unit = (SELECT udn.UDN FROM usuarios u LEFT JOIN udn ON u.usr_udn = udn.idUDN WHERE u.idUser = ?)";
            array_push($array, $user_id);
        }
        
        // Aplicar restricciones de fecha según nivel de permiso
        if ($permissionLevel === 'Captura') {
            $query .= " AND fv.upload_date = ?";
            array_push($array, $date_from);
        } else {
            $query .= " AND fv.upload_date BETWEEN ? AND ?";
        }
        
        $query .= " ORDER BY fv.upload_datetime DESC";
        
        return $this->_Read($query, $array);
    }

    public function getFileById($array) {
        $query = "
            SELECT 
                fv.*,
                f.path as full_path,
                f.original_name,
                f.download_token
            FROM file_view fv
            INNER JOIN file f ON fv.id = f.id
            WHERE fv.id = ? AND fv.is_deleted = 0
        ";
        return $this->_Read($query, $array);
    }

    public function getFileByToken($array) {
        $query = "
            SELECT 
                fv.*,
                f.path as full_path,
                f.original_name
            FROM file_view fv
            INNER JOIN file f ON fv.id = f.id
            WHERE f.download_token = ? AND fv.is_deleted = 0
        ";
        return $this->_Read($query, $array);
    }

    public function getFileModules() {
        $query = "
            SELECT id, module_name, module_description
            FROM file_modules
            WHERE 1=1
            ORDER BY module_name
        ";
        return $this->_Read($query, []);
    }

    public function getUserPermission($array) {
        return $this->_Select([
            'table' => "{$this->bd}user_permissions",
            'values' => 'permission_level, can_view, can_download, can_delete, can_view_all_units',
            'where' => 'user_id = ? AND module_id = ?',
            'data' => $array
        ]);
    }

    public function getUserGlobalPermission($array) {
        return $this->_Select([
            'table' => "{$this->bd}user_permissions",
            'values' => 'MAX(permission_level) as permission_level',
            'where' => 'user_id = ?',
            'data' => $array
        ]);
    }

    public function fileExists($array) {
        $query = "
            SELECT COUNT(*) AS count
            FROM {$this->bd}file
            WHERE file_name = ? AND is_deleted = 0
        ";
        $result = $this->_Read($query, $array);
        return $result[0]['count'] > 0;
    }

    public function createFile($array) {
        return $this->_Insert([
            'table' => "{$this->bd}file",
            'values' => 'udn_id, user_id, module_id, file_name, original_name, upload_date, size_bytes, path, extension, mime_type, download_token',
            'data' => $array
        ]);
    }

    public function deleteFile($array) {
        return $this->_Update([
            'table' => "{$this->bd}file",
            'values' => 'is_deleted = 1, deleted_by = ?, deleted_at = NOW()',
            'where' => 'id = ?',
            'data' => $array
        ]);
    }

    public function logFileAccess($array) {
        return $this->_Insert([
            'table' => "{$this->bd}file_access_log",
            'values' => 'file_id, user_id, action, ip_address, user_agent',
            'data' => $array
        ]);
    }

    public function getFileStats($array) {
        $query = "
            SELECT 
                fm.module_name,
                COUNT(f.id) as file_count,
                SUM(f.size_bytes) as total_size,
                MAX(f.upload_date) as last_upload
            FROM file_modules fm
            LEFT JOIN file f ON fm.id = f.module_id AND f.is_deleted = 0
            WHERE (? IS NULL OR fm.id = ?)
            GROUP BY fm.id, fm.module_name
            ORDER BY fm.module_name
        ";
        return $this->_Read($query, $array);
    }

    public function getFilesByDateRange($array) {
        $query = "
            SELECT 
                fv.*,
                up.permission_level,
                up.can_view,
                up.can_download,
                up.can_delete
            FROM file_view fv
            LEFT JOIN user_permissions up ON up.user_id = ? AND up.module_id = fv.module_id
            WHERE 
                fv.upload_date BETWEEN ? AND ?
                AND fv.is_deleted = 0
                AND (up.permission_level IN ('Gerencia', 'Direccion', 'Contabilidad') OR up.permission_level IS NOT NULL)
            ORDER BY fv.upload_datetime DESC
        ";
        return $this->_Read($query, $array);
    }

    public function getRecentFiles($array) {
        $query = "
            SELECT 
                fv.*,
                up.permission_level,
                up.can_view,
                up.can_download,
                up.can_delete
            FROM file_view fv
            LEFT JOIN user_permissions up ON up.user_id = ? AND up.module_id = fv.module_id
            WHERE 
                fv.is_deleted = 0
                AND (up.permission_level IN ('Gerencia', 'Direccion', 'Contabilidad') OR up.permission_level IS NOT NULL)
            ORDER BY fv.upload_datetime DESC
            LIMIT ?
        ";
        return $this->_Read($query, $array);
    }

    public function getUserFiles($array) {
        $query = "
            SELECT 
                fv.*,
                up.permission_level,
                up.can_view,
                up.can_download,
                up.can_delete
            FROM file_view fv
            INNER JOIN user_permissions up ON up.user_id = ? AND up.module_id = fv.module_id
            WHERE 
                fv.user_id = ?
                AND fv.is_deleted = 0
            ORDER BY fv.upload_datetime DESC
        ";
        return $this->_Read($query, $array);
    }

    public function updateDownloadToken($array) {
        return $this->_Update([
            'table' => "{$this->bd}file",
            'values' => 'download_token = ?',
            'where' => 'id = ?',
            'data' => $array
        ]);
    }

    public function getFileByUserPermission($array) {
        $query = "
            SELECT 
                f.id,
                f.file_name,
                f.original_name,
                f.path,
                f.size_bytes,
                f.extension,
                up.can_download,
                up.can_delete
            FROM file f
            INNER JOIN user_permissions up ON up.user_id = ? AND up.module_id = f.module_id
            WHERE f.id = ? AND f.is_deleted = 0
            AND (up.permission_level IN ('Gerencia', 'Direccion', 'Contabilidad') OR up.can_download = 1)
        ";
        return $this->_Read($query, $array);
    }
}