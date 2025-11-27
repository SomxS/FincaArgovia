<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-movimientos.php';

class ctrl extends mdl {

    function init() {
        return [
            'almacenes' => $this->lsAlmacenes(),
            'meses'     => $this->lsMeses(),
            'anios'     => $this->lsAnios()
        ];
    }

    function lsMovimientos() {
        $mes     = $_POST['mes'] ?? date('n');
        $anio    = $_POST['anio'] ?? date('Y');
        $almacen = $_POST['almacen'] ?? 'Todos';

        $ls   = $this->listMovimientos([$mes, $anio, $almacen]);
        $rows = [];

        $movimientosAgrupados = [];
        foreach ($ls as $item) {
            $idMovimiento = $item['id_movimiento'];
            
            if (!isset($movimientosAgrupados[$idMovimiento])) {
                $movimientosAgrupados[$idMovimiento] = [
                    'id_movimiento'    => $item['id_movimiento'],
                    'folio'            => $item['folio'],
                    'fecha'            => $item['fecha'],
                    'tipo_movimiento'  => $item['tipo_movimiento'],
                    'nombre_almacen'   => $item['nombre_almacen'],
                    'productos'        => [],
                    'total_cantidad'   => 0
                ];
            }
            
            $movimientosAgrupados[$idMovimiento]['productos'][] = $item['nombre_producto'];
            $movimientosAgrupados[$idMovimiento]['total_cantidad'] += $item['cantidad'];
        }

        foreach ($movimientosAgrupados as $mov) {
            $productos = implode(', ', array_unique($mov['productos']));
            $cantidad  = $mov['total_cantidad'];
            
            if ($mov['tipo_movimiento'] == 'Salida') {
                $cantidad = -$cantidad;
            }

            $rows[] = [
                'id'       => $mov['id_movimiento'],
                'Folio'    => $mov['folio'],
                'Fecha'    => $mov['fecha'],
                'Tipo'     => renderTipoMovimiento($mov['tipo_movimiento']),
                'Producto' => $productos,
                'Cantidad' => renderCantidad($cantidad, $mov['tipo_movimiento']),
                'Almacén'  => $mov['nombre_almacen'] ?? 'N/A',
                'opc'      => 0
            ];
        }

        $resumen = $this->getResumen([$mes, $anio, $almacen]);

        return [
            'row'     => $rows,
            'resumen' => $resumen
        ];
    }

    function getResumen($params) {
        $data = $this->getResumenMovimientos($params);
        
        $totalMovimientos = 0;
        $entradas         = 0;
        $salidas          = 0;

        foreach ($data as $item) {
            $totalMovimientos += $item['total_movimientos'];
            
            if ($item['tipo_movimiento'] == 'Entrada') {
                $entradas = $item['total_unidades'];
            } else if ($item['tipo_movimiento'] == 'Salida') {
                $salidas = $item['total_unidades'];
            }
        }

        $balance = $entradas - $salidas;

        return [
            'total'    => $totalMovimientos,
            'entradas' => $entradas,
            'salidas'  => $salidas,
            'balance'  => $balance
        ];
    }
}

// Complements

function renderTipoMovimiento($tipo) {
    switch ($tipo) {
        case 'Entrada':
            return '<span class="inline-block px-3 py-1 rounded-2xl text-sm font-semibold bg-green-100 text-green-700 min-w-[100px] text-center">↑ Entrada</span>';
        case 'Salida':
            return '<span class="inline-block px-3 py-1 rounded-2xl text-sm font-semibold bg-red-100 text-red-700 min-w-[100px] text-center">↓ Salida</span>';
        default:
            return $tipo;
    }
}

function renderCantidad($cantidad, $tipo) {
    $color = ($tipo == 'Entrada') ? 'text-green-600' : 'text-red-600';
    $signo = ($cantidad >= 0) ? '+' : '';
    
    return [
        'html'  => '<span class="' . $color . ' font-bold">' . $signo . $cantidad . '</span>',
        'class' => 'text-center'
    ];
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
