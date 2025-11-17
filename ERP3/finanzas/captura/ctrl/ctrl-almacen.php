<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-almacen.php';

class ctrl extends mdl {

    private function getUserAccessLevel() {
        return isset($_SESSION['access_level']) ? (int)$_SESSION['access_level'] : 1;
    }

    private function hasPermission($requiredLevel) {
        $userLevel = $this->getUserAccessLevel();
        return $userLevel >= $requiredLevel || $userLevel === 4;
    }

    private function checkPermission($requiredLevel, $action = 'realizar esta acción') {
        if (!$this->hasPermission($requiredLevel)) {
            return [
                'status' => 403,
                'message' => "Acceso denegado. No tienes permisos para {$action}."
            ];
        }
        return null;
    }

    function init() {
        $accessLevel = $this->getUserAccessLevel();
        
        return [
            'supplyItems' => $this->lsSupplyItems([1]),
            'udn'         => $this->lsUDN(),
            'productClass' => $this->lsProductClass([1]),
            'accessLevel' => $accessLevel,
            'permissions' => $this->getUserPermissions($accessLevel)
        ];
    }

    private function getUserPermissions($level) {
        $permissions = [
            1 => [
                'name' => 'Captura',
                'moduleName' => 'Salidas de almacén',
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => true,
                'canView' => true,
                'canViewConcentrado' => false,
                'canExportExcel' => false,
                'canUploadFiles' => true,
                'canLockModule' => false
            ],
            2 => [
                'name' => 'Gerencia',
                'moduleName' => 'Almacén',
                'canCreate' => false,
                'canEdit' => false,
                'canDelete' => false,
                'canView' => true,
                'canViewConcentrado' => true,
                'canExportExcel' => true,
                'canUploadFiles' => false,
                'canLockModule' => false
            ],
            3 => [
                'name' => 'Contabilidad',
                'moduleName' => 'Almacén',
                'canCreate' => false,
                'canEdit' => false,
                'canDelete' => false,
                'canView' => true,
                'canViewConcentrado' => true,
                'canExportExcel' => true,
                'canUploadFiles' => false,
                'canLockModule' => false
            ],
            4 => [
                'name' => 'Administración',
                'moduleName' => 'Almacén',
                'canCreate' => true,
                'canEdit' => true,
                'canDelete' => true,
                'canView' => true,
                'canViewConcentrado' => true,
                'canExportExcel' => true,
                'canUploadFiles' => true,
                'canLockModule' => true
            ]
        ];

        return $permissions[$level] ?? $permissions[1];
    }

    function ls() {
        $__row = [];
        $date = $_POST['date'];
        
        $ls = $this->listOutputs([$date]);
        $total = 0;

        foreach ($ls as $key) {
            $a = [];

            $a[] = [
                'class'   => 'btn btn-sm btn-primary me-1',
                'html'    => '<i class="icon-pencil"></i>',
                'onclick' => 'app.editOutput(' . $key['id'] . ')'
            ];

            $a[] = [
                'class'   => 'btn btn-sm btn-danger me-1',
                'html'    => '<i class="icon-trash"></i>',
                'onclick' => 'app.deleteOutput(' . $key['id'] . ')'
            ];

            $a[] = [
                'class'   => 'btn btn-sm btn-info',
                'html'    => '<i class="icon-eye"></i>',
                'onclick' => 'app.viewDescription("' . addslashes($key['description']) . '")'
            ];

            $total += floatval($key['amount']);

            $__row[] = [
                'id'          => $key['id'],
                'Almacén'     => $key['product_name'],
                'Monto'       => evaluar($key['amount']),
                'Descripción' => substr($key['description'], 0, 50) . '...',
                'a'           => $a
            ];
        }

        return [
            'row'   => $__row,
            'total' => $total,
            'ls'    => $ls
        ];
    }

