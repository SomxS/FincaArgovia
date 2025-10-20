<?php
if(empty($_POST['opc'])){
 exit(0);
}

require_once('../mdl/mdl-pedidos-cerrar.php');
$obj = new cerrarPedidos;

# -- variables --
$encode = [];

# -- opciones  --
switch ($_POST['opc']) {
    
    case 'layout-lista-pedidos':
        $th     = ['Folio','Cliente','Fecha','Estado'];
        $encode = list_($obj, $th);
    break;

    case 'formulario-js':
        $encode = list_item($obj);
    break;

    case 'list-flores':

        $list  = $obj->list_flores(null);
        $total = count($list);

        foreach ($list as $key => $row) {
          $encode[$key] = $row['NombreProducto'];
        }

    break;

    case 'list-formato-cerrar-pedido':
        $th     = ['Folio','Cliente','Fecha','Estado'];   
        $encode = list_pedidos($obj,$th);
    break;

    case 'cerrarGrupoPedidos':
        $encode = cerrarGrupoPedidos($obj);
    break;    
}

function cerrarGrupoPedidos($obj) {
  
    # Consultar a la base de datos
    $list    = $obj->Consultar_folio(null);
    $TotalPedidos = count($list);

    /* Crear Hoja de pedidos */ 
    $HojaPedido = $obj->crearHojaPedido(['2024-01-28',$TotalPedidos]);

    $fechaCierre = date("Y-m-d H:i:s");
    foreach ($list as $key ) {
        $obj->cerrarPedido([$fechaCierre, $HojaPedido,$key['idLista']]);
    }
    
    
   return $HojaPedido;

}

function list_pedidos($obj,$th){
    # Declarar variables
    $__row   = [];
    $__col   = [];

    # Consultar a la base de datos
    $list    = $obj->Consultar_folio(null);
    $cont = 0;
    foreach ($list as $key ) {

        $list_row = $obj->row_data(array($key['idLista']));

        foreach($list_row as $num => $list){
        
            #Desgloze de productos
            $__row[] = array(
                "id" => $list['cantidad'],
                "cantidad" => $list['cantidad'],
                'nombre'   => $list['NombreProducto'],
                'costo'    => evaluar($list['costo']),
                "opc"      => 0
            );

        }
        
        #Grupo de productos 
        $__col[] = array(
            'id'     => $key['fecha'],
            'fecha'  => $key['fecha'],
            'titulo' => 'Cliente: '.$key['NombreCliente'].' / '.formatSpanishDate($key['fecha']).' / '.$key['hora'],
            'row'    => $__row 
        );

    }// end lista de folios

    # encapsular datos 

    $encode = [
        "thead" => $th,
        "cols"  => $__col,
        "row"   => $__row,

    ];

    return $encode;  

}

function list_item($obj){

    # Declarar variables
    $__row   = [];
    
   //  # Sustituir por consulta a la base de datos
    $list    = $obj->Consultar_folio(null);

 
    foreach ($list as $_key) {
         $atributos   = [];

         $atributos[] = [
         "label"   => 'input',
         "tipo"    => 'text',
         "default" => '',
         "name"    => ''
         ];
        

        $__row[] = array(
         
            'form'     => $_key['idLista'],
            'inputs'   => $atributos,
            "opc"    => 0
        );

    }// end lista de folios

    $encode = [
        "row" => $__row
    ];

    return $encode;
}

function list_($obj,$th){
    # Declarar variables
    $__row   = [];
    
    # Sustituir por consulta a la base de datos
    $list    = $obj->Consultar_folio($arreglo);
  
    foreach ($list as $_key) {
        
        $__row[] = array(
            'nombre' => '<strong>'.$_key['NombreCliente'].'     </strong>',
            "fecha"  => '<strong>'.dia_format($_key['fecha']).' </strong>',
            'id'     => $_key['idLista'],
            "stat"   => '',
            "opc"    => 1
        );

        # 

         $list_row = $obj->row_data(array($_key['idLista']));

         foreach($list_row as $num => $list){

          $__row[] = array(
              'cantidad'     => $list['idListaProductos'],
              'nombre' => $list['NombreProducto'],
              "fecha"  => $list['cantidad'],
              "stat"   => '',
              "opc"    =>0
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

function formatSpanishDate($fecha = null) {
    setlocale(LC_TIME, 'es_ES.UTF-8'); // Establecer la localización a español

    if ($fecha === null) {
        $fecha = date('Y-m-d'); // Utilizar la fecha actual si no se proporciona una fecha específica
    }

    // Convertir la cadena de fecha a una marca de tiempo
    $marcaTiempo = strtotime($fecha);

    $formatoFecha = "%A, %d de %B del %Y"; // Formato de fecha en español
    $fechaFormateada = strftime($formatoFecha, $marcaTiempo);

    return $fechaFormateada;
}


function evaluar($val){
    return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
}


 function dia_format($date){
  $lbl       = '';
  $dias      = array('','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado','Domingo');
  $mes_array = ['','ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGO','SEP','OCT','NOV','DIC'];
  $dia_letra = $dias[date('N', strtotime($date))];

  $data =  separar_fecha($date);
  $mes  =  $mes_array[$data[1]];
  $lbl  =  $dia_letra.', '.$data[2].' de '.$mes.' del '.$data[0];

  return $lbl;
 }

function separar_fecha ($date){

    $y = date("Y", strtotime("$date"));
    $m = date("m", strtotime("$date"));
    $d = date("d", strtotime("$date"));

    $array = array($y,$m,$d );
    return $array;
}
#-- funciones --
echo json_encode($encode);

?>