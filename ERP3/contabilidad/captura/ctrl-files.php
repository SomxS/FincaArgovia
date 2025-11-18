<?php

session_start();

if (empty($_POST['opc'])) exit(0);

require_once '../mdl/mdl-files.php';
require_once 'security.php';

class ctrl extends mdl {
    
    public function init() {
        return [
            'modules' => $this->getFileModules(),
            'userPermission' => $this->getUserGlobalPermission([$_SESSION['idUser']])
        ];
    }
    
    public function ls() {
        $fi = $this->util->sql($_POST['fi'] ?? date('Y-m-d'));
        $ff = $this->util->sql($_POST['ff'] ?? date('Y-m-d'));
        $module_id = $this->util->sql($_POST['module_id'] ?? null);
        $user_id = $_SESSION['idUser'];
        
        // Obtener permisos del usuario
        $userPermission = $this->getUserGlobalPermission([$user_id]);
        $permissionLevel = $userPermission[0]['permission_level'] ?? 'Captura';
        
        // Aplicar restricciones según nivel de permiso
        switch ($permissionLevel) {
            case 'Captura':
                // Solo puede ver archivos de su unidad y fecha específica
                $files = $this->getFilesByPermission([$user_id, $user_id, $module_id, $module_id, $fi, $fi]);
                break;
                
            case 'Gerencia':
                // Puede ver archivos de su unidad en rango de fechas
                $files = $this->getFilesByPermission([$user_id, $user_id, $module_id, $module_id, $fi, $ff]);
                break;
                
            case 'Contabilidad':
            case 'Direccion':
                // Puede ver todos los archivos
                $files = $this->getFilesByPermission([$user_id, $user_id, $module_id, $module_id, $fi, $ff]);
                break;
                
            default:
                $files = [];
        }
        
        $rows = [];
        foreach ($files as $file) {
            $row = [
                'id' => $file['id'],
                'upload_date' => $file['upload_date'],
                'module_name' => $file['module_name'],
                'uploaded_by' => $file['uploaded_by'],
                'file_name' => $file['original_name'],
                'type_size' => strtoupper($file['extension']) . ' / ' . $file['formatted_size'],
                'a' => $this->dropdown($file['id'], $file['can_download'], $file['can_delete'])
            ];
            $rows[] = $row;
        }
        
        return [
            'status' => 200,
            'message' => 'Archivos obtenidos exitosamente',
            'row' => $rows
        ];
    }
    
    public function get() {
        $id = $this->util->sql($_POST['id']);
        $user_id = $_SESSION['idUser'];
        
        $file = $this->getFileById([$id]);
        
        if (empty($file)) {
            return [
                'status' => 404,
                'message' => 'Archivo no encontrado'
            ];
        }
        
        $permission = $this->getUserPermission([$user_id, $file[0]['module_id']]);
        
        if (empty($permission) && !in_array($permission[0]['permission_level'] ?? '', ['Direccion', 'Contabilidad'])) {
            return [
                'status' => 403,
                'message' => 'No tiene permisos para ver este archivo'
            ];
        }
        
        return [
            'status' => 200,
            'message' => 'Archivo obtenido exitosamente',
            'data' => $file[0]
        ];
    }
    
    public function stats() {
        $user_id = $_SESSION['idUser'];
        $module_id = $this->util->sql($_POST['module_id'] ?? null);
        
        $stats = $this->getFileStats([$module_id, $module_id]);
        
        return [
            'status' => 200,
            'message' => 'Estadísticas obtenidas exitosamente',
            'data' => $stats
        ];
    }
    
