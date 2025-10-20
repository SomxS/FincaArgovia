<?php
if (empty($_POST['opc'])) {
    exit(0);
}

header("Access-Control-Allow-Origin: *"); // Permite solicitudes de cualquier origen
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type"); // Encabezados permitidos

// incluir tu modelo
require_once '../mdl/mdl-punto-de-venta.php';
require_once '../../conf/_Utileria.php';
require_once '../../conf/coffeSoft.php';


class ctrl extends Pos{
      public $util;

    public function __construct() {
        parent::__construct(); // Llama al constructor de la clase padre
        $this->util = new Utileria(); 
    }


    function setNewFolio(){
        $random         = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
        $_POST['folio'] = $random;

        $data           = $this-> util :: sql($_POST);
        $success        = $this-> setFolio($data);
        
        // devolver lista con el nuevo ticket
        $lsFolio  = $this->getTickets();
        $newFolio = $this->getNewFolio();

        return [

            'ok'      => $ok,
            'lsFolio' => $lsFolio,
            'idFolio' => $newFolio[0]['idLista']
        ];
    }

    function getProductsBy(){

        $infoTicket =  $this -> getFolioByID([ $_POST['idFolio'] ]);

         

         $list = $this -> listProductsBy([$_POST['id']]);
         $row  = [];

         foreach ($list as $key) {
            
            $precio_especial = $this -> Select_Precio_Especial([$infoTicket[0]['idCliente'],$key['id']]);
            $entrada         = $this -> Select_Entrada_Productos([$key['id']]);
            $salida          = $this -> Select_Salida_Productos([ $key['id'], 1 ]);
            $temporal        = $this -> Select_Salida_Productos([ $key['id'], 0 ]);
             
            $actual   = $entrada['total'] - $salida['total'] - $temporal['total'] ;

            $row[] = array(

                'id'         => $key['id'],
                'valor'      => $key['valor'],
                'costo'      => $key['costo'],
                'especial'   => $precio_especial['Costo'],
                'disponible' => $actual
            
            );
         }

         return $row;
    }

    function setProducts(){


        $products = $this -> getProductsByID([ $_POST['idFolio'] , $_POST['id_producto'] ]);

        if($products): // ya existe en la lista
        
        else:

            $data  = $this -> util -> sql( [

                 'id_lista'     => $_POST['idFolio'],
                 'id_productos' => $_POST['id_producto'],
                 'cantidad'     => 1,
                 'costo'        => $_POST['costo']

            ]);

            $success = $this ->addProduct($data);
        
        endif;


        return [
            'ok' => $success,
            'products' => $products
        ];
    }

    function removeItem(){
        return $this -> removeProducts([$_POST['idListaProductos']]);

    }

    function setCantidad(){
        $data    = $this -> util -> sql($_POST, 1);
        $success = $this -> updateCantidad($data);

        return [
            'ok' => $data,
            'success' => $success
        ];
   
    }

    function setCortesia(){

        $data    = $this -> util -> sql($_POST,1);

        $success = $this ->setListCortesia($data);

        return [

            'ok' => $success,
        ];

    }
     
    function lsTicket(){

        # Variables
        $__row   = [];
        $idFolio = $_POST['NoFolio'];

        // # Lista de productos
        
        
        $ls = $this->getProductsByFolio([$idFolio]);
        $totalGeneral = 0;

        foreach ($ls as $key) {
            // vars.
            $btn   = [];
            $costo = evaluar($key['costo']);
            $total = $key['costo'] * $key['cantidad'] ;
            
            
            $btn[] = [
                'icon'  => 'icon-trash',
                'fn'    => 'pos.removeItem',
                'color' => ' text-danger',
            ];

                       
            $check = createdChecked($key['id'],$key['costo']);
            
            if($key['costo'] == 0){
              $products = $this ->  getProduct([$key['id_productos']]);
              $costo    = evaluar($products['precio']);  
            }

            $input = $this -> componentInput(['id'=>$key['id'],'value' => $key['cantidad']]);

            $__row[] = array(
                'id'       => $key['id'],
                'item'     => $key['valor'],
                'Cant'     => ['class'=>'p-0 m-0 col-2', 'html'=>$input],
                'costo'    => ['text' => $costo , 'class' =>  $key['costo'] == 0 ? 'text-center text-danger text-decoration-line-through' : ''],
                'total'    => evaluar($total),
                'Cortesia' => ['html'=> $check , 'class' => 'text-center'],
                'btn'      => $btn
            );

            $totalGeneral += $total;
        } 

        $lsFolio = $this->getFolioByID([$idFolio]);

        $head = [

            'data' => [
                'folio'   => '#'.$idFolio,
                'title'   => '',
                'date'    => formatSpanishDate($key['fecha']),
                'cliente' => $lsFolio[0]['Name_Cliente'],
            ],
            'type' => 'details'
        ];

        $foot = footerTotal([
            'id' => $id,
            'total' => $totalGeneral,
        ]);

        // Encapsular arreglos
        return [ 'head'=> $head,"thead" => '', "row" => $__row,'cardtotal'=>$foot];


    }

    function componentInput($data) {
        return "<input 
          
            id='iptCantidad_{$data['id']}'
            value='{$data['value']}'
            onkeyup='pos.updateCantidad( {$data['id']})'
            class='w-full p-2 bg-transparent text-right focus:outline-none focus:ring-2 focus:ring-blue-500'
        />";
    }


    // Operations.

    function endTicket(){

        // Crear base común para los datos
        $lsData = [
        'Total'     => $_POST['Total'],
        'id_Estado' => $_POST['id_Estado']
      
        ];

        // Agregar clave adicional según el tipo
        switch ($_POST['tipo']) {

            case 1: // Efectivo
                $lsData['Efectivo'] = $_POST['Total'];
                $lsData['idLista'] = $_POST['idLista'];
             
            break;

            case 2: // Crédito
                $lsData['Credito'] = $_POST['Total'];
                $lsData['idLista'] = $_POST['idLista'];
           
            break;

            case 3: // TDC
                $lsData['TDC'] = $_POST['Total'];
                $lsData['idLista'] = $_POST['idLista'];
           
            break;

            case 4: // Otros datos
                unset($_POST['tipo']); // Eliminar 'tipo' de $_POST si es necesario
                $lsData['idLista'] = $_POST['idLista'];
           
                $lsData = array_merge($lsData, $_POST); // Combinar $_POST con la base
            break;

            default: // Tipo inválido
            return ['error' => 'Tipo no válido'];
        }

        // Procesar los datos
        $update = $this->util->sql($lsData, 1);
          return $this->setTickets($update);


    }

    function cancelTicket(){


         return $this->update_ticket([2, $_POST['idFolio']]);
    }
 

}


// Complement.
function footerTotal($data){

    $foot = '
        <div class="row">
            <div class="col-12 text-end">
            <label class="font-bold text-lg text-gray-700">TOTAL:  
            <span id="total" class="font-bold text-gray-500"> '.evaluar($data['total']).'</span> </label>    
            </div>
        </div>';

    return $foot;

   
}

function createdChecked($idList,$costo){
    
    $checked = $costo == 0 ? 'checked' : '';
    $check = '
        <input  id="checked-checkbox" 
        type="checkbox" 
        '.$checked.'
        value="" 
        class="form-check-input"
        onchange="pos.addCortesia(event,'.$idList.')"
        >
    ';


    return $check;



}

// Instancia del objeto

$obj = new ctrl();
$fn = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);
