<?php
if(empty($_POST['opc'])) exit(0);

require_once('../mdl/mdl-reportes-ingreso.php');
$obj = new Reportesingreso;

$meses = ['','ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE'];

$encode = [];
switch ($_POST['opc']) {
    case 'listUDN':
        $encode = $obj->lsUDN();
    break;
    case 'ingresos':
        $mes = 12;
        $_MES = [];

       $th = ['Ingresos', 'Total'];
        
        for ($x = $mes - 1; $x <= $mes; $x++) {
            $th[] = $meses[$x];
        }
        
        $encode = ls($obj,$th);
    break;
}

# Funciones 


function ls($obj,$th){
# Declarar variables
    $__row   = [];
    $mes     = 12;

#Consultar a la base de datos

    // # REPORTE DE INGRESOS
    $list_categoria    = $obj->VER_CATEGORIAS([1]);
   

    foreach ($list_categoria as $key) {
        $_MES = [];

        for ($x = $mes - 1; $x <= $mes; $x++) {
        $f = __DESGLOZE_MENSUAL($obj, $key['idCategoria'], $x, 2023);
        array_push($_MES,$f );
        }

        $__row[] = array(
        'id'   => '',
        'name' => $key['Categoria'],
        'total' => '',
        'mes1' => evaluar($_MES[0]),
        'mes2' => evaluar($_MES[1]),
        "opc"  => 0
        );
    }


#encapsular datos
    $encode = [
        "thead" => $th,
        "row" => $__row
    ];

return $encode;  

}


function list_($obj,$th){
    // # Declarar variables
    // $mes    = 9;
    // $anio   = 2023;
    // $__row  = [];
        
    // # REPORTE DE INGRESOS
    // $list_categoria    = $obj->VER_CATEGORIAS([1]);
    // // $fpago  = $obj ->Select_formaspago_by_categoria(array(1));
          
    // foreach ($list_categoria as $_key) {
 

    
 
    //     # desgloze mensual
    //     $__row[] = array(
    //         'Categoria'   => '<strong>'.$_key['Categoria'].'</strong>',
    //         'total'     => '',
    //         'm1'     => evaluar($_MES[0]),
    //         'm2'     => evaluar($_MES[1]),
    //         "opc"       => 0
    //     );
    // }// end lista de folios

    // $__row[] = array(
    //     'Categoria' => 'FORMAS DE PAGO ',
    //     'total'     => '',
    //     'm1'        => '',
    //     'm2'        => '',
    //     // 'm3'        => '',
    //     // 'Estatus'   => '',
    //     "opc"       => 1
    // );

    // // Encapsular arreglos
    // return [
    //     "thead" => $th,
    //     "row" => $__row
    // ];

    // return $encode;
}

// function list_2($obj,$th){
//     # Declarar variables
//   $mes = 9;
//   $anio = 2023;
//     $__row   = [];
    
//     # REPORTE DE INGRESOS
//     $list_categoria    = $obj->VER_CATEGORIAS(1);
//      $fpago  = $obj ->Select_formaspago_by_categoria(array(1));
          
//     foreach ($list_categoria as $_key) {
//         $sub      = $obj ->Select_group(array($_key['idc']));

//         # desgloze mensual
//         $__row[] = array(
//             'Categoria'   => '<strong>'.$_key['Categoria'].'</strong>',
//             'total'     => '',
//             'm1'     => $_MES[0],
//             'm2'     => $_MES[1],
//             "opc"       => 0
//         );

//         foreach ($sub as $_KEY) {
//             $sql    = $obj ->Select_Subcategoria_x_grupo($_key['idc'],$_KEY['idgrupo']);
//             $__row[] = array(
//                 'Categoria'   => $_KEY['gruponombre'],
//                 'total'     => '',
//                 'm1'     => '',
//                 'm2'     => '',
//                 "opc"       => 1
//             );
            
//             foreach($sql as $key => $value){ // vista de subcategorias
            
//                 $total_sub = [];
//                 foreach ($fpago as $key => $valfpago) {
//                     $total = '';
//                     $_MES = [];

//                     for ($x = $mes - 1; $x <= $mes; $x++) {
//                         $total = __reporte_mensual($obj, $x, $anio,
//                         $value['idSubcategoria'],
//                         $valfpago['idFormas_Pago']);
//                         array_push($_MES,$total );
//                     }
       

//                     $__row[] = array(
//                         'Categoria' => '<strong class="text-danger">'.$valfpago['FormasPago'].'</strong>',
//                         'total'     => '',
//                         'm1'        => $_MES[0],
//                         'm2'        => $_MES[1],
//                         "opc"       => 1
//                     );
//                 }


//                 $__row[] = array(
//                     'Categoria'   => $value['Subcategoria'],
//                     'total'     => '',
//                     'm1'     => '',
//                     'm2'     => '',
//                     "opc"       => 0
//                 );
            
//              }//end subcategoria
//         }
//     }// end lista de folios

//     $__row[] = array(
//         'Categoria'   => 'FORMAS DE PAGO ',
//         'total'     => '',
//         'm1'     => '',
//         'm2'     => '',
//         // 'm3'     => '',
//         // 'Estatus'   => '',
//         "opc"       => 1
//     );

//     // Encapsular arreglos
//     $encode = [
//         "thead" => $th,
//         "row" => $__row
//     ];

//     return $encode;
// }

function _MES($NoMes){
    $mes = '';
    switch ($NoMes) {
        case 1:
            $mes = 'ENERO';
        break;

        case 2:
            $mes = 'FEBRERO';
        break;

        case 3:
            $mes = 'MARZO';
        break;
       
        case 4:
            $mes = 'ABRIL';
        break;
        
        case 5:
            $mes = 'MAYO';
        break;

        case 6:
            $mes = 'JUNIO';
        break;
        
        case 7:
            $mes = 'JULIO';
        break;
      
        case 8:
            $mes = 'AGOSTO';
        break;
        case 9:
            $mes = 'SEPTIEMBRE';
        break;
       
        case 10:
            $mes = 'OCTUBRE';
        break;

        case 11:
            $mes = 'NOVIEMBRE';
        break;

        case 8:
           $mes = 'DICIEMBRE';
        break;
    }
    
    return $mes;
}


function __reporte_mensual($obj,$mes,$anio,$idS,$idF){
    $total = 0;   
    $montoFP  = $obj->ver_reporte_mensual(array($mes,$anio,$idS,$idF));
// $total    = $total + $montoFP;

    return $montoFP;
}


 function __DESGLOZE_MENSUAL($obj,$idCategoria,$mes,$anio){
    // $frm           = '';
    $total_mensual = 0;
    $sub           = $obj ->Select_group([$idCategoria]);

    foreach($sub as $_KEY){
        $id_g  = 1;
        $total = 0;
        $tb    = '';

        $sql    = $obj ->Select_Subcategoria_x_grupo([$idCategoria,$_KEY['idgrupo']]);    
        $fpago  = $obj ->Select_formaspago_by_categoria(array($id_g));
        
        foreach($sql as $key => $value){ // vista de subcategorias
            $total  = 0;
            # Formas de pago ---
            foreach ($fpago as $key => $valfpago) {
                $montoFP  = $obj->ver_reporte_mensual(array($mes,$anio,$value['idSubcategoria'],$valfpago['idFormas_Pago']));
                $total    = $total + $montoFP;
            }

            $total_mensual = $total_mensual + $total;
        }
    }

    return $total_mensual;
}


function evaluar($val){
    return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
}

# Empaquetar datos

echo json_encode($encode);
?>