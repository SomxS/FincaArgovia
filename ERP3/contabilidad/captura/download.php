<?php
session_start();

if (!isset($_SESSION['idUser'])) {
    http_response_code(403);
    die('Acceso no autorizado');
}

require_once '../../conf/_CRUD.php';
require_once '../../conf/_Utileria.php';
require_once 'security.php';

class FileDownloader extends CRUD {
    public $util;
    public $bd;
    private $security;

    public function __construct() {
        parent::__construct();
        $this->util = new Utileria();
        $this->bd = "erp_";
        $this->security = new FileSecurity();
    }

    public function getFileByToken($token, $user_id) {
        // Validar token y obtener archivo
        return $this->security->validateToken($token, $user_id);
    }

    public function logDownload($file_id, $user_id) {
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
        return $this->security->logSecurityEvent($user_id, $file_id, 'download', 'success', $ip_address, $user_agent);
    }

    public function clearDownloadToken($token) {
        return $this->_Update([
            'table' => "{$this->bd}file",
            'values' => 'download_token = NULL',
            'where' => 'download_token = ?',
            'data' => [$token]
        ]);
    }
}

// Procesar descarga
try {
    if (empty($_GET['token'])) {
        throw new Exception('Token de descarga no proporcionado');
    }

    $downloader = new FileDownloader();
    $security = new FileSecurity();
    $token = $_GET['token'];
    $user_id = $_SESSION['idUser'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    // Validar límite de descargas
    if (!$security->checkRateLimit($user_id, 'download', 20, 3600)) {
        $security->logSecurityEvent($user_id, null, 'download_rate_limit', 'denied', $ip_address, $user_agent);
        throw new Exception('Ha excedido el límite de descargas por hora');
    }
    
    // Obtener información del archivo
    $files = $downloader->getFileByToken($token, $user_id);
    
    if (empty($files)) {
        $security->logSecurityEvent($user_id, null, 'download_invalid_token', 'denied', $ip_address, $user_agent);
        throw new Exception('Token de descarga inválido o expirado');
    }
    
    $file = $files[0];
    $file_path = $file['path'];
    
    // Validar acceso al archivo
    $validation = $security->validateFileAccess($user_id, $file['id'], 'download');
    if (!$validation['valid']) {
        $security->logSecurityEvent($user_id, $file['id'], 'download_access_denied', 'denied', $ip_address, $user_agent);
        throw new Exception($validation['message']);
    }
    
    // Verificar que el archivo existe físicamente
    if (!file_exists($file_path)) {
        $security->logSecurityEvent($user_id, $file['id'], 'download_file_not_found', 'error', $ip_address, $user_agent);
        throw new Exception('El archivo no existe en el servidor');
    }
    
    // Sanitizar nombre del archivo
    $filename = $security->sanitizeFileName($file['original_name'] ?: $file['file_name']);
    
    // Registrar la descarga
    $downloader->logDownload($file['id'], $user_id);
    
    // Limpiar el token de descarga (uso único)
    $downloader->clearDownloadToken($token);
    
    // Configurar headers para la descarga
    $mime_type = $file['mime_type'] ?: 'application/octet-stream';
    
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . $file['size_bytes']);
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: 0');
    header('Pragma: public');
    header('X-Content-Type-Options: nosniff');
    
    // Leer y enviar el archivo
    readfile($file_path);
    
    exit;
    
} catch (Exception $e) {
    http_response_code(400);
    die('Error: ' . $e->getMessage());
}