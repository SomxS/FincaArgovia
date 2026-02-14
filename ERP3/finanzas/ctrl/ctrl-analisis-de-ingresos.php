<?php
if (empty($_POST['opc'])) {
    exit(0);
}

// $obj = new Analisisdeingresos;
require_once '../mdl/mdl-analisis-de-ingresos.php';

class ctrl extends Ingresos{

    function initComponents(){
        $categorias = $this ->lsCategorias([1]);
        
        return [
            'categorias'  => $categorias,
         
        ];
    
    }

    public function lsCategoria(){
        # Declarar variables
        $__row = [];
        $fi = $_POST['fi'];
        $ff = $_POST['ff'];
        $id = $_POST['id'];
        #Consulta de grupos
        $grup = $this->lsGrupo([$_POST['id']]);
        # Consulta de formas de pago
        $fpago = $this->lsFormasPago([1]);

        foreach ($grup as $no => $key) {

            if ($key['gruponombre'] != 'SIN GRUPO') {
                $__row[] = array(
                    'id' => '',
                    'Categoria' => $key['gruponombre'],
                    'Ocup.' => '',
                    'Monto' => '',
                    'opc' => 1,
                );
            }

            # consulta de subGrupos
            $sub = $this::Select_Subcategoria_x_grupo([$_POST['id'], $key['idgrupo']]);

            foreach ($sub as $_key => $value) {

                $row = [];

                $tar = $this::Select_Total2($fi, $ff, $value['idSubcategoria']);

                # variables
                $paxID = $this::ExisteEnBitacora($fi, $ff, $value['idSubcategoria'], 'pax');
                $noID = $this::ExisteEnBitacora($fi, $ff, $value['idSubcategoria'], 'Noche');

                if ($tar) {
                    $row = array(
                        'id' => $value['idSubcategoria'],
                        'Categoria' => $value['Subcategoria'],
                        'Ocup.' => evaluar2($paxID),
                        'Monto' => evaluar($tar, '$'),
                    );
                }

                $ingresos['opc'] = 0;

                $__row[] = array_merge($row, $ingresos);

            }

        }

        #encapsular datos
        return [
            "frm_head" => $this::breadCrumb(),
            "thead" => $th,
            "row" => $__row,
            "foot" => 'hola',
        ];

    }

    public function ingresoMensual(){
        //
        $__row = [];

        // # Lista de productos
        $ls = $this->lsCategorias([1]);

        foreach ($ls as $_key) {

            $row = [];

            $row = array(
                'id' => $_key['id'],
                // 'idx' => $_key['id'],
                'Categoria' => $_key['valor'],

            );

            $mes = [];

            for ($m = 1; $m <= 12; $m++) {
                
                $mesLetra = $this->__MES($m);
                $Ingreso  = $this->__DESGLOZE_MENSUAL($_key['id'], $m, $_POST['Anio']);

                $mes[$mesLetra] = evaluar($Ingreso['total']);
            }

            $mes['opc'] = 0;

            $__row[] = array_merge($row, $mes);

        } // end lista de folios

        // Encapsular arreglos
        return [

            "thead"    => '',
            "row"      => $__row,
            "frm_head" => '',

        ];

    }

