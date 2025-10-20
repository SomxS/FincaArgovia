<?php
if (empty($_POST['opc'])) {
    exit(0);
}

setlocale(LC_TIME, 'es_ES.UTF-8'); // Configura el idioma a español


require_once '../mdl/mdl-pedidos.php';
require_once '../../../conf/coffeSoft.php';
require_once('../../../conf/_Utileria.php');

$encode = [];
class ctrl extends POS {
    public $util;

    public function __construct() {
        // parent::__construct(); // Llama al constructor de la clase padre
        $this-> util = new Utileria(); 
    }

    public function listItem(){

        # Declarar variables
        $__row = [];

        $list = $this->list_productos([$_POST['sub']]);

        foreach ($list as $_key) {
         
            $__row[] = array(

                'id'      => $_key['idProducto'],
                'nombre'  => $_key['NombreProducto'],
                'costo'   => evaluar($_key['Costo']),
                // 'attr'    => ['src' => ''],
                'onclick' => 'pos.addItem('.$_key['idProducto'].','.$_key['Costo'].')',
            );

        } // end lista de folios

       return [  "row" => $__row, ];

    
    }
    
    function listFols(){

        return [

        
            "listFols"     =>  $this->lsFolio(),
            'listClientes' =>  $this->listClientes()
        
        ];
       
    }

    function openTicket(){
        # Variables
        $__row = [];
        $idFolio = $_POST['NoFolio'];

        # Lista de productos 
        $ls    = $this -> listProducts([ $idFolio ]);
        $total = 0;
        
        
        foreach ($ls as $key) {

            $btn = [];

            $btn[] = [
                'icon' => 'icon-trash',
                'fn'   => 'pos.removeItem',
                'color' => ' text-danger',
            ];
            
            $btn[] = [
                'icon' => ' icon-list-add',
                'fn'   => 'pos.modalComplements',
                'color' => ' text-success',
            ];
           
            
            $__row[] = array(

                'id'       => $key['id'],
                'item'     => $key['NombreProducto'],
                'Cant.' => $key['cantidad'],
                // 'U/Venta'  => $key['Unidad'],
                'costo'    => evaluar($key['costo']),
                'total'    => evaluar($key['total']),
                'btn'      => $btn
            
            );


            $total += $key['total'];
        
        }// end lista de folios


        $lsFolio  = $this -> getFolio([$idFolio]);

        $head = [

            'data' => [
                'folio'   => $lsFolio[0]['idLista'],
                'title'   => '',
                'date'    => formatSpanishDate($key['f']),
                'cliente' => $lsFolio[0]['NombreCliente'],
            ]
        ];

        $foot = $this :: ticket_foot( [
            'id'    => $id,
            'total' => $total
        ]);


        // Encapsular arreglos
        return [ 'head'=> $head, "thead" => '',"row" => $__row ];
    }


    // Products.

    function addItem(){

        $Inlist  = $this -> Inlist([$_POST['id_producto'],$_POST['idFolio']]);

        if($Inlist): // actualizr registro

            $cant  =  $Inlist[0]['cantidad'] + 1 ;
            $total =  $Inlist[0]['costo']    *  $cant ;

            $data  = $this-> util -> sql([
                
                'cantidad'         => $cant,
                'total'            => $total,
                'idListaProductos' => $Inlist[0]['idListaProductos']

            ], 1 );


            return $this ->update_producto_vendido( $data );


        else: // agrega el producto


            $data  = $this -> util -> sql([

                 'id_lista'     => $_POST['idFolio'],
                 'id_productos' => $_POST['id_producto'],
                 'cantidad'     => 1,
                 'id_unidad'    => 1,
                 'costo'        => $_POST['costo'],
                 'total'        => $_POST['costo'],
           
            ]);


            return $this ->add_producto_vendido($data);

        endif;



     
    }

    function removeItem(){

        return $this -> removeProducts([$_POST['id']]);
    }

    function listComplements(){
        # Variables
        $__row = [];


        # Lista de productos 
        $ls = $this->lsComplements($_POST['']);

        foreach ($ls as $key) {

            $__row[] = array(
                'id'      => $key['id'],
                'insumo'  => $key['Nombre_insumo'],
                'name'    => $key['valor'],
                'opc'     => 0
            );
        }// end lista de folios



        // Encapsular arreglos
        return [ "thead"    => ['id','Flor','btns'], "row" => $__row,];


    }

