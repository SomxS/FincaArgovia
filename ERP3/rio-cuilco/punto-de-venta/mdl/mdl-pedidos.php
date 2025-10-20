<?php
require_once('../../../conf/_CRUD.php');

class Pedidos extends CRUD{

    public $bd;
    
    public function __construct() {
        $this->bd = "hgpqgijw_ventas.";
    }

    function getResponsabilidad($array){
        
        $query ="
            SELECT
                usuarios.idUser,
                usuarios.usser,
                usuarios.usr_Colaborador,
                usuarios.usr_empleado
            FROM
                hgpqgijw_erp.usuarios
            
            WHERE idUser = ?    
        
        ";

        return $this->_Read($query, $array)[0];

    }

    function useEventStatus($array){

        $query = "
            SELECT 
            
            idEventStatus,
            id_Event,
            date_ocurrence,
            id_Status,
            DATE_FORMAT(date_finished,'%Y-%m-%d') as date_finished
        
            
            FROM hgpqgijw_calendarizacion.event_status where id_Event = ?;

        ";

        return $this->_Read($query, $array);


    }

    function VerFormatos($array){

        $sql = "
        SELECT
        lista_productos.idLista,
        lista_productos.folio,
        NombreCliente,
        date_format(foliofecha,'%Y-%m-%d') as fecha,
        date_format(foliofecha,'%H:%i:%s') as hora,
        id_Estado,
        id_cliente

        FROM
        hgpqgijw_ventas.cliente
        INNER JOIN hgpqgijw_ventas.lista_productos ON
        hgpqgijw_ventas.lista_productos.id_cliente = hgpqgijw_ventas.cliente.idCliente
        WHERE
        lista_productos.id_Estado = 2 and
        DATE_FORMAT(foliofecha,'%Y-%m-%d') Between  ? and ?
        GROUP BY idLista ORDER BY idLista desc";

        return $this->_Read($sql, $array);
    }

    function row_data($array){
        $sql = "
        SELECT
        listaproductos.idListaProductos,
        listaproductos.descripcion,
        listaproductos.cantidad,
        listaproductos.id_unidad,
        listaproductos.id_lista,
        venta_unidad.Unidad,
        listaproductos.observacion,
        listaproductos.costo,
        (listaproductos.costo * cantidad) as total,
        venta_productos.NombreProducto
        FROM
        hgpqgijw_ventas.listaproductos
        LEFT JOIN hgpqgijw_ventas.venta_unidad ON listaproductos.id_unidad = venta_unidad.idUnidad
        INNER JOIN hgpqgijw_ventas.venta_productos ON venta_productos.id_unidad = venta_unidad.idUnidad AND listaproductos.id_productos = venta_productos.idProducto

        WHERE  id_lista = ?
        ORDER BY idListaProductos asc";

        return $this->_Read($sql, $array);
    }

    //   

    function listCategory(){
        $query ="SELECT 
        idcategoria as id, 
        nombrecategoria as valor FROM hgpqgijw_ventas.venta_categoria;";
        return $this->_Read($query, null);
    }

    function listSubcategory($array){

        $query = "SELECT
        idSubcategoria as id,
        Nombre_subcategoria as valor 
        FROM hgpqgijw_ventas.venta_subcategoria
        WHERE id_categoria = ?";
        return $this->_Read($query, $array);


    }

    function lsProducts(){

        $query = "

        SELECT
            idProducto as id,
            NombreProducto as valor,
            Venta as precio
        FROM
            hgpqgijw_ventas.venta_productos

        
        ";

        return $this->_Read($query, null);

    }

    function lsFolio(){
        $query =" SELECT
        idLista as id,
        CONCAT(NombreCliente,'  - ', DATE_FORMAT(foliofecha,'%Y-%m-%d')) as valor,
        id_Estado
        FROM
        hgpqgijw_ventas.cliente
        INNER JOIN hgpqgijw_ventas.lista_productos ON lista_productos.id_cliente = cliente.idCliente WHERE id_Estado = 1
        ";


        return $this->_Read($query, null);
    }

