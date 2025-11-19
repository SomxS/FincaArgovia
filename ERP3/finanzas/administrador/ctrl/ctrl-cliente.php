<?php
session_start();

if (empty($_POST['opc'])) exit(0);



require_once '../mdl/mdl-cliente.php';

class ctrl extends mdl {


    function lsClientes() {
        $__row = [];
        $active = $_POST['active'];
        $udn = $_POST['udn'];

        $ls = $this->listClientes([$active, $udn]);
        
        // Validar que $ls sea un array antes de iterar
        if (!is_array($ls)) {
            $ls = [];
        }

        foreach ($ls as $key) {
            $a = [];

            if ($active == 1) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'client.editCliente(' . $key['id'] . ')'
                ];

                $a[] = [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-toggle-on"></i>',
                    'onclick' => 'client.statusCliente(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            } else {
                $a[] = [
                    'class'   => 'btn btn-sm btn-outline-danger',
                    'html'    => '<i class="icon-toggle-off"></i>',
                    'onclick' => 'client.statusCliente(' . $key['id'] . ', ' . $key['active'] . ')'
                ];
            }

            $__row[] = [
                'id'     => $key['id'],
                'Nombre' => $key['name'],
                'Estado' => renderStatus($key['active']),
                'a'      => $a
            ];
        }

        return [
            'row' => $__row,
            'ls'  => $ls
        ];
    }

    function getCliente() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Cliente no encontrado';
        $data = null;

        $cliente = $this->getClienteById([$id]);

        if ($cliente) {
            $status  = 200;
            $message = 'Cliente encontrado';
            $data    = $cliente;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function addCliente() {
        $status               = 500;
        $message              = 'No se pudo agregar el cliente';
        $_POST['active']      = 1;

        $exists = $this->existsClienteByName([$_POST['name'], $_POST['udn_id']]);

        if ($exists === 0) {
            $create = $this->createCliente($this->util->sql($_POST));
            if ($create) {
                $status = 200;
                $message = 'Cliente agregado correctamente';
            }
        } else {
            $status = 409;
            $message = 'Ya existe un cliente con ese nombre en esta unidad de negocio.';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editCliente() {
        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al editar cliente';

        $edit = $this->updateCliente($this->util->sql($_POST, 1));
        if ($edit) {
            $status = 200;
            $message = 'Cliente editado correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function statusCliente() {
        $status = 500;
        $message = 'No se pudo actualizar el estado del cliente';

        $update = $this->updateCliente($this->util->sql($_POST, 1));

        if ($update) {
            $status = 200;
            $message = 'El estado del cliente se actualizó correctamente';
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
            return '<span class="px-3 py-1 rounded-lg text-sm font-semibold bg-green-100 text-green-700 inline-block min-w-[80px] text-center">Activo</span>';
        case 0:
            return '<span class="px-3 py-1 rounded-lg text-sm font-semibold bg-red-100 text-red-700 inline-block min-w-[80px] text-center">Inactivo</span>';
        default:
            return '<span class="px-3 py-1 rounded-lg text-sm font-semibold bg-gray-100 text-gray-700 inline-block min-w-[80px] text-center">Desconocido</span>';
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