    function getDashboardData() {
        $date = $_POST['date'];
        $ls = $this->listOutputs([$date]);
        $total = 0;

        foreach ($ls as $key) {
            $total += floatval($key['amount']);
        }

        return [
            'status' => 200,
            'total'  => $total,
            'count'  => count($ls)
        ];
    }

    function getOutput() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Salida de almacén no encontrada';
        $data = null;

        $output = $this->getOutputById([$id]);

        if ($output) {
            $status = 200;
            $message = 'Datos obtenidos correctamente';
            $data = $output;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function addOutput() {
        $permissionCheck = $this->checkPermission(1, 'crear salidas de almacén');
        if ($permissionCheck) return $permissionCheck;

        $status = 500;
        $message = 'Error al crear la salida de almacén';

        if (empty($_POST['product_id']) || empty($_POST['amount']) || empty($_POST['description'])) {
            return [
                'status'  => 400,
                'message' => 'Todos los campos son obligatorios'
            ];
        }

        if ($_POST['amount'] <= 0) {
            return [
                'status'  => 400,
                'message' => 'La cantidad debe ser mayor a cero'
            ];
        }

        $_POST['operation_date'] = date('Y-m-d');
        $_POST['active'] = 1;

        $create = $this->createOutput($this->util->sql($_POST));

        if ($create) {
            $status = 200;
            $message = 'Salida de almacén registrada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function editOutput() {
        $permissionCheck = $this->checkPermission(1, 'editar salidas de almacén');
        if ($permissionCheck) return $permissionCheck;

        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al editar la salida de almacén';

        if (empty($_POST['product_id']) || empty($_POST['amount']) || empty($_POST['description'])) {
            return [
                'status'  => 400,
                'message' => 'Todos los campos son obligatorios'
            ];
        }

        if ($_POST['amount'] <= 0) {
            return [
                'status'  => 400,
                'message' => 'La cantidad debe ser mayor a cero'
            ];
        }

        $edit = $this->updateOutput($this->util->sql($_POST, 1));

        if ($edit) {
            $status = 200;
            $message = 'Salida de almacén actualizada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function deleteOutput() {
        $permissionCheck = $this->checkPermission(1, 'eliminar salidas de almacén');
        if ($permissionCheck) return $permissionCheck;

        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al eliminar la salida de almacén';

        $output = $this->getOutputById([$id]);

        if ($output) {
            $this->logAuditDelete([
                'udn_id'      => 1,
                'user_id'     => 1,
                'record_id'   => $id,
                'table_name'  => 'warehouse_output',
                'amount'      => $output['amount'],
                'description' => $output['description'],
                'name_user'   => 'Usuario Sistema',
                'name_udn'    => 'UDN Principal'
            ]);

            $delete = $this->deleteOutputById([$id]);

            if ($delete) {
                $status = 200;
                $message = 'Salida de almacén eliminada correctamente';
            }
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function getConcentrado() {
        $__row = [];
        $fi = $_POST['fi'];
        $ff = $_POST['ff'];

        $ls = $this->listConcentrado([$fi, $ff]);
        
        $totalInitial = 0;
        $totalInputs = 0;
        $totalOutputs = 0;
        $totalFinal = 0;

        foreach ($ls as $key) {
            $totalInitial += floatval($key['initial_balance']);
            $totalInputs += floatval($key['total_inputs']);
            $totalOutputs += floatval($key['total_outputs']);
            $totalFinal += floatval($key['final_balance']);

            $__row[] = [
                'id'              => $key['id'],
                'Clasificación'   => $key['product_class_name'],
                'Almacén'         => $key['product_name'],
                'Saldo Inicial'   => [
                    'html'  => evaluar($key['initial_balance']),
                    'class' => 'text-end'
                ],
                'Entradas'        => [
                    'html'  => evaluar($key['total_inputs']),
                    'class' => 'text-end bg-green-100'
                ],
                'Salidas'         => [
                    'html'  => evaluar($key['total_outputs']),
                    'class' => 'text-end bg-orange-100'
                ],
                'Saldo Final'     => [
                    'html'  => evaluar($key['final_balance']),
                    'class' => 'text-end font-bold'
                ],
                'opc'             => 0
            ];
        }

        return [
            'row' => $__row,
            'ls'  => $ls,
            'totals' => [
                'initial' => $totalInitial,
                'inputs'  => $totalInputs,
                'outputs' => $totalOutputs,
                'final'   => $totalFinal
            ]
        ];
    }

    function getWarehouseDetails() {
        $productId = $_POST['product_id'];
        $fi = $_POST['fi'];
        $ff = $_POST['ff'];

        $details = $this->getWarehouseDetails([
            'product_id' => $productId,
            'fi'         => $fi,
            'ff'         => $ff
        ]);

        return [
            'status' => 200,
            'data'   => $details
        ];
    }

    function uploadFile() {
        $status = 500;
        $message = 'Error al subir el archivo';

        if (!isset($_FILES['file'])) {
            return ['status' => 400, 'message' => 'No se recibió ningún archivo'];
        }

        $file = $_FILES['file'];
        $maxSize = 20 * 1024 * 1024;

        if ($file['size'] > $maxSize) {
            return [
                'status'  => 400,
                'message' => 'El archivo excede el tamaño máximo de 20MB'
            ];
        }

        $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'xlsx', 'xls'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions)) {
            return [
                'status'  => 400,
                'message' => 'Tipo de archivo no permitido'
            ];
        }

        $uploadDir = '../uploads/almacen/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = time() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            $fileData = [
                'udn_id'         => 1,
                'user_id'        => 1,
                'file_name'      => $fileName,
                'upload_date'    => date('Y-m-d H:i:s'),
                'size_bytes'     => $file['size'],
                'path'           => $filePath,
                'extension'      => $extension,
                'operation_date' => $_POST['operation_date']
            ];

            $create = $this->createFile($this->util->sql($fileData));

            if ($create) {
                $status = 200;
                $message = 'Archivo subido correctamente';
            }
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function checkModuleLock() {
        $moduleId = $_POST['module_id'] ?? 1;
        $udnId = $_POST['udn_id'] ?? 1;

        $lockStatus = $this->getModuleLockStatus([
            'module_id' => $moduleId,
            'udn_id'    => $udnId
        ]);

        $isLocked = $lockStatus && $lockStatus['lock_date'] !== null;

        return [
            'status'   => 200,
            'isLocked' => $isLocked,
            'lockData' => $lockStatus
        ];
    }

    function lockModule() {
        $permissionCheck = $this->checkPermission(4, 'bloquear el módulo');
        if ($permissionCheck) return $permissionCheck;

        $status = 500;
        $message = 'Error al bloquear el módulo';

        if (empty($_POST['lock_reason'])) {
            return [
                'status'  => 400,
                'message' => 'Debe proporcionar una razón para el bloqueo'
            ];
        }

        $lock = $this->lockModule([
            'udn_id'      => $_POST['udn_id'] ?? 1,
            'module_id'   => $_POST['module_id'] ?? 1,
            'lock_reason' => $_POST['lock_reason']
        ]);

        if ($lock) {
            $status = 200;
            $message = 'Módulo bloqueado correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function unlockModule() {
        $permissionCheck = $this->checkPermission(4, 'desbloquear el módulo');
        if ($permissionCheck) return $permissionCheck;

        $status = 500;
        $message = 'Error al desbloquear el módulo';

        if (empty($_POST['id'])) {
            return [
                'status'  => 400,
                'message' => 'ID de bloqueo no proporcionado'
            ];
        }

        $unlock = $this->unlockModule([
            'id' => $_POST['id']
        ]);

        if ($unlock) {
            $status = 200;
            $message = 'Módulo desbloqueado correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
