<?php
if(empty($_POST['opc'])){
    exit(0);
}

require_once('../mdl/mdl-pedido-flores.php');
$obj = new Pedidoflores;

$encode = [];
switch ($_POST['opc']) {
        case 'prueba':
            $encode = $obj->now();
        break;

        case 'cargar_clientes':
         $select  = $obj ->Group_destino();
         $data = [];
         foreach ($select as $key => $row) {
            $encode[$key] = $row['NombreCliente'];
         }
        break;
    
        case 'init':

         $data    = $obj -> pedido_activo(null);
         $flag    = 0;
         $idLista = 0;
         $table   = '';
         $Destino = '';
         $Fecha   = '';
         $activo  = count($data);

            if ($activo) { // Existen pedidos activos

            foreach ($data as $key);
               $flag    = 1;
               $Fecha   = $key['fecha'];
               $Destino = $key['NombreCliente'];
               $idLista = $key['idLista'];
               $table   = list_productos($obj,$idLista); //@tabla lista productos
            }

         //    $lblFolio = Format_Folio($idLista);
            $encode = array(
               0=> $flag,
               1=>$table,
               2=>$idLista,
               3=>$Fecha,
               4=>$Destino,
               5=>$activo
            );

        break;

        case 'crear-formato':
         $Cliente    = $_POST['Destino'];                        //Lugar
         $Fecha      = '2023-09-24';                        // Date
         $HourNow    = date("H:i:s");
         $FolioFecha = $Fecha.' '.$HourNow;

         $idCliente  = $obj->get_id_cliente([$Cliente]);
         $num        = $obj-> NumFolio();
         
         $data    = [$idCliente,$FolioFecha,1,$num];
         $campos = ['id_cliente','foliofecha','id_Estado','folio'];

         $encode   = $obj -> _INSERT($data,$campos,'hgpqgijw_ventas.lista_productos');

        break;

        case 'terminar-formato':
           
            $idFolio = $_POST['Folio'];
            $data    = array(2,$idFolio);
            $campos  = array('id_Estado');
            $bd      ='hgpqgijw_ventas.lista_productos';
            $where   = array('idLista');
            $encode   = $obj -> _UPDATE($data,$campos,$bd,$where);
        break;


        case 'historial-pedidos':
         $th     = ['Folio','Lugar/Destino','Fecha','Hora','Estado',''];
         $encode = list_pedidos($obj,$th);
        break;

         case 'list_tabla_pedido':
        
         $encode = list_productos($obj,$_POST['fol']);
        break;

        case 'ver-ticket':
         $th     = ['Producto','Cantidad','Precio','Total','Detalles',''];
         $encode = ticket($obj,$th);
        break;

         case 'list-clientes':
            $th     = ['Cliente','Lugar','Direccion','Telefono','Clave','Estatus',''];
            $encode = list_($obj,$th);
         break; 


}

