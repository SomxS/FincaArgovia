<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-costos.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn' => $this->lsUDN(),
            'userLevel' => $_SESSION['nivel'] ?? 2
        ];
    }

    function lsCostos() {
        $status = 500;
        $message = 'Error al consultar costos';
        $row = [];

        try {
            $fi = $_POST['fi'] ?? date('Y-m-01');
            $ff = $_POST['ff'] ?? date('Y-m-d');
            $udn = $_POST['udn'] ?? null;

            if (strtotime($fi) > strtotime($ff)) {
                return [
                    'status' => 400,
                    'message' => 'Rango de fechas inválido. La fecha inicial debe ser menor o igual a la fecha final.'
                ];
            }

            $costosDirectos = $this->listCostosDirectos(['fi' => $fi, 'ff' => $ff, 'udn' => $udn]);
            $salidas = $this->listSalidasAlmacen(['fi' => $fi, 'ff' => $ff, 'udn' => $udn]);

            $concentrado = $this->consolidateCostos($costosDirectos, $salidas, $fi, $ff);

            $this->createAuditLog([
                'action' => 'consulta',
                'details' => json_encode(['fi' => $fi, 'ff' => $ff, 'udn' => $udn])
            ]);

            $status = 200;
            $message = 'Consulta exitosa';
            $row = $concentrado;

        } catch (Exception $e) {
            $this->createAuditLog([
                'action' => 'error',
                'details' => json_encode([
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ])
            ]);

            $message = 'Error al consultar costos: ' . $e->getMessage();
        }

        return [
            'status' => $status,
            'message' => $message,
            'row' => $row,
            'thead' => ''
        ];
    }

    function exportExcel() {
        $status = 500;
        $message = 'Error al generar archivo Excel';

        try {
            $fi = $_POST['fi'] ?? date('Y-m-01');
            $ff = $_POST['ff'] ?? date('Y-m-d');
            $udn = $_POST['udn'] ?? null;

            $data = $this->lsCostos();
            
            if ($data['status'] !== 200) {
                return $data;
            }

            $udnText = $udn ? $this->getUDNName($udn) : 'Todas';
            $filename = "Costos_{$fi}_{$ff}_{$udnText}.xlsx";

            $this->createAuditLog([
                'action' => 'exportacion',
                'details' => json_encode([
                    'filename' => $filename,
                    'fi' => $fi,
                    'ff' => $ff,
                    'udn' => $udnText
                ])
            ]);

            return [
                'status' => 200,
                'file' => $filename,
                'message' => 'Archivo generado exitosamente',
                'data' => $data['row']
            ];

        } catch (Exception $e) {
            $this->createAuditLog([
                'action' => 'error',
                'details' => json_encode([
                    'operation' => 'exportExcel',
                    'error' => $e->getMessage()
                ])
            ]);

            return [
                'status' => 500,
                'message' => 'Error al generar archivo Excel: ' . $e->getMessage()
            ];
        }
    }

    function validateReadOnly() {
        $allowedOperations = ['init', 'lsCostos', 'exportExcel'];
        
        if (!in_array($_POST['opc'], $allowedOperations)) {
            $this->createAuditLog([
                'action' => 'error',
                'details' => json_encode([
                    'attempted_operation' => $_POST['opc'],
                    'error' => 'Intento de modificación en módulo de solo lectura'
                ])
            ]);
            
            return [
                'status' => 403,
                'message' => 'Operación no permitida en módulo de solo lectura'
            ];
        }
    }

    function getUDNName($udnId) {
        $udns = $this->lsUDN();
        foreach ($udns as $udn) {
            if ($udn['id'] == $udnId) {
                return $udn['valor'];
            }
        }
        return 'Desconocida';
    }
}

function formatCurrency($amount) {
    return '$' . number_format(floatval($amount), 2);
}

function generateDayColumns($fi, $ff) {
    $begin = new DateTime($fi);
    $end = new DateTime($ff);
    $end->modify('+1 day');
    $interval = DateInterval::createFromDateString('1 day');
    $period = new DatePeriod($begin, $interval, $end);

    $columns = [];
    $dayNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    
    foreach ($period as $dt) {
        $columns[] = [
            'key' => strtoupper($dayNames[$dt->format('w')]) . '_' . $dt->format('d'),
            'display' => $dayNames[$dt->format('w')] . ' ' . $dt->format('d')
        ];
    }

    return $columns;
}

function formatDateHeader($date) {
    $dayNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $dt = new DateTime($date);
    return $dayNames[$dt->format('w')] . ' ' . $dt->format('d');
}

$obj = new ctrl();

$allowedOps = ['init', 'lsCostos', 'exportExcel'];
if (!in_array($_POST['opc'], $allowedOps)) {
    echo json_encode([
        'status' => 403,
        'message' => 'Operación no permitida en módulo de solo lectura'
    ]);
    exit();
}

echo json_encode($obj->{$_POST['opc']}());
