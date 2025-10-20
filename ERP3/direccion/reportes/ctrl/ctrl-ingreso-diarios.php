<?php
if(empty($_POST['opc'])){
exit(0);
}

require_once('../mdl/mdl-ingreso-diarios.php');
$obj = new Ingresodiarios;

$encode = [];
switch ($_POST['opc']) {
    case 'listUDN':
    $encode = $obj->lsUDN();
    break;

    case 'files':-
    $th =['Fecha','Hora','Archivo','Descarga'];
    $encode = list_file($obj,$th);
    break;

    case 'tc':
    $th =['Fecha','Monto','Terminal','Tipo de tc','Concepto de Pago','No autorizacion','Nombre Cliente'];
    $encode = list_tc($obj,$th);
    break;
}

function list_file($obj,$th){
    # Declarar variables
    $__row   = [];
    
    # Sustituir por consulta a la base de datos
    $list    = $obj->list_tabla_file();
   
    foreach ($list as $_key) {
        $a = '<a 
      class="btn btn-outline-info btn-sm" 
      title="Visualizar" 
      href="     ' . $_key['Ruta'] . '' . $_key['Archivo'] . ' " target="_blank"><i class="bx icon-eye"></i></a>';
        if ($_key['Archivo'] != '') {
            $__row[] = array(
                'Fecha' => $_key['Fecha'],
                'Hora' => $_key['Hora'],
                "Archivo" => $_key['Archivo'],

                'Descarga' => $a,
                "opc" => ''
            );
        }
    }// end lista de folios

    // Encapsular arreglos
    $encode = [
        "thead" => $th,
        "row" => $__row
    ];

    return $encode;
}

function list_tc($obj,$th){
    # Declarar variables
    $__row   = [];
    
    # Sustituir por consulta a la base de datos
    $list    = $obj->Select_TC_Data('2023-10-01','2023-10-03');
   
    foreach ($list as $_key) {
       
        
            $__row[] = array(
                'Fecha' => $_key['Fecha'],
                'Monto' => evaluar($_key['Monto']),
                "Terminal" => $_key['TCodigo'],
                "tipo" => $_key['TTCodigo'],
                "concepto" => $_key['Concepto'],

                "esp" => $_key['Autorizacion'],
                "c" => $_key['Cliente'],
    //          
                "opc" => ''
            );
      
    }// end lista de folios

    // Encapsular arreglos
    $encode = [
        "thead" => $th,
        "row" => $__row
    ];

    return $encode;
}

echo json_encode($encode);


function evaluar($val){
    return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
}

?>