    public function chequePromedio() {
        # Variables

        $__row = [];
        $anio  = $_POST['Anio'];
        $idCategoria = $_POST['categoria'];

      
        // # Lista de productos
        $grupo = $this->lsGrupo(array($idCategoria));
        $totalCol   = [];
        $totalGral  = 0;

        foreach($grupo as $_KEY):


        $ls = $this->Select_Subcategoria_x_grupo([$idCategoria, $_KEY['idgrupo']]);
        $total = 0;
        foreach ($ls as $_key) {

            $row = [];
            $mes = [];

            $totalAcomulado = 0;

            for ($m = 1; $m <= 12; $m++) {

                $mesLetra = $this->__MES($m);

                $monto = $this->getMontoMensual([$_key['idSubcategoria'], $m, $anio]);
                $ocup  = $this->getChequePromedio([$_key['idSubcategoria'], $m, $anio]);
                
                
                $chequePromedio = ($monto['total'] !=0) ? ($monto['total'] / $ocup['pax']) : '';
             
                $totalAcomulado += $monto['total'];
                $mes['Total '.$mesLetra]      = evaluar($monto['total']);
                $mes['Ocup '.$mesLetra]       = $ocup['pax'];
                $mes['Cheque Prom. '.$mesLetra] = [
                    'html'  => evaluar($chequePromedio),
                    'class' => 'bg-default fw-bold text-end'
                ];

                 $totalCol['Total '.$mesLetra]          += $monto['total'];
                 $totalCol['Ocup '.$mesLetra]            += $ocup['pax'];
                 $totalCol['Cheque Prom. '.$mesLetra]    += $chequePromedio;
                
            }

            $totalGral += $totalAcomulado;

             $row = array(
                'id' => $_key['id'],

                'Categoria' => [
                    'html'  => $_key['Subcategoria'],
                    'class' => 'fw-bold bg-default'
                ],

                'total Acomul.' => [
                'html'  => evaluar($totalAcomulado),
                'class' => 'fw-bold text-end',
                'style' => 'font-size: 12px'
                ],



            );

         

           

            $mes['opc'] = 0;

            $__row[] = array_merge($row, $mes);

        } // end lista de folios


        endforeach;

        $endCol = [];
        $endRow = [];

         $endRow = array(
                'id' => '',

                'Categoria' => 'Total',

                'total Acomul.' => evaluar($totalGral)


            );

        for ($m = 1; $m <= 12; $m++) {

             $mesLetra = $this->__MES($m);

              $endCol['Total '.$mesLetra] = evaluar($totalCol['Total '.$mesLetra]);
              $endCol['Ocup '.$mesLetra] = $totalCol['Ocup '.$mesLetra];
              $endCol['Cheque Prom. '.$mesLetra] = evaluar($totalCol['Cheque Prom. '.$mesLetra]);



        }

         $endCol['opc'] = 0;

        $__row[] = array_merge($endRow, $endCol);





        // Encapsular arreglos
        return [
            "thead" => '',
            "row" => $__row,
        ];
    }

