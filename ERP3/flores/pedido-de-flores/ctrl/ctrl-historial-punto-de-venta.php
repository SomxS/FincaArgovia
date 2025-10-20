<?php
if (empty($_POST['opc'])) {
  exit(0);
}

require_once '../mdl/mdl-historial-punto-de-venta.php';
$obj = new mdl();

# -- variables --
$encode = [];

# -- opciones  --
switch ($_POST['opc']) {
    case 'historial-pedidos':
        $th     = ['Folio','Lugar/Destino','Fecha','Hora','Estado','Productos',''];
        $encode = list_pedidos($obj,$th);
    break; 

    case 'ver-ticket':
    $th     = ['Producto','Cantidad','Precio','Total',''];
    $encode = ticket($obj,$th);
    break;

    case 'status-producto':
    $id     = $_POST['id'];
    $status = $_POST['status'];
    $obj->status_producto([$status,$id]);
    break;  

    case 'ver-complementos':
    $id     = $_POST['id'];

    $encode =   lsComplementos($obj);
    break;  

}



#-- funciones --
function list_pedidos($obj, $th){
   //  # Declarar variables
    $fi      = $_POST['fi'];
    $ff      = $_POST['ff'];

   
    $arreglo = [$fi, $ff];
    $__row   = [];

   //  # Sustituir por consulta a la base de datos
    $list            = $obj->VerFormatos($arreglo);

      
    foreach ($list as $_key) {

    $sql_producto = $obj->row_data([$_key['idLista']]);
    $total        = count($sql_producto);

     $botones   = [];

      $botones[] = [
         "fn"    => 'verTicket',
         "color" => 'primary text-primary btn-sm',
         "icon"  => 'icon-truck-1'
      ];

      $botones[] = [
         "fn"    => 'CancelarFolio',
         "color" => 'danger btn-sm',
         "icon"  => '  icon-trash'
      ];

        $__row[] = array(
            'id'             => $_key['idLista'],
            'fol'            => 'F-'.$_key['idLista'],
            'nombre'         => $_key['NombreCliente'],
            "fecha"          => $_key['fecha'],
            "hora"           => $_key['hora'],
            'stado'          => estado($_key['id_Estado']),
            "TotalProductos" => $total,
            "btn"            => $botones
        );

    } // end lista de folios

    // Encapsular arreglos
    $encode = [
        "thead" => $th,
        "row" => $__row
    ];

    return $encode;
}

function ticket($obj,$th){

   $idFolio      = $_POST['id'];
   # Lista de productos 
   $sql_producto = $obj->row_data([$_POST['id']]);
   $__row        = [];

   foreach ($sql_producto as $_key) {
    $id   = $_key['idListaProductos'];
    $list = $obj->ls_complemento([$id]);
    $totalComplementos = count($list);

    $btn = [];

    if($totalComplementos)
    $btn[] = [
        "fn"    => 'verComplementos('.$id.')',
        "color" => 'warning ',
        "icon"  => 'icon-star',
        "text"  => '('.$totalComplementos.')'
    ]; 

    $btn[] = [
        "fn"    => 'desactivarProducto('.$id.')',
        "color" => 'success ',
        "estado"=> 0,
        "icon"  => 'icon-toggle-on',
    ];
    
   $__row[] = array(
    'id'                => $_key['idListaProductos'],
    'NombreProducto'    => $_key['NombreProducto'],
    'cantidad'          => $_key['cantidad'],
    'costo'             => evaluar($_key['costo']),
    'total'             => evaluar($_key['total']),
    "btn_personalizado" => $btn
   );

   }// end lista de folios


   /* Consultar informacion del ticket */ 
        
    $lsFolio  = $obj -> ver_folio(array($idFolio));
    foreach ($lsFolio as $key) ;
    
    $objs = ticket_head(
        ['fecha'  =>$key['f'],
        'titulo' =>'NOTA DE VENTA',
        'folio'  =>$key['idLista'],
        'cliente'=>'Somx',
        'estado'=>estado($key['id_Estado']),
        ]
    );

    /*--      end consulta   -- */ 




    // Encapsular arreglos
    $encode = [
        "thead" => $th,
        "row"   => $__row,
        "frm_head"  => $objs
    ];

    return $encode;
}

function ticket_head($data){


    $div = '
     <div class=" ">
     <table style="font-size:.6em; " class="table table-bordered table-sm">
     
      <tr>
        <td class="col-sm-1 fw-bold">Fecha </td>
        <td class="col-sm-2">'.$data['fecha'].'</td>
        <td class="col-sm-6 text-center"> <strong>'.$data['titulo'].'</strong> </td>
        <td class="col-sm-1 fw-bold"> FOLIO  </td>
        <td class="col-sm-2 text-right text-end">'.$data['folio'].'</td>
      </tr>

        <tr>
        <td class="col-sm-1 fw-bold"> Cliente   </td>
        <td class="col-sm-2"> '.$data['cliente'].'  </td>
        <td class="col-sm-6"></td>

        <td class="col-sm-1 fw-bold">Destino:</td>
        <td class="col-sm-2">    </td>

        </tr>

        <tr>
        <td class="col-sm-1 fw-bold"> Estado   </td>
        <td class="col-sm-2">'.$data['estado'].'</td>
        <td class="col-sm-6"></td>

        <td class="col-sm-1 fw-bold"></td>
        <td class="col-sm-2">    </td>

        </tr>
      
      </table>
      </div>
    
   ' ;

    return $div;
}

function ticket_foot($data){
    $foot = '
    <div class="row">
    <div class="col-12 text-end">
       <strong>TOTAL:  '.evaluar($data['total']).' </strong>    
    </div>
    </div>';


    return $foot;
}


function lsComplementos($obj){
    $__row    = [];

    $id       = $_POST['id'];
    $th       = ['Producto','Cantidad','Costo',''];


    # Sustituir por consulta a la base de datos
      $list = $obj->ls_complemento([$_POST['id']]);

        foreach ($list as $_key) {
            $btn   = [];

            $btn[] = [
            "fn"    => 'ProductoComplemento',
            "color" => 'primary ',
            "icon"  => 'icon-toggle-off'
            ];


            $__row[] = array(

            'id'            => $_key['idInsumo'],
            'Nombre_insumo' => $_key['NombreProducto'],
            'cantidad'      => $_key['cantidad'],
            'Precio'        => $_key['Precio'],
            "btn"           => $btn
            
            );

        } // end lista de folios


    // Encapsular arreglos
    $encode = [
        "thead" => $th,
        "row" => $__row
    ];

    return $encode;
}


#-- funciones --
echo json_encode($encode);

function estado($opc){
  $lbl_status = '';

  switch($opc){

    case 1:
          $lbl_status = '<i class="text-success  icon-thumbs-up">Pendiente </i> ';
    break;

    case 2:
        $lbl_status   = '<i class="text-success  icon-ok-circle"> Terminado  </i> ';
    break;

  }

  return $lbl_status;
}

function evaluar($val){
    return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
}


?>
