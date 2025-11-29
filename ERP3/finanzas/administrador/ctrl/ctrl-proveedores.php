<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-proveedores.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn' => $this->lsUDN()
        ];
    }

    function lsSuppliers() {
        $__row = [];
        $udn = $_POST['udn'];
        $active = $_POST['active'] ?? 1;
        
        $ls = $this->listSupplier([$udn, $active]);

        foreach ($ls as $key) {
            $a = [];

            if ($key['active'] == 1) {
                $a[] = [
                    'class' => 'btn btn-sm btn-primary me-1',
                    'html' => '<i class="icon-pencil"></i>',
                    'onclick' => 'supplier.editSupplier(' . $key['id'] . ')'
                ];

                $a[] = [
                    'class' => 'btn btn-sm btn-danger',
                    'html' => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'supplier.toggleStatus(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'html' => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'supplier.toggleStatus(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            }

            $__row[] = [
                'id'        => $key['id'],
                'Proveedor' => $key['name'],
                'UDN'       => $key['udn_name'],
                'Estado'    => renderStatus($key['active']),
                'a'         => $a
            ];
        }

        return [
            'row' => $__row,
            'ls' => $ls
        ];
    }

    function getSupplier() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Proveedor no encontrado';
        $data = null;

        $supplier = $this->getSupplierById([$id]);

        if ($supplier) {
            $status = 200;
            $message = 'Proveedor encontrado';
            $data = $supplier;
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    function addSupplier() {
        $status = 500;
        $message = 'Error al agregar proveedor';

        if (empty($_POST['name'])) {
            return [
                'status' => 400,
                'message' => 'El nombre del proveedor es obligatorio'
            ];
        }

        if (empty($_POST['udn_id'])) {
            return [
                'status' => 400,
                'message' => 'Debe seleccionar una unidad de negocio'
            ];
        }

        $_POST['active'] = 1;

        $exists = $this->existsSupplierByName([$_POST['name'], $_POST['udn_id']]);

        if ($exists) {
            return [
                'status' => 409,
                'message' => 'Ya existe un proveedor con ese nombre para esta unidad de negocio'
            ];
        }

        $create = $this->createSupplier($this->util->sql($_POST));

        if ($create) {
            $status = 200;
            $message = 'Proveedor agregado correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editSupplier() {
        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al editar proveedor';

        if (empty($_POST['name'])) {
            return [
                'status' => 400,
                'message' => 'El nombre del proveedor es obligatorio'
            ];
        }

        $edit = $this->updateSupplier($this->util->sql($_POST, 1));

        if ($edit) {
            $status = 200;
            $message = 'Proveedor actualizado correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function statusSupplier() {
        $status = 500;
        $message = 'No se pudo actualizar el estado';

        $update = $this->updateSupplier($this->util->sql($_POST, 1));

        if ($update) {
            $status = 200;
            $message = 'El estado del proveedor se actualizó correctamente';
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
            return '<span class="px-3 py-1 rounded-2xl text-xs font-semibold bg-green-100 text-green-700 inline-block min-w-[80px] text-center">Activo</span>';
        case 0:
            return '<span class="px-3 py-1 rounded-2xl text-xs font-semibold bg-red-100 text-red-700 inline-block min-w-[80px] text-center">Inactivo</span>';
        default:
            return '<span class="px-3 py-1 rounded-2xl text-xs font-semibold bg-gray-100 text-gray-700 inline-block min-w-[80px] text-center">Desconocido</span>';
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
