<?php
if(empty($_POST['opc'])){
exit(0);
}

require_once('../mdl/mdl-mantenimiento-almacen.php');
$obj = new Mantenimientoalmacen;

$encode = [];
switch ($_POST['opc']) {
    case 'listUDN': 
            $encode = $obj->lsZona();
        break;
    case 'list': 
            $th     = ['Codigo','Zona','Equipo','Categoria','Area','Cantidad','Costo','fecha ingreso',''];
            $encode = list_($obj,$th);
        break;
    case 'list-esp': 
            $encode = list_esp($obj);
        break;

    case 'list-item': 
            $encode = list_item($obj);
        break;

}















function list_($obj,$th){
    # Declarar variables
    $__row = [];
    
    # Sustituir por consulta a la base de datos
    $list = $obj->list_equipos([1,$_POST['udn']]);
   
    foreach ($list as $_key) {
    $btn = [];

    $btn[] = [
    "fn"    => 'subir_poliza',
    "color" => 'warning ',
    "icon"  => 'icon-upload-1'
    ];
    
    $btn[] = [
    "fn"    => 'subir_poliza',
    "color" => 'info ',
    "icon"  => ' icon-camera-1'
    ];

    $__row[] = array(
        'Codigo'          => $_key['CodigoEquipo'],
        'zona'            => $_key['zona'],
        "Nombre_Equipo"   => $_key['Nombre_Equipo'],
        "nombrecategoria" => $_key['nombrecategoria'],
        "Nombre_Area"     => $_key['Nombre_Area'],
        "cantidad"        => $_key['cantidad'],
        'Costo'           => evaluar($_key['Costo']),
        "fecha_i"         => $_key['fecha_i'],
            // "esp" => $_key['Autorizacion'],
            // "c"   => $_key['Cliente'],
            // //          
        "btn" => $btn
    );
      
    }// end lista de folios

        // Encapsular arreglos
    $encode = [
        "thead" => $th,
        "row"   => $__row
    ];

    return $encode;
}



























function list_item($obj){
    # Declarar variables
    $__row = [];
    
       //  # Sustituir por consulta a la base de datos
    $list = $obj->lsarea(null);
 
    foreach ($list as $_key) {
         $atributos   = [];
         $total       = $obj->list_equipos_count([1,$_key['id']]);
         $atributos[] = [
         "fn"        => 'consulta_item',
         "color"     => 'bg-primary-1',
         "url_image" => ''
         ];
        
        $__row[] = array(
            'id'     => $_key['id'],
            'nombre' => $_key['valor'],
            'costo'  => $total.' PZA',
            'atrr'   => $atributos,
            "opc"    => 0
        );

    }// end lista de folios

    $encode = [
        "row" => $__row
    ];

    return $encode;
}

function list_esp($obj){
    # Declarar variables
    $__row = [];
    $th    = ['Codigo','Zona','Equipo','Categoria','Area','Cantidad','Costo','fecha ingreso',$_POST['udn']];
    
    # Sustituir por consulta a la base de datos
     $list = $obj->list_equipos_esp([1,$_POST['udn']]);
   
    foreach ($list as $_key) {
    $btn = [];

    $btn[] = [
    "fn"    => 'subir_poliza',
    "color" => 'warning ',
    "icon"  => 'icon-upload-1'
    ];
    
    $btn[] = [
    "fn"    => 'subir_poliza',
    "color" => 'info ',
    "icon"  => ' icon-camera-1'
    ];

    $__row[] = array(
        'Codigo'          => $_key['CodigoEquipo'],
        'zona'            => $_key['zona'],
        "Nombre_Equipo"   => $_key['Nombre_Equipo'],
        "nombrecategoria" => $_key['nombrecategoria'],
        "Nombre_Area"     => $_key['Nombre_Area'],
        "cantidad"        => $_key['cantidad'],

        'Costo'   => evaluar($_key['Costo']),
        "fecha_i" => $_key['fecha_i'],
            // "esp" => $_key['Autorizacion'],
            // "c"   => $_key['Cliente'],
            // //          
        "btn" => $btn
    );
      
    }// end lista de folios

        // Encapsular arreglos
    $encode = [
        "thead" => $th,
        "row"   => $__row
    ];

    return $encode;
}

echo json_encode($encode);

function evaluar($val){
    return $val ? '$ ' . number_format($val, 2, '.', ','): '-';
}

?>