# ----------------------- #
#  Historial de pedidos   #
# ----------------------- #
function list_pedidos($obj, $th){
   //  # Declarar variables
   // //  $fi      = $_POST['fi'];
   // //  $ff      = $_POST['ff'];

    $fi      = '2023-01-01';
    $ff      = '2023-10-31';

    $arreglo = [$fi, $ff];
    $__row   = [];

   //  # Sustituir por consulta a la base de datos
    $list            = $obj->VerFormatos($arreglo);
   //  $total_productos = count($list);
    $btn = [];
    foreach ($list as $_key) {
      
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
            'id'     => $_key['idLista'],
            'nombre' => $_key['NombreCliente'],
            "fecha"  => $_key['fecha'],
            "hora"  => $_key['hora'],
            'stado'  => $_key['id_Estado'],
            "btn"    => $botones
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

   $idFolio = $_POST['id'];

   $sql_producto = $obj->row_data(array($idFolio));
   $__row    = [];


   foreach ($sql_producto as $_key) {
    


   $__row[] = array(
   'Cliente'   => $_key['descripcion'],
   'cantidad'  => $_key['cantidad'],
   "direccion" => $_key['direccion'],
   'tel'       => $_key['contacto'],
   'clave'     => $_key['clave'],
   'Estatus'   => estatus($_key['estado_cliente']),
   "opc"       => ''
   );

   }// end lista de folios


    # -----
   $tb = '<div class="mt-20" id="contenedor_ticket">';

   $tb .= '<div style="margin-top:10px;" class="row">';
   $tb .= '<div class="col-xs-6 col-sm-9 t_1" >  <br> CLIENTE: SERGIO</div>';
   $tb .= '<div class="col-xs-6 col-sm-3 text-right text-danger" <b> </b></div>';
   $tb .= '</div>';

    $cabecera = [$tb];



    // Encapsular arreglos
    $encode = [
        "thead" => $th,
        "row"   => $__row,
        "head"  => $cabecera
    ];

    return $encode;
}


function list_($obj,$th){
    # Declarar variables
    $__row   = [];
    
    # Sustituir por consulta a la base de datos
    $list    = $obj->list_cliente();
   

    foreach ($list as $_key) {

       $btn_edit   = btn_edit($_key['idCliente'],$_key['scl_name'],'editar_sucursales');
       $btn_toggle = btn_toggle($_key['idCliente'],'toggleStatus',1);
        
        $__row[] = array(
            'Cliente'   => $_key['NombreCliente'],
            'Lugar'     => $_key['ciudad'],
            "direccion" => $_key['direccion'],
            'tel'       => $_key['contacto'],
            'clave'     => $_key['clave'],
            'Estatus'   => estatus($_key['estado_cliente']),
            "opc"       => $btn_edit.''.$btn_toggle 
        );

    }// end lista de folios

    // Encapsular arreglos
    $encode = [
        "thead" => $th,
        "row" => $__row
    ];

    return $encode;
}

function btn_edit($id_btn,$name,$nombre_funcion){

$__btn = '
<button 
class="btn btn-outline-primary btn-sm" title="Editar" onclick="'.$nombre_funcion.'('.$id_btn.',\''.$name.'\') ">
<i class="icon-pencil"></i>

</button>';    

return $__btn;
}

function btn_toggle($id_btn,$nombre_funcion,$estatus){
$iconToggle = "icon-toggle-on";

if ($estatus == 0) {
$iconToggle = "icon-toggle-off";
}

$__btn = '
<button class="btn btn-outline-danger btn-sm" title="Estado"

id="btn_'.$id_btn.'" 
estatus="'.$estatus.'" 
onclick="'.$nombre_funcion.'('.$id_btn.')" >

<i class="'.$iconToggle.'"></i>
</button>

';

return $__btn;
}

function estatus($idEstatus){
   $estatus = '';
   switch ($idEstatus) {
   case 1:
   $estatus = '<b><span class="text-success">Activo</span></b>';
   break;

   case 0:
   $estatus = '<b><span class="text-danger">Inactivo</span></b>';
   break;

   }

   return $estatus;
}


# list_productos


function list_productos($obj,$id){
    

    $__row     = [];
    $number_list = 0;

    $data      = $obj->row_data(array($id));
    $count_reg = count($data);
    
    $th   = array('#','PRODUCTO','CANTIDAD','UNIDAD','COSTO','TOTAL' ,'<i class=" icon-cog-2"></i>');
    
    foreach ($data as $key => $v) {
      $botones  = [];
      $producto = [];
      $costo    = [];
      $Total    = [];

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

      $producto[] = [
        "fn"    => 'GuardarDatos',
        "name"  => 'Item',
        "atrr"  => 'onFocus="addBusqueda()" ',
        "disabled"  => false,
        "valor" => $v['descripcion']
      ];
     
      $costo[] = [
        "fn"    => '',
        "name"  => 'costo',
        "atrr"  => ' ',
        "disabled"  => true,
        "valor" => $$v['costo']
      ];
      
      $Total[] = [
        "fn"    => '',
        "name"  => 'Total',
        "atrr"  => ' ',
        "disabled"  => true,
        "valor" => $$v['total']
      ];


      $__row[] = [
         "id"          => $v['idListaProductos'],
         "producto"    => $producto ,
         "cantidad"    => $v['cantidad'],
         "unidad"      => $v['Unidad'],
         "costo"       => $costo,
         "total"       => $Total,
        
         "btn"         => $botones
      ];


    }

   //  for ($i = $count_reg; $i < 7; $i++) {
   //      // $btn  =' <button class="btn btn-warning icon-star btn-sm"  ></button></td>';
   //      $btn = '<button class="btn btn-warning icon-star btn-sm disabled"  ></button></td>';
   //      $__fila[] = array(

   //      '<i class="icon-right-dir-1"></i>',
   //      '',
   //      '',
   //      '',
   //      '',
   //      '',
   //      '',
   //      '',
     

   //      $btn
   //      ); 
   //  }

   //  $lista_productos = simple_tabla_php($th, $__fila);

   //  $lista_productos .= '
   //  <div class=" row">
   //  <div class=" col-xs-12">
   //  <label style="font-size:1.2em;" >NOTA.-</label>
   //  <textarea id="txtDetalles" placeholder="Agregar nota de pedido" class="form-control" onkeyup="actualizar_datos(\'Detalles\')"></textarea>
   //  </div>
   //  </div>
    
   //  '; 

   //  return $lista_productos;
    // Encapsular arreglos
    $encode = [
        "thead" => $th,
        "row" => $__row
    ];

    return $encode;
}


function simple_tabla_php($th,$row){
    $thead = '';
    $tbody = '';

    $total_row = count($th)-1;
    $thead .= "<tr>";

    for ($i = 0; $i <= $total_row; $i++) {
    $thead .= '<th class="" >  ' .$th[$i] . ' </th>';
    }

    $thead .= "<tr>";

   foreach ($row as $key => $value) {

      $bg = 'bg-default-soft';

     
      $tbody .= "<tr class='{$bg}'>";
     
      for ($i = 0; $i <= $total_row;$i++){

        $tbody .= '<td class="text-center"> ' . $value[$i] . ' </td>';
      
      }

      $tbody .= '</tr>';
   }

   // Imprime los resultados en una tabla
    
    $table .= '
        <table style="margin-top:15px; font-size:.68em; font-weight:500;"
         class="table table-bordered table-xs" width="100%" id="">
        
        <thead>
          '.$thead.'
        </thead>
       
        <tbody>
           '.$tbody.'
        </tbody>
        </table>
    ';


   return $table;
}



function evaluar($val){
    return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
}

echo json_encode($encode);
?>