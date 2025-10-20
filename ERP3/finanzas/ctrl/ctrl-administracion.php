<?php
if(empty($_POST['opc'])){
    exit(0);
}

require_once('../mdl/mdl-administracion.php');
require_once('../../conf/_Utileria.php');

class ctrl extends Administracion{

    function initComponents(){

        $ls     = $this->lsCategorias([1]);
        $sub    = $this->lsSubs([1]);
        $grupos = $this->Grupos();
        $gruopByCategory = $this->gruopByCategory([3]);
      
        
        return [
            'categorias'      => $ls,
            'subcategorias'   => $sub,
            'grupos'          => $grupos,
            'gruopByCategory' => $gruopByCategory
        ];  

    }

    function lsSubcategorias(){

        # Declarar variables
        $__row = [];
        $idCategoria = $_POST['categoria'];


        // #Consultar a la base de datos
        $ls = $this->subCategorias([$idCategoria,$_POST['id_grupo'],$_POST['activo']]);

        foreach ($ls as $key) {

          $btn = []; 
          
          $btn[] = [

            'color' => $key['activo'] == 1 ? 'success' : 'danger',
            'icon'  => $key['activo'] == 1 ? 'icon-toggle-on': 'icon-toggle-off',
            'fn'    => 'admin.disabledSub('.$key['id'].', '.$key['Stado'].', event )',
            'id'    => 'btnDisabledSub'.$key['id']
          ];

           $btn[] = [

            'color' => 'primary',
            'icon'  => 'icon-edit',
            'fn'    => 'editar',
            'id'    => $key['id']
          ];

          $verificar = $this -> getIngresos([$key['id']]);
         

          $__row[] = array(
            'id'                => $key['id'],
            'Subcategoria'      =>  $key['valor'],

            'subgrupo'          => $key['gruponombre'],
            'tarifa'            => evaluar($key['tarifa']),
            'Ingresos'          => evaluar($verificar[0]['total']),
            'status'            => $key['activo'] == 1 ? 'Activo' : 'Inactivo',
            'btn_personalizado' => $btn,
            // 'opc' => 0
          );
        }



        #encapsular datos
        return [

            "thead" => '',
            "row" => $__row,
        ];

    }


    function createSubcategoria(){

        $util = new Utileria;
        $data = $util->sql($_POST);
        $ok = $this->insertSubCategoria($data);

        return ['data' => $ok];

    }

    function disabledSub(){
        $util = new Utileria;
        $dtx  = $util->sql($_POST,1);
        $ok = $this :: updateSubCategoria($dtx);

        return ['ok' => $ok];

    }

    function ls(){
        return ['ok'=> 'ok'];
    }


}

// Complementos:
function evaluar($val){
    return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
}


// Instancia del objeto

$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);