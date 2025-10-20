<?php
if (empty($_POST['opc'])) {
    exit(0);
}

header("Access-Control-Allow-Origin: *"); // Permite solicitudes de cualquier origen
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type"); // Encabezados permitidos

// incluir tu modelo
require_once '../mdl/mdl-pedidos.php';

$encode = [];
class ctrl extends Clientes {
    // public $util;

    public function __construct() {

        // parent::__construct(); // Llama al constructor de la clase padre
    //     $this-> util = new Utileria(); 
    }

    function init(){

        return [
            'groups' => $this -> listCategory()
        ];
    }

    public function lsClientes(){
        $__row = [];

        // #Consultar a la base de datos
        $ls = $this->listClientes();

        foreach ($ls as $key) {

            $button = [];

            $button[] = [

                'class' => ' mx-0 pointer text-primary',
                'html'  => '<i class="icon-pencil"></i>',
                'onclick' => 'clients.onshowDescuentos('.$key['idCliente'].')'

            ];

            

            $__row[] = array(
                'id'      => $key['idCliente'],
                'Folio'   => $key['idCliente'],
                'Cliente' => $key['Name_Cliente'],
                'a'     => $button,
            );
        }

        #encapsular datos
        return [

            "thead" => '',
            "row" => $__row,
        ];

    }

    public function lsProducts(){
        $__row = [];

        // #Consultar a la base de datos
        $ls = $this->listProductsBy([$_POST['idGroup']]);

        foreach ($ls as $key) {

            $button = [];

            $button[] = [

                'class'   => ' mx-0 pointer text-primary',
                'html'    => '<i class="icon-pencil"></i>',
                'onclick' => 'clients.onshowDescuentos('.$key['idCliente'].')'

            ];

            $price = $this -> getPreferentialPrice([$_POST['idCliente'],$key['id']]);

            
            $__row[] = array(
                'id'           => $key['id'],
                'Folio'        => $key['id'],
                'Producto'     => $key['NombreProducto'],
                'Presentacion' => $key['presentacion'],
                'Precio'       => ['html' =>  _input(['value' =>$price['Costo']]) , 'style' => 'padding:0; margin:0;'  ],
                'opc'          => 0,
            );
        }

        #encapsular datos
        return [

            "thead" => '',
            "row" => $__row,
            'group' => $_POST['idGroup']
        ];

    }

}

// Instancia del objeto

$obj = new ctrl();
$fn = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);

function _input($data)
{
    return '
  
       <input onkeyup="clients.setCantidad(this)" type="text" id="' . $data[1] . '' . $data[0] . '" value="' . $data['value'] . '"  
       class="w-full  py-2 px-4  text-gray-700 leading-tight focus:outline-none text-right focus:shadow-outline" >
    ';
}


