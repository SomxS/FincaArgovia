<?php

if (empty($_POST['opc'])) exit(0);

require_once '../mdl/mdl-compras.php';

class ctrl extends mdl {

    function init() {
        return [
            'productClass'  => $this->lsProductClass([1]),
            'product'       => $this->lsProduct([1]),
            'purchaseType'  => $this->lsPurchaseType([1]),
            'supplier'      => $this->lsSupplier([1]),
            'methodPay'     => $this->lsMethodPay([1]),
            'udn'           => $this->lsUDN(),
            'userLevel'     => 4
        ];
    }

    function getTotales() {
        $fecha   = $_POST['fecha'];
        $totales = $this->getTotalesPorFecha($fecha);

        return [
            'totalCompras'     => $totales['total_compras'] ?? 0,
            'totalFondoFijo'   => $totales['total_fondo_fijo'] ?? 0,
            'totalCorporativo' => $totales['total_corporativo'] ?? 0,
            'totalCredito'     => $totales['total_credito'] ?? 0
        ];
    }

    function ls() {
        $__row = [];
        $fecha = $_POST['fecha'] ?? date('Y-m-d');
        $tipo  = $_POST['tipoCompra'] ?? 'todos';

        $ls = $this->listCompras([
            'fecha' => $fecha,
            'tipo'  => $tipo
        ]);

        foreach ($ls as $key) {
            $a = [];

            $a[] = [
                'class'   => 'btn btn-sm btn-info me-1',
                'html'    => '<i class="icon-eye"></i>',
                'onclick' => 'compras.viewDetalle(' . $key['id'] . ')'
            ];

            $a[] = [
                'class'   => 'btn btn-sm btn-primary me-1',
                'html'    => '<i class="icon-pencil"></i>',
                'onclick' => 'compras.editCompra(' . $key['id'] . ')'
            ];

            $a[] = [
                'class'   => 'btn btn-sm btn-danger',
                'html'    => '<i class="icon-trash"></i>',
                'onclick' => 'compras.deleteCompra(' . $key['id'] . ')'
            ];

            $__row[] = [
                'id'                   => $key['id'],
                'Categoría'            => $key['product_class_name'],
                'Producto'             => $key['product_name'],
                'Tipo de compra'       => renderPurchaseType($key['purchase_type_name']),
                'Método de pago'       => $key['method_pay_name'] ?? 'N/A',
                'Total'                => [
                    'html'  => evaluar($key['total']),
                    'class' => 'text-end'
                ],
                'a' => $a
            ];
        }

        return [
            'row' => $__row,
            'ls'  => $ls
        ];
    }

