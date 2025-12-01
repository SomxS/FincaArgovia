<?php
require_once '../../conf/_CRUD.php';
require_once '../../conf/_Utileria.php';
session_start();

class mdl extends CRUD {
    protected $util;
    public $bd;

    public function __construct() {
        $this->util = new Utileria;
        $this->bd = "rfwsmqex_contabilidad.";
    }

    function lsUDN() {
        $query = "
            SELECT DISTINCT idUDN as id, nombre_udn as valor
            FROM {$this->bd}usuarios
            WHERE active = 1
            ORDER BY nombre_udn ASC
        ";
        return $this->_Read($query, []);
    }

    function listCostosDirectos($params) {
        $fi = $params['fi'];
        $ff = $params['ff'];
        $udn = $params['udn'];

        $whereUDN = $udn ? "AND c.udn_id = ?" : "";
        $data = $udn ? [$fi, $ff, $udn] : [$fi, $ff];

        $query = "
            SELECT 
                c.operation_date as fecha,
                pc.name as categoria,
                SUM(c.amount) as total
            FROM {$this->bd}compras c
            LEFT JOIN {$this->bd}product_class pc ON c.category_id = pc.id
            WHERE c.operation_date BETWEEN ? AND ?
            $whereUDN
            AND c.active = 1
            GROUP BY c.operation_date, pc.name
            ORDER BY c.operation_date ASC, pc.name ASC
        ";

        return $this->_Read($query, $data);
    }

    function listSalidasAlmacen($params) {
        $fi = $params['fi'];
        $ff = $params['ff'];
        $udn = $params['udn'];

        $whereUDN = $udn ? "AND wo.udn_id = ?" : "";
        $data = $udn ? [$fi, $ff, $udn] : [$fi, $ff];

        $query = "
            SELECT 
                wo.operation_date as fecha,
                pc.name as categoria,
                SUM(wo.amount) as total
            FROM {$this->bd}warehouse_output wo
            INNER JOIN {$this->bd}product p ON wo.product_id = p.id
            INNER JOIN {$this->bd}product_class pc ON p.product_class_id = pc.id
            WHERE wo.operation_date BETWEEN ? AND ?
            $whereUDN
            AND wo.active = 1
            GROUP BY wo.operation_date, pc.name
            ORDER BY wo.operation_date ASC, pc.name ASC
        ";

        return $this->_Read($query, $data);
    }

    function consolidateCostos($costosDirectos, $salidas, $fi, $ff) {
        $rows = [];
        $categorias = ['Alimentos', 'Bebidas', 'Diversos'];
        
        $begin = new DateTime($fi);
        $end = new DateTime($ff);
        $end->modify('+1 day');
        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);

        $dates = [];
        $dayNames = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        
        foreach ($period as $dt) {
            $dates[] = [
                'date' => $dt->format('Y-m-d'),
                'key' => strtoupper($dayNames[$dt->format('w')]) . '_' . $dt->format('d'),
                'display' => $dayNames[$dt->format('w')] . ' ' . $dt->format('d')
            ];
        }

        $totalesPorDia = [];
        foreach ($dates as $dateInfo) {
            $totalesPorDia[$dateInfo['key']] = [
                'compras' => 0,
                'salidas' => 0,
                'total' => 0
            ];
        }

        foreach ($categorias as $cat) {
            $row = [
                'id' => strtolower($cat),
                'COSTOS' => $cat,
                'TOTAL' => 0
            ];

            foreach ($dates as $dateInfo) {
                $totalDia = 0;

                foreach ($costosDirectos as $costo) {
                    if ($costo['fecha'] == $dateInfo['date'] && $costo['categoria'] == $cat) {
                        $totalDia += floatval($costo['total']);
                        $totalesPorDia[$dateInfo['key']]['compras'] += floatval($costo['total']);
                    }
                }

                foreach ($salidas as $salida) {
                    if ($salida['fecha'] == $dateInfo['date'] && $salida['categoria'] == $cat) {
                        $totalDia += floatval($salida['total']);
                        $totalesPorDia[$dateInfo['key']]['salidas'] += floatval($salida['total']);
                    }
                }

                $totalesPorDia[$dateInfo['key']]['total'] += $totalDia;
                $row[$dateInfo['key']] = $totalDia > 0 ? '$' . number_format($totalDia, 2) : '-';
                $row['TOTAL'] += $totalDia;
            }

            $row['TOTAL'] = '$' . number_format($row['TOTAL'], 2);
            $row['opc'] = 0;
            $rows[] = $row;
        }

        $totalCompras = 0;
        $totalSalidas = 0;

        foreach ($costosDirectos as $costo) {
            $totalCompras += floatval($costo['total']);
        }

        foreach ($salidas as $salida) {
            $totalSalidas += floatval($salida['total']);
        }

        $totalGeneral = $totalCompras + $totalSalidas;

        $rowTotalCompras = [
            'id' => 'total-compras',
            'COSTOS' => 'Total en compras (costo directo)',
            'TOTAL' => '$' . number_format($totalCompras, 2),
            'class' => 'font-bold bg-yellow-100',
            'opc' => 0
        ];

        $rowTotalSalidas = [
            'id' => 'total-salidas',
            'COSTOS' => 'Total en salidas de almacén',
            'TOTAL' => '$' . number_format($totalSalidas, 2),
            'class' => 'font-bold bg-blue-100',
            'opc' => 0
        ];

        $rowTotalGeneral = [
            'id' => 'total-general',
            'COSTOS' => 'Costo total',
            'TOTAL' => '$' . number_format($totalGeneral, 2),
            'class' => 'font-bold bg-green-100',
            'opc' => 0
        ];

        foreach ($dates as $dateInfo) {
            $rowTotalCompras[$dateInfo['key']] = $totalesPorDia[$dateInfo['key']]['compras'] > 0 
                ? '$' . number_format($totalesPorDia[$dateInfo['key']]['compras'], 2) 
                : '-';
            
            $rowTotalSalidas[$dateInfo['key']] = $totalesPorDia[$dateInfo['key']]['salidas'] > 0 
                ? '$' . number_format($totalesPorDia[$dateInfo['key']]['salidas'], 2) 
                : '-';
            
            $rowTotalGeneral[$dateInfo['key']] = $totalesPorDia[$dateInfo['key']]['total'] > 0 
                ? '$' . number_format($totalesPorDia[$dateInfo['key']]['total'], 2) 
                : '-';
        }

        $rows[] = $rowTotalCompras;
        $rows[] = $rowTotalSalidas;
        $rows[] = $rowTotalGeneral;

        return $rows;
    }

    function createAuditLog($data) {
        $action = $data['action'];
        $details = $data['details'];
        
        $userId = $_SESSION['idUser'] ?? 0;
        $udnId = $_SESSION['idUDN'] ?? 0;
        $userName = $_SESSION['nombre'] ?? 'Unknown';
        $udnName = $_SESSION['nombre_udn'] ?? 'Unknown';

        return $this->_Insert([
            'table' => "{$this->bd}audit_log",
            'values' => 'udn_id, user_id, name_table, name_user, name_udn, action, change_items, creation_date',
            'data' => [
                $udnId,
                $userId,
                'costos',
                $userName,
                $udnName,
                $action,
                $details,
                date('Y-m-d H:i:s')
            ]
        ]);
    }
}
