<?php


if(empty($_POST['opc'])){
    exit(0);
}

require_once('../mdl/mdl-movimientos-diarios.php');
$cxc = new CxC;

require_once('../../conf/_Utileria.php');
$util = new Utileria;

$encode = [];

$objCxC = new CuentasPorCobrar($cxc);

switch ($_POST['opc']) {

    case 'lsCXC':
        $encode = lsCXC($cxc);
    break;

    
    case 'setCxC':
     $encode =   $objCxC ->setCxC($cxc);
    break;
    

    case 'editCxC':
           $encode = $objCxC -> getCxC();
    break;
 
}

echo json_encode($encode);



class CuentasPorCobrar {

    private $conn;
    public function __construct($obj){
        $this->conn = $obj;
    }

    function setCxC($obj){
        $util = new Utileria;

        $monto     = [];
        $getInsert = [];
        $getUpdate = [];
        $ls = $obj-> lsformas_pago();

        foreach ($ls as $key) {

            // Comprobar si existe forma de pago

            $ls   = $obj -> getListRevisionCxC([$_POST['id'],$key['id']]);

            if($ls){ // actualizar registro


                $dataUpdate = $util->sql([
                    'monto' => $_POST[$key['name']],
                    'idcxc' => $ls[0]['idcxc']
                ],1);

                $getUpdate[] = $obj->updateCxC($dataUpdate);


            }else{ // insertar registro


                if($_POST[$key['name']] !=''):

                $dataInsert = $util->sql([
                    'monto'                  => $_POST[$key['name']],
                    'id_bitacora_formaspago' => $_POST['id'],
                    'id_tipo_formaspago'     => $key['id']
                ]);

                $getInsert[] = $obj->addCxC($dataInsert);
                
                endif;

            }




            $monto[$key['name']] = $_POST[$key['name']];


        }


        return [
            'monto' => $monto,
            'data'  => $getUpdate,
            'data2' => $getInsert
 
        ];


    }

    function getCxC(){
        
        $ls = $this->conn ->lsformas_pago();
        $data = [];

        foreach ($ls as $key) {
            $montoRevision      = $this->conn  -> getListRevisionCxC([$_POST['idFP_Bitacora'],$key['id']]);
            $data[$key['name']] = $montoRevision[0]['monto'];
        }

        return $data;

    }


    function listCuentasPorCobrar(){

    }

  

}

// Funciones :

function lsCXC($obj){
    # Declarar variables
    $__row   = [];

    $fecha      = $_POST['fecha'];

    $fechaEntera = strtotime($fecha);
    $anio        = date("Y", $fechaEntera);
    $mes         = date("m", $fechaEntera);
    $dia         = date("d", $fechaEntera);

    $fi = $anio.'-'.$mes.'-01';
    $ff = $anio.'-'.$mes.'-'.$dia;

    # Consultar a la base de datos
    $ls    = $obj->Ver_Folio([$fi,$ff]);

    $lsfp  = $obj->tipoFormasPago();
   
     
    foreach ($ls as $key) {


      #Recorrido por movimientos
      $ls_bitacora = $obj->ver_bitacora_ventas(array($key['id']));

        foreach ($ls_bitacora as $_key) {


           $lsFormasPago = $obj-> bitacora_formas_pago(array($_key['idVentasBit']));


            foreach ($lsFormasPago as $__key ):

                $btn    = [];
                $btn[]  = [
                    'onclick' => "definirFormaPago(".$__key['idFP_Bitacora'].",event)",
                    'class'   => 'btn btn-primary btn-sm',
                    'html'    => '<i class="icon-pencil"></i> Forma de pago',
                ];


                $col = [];
                
                foreach ($lsfp as $key ) {

                    $montoRevision      = $obj -> getListRevisionCxC([$__key['idFP_Bitacora'],$key['id']]);
                    $col[$key['name']]  = evaluar($montoRevision[0]['monto']);

                }
                $col['Observaciones'] = $__key['Observaciones'];
                $col['a'] = $btn;
                
                
                // if($__key['Monto'] != 0)
                $row = [];
                
                $row= array(
                    'id'            => $key['id'],
                    'Folio'         => $__key['idFP_Bitacora'],
                    'Fecha'         => formatSpanishDate($key['Fecha']),
                    'Sub Categoria' => $_key['Subcategoria'],
                    'CxC'           => evaluar($__key['Monto']),
                   
                );
                
                $__row[] = array_merge($row,$col);
            endforeach;    
            
           
        }
   



    }
    
    #encapsular datos

    return [
        "thead" => '',
        "row"   => $__row
    ];
}

// Complementos :

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

?>