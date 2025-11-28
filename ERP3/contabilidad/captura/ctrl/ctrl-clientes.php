<?php

if (empty($_POST['opc'])) exit(0);



require_once '../mdl/mdl-clientes.php';

class ctrl extends mdl {

    function init() {
        $fecha        = $_POST['fecha'] ?? date('Y-m-d');
        $udnId        = $_POST['udn_id'] ?? 4;
        $dailyClosure = $this->getDailyClosure([$fecha, $udnId]);

        return [
            'clientes'        => $this->lsClientes([1]),
            'udn'             => $this->lsUDN(),
            'tiposMovimiento' => [
                ['id' => 'consumo',       'valor' => 'Consumo a crédito'],
                ['id' => 'abono_parcial', 'valor' => 'Abono parcial'],
                ['id' => 'pago_total',    'valor' => 'Pago total']
            ],
            'metodosPago' => [
                ['id' => 'N/A',      'valor' => 'N/A (No aplica)'],
                ['id' => 'efectivo', 'valor' => 'Efectivo'],
                ['id' => 'banco',    'valor' => 'Banco']
            ],
            'dailyClosure' => $dailyClosure,
            'userLevel'    => 4
        ];
    }

    function DailyClousure(){
        $fecha = $_POST['date'] ?? date('Y-m-d');
        $udnId = $_POST['udn_id'] ?? 4;

        return [
            'data' => $this->getDailyClosure([$fecha, $udnId]),
            [$fecha, $udnId]
        ];
    }

