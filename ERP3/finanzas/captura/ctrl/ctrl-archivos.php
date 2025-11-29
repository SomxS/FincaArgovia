<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-archivos.php';
require_once '../../../conf/coffeSoft.php';

class ctrl extends mdl {

    function init() {
        $userLevel = 3;

        return [
            'modules'   => $this->lsModules(),
            'udn'       => $this->lsUDN(),
            'counts'    => $this->getFileCountsByModule(),
            'userLevel' => $userLevel
        ];
    }

    function ls() {
        $fi        = $_POST['fi'] ?? date('Y-m-d');
        $ff        = $_POST['ff'] ?? date('Y-m-d');
        $module_id = $_POST['module'] ?? '';
        $udn_id    = $_POST['udn'] ?? null;
        
        $data = $this->listFiles([
            'fi'        => $fi,
            'ff'        => $ff,
            'module_id' => $module_id,
            'udn_id'    => $udn_id
        ]);
        
        $rows = [];

        if (is_array($data) && !empty($data)) {
            foreach ($data as $item) {
                $fileSize    = formatFileSize($item['size_bytes']);
                $uploadDate  = formatSpanishDate($item['upload_date'], 'normal');
                $moduleBadge = renderModuleBadge($item['module_name']);
                
                $rows[] = [
                    'id'                 => $item['id'],
                    'Fecha subida'       => $uploadDate,
                    'Módulo'             => [
                        'html'  => $moduleBadge,
                        'class' => 'text-center '
                    ],
                    'Subido por'         => htmlspecialchars($item['uploaded_by']),
                    'Nombre del archivo' => '<span class="font-medium">' . htmlspecialchars($item['file_name']) . '</span>',
                    'Tipo / Tamaño'      => [
                        'html'  => renderFileType($item['extension'], $fileSize),
                        'class' => 'text-center'
                    ],
                    'a'                  => actionButtons($item['id'], $item['path'])
                ];
            }
        }

        return [
            'row' => $rows,
            'ls'  => $data ?? []
        ];
    }

    function getFile() {
        $id      = $_POST['id'];
        $status  = 404;
        $message = 'Archivo no encontrado';
        $data    = null;

        $file = $this->getFileById([$id]);

        if ($file) {
            $status  = 200;
            $message = 'Archivo encontrado';
            $data    = $file;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function downloadFile() {
        $status  = 500;
        $message = 'Error al descargar el archivo';
        
        try {
            $id = $_POST['id'];
            
            if (!isset($_SESSION['user_id'])) {
                return [
                    'status'  => 401,
                    'message' => 'Sesión no válida'
                ];
            }
            
            $file = $this->getFileById([$id]);
            
            if (!$file) {
                return [
                    'status'  => 404,
                    'message' => 'Archivo no encontrado'
                ];
            }
            
            $token  = bin2hex(random_bytes(32));
            $expiry = time() + 300;
            
            $_SESSION['download_tokens'][$token] = [
                'file_id' => $id,
                'user_id' => $_SESSION['user_id'],
                'expiry'  => $expiry,
                'path'    => $file['path']
            ];
            
            $this->createFileLog($this->util->sql([
                'file_id'     => $id,
                'user_id'     => $_SESSION['user_id'],
                'action'      => 'download',
                'action_date' => date('Y-m-d H:i:s'),
                'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? null
            ]));
            
            $status  = 200;
            $message = 'Token generado correctamente';
            $url     = '../../../' . $file['path'];
            
        } catch (Exception $e) {
            $status  = 500;
            $message = 'Error: ' . $e->getMessage();
        }
        
        return [
            'status'  => $status,
            'message' => $message,
            'url'     => $url ?? null,
            'token'   => $token ?? null
        ];
    }

    function deleteFile() {
        $status  = 500;
        $message = 'Error al eliminar el archivo';
        
        try {
            $id = $_POST['id'];
            
            if (!isset($_SESSION['user_id'])) {
                return [
                    'status'  => 401,
                    'message' => 'Sesión no válida'
                ];
            }
            
            $userLevel = $this->getUserLevel([$_SESSION['user_id']]);
            
            if ($userLevel < 1) {
                return [
                    'status'  => 403,
                    'message' => 'No tiene permisos para eliminar archivos'
                ];
            }
            
            $file = $this->getFileById([$id]);
            
            if (!$file) {
                return [
                    'status'  => 404,
                    'message' => 'Archivo no encontrado'
                ];
            }
            
            $filePath = '../../../' . $file['path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            $this->createFileLog($this->util->sql([
                'file_id'     => $id,
                'user_id'     => $_SESSION['user_id'],
                'action'      => 'delete',
                'action_date' => date('Y-m-d H:i:s'),
                'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? null
            ]));
            
            $delete = $this->deleteFileById([$id]);
            
            if ($delete) {
                $status  = 200;
                $message = 'Archivo eliminado correctamente';
            }
            
        } catch (Exception $e) {
            $status  = 500;
            $message = 'Error: ' . $e->getMessage();
        }
        
        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function getFileCounts() {
        $counts = $this->getFileCountsByModule();
        
        return [
            'status' => 200,
            'data'   => $counts
        ];
    }
}

// Complements

function actionButtons($id, $path) {
    return [
        [
            'class'   => 'btn btn-sm btn-success me-1',
            'html'    => '<i class="icon-eye"></i>',
            'onclick' => "app.viewFile($id, '$path')"
        ],
        [
            'class'   => 'btn btn-sm btn-primary me-1',
            'html'    => '<i class="icon-download"></i>',
            'onclick' => "app.downloadFile($id)"
        ],
        [
            'class'   => 'btn btn-sm btn-danger',
            'html'    => '<i class="icon-trash"></i>',
            'onclick' => "app.deleteFile($id)"
        ]
    ];
}

function renderFileType($extension, $size) {
    $ext    = strtoupper($extension);
    $colors = [
        'PDF'  => ['bg' => 'bg-red-100',    'text' => 'text-red-700'],
        'XLS'  => ['bg' => 'bg-green-100',  'text' => 'text-green-700'],
        'XLSX' => ['bg' => 'bg-green-100',  'text' => 'text-green-700'],
        'DOC'  => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700'],
        'DOCX' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700'],
        'PNG'  => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
        'JPG'  => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
        'JPEG' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700']
    ];
    
    $style = $colors[$ext] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
    
    return '
        <div class="flex items-center justify-center gap-2">
            <span class="px-2 py-1 rounded text-xs font-bold ' . $style['bg'] . ' ' . $style['text'] . '">' . $ext . '</span>
            <div class="text-left">
                <div class="text-sm font-medium text-gray-700">' . $ext . '</div>
                <div class="text-xs text-gray-400">' . $size . '</div>
            </div>
        </div>';
}

function renderModuleBadge($module) {
    $colors = [
        'Ventas'      => ['bg' => 'bg-green-100',  'text' => 'text-green-700'],
        'Compras'     => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700'],
        'Almacén'     => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700'],
        'Tesorería'   => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
        'Proveedores' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700']
    ];
    
    $style = $colors[$module] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700'];
    
    return '<span class="inline-block px-3 py-1 rounded text-xs font-semibold min-w-[100px] text-center ' . $style['bg'] . ' ' . $style['text'] . '">' . htmlspecialchars($module) . '</span>';
}

function formatFileSize($bytes) {
    if ($bytes == 0) return '0 B';
    
    $units = ['B', 'KB', 'MB', 'GB'];
    $i     = floor(log($bytes) / log(1024));
    
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
