<?php

require_once('../../conf/_CRUD.php');

class Puntodeventa extends CRUD{
    
    public $bd;
    
    public function __construct() {
        $this->bd = "hgpqgijw_dia2.";
    }

    
    function update_ticket($array){
        $query = "
        
        UPDATE
            {$this->bd}lista_folio
        SET id_Estado   = ?

        WHERE  idLista = ? ";

        return $this->_CUD($query, $array);
    }




    function lsUDN(){
        $query ="SELECT idUDN AS id, UDN AS valor FROM udn WHERE Stado = 1";
        return $this->_Read($query, null);
    }

    function lsTipo() {
        $query = "SELECT idtipo as id, tipo as valor FROM {$this->bd}tipo WHERE idtipo <> 3";
        return $this->_Read($query, null);
    }

    function getTypeSales() {
        $query = "SELECT idTipo as id, tipo_venta as valor FROM {$this->bd}tipo_venta ";
        return $this->_Read($query, null);
    }

    function lsGroup(){

        $query = " 
            SELECT  
                idcategoria as id,
                nombrecategoria as valor
            FROM 
                {$this->bd}venta_categoria
        ";
       
        return $this->_Read($query, null);
       
    }

    function getClients(){

        $query = " 
           SELECT idCliente as id,
           Name_Cliente as valor
           FROM {$this->bd}clientes
           WHERE estado_cliente = 1
        ";
        
        return $this->_Read($query, null);
    }


    function lsSubgroup(){

        $query = "
        SELECT
            venta_subcategoria.idSubcategoria AS id,
            venta_subcategoria.Nombre_subcategoria AS valor,
            venta_subcategoria.id_categoria,
            venta_categoria.nombrecategoria
        FROM
        {$this->bd}venta_subcategoria
        INNER JOIN {$this->bd}venta_categoria ON venta_subcategoria.id_categoria = venta_categoria.idcategoria

        ";

        return $this->_Read($query, null);
    }

    function listProductsBy($array){
        $query = "SELECT
        idProducto as id,CONCAT(NombreProducto,' ',presentacion) as valor,NombreProducto,Status,Precio as costo,Precio_Mayoreo,presentacion
        FROM {$this->bd}venta_productos WHERE id_subcategoria = ? ORDER BY idProducto desc";
        return $this->_Read($query, $array);
    }

    function getProduct($array){
        $query = "SELECT *   FROM {$this->bd}venta_productos WHERE idProducto = ? ";
        return $this->_Read($query, $array)[0];
    }

    function getTickets(){
        // $query ="
        // SELECT
        //     lista_folio.idLista as id,
        //     CONCAT(folio,' - ',fecha) as valor
        
        // FROM
        // {$this->bd}lista_folio
        // WHERE id_Estado = 0
        // ";

        $query = "

            SELECT
            idLista as id,
            folio,
            CONCAT(Name_Cliente, ' - ', fecha) as valor,
            fecha,
            Name_Cliente
            FROM
            {$this->bd}lista_folio
            INNER JOIN {$this->bd}clientes ON lista_folio.elaboro = clientes.idCliente
            WHERE id_Estado = 0
        
        ";


        return $this->_Read($query, null);
    }


    function updateEstatus($array){

        return $this->_Update([

            'table'  => "{$this->bd}venta_productos",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data'],
        
        ]);
    }

    function updateProducts($array){

        return $this->_Update([

            'table' => "{$this->bd}venta_productos",
            'values' => $array['values'],
            'where' => $array['where'],
            'data' => $array['data'],

        ],true);
    }





    // Clients.


    public function listClientes(){

        $sql = "SELECT idCliente,Name_Cliente FROM {$this->bd}clientes WHERE  estado_cliente = 1 ORDER BY idCliente desc";

        return $this->_Read($sql, null);

    }

    public function getPreferentialPrice($array){
        
        $query = "

        SELECT
            Costo
        FROM 
            {$this->bd}precio_especial
        WHERE 
        id_Cliente = ? 
        AND id_Producto = ? ";

        return $this->_Read($query, $array)[0];

    }

    // Operations.

    function addClients($array){

        return $this->_Insert([

            'table'  => "{$this->bd}clientes",
            'values' => $array['values'],
            'data'   => $array['data'],
        
        ]);
    
    }

    function Select_idPrecioEspecial($array){
        $row   = null;
        $query = "SELECT idEspecial FROM {$this->bd}precio_especial WHERE id_Cliente = ? AND id_Producto = ?";
        $sql   = $this->_Read($query, $array);
        foreach ($sql as $row);
        if (!isset($row['idEspecial'])) {$row['idEspecial'] = 0;}
        return $row['idEspecial'];
    }


    function Insert_PrecioEspecial($array){

        return $this->_Insert([
            'table'  => "{$this->bd}precio_especial",
            'values' => $array['values'],
            'data'   => $array['data'],
        ],true);
    }
    
