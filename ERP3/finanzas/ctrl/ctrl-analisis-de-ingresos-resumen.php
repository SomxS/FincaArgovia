<?php
if (empty($_POST['opc']))
    exit(0);

// $obj = new Analisisdeingresos;
require_once '../mdl/mdl-analisis-de-ingresos.php';
//incluir libreria coffeSoft 
require_once('../../conf/coffeSoft.php');

// sustituir 'mdl' extends de acuerdo al nombre que tiene el modelo
class ctrl extends Ingresos{

    function lsResumenOperativo(){
        $mes       = $_POST['mesResumen'];
        $mesLetra  = $_POST['mesLetra'];
        $days      = cal_days_in_month(CAL_GREGORIAN, $mes, 2025);
        $ls        = $this->lsCategorias([1]);
        $totalGral = 0;


        // Simulacion.
        $ingresosCategoria = [];
         foreach ($ls as $_key) {

             $monto      = $this->__DESGLOZE_MENSUAL($_key['id'], $mes, 2025);
            

            //  $total      = $monto['total'];

            $sin_iva    = $this->getIngresosIVA(['id'=>  $_key['id'], 'total' => $monto['total']]);

            $total = $sin_iva;
            $totalGral += $total;

             $ingresosCategoria[] = array(

                'id'        => $_key['id'],
                'Categoria' => $_key['valor'],
                
                'valor'     => [
                
                    'text' => evaluar($sin_iva) ,
                    'val'  => $sin_iva
                
                ],
                
                'promed'    => '',
                'opc'       => 0

            );

        }

        foreach ($ingresosCategoria as $key => $item) {

            $m = ($ingresosCategoria[$key]['valor']['val'] / $totalGral)*100;

            
            $ingresosCategoria[$key]['promed'] = evaluar($m,'%');
        }


        $Hospedaje = array_filter($ingresosCategoria, function($ingreso) {
            return $ingreso['id'] == 1;
        });



        # Declarar variables
        $__row = [];

        $consultas = array(

            'Dias'                => 'dias',
            'No.Cuartos'          => 'NoCuartos',
            'Cuartos Ocupados'    => 'totalAyB',
            'Cuartos Disponibles' => 'totalAyB',
            '% de Ocupación'      => 'totalDiversos',
            'ADR Promedio'        => 'totalDiversos',
            'RevPar Promedio'     => 'totalDiversos',
        
        );

        $lsCategorias    = $this -> Select_Subcategoria_x_grupo([1,1]);
        $cuartosOcupados = $this -> lsCuartosOcupados([1,2025,$mes]);


        $NoCuartos     = count($lsCategorias) -1;
        $CuartosDisponibles = $days * $NoCuartos;

        $porcOcupacion = ($cuartosOcupados['cuartosOcupados']/$CuartosDisponibles) * 100;

        $totalHospedaje = $Hospedaje[0]['valor']['val'];


        $__row = [

            array( 'id' => $key, 'concepto' => 'Dias',                'total'   => $days, 'porc'=>'','opc' => 0),
            array( 'id' => $key, 'concepto' => 'No.Cuartos',          'total'   => $NoCuartos, 'porc'=>'','opc' => 0),
            array( 'id' => $key, 'concepto' => 'Cuartos Ocupados',    'total'   => $cuartosOcupados['cuartosOcupados'],'porc'=>'', 'opc' => 0),
            array( 'id' => $key, 'concepto' => 'Cuartos Disponibles', 'total'   => $CuartosDisponibles,'porc'=>'', 'opc' => 0),
            
            array( 'id' => $key, 'concepto' => '% Ocupacion',     'total'   => evaluar($porcOcupacion,'%') , 'porc' => '' , 'opc' => 0),
            
            array( 
            
                'id'       => $key,
                'concepto' => 'ADR Promedio',
                'total'    => evaluar($totalHospedaje / $cuartosOcupados['cuartosOcupados']),
                'porc'     => '',                                                             
                'opc' => 0

            ),


            array( 'id' => $key, 
            'concepto' => 'RevPar Promedio', 
            'total'   => evaluar($totalHospedaje / $CuartosDisponibles),
            'porc'=>'','opc' => 0),
        ];

        // despliegue por categoria.

        
    
        $__row[] = array(

        'id'        => 0,
        'Categoria' => '',
        'valor'     => '',
        'porc'      => '',
        'opc'       => 1

        );

        $__row = array_merge($__row,$ingresosCategoria);

 
        
        
          $__row[] = array(
                
                'id'        => 0,
                'Categoria' => ' TOTAL DE INGRESOS (*antes de impuestos ) ',
                'valor'     => evaluar($totalGral ),
                'porc'      => '',
                'opc'       => 2

            );

     


        #encapsular datos
        return [

            "thead" => ['Concepto','Total','%'],
            "row"   => $__row,
        ];
        

    }

    function getIngresosIVA($data){

        if ($data['id'] == 1):

            return $data['total'] / 1.18;

        elseif ($data['id'] == 9):

            return $data['total'];
        else:

            return $data['total'] / 1.16;
        endif;

    }


    public function __DESGLOZE_MENSUAL($idCategoria, $mes, $anio){
        $frm = '';
        $total_mensual = 0;

        $sub = $this->lsGrupo(array($idCategoria));

        foreach ($sub as $_KEY) {

            $sql = $this->Select_Subcategoria_x_grupo([$idCategoria, $_KEY['idgrupo']]);
            $fpago = $this->lsFormasPago([1]);

            foreach ($sql as $value) { // vista de subcategorias

                // $total = 0;
                $monto = $this->getMontoMensual([$value['idSubcategoria'], $mes, $anio]);
                $total = $total + $monto['total'];



            }

        }

        return array('frm' => $frm, 'total' => $total);
    }


}


// Instancia del objeto

$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);