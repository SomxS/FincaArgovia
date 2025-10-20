<?php
if (empty($_POST['opc']))
    exit(0);

require_once ('../mdl/mdl-pedidos.php');
require_once '../../../conf/coffeSoft.php';
require_once '../../../conf/_Utileria.php';



class ctrl extends CerrarPedidos{
      public $util;

    public function __construct() {
        $this->util = new Utileria(); 
    }

    function enEspera(){

        // #Consultar a la base de datos
        $ls = $this->lsPedidosEnEspera();

        foreach ($ls as $key) {

            $lsTicket = $this-> lsPedidosPendientes([$key['idCierre']]);

            $__row[] = array(

                'id'       => $key['idCierre'],
                'valor'    => $key['Numero_Pedidos'].' Pedidos ',
                'puntaje'  => $key['idCierre'],
                'obtenido' => count($lsTicket)
        
            );


        }

        return $__row;


    }

    function getSubGroup(){

        // #Consultar a la base de datos
        $ls = $this->lsPedidosPendientes([$_POST['id']]);

        foreach ($ls as $key) {

            $__row[] = array(

                'id'      => $key['idLista'],
                'valor'   => $key['NombreCliente'] ,
                'total'   => $key['idLista'],
                'current' => 0,

            );

        }

        return $__row;

    }

    function getQuestions(){
        
    }

    function pedidos(){

        # Declarar variables
        $__row = [];

        // #Consultar a la base de datos
        $ls = $this->lsPedidosTerminados([]);

        foreach ($ls as $key) {

        $products = $this->row_data(array($key['idLista']));

        $__row[] = array(

            'id'        => $key['id'],
            'nombre'    => $key['NombreCliente'],
            'fecha'     => formatSpanishDate($key['fecha']),
            'hora'      => $key['hora'],
            'productos' => count($products),
            'estado'    => setStatus($key['id_Estado']),

        );

        }

        #encapsular datos
        return [

        "thead" => '',
        "row" => $__row,
        ];


    }

    function lsPedidos(){

        # Declarar variables
        $__row = [];


        // #Consultar a la base de datos
        $ls = $this->lsPedidosTerminados([]);

        foreach ($ls as $key) {

            $products = $this -> row_data(array($key['idLista']));

            $__row[] = array(

                'id'       => $key['id'],
                'nombre'   => $key['NombreCliente'],
                'fecha'    => formatSpanishDate($key['fecha']),
                'hora'     => $key['hora'],
                'productos' => count($products),
                'estado'    => setStatus($key['id_Estado']),
                'opc'      => 0
            
            );

        }



        #encapsular datos
        return [

            "thead" => '',
            "row" => $__row,
        ];

    }

    function cerrarPedidos(){

        $data = $this-> util :: sql ([

            'FechaCierre' => date('Y-m-d H:i:s'),
            'NoPedidos'   => '',
            'id_estado'   => 1
      
        ]);


        // $success = [];

        // crear lista general
        $ok = $this-> createListGral($data);
        $lastPedido = $this-> getListID();

        $ls = $this->lsPedidosTerminados([]);
        
        foreach ($ls as $key) {

            $dataa = [
                'fecha_cierre' => date('Y-m-d H:i:s'),
                'id_cierre' => $lastPedido['idCierre'],
                'idLista'   => $key['idLista']
            ];

            $data_cerrar_pedido = $this-> util -> sql ($dataa,1);

           $ok = $this -> cerrar_pedido($data_cerrar_pedido);

            $success[] = [
                'ok' => $ok,
                'data' => $data_cerrar_pedido
            ];








        }



        return [
            'ok' => $ok,
            'success'      => $success
        
        ];

    }


}


// Instancia del objeto

$obj    = new ctrl();
$fn     = $_POST['opc'];
$encode = $obj->$fn();

echo json_encode($encode);