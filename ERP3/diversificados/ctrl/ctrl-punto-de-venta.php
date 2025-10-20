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


// sustituir 'mdl' extends de acuerdo al nombre que tiene el modelo
class ctrl extends Puntodeventa {
      public $util;

    public function __construct() {
        parent::__construct(); // Llama al constructor de la clase padre

        $this->util = new Utileria(); 
    }

    public function init(){

        return [

            'grupo'    => $this -> lsGroup(),
            'subgrupo' => $this -> lsSubgroup(),
            'tipo'     => $this -> lsTipo(),
            'clients'  => $this -> getClients(),
            'typeSales' => $this -> getTypeSales(),
            'tickets'  => $this -> getTickets()

        ];

    }

    public function lsProducts(){

        # Declarar variables
        $__row = [];
        

        $ls = $this->listProductsBy([$_POST['id']]);
        foreach ($ls as $key) {
            $estatus = $key['Status'];


            $a = [];

            $a[] = [
                'id'    => $key['id'],
                'html'  => '<i class="icon-pencil"></i>',
                'icon'  => 'icon-user',
                'class' => 'pointer text-info',
                'onclick' => 'pos.onshowEditProducts(' . $key['id'] . ')',
            ];

            $icon = $estatus == 1 ? ' icon-toggle-on' : ' icon-toggle-off';


            $a[] = [

                'class'   => 'text-primary pointer py-0',
                'html'    => '<i class="' . $icon . '"></i>',
                'id'      => 'btnEstatus' . $key['id'],
                'estatus' => $estatus,

                'onclick' => 'pos.setEstatus(' . $key['id'] . ')',

            ];


            $__row[] = array(

                'id'             => $key['id'],

                'nombre'         => $key['NombreProducto'],
                'Presentación'   => $key['presentacion'],
                'Precio'         => evaluar($key['Precio']),
                'Precio Mayoreo' => evaluar($key['Precio_Mayoreo']),
                'a'              => $a
            );

        }

        #encapsular datos
        return [  "thead" => '' , "row" => $__row  ];

    }

    function InfoeditProducts(){
        return $this -> getProduct([$_POST['idProduct']]);
    }

    function editProducts(){

        $data =  $this-> util :: sql ($_POST,1);

        $ok = $this -> updateProducts($data);

        return $ok;
    }


    function setEstatusProducts(){

        $data = $this->util :: sql([
        
            'Status'     => $_POST['nuevoEstado'],
            'idProducto' => $_POST['id']
        
        ],1);


        return  $this -> updateEstatus($data);


    
    }

    // Sub-Groups .

    public function lsSubGroups(){

        # Declarar variables
        $__row = [];
        
        #Consultar a la base de datos

        $ls = $this->lsSubgroup([1]);
        foreach ($ls as $key) {

            $a = [];

            $a[] = [
            'id'    => $key['id'],
            'html'  => 'Editar',
            'icon'  => 'icon-user',
            'class' => 'pointer text-info',
            ];


            $__row[] = array(

                'id'       => $key['id'],
                'Grupo'    => $key['nombrecategoria'],
                'SubGrupo' => $key['valor'],
                'a'        => $a
            );

        }

        #encapsular datos
        return [  "thead" => '' , "row" => $__row  ];

    }

    function setSubGroups(){
        return $_POST;
    }

    // Clientes preferenciales.
    public function lsClientes(){
        $__row = [];

        // #Consultar a la base de datos
        $ls = $this->listClientes();

        foreach ($ls as $key) {

            $button = [];

            $button =array ( [

                'class'   => ' mx-0 pointer text-primary',
                'html'    => '<i class=" icon-pencil"></i>',
                'onclick' => 'clientes.onshowEdit('.$key['idCliente'].')',
                'title'   => 'Precios preferenciales'

            ],

            [

                'class'   => ' mx-0 pointer text-primary',
                'html'    => '<i class=" icon-shop"></i>',
                'onclick' => 'clientes.onshowDescuentos('.$key['idCliente'].')',
                'title'   => 'Precios preferenciales'

            ] );

            

            $__row[] = array(
                'id'      => $key['idCliente'],
                // 'Folio'   => $key['idCliente'],
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

    public function lsProductsPreferentials(){
        $__row = [];

        // #Consultar a la base de datos
        $ls = $this->listProductsBy([$_POST['idGroup']]);

        foreach ($ls as $key) {


            $price = $this -> getPreferentialPrice([$_POST['idCliente'],$key['id']]);
           
            
            $__row[] = array(
                'id'           => $key['id'],
                'Folio'        => $key['id'],
                'Producto'     => $key['NombreProducto'],
                'Presentacion' => $key['presentacion'],
                'Precio'       => [
                    
                    'html' =>  _input([
                        'id'    => $key['id'],
                        'value' => $price['Costo']

                    ]) , 'style' => 'padding:0; margin:0;'  ],
                'opc'          => 0,
            );
        }

        #encapsular datos
        return [

            "thead" => '',
            "row"   => $__row,
            'group' => $_POST['idGroup']
        ];

    }

    public function setPriceDiscount(){

            $idEpecial = $this->Select_idPrecioEspecial([$_POST['idCliente'], $_POST['idProducto']]);

            // if ($idEpecial == 0) {
            //     $data = $this->util->sql($_POST);

            //  return   $this-> Insert_PrecioEspecial($data);
            // } else {
            //     $data = $this->util->sql(['Costo' => $_POST['Costo'],'idEspecial' => $idEpecial],1);
            //   return $this -> Update_PrecioEspecial($data);
            // }


            return $idEpecial;


        // return $this -> setPreferentialPrice([$_POST['idCliente'], $_POST['id'], $_POST['value']]);

    }

    public function addCliente(){

        $data = $this -> util -> sql($_POST);

        $ok = $this -> addClients($data); 

        return $ok;
    }
    

   




}
// Complements.

function _input($data){
    return '

        <input onkeyup="clientes.setPriceDiscount(event)" type="text" id="' . $data['id'] . '' . $data[0] . '" value="' . $data['value'] . '"
        class="form-control input-sm text-end" >
        ';
}


// Instancia del objeto

$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);
