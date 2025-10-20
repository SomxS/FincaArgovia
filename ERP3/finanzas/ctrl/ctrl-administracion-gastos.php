<?php
if (empty($_POST['opc']))
    exit(0);

// incluir modelo
require_once('../mdl/mdl-administracion.php');

//incluir libreria coffeSoft 
require_once('../../conf/coffeSoft.php');
require_once('../../conf/_Utileria.php');

class ctrl extends Gastos{
      public $util;

    public function __construct() {
        parent::__construct(); // Llama al constructor de la clase padre
        $this->util = new Utileria(); 
    }

    function lsGastos(){

        # Declarar variables
        $__row = [];
        $date  = $_POST['iptDate'];
        // $date  = '2024-06-30';

        $fi = $_POST['fi'];
        $ff = $_POST['ff'];


        #Consultar a la base de datos
        $ls = $this->listGastos([$fi,$ff]);

        foreach ($ls as $key) {

            $btn   = [];

            $btn = array( [
                
                'color' => 'danger',
                'fn'    => "gastos.quitarGasto({$key['id']},event)",
                'icon'  => 'icon-trash'

            ] ,

            [
                
                'color' => 'primary',
                'fn'    => "gastos.editarGasto({$key['id']})",
                'icon'  => 'icon-pencil'

            ]
        );
         

          $__row[] = array(

            'id'                => $key['id'],
            'fecha'             => formatSpanishDate($key['fechaGasto']),
            'proveedor'         => $key['Name_Proveedor'],
            'Monto'             => evaluar($key['Monto']),
            'Descripcion'       => $key['descripcion'],
            'btn_personalizado' => $btn

          );


        }



        #encapsular datos
        return [

            "thead" => '',
            "row" => $__row,
        ];

    }

    function setGastos(){

        $data = $this -> util :: sql($_POST);
        $ok   = $this -> insertGastos($data);

        return ['ok' => $ok];

    }

    function quitarGasto(){

        $id = $_POST['id'];

        $data = $this -> util :: sql($_POST,1);

        $ok = $this->removeGasto($data);

        return ['ok' => $ok,'data'=> $data];

    }

    function getGasto(){

        return $this -> getGastoID([$_POST['id']])[0];

    }

    function editGasto(){

        $values = [

            // 'fechaGasto'   => $_POST['fechaGasto_edit'],
            'id_proveedor' => $_POST['id_proveedorEdit'],
            'Monto'        => $_POST['Monto'],
            'descripcion'  => $_POST['descripcion'],
            'idgastos'     => $_POST['idGastos']

        ];

        $data = $this -> util :: sql($values,1);
        $ok   = $this -> updateGasto($data);

        return  [
            'data' => $data,
            'ok'   => $ok
        ];
        

    }


    function lsCxC(){
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
        $ls_folios = $this :: listFolios([$fi,$ff]);
        $ls_fp     = $this :: tipoFormasPago();
   


        foreach ($ls_folios as $key) {
            
            #Recorrido por movimientos
            $ls_bitacora = $this->ver_bitacora_ventas(array($key['id']));

            foreach ($ls_bitacora as $_key) {

                $lsFormasPago = $this-> bitacora_formas_pago(array($_key['idVentasBit']));

                foreach ($lsFormasPago as $__key ):
                    $col = [];

                    // foreach ($ls_fp as $fp ) {

                    //     $montoRevision      = $this -> getListRevisionCxC([$__key['idFP_Bitacora'],$fp['id']]);
                    //     $col[$fp['name']]  = [
                    //         'html'  => evaluar($montoRevision[0]['monto']),
                    //         'class' => 'text-end bg-warning-1'
                    //     ] ;
                    // }
                    
                    $col['opc'] = 0;
                     
                    $row = []; 
                    $row = array(
                        
                        'id'                => $key['id'],
                        'Folio'             => $_key['idVentasBit'],
                        'Fecha del importe' => formatSpanishDateAll($key['Fecha']),
                        'Concepto'     => $_key['Subcategoria'],
                        'CxC'               => evaluar($__key['Monto']),
                        'Observaciones'     => '',
                        'Se pago en'           => statusPago($__key['nombreFormasPago']),
                     
                    );

                      $__row[] = array_merge($row,$col);

                endforeach;


            }

        }


        // Encapsular arreglos
        return [
            "thead"    => '',
            "row"      => $__row,
            "frm_head" => ''
        ];
    }


}

function statusPago($status) {
    $status = strtoupper(trim($status));

    $baseClass = 'inline-flex items-center gap-2 justify-center min-w-[130px] px-2 py-1 rounded text-xs font-semibold';

    switch ($status) {
        case 'EFECTIVO':
            return '<span class="' . $baseClass . ' bg-green-100 text-green-800">
                        <i class="icon-money text-green-600"></i> Efectivo
                    </span>';
        case 'TC':
            return '<span class="' . $baseClass . ' bg-blue-100 text-blue-800">
                        <i class="icon-credit-card text-blue-600"></i> TC
                    </span>';
        case 'ANTICIPO':
            return '<span class="' . $baseClass . ' bg-purple-100 text-purple-800">
                        <i class="icon-forward text-purple-600"></i> Anticipo
                    </span>';
        case 'CORTESIA':
            return '<span class="' . $baseClass . ' bg-pink-100 text-pink-800">
                        <i class="icon-gift text-pink-600"></i> Cortesía
                    </span>';
        case 'SIN ESPECIFICAR':
        default:
            return '<span class="' . $baseClass . ' bg-gray-200 text-gray-700">
                        <i class="icon-help text-gray-600"></i> Pendiente 
                    </span>';
    }
}






// Instancia del objeto

$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);