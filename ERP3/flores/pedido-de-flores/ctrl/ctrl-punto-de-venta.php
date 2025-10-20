<?php
if(empty($_POST['opc'])){
exit(0);
}

require_once('../mdl/mdl-punto-de-venta.php');
$obj = new Puntodeventa;

$encode = [];
switch ($_POST['opc']) {
# ===========================
#   crear nota
# ===========================
case 'crear-nota':
    $encode = $_POST;
    $Cliente    = $_POST['Destino'];
    $Nota       = $_POST['nota'];
    $Fecha      = $_POST['Date'];
    $HourNow    = date("H:i:s");
    $FolioFecha = $Fecha.' '.$HourNow;
    $idCliente  = $obj->get_id_cliente([$Cliente]);
    $num        = $obj-> NumFolio();
    $data       = [$idCliente,$FolioFecha,1,$num];
    $encode     = $obj->crear_pedido($data);
break;    


# ===========================
#   Historial
# ===========================
case 'cancelar-folio':
   $id    = $_POST['id'];
   $sql   = $obj -> update_ticket([3,$id]);
   $encode = $sql;
break;


case 'list_cat':

    $list       = $obj->lsCat();
    $lsProducto = $obj->lsProductos();

 
    $data = array();
    foreach ($lsProducto as $key => $row) {
       $data[$key] = $row['NombreProducto'];
    }


    
    $encode = array(
        "lsCat"       => $list,
        "lsName"      => $data

    );



break;

case 'tab':
     $list = $obj->lsFolio(null);
     $encode[] = array(

        'id'    => 0,
        'fn'    => '',
        'name'  =>  '<i class=" icon-doc-text"></i> Nuevo'
     );


     foreach ($list as $key ) {
        $encode[] = array(
        'id'    => $key['idLista'],
        'fn'    => 'list_tabla_pedido',
        'name'  =>  'No. '.$key['idLista']
        );
      }

break;    

case 'list_subcat':

    $list = $obj->lsSubCat([$_POST['sub']]);

    foreach ($list as $key ) {
            $encode[] = array(
                'id'    => $key['idSubcategoria'],
                "valor" => $key['Nombre_subcategoria']
            );
    }
break;

case 'crear-pedido':

    $Cliente    = $_POST['Destino'];
    $Nota       = $_POST['Fecha'];
    $Fecha      = $_POST['Date'];
    $HourNow    = date("H:i:s");

    $FolioFecha = $Fecha.' '.$HourNow;
    $idCliente  = $obj->get_id_cliente([$Cliente]);
    $num        = $obj-> NumFolio();
    $data       = [$idCliente,$FolioFecha,1,$num];
    $encode     = $obj->crear_pedido($data);


    // $campos = ['id_cliente','foliofecha','id_Estado','folio'];

    // $encode   = $obj -> _INSERT($data,$campos,'hgpqgijw_ventas.lista_productos');

break;

case 'list-productos':
    $encode = list_($obj);
break;

case 'quitar':
   $id    = $_POST['id'];
   $sql   = $obj -> DeleteMovimiento([$id]);
break;

case 'agregar-producto':

    $folio        = $_POST['Folio'];
    $idProducto   = $_POST['id'];
    $costo        = $_POST['costo'];

    $list_pedido  = $obj->get_producto_vendido([$idProducto,$folio]);
    $encontro     = count($list_pedido);

    $id_vendido = 0;

    foreach ($list_pedido as $key ) {
        
        $id_vendido = $key['idListaProductos'];
        $costo      = $key['costo'];
        $cantidad   = $key['cantidad'];
    }

    $cantidad = $cantidad + 1;
    $encode   = [$cantidad];
    $total    = $cantidad * $costo;
    
    if($id_vendido){ // actualizar
        $encode = $obj->update_producto_vendido([$cantidad,$total,$id_vendido]);
        
    }else{

        $total = 1 * $_POST['costo'];

        $obj -> add_producto([
            $_POST['Folio'],
            $_POST['id'],
            1,
            $_POST['costo'],
            $total,
            1]);
    }   
    
    $encode = [
        'id'     => $id_vendido,
        'total'  => $total,
        'costo'  => $costo,
        'encontro' => $list_pedido
    ];
       
break;

case 'agregar-complemento':
    $nombre       = $_POST['FlorComplemento'];
    $idFlor       = $obj->get_id([$nombre]);
    $cant         = $_POST['CantidadComplemento'];
    $idReferencia = $_POST['id'];

    $obj->agregar_complemento(
    [
        $idReferencia,
        $idFlor,
        $cant,
        $nombre
    ]);

    $encode   = list_com($obj);
break;

  

case 'Buscar-flor': // Buscar precio de la flores
    $nombre     = $_POST['Flor'];
    $precio   = $obj-> _get_producto(array($nombre));
  
    $json = array('Precio'=>$precio);
break;

# ===========================
#   Realizar pedido 
# ===========================

case 'list_tabla_pedido':
    $encode = list_productos($obj,$_POST['fol']);
break;

case 'cancelar-pedido':
   $id       =  $_POST['id'];
   $encode   =  $obj -> cancelar_pedido([$id]);
break;

case 'terminar-formato':
    $encode = $obj -> update_ticket([2,$_POST['Folio']]);
break;

case 'ls-complemento':
  $encode =  list_com($obj);
break;

case 'quitar-complemento':
    $id     = $_POST['idComplemento'];
    $encode = $obj->quitar_complemento([$id]);
break;

case 'buscar-precio':
    $nombre = $_POST['flor'];
    $precio = $obj-> _get_producto(array($nombre));
    $encode   = ['precio'=>$precio];
break;    


}