    function Update_PrecioEspecial($array){

        return $this->_Update([
            'table'  => "{$this->bd}precio_especial",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data'],
        ], true);
    }






}

class Ventas extends Puntodeventa{
    
    public function __construct() {
         parent::__construct(); // Llama al constructor de la clase padre
    }

    function  list_ticketALL($array) {


        $sql  = "
            SELECT
            lista_folio.idLista as id,
            lista_folio.folio,
            Name_Cliente,
            date_format(fecha,'%Y-%m-%d') as fecha,
            Total,

            id_DatosFactura,
            id_tipo,
            idFactura,
            Mixto,
            Efectivo,

            TDC,
            Credito,
            id_Estado

            FROM
            {$this->bd}lista_folio
            INNER JOIN {$this->bd}clientes ON lista_folio.elaboro = clientes.idCliente
            WHERE
            lista_folio.id_Estado IN (1,2)

            and DATE_FORMAT(fecha,'%Y-%m-%d') Between  ? and ?  GROUP BY idLista
            order by idLista desc
        ";

        return   $this->_Read($sql, $array);
    }

    function  list_ticket($array) {


        $sql  = "
            SELECT
            lista_folio.idLista as id,
            lista_folio.folio,
            Name_Cliente,
            date_format(fecha,'%Y-%m-%d') as fecha,
            Total,

            id_DatosFactura,
            id_tipo,
            idFactura,
            Mixto,
            Efectivo,

            TDC,
            Credito,
            id_Estado

            FROM
            {$this->bd}lista_folio
            INNER JOIN {$this->bd}clientes ON lista_folio.elaboro = clientes.idCliente
            WHERE
            lista_folio.id_Estado = ?

            and DATE_FORMAT(fecha,'%Y-%m-%d') Between  ? and ?  GROUP BY idLista
            order by idLista desc
        ";

        return   $this->_Read($sql, $array);
    }

    function getProductsBY($array){

        $query = "
        SELECT
            venta_productos.NombreProducto,
            lista_productos.cantidad,
            lista_productos.costo,
            ROUND((cantidad*costo),2) as total,
            lista_productos.precio_especial,
            presentacion,
            lista_productos.idListaProductos,
            nota
        FROM
        {$this->bd}lista_folio
        INNER JOIN {$this->bd}lista_productos ON lista_productos.id_lista = lista_folio.idLista
        INNER JOIN {$this->bd}venta_productos ON lista_productos.id_productos = venta_productos.idProducto
        WHERE id_lista = ? and id_Canasta is null";

        return $this->_Read($query, $array);
   
    }

    function getListFolioByID($array){

        $sql = "
            SELECT
                lista_folio.Mixto,
                lista_folio.Efectivo,
                lista_folio.TDC,
                lista_folio.Credito,

                lista_folio.id_DatosFactura,
                lista_folio.idFactura,
                lista_facturas.folio,
                lista_folio.fecha,

                clientes.Name_Cliente,
                lista_folio.folio,
                id_tipo

            FROM
                {$this->bd}lista_folio
                LEFT JOIN {$this->bd}clientes ON lista_folio.elaboro = clientes.idCliente
                LEFT JOIN {$this->bd}lista_facturas ON lista_folio.idFactura = lista_facturas.idFactura
            WHERE
                idLista = ? ";

        return $this->_Read($sql, $array)[0];
        
    }

}

class Clientes extends CRUD {

    public $dia;

    public function __construct(){
        $this->dia = "{$this->bd}";
    }

    public function listCategory(){
        $query = " SELECT
        idSubcategoria as id,
        Nombre_subcategoria as valor
        FROM {$this->bd}venta_subcategoria ";
        return $this->_Read($query, null);
    }

    public function listClientes(){

        $sql = "SELECT
                idCliente,Name_Cliente
            FROM {$this->bd}clientes WHERE  estado_cliente = 1
            
            ";

        return $this->_Read($sql, null);

    }

    public function listProductsBy($array){
        $query = "SELECT
        idProducto as id,
        NombreProducto,Precio,Precio_Mayoreo,presentacion
         FROM {$this->bd}venta_productos WHERE id_subcategoria = ? ORDER BY presentacion asc";
        return $this->_Read($query, $array);
    }

 

}

class Pos extends Puntodeventa {

    public function __construct() {
        parent::__construct(); // Llama al constructor de la clase padre
        $this->util = new Utileria(); 
    }

    function Select_Precio_Especial($array){
        $query = "SELECT Costo FROM hgpqgijw_dia.precio_especial WHERE id_Cliente = ? AND  id_Producto = ?";
        return $this->_Read($query,$array)[0];
   
    }
    
    function getNewFolio(){
        $query = "
            SELECT
                idLista

            FROM {$this->bd}lista_folio
            WHERE id_Estado = 0
            ORDER BY idLista DESC LIMIT 1
        ";
        return $this->_Read($query, null);
    }

