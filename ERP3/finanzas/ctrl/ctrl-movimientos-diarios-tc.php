<?php
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_MX.UTF-8');

if(empty($_POST['opc'])){
    exit(0);
}

// importar Modelo & librerias

require_once('../mdl/mdl-movimientos-diarios.php');
$tc   = new TC;

require_once('../../conf/_Utileria.php');
$util = new Utileria;


# -- variables --
$encode = [];

switch ($_POST['opc']) {

    case 'frm-tc':
     $array  = $util -> sql($_POST);
     $ok     = $tc   -> add_tc($array);
     $encode = ['ok' => $ok];
    break;

    case 'lsTC':
     $encode = lsTC($tc);
    break;
          

    case 'QuitarTC':
     $ok = $tc->remove_tc([$_POST['id']]);
     $encode = $ok;
    break;


    


}


# -- Funciones --

function lsTC($obj){

    # Declarar variables
    $__row   = [];

    $now    = date('Y-m-d');

    $date   = $_POST['date'];
    
    #Consultar a la base de datos
    $ls    = $obj->lsTC([$_POST['fol']]);

     foreach ($ls as $_key) {
        $btn   = [];

        if($date == $now):

            $btn[] = [
                "fn"    => 'QuitarTC',
                "color" => 'danger',
                "icon"  => 'icon-trash-2'
            ];
       
        endif;    


         $__row[] = array(

            'id'           => $_key['id'],
            'Cliente'      => $_key['Cliente'],
            'Concepto'     => $_key['Concepto'],
            'TTCodigo'     => $_key['TTCodigo'],
            'Monto'        => evaluar($_key['Monto']),
            'Autorizacion' => $_key['Autorizacion'],
            'TCodigo'      => $_key['TCodigo'],
            'Observaciones'=> $_key['Observaciones'],
            'propina'      => evaluar($_key['propina']),
            "btn"          => $btn
        
        );

    } 
    
    #encapsular datos
        return [
            "thead" =>'',
            "row"   => $__row
        ];
}

# -- Complementos  -- 
function evaluar($val){

    if($val < 0) $text = 'text-danger'; else $text = '';
    return $val ? '<span class="'.$text.'"> $ ' . number_format($val, 2, '.', ',').'</span>' : '-';

}


echo json_encode($encode);