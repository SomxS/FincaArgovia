<?php

if (empty($_POST['opc'])) exit(0);

require_once '../mdl/mdl-proveedor.php';

class ctrl extends mdl {

    function init() {
        return [
            'suppliers'    => $this->lsSuppliers([1]),
            'paymentTypes' => $this->lsPaymentTypes([1]),
            'udn'          => $this->lsUDN(),
            'userLevel'    => $_SESSION['nivel'] ?? 1
        ];
    }

    function getTotales() {
        $fecha = $_POST['fecha'];
        $udn   = $_POST['udn'] ?? null;

        $totales = $this->getTotalesPorFecha([
            'fecha' => $fecha,
            'udn'   => $udn
        ]);

        return [
            'totalPagos'       => $totales['total_pagos'] ?? 0,
            'totalCorporativo' => $totales['total_corporativo'] ?? 0,
            'totalFondoFijo'   => $totales['total_fondo_fijo'] ?? 0
        ];
    }

    function ls() {
        $__row = [];
        $fecha = $_POST['fecha'] ?? date('Y-m-d');
        $udn   = $_POST['udn'] ?? null;
        $nivel = $_SESSION['nivel'] ?? 1;

        $ls = $this->listPayments([
            'fecha' => $fecha,
            'udn'   => $udn
        ]);

        $today = date('Y-m-d');

        foreach ($ls as $key) {
            $a = [];

            if ($nivel == 1 && $fecha == $today) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'proveedor.editPayment(' . $key['id'] . ')'
                ];

                $a[] = [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-trash"></i>',
                    'onclick' => 'proveedor.deletePayment(' . $key['id'] . ')'
                ];
            }

