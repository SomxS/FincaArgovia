
<?php

require_once('../../conf/_CRUD.php');

class Inventary extends CRUD{
   
    public $bd;
    
    public function __construct() {
        $this->bd = "hgpqgijw_dia.";
    }

    function lsInventary($array){

        $query = "
            SELECT
                idProducto as id,
                NombreProducto,
                presentacion,
                precio,
                min_inventario,
                min_mayoreo,
                precio_mayoreo
            
            FROM
                {$this->bd}venta_productos
            WHERE
            id_subcategoria = ? 
            AND STATUS = 1
            AND id_tipo =1
        
            ORDER BY
            NombreProducto ASC
               
       ";

      return $this->_Read($query, $array);

    }

    function getAlmacenByID($array){

        $query = " SELECT
            Fecha_Inventario,
            Inventario_total
            FROM
            {$this->bd}almacen_producto
            WHERE
            id_Producto = ?
            AND Fecha_Inventario <= NOW()
            ORDER BY
            Fecha_Inventario DESC
            LIMIT 1 

             ";

      return $this->_Read($query, $array)[0];

    }

    function getEntradaByID($idProducto) {
        $query = "SELECT SUM(Inventario_total) AS total 
                FROM {$this->bd}almacen_producto 
                WHERE Fecha_Inventario <= NOW() 
                AND id_Producto = ?";
        
        return $this->_Read($query, $idProducto);
       
    }

    function getEntradaByIDx($idProducto) {
        $query = "
        SELECT
        SUM(Inventario_total) AS total,
        venta_productos.NombreProducto,
        venta_productos.presentacion,
        venta_productos.id_subcategoria,
        venta_subcategoria.Nombre_subcategoria

        FROM
        {$this->bd}almacen_producto
        INNER JOIN {$this->bd}venta_productos ON almacen_producto.id_Producto = venta_productos.idProducto
        INNER JOIN {$this->bd}venta_subcategoria ON venta_productos.id_subcategoria = venta_subcategoria.idSubcategoria
        WHERE Fecha_Inventario <= ?
        AND id_Producto = ?
        
        ";
        
        return $this->_Read($query, $idProducto);
       
    }

     function getSalidaByIDx($idProducto) {
        $query = "
           SELECT
            SUM(cantidad) as total,
            lista_folio.fecha
            FROM
            {$this->bd}lista_folio
            INNER JOIN {$this->bd}lista_productos ON lista_productos.id_lista = lista_folio.idLista
            WHERE  fecha <= ? AND id_productos = ?
        
        ";
        
        return $this->_Read($query, $idProducto);
       
    }



    function getSalidaByID($idProducto) {
        $query = "SELECT
        SUM(cantidad) as total,
        lista_folio.fecha
        FROM
        {$this->bd}lista_folio
        INNER JOIN {$this->bd}lista_productos ON lista_productos.id_lista = lista_folio.idLista
        WHERE  fecha <= NOW() AND id_productos = ?
        ";
        
        return $this->_Read($query, $idProducto);
        
    }

    function listSubCategoria(){

        $query = "
            SELECT
                idSubcategoria as id,
                Nombre_subcategoria
            FROM
            {$this->bd}venta_subcategoria

       ";

        return $this->_Read($query, null);


    }
    
    

    

   
    

}