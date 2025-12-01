<?php

if (empty($_POST['opc'])) exit(0);


require_once '../mdl/mdl-banco.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn'            => $this->lsUDN(),
            'paymentMethods' => $this->lsPaymentMethods(),
            'banks'          => $this->listBanks([1])
        ];
    }

    function lsBankAccounts() {
        $active = $_POST['active'] ?? 1;
        $udn_id = $_POST['udn'] ?? null;
        
        $data = $this->listBankAccounts([
            'active' => $active,
            'udn_id' => $udn_id
        ]);
        
        $rows = [];

        foreach ($data as $item) {
            $a = [];

            if ($active == 1) {
                $a[] = [
                    'class' => 'btn btn-sm btn-primary me-1',
                    'html' => '<i class="icon-pencil"></i>',
                    'onclick' => 'banco.editBankAccount(' . $item['id'] . ')'
                ];

                $a[] = [
                    'class' => 'btn btn-sm btn-danger',
                    'html' => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'banco.toggleStatusAccount(' . $item['id'] . ', ' . $item['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'html' => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'banco.toggleStatusAccount(' . $item['id'] . ', ' . $item['active'] . ')'
                ];
            }

            $last4 = substr($item['account'], -4);
            $accountDisplay = $item['bank_name'] . '(' . $last4 . ')';

            $rows[] = [
                'id' => $item['id'],
                'Cuenta bancaria' => $accountDisplay,
                'Estado' => renderStatus($item['active']),
                'a' => $a
            ];
        }

        return [
            'row' => $rows,
            'ls' => $data
        ];
    }

    function getBank() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Banco no encontrado';
        $data = null;

        $bank = $this->getBankById([$id]);

        if ($bank) {
            $status = 200;
            $message = 'Banco encontrado';
            $data = $bank;
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    function getBankAccount() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Cuenta bancaria no encontrada';
        $data = null;

        $account = $this->getBankAccountById([$id]);

        if ($account) {
            $status = 200;
            $message = 'Cuenta bancaria encontrada';
            $data = $account;
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    function addBank() {
        $status = 500;
        $message = 'Error al agregar banco';


        $_POST['active'] = 1;

        $exists = $this->existsBankByName([$_POST['name']]);

        if ($exists) {
            return [
                'status' => 409,
                'message' => 'Ya existe un banco con ese nombre'
            ];
        }

        $create = $this->createBank($this->util->sql($_POST));

        if ($create) {
            $status = 200;
            $message = 'Banco agregado correctamente';
        }

        return [
            'status' => $status,
            'message' => $message,
            'end-point' => $this->util->sql($_POST)
        ];
    }

    function addBankAccount() {
        $status = 500;
        $message = 'Error al agregar cuenta bancaria';

       
        $_POST['active'] = 1;

        $create = $this->createBankAccount($this->util->sql($_POST));

        if ($create) {
            $status = 200;
            $message = 'Cuenta bancaria agregada correctamente';
        }

        return [
            'status' => $status,
            'message' => $message,
            'values'  =>$this->util->sql($_POST)
                ];
    }

    function editBankAccount() {
        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al editar cuenta bancaria';

        if (empty($_POST['bank_id']) || empty($_POST['account']) || empty($_POST['name'])) {
            return [
                'status' => 400,
                'message' => 'Todos los campos son obligatorios'
            ];
        }

        $edit = $this->updateBankAccount($this->util->sql($_POST, 1));

        if ($edit) {
            $status = 200;
            $message = 'Cuenta bancaria actualizada correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function toggleStatusAccount() {
        $status = 500;
        $message = 'Error al cambiar el estado de la cuenta';

        $newStatus = $_POST['active'];
        
        $update = $this->updateBankAccount($this->util->sql($_POST, 1));

        if ($update) {
            $status = 200;
            if ($newStatus == 1) {
                $message = 'La cuenta estará disponible para captura de información.';
            } else {
                $message = 'La cuenta bancaria ya no estará disponible, pero seguirá reflejándose en los registros contables.';
            }
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }
}

function renderStatus($status) {
    switch ($status) {
        case 1:
            return '<span class="inline-block px-3 py-1 rounded-2xl text-xs font-semibold bg-green-100 text-green-700 min-w-[100px] text-center">Activo</span>';
        case 0:
            return '<span class="inline-block px-3 py-1 rounded-2xl text-xs font-semibold bg-red-100 text-red-700 min-w-[100px] text-center">Inactivo</span>';
        default:
            return '<span class="inline-block px-3 py-1 rounded-2xl text-xs font-semibold bg-gray-100 text-gray-700 min-w-[100px] text-center">Desconocido</span>';
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
