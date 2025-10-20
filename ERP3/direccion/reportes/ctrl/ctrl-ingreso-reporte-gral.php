<?php
if (empty($_POST['opc'])) {
  exit(0);
}

require_once '../mdl/mdl-ingreso-diarios.php';
$obj = new Ingresodiarios();

# -- variables --
$encode = [];

# -- opciones  --
switch ($_POST['opc']) {
  case 'report-gral':
    $th     = ['INGRESOS TURISMO','SUBTOTAL','IVA','2 % HOSP','TOTAL'];
    $encode = reporte_gral($obj, $th);
  break;
}

function reporte_gral($obj,$th){
 # Declarar variables
 $__row   = [];
 $fi      = '2023-9-01';
 $ff      = '2023-9-17';
 
    
  /*------- INGRESOS GENERALES ---------*/
$categorias        = $obj -> VER_CATEGORIAS([1]);
  
    foreach ($categorias as $_key) {
    // $IVA2              = 0;  

     $ingreso_categoria    = $obj -> ver_ingresos_turismo([$fi,$ff,$_key['idCategoria']]);
  
        $__row[] = array(
    //         'id'     => $_key['Categoria'],
            'nombre' => $_key['Categoria'],
            "sub"    => '',
            "iva"    => '',
            "hosp"   => '',
            'total'  => evaluar($ingreso_categoria),
            "opc"    => 0

            );

    }// end lista de folios

    // Encapsular arreglos
    $encode = [
        "thead" => $th,
        "row" => $__row
    ];

    return $encode;
}






#-- funciones --
echo json_encode($encode);


function evaluar($val){
    return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
}


?>
