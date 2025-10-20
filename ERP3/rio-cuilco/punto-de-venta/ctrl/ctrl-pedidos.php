<?php
if (empty($_POST['opc'])) {
    exit(0);
}

// incluir tu modelo
require_once '../mdl/mdl-pedidos.php';
require_once '../../../conf/coffeSoft.php';

// sustituir 'mdl' extends de acuerdo al nombre que tiene el modelo
class ctrl extends Pedidos{
    
    
    function initComponents(){
        $lsCategory =$this->listCategory();
        $lsFolio =$this->lsFolio();
        $lsProducts = $this -> lsProducts();
        
        return [
            
            'lsCategory' => $lsCategory,
            'lsFolio'    => $lsFolio,
            'lsClientes' => $this->listClientes(),
            'lsProducts' => $lsProducts,
        
        ];
    }
  
    function HistorialPedidos(){
        $fi      = $_POST['date1'];
        $ff      = $_POST['date2'];

        $__row = [];

        $list = $this ->VerFormatos([$fi,$ff]);


        foreach ($list as $key) {

            $btn   = [];
            $btn[] = [
            "fn"    => 'historial.openTicket',
            "color" => 'secondary btn-sm',
            "icon"  => 'icon-truck-1',
            ];

            $btn[] = [
            "fn"    => 'CancelarFolio',
            "color" => 'danger btn-sm',
            "icon"  => '  icon-trash',
            ];

            $sql_producto = $this ->row_data([$key['idLista']]);

            $__row[] = [

                'id'            => $key['idLista'],
                'Folio'         => $key['folio'],
                'Lugar/Destino' => $key['NombreCliente'],
                'Fecha'         => formatSpanishDate($key['fecha']),
                
                'No. Prod.'     => count($sql_producto),
                'Estado'        => estado($key['id_Estado']),

                    // 'Hr'      => $key['hora'],
                'btn' => $btn,
            
            ];
            
        }


        // Encapsular arreglos
        return [
            "thead" => '',
            "row" => $__row,
        ];


    }

    function openTicket(){
        
        $__row = [];
        $idFolio = $_POST['id'];


        $sql_producto = $this->row_data([$_POST['id']]);

        $total = 0;


        foreach ($sql_producto as $_key) {

            $btn = [];

            // if($totalComplementos)
            $btn[] = [
                "fn"    => 'verComplementos('.$id.')',
                "color" => 'warning ',
                "icon"  => 'icon-star',
                // "text"  => '('.$totalComplementos.')'
            ]; 

            $btn[] = [
                "fn"    => 'desactivarProducto('.$id.')',
                "color" => 'success ',
                "estado"=> 0,
                "icon"  => 'icon-toggle-on',
            ];

            $total += $_key['total'];

            $__row[] = array(
                'id'                => $_key['idListaProductos'],
                'NombreProducto'    => $_key['NombreProducto'],
                'cantidad'          => $_key['cantidad'],
                'costo'             => evaluar($_key['costo']),
                'total'             => evaluar($_key['total']),
                "btn_personalizado" => $btn
            );

        }


        $foot = ticket_foot([  'total' => $total ]);    
        $header = ticket_head([  'total' => $total ]);    


        return [
            'frm_head' => $header,
            'thead'    => '',
            'row'      => $__row,
            'frm_foot' => $foot
        ];

    }

    function subGrupo(){

       return  $this -> listSubcategory([$_POST['idgrupo']]);

    }

