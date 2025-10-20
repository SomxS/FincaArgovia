<?php
require_once('../../../conf/_CRUD.php');

class mdl extends CRUD {

function select_($array){
    $sql="";

    return 	 $this->_Read($sql,$array);
}

function delete_($array){
    $query	=	"
        DELETE FROM tabla
        WHERE id = ?
    ";
    return $this->_CUD($query,$array);
}

 function add($array) {
    $query = "
    INSERT INTO 
    table
    (,,,) 
    VALUES (?,?,?,?)
    ";
    return $this->_CUD($query,$array);
 }

function VerFormatos($array){

  $sql  ="
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

    return 	 $this->_Read($sql,$array);
 }



function	row_data($array){
    $sql="
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


    return 	 $this->_Read($sql,$array);
}



 function ver_folio($array){
  $key = 0;
  $sql="
  SELECT
  date_format(foliofecha,'%Y-%m-%d') as f,
  id_cliente,
  folio,
  idLista,
  id_Estado,
  Detalles
  FROM
  hgpqgijw_ventas.lista_productos
  WHERE idLista=?";

  return $this->_Read($sql,$array);

 
 }

 function ls_complemento($array){
   
   $query = "

    SELECT
        productos_adicionales.idInsumo,
        productos_adicionales.Nombre_insumo,
        productos_adicionales.cantidad,
        productos_adicionales.Precio,
        venta_productos.NombreProducto
    FROM
    hgpqgijw_ventas.productos_adicionales
    INNER JOIN hgpqgijw_ventas.venta_productos 
    ON productos_adicionales.id_producto_adicional = venta_productos.idProducto
    WHERE idInsumo = ?
   ";
    return $this->_Read($query,$array);
 }


}

?>