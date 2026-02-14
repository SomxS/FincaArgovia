<?php


if(empty($_POST['opc'])){
    exit(0);
}

require_once('../mdl/mdl-movimientos-diarios.php');
$obj       = new Movimientosdiarios;
$cortesias = new Cortesias;


require_once('../../conf/_Utileria.php');
$util = new Utileria;

$encode = [];

switch ($_POST['opc']) {

    case 'lsCortesias':
        $encode = lsCortesias($cortesias);
    break;

  

    case 'frm-cortesias':

        $data = [
         'id_Folio'        => $_POST['id'],
         'id_Subcategoria' => $_POST['idSubcategoria'],
         'Subtotal'        => $_POST['cantidad'],
         'pax'             => 1,
         'Noche'           => 1
        ];

        $dtx = $util ->sql($data);

        $ok = $cortesias ->insert_cortesia($dtx);

        $encode = [
            'ok'=> $ok
        ];
  
    break;

    case 'quitar_cortesia':

        $ok        = $cortesias ->remove_producto([$_POST['idCortesia']]);
        $encode    = ['ok'=> $ok];

    break;


    case 'frm-tc':
        $encode = $_POST;
    break; 
    
    
    case 'lstcx':
        $encode = $_POST;
    break;





}

echo json_encode($encode);

function lsCortesias($obj){
    # Declarar variables
    $__row   = [];
    
    #Consultar a la base de datos
    $ls    = $obj->lsCortesias([$_POST['fecha']]);
    
    $totalGral = 0;
     
    foreach ($ls as $key) {

        $btn  = [];

        $btn[]  = [
            'fn'      => 'quitarCortesia',
            'color'   => 'danger p-1',
            'icon'    => 'icon-trash-2',
        ];

        
        $__row[] = array(
        'id'           => $key['id'],
        'Folio'        => $key['Folio'],
        'Fecha'        => $key['Fecha'],
        'Concepto'     => $key['Subcategoria'],
        
        'Total'        => evaluar($key['Subtotal']),
        "btn"          => $btn
        );

        $totalGral += $key['Subtotal'];
     
    }


        $__row[] = array(
            'id'           => $key['id'],
            'Folio'        => 'TOTAL',
            'Fecha'        => '',
            'Concepto'     => '',
            'Total'        => evaluar($totalGral),
            'btn'          => null,
            "opc"          => 2
            
        );

    
    #encapsular datos

    return [
        "thead" => '',
        "row"   => $__row
    ];
}



function evaluar($val){
    return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
}
?>