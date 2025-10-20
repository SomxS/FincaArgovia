<?php
if(empty($_POST['opc'])){
    exit(0);
}

require_once('../mdl/mdl-flores.php');
$obj = new Flores;


# -- variables --
$encode = [];

/* Guarda los recorridos */ 
$array  = array();

foreach ($_POST as $key => $value) {
  if($key != 'opc') $array[] = $value;
}


switch ($_POST['opc']) {
case 'listUDN':
$encode = $obj->lsUDN();
break;

case 'initComponents':
    // $data_categoria      =  $obj->lsUDN();
    $data_categoria      =  $obj->lsCategoria();
   
    $encode = [
    "data_categoria" => $data_categoria,
    // "tipo"      => $tipoMovimiento,
    ];

break;

case 'lsFolios':
    $th     = ['Producto','Costo','Venta','Categoria','SubCategoria','Stock Minimo',''];   
    $encode = ls($obj,$th);
break;

}

#----------------------
# Funciones y metodos
#----------------------

function ls($obj,$th){
    # Declarar variables
    $__row   = [];

 
    # Consultar a la base de datos
    $ls    = $obj->VerProductos();
    
    // $cont = count($ls);

    foreach ($ls as $key) {
            $btn = [];

            $btn[] = [
                "icon"  => "icon-pencil",
                "color" => "primary",
                "fn"    => "verTicket"
            ];

             $btn[] = [
                "icon"  => "icon-trash-2",
                "color" => "danger",
                "fn"    => "VerPedidos"
            ];

            $__row[] = array(
                'id'             => $key['idProducto'],
                'NombreProducto' => $key['NombreProducto'],
                'Costo'          => $key['Costo'],
                'Venta'          => $key['Venta'],
                'a'              => $key['NombreProducto'],
                'x'              => $key['NombreProducto'],
                'xa'             => $key['NombreProducto'],
                "btn"            => $btn
            );
    }


    #encapsular datos
        $encode = [
            "thead" => $th,
            "row"   => $__row,
            "cont"  => $cont
        ];

    return $encode;  

}



echo json_encode($encode);
?>