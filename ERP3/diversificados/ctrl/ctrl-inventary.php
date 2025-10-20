<?php
if (empty($_POST['opc'])) exit(0);


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

setlocale(LC_TIME, 'es_ES.UTF-8');
date_default_timezone_set('America/Mexico_City');
// utils
require_once '../../conf/_Utileria.php';
require_once '../../conf/coffeSoft.php';
// incluir tu modelo
require_once '../mdl/mdl-inventary.php';

$encode = [];


// sustituir 'mdl' extends de acuerdo al nombre que tiene el modelo
class ctrl extends Inventary{


    public function ls(){

        # Declarar variables
        $__row = [];

        $subs = $this -> listSubCategoria();

        foreach ($subs as $sub) {

            $__row[] = [
                
                'id'           => $sub['id'],
                'Grupo'        => '<i class="ico'.$sub['id'].' icon-right-dir-1"></i>'.$sub['Nombre_subcategoria'],
                'presentacion' => '',
                // 'Entrada'      => '',
                // 'Salida'       => '',
                // 'Precio menudeo' => '',

                // 'cant min Mayoreo' => '',
                // 'Precio Mayoreo'   => '',
                // 'Fecha'            => '',
                // 'Inv. Inicial'     => '',
                // 'Inv. Minimo'      => '',

                'Existente'        => '',
                'Estatus'          => '',
                'Contenido'        => '',
                'Total'            => '',
                'opc'              => 1

            ];


            $inventary = $this -> lsInventary([$sub['id']]);

            foreach ($inventary as $product) {

                // Inv. Inicial
                $almacen = $this -> getAlmacenByID([$product['id']]);
                $entrada = $this -> getEntradaByIDx([$_POST['fi'],$product['id']]);
                $salida  = $this -> getSalidaByIDx([$_POST['fi'],$product['id']]);

                $actual  = $entrada[0]['total'] - $salida[0]['total'];
                $costo   = $actual * $product['precio'];
                $total   = $total + $costo;

                $__row[] = [

                    'id' => $sub['id'],
                      // 'clave'               => $product['id'],
                    'Producto'     => $product['NombreProducto'],
                    'presentacion' => $product['presentacion'],
                    // 'Entrada'      => $entrada[0]['total'],
                    // 'Salida'       => $salida[0]['total'],
                    // 'Precio menudeo'   => evaluar($product['precio']),
                    // 'cant min Mayoreo' => $product['min_mayoreo'],
                    // 'Precio Mayoreo'   => evaluar($product['precio_mayoreo']),

                    // 'Fecha'            => $almacen['Fecha_Inventario'],
                    // 'Inv. Inicial'     => $almacen['Inventario_total'],
                    // 'Inv. Minimo'      => $entrada[0]['total'].'-'.$salida[0]['total'],

                    'Existente'        => $actual,
                    'Estatus'          => inventarioStatus(10,5),
                    'Contenido'        => $product['title'],

                    'Total'            => evaluar($costo),
                    'opc'              => 0

                ];

            }
           
        }



        # encapsular datos
        return [
            "row"   => $__row,
            'frm_head'  => '<h1 class="text-lg font-bold">Reporte de inventarios al dia '.$_POST['fi'].' </h1>'
        ];

    }



} 

// aux functions
function inventarioStatus($min_inventario, $actual) {
    if ($actual > $min_inventario) {
        return '<span class="bgx-green-100 w-32 text-green-800 text-xs font-semibold px-3 py-1 my-1 rounded"><i class="icon-ok-circled-1"></i> Disponible</span>';
    } elseif ($actual <= $min_inventario) {
        return '<span class="bgx-red-100 w-32 text-red-800 text-xs font-semibold  px-3 py-1 my-1 rounded"><i class="icon-attention-3"></i> PEDIR</span>';
    }
}

// Instancia del objeto

$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);