<?php
   if ( empty($_POST['opc']) ) {
     exit(0);
   }

   require_once('../mdl/mdl-remisiones.php');
   $obj = new mdl();

   $encode = [];

   switch ($_POST['opc']) {
    case 'lsFolios':
        $th     = ['Fecha','Numero de pedidos',''];   
        $encode = ls($obj,$th);
    break;
    case 'ver-ticket':
        $th     = ['Producto','Cantidad'];
        $encode = ticket($obj,$th);
    break;
    
    case 'ver-pedidos':
        $encode = verPedidos($obj);
    break;
    
    case 'ver-ticket-cliente': 
        $encode = lsTicket($obj);
    break;    
   } 

#----------------------
# Funciones y metodos
#----------------------

function ls($obj,$th){
    # Declarar variables
    $__row   = [];

    $fi = $_POST['fi'];
    $ff = $_POST['ff'];

    # Consultar a la base de datos
    $ls    = $obj->lsFolio([$fi,$ff]);
    $cont = count($ls);

        foreach ($ls as $key) {
            $btn = [];

            $btn[] = [
               "icon"  => "icon-table",
               "color" => "danger",
               "fn"    => "VerPedidos"
            ];

            $btn[] = [
                "icon"  => "icon-truck-1",
                "color" => "primary",
                "fn"    => "verTicket"
            ];


            $__row[] = array(
            'id'        => $key['idCierre'],
            'name'      => formatSpanishDate($key['FechaCierre']),
            'NoPedidos' => $key['NoPedidos'],
            "btn"  => $btn
            );
        }


    #encapsular datos
        $encode = [
            "thead" => $th,
            "row" => $__row,
            "cont" => $cont
        ];

    return $encode;  

}

function ticket($obj,$th){

  
   # Lista de productos 
   $sql_producto = $obj->row_data([$_POST['id']]);
   $__row        = [];

   foreach ($sql_producto as $_key) {

    $id = $_key['idListaProductos'];
   
   
    $__row[] = array(
        'id'       => $_key['idCierre'],
        'folio'    => '<span class="text-uppercase">'.$_key['NombreProducto'].'</span>',
        'cantidad' => $_key['cantidad'],
        // 'idLista'  => $_key['idLista'],
        'opc'      => 0,

    );

   }


   /* Consultar informacion del ticket */
    $lsFolio    =  $obj->lsDataTicket([$_POST['id']]);

    foreach ($lsFolio as $key) ;
        
        $objs = ticket_head(
            [
                'fecha'   => formatSpanishDate($key['FechaCierre']),
                'titulo'  => 'HOJA DE CORTE ',
                'folio'   => $_POST['id'],
                'estado'  => $key['estado_pedido'],
            ]
        );

    /*--      end consulta   -- */ 

        
    // Encapsular arreglos
    $encode = [
        "thead"    => $th,
        "row"      => $__row,
        "frm_head" => $objs
    ];

    return $encode;
}

function verPedidos($obj){

    $__row = [];
    $th    = ['Folio','Cliente','Fecha',''];
    
    # Lista de productos 
    $ls = $obj->lsFoliosCierre([$_POST['id']]);

    // $__row = DynamicRow($ls);

    foreach ($ls as $_key) {

        $btn = [];

        $btn[] = [
            "icon"  => "icon-eye",
            "color" => "primary",
            "fn"    => "ver_tickets"
        ];

        $__row[] = array(
        'id'    => $_key['idLista'],
        'idx'   => $_key['idLista'],
        'name'  => $_key['NombreCliente'],
        'FechaDelDia' => $_key['fecha'],
        'btn'   => $btn,

        );
    }

    // Encapsular arreglos
   return  [
        "thead"    => $th,
        "row"      => $__row,
    ];

   
}