    public function download() {
        $id = $this->util->sql($_POST['id']);
        $user_id = $_SESSION['idUser'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        // Validar límite de descargas
        $security = new FileSecurity();
        if (!$security->checkRateLimit($user_id, 'download', 20, 3600)) {
            return [
                'status' => 429,
                'message' => 'Ha excedido el límite de descargas por hora'
            ];
        }
        
        // Validar acceso al archivo
        $validation = $security->validateFileAccess($user_id, $id, 'download');
        if (!$validation['valid']) {
            $security->logSecurityEvent($user_id, $id, 'download_attempt', 'denied', $ip_address, $user_agent);
            return [
                'status' => 403,
                'message' => $validation['message']
            ];
        }
        
        $file = $this->getFileByUserPermission([$user_id, $id]);
        
        if (empty($file)) {
            $security->logSecurityEvent($user_id, $id, 'download_attempt', 'file_not_found', $ip_address, $user_agent);
            return [
                'status' => 404,
                'message' => 'Archivo no encontrado o sin permisos'
            ];
        }
        
        $token = $security->generateSecureToken();
        $this->updateDownloadToken([$token, $id]);
        
        $this->logFileAccess([$id, $user_id, 'download', $ip_address, $user_agent]);
        $security->logSecurityEvent($user_id, $id, 'download', 'success', $ip_address, $user_agent);
        
        return [
            'status' => 200,
            'message' => 'Token de descarga generado',
            'token' => $token,
            'filename' => $file[0]['original_name']
        ];
    }
    
    public function delete() {
        $id = $this->util->sql($_POST['id']);
        $user_id = $_SESSION['idUser'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        // Validar acceso al archivo
        $security = new FileSecurity();
        $validation = $security->validateFileAccess($user_id, $id, 'delete');
        if (!$validation['valid']) {
            $security->logSecurityEvent($user_id, $id, 'delete_attempt', 'denied', $ip_address, $user_agent);
            return [
                'status' => 403,
                'message' => $validation['message']
            ];
        }
        
        $file = $this->getFileByUserPermission([$user_id, $id]);
        
        if (empty($file)) {
            $security->logSecurityEvent($user_id, $id, 'delete_attempt', 'file_not_found', $ip_address, $user_agent);
            return [
                'status' => 404,
                'message' => 'Archivo no encontrado o sin permisos'
            ];
        }
        
        $result = $this->deleteFile([$user_id, $id]);
        
        if ($result) {
            $this->logFileAccess([$id, $user_id, 'delete', $ip_address, $user_agent]);
            $security->logSecurityEvent($user_id, $id, 'delete', 'success', $ip_address, $user_agent);
            return [
                'status' => 200,
                'message' => 'Archivo eliminado exitosamente'
            ];
        } else {
            $security->logSecurityEvent($user_id, $id, 'delete_attempt', 'error', $ip_address, $user_agent);
            return [
                'status' => 500,
                'message' => 'Error al eliminar el archivo'
            ];
        }
    }
    
    public function getUserFiles() {
        $user_id = $_SESSION['idUser'];
        
        $files = $this->getUserFiles([$user_id, $user_id]);
        
        return [
            'status' => 200,
            'message' => 'Archivos del usuario obtenidos exitosamente',
            'data' => $files
        ];
    }
    
    private function dropdown($id, $can_download, $can_delete) {
        $dropdown = '<div class="dropdown">';
        $dropdown .= '<button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">';
        $dropdown .= '<i class="icon-gear"></i>';
        $dropdown .= '</button>';
        $dropdown .= '<ul class="dropdown-menu">';
        
        if ($can_download) {
            $dropdown .= '<li><a class="dropdown-item" href="#" onclick="files.download(' . $id . ')">';
            $dropdown .= '<i class="icon-download"></i> Descargar</a></li>';
        }
        
        $dropdown .= '<li><a class="dropdown-item" href="#" onclick="files.view(' . $id . ')">';
        $dropdown .= '<i class="icon-eye"></i> Ver</a></li>';
        
        if ($can_delete) {
            $dropdown .= '<li><hr class="dropdown-divider"></li>';
            $dropdown .= '<li><a class="dropdown-item text-danger" href="#" onclick="files.delete(' . $id . ')">';
            $dropdown .= '<i class="icon-trash"></i> Eliminar</a></li>';
        }
        
        $dropdown .= '</ul></div>';
        
        return $dropdown;
    }
}

$obj = new ctrl();
$fn = $_POST['opc'];
$encode = $obj->$fn();
echo json_encode($encode);