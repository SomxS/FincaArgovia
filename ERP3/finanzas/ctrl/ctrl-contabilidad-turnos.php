<?php
if (empty($_POST['opc']))
    exit(0);

// incluir modelo
require_once('../mdl/mdl-contabilidad.php');


class ctrl extends Turnos{

    function lsTurnos(){

        # Declarar variables
        $__row = [];
        $fecha = $_POST['fecha'];

        // #Consultar a la base de datos
        $ls    = $this->lsCategorias([1]);
        $fpago = $this ->Select_formaspago_by_categoria([1]);

        foreach ($ls as $Categoria) {

            $ventasByCategory = $this :: listVentasPorCategoria([$Categoria['id'],$fecha]);
            if($ventasByCategory[0]['total']) // Muestra solo si hubo ventas en esta categoria
            
            $__row[] = array(
                'id'       => $Categoria['id'],
                'Concepto' => $Categoria['Categoria'],
                'colgroup' => true
            );
     
            $group      = self :: lsGroup([$Categoria['id']]);

            foreach ($group as $_key) {

                $ventas        = $this ->listIngresosPorGrupo([$_key['idgrupo'],$Categoria['id'],$_POST['fecha']]);
                $totalIngresos = 0;

                if($ventas): // muestra unicamente si hubo ventas 

                    if($_key['gruponombre'] != 'SIN GRUPO')

                    $__row[] = array(
                        'id'       => $_key['idgrupo'],
                        'nombre'   => $_key['gruponombre'],
                        'colgroup' => true
                    );

                    foreach ($ventas as $venta) {

                        $conceptos      = [];
                        $ingresos       = [];
                        
                        $totalIngresos += $venta['Tarifa'];

                        $conceptos = array(
                            'id'       => $venta['idSubcategoria'],
                            'nombre'   => $venta['Subcategoria'],
                            'Monto'    => evaluar($venta['Tarifa']),
                            'Pax'      => $venta['pax']
                        );

                        // añadir como se pago
                        foreach ($fpago as  $valfpago) {

                            $valMontofpago                     = $this ->Select_MontoFPago([$fecha,$fecha,$venta['idSubcategoria'],$valfpago['idFormas_Pago']]);
                            $ingresos[$valfpago['FormasPago']] = evaluar($valMontofpago);
                            
                        }

                        $ingresos['opc'] = 0;
                        $__row[]         = array_merge($conceptos,$ingresos);
                    }

                endif;
            }
        }

        # encapsular datos
        return [
            "thead" => ['Grupo','Total','Pax','EFECTIVO','TC','CXC','ANTICIPO'],
            "row" => $__row,
        ];

    }

    function listIngresosPorGrupos(){
        $group      = self :: lsGroup([$Categoria['id']]);

        foreach ($group as $_key) {

        }

    }


}

// Complementos :
function evaluar($val)
{
    return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
}

// Instancia del objeto

$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);