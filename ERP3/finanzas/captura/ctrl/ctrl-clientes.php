<?php

if (empty($_POST['opc'])) exit(0);



require_once '../mdl/mdl-clientes.php';

class ctrl extends mdl {

    function init() {
        return [
            'clientes' => $this->lsClientes([1]),
            'udn' => $this->lsUDN(),
            'tiposMovimiento' => [
                ['id' => 'consumo', 'valor' => 'Consumo a crédito'],
                ['id' => 'abono_parcial', 'valor' => 'Abono parcial'],
                ['id' => 'pago_total', 'valor' => 'Pago total']
            ],
            'metodosPago' => [
                ['id' => 'N/A', 'valor' => 'N/A (No aplica)'],
                ['id' => 'efectivo', 'valor' => 'Efectivo'],
                ['id' => 'banco', 'valor' => 'Banco']
            ],
            'userLevel' => 4
        ];
    }

    function getTotales() {
        $fecha = $_POST['fecha'];
        $totales = $this->getTotalesPorFecha($fecha);

        return [
            'totalConsumos' => $totales['total_consumos'] ?? 0,
            'totalPagosEfectivo' => $totales['total_pagos_efectivo'] ?? 0,
            'totalPagosBanco' => $totales['total_pagos_banco'] ?? 0
        ];
    }



    function checkUserLevel($requiredLevel) {
        $userLevel = $_SESSION['nivel_usuario'] ?? 1;
        
        if ($userLevel < $requiredLevel) {
            return [
                'status' => 401,
                'message' => 'No tiene permisos para realizar esta acción'
            ];
        }
        
        return true;
    }

    function ls() {
        $__row = [];
        $dailyClosureId = $_POST['daily_closure_id'];
        $tipo = $_POST['tipo'];

        $ls = $this->listMovimientos([
            'daily_closure_id' => 1,
            'tipo' => $tipo
        ]);

        foreach ($ls as $key) {
            $a = [];

            $a[] = [
                'class' => 'btn btn-sm btn-info me-1',
                'html' => '<i class="icon-eye"></i>',
                'onclick' => 'app.viewDetalle(' . $key['id'] . ')'
            ];

            $a[] = [
                'class' => 'btn btn-sm btn-primary me-1',
                'html' => '<i class="icon-pencil"></i>',
                'onclick' => 'app.editMovimiento(' . $key['id'] . ')'
            ];

            $a[] = [
                'class' => 'btn btn-sm btn-danger',
                'html' => '<i class="icon-trash"></i>',
                'onclick' => 'app.deleteMovimiento(' . $key['id'] . ')'
            ];

            $tipoLabel = '';
           
            switch ($key['movement_type']) {
                case 'consumo':
                    $tipoLabel = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                        <i class="icon-lock text-orange-600"></i> Consumo
                    </span>';
                    break;
                case 'anticipo':
                    $tipoLabel = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="icon-clock text-blue-600"></i> Anticipo
                    </span>';
                    break;
                case 'pago':
                    $tipoLabel = '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="icon-check text-green-600"></i> Pago
                    </span>';
                    break;
            }

            $__row[] = [
                'id' => $key['id'],
                'Cliente'            => $key['cliente_nombre'],
                'Tipo de movimiento' => $tipoLabel,
                'Método de pago'     => $key['method_pay'],
                'Monto'              => [
                    'html'  => evaluar($key['amount']),
                    'class' => 'text-end '
                ],
                'a' => $a
            ];
        }

        return [
            'row' => $__row,
            'ls' => $ls
        ];
    }

    function getMovimiento() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Movimiento no encontrado';
        $data = null;

        $movimiento = $this->getMovimientoById($id);

        if ($movimiento) {
            $status = 200;
            $message = 'Movimiento encontrado';
            $data = $movimiento;
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }

    function getDeudaActual() {
        $clienteId = $_POST['cliente_id'];
        $deuda = $this->getDeudaActualByID($clienteId);

        return [
            'deuda' => $deuda
        ];
    }

