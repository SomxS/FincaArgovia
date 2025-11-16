<?php

if (empty($_POST['opc'])) exit(0);



require_once '../mdl/mdl-clientes.php';

class ctrl extends mdl {

    function init() {
        $fecha = $_POST['fecha'] ?? date('Y-m-d');
        $udnId = $_POST['udn_id'] ?? 1;

        $dailyClosure = $this->getDailyClosure([$fecha, $udnId]);

        return [
            'clientes'        => $this->lsClientes([1]),
            'udn'             => $this->lsUDN(),
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
            'dailyClosure' => $dailyClosure,
            'userLevel' => 4
        ];
    }

    function DailyClousure(){

        $fecha = $_POST['date'] ?? date('Y-m-d');
        $udnId = $_POST['udn_id'] ?? 1;

        return [
            'data' =>$this->getDailyClosure([$fecha, $udnId]),
            [$fecha, $udnId]
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
        $dailyClosureId = $_POST['daily_closure_id'] ?? null;
        $tipo = $_POST['tipo'] ?? 'todos';

        if (!$dailyClosureId) {
            return [
                'row' => [],
                'ls' => [],
                'message' => 'No se proporcionó un ID de cierre diario'
            ];
        }

        $ls = $this->listMovimientos([
            'daily_closure_id' => $dailyClosureId,
            'tipo' => $tipo
        ]);

        foreach ($ls as $key) {
            $a = [];

            $a[] = [
                'class'   => 'btn btn-sm btn-info me-1',
                'html'    => '<i class="icon-eye"></i>',
                'onclick' => 'clientes.viewDetalle(' . $key['id'] . ')'
            ];

            $a[] = [
                'class' => 'btn btn-sm btn-primary me-1',
                'html' => '<i class="icon-pencil"></i>',
                'onclick' => 'clientes.editMovimiento(' . $key['id'] . ')'
            ];

            $a[] = [
                'class' => 'btn btn-sm btn-danger',
                'html' => '<i class="icon-trash"></i>',
                'onclick' => 'clientes.deleteMovimiento(' . $key['id'] . ')'
            ];

            $__row[] = [
                'id' => $key['id'],
                'Cliente'            => $key['cliente_nombre'],
                'Tipo de movimiento' => renderMovementType($key['movement_type']),
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
        $id      = $_POST['id'];
        $status  = 404;
        $message = 'Movimiento no encontrado';
        $data    = null;

        $movimiento = $this->getMovimientoById($id);

        if ($movimiento) {
            $status  = 200;
            $message = 'Movimiento encontrado';
            $data    = $movimiento;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
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

       

        // if ($_POST['movement_type'] === 'consumo' && $_POST['method_pay'] !== 'N/A') {
        //     return [
        //         'status' => 400,
        //         'message' => 'El tipo consumo debe tener método de pago N/A'
        //     ];
        // }

        // $deudaAnterior = $this->getDeudaActualByID($_POST['customer_id']);
        // $_POST['old_debt'] = $deudaAnterior;

        // if ($_POST['movement_type'] === 'consumo') {
        //     $_POST['new_debt'] = $deudaAnterior + $_POST['amount'];
        // } else {
        //     $_POST['new_debt'] = $deudaAnterior - $_POST['amount'];
        // }

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

        // $movimiento = $this->getMovimientoById($id);

        // if ($movimiento) {
        //     // $this->logAuditoria([
        //     //     'movimiento_id' => $id,
        //     //     'cliente_id' => $movimiento['customer_id'],
        //     //     'accion' => 'delete',
        //     //     'usuario_id' => $_SESSION['usuario_id'] ?? 1,
        //     //     'datos_anteriores' => $movimiento
        //     // ]);
        $values  = $this->util->sql([
            'active' =>0,
            'id'     => $id
        ], 1);
        $delete = $this->deleteMovimientoById($values);

            if ($delete) {
                $status = 200;
                $message = 'Movimiento eliminado correctamente';
            }
        // }

        return [
            'status' => $status,
            'message' => $message,
            'endpoint' =>$values
        ];
    }


    // Concentrado clientes.


    function getTotalesConcentrado() {
        $fi  = $_POST['fi'];
        $ff  = $_POST['ff'];
        $udn = $_POST['udn'] ?? 'todas';

        $ls = $this->listConcentrado([
            'fi' => $fi,
            'ff' => $ff,
            'udn' => $udn
        ]);

        $saldoInicial = 0;
        $totalConsumos = 0;
        $totalPagos = 0;
        $saldoFinal = 0;

        foreach ($ls as $key) {
            $saldoInicial += $key['saldo_inicial'];
            $totalConsumos += $key['total_consumos'];
            $totalPagos += $key['total_pagos'];
            $saldoFinal += $key['saldo_final'];
        }

        return [
            'saldoInicial' => $saldoInicial,
            'totalConsumos' => $totalConsumos,
            'totalPagos' => $totalPagos,
            'saldoFinal' => $saldoFinal
        ];
    }

    function lsConcentrado() {
        $rows = [];
        $fi   = $_POST['fi'];
        $ff   = $_POST['ff'];
        $udn  = $_POST['udn'] ?? 'todas';

        $clientes = $this->listConcentrado([
            'fi' => $fi,
            'ff' => $ff,
            'udn' => $udn
        ]);

        $dias = [];
        $diasName = [];
        $thead = ['CLIENTES', 'DEUDA'];

        $start = new DateTime($fi);
        $end = new DateTime($ff);
        $end->modify('+1 day');
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);

        foreach ($period as $date) {
            $fecha = $date->format('Y-m-d');
            $fechaName = strtoupper($date->format('d \D\E M'));
            
            $dias[] = $fecha;
            $diasName[] = $fechaName;
            
            $thead[] = "CONSUMOS $fechaName";
            $thead[] = "PAGOS $fechaName";
        }

        foreach ($clientes as $cliente) {
            $clienteId = $cliente['cliente_id'];
            $clienteNombre = $cliente['cliente_nombre'];

            $headerCliente = [
                'id' => $clienteId,
                'key' => '',
                'CLIENTES' => $clienteNombre,
                'DEUDA' => [
                    'html' => evaluar($cliente['saldo_inicial']),
                    'class' => 'text-end font-bold'
                ]
            ];

            foreach ($diasName as $dia) {
                $headerCliente["CONSUMOS $dia"] = '';
                $headerCliente["PAGOS $dia"] = '';
            }

            $headerCliente['opc'] = 1;
            $idxHeader = count($rows);
            $rows[] = $headerCliente;

            $movimientos = $this->getMovimientosByCliente([
                $clienteId,
                $fi,
                $ff
            ]);

            $movimientosPorDia = [];
            foreach ($movimientos as $mov) {
                $fecha = $mov['fecha'];
                if (!isset($movimientosPorDia[$fecha])) {
                    $movimientosPorDia[$fecha] = [
                        'consumos' => 0,
                        'pagos' => 0
                    ];
                }

                if ($mov['movement_type'] === 'consumo') {
                    $movimientosPorDia[$fecha]['consumos'] += floatval($mov['amount']);
                } else {
                    $movimientosPorDia[$fecha]['pagos'] += floatval($mov['amount']);
                }
            }

            $saldoActual = floatval($cliente['saldo_inicial']);
            $totalConsumos = 0;
            $totalPagos = 0;

            $rowSaldoInicial = [
                'id' => $clienteId,
                'key' => 'saldo_inicial',
                'CLIENTES' => 'Saldo inicial',
                'DEUDA' => [
                    'html' => evaluar($saldoActual),
                    'class' => 'text-end'
                ]
            ];

            foreach ($dias as $i => $fecha) {
                $rowSaldoInicial["CONSUMOS {$diasName[$i]}"] = '-';
                $rowSaldoInicial["PAGOS {$diasName[$i]}"] = '-';
            }
            $rowSaldoInicial['opc'] = 0;
            $rows[] = $rowSaldoInicial;

            $rowConsumos = [
                'id' => $clienteId,
                'key' => 'consumos',
                'CLIENTES' => 'Consumo a crédito',
                'DEUDA' => ''
            ];

            $rowPagos = [
                'id' => $clienteId,
                'key' => 'pagos',
                'CLIENTES' => 'Pagos y anticipos',
                'DEUDA' => ''
            ];

            $rowSaldoFinal = [
                'id' => $clienteId,
                'key' => 'saldo_final',
                'CLIENTES' => 'Saldo final',
                'DEUDA' => ''
            ];

            foreach ($dias as $i => $fecha) {
                $consumo = $movimientosPorDia[$fecha]['consumos'] ?? 0;
                $pago = $movimientosPorDia[$fecha]['pagos'] ?? 0;

                $totalConsumos += $consumo;
                $totalPagos += $pago;

                $rowConsumos["CONSUMOS {$diasName[$i]}"] = [
                    'html' => $consumo > 0 ? evaluar($consumo) : '-',
                    'val' => $consumo,
                    'class' => 'text-end bg-green-50'
                ];
                $rowConsumos["PAGOS {$diasName[$i]}"] = '-';

                $rowPagos["CONSUMOS {$diasName[$i]}"] = '-';
                $rowPagos["PAGOS {$diasName[$i]}"] = [
                    'html' => $pago > 0 ? '<span class="text-red-600">' . evaluar($pago) . '</span>' : '-',
                    'val' => $pago,
                    'class' => 'text-end bg-red-50'
                ];

                $saldoActual = $saldoActual + $consumo - $pago;

                $rowSaldoFinal["CONSUMOS {$diasName[$i]}"] = '-';
                $rowSaldoFinal["PAGOS {$diasName[$i]}"] = [
                    'html' => evaluar($saldoActual),
                    'val' => $saldoActual,
                    'class' => 'text-end'
                ];
            }

            $rowConsumos['opc'] = 0;
            $rowPagos['opc'] = 0;
            $rowSaldoFinal['opc'] = 0;

            $rows[] = $rowConsumos;
            $rows[] = $rowPagos;
            $rows[] = $rowSaldoFinal;

            $rowTotalConsumos = [
                'id' => $clienteId,
                'key' => 'total_consumos',
                'CLIENTES' => 'Total de consumos a crédito',
                'DEUDA' => [
                    'html' => '<strong>' . evaluar($totalConsumos) . '</strong>',
                    'class' => 'text-end font-bold'
                ]
            ];

            $rowTotalPagos = [
                'id' => $clienteId,
                'key' => 'total_pagos',
                'CLIENTES' => 'Total de pagos y anticipos',
                'DEUDA' => [
                    'html' => '<strong class="text-red-600">' . evaluar($totalPagos) . '</strong>',
                    'class' => 'text-end font-bold'
                ]
            ];

            foreach ($diasName as $dia) {
                $rowTotalConsumos["CONSUMOS $dia"] = '';
                $rowTotalConsumos["PAGOS $dia"] = '';
                $rowTotalPagos["CONSUMOS $dia"] = '';
                $rowTotalPagos["PAGOS $dia"] = '';
            }

            $rowTotalConsumos['opc'] = 0;
            $rowTotalPagos['opc'] = 0;

            $rows[] = $rowTotalConsumos;
            $rows[] = $rowTotalPagos;
        }

        return [
            'thead' => $thead,
            'row' => $rows
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

// Complements

function renderMovementType($movementType) {
    switch ($movementType) {
        case 'consumo':
            return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                <i class="icon-lock text-orange-600"></i> Consumo
            </span>';
        case 'anticipo':
            return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                <i class="icon-clock text-blue-600"></i> Anticipo
            </span>';
        case 'pago':
            return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <i class="icon-check text-green-600"></i> Pago
            </span>';
        default:
            return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                <i class="icon-help text-gray-600"></i> Desconocido
            </span>';
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
