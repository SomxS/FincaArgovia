<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-movimientos.php';

class ctrl extends mdl {

    function init() {
        return [
            'categorias' => $this->lsCategorias(),
            'meses'      => $this->lsMeses(),
            'anios'      => $this->lsAnios()
        ];
    }

    function lsMovimientos() {
        $mes       = $this->util->sql(['mes' => $_POST['mes'] ?? date('n')])['data'][0];
        $anio      = $this->util->sql(['anio' => $_POST['anio'] ?? date('Y')])['data'][0];
        $categoria = $this->util->sql(['categoria' => $_POST['categoria'] ?? 'Todos'])['data'][0];

        if (!is_numeric($mes) || $mes < 1 || $mes > 12) {
            return [
                'status' => 400,
                'message' => 'Mes inválido',
                'row' => [],
                'resumen' => ['total' => 0, 'entradas' => 0, 'salidas' => 0, 'balance' => 0]
            ];
        }

        if (!is_numeric($anio) || $anio < 2000 || $anio > 2100) {
            return [
                'status' => 400,
                'message' => 'Año inválido',
                'row' => [],
                'resumen' => ['total' => 0, 'entradas' => 0, 'salidas' => 0, 'balance' => 0]
            ];
        }

        $ls   = $this->listMovimientos([$mes, $anio, $categoria]);
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
                    'nombre_categoria' => $item['nombre_grupo'],
                    'responsable'      => $item['responsable'] ?? 'N/A',
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
                'id'         => $mov['id_movimiento'],
                'Folio'      => $mov['folio'],
                'Fecha'      => $mov['fecha'],
                'Tipo'       => renderTipoMovimiento($mov['tipo_movimiento']),
                'Producto'   => $productos,
                'Cantidad'   => renderCantidad($cantidad, $mov['tipo_movimiento']),
                'Categoría'  => $mov['nombre_categoria'],
                'Responsable' => $mov['responsable'],
                'opc'        => 0
            ];
        }

        // $resumen = $this->getResumen([$mes, $anio, $categoria]);

        return [
            'row'     => $rows,
            // 'resumen' => $resumen
        ];
    }

    function getResumen() {
        $data = $this->getResumenMovimientos([ $_POST['mes'],  $_POST['anio'],  $_POST['categoria']]);
        
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
            $data,
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
