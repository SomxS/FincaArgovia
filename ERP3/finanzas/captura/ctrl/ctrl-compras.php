<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-compras.php';

class ctrl extends mdl {

    function validatePermissions($action) {
        $userLevel = $_SESSION['user_level'] ?? 'Captura';
        
        $permissions = [
            'Captura' => ['add', 'ls', 'getPurchase'],
            'Gerencia' => ['add', 'edit', 'ls', 'getPurchase', 'concentrado'],
            'Dirección' => ['add', 'edit', 'delete', 'ls', 'getPurchase', 'concentrado'],
            'Contabilidad' => ['add', 'edit', 'delete', 'ls', 'getPurchase', 'concentrado', 'lock', 'unlock']
        ];
        
        if (!isset($permissions[$userLevel]) || !in_array($action, $permissions[$userLevel])) {
            return [
                'status' => 403,
                'message' => 'No tiene permisos para realizar esta acción'
            ];
        }
        
        return true;
    }

    function isModuleLocked() {
        $query = "
            SELECT lock_time 
            FROM {$this->bd}monthly_module_lock 
            WHERE month = ? AND lock_time IS NOT NULL
            ORDER BY lock_time DESC 
            LIMIT 1
        ";
        $result = $this->_Read($query, [date('Y-m')]);
        return !empty($result);
    }

    function init() {
        $udn = $_POST['udn'] ?? 1;
        return [
            'productClass' => $this->lsProductClass([]),
            'purchaseType' => $this->lsPurchaseType(),
            'supplier'     => $this->lsSupplier([$udn]),
            'methodPay'    => $this->lsMethodPay(),
            'userLevel'    => $_SESSION['user_level'] ?? 'Captura',
            'moduleLocked' => $this->isModuleLocked()
        ];
    }

    function ls() {
        $__row = [];
        $udn = $_POST['udn'] ?? 1;
        $fi = $_POST['fi'] ?? date('Y-m-01');
        $ff = $_POST['ff'] ?? date('Y-m-t');

        $ls = $this->listPurchases([$udn, $fi, $ff]);
        $totals = $this->getTotalsByType([$udn, $fi, $ff]);
        $balance = $this->getBalanceFondoFijo([$udn, $fi, $ff]);

        $userLevel = $_SESSION['user_level'] ?? 'Captura';
        $moduleLocked = $this->isModuleLocked();

        $canEdit = in_array($userLevel, ['Gerencia', 'Dirección', 'Contabilidad']) && !$moduleLocked;
        $canDelete = in_array($userLevel, ['Dirección', 'Contabilidad']) && !$moduleLocked;

        foreach ($ls as $key) {
            $a = [];

            $a[] = [
                'class'   => 'btn btn-sm btn-info me-1',
                'html'    => '<i class="icon-eye"></i>',
                'onclick' => 'app.showDetails(' . $key['id'] . ')'
            ];

            if ($canEdit) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'app.editPurchase(' . $key['id'] . ')'
                ];
            }