    function getCompra() {
        $id      = $_POST['id'];
        $status  = 404;
        $message = 'Compra no encontrada';
        $data    = null;

        $compra = $this->getCompraById($id);

        if ($compra) {
            $status  = 200;
            $message = 'Compra encontrada';
            $data    = $compra;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function getProductsByClass() {
        $classId = $_POST['product_class_id'];
        $products = $this->lsProductByClass([$classId]);

        return [
            'products' => $products
        ];
    }

    function addCompra() {
        $status  = 500;
        $message = 'Error al registrar compra';

        $_POST['operation_date'] = $_POST['fecha'] ?? date('Y-m-d');
        $_POST['udn_id']         = $_POST['udn'] ?? 4;
        $_POST['active']         = 1;

        $create = $this->createCompra($this->util->sql($_POST));

        if ($create) {
            $status  = 200;
            $message = 'Compra registrada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function editCompra() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al editar compra';

        $edit = $this->updateCompra($this->util->sql($_POST, 1));

        if ($edit) {
            $status  = 200;
            $message = 'Compra editada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function deleteCompra() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al eliminar compra';

        $values = $this->util->sql([
            'active' => 0,
            'id'     => $id
        ], 1);

        $delete = $this->deleteCompraById($values);

        if ($delete) {
            $status  = 200;
            $message = 'Compra eliminada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function getTotalesConcentrado() {
        $fi  = $_POST['fi'];
        $ff  = $_POST['ff'];
        $udn = $_POST['udn'] ?? 4;

        $totales = $this->getTotalesConcentradoPeriodo([
            'fi'  => $fi,
            'ff'  => $ff,
            'udn' => $udn
        ]);

        return [
            'saldoInicial'    => $totales['saldo_inicial'] ?? 15000,
            'totalCompras'    => $totales['total_compras'] ?? 0,
            'salidasFondoFijo' => $totales['salidas_fondo_fijo'] ?? 0,
            'saldoFinal'      => ($totales['saldo_inicial'] ?? 15000) - ($totales['salidas_fondo_fijo'] ?? 0)
        ];
    }

    function lsConcentrado() {
        $fi   = $_POST['fi'];
        $ff   = $_POST['ff'];
        $udn  = $_POST['udn'] ?? 4;

        $start = new DateTime($fi);
        $end   = new DateTime($ff);
        $end->modify('+1 day');

        $interval = new DateInterval('P1D');
        $period   = new DatePeriod($start, $interval, $end);

        $dias     = [];
        $diasName = [];
        $rows     = [];

        $diasSemana = getDiasSemanaEspanol();
        $meses      = getMesesEspanol();

        $thead = ['CLASE PRODUCTO'];

        foreach ($period as $date) {
            $fecha       = $date->format('Y-m-d');
            $dia         = $date->format('d');
            $mesEn       = $date->format('M');
            $mesEs       = $meses[$mesEn];
            $diaSemanaEn = $date->format('l');
            $diaSemanaEs = $diasSemana[$diaSemanaEn];

            $dias[]     = $fecha;
            $diasName[] = "$diaSemanaEs, $dia DE $mesEs";
            $thead[]    = "$diaSemanaEs, $dia";
        }

        $thead[] = 'TOTAL';

        $totalPorDia = array_fill(0, count($dias), 0);
        $clases      = $this->listProductClass([$fi, $ff, $udn]);

        foreach ($clases as $clase) {
            $claseId     = $clase['id'];
            $claseNombre = $clase['name'];

            $row = [
                'id'              => $claseId,
                'CLASE PRODUCTO'  => [
                    'html'  => $claseNombre,
                    'class' => 'font-bold bg-white'
                ]
            ];

            $totalClase = 0;
            $colIdx     = 0;

            foreach ($dias as $fecha) {
                $comprasDia = $this->getComprasPorClaseYFecha($claseId, $fecha);
                $total      = floatval($comprasDia['total'] ?? 0);

                $bgColor = ($colIdx % 2 == 0) ? 'bg-blue-50' : 'bg-green-50';

                $row["DIA_$colIdx"] = [
                    'html'  => $total > 0 ? evaluar($total) : '-',
                    'class' => "text-center $bgColor"
                ];

                $totalPorDia[$colIdx] += $total;
                $totalClase += $total;
                $colIdx++;
            }

            $row['TOTAL'] = [
                'html'  => evaluar($totalClase),
                'class' => 'text-end font-bold bg-gray-100'
            ];

            $rows[] = $row;
        }

        $totalRow = [
            'id'             => 'total',
            'CLASE PRODUCTO' => [
                'html'  => 'TOTAL',
                'class' => 'font-bold bg-white'
            ]
        ];

        $totalGeneral = 0;
        $colIdx       = 0;

        foreach ($diasName as $diaName) {
            $bgColor = ($colIdx % 2 == 0) ? 'bg-blue-50' : 'bg-green-50';
            $totalRow["DIA_$colIdx"] = [
                'html'  => evaluar($totalPorDia[$colIdx]),
                'class' => "text-center font-bold $bgColor"
            ];
            $totalGeneral += $totalPorDia[$colIdx];
            $colIdx++;
        }

        $totalRow['TOTAL'] = [
            'html'  => evaluar($totalGeneral),
            'class' => 'text-end font-bold bg-gray-100'
        ];

        $rows[] = $totalRow;

        return [
            'thead' => $thead,
            'row'   => $rows
        ];
    }
}

function renderPurchaseType($purchaseType) {
    switch ($purchaseType) {
        case 'Fondo fijo':
            return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <i class="icon-money text-green-600"></i> Fondo fijo
            </span>';
        case 'Corporativo':
            return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                <i class="icon-briefcase text-blue-600"></i> Corporativo
            </span>';
        case 'Crédito':
            return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                <i class="icon-credit-card text-orange-600"></i> Crédito
            </span>';
        default:
            return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                <i class="icon-help text-gray-600"></i> Desconocido
            </span>';
    }
}

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