# ===========================
#   Historial 
# ===========================



# ===========================
#   Historial 
# ===========================

function list_com($obj){
    $__row    = [];

    $id       = $_POST['id'];
    $th       = ['Producto','Cantidad','Costo',''];


    # Sustituir por consulta a la base de datos
    $list            = $obj->ls_complemento_id_producto([$id]);

    foreach ($list as $_key) {
        $btn   = [];

        $btn[] = [
        "fn"    => 'QuitarComplemento('.$_key['idInsumo'].','.$id.')',
        "color" => 'primary ',
        "icon"  => ' icon-trash-empty'
        ];


        $__row[] = array(

            'id'                => $_key['idInsumo'],
            'Nombre_insumo'     => '<span title="'. $_key['idInsumo'].'">'.$_key['NombreProducto'].'</span>',
            'cantidad'          => $_key['cantidad'],
            'Precio'            => $_key['Precio'],
            "btn_personalizado" => $btn
        
        );

    } // end lista de folios


    // Encapsular arreglos
    $encode = [
        "thead" => $th,
        "row" => $__row
    ];

    return $encode;
}

function list_productos($obj,$id){
    

    $__row      = [];
    $th         = array('PRODUCTO','CANTIDAD','UNIDAD','COSTO','TOTAL' ,'<i class=" icon-cog-2"></i>');
    $total_gral = 0;
  
    
    /* Consulta list de productos agregados */ 
    $data      = $obj->row_data(array($id));
    foreach ($data as $key ) {

      $botones  = [];
     
      $botones[] = [
      "fn"    => '__modal',
      "color" => 'warning',
      "icon"  => 'icon-star'
      ];

      $botones[] = [
      "fn"    => 'Quitar',
      "color" => 'danger',
      "icon"  => 'icon-trash-empty'
      ];


      
      $__row[] = [
          "id"       => $key['idListaProductos'],
          "producto" => '<span id="td_name'.$key['idListaProductos'].'" 
          name="'.$key['NombreProducto'].'">'.$key['NombreProducto'].'</span>',
          "cantidad" => $key['cantidad'],
          "unidad"   => $key['Unidad'],
          "costo"    => $key['costo'],
          "total"    => $key['total'],
          "btn"      => $botones
        ];
        
        
     $total_gral += $key['total'];
    }

    /*Datos de la nota de embarque*/ 

    $lsFolio  = $obj -> ver_folio([$id]);
    foreach ($lsFolio as $key) ;

    $head = ticket_head(
        [
        'fecha'   => formatSpanishDate($key['f']),
        'titulo'  => 'RIO CUILCO',
        'folio'   => $key['idLista'],
        'cliente' => $key['NombreCliente'],
        'destino' => $key['ciudad'],
        'estado'  => estado($key['id_Estado']),
        ]
    );
    # -- End nota de embarque -- 

    $foot = ticket_foot(
        [
        'id'    => $id,
        'total' => $total_gral
        ]
    );

    return [
        "thead" => $th,
        "row"   => $__row,
        "frm_head"  => $head,
        "frm_foot"  => $foot
    ];

  
}


