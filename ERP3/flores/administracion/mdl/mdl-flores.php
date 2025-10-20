<?php
require_once('../../../conf/_CRUD.php');

class Flores extends CRUD{
function lsUDN(){
    $query ="SELECT idUDN AS id, UDN AS valor FROM udn WHERE Stado = 1";
    return $this->_Read($query, null);
}

function lsCategoria(){
    $query ="
    SELECT 
    idcategoria AS id,
    nombrecategoria AS valor
    FROM hgpqgijw_ventas.venta_categoria WHERE status = 1";
    return $this->_Read($query, null);
}    

function VerProductos(){
    $sql="
    SELECT
        NombreProducto,
        Costo,
        Venta,

        nombrecategoria,
        Stock_Inicial,
        Stock_Minimo,
        idProducto

    FROM
    hgpqgijw_ventas.venta_productos
    INNER JOIN hgpqgijw_ventas.venta_subcategoria ON hgpqgijw_ventas.venta_productos.id_subcategoria = hgpqgijw_ventas.venta_subcategoria.idSubcategoria
    INNER JOIN hgpqgijw_ventas.venta_categoria ON hgpqgijw_ventas.venta_subcategoria.id_categoria = hgpqgijw_ventas.venta_categoria.idcategoria
    ";
    $ps	=	$this->_Read($sql,null);
    return	$ps;
}



}
?>