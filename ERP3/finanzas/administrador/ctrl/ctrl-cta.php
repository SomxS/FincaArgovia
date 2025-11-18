<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-cta.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn' => $this->lsUDN(),
            'mayorAccount' => $this->lsProductClass()
        ];
    }

    // Cuenta de mayor (product_class)

    function lsMayorAccount() {
        $__row = [];
        $udn = $_POST['udn'];
        
        $ls = $this->listProductClass([$udn]);

        foreach ($ls as $key) {
            $a = [];

            if ($key['active'] == 1) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'mayorAccount.editMayorAccount(' . $key['id'] . ')'
                ];

                $a[] = [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'mayorAccount.statusMayorAccount(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class'   => 'btn btn-sm btn-outline-danger',
                    'html'    => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'mayorAccount.statusMayorAccount(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            }

            $__row[] = [
                'id'            => $key['id'],
                'Cuenta de mayor' => $key['name'],
                'Estado'        => renderStatus($key['active']),
                'a'             => $a
            ];
        }

        return [
            'row' => $__row,
            'ls'  => $ls
        ];
    }

    function getMayorAccount() {

        $id      = $_POST['id'];
        $status  = 404;
        $message = 'Cuenta no encontrada';
        $data    = null;

        $account = $this->getProductClassById([$id]);

        if ($account) {
            $status = 200;
            $message = 'Cuenta encontrada';
            $data = $account;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function addMayorAccount() {
        $status = 500;
        $message = 'No se pudo agregar la cuenta';
        // $_POST['date_creation'] = date('Y-m-d H:i:s');
        $_POST['active'] = 1;

        $exists = $this->existsProductClassByName([$_POST['name'], $_POST['udn']]);

        if ($exists === 0) {
            $create = $this->createProductClass($this->util->sql($_POST));
            if ($create) {
                $status = 200;
                $message = 'Cuenta de mayor agregada correctamente';
            }
        } else {
            $status = 409;
            $message = 'Ya existe una cuenta de mayor con ese nombre en esta unidad de negocio.';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editMayorAccount() {
        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al editar cuenta';

        $edit = $this->updateProductClass($this->util->sql($_POST, 1));
        if ($edit) {
            $status = 200;
            $message = 'Cuenta de mayor editada correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function statusMayorAccount() {
        $status = 500;
        $message = 'No se pudo actualizar el estado';

        $update = $this->updateProductClass($this->util->sql($_POST, 1));

        if ($update) {
            $status = 200;
            $message = 'El estado de la cuenta se actualizó correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }


    function lsMayorAccountByUdn() {
        $udn = $_POST['udn'];
        $data = $this->lsProductClass([$udn]);
        
        return [
            'status' => 200,
            'data' => $data
        ];
    }

    // Sub cuenta 

    function lsSubAccount() {
        $__row = [];
        $udn = $_POST['udn'];
        
        $ls = $this->listProduct([$udn]);

        foreach ($ls as $key) {
            $a = [];

            if ($key['active'] == 1) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'subAccount.editSubAccount(' . $key['id'] . ')'
                ];

                $a[] = [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'subAccount.statusSubAccount(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class'   => 'btn btn-sm btn-outline-danger',
                    'html'    => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'subAccount.statusSubAccount(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            }

            $__row[] = [
                'id'              => $key['id'],
                'Subcuenta'       => $key['name'],
                'Cuenta de mayor' => $key['product_class_name'],
                'Estado'          => renderStatus($key['active']),
                'a'               => $a
            ];
        }

        return [
            'row' => $__row,
            'ls'  => $ls
        ];
    }

    function getSubAccount() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Subcuenta no encontrada';
        $data = null;

        $subAccount = $this->getProductById([$id]);

        if ($subAccount) {
            $status = 200;
            $message = 'Subcuenta encontrada';
            $data = $subAccount;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function addSubAccount() {
        $status = 500;
        $message = 'No se pudo agregar la subcuenta';
        $_POST['active'] = 1;

        $exists = $this->existsProductByName([$_POST['name'], $_POST['udn_id']]);

        if ($exists === 0) {
            $create = $this->createProduct($this->util->sql($_POST));
            if ($create) {
                $status = 200;
                $message = 'Subcuenta agregada correctamente';
            }
        } else {
            $status = 409;
            $message = 'Ya existe una subcuenta con ese nombre en esta unidad de negocio.';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editSubAccount() {
        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al editar subcuenta';

        $edit = $this->updateProduct($this->util->sql($_POST, 1));
        if ($edit) {
            $status = 200;
            $message = 'Subcuenta editada correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function statusSubAccount() {
        $status = 500;
        $message = 'No se pudo actualizar el estado';

        $update = $this->updateProduct($this->util->sql($_POST, 1));

        if ($update) {
            $status = 200;
            $message = 'El estado de la subcuenta se actualizó correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function lsPurchaseType() {
        $__row = [];
        $udn = $_POST['udn'];
        
        $ls = $this->listPurchaseType([$udn]);

        foreach ($ls as $key) {
            $__row[] = [
                'id'            => $key['id'],
                'Tipo de compra' => $key['name'],
                'Descripción'   => $key['description'],
                'Estado'        => renderStatus($key['active']),
                'opc'           => 0
            ];
        }

        return [
            'row' => $__row,
            'ls'  => $ls
        ];
    }

    

    function lsPaymentMethod() {
        $__row = [];
        $udn = $_POST['udn'];
        
        $ls = $this->listPaymentMethod([$udn]);

        foreach ($ls as $key) {
            $__row[] = [
                'id'           => $key['id'],
                'Forma de pago' => $key['name'],
                'Descripción'  => $key['description'],
                'Estado'       => renderStatus($key['active']),
                'opc'          => 0
            ];
        }

        return [
            'row' => $__row,
            'ls'  => $ls
        ];
    }
}

function renderStatus($status) {
    switch ($status) {
        case 1:
            return '<span class="px-3 py-1 rounded-lg text-sm font-semibold bg-green-100 text-green-700 inline-block min-w-[80px] text-center">Activo</span>';
        case 0:
            return '<span class="px-3 py-1 rounded-lg text-sm font-semibold bg-red-100 text-red-700 inline-block min-w-[80px] text-center">Inactivo</span>';
        default:
            return '<span class="px-3 py-1 rounded-lg text-sm font-semibold bg-gray-100 text-gray-700 inline-block min-w-[80px] text-center">Desconocido</span>';
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