    function addMovimiento() {
        $status = 500;
        $message = 'Error al registrar movimiento';

        if (empty($_POST['customer_id']) || empty($_POST['movement_type']) || empty($_POST['amount'])) {
            return [
                'status' => 400,
                'message' => 'Todos los campos son obligatorios'
            ];
        }

        if ($_POST['movement_type'] === 'consumo' && $_POST['method_pay'] !== 'N/A') {
            return [
                'status' => 400,
                'message' => 'El tipo consumo debe tener método de pago N/A'
            ];
        }

        $deudaAnterior = $this->getDeudaActualByID($_POST['customer_id']);
        $_POST['old_debt'] = $deudaAnterior;

        if ($_POST['movement_type'] === 'consumo') {
            $_POST['new_debt'] = $deudaAnterior + $_POST['amount'];
        } else {
            $_POST['new_debt'] = $deudaAnterior - $_POST['amount'];
        }

        $create = $this->createMovimiento($this->util->sql($_POST));

        if ($create) {
            $status = 200;
            $message = 'Movimiento registrado correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function editMovimiento() {
        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al editar movimiento';

        if (empty($_POST['customer_id']) || empty($_POST['movement_type']) || empty($_POST['amount'])) {
            return [
                'status' => 400,
                'message' => 'Todos los campos son obligatorios'
            ];
        }

        $deudaAnterior = $this->getDeudaActualByID($_POST['customer_id']);
        $_POST['old_debt'] = $deudaAnterior;

        if ($_POST['movement_type'] === 'consumo') {
            $_POST['new_debt'] = $deudaAnterior + $_POST['amount'];
        } else {
            $_POST['new_debt'] = $deudaAnterior - $_POST['amount'];
        }

        $edit = $this->updateMovimiento($this->util->sql($_POST, 1));

        if ($edit) {
            $status = 200;
            $message = 'Movimiento editado correctamente';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function deleteMovimiento() {
        $id = $_POST['id'];
        $status = 500;
        $message = 'Error al eliminar movimiento';

        $movimiento = $this->getMovimientoById($id);

        if ($movimiento) {
            $this->logAuditoria([
                'movimiento_id' => $id,
                'cliente_id' => $movimiento['customer_id'],
                'accion' => 'delete',
                'usuario_id' => $_SESSION['usuario_id'] ?? 1,
                'datos_anteriores' => $movimiento
            ]);

            $delete = $this->deleteMovimientoById([$id]);

            if ($delete) {
                $status = 200;
                $message = 'Movimiento eliminado correctamente';
            }
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function lsConcentrado() {
        $__row = [];
        $fi = $_POST['fi'];
        $ff = $_POST['ff'];
        $udn = $_POST['udn'] ?? 'todas';

        $ls = $this->listConcentrado([
            'fi' => $fi,
            'ff' => $ff,
            'udn' => $udn
        ]);

        foreach ($ls as $key) {
            $__row[] = [
                'id' => $key['cliente_id'],
                'Cliente' => $key['cliente_nombre'],
                'Saldo inicial' => [
                    'html' => evaluar($key['saldo_inicial']),
                    'class' => 'text-end'
                ],
                'Total consumos' => [
                    'html' => evaluar($key['total_consumos']),
                    'class' => 'text-end bg-green-100'
                ],
                'Total pagos' => [
                    'html' => evaluar($key['total_pagos']),
                    'class' => 'text-end bg-orange-100'
                ],
                'Saldo final' => [
                    'html' => evaluar($key['saldo_final']),
                    'class' => 'text-end font-bold'
                ]
            ];
        }

        return [
            'row' => $__row,
            'ls' => $ls
        ];
    }

    function getDetalleCliente() {
        $clienteId = $_POST['cliente_id'];
        $dailyClosureId = $_POST['daily_closure_id'];

        $cliente = $this->getClienteById($clienteId);
        $movimientos = $this->listMovimientos([
            'daily_closure_id' => $dailyClosureId,
            'tipo' => 'todos'
        ]);

        return [
            'cliente_nombre' => $cliente['name'],
            'movimientos' => $movimientos
        ];
    }

    function lsClientesAdmin() {
        $__row = [];
        $ls = $this->lsClientes([1]);

        foreach ($ls as $key) {
            $a = [];

            $a[] = [
                'class' => 'btn btn-sm btn-primary me-1',
                'html' => '<i class="icon-pencil"></i>',
                'onclick' => 'app.editCliente(' . $key['id'] . ')'
            ];

            $a[] = [
                'class' => 'btn btn-sm btn-danger',
                'html' => '<i class="icon-trash"></i>',
                'onclick' => 'app.deleteCliente(' . $key['id'] . ')'
            ];

            $deudaActual = $this->getDeudaActualByID($key['id']);

            $__row[] = [
                'id' => $key['id'],
                'Cliente' => $key['valor'],
                'Deuda Actual' => [
                    'html' => evaluar($deudaActual),
                    'class' => 'text-end'
                ],
                'a' => $a
            ];
        }

        return [
            'row' => $__row,
            'ls' => $ls
        ];
    }

    function exportExcel() {
        $fi = $_POST['fi'];
        $ff = $_POST['ff'];

        $data = $this->listConcentrado([
            'fi' => $fi,
            'ff' => $ff
        ]);

        $filename = 'concentrado_clientes_' . date('Y-m-d_His') . '.csv';
        $filepath = '../../exports/' . $filename;

        $file = fopen($filepath, 'w');
        fputcsv($file, ['Cliente', 'Saldo Inicial', 'Total Consumos', 'Total Pagos', 'Saldo Final']);

        foreach ($data as $row) {
            fputcsv($file, [
                $row['cliente_nombre'],
                $row['saldo_inicial'],
                $row['total_consumos'],
                $row['total_pagos'],
                $row['saldo_final']
            ]);
        }

        fclose($file);

        return [
            'status' => 200,
            'message' => 'Archivo generado correctamente',
            'fileUrl' => '../../exports/' . $filename
        ];
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