            $__row[] = [
                'id'          => $key['id'],
                'Proveedor'   => $key['supplier_name'],
                'Tipo de pago' => renderPaymentType($key['payment_type_name']),
                'Monto'       => [
                    'html'  => evaluar($key['amount']),
                    'class' => 'text-end'
                ],
                'Descripción' => $key['description'] ?? '-',
                'a'           => $a
            ];
        }

        return [
            'row' => $__row,
            'ls'  => $ls
        ];
    }

    function getPayment() {
        $id      = $_POST['id'];
        $status  = 404;
        $message = 'Pago no encontrado';
        $data    = null;

        $payment = $this->getPaymentById($id);

        if ($payment) {
            $status  = 200;
            $message = 'Pago encontrado';
            $data    = $payment;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function addPayment() {
        $status  = 500;
        $message = 'Error al registrar pago';
        $nivel   = $_SESSION['nivel'] ?? 1;

        if ($nivel != 1) {
            return [
                'status'  => 403,
                'message' => 'No tiene permisos para realizar esta acción'
            ];
        }

        $_POST['payment_date'] = $_POST['fecha'] ?? date('Y-m-d');
        $_POST['user_id']      = $_SESSION['idUsuario'] ?? 1;
        $_POST['active']       = 1;

        $values = $this->util->sql($_POST);
        $create = $this->createPayment($values);

        if ($create) {
            $status  = 200;
            $message = 'Pago registrado correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function editPayment() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al editar pago';
        $nivel   = $_SESSION['nivel'] ?? 1;

        if ($nivel != 1) {
            return [
                'status'  => 403,
                'message' => 'No tiene permisos para realizar esta acción'
            ];
        }

        $payment = $this->getPaymentById($id);
        $today   = date('Y-m-d');

        if ($payment['payment_date'] !== $today) {
            return [
                'status'  => 400,
                'message' => 'Solo puede editar pagos del día actual'
            ];
        }

        $edit = $this->updatePayment($this->util->sql($_POST, 1));

        if ($edit) {
            $status  = 200;
            $message = 'Pago editado correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function deletePayment() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al eliminar pago';
        $nivel   = $_SESSION['nivel'] ?? 1;

        if ($nivel != 1) {
            return [
                'status'  => 403,
                'message' => 'No tiene permisos para realizar esta acción'
            ];
        }

        $payment = $this->getPaymentById($id);
        $today   = date('Y-m-d');

        if ($payment['payment_date'] !== $today) {
            return [
                'status'  => 400,
                'message' => 'Solo puede eliminar pagos del día actual'
            ];
        }

        $values = $this->util->sql([
            'active' => 0,
            'id'     => $id
        ], 1);

        $delete = $this->deletePaymentById($values);

        if ($delete) {
            $status  = 200;
            $message = 'Pago eliminado correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function lsBalance() {
        $fi  = $_POST['fi'];
        $ff  = $_POST['ff'];
        $udn = $_POST['udn'] ?? null;

        $balance = $this->getSupplierBalance([
            'fi'  => $fi,
            'ff'  => $ff,
            'udn' => $udn
        ]);

        $rows = [];

        foreach ($balance as $supplier) {
            $headerRow = [
                'id'               => $supplier['supplier_id'],
                'key'              => '',
                'Proveedor'        => [
                    'html'  => $supplier['supplier_name'],
                    'class' => 'font-bold bg-gray-100'
                ],
                'Saldo inicial'    => [
                    'html'  => evaluar($supplier['initial_balance']),
                    'class' => 'text-end bg-gray-100'
                ],
                'Compras a crédito' => [
                    'html'  => evaluar($supplier['credit_purchases']),
                    'class' => 'text-end bg-green-100 text-green-800'
                ],
                'Pagos de crédito' => [
                    'html'  => evaluar($supplier['credit_payments']),
                    'class' => 'text-end bg-red-100 text-red-800'
                ],
                'Saldo final'      => [
                    'html'  => evaluar($supplier['final_balance']),
                    'class' => 'text-end font-bold bg-gray-100'
                ],
                'opc'              => 1
            ];

            $rows[] = $headerRow;

            foreach ($supplier['details'] as $detail) {
                $detailRow = [
                    'id'               => $supplier['supplier_id'],
                    'key'              => $detail['date'],
                    'Proveedor'        => $detail['date'],
                    'Saldo inicial'    => '-',
                    'Compras a crédito' => $detail['type'] == 'purchase' ? evaluar($detail['amount']) : '-',
                    'Pagos de crédito' => $detail['type'] == 'payment' ? evaluar($detail['amount']) : '-',
                    'Saldo final'      => '-',
                    'opc'              => 0
                ];

                $rows[] = $detailRow;
            }
        }

        return [
            'thead' => ['Proveedor', 'Saldo inicial', 'Compras a crédito', 'Pagos de crédito', 'Saldo final'],
            'row'   => $rows
        ];
    }

    function exportExcel() {
        $fi  = $_POST['fi'];
        $ff  = $_POST['ff'];
        $udn = $_POST['udn'] ?? null;

        $balance = $this->getSupplierBalance([
            'fi'  => $fi,
            'ff'  => $ff,
            'udn' => $udn
        ]);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="balance_proveedores_' . date('Y-m-d') . '.xls"');
        header('Cache-Control: max-age=0');

        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
        echo '<x:Name>Balance Proveedores</x:Name>';
        echo '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>';
        echo '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        echo '</head>';
        echo '<body>';

        echo '<table border="1">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Proveedor</th>';
        echo '<th>Saldo Inicial</th>';
        echo '<th>Compras a Crédito</th>';
        echo '<th>Pagos de Crédito</th>';
        echo '<th>Saldo Final</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($balance as $supplier) {
            echo '<tr>';
            echo '<td><strong>' . htmlspecialchars($supplier['supplier_name']) . '</strong></td>';
            echo '<td style="text-align:right">' . number_format($supplier['initial_balance'], 2) . '</td>';
            echo '<td style="text-align:right;background-color:#d1fae5">' . number_format($supplier['credit_purchases'], 2) . '</td>';
            echo '<td style="text-align:right;background-color:#fee2e2">' . number_format($supplier['credit_payments'], 2) . '</td>';
            echo '<td style="text-align:right"><strong>' . number_format($supplier['final_balance'], 2) . '</strong></td>';
            echo '</tr>';

            foreach ($supplier['details'] as $detail) {
                echo '<tr>';
                echo '<td style="padding-left:20px">' . $detail['date'] . '</td>';
                echo '<td>-</td>';
                echo '<td style="text-align:right">' . ($detail['type'] == 'purchase' ? number_format($detail['amount'], 2) : '-') . '</td>';
                echo '<td style="text-align:right">' . ($detail['type'] == 'payment' ? number_format($detail['amount'], 2) : '-') . '</td>';
                echo '<td>-</td>';
                echo '</tr>';
            }
        }

        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';

        exit;
    }
}

function renderPaymentType($paymentType) {
    switch ($paymentType) {
        case 'Corporativo':
            return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                <i class="icon-briefcase text-blue-600"></i> Corporativo
            </span>';
        case 'Fondo fijo':
            return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                <i class="icon-money text-green-600"></i> Fondo fijo
            </span>';
        default:
            return '<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                <i class="icon-help text-gray-600"></i> Desconocido
            </span>';
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