    function listClientes(){

        $query = "
            SELECT
            idCliente as id,
            NombreCliente as valor
            FROM
            hgpqgijw_ventas.cliente WHERE estado_cliente = 1";
        return $this->_Read($query, null);

    }

    function getFolio($array){

        $sql="
            SELECT
                date_format(foliofecha,'%Y-%m-%d') AS f,
                lista_productos.id_cliente,
                lista_productos.folio,
                lista_productos.idLista,
                lista_productos.id_Estado,
                lista_productos.Detalles,
                cliente.NombreCliente,
                cliente.ciudad,
                estado_solicitud.EstadoSolicitud
            FROM
            hgpqgijw_ventas.lista_productos
            INNER JOIN hgpqgijw_ventas.cliente ON lista_productos.id_cliente = cliente.idCliente
            INNER JOIN hgpqgijw_ventas.estado_solicitud ON lista_productos.id_Estado = estado_solicitud.idEstado
            WHERE idLista=?";

        return $this->_Read($sql,$array);


    }

    // Complements.

    function lsComplements($array){

        $query = "

        SELECT
            hgpqgijw_ventas.productos_adicionales.idInsumo,
            hgpqgijw_ventas.productos_adicionales.Nombre_insumo,
            hgpqgijw_ventas.productos_adicionales.cantidad,
            hgpqgijw_ventas.productos_adicionales.Precio,
            hgpqgijw_ventas.venta_productos.NombreProducto,
            hgpqgijw_ventas.productos_adicionales.id_referencia
        FROM
            hgpqgijw_ventas.productos_adicionales
        INNER JOIN hgpqgijw_ventas.venta_productos
        ON productos_adicionales.id_producto_adicional = venta_productos.idProducto
        WHERE id_referencia = ?
        ";

        return $this->_Read($query, $array);
    }

    function lsEvents($array){
        // $query ="
        //     SELECT
        //     hgpqgijw_erp.usuarios.idUser,
        //     hgpqgijw_erp.usuarios.usser,
        //     usr_creator,
        //     custom,
        //     until,
        //     idEvent,
        //     id_Replay,
        //     hgpqgijw_calendarizacion.events.creation,
        //     hgpqgijw_calendarizacion.events.update,
        //     hgpqgijw_calendarizacion.events.title,
        //     hgpqgijw_calendarizacion.people.id_Employe,
        //     hgpqgijw_calendarizacion.people.id_Event,
        //     hgpqgijw_calendarizacion.people.id_Status,
        //     hgpqgijw_calendarizacion.people.finished,
        //     hgpqgijw_calendarizacion.people.initiation,
        //     hgpqgijw_calendarizacion.priority.idPriority,
        //     hgpqgijw_calendarizacion.priority.name,
        //     hgpqgijw_rrhh.empleados.Nombres,
        //     hgpqgijw_rrhh.empleados.idEmpleado
        //     FROM
        //     hgpqgijw_calendarizacion.events
        //     INNER JOIN hgpqgijw_erp.usuarios ON hgpqgijw_calendarizacion.`events`.usr_creator = hgpqgijw_erp.usuarios.idUser
        //     INNER JOIN hgpqgijw_calendarizacion.people ON hgpqgijw_calendarizacion.people.id_Event = hgpqgijw_calendarizacion.`events`.idEvent
        //     INNER JOIN hgpqgijw_calendarizacion.priority ON hgpqgijw_calendarizacion.`events`.id_Priority = hgpqgijw_calendarizacion.priority.idPriority
        //     INNER JOIN hgpqgijw_rrhh.empleados ON hgpqgijw_calendarizacion.people.id_Employe = hgpqgijw_rrhh.empleados.idEmpleado
        // ";

        $query = "
            
            SELECT

                hgpqgijw_rrhh.empleados.Nombres,
                date_start,
                date_end,
                hgpqgijw_calendarizacion.`events`.idEvent,
                hgpqgijw_calendarizacion.`events`.creation,
                hgpqgijw_calendarizacion.`events`.`update`,
                hgpqgijw_calendarizacion.`events`.usr_creator,
                hgpqgijw_calendarizacion.`events`.title,
                hgpqgijw_calendarizacion.replay.frecuency,
                hgpqgijw_calendarizacion.replay.`name`
            
            FROM
                hgpqgijw_rrhh.empleados
                INNER JOIN hgpqgijw_calendarizacion.people ON hgpqgijw_calendarizacion.people.id_Employe = hgpqgijw_rrhh.empleados.idEmpleado
                INNER JOIN hgpqgijw_calendarizacion.`events` ON hgpqgijw_calendarizacion.people.id_Event = hgpqgijw_calendarizacion.`events`.idEvent
                INNER JOIN hgpqgijw_calendarizacion.replay ON hgpqgijw_calendarizacion.`events`.id_Replay = hgpqgijw_calendarizacion.replay.idReplay

        
        ";
        return $this->_Read($query, $array);
    }
}