    function ls(){
        $usuario  = $_COOKIE['IDU']; // 6

        $__row      = [];
        $anioActual = date("Y");

        $responsabilidad = $this -> getResponsabilidad([$usuario]);
        $events          = $this -> lsEvents([]);
        
        foreach($events as $key ):


            $usr    = ($key['usr_creator'] == $responsabilidad['idUser']) ? 'CREADOR' : 'NO CREADOR';

            $intervalDate = isIntervalDate($_POST['date1'], $_POST['date2'], $finished);

            //     if($key['usr_creator'] == $responsabilidad['idUser']):   // es usuario creador.

                switch($key['id_Replay']){

                    case '1': // no-repat

                    case '2': // daily

                    case '3': // weekly

                    // Algun date finished tiene la semana de data1 y date2

                    
                    case '4': // monthly -> return [] fechas

                    case '5': // yearly

                        $anio = date("Y", strtotime( $finished ));

                    case '6': // custom

                }


                $isCreated  =  $this -> useEventStatus([$key['idEvent']]);     

            
            $__row[] = [

                'id'       => 1,
                
                'title'    => $key['title'],
                'Creador'  => $usr,
                'Replay'   => $key['frecuency'].' ->'.$key['id_Replay'],
                'entre'    =>$key['date_start'] . ' - ' . $key['date_end'],
                // 'timer'    => ['text' => $intervalDate, 'class' => 'bg-warning'],
                // 'idEvent'  => $key['idEvent'],
                // 'entre'    => $_POST['date1'] . ' - ' . $_POST['date2'],
                
                'finished' => $isCreated[0]['date_finished'],
                'opc'      => 0,
                
            ];

        
        endforeach;



        // foreach($events as $key ):

            
            
        //     $custom = $key['custom'];


        //     if($custom) { // Se repite {custom != 0}.
                
        //         $until = $key['until'];
        //         $anio = date("Y", strtotime($until));

                
        //         if( $anio >= $anioActual){ //  fecha de until es mayor o igual al año.
                    
        //           


        //            if($isCreated): // encontro el evento en la tabla status.

        //             // tipo de repeticion.

        //             $finished = $isCreated[0]['date_finished'];


                









                
        //            endif;






        //         }
            
                

            
            
        //     }else{


        //     }








        //    endif;   



        //         $__row[] = [
        //         'id' => 1,
        //         'title' => $key['title'],
        //         'timer' => ['text' => $intervalDate, 'class' => 'bg-warning'],
        //         'idEvent' => $key['idEvent'],
        //         'Creador' => $key['usser'],
        //         'entre' => $_POST['date1'] . ' - ' . $_POST['date2'],
        //         'finished' => $finished,
        //         'idReplay' => $key['id_Replay'],
        //         'opc' => 0,
        //         ];



        //         // $__row[] = [
        //         //     'id'         => 1,
                    
        //         //     'title'     => $key['title'],
        //         //     'idEvent'   => $key['idEvent'],
        //         //     'Creador'   => $key['usser'],
        //         //     'idCreator' => $usr,
        //         //     'custom'    => $custom,
        //         //     'until'     => $until.'>'.$anio,
                    
        //         //     'Nombre'     => $key['Nombres'],
        //         //     'finished'   => $key['finished'],
        //         //     'initiation' => $key['initiation'],
                    
        //         //     'status'     => $responsabilidad['usser'],
        //         //     'Employe'    => $key['id_Employe'],
        //         //     'rrhh'       => $responsabilidad['usr_empleado'],

        //         //     'repeat'    => $key['id_Replay'],
        //         //     'finishedx' => $finished,
                    
        //         //     'isCreated' => ['text' => isset( $isCreated ), 'class' => 'bg-info'],
                    
        //         //     'opc'       => 0,
        //         // ];

            
        

        // endforeach;    








        $title = " <strong> Consultando : </strong>   {$_POST['iptCalendar']} /  <b> USR</b> : {$usuario} "  ;

        return [
            'frm_head' => $title,
            'thead' => '',
            'row'   => $__row
        ];

    }

    function getEventsDate($array){
      // r. [fi:'',ff:'',custom:1,type:'month',interval: '1',until:''] * caducidad

        $list = [
        'fi'           => '2023-08-04',
        'ff'           => '2023-08-08',


        'interval'     => '1',
        'custom'       => '1',
        'type'         => 'yearly',
        'until'        => '2025-01-01',
        'name'         => 'Cumpleaños',
        
        'id_Categoria' => 2,
        'idcreador'    => '1',// adelita

        'assign'       => array(
            array(
                'idEmpleado' => '3',
                'name'       => 'Juan perez',
                'idUser'     => '3',
                'idStatus'   => '1'            //
            ),
            array(
                'id'   => '2',
                'name' => 'Maria Lopez',
                'idUser' => '2',
                'idStatus' => '2'
            )
        ),
        'stados'   => array(
            array(
                'id' => '1',
                'name' => 'Revisado',
                'date_ocurrence' => '2023-08-04',
                'date_finished' => '2023-08-05',
            )
        ) ,
        
        

        'fi'       => '2023-08-05',
        'ff'       => '2023-08-16',
        'interval' => '1',
        'custom'   => '1',
        'type'     => 'yearly',
        'until'    => '2024-12-01',
        'name'     => 'Platillos Nuevos',
        'id_Categoria' => 1,       
        'idcreador' => '3',
        'assign'   => array(
            array(
                'id'   => '1',
                'name' => 'Adelita Martinez'
            ),
            array(
                'id'   => '2',
                'name' => 'Maria Lopez'
            )
        ),
        'stados'   => array(
            array(
                'id' => '1',
                'name' => 'Revisado',
                'date_ocurrence' => '2023-08-04',
                'date_finished' => '2023-08-05',
            )
        ) ,
    ];


    $list = [
        [
            'fi'           => '2024-08-03',
            'ff'           => '2023-08-06',
            'interval'     => '1',
            'custom'       => '1',
            'type'         => 'yearly',
            'until'        => '2025-01-01',
            'name'         => 'Cumpleaños',
            'id_Categoria' => 2,
            'idcreador'    => '1',
        
        ],
        ['fi' => '2024-06-03'],
    ];


    return $list;
    }

  






}


// Complementos

function isIntervalDate($fecha, $inicio, $fin) {
    // Convertir las fechas a objetos DateTime
    $fechaObj = new DateTime($fecha);
    $inicioObj = new DateTime($inicio);
    $finObj = new DateTime($fin);

    // Verificar si la fecha está dentro del rango
    return ($fechaObj >= $inicioObj && $fechaObj <= $finObj);
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

function ticket_foot($data){
   return '
    <div class="row">
    <div class="col-12 text-end">
       <strong>TOTAL:  '.evaluar($data['total']).' </strong>    
    </div>
    </div>';


  
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

// Instancia del objeto 

$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);
