<?php

require_once '../../conf/_CRUD.php';
require_once '../../conf/_Utileria.php';

class FileSecurity extends CRUD {
    public $util;
    public $bd;

    public function __construct() {
        parent::__construct();
        $this->util = new Utileria();
        $this->bd = "erp_";
    }

    public function validateFileAccess($user_id, $file_id, $action = 'view') {
        // Obtener información del archivo y permisos del usuario
        $query = "
            SELECT 
                f.id,
                f.is_deleted,
                up.permission_level,
                up.can_view,
                up.can_download,
                up.can_delete,
                up.can_view_all_units,
                u.usr_udn,
                fv.business_unit
            FROM file f
            LEFT JOIN file_view fv ON f.id = fv.id
            LEFT JOIN user_permissions up ON up.user_id = ? AND up.module_id = f.module_id
            LEFT JOIN usuarios u ON u.idUser = ?
            WHERE f.id = ?
            LIMIT 1
        ";
        
        $result = $this->_Read($query, [$user_id, $user_id, $file_id]);
        
        if (empty($result)) {
            return ['valid' => false, 'message' => 'Archivo no encontrado'];
        }
        
        $file = $result[0];
        
        // Verificar si el archivo está eliminado
        if ($file['is_deleted']) {
            return ['valid' => false, 'message' => 'El archivo ha sido eliminado'];
        }
        
        // Verificar permisos según el nivel del usuario
        $permissionLevel = $file['permission_level'] ?? 'Captura';
        
        // Restricción de unidad de negocio
        if ($permissionLevel !== 'Direccion' && $file['can_view_all_units'] != 1) {
            $userUnitQuery = "SELECT UDN FROM udn WHERE idUDN = ?";
            $userUnit = $this->_Read($userUnitQuery, [$file['usr_udn']]);
            $userUnitName = $userUnit[0]['UDN'] ?? '';
            
            if ($userUnitName !== $file['business_unit']) {
                return ['valid' => false, 'message' => 'No tiene permisos para acceder a archivos de esta unidad de negocio'];
            }
        }
        
        // Verificar permisos específicos para la acción
        switch ($action) {
            case 'view':
                if ($file['can_view'] != 1) {
                    return ['valid' => false, 'message' => 'No tiene permisos para ver este archivo'];
                }
                break;
                
            case 'download':
                if ($file['can_download'] != 1) {
                    return ['valid' => false, 'message' => 'No tiene permisos para descargar este archivo'];
                }
                break;
                
            case 'delete':
                if ($file['can_delete'] != 1) {
                    return ['valid' => false, 'message' => 'No tiene permisos para eliminar este archivo'];
                }
                break;
                
            default:
                return ['valid' => false, 'message' => 'Acción no válida'];
        }
        
        return ['valid' => true, 'permission_level' => $permissionLevel];
    }

    public function generateSecureToken($length = 32) {
        return bin2hex(random_bytes($length));
    }

    public function validateToken($token, $user_id) {
        $query = "
            SELECT f.id, f.file_name, f.original_name, f.path, f.size_bytes
            FROM file f
            INNER JOIN user_permissions up ON up.module_id = f.module_id
            WHERE f.download_token = ? 
            AND up.user_id = ? 
            AND up.can_download = 1
            AND f.is_deleted = 0
            LIMIT 1
        ";
        
        return $this->_Read($query, [$token, $user_id]);
    }

    public function logSecurityEvent($user_id, $file_id, $action, $result, $ip_address, $user_agent) {
        $query = "
            INSERT INTO file_access_log (file_id, user_id, action, action_datetime, ip_address, user_agent, result)
            VALUES (?, ?, ?, NOW(), ?, ?, ?)
        ";
        
        return $this->_CUD($query, [$file_id, $user_id, $action, $ip_address, $user_agent, $result]);
    }

    public function checkRateLimit($user_id, $action = 'download', $limit = 10, $time_window = 3600) {
        $query = "
            SELECT COUNT(*) as count
            FROM file_access_log
            WHERE user_id = ? 
            AND action = ? 
            AND action_datetime > DATE_SUB(NOW(), INTERVAL ? SECOND)
            AND result = 'success'
        ";
        
        $result = $this->_Read($query, [$user_id, $action, $time_window]);
        $count = $result[0]['count'] ?? 0;
        
        return $count < $limit;
    }

    public function sanitizeFileName($filename) {
        // Eliminar caracteres peligrosos
        $filename = preg_replace('/[^a-zA-Z0-9\-_\.]/', '', $filename);
        
        // Limitar longitud
        $filename = substr($filename, 0, 255);
        
        // Prevenir path traversal
        $filename = str_replace(['../', '..\\', '..'], '', $filename);
        
        return $filename;
    }

    public function validateFileUpload($file) {
        $max_size = 10 * 1024 * 1024; // 10MB
        $allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'txt', 'csv'];
        
        // Verificar tamaño
        if ($file['size'] > $max_size) {
            return ['valid' => false, 'message' => 'El archivo excede el tamaño máximo permitido (10MB)'];
        }
        
        // Verificar extensión
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed_extensions)) {
            return ['valid' => false, 'message' => 'Tipo de archivo no permitido'];
        }
        
        // Verificar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        $allowed_mimes = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'image/jpeg',
            'image/png',
            'text/plain',
            'text/csv'
        ];
        
        if (!in_array($mime_type, $allowed_mimes)) {
            return ['valid' => false, 'message' => 'Tipo de archivo no permitido'];
        }
        
        return ['valid' => true, 'extension' => $extension, 'mime_type' => $mime_type];
    }
}