            if ($canDelete) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-trash"></i>',
                    'onclick' => 'app.deletePurchase(' . $key['id'] . ')'
                ];
            }

            $__row[] = [
                'id'              => $key['id'],
                'Fecha'           => $key['operation_date'],
                'Clase Producto'  => $key['product_class'],
                'Producto'        => $key['product'],
                'Tipo'            => renderPurchaseType($key['purchase_type']),
                'Total'           => [
                    'html'  => '$' . number_format($key['total'], 2),
                    'class' => 'text-end'
                ],
                'a' => $a
            ];
        }

        return [
            'row'     => $__row,
            'totals'  => $totals,
            'balance' => $balance
        ];
    }

    function getPurchase() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Compra no encontrada';
        $data = null;

        $purchase = $this->getPurchaseById([$id]);

        if ($purchase) {
            $status = 200;
            $message = 'Compra encontrada';
            $data = $purchase;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function addPurchase() {
        $permissionCheck = $this->validatePermissions('add');
        if ($permissionCheck !== true) {
            return $permissionCheck;
        }

        $status = 500;
        $message = 'Error al registrar la compra';

        if (empty($_POST['product_class_id']) || empty($_POST['product_id'])) {
            return [
                'status' => 400,
                'message' => 'Campos requeridos faltantes'
            ];
        }

        if ($_POST['purchase_type_id'] == 2 && empty($_POST['method_pay_id'])) {
            return [
                'status' => 400,
                'message' => 'Debe seleccionar un método de pago para compras corporativas'
            ];
        }

        if ($_POST['purchase_type_id'] == 3 && empty($_POST['supplier_id'])) {
            return [
                'status' => 400,
                'message' => 'Debe seleccionar un proveedor para compras a crédito'
            ];
        }

        $_POST['total'] = $_POST['subtotal'] + $_POST['tax'];
        $_POST['operation_date'] = date('Y-m-d');
        $_POST['udn_id'] = $_POST['udn'];
        $_POST['active'] = 1;

        $create = $this->createPurchase($this->util->sql($_POST));

        if ($create) {
            $status = 200;
            $message = 'Compra registrada correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editPurchase() {
        $permissionCheck = $this->validatePermissions('edit');
        if ($permissionCheck !== true) {
            return $permissionCheck;
        }

        if ($this->isModuleLocked()) {
            return [
                'status' => 403,
                'message' => 'El módulo está bloqueado. No se pueden realizar modificaciones.'
            ];
        }

        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al editar compra';

        $edit = $this->updatePurchase($this->util->sql($_POST, 1));

        if ($edit) {
            $status = 200;
            $message = 'Compra actualizada correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function deletePurchase() {
        $permissionCheck = $this->validatePermissions('delete');
        if ($permissionCheck !== true) {
            return $permissionCheck;
        }

        if ($this->isModuleLocked()) {
            return [
                'status' => 403,
                'message' => 'El módulo está bloqueado. No se pueden eliminar compras.'
            ];
        }

        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al eliminar compra';

        $delete = $this->deletePurchaseById([$id]);

        if ($delete) {
            $status = 200;
            $message = 'Compra eliminada correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function getConcentrado() {
        $udn = $_POST['udn'] ?? 1;
        $fi = $_POST['fi'] ?? date('Y-m-01');
        $ff = $_POST['ff'] ?? date('Y-m-t');

        $data = $this->listConcentrado([$udn, $fi, $ff]);
        $balance = $this->getBalanceFondoFijo([$udn, $fi, $ff]);

        $__row = [];
        foreach ($data as $key) {
            $__row[] = [
                'Fecha'          => $key['fecha'],
                'Día'            => $key['dia'],
                'Clase Producto' => $key['clase_producto'],
                'Subtotal'       => '$' . number_format($key['subtotal'], 2),
                'Impuesto'       => '$' . number_format($key['impuesto'], 2),
                'Total'          => '$' . number_format($key['total'], 2)
            ];
        }

        return [
            'row'     => $__row,
            'balance' => $balance
        ];
    }

    function getProducts() {
        $classId = $_POST['product_class_id'];
        $products = $this->lsProduct([$classId]);

        return [
            'status' => 200,
            'data'   => $products
        ];
    }

    function lockModule() {
        $permissionCheck = $this->validatePermissions('lock');
        if ($permissionCheck !== true) {
            return $permissionCheck;
        }

        $month = $_POST['month'] ?? date('Y-m');
        $udn = $_POST['udn'] ?? 1;

        $query = "
            INSERT INTO {$this->bd}monthly_module_lock (udn_id, module_id, month, lock_time)
            VALUES (?, 1, ?, NOW())
            ON DUPLICATE KEY UPDATE lock_time = NOW()
        ";

        $result = $this->_Read($query, [$udn, $month]);

        return [
            'status' => 200,
            'message' => 'Módulo bloqueado correctamente'
        ];
    }

    function unlockModule() {
        $permissionCheck = $this->validatePermissions('unlock');
        if ($permissionCheck !== true) {
            return $permissionCheck;
        }

        $month = $_POST['month'] ?? date('Y-m');
        $udn = $_POST['udn'] ?? 1;

        $query = "
            UPDATE {$this->bd}monthly_module_lock 
            SET lock_time = NULL 
            WHERE udn_id = ? AND module_id = 1 AND month = ?
        ";

        $result = $this->_Read($query, [$udn, $month]);

        return [
            'status' => 200,
            'message' => 'Módulo desbloqueado correctamente'
        ];
    }
}

function renderPurchaseType($type) {
    $colors = [
        'Fondo fijo'   => 'bg-green-100 text-green-800',
        'Corporativo'  => 'bg-blue-100 text-blue-800',
        'Crédito'      => 'bg-orange-100 text-orange-800'
    ];

    $color = $colors[$type] ?? 'bg-gray-100 text-gray-800';
    return '<span class="px-2 py-1 rounded-md text-sm font-semibold ' . $color . '">' . $type . '</span>';
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