    function getFolioByID($array){

        $sql="
            SELECT
                lista_folio.fecha,
                clientes.idCliente,
                lista_folio.folio,
                lista_folio.idLista,
                lista_folio.id_Estado,
                clientes.Name_Cliente
            FROM
                {$this->bd}lista_folio
            INNER JOIN {$this->bd}clientes ON lista_folio.elaboro = clientes.idCliente

            WHERE idLista= ?

            
            ";

        return $this->_Read($sql,$array);


    }

    function getProductsByFolio($array){
        $sql = "
            SELECT
                lista_productos.idListaProductos as id,
                venta_productos.NombreProducto,
                CONCAT(NombreProducto,' ',presentacion) as valor,
                lista_productos.costo,
                lista_productos.cantidad,
                ROUND((cantidad*costo), 2) as total,
                lista_productos.nota,
                id_productos,
                lista_productos.precio_especial,
                lista_productos.id_Canasta,
                lista_productos.id_lista
            FROM
            {$this->bd}venta_productos
            INNER JOIN {$this->bd}lista_productos ON lista_productos.id_productos = venta_productos.idProducto
            where id_lista = ?
            ORDER BY idListaProductos DESC
        ";
      

        return $this->_Read($sql, $array);
        
    }

    function getProductsByID($array){

        $query ="
        SELECT
            idListaProductos,
            cantidad,
            costo
        FROM
        {$this->bd}lista_productos
        WHERE id_lista = ? and id_productos = ?

        ";

        return $this->_Read($query, $array);

    }

   

    // Operations.

    function setFolio($array){

        return $this->_Insert([
            'table' => "{$this->bd}lista_folio",
            'values' => $array['values'],
            'data' => $array['data'],
        ]);
    }

    function addProduct($array){

        return $this->_Insert([
            'table'  => "{$this->bd}lista_productos",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function setTickets($array) {

        return $this->_Update([
            'table'  => "{$this->bd}lista_folio",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function removeProducts($array){
        $query = "
        DELETE FROM {$this->bd}lista_productos
        WHERE idListaProductos = ?
        ";

        return $this->_CUD($query, $array);
    }

    function setListCortesia($array) {

        return $this->_Update([
            'table'  => "{$this->bd}lista_productos",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function updateCantidad($array) {

        return $this->_Update([
            'table'  => "{$this->bd}lista_productos",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }



    // Consultas de almacen.

    function Select_Movimientos_Productos($idProducto){

        $entrada  = $this->Select_Entrada_Productos([$idProducto]);
        // $salida   = $this->Select_Salida_Productos($idProducto,1);
        // $temporal = $this->Select_Salida_Productos($idProducto,0);
        // $canasta  = $this->Select_Movimientos_Canasta($idProducto);
        // $actual   = $entrada - $salida - $temporal - $canasta;
        return $entrada['entrada'];
    }

    function Select_Entrada_Productos($array){

        $query = "SELECT SUM(Inventario_total) as total FROM {$this->bd}almacen_producto WHERE id_Producto = ?";
        return $this->_Read($query,$array)[0];
       
    }


    function Select_Salida_Productos($array){
        $query = "
            SELECT
            SUM(cantidad) as total,
            id_productos,
            id_Estado
            FROM
            {$this->bd}lista_folio
            INNER JOIN {$this->bd}lista_productos ON lista_productos.id_lista = lista_folio.idLista
            WHERE id_productos = ? and id_Estado = ?
        ";
        return $this->_Read($query,$array)[0];
       
    }


    function Select_Movimientos_Canasta($idProducto){
        $almacen = 0;
        $row = null;
      
        $query = "
            SELECT
                idCanasta,
                Stado,
                id_Producto
            FROM
            {$this->bd}venta_canasta
            INNER JOIN {$this->bd}canasta_productos ON canasta_productos.id_Canasta = venta_canasta.idCanasta
            WHERE Stado = 1
            GROUP BY id_Canasta ";

        $sql = $this->_Read($query, $array);
                
        foreach ($sql as $row) {
        

            $array = array($row['idCanasta'], $idProducto);
         
            $query = "SELECT 
            COUNT(idCanasta_Producto) as canastas
            FROM {$this->bd}canasta_productos 
            WHERE id_Canasta = ? AND id_Producto = ?";
        
            // $lsCanasta = $this->_Read($query, $array);

            // $cantidad_producto_canasta = $lsCanasta[0]['canastas'];

            // foreach ($lsCanasta as $key);



 

        //     $entrada = $this->Select_Canasta_Entrada_Inventario($idCanasta);
        //     $salida = $this->Select_Canasta_Salida_Inventario($idCanasta, 1);
        //     $temporal = $this->Select_Canasta_Salida_Inventario($idCanasta, 0);
        //     $actual = $entrada - $salida - $temporal;
        //     $almacen = $almacen + ($cantidad_producto_canasta * $actual);

        }

        // return $almacen;
    }

    
  

  

}



?>