function list_($obj){
    # Declarar variables
    $__row   = [];
    
   //  # Sustituir por consulta a la base de datos
    $list    = $obj->list_productos([$_POST['sub']]);
 
    foreach ($list as $_key) {
         $atributos   = [];

         $atributos[] = [
         "fn"        => 'agregar_producto',
         "color"     => 'bg-primary',
         "url_image" => null
         ];
        
        $__row[] = array(
            'id'     => $_key['idProducto'],
            'nombre' => $_key['NombreProducto'],
            'costo'  => $_key['Costo'],
            'atrr'   => $atributos,
            "opc"    => 0
        );

    }// end lista de folios

    $encode = [
        "row" => $__row
    ];

    return $encode;
}

function ticket_head($data){


    $div = '
     <div class=" ">
     <table style="font-size:.6em; " class="table table-bordered">
     
      <tr>
             <td class="col-sm-1 fw-bold"> Cliente:   </td>
        <td class="col-sm-2"> '.$data['cliente'].'  </td>
      
        <td colspan="2" class="col-sm-6 text-center"> <strong>'.$data['titulo'].'</strong> </td>
        <td class="col-sm-1 fw-bold"> FOLIO  </td>
        <td class="col-sm-2 text-right text-end">'.$data['folio'].'</td>
      </tr>
        
      <tr>
 
      
      <td class="col-sm-1 fw-bold">Destino :</td>
      <td class="col-sm-2">  '.$data['destino'].'  </td>
      <td class="col-sm-1 fw-bold">Fecha:</td>
      <td class="col-sm-2">'.$data['fecha'].'</td>
    <td class="col-sm-1 fw-bold">Estado:</td>
      <td class="col-sm-2"></td>
        </tr>
      
   
      </table>
      </div>
    
   ' ;
    //  <td class="col-sm-1 fw-bold">Fecha </td>
    //     <td class="col-sm-3">'.$data['fecha'].'</td>

   

    return $div;
}

function ticket_foot($data){
    $foot = '
    <div class="row">
    <div class="col-12 text-end">
       <strong>TOTAL:  '.evaluar($data['total']).' </strong>    
    </div>
    </div>';

    if($data['id'])

    $foot .= '    
    <div class="row  mt-3">
    <div class="col-12 text-end">
    <button class="btn btn-outline-primary" onclick="subirPedidos('.$data['id'].')"> <i class="icon-ok"></i> Terminar Pedido </button>
    <button class="btn btn-outline-danger"  onclick="cancelarPedido('.$data['id'].')"> <i class="icon-ok"></i> Cancelar Pedido </button>
    </div>
    </div>
    ';

    return $foot;
}


echo json_encode($encode);

# ===========================
#   Complementos
# ===========================

function evaluar($val){
    return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
}

function formatSpanishDate($fecha = null) {
    setlocale(LC_TIME, 'es_ES.UTF-8'); // Establecer la localización a español

    if ($fecha === null) {
        $fecha = date('Y-m-d'); // Utilizar la fecha actual si no se proporciona una fecha específica
    }

    // Convertir la cadena de fecha a una marca de tiempo
    $marcaTiempo = strtotime($fecha);

    $formatoFecha = " %d de %B del %Y"; // Formato de fecha en español
    $fechaFormateada = strftime($formatoFecha, $marcaTiempo);

    $lbl = '<label style="font-size:.68em;">'.$fechaFormateada.'</label>';

    return $fechaFormateada;
}


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

?>