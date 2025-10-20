<?php
if (empty($_POST['opc'])) {
    exit(0);
}
header("Access-Control-Allow-Origin: *"); // Permite solicitudes de cualquier origen
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type"); // Encabezados permitidos

setlocale(LC_TIME, 'es_ES.UTF-8'); // Configura el idioma a español

// incluir tu modelo
require_once '../mdl/mdl-punto-de-venta.php';
require_once '../../conf/_Utileria.php';
require_once '../../conf/coffeSoft.php';


class ctrl extends Ventas{

    public function lsVentas() {

        # Declarar variables
        $__row = [];

        // #Consultar a la base de datos
        if($_POST['status'] == 0) {
            $ls = $this->list_ticketALL([$_POST['fi'],$_POST['ff']]);
        }else{
            $ls = $this->list_ticket([ $_POST['status'],$_POST['fi'], $_POST['ff']]);
        }

        foreach ($ls as $key) {

            $a = [
                [
                    'text'    => 'Ver nota',
                    'icon'    => 'icon-shop',
                    'onclick' => "ventas.onShowTicket({$key['id']})",
                    'color'   => 'success'
                ]
            ];

            if ($key['id_Estado'] == 1) {
                $a[] = [
                    'text'    => 'Cancelar',
                    'icon'    => 'icon-block-1',
                    'onclick' => "ventas.removeItem({$key['id']})",
                    'color'   => 'danger'
                ];
            }


       

            $__row[] = array(
                
                'id'         => $key['id'],
                'Folio'      => $key['id'],
                'Fecha'      => formatSpanishDateAll($key['fecha']),
                'Cliente'    => $key['Name_Cliente'],
                'Total'      => evaluar($key['Total']),
                'F. de Pago' => ico_tipo_pago($key['Mixto'],$key['Efectivo'],$key['TDC'],$key['Credito']),
                'Factura'    => ico_factura($key['idFactura']),
                'Estado'     => Status($key['id_Estado']),
                'dropdown'          => $a,
            );
        }

        #encapsular datos
        return [

            "thead" => '',
            "row" => $__row,
        ];

    }

    function onShowTicket(){
        # Variables
        $__row = [];
        $total = 0;

        # Lista de productos 
        $ls    = $this -> getProductsBY([$_POST['NoFolio'] ]);
        
        
        foreach ($ls as $key) {
            
            $__row[] = array(

                'id'           => $key['id'],
                'item'         => $key['NombreProducto'],
                'presentacion' => $key['presentacion'],
                'Cant.'        => $key['cantidad'],

                'costo'    => evaluar($key['costo']),
                'total'    => evaluar($key['total']),
                'Especial' => $key['Especial'],
                'opc'      => 0
            
            );


            $total += $key['total'];
        
        }// end lista de folios


        // head and foot

        $lsFolio  = $this -> getListFolioByID(  [  $_POST['NoFolio']  ]  ) ;

        $head = [

            'data' => [

                    'folio'   => $_POST['NoFolio'],
                    'title'   => 'Venta',
                    'date'    => '',
                    'cliente' => $lsFolio['Name_Cliente'],
            
                ]
        ];

        $foot = ticket_foot ( [
            'id'       => $id,
            'total'    => $total,
            'Efectivo' => $lsFolio['Efectivo'],
            'TDC'      => $lsFolio['TDC'],
            'Credito'  => $lsFolio['Credito'],
            'Mixto'    => $lsFolio['Mixto'],
           
        ]);


        // Encapsular arreglos
        return ['head'=> $head, "thead" => '',"row" => $__row ,'frm_foot'=>$foot];
    }

    function addFile(){
        return $_POST;
    }

    function cancelTicket(){
      

        $success = $this->update_ticket([2, $_POST['id']]);

        return [
            'success' => $success,
            'POST'    => $_POST
        ];
    }




}

// Instancia del objeto

$obj = new ctrl();
$fn = $_POST['opc'];
$encode = $obj->$fn();

// Complement.
function ticket_foot($data){

    $pago = '';
   // Evaluar el método de pago
    if ($data['Mixto'] == 1) {
        $pago = 'MIXTO ';
    }
    else if ($data['Efectivo']) {
        $pago = 'EFECTIVO';
    } elseif ($data['TDC']) {
        $pago = 'TARJETA DE CRÉDITO (TDC)';
    } elseif ($data['Credito']) {
        $pago = 'CRÉDITO';
    } 


    $foot = '
    <div class="row">
    <div class="col-12 text-end">
    <strong>TOTAL:  '.evaluar($data['total']).' </strong>    
    </div>

    <div class="col-12 text-center fw-bold">  '.$pago.'</div>
    </div>';


    return $foot;
}



function Status($idEstado){

    switch ($idEstado) {
        case 1:
            return '<span class="bg-green-700 text-white text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">PAGADO</span>';
        case 2:
            return '<span class="bg-red-700 text-white text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">CANCELADO</span>';
           
        
        
      
    }
}


function ico_tipo_pago($Mixto, $Efectivo, $TDC, $Credito){
  $ico = '';
  if ($Mixto == 1) {
    $ico = ' Pago Mixto';
  } else {

    if ($Efectivo != 0) {
      $ico = 'Efectivo';
    } else if ($TDC != 0) {
      $ico = 'TDC';
    } else if ($Credito != 0) {
      $ico = 'Credito';
    }
  }
  // no Mixto

  return $ico;
}


function ico_factura($val){
  $ico = '<span class="ico-2x lnr lnr-checkmark-circle text-success"></span>';
  if ($val == null) {
    $ico = '';
  }
  return $ico;
}

echo json_encode($encode);