class POS  extends Pedidos {
 
    public function __construct() {
        parent::__construct(); // Llama al constructor de la clase padre
    }

    function newFolio(){
        $query = "
            SELECT
                lista_productos.idLista,
                lista_productos.id_Estado
            FROM hgpqgijw_ventas.lista_productos
            WHERE id_Estado = 1
            ORDER BY idLista DESC LIMIT 1
        ";
        return $this->_Read($query, null);
    
    }

    function update_ticket($array){
        $query = '
        UPDATE
        hgpqgijw_ventas.lista_productos
        SET id_Estado   = ?

        WHERE  idLista = ? ';

        return $this->_CUD($query, $array);
    }


    function lsSubCat($array){
        $query = "SELECT * FROM hgpqgijw_ventas.venta_subcategoria WHERE id_categoria = ?";
        return $this->_Read($query, $array);
    }

    function listProducts($array){
    
        $sql = "
        SELECT
            idListaProductos as id,
            listaproductos.descripcion,
            listaproductos.cantidad,
            listaproductos.id_unidad,
            listaproductos.id_lista,
            venta_unidad.Unidad,
            listaproductos.observacion,
            listaproductos.costo,
            (listaproductos.costo * cantidad) as total,
            venta_productos.NombreProducto
        FROM
        hgpqgijw_ventas.listaproductos
        LEFT JOIN hgpqgijw_ventas.venta_unidad ON listaproductos.id_unidad = venta_unidad.idUnidad
        INNER JOIN hgpqgijw_ventas.venta_productos ON venta_productos.id_unidad = venta_unidad.idUnidad AND listaproductos.id_productos = venta_productos.idProducto

        WHERE  id_lista = ?
        ORDER BY idListaProductos asc";

        return $this->_Read($sql, $array);
        
    }

    function Inlist($array){
        $id = 0;

        $query =
        "
        SELECT
            idListaProductos,
            cantidad
        FROM hgpqgijw_ventas.listaproductos
        WHERE
        id_productos = ?
        and id_lista = ?

        ";

        return $this->_Read($query, $array);

    }


    function update_producto_vendido($array){

        return $this->_Update([

            'table'  => "hgpqgijw_ventas.listaproductos",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data'],
        
        ]);
    }

    function add_producto_vendido($array){
       
        return $this->_Insert([
            'table'  => "hgpqgijw_ventas.listaproductos",
            'values' => $array['values'],
            'data'   => $array['data'],
        ]);
    
    }



    function list_productos($array){
        // $query = "SELECT
        // venta_productos.idProducto,
        // venta_productos.NombreProducto,
        // venta_productos.Costo,
        // venta_productos.url_image,
        // venta_productos.id_subcategoria
        // FROM
        // hgpqgijw_ventas.venta_productos WHERE id_subcategoria = ? ";

        $query = '
        SELECT
            venta_subcategoria.id_categoria,
            venta_productos.idProducto,
            venta_productos.NombreProducto,
            venta_productos.Costo,
            venta_productos.url_image,
            venta_productos.id_subcategoria,
            venta_productos.udn_almacen
        FROM
            hgpqgijw_ventas.venta_subcategoria
        INNER JOIN hgpqgijw_ventas.venta_productos ON venta_productos.id_subcategoria = venta_subcategoria.idSubcategoria
        Where id_categoria = ?
        ';
        return $this->_Read($query, $array);
    }