function ticket_head($data){


    $div = '
    <div class="row mt-2">
    <div class="col-sm-12 ">
    <a class="btn btn-outline-primary" onclick="print_formato()" ><i class="icon-print"></i>Imprimir </a>
    </div>
    
    </div>

     <div class="table-responsive mt-3 " id="content-rpt">
     <table style="font-size:.6em; " class="table table-bordered table-sm">
     
    <tr>
        <td class="col-sm-1 fw-bold">FECHA: </td>
        <td class="col-sm-2">'.$data['fecha'].'</td>
        <td class="col-sm-6 text-center"> <strong>'.$data['titulo'].'</strong> </td>
        <td class="col-sm-1 fw-bold"> FOLIO:  </td>
        <td class="col-sm-2 text-right text-end text-danger fw-bold">'.Folio($data['folio'],'R').'</td>
    </tr>

    <tr>
        <td class="col-sm-1 fw-bold"> ESTADO:   </td>
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
 
function lsTicket($obj){
    # Variables
    $idFolio      = $_POST['id'];
    $__row        = [];

    
    $ls = $obj->row_data_cliente([$idFolio]);
   
    foreach ($ls as $_key) {
        /* Crear boton */ 
        $btn = [];

        $btn[] = [
            "fn"    => 'desactivarProducto('.$_key['id'].')',
            "color" => 'success ',
            "estado"=> 0,
            "icon"  => 'icon-toggle-on',
        ];

        $__row[] = array(
            'id'                => $_key['id'],
            'Producto'          => $_key['NombreProducto'],
            'unidad'            => $_key['Unidad'],
            'cantidad'          => $_key['cantidad'],
            'costo'             => evaluar($_key['costo']),
            'total'             => evaluar($_key['total']),
            'btn'               => $btn
        );

    }// end lista de folios


    /* Consultar informacion del ticket */ 

    $lsFolio  = $obj -> ver_folio([$idFolio]);
    foreach ($lsFolio as $key) ;

    $objs = ticket_head_breadcumbs(
        ['fecha'  =>$key['f'],
        'titulo'  =>'NOTA DE VENTA',
        'folio'   => $key['idLista'],
        'cliente' => 'Somx',
        'estado'  => estado($key['id_Estado']),
        ]
    );

    /*--      end consulta   -- */ 

    // Encapsular arreglos
    return [
    "thead"     => '',
    "row"       => $__row,
    "frm_head"  => $objs
    ];

    
}

#----------------------
# Auxiliares
#----------------------
function ticket_head_breadcumbs($data){

    return "
    <div class='row'>
        <div class='col-sm-12 mt-3'>

        <nav aria-label='breadcrumb'>
            <ol class='breadcrumb'>
        
            <li class='breadcrumb-item  text-muted pointer'>
            <i class='icon-left-circle'></i> Regresar </li>
            <li class='breadcrumb-item fw-bold active'>Nota: {$data['nota']}</li>
            </ol>
        </nav>
        
        </div>
    </div>

    ";


//     $div = '
//     <div class="row mt-2">
//     <div class="col-sm-12 ">
//     <a class="btn btn-outline-primary" onclick="print_formato()" ><i class="icon-print"></i>Imprimir </a>
//     </div>
    
//     </div>

//      <div class="table-responsive mt-3 " id="content-rpt">
//      <table style="font-size:.6em; " class="table table-bordered table-sm">
     
//     <tr>
//         <td class="col-sm-1 fw-bold">FECHA: </td>
//         <td class="col-sm-2">'.$data['fecha'].'</td>
//         <td class="col-sm-6 text-center"> <strong>'.$data['titulo'].'</strong> </td>
//         <td class="col-sm-1 fw-bold"> FOLIO:  </td>
//         <td class="col-sm-2 text-right text-end text-danger fw-bold">'.Folio($data['folio'],'R').'</td>
//     </tr>

//     <tr>
//         <td class="col-sm-1 fw-bold"> ESTADO:   </td>
//         <td class="col-sm-2">'.$data['estado'].'</td>
//         <td class="col-sm-6"></td>
//         <td class="col-sm-1 fw-bold"></td>
//         <td class="col-sm-2">    </td>
//     </tr>
      
//       </table>
//       </div>
    
//    ' ;

    return $div;
}



#----------------------
# Complementos
#----------------------
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

function DynamicRow($ls) {
    $__row = [];
    foreach ($ls as $fila) {

        foreach ($fila as $key => $valor) {
            
            $fila[$key] = $valor;
        }

        $fila['opc'] = 0;

        $__row[] = $fila;
    }
    return $__row;
}

function Folio($Folio, $Area) {
    $Folio = (int)$Folio; // Convertir a entero por si acaso

    // Asegurarse de que el folio tenga al menos 4 dígitos
    $Folio = str_pad($Folio, 4, '0', STR_PAD_LEFT);

    $NewFolio = $Area .'-'. $Folio;
    return $NewFolio;
}
#----------------------
# Enpaquetar
#----------------------
 echo json_encode($encode);
?>    