    public function __DESGLOZE_MENSUAL($idCategoria, $mes, $anio) {
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

                 $frm .= '/'.$monto['total'];

            }

        }

        return array('frm' => $frm, 'total' => $total);
    }

    public function __MES($mes){
        $m = '';
        switch ($mes) {
            case 1:$m = "Enero";
                break;
            case 2:$m = "Febrero";
                break;
            case 3:$m = "Marzo";
                break;
            case 5:$m = "Mayo";
                break;
            case 4:$m = "Abril";
                break;
            case 6:$m = "Junio";
                break;
            case 7:$m = "Julio";
                break;
            case 8:$m = "Agosto";
                break;
            case 9:$m = "Septiembre";
                break;
            case 10:$m = "Octubre";
                break;
            case 11:$m = "Noviembre";
                break;
            case 12:$m = "Diciembre";
                break;
        }

        return $m;
    }

    public function SubCategoria($idGrupo){

        $__row = [];

        # consulta de subGrupos
        $sub = $this::Select_Subcategoria_x_grupo([$_POST['id'], $idGrupo]);

        foreach ($sub as $_key => $value) {
            $row = [];

            $row = array(
                'id' => $value['idSubcategoria'],
                'Categoria' => $value['Subcategoria'],
                // 'Pax'       => evaluar2($paxID),
                // 'Noche' => evaluar2($noID),
                // 'Monto' => evaluar($tar,'$'),
            );

            $ingresos['opc'] = 0;

            $__row[] = array_merge($row, $ingresos);

        }

        return $__row;

    }

    public function breadCrumb(){

        return "
        <div class='row'>
        <div class='col-sm-12 mt-3'>

        <nav aria-label='breadcrumb'>
        <ol class='breadcrumb'>

        <li onclick='ConsultarIngresos()' class='breadcrumb-item  text-muted pointer'>
        <i  class='icon-left-circle'></i> Regresar </li>
        <li onclick='verCategoria({$_POST['id']})' class='breadcrumb-item fw-bold pointer active'> Consultar </li>
        </ol>
        </nav>

        </div>
        </div>

        ";

    }

    function getIngresosIVA($data){

        if($data['id'] == 1):
        
            return $data['total'] / 1.18;
        
        elseif($data['id'] == 9):
        
            return $data['total'];    
        else:
        
            return $data['total'] / 1.16;
        endif;

    }

    public function lsIngresos(){

        # Declarar variables
        $fi = $_POST['fi'];
        $ff = $_POST['ff'];

        $fic = $_POST['fechaComparativa1'];
        $ffc = $_POST['fechaComparativa2'];

        $__row = [];

        $list_categoria = $this->lsCategorias([1]);

        $total = 0;
        $totalComparativo = 0;
        $totalDif = 0;

        foreach ($list_categoria as $_key) {

            $ingreso_categoria   = $this->ver_ingresos_turismo([$fi, $ff, $_key['id']]);
            $ingreso_comparativo = $this->ver_ingresos_turismo([$fic, $ffc, $_key['id']]);
            $bgIva               = '';
            $bgHosp              = '';

            $subT = $ingreso_categoria;

            $dif = $ingreso_categoria - $ingreso_comparativo;
            $crecimiento = ($ingreso_comparativo != 0) ? ($dif * 100) / $ingreso_comparativo : 0;

         
            // Tablero de ingresos:
            // $anio_actual = $this :: getIngresosIVA(['id'=> $_key['id'] , 'total' => $ingreso_categoria   ]);
            // $anio_comparativo = $this :: getIngresosIVA(['id'=> $_key['id'], 'total' => $ingreso_comparativo   ]);

            
            $anio_actual = $ingreso_categoria;
            $anio_comparativo = $ingreso_comparativo;



            $dif = $anio_actual - $anio_comparativo;
            $crecimiento = ($anio_comparativo != 0) ? ($dif * 100) / $anio_comparativo : 0;
            
            $totalDif += $dif;

            $total += $anio_actual;
            $totalComparativo += $anio_comparativo;



            $__row[] = array(
                'id'           => $_key['id'],
                'Ingresos'     => '<a class="pointer" onclick="verCategoria(' . $_key['id'] . ')"><i class="text-info icon-eye"></i>' . $_key['Categoria'] . '</a> ',
                'Año actual'   => evaluar($anio_actual),
                "Año anterior" => evaluar($anio_comparativo, ''),
                "Diferencia"   => evaluar3($dif, '$'),
                "Crecimiento"  => evaluar3($crecimiento, '%'),
                "opc"          => 0,
            );

        }

        $__row[] = array(
            'id' => $_key['id'],
            'Ingresos' => 'TOTAL',
            'Año actual' => evaluar($total),
            "Año anterior" => evaluar($totalComparativo),
            "Diferencia" => evaluar($totalDif),
            "Crecimiento" => '',
            "opc" => 1,
        );

        // Encapsular arreglos
        return [
            'thead' => '',
            'row' => $__row,
        ];
    }

}

// Instancia del objeto

$obj = new ctrl();
$fn = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);

// Complementos;
function evaluar($val)
{
    return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
}
function evaluar3($val, $icon = '$')
{

    if ($val < 0) {
        $text = 'text-danger fw-bold ';
    } else {
        $text = 'text-success fw-bold';
    }

    if ($icon == '$') {
        return $val ? '<span class="' . $text . ' "> $ ' . number_format($val, 2, '.', ',') . '</span>' : '-';

    } else {

        $val = ($val == -100) ? 0 : $val;

        return $val ? '<span class="' . $text . ' "> ' . number_format($val, 2, '.', ',') . ' ' . $icon . ' </span>' : '-';

    }

}

function evaluar2($val)
{

    return $val ? $val : '-';
}