    function getTotales() {
        $fecha   = $_POST['fecha'];
        $totales = $this->getTotalesPorFecha($fecha);

        return [
            'totalConsumos'      => $totales['total_consumos'] ?? 0,
            'totalPagosEfectivo' => $totales['total_pagos_efectivo'] ?? 0,
            'totalPagosBanco'    => $totales['total_pagos_banco'] ?? 0
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
        $__row          = [];
        $dailyClosureId = $_POST['daily_closure_id'] ?? null;
        $tipo           = $_POST['tipo'] ?? 'todos';

        if (!$dailyClosureId) {
            return [
                'row'     => [],
                'ls'      => [],
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
                'id'                 => $key['id'],
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
        $status  = 500;
        $message = 'Error al registrar movimiento';
        $create  = $this->createMovimiento($this->util->sql($_POST));

        if ($create) {
            $status  = 200;
            $message = 'Movimiento registrado correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function editMovimiento() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al editar movimiento';

        if (empty($_POST['customer_id']) || empty($_POST['movement_type']) || empty($_POST['amount'])) {
            return [
                'status'  => 400,
                'message' => 'Todos los campos son obligatorios'
            ];
        }

        $deudaAnterior         = $this->getDeudaActualByID($_POST['customer_id']);
        $_POST['old_debt']     = $deudaAnterior;
        $_POST['new_debt']     = ($_POST['movement_type'] === 'consumo') 
                                 ? $deudaAnterior + $_POST['amount'] 
                                 : $deudaAnterior - $_POST['amount'];
        $edit                  = $this->updateMovimiento($this->util->sql($_POST, 1));

        if ($edit) {
            $status  = 200;
            $message = 'Movimiento editado correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function deleteMovimiento() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al eliminar movimiento';
        $values  = $this->util->sql([
            'active' => 0,
            'id'     => $id
        ], 1);
        $delete = $this->deleteMovimientoById($values);

        if ($delete) {
            $status  = 200;
            $message = 'Movimiento eliminado correctamente';
        }

        return [
            'status'   => $status,
            'message'  => $message,
            'endpoint' => $values
        ];
    }


    // Concentrado clientes.


    function getTotalesConcentrado() {
        $fi  = $_POST['fi'];
        $ff  = $_POST['ff'];
        $udn = $_POST['udn'] ?? 'todas';
        $ls  = $this->listConcentrado([
            'fi'  => $fi,
            'ff'  => $ff,
            'udn' => $udn
        ]);

        $saldoInicial  = 0;
        $totalConsumos = 0;
        $totalPagos    = 0;
        $saldoFinal    = 0;

        foreach ($ls as $key) {
            $saldoInicial  += $key['saldo_inicial'];
            $totalConsumos += $key['total_consumos'];
            $totalPagos    += $key['total_pagos'];
            $saldoFinal    += $key['saldo_final'];
        }

        return [
            'saldoInicial'  => $saldoInicial,
            'totalConsumos' => $totalConsumos,
            'totalPagos'    => $totalPagos,
            'saldoFinal'    => $saldoFinal
        ];
    }

    function lsConcentrado() {
        
        // Vars.
        $fi    = $_POST['fi'];
        $ff    = $_POST['ff'];
        $udn   = $_POST['udn'];
        
        $start = new DateTime($fi);
        $end   = new DateTime($ff);
        $end->modify('+1 day');

        $interval = new DateInterval('P1D');
        $period   = new DatePeriod($start, $interval, $end);
                
        $dias        = [];
        $diasName    = [];
        $rows        = [];
              
        $diasSemana = getDiasSemanaEspanol();
        $meses      = getMesesEspanol();
   

        // Crear thead.
        $thead       = ['CLIENTES', 'DEUDA'];
        $theadGroups = [];

        $theadGroups[] = [
            'label'   => '',
            'colspan' => 2,
            'color'   => 'bg-[#003360] text-white'
        ];


        $colIndex = 0;
        foreach ($period as $date) {
            $fecha       = $date->format('Y-m-d');
            $dia         = $date->format('d');
            $mesEn       = $date->format('M');
            $mesEs       = $meses[$mesEn];
            $diaSemanaEn = $date->format('l');
            $diaSemanaEs = $diasSemana[$diaSemanaEn];
            
            $dias[]     = $fecha;
            $diasName[] = "$diaSemanaEs, $dia DE $mesEs";
            $thead[]    = 'CONSUMOS';
            $thead[]    = 'PAGOS';

            $theadGroups[] = [
                'label'   => "$diaSemanaEs, $dia DE $mesEs",
                'colspan' => 2,
                'color'   => 'bg-[#003360] text-white'
            ];
            $colIndex++;
        }


        // Crear body.
        $totalConsumos = array_fill(0, count($dias), 0);
        $totalPagos    = array_fill(0, count($dias), 0);
      
        $subRows = [
            ['label' => 'Saldo inicial', 'type' => 'saldo_inicial'],
            ['label' => 'Consumo a crédito', 'type' => 'consumo'],
            ['label' => 'Pagos y anticipos', 'type' => 'pagos'],
            ['label' => 'Saldo final', 'type' => 'saldo_final']
        ];

        $clientes      = $this->listCustomer([$fi, $ff, $udn]);

        foreach ($clientes as $cliente) {

            $clienteId     = $cliente['id'];
            $clienteNombre = $cliente['valor'];
            
            $totalesCliente = $this->getTotalesClientePeriodo($clienteId, $fi, $ff);
            $consumoTotal   = floatval($totalesCliente['total_consumos']);

            $headerCliente = [
                'id'         => $clienteId,
                'expandable' => true,
                'CLIENTES'   => [
                    'html'  => $clienteNombre,
                    'class' => 'font-bold bg-white'
                ],
                'DEUDA'      => [
                    'html'  => evaluar($consumoTotal),
                    'class' => 'text-end font-bold bg-white'
                ]
            ];

            // Recorrido por periodos.

            $colIdx = 0;
            $movimientosPorFecha = $this->getMovimientosPorFecha($clienteId, $fi, $ff);
            $movimientosIndexados = [];
            foreach ($movimientosPorFecha as $mov) {
                $movimientosIndexados[$mov['fecha']] = [
                    'consumo' => floatval($mov['consumo']),
                    'pago'    => floatval($mov['pago'])
                ];
            }

            $colIdx = 0;
            foreach ($dias as $fecha) {
                $bgColor = ($colIdx % 2 == 0) ? 'bg-orange-200' : 'bg-green-200';
                
                $consumoDia = $movimientosIndexados[$fecha]['consumo'] ?? 0;
                $pagoDia    = $movimientosIndexados[$fecha]['pago'] ?? 0;
                
                $headerCliente["CONSUMOS_$colIdx"] = [
                    'html'  => $consumoDia > 0 ? evaluar($consumoDia) : '-',
                    'class' => "text-center $bgColor"
                ];
                
                $headerCliente["PAGOS_$colIdx"] = [
                    'html'  => $pagoDia > 0 ? evaluar($pagoDia) : '-',
                    'class' => "text-center $bgColor"
                ];
                
                $totalConsumos[$colIdx] += $consumoDia;
                $totalPagos[$colIdx] += $pagoDia;
                
                $colIdx++;
            }

            $rows[] = $headerCliente;

           

            foreach ($subRows as $subRow) {
                $row = [
                    'id'       => $clienteId,
                    'subrow'   => true,
                    'CLIENTES' => [
                        'html'  => $subRow['label'],
                        'class' => 'pl-8 text-gray-700 bg-gray-50'
                    ],
                    'DEUDA'    => [
                        'html'  => evaluar(0),
                        'class' => 'text-end bg-gray-50'
                    ]
                ];

                $colIdx = 0;
                foreach ($diasName as $diaName) {

                    $bgColor = ($colIdx % 2 == 0) ? 'bg-orange-50' : 'bg-green-50';
                    $valueConsumo = '-';
                    $valuePago    = '-';
                    
                    if ($subRow['type'] === 'consumo') {
                        $valueConsumo = evaluar(0);
                        $totalConsumos[$colIdx] += 0;
                    } elseif ($subRow['type'] === 'pagos') {
                        $valuePago = evaluar(0);
                        $totalPagos[$colIdx] += 0;
                    }

                    $row["CONSUMOS_$colIdx"] = [
                        'html'  => $valueConsumo,
                        'class' => "text-center $bgColor"
                    ];
                    $row["PAGOS_$colIdx"] = [
                        'html'  => $valuePago,
                        'class' => "text-center $bgColor"
                    ];
                    $colIdx++;
                }

                $rows[] = $row;
            }
        }

        $totalRow = [
            'id'         => 'total_consumos',
            'expandable' => true,
            'CLIENTES'   => [
                'html'  => 'Total de consumos a crédito',
                'class' => 'font-bold bg-white'
            ],
            'DEUDA'      => [
                'html'  => evaluar(0),
                'class' => 'text-end font-bold bg-white'
            ]
        ];

        $colIdx = 0;
        foreach ($diasName as $diaName) {
            $bgColor = ($colIdx % 2 == 0) ? 'bg-orange-50' : 'bg-green-50';
            $totalRow["CONSUMOS_$colIdx"] = [
                'html'  => evaluar($totalConsumos[$colIdx]),
                'class' => "text-center font-bold $bgColor"
            ];
            $totalRow["PAGOS_$colIdx"] = [
                'html'  => '-',
                'class' => "text-center $bgColor"
            ];
            $colIdx++;
        }
        $rows[] = $totalRow;

        $totalPagosRow = [
            'id'         => 'total_pagos',
            'expandable' => true,
            'CLIENTES'   => [
                'html'  => 'Total de pagos y anticipos',
                'class' => 'font-bold bg-white'
            ],
            'DEUDA'      => [
                'html'  => evaluar(0),
                'class' => 'text-end font-bold bg-white'
            ]
        ];

        $colIdx = 0;
        foreach ($diasName as $diaName) {
            $bgColor = ($colIdx % 2 == 0) ? 'bg-orange-50' : 'bg-green-50';
            $totalPagosRow["CONSUMOS_$colIdx"] = [
                'html'  => '-',
                'class' => "text-center $bgColor"
            ];
            $totalPagosRow["PAGOS_$colIdx"] = [
                'html'  => evaluar($totalPagos[$colIdx]),
                'class' => "text-center font-bold $bgColor"
            ];
            $colIdx++;
        }
        $rows[] = $totalPagosRow;

        return [
            'thead'       => $thead,
            'row'         => $rows,
            'theadGroups' => $theadGroups,
            'clientes'    => $clientes,
            'params'      => ['fi' => $fi, 'ff' => $ff, 'udn' => $udn]
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

// Complements

function getDiasSemanaEspanol() {
    return [
        'Monday'    => 'LUNES',
        'Tuesday'   => 'MARTES',
        'Wednesday' => 'MIÉRCOLES',
        'Thursday'  => 'JUEVES',
        'Friday'    => 'VIERNES',
        'Saturday'  => 'SÁBADO',
        'Sunday'    => 'DOMINGO'
    ];
}

function getMesesEspanol() {
    return [
        'Jan' => 'ENE', 'Feb' => 'FEB', 'Mar' => 'MAR', 'Apr' => 'ABR',
        'May' => 'MAY', 'Jun' => 'JUN', 'Jul' => 'JUL', 'Aug' => 'AGO',
        'Sep' => 'SEP', 'Oct' => 'OCT', 'Nov' => 'NOV', 'Dec' => 'DIC'
    ];
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