    function addComplements(){

        return $_POST;
    }

    // ticket.

    function ticket_head($data){


        $div = '

    

        <div class="">
        <table style="font-size:.6em; " class="table table-sm table-bordered">

        <tr>
        <td class="col-sm-1 fw-bold"> Cliente:   </td>
        <td class="col-sm-2"> '.$data['cliente'].'  </td>

        <td colspan="2" class="col-sm-6 text-center"> <strong>'.$data['titulo'].'</strong> </td>
        <td class="col-sm-1 fw-bold"> Folio:  </td>
        <td class="col-sm-2 text-right text-end">'.$data['folio'].'</td>
        </tr>

        <tr>


        <td class="col-sm-1 fw-bold">Destino :</td>
        <td class="col-sm-2">  '.$data['destino'].'  </td>
        <td class="col-sm-1 fw-bold">Fecha:</td>
        <td class="col-sm-2">'.$data['fecha'].'</td>
        <td class="col-sm-1 fw-bold">Estado:</td>
        <td class="col-sm-2"></td>
        </tr>


        </table>
        </div>

        ' ;
        //  <td class="col-sm-1 fw-bold">Fecha </td>
        //     <td class="col-sm-3">'.$data['fecha'].'</td>



        return $div;
    }

    function ticket_foot($data){
        $foot = '
        <div class="row">
        <div class="col-12 text-end">
        <strong>TOTAL:  '.evaluar($data['total']).' </strong>    
        </div>
        </div>';

        if($data['id'])

        $foot .= '    
        <div class="row  mt-3">
        <div class="col-12 text-end">
        <button class="btn btn-outline-primary" onclick="subirPedidos('.$data['id'].')"> <i class="icon-ok"></i> Terminar Pedido </button>
        <button class="btn btn-outline-danger"  onclick="cancelarPedido('.$data['id'].')"> <i class="icon-ok"></i> Cancelar Pedido </button>
        </div>
        </div>
        ';

        return $foot;
    }

    function newTicket(){

        $hour = date('H:i:s');

        $ticket               = $_POST;
        $ticket['id_tipo']    = 1;
        $ticket['id_Estado']  = 1;
        $ticket['folio']      = '000';  
        $ticket['foliofecha'] = $_POST['foliofecha'].' '.$hour;

        // add new ticket.
        $data                = $this -> util->sql( $ticket );
        $ok                  = $this -> _Insert($data,'hgpqgijw_ventas.lista_productos');

        // devolver lista con el nuevo ticket
        $lsFolio =$this->lsFolio();

        $newFolio = $this->newFolio();
        
        return [ 'ok' => $data ,'lsFolio' => $lsFolio, 'idFolio' => $newFolio[0]['idLista']];
    }

    function cerrarTicket(){
        return $this->update_ticket([2, $_POST['idFolio']]);

    }
  
    function cancelarTicket(){


        return $this->update_ticket([3, $_POST['idFolio']]);


    }


    function addComents(){

        return $_POST;

    }

    function addFile(){

    
        $MES     = strftime('%B'); 
        $year    = date('Y');
        
        $file_C       = $MES.'_'.$year;
        $ruta = 'ERP/recursos/pedidos/'.$year.'/'.$file_C.'/';
        $carpeta      = '../../../'.$ruta_cheques;

        $estatus      = true;


        // //Busca si existe la carpeta si no la crea
        if (!file_exists($carpeta)) {


            $estatus  = false;
            // mkdir($carpeta, 0777, true);
        }

        $data = [];    

        foreach ($_FILES as $cont => $key):

            $NombreOriginal = $key['name'];             
            $temporal       = $key['tmp_name'];         
            $size           = ($key['size']/1024) / 1024;

            $destino        = $carpeta.$NombreOriginal;

            $trozos    = explode(".", $NombreOriginal);
            $extension = end($trozos);

        // // //     move_uploaded_file($temporal, $destino); 



            $data['Archivo'] = $NombreOriginal;
            $data['Ruta']    = $ruta;
            $data['Fecha']   = date('Y-m-d');
            $data['id_lista'] = $_POST['idfolio'];

            $dtx  = $this -> util->sql($data);

            $ok    = $this -> add_archivo($dtx);




        endforeach;

        return [
            'ok'   => $ok,
            'file' => $estatus,
            'dtx'  => $dtx
        ]; 

    }

  

  

}


// Instancia del objeto

$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);