    function removeProducts($array){
        $query = "
        DELETE FROM hgpqgijw_ventas.listaproductos
        WHERE idListaProductos = ?
        ";

        return $this->_CUD($query, $array);
    }

    function add_archivo($array){

        return $this->_Insert([
            'table'  => "hgpqgijw_ventas.venta_files",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);

    }

  


  



}

class CerrarPedidos extends Pedidos{

   function createListGral($array){

        return $this->_Insert([

            'table'  => "hgpqgijw_ventas.CierrePedidos",
            'values' => $array['values'],
            'data'   => $array['data']
        
        ]);
   }

   function cerrar_pedido($array) {

        return $this->_Update([
            'table'  => "hgpqgijw_ventas.lista_productos",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
   }
   

   function getListID(){

         $sql = "

            SELECT
            CierrePedidos.idCierre,
            CierrePedidos.FechaCierre,
            CierrePedidos.Comentarios
            FROM hgpqgijw_ventas.CierrePedidos

            ORDER BY idCierre DESC
            LIMIT 1
            
            ";

        return $this->_Read($sql,null)[0];

   }

   function lsPedidosTerminados(){

        $sql = "
            SELECT
                lista_productos.idLista,
                lista_productos.folio,
                NombreCliente,
                date_format(foliofecha, '%Y-%m-%d') AS fecha,
                date_format(foliofecha, '%H:%i:%s') AS hora,
                id_Estado,
                id_cliente
            FROM
                hgpqgijw_ventas.cliente
            INNER JOIN hgpqgijw_ventas.lista_productos ON hgpqgijw_ventas.lista_productos.id_cliente = hgpqgijw_ventas.cliente.idCliente
            WHERE id_cierre is null
            ORDER BY
                idLista DESC
        ";

        return $this->_Read($sql,null);


   }

    function lsPedidosEnEspera(){

        $sql = "
        SELECT
            CierrePedidos.idCierre,
            CierrePedidos.FechaCierre,
            CierrePedidos.Comentarios,
            CierrePedidos.Numero_Pedidos,
            CierrePedidos.id_estado,
            CierrePedidos.folio
        FROM hgpqgijw_ventas.CierrePedidos    
        ";

        return $this->_Read($sql, null);

    }

    function lsPedidosPendientes($array){

        $sql = "
            SELECT
                lista_productos.idLista,
                lista_productos.id_Estado,
                lista_productos.folio,
                cliente.NombreCliente,
                lista_productos.id_cierre
            FROM
            hgpqgijw_ventas.lista_productos
            INNER JOIN hgpqgijw_ventas.cliente ON lista_productos.id_cliente = cliente.idCliente
            WHERE id_cierre = ?
        ";

        return $this->_Read($sql, $array);

    }




}

class Clientes extends CRUD {

    public $dia;

    public function __construct() {
        $this->dia = "hgpqgijw_dia.";
    }

    function listCategory(){
        $query = " SELECT
        idSubcategoria as id,
        Nombre_subcategoria as valor
        FROM hgpqgijw_dia.venta_subcategoria ";
        return $this->_Read($query, null);
    }

    function listClientes(){

            $sql = "SELECT 
                idCliente,Name_Cliente 
            FROM hgpqgijw_dia.clientes WHERE  estado_cliente = 1
            ";

            return $this->_Read($sql,null);

    }

    function listProductsBy($array){
        $query = "SELECT 
        idProducto as id,NombreProducto,Precio,Precio_Mayoreo,presentacion
         FROM hgpqgijw_dia.venta_productos WHERE id_subcategoria = ? ORDER BY presentacion asc";
        return $this->_Read($query,$array);
    }

    function getPreferentialPrice($array){
        $query = "
        SELECT 
            Costo 
        FROM hgpqgijw_dia.precio_especial 
        WHERE id_Cliente = ? AND id_Producto = ?";

        return $this->_Read($query, $array)[0];
         
    }




    




}




?>