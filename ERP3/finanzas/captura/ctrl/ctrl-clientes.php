<?php

if (empty($_POST['opc'])) exit(0);



require_once '../mdl/mdl-clientes.php';

class ctrl extends mdl {

    function init() {
        return [
            'clientes' => $this->lsClientes(),
             'udn' => $this->lsUDN([1]),
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
        $fecha   = $_POST['fecha'];
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
        $fecha = $_POST['fecha'];
        $tipo = $_POST['tipo'];

        $ls = $this->listMovimientos([
            'fecha' => $fecha,
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
                    $tipoLabel = '<span class="badge bg-warning">Consumo</span>';
                    break;
                case 'abono_parcial':
                    $tipoLabel = '<span class="badge bg-info">Abono parcial</span>';
                    break;
                case 'pago_total':
                    $tipoLabel = '<span class="badge bg-success">Pago total</span>';
                    break;
            }

            $__row[] = [
                'id' => $key['id'],
                'Cliente' => $key['cliente_nombre'],
                'Tipo de movimiento' => $tipoLabel,
                'Método de pago' => $key['payment_method'],
                'Monto' => [
                    'html' => evaluar($key['quantity']),
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

        if (empty($_POST['cliente_id']) || empty($_POST['tipo_movimiento']) || empty($_POST['cantidad'])) {
            return [
                'status' => 400,
                'message' => 'Todos los campos son obligatorios'
            ];
        }

        if ($_POST['tipo_movimiento'] === 'consumo' && $_POST['metodo_pago'] !== 'N/A') {
            return [
                'status' => 400,
                'message' => 'El tipo consumo debe tener método de pago N/A'
            ];
        }

        $_POST['fecha_captura'] = date('Y-m-d');
        $_POST['hora_captura'] = date('H:i:s');
        $_POST['usuario_id'] = $_SESSION['usuario_id'] ?? 1;

        $deudaAnterior = $this->getDeudaActual($_POST['cliente_id']);
        $_POST['deuda_anterior'] = $deudaAnterior;

        if ($_POST['tipo_movimiento'] === 'consumo') {
            $_POST['deuda_nueva'] = $deudaAnterior + $_POST['cantidad'];
        } else {
            $_POST['deuda_nueva'] = $deudaAnterior - $_POST['cantidad'];
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

        if (empty($_POST['cliente_id']) || empty($_POST['tipo_movimiento']) || empty($_POST['cantidad'])) {
            return [
                'status' => 400,
                'message' => 'Todos los campos son obligatorios'
            ];
        }

        $deudaAnterior = $this->getDeudaActual($_POST['cliente_id']);
        $_POST['deuda_anterior'] = $deudaAnterior;

        if ($_POST['tipo_movimiento'] === 'consumo') {
            $_POST['deuda_nueva'] = $deudaAnterior + $_POST['cantidad'];
        } else {
            $_POST['deuda_nueva'] = $deudaAnterior - $_POST['cantidad'];
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
                'cliente_id' => $movimiento['cliente_id'],
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
        $fi = $_POST['fi'];
        $ff = $_POST['ff'];

        $cliente = $this->getClienteById($clienteId);
        $movimientos = $this->listMovimientos([
            'fecha' => null,
            'tipo' => 'todos',
            'cliente_id' => $clienteId,
            'fi' => $fi,
            'ff' => $ff
        ]);

        return [
            'cliente_nombre' => $cliente['name'],
            'movimientos' => $movimientos
        ];
    }

    function lsClientesAdmin() {
        $__row = [];
        $ls = $this->lsClientes();

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

            $__row[] = [
                'id' => $key['id'],
                'Cliente' => $key['name'],
                'Deuda Inicial' => [
                    'html' => evaluar($key['deuda_inicial'] ?? 0),
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
