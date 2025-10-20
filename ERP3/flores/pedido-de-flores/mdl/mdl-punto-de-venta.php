<?php
require_once('../../../conf/_CRUD.php');

class Puntodeventa extends CRUD{

function lsCat(){
   $query ="SELECT idcategoria as id, nombrecategoria as valor FROM hgpqgijw_ventas.venta_categoria;";
   return $this->_Read($query, null);
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


function lsProductos(){
    $query = "SELECT
    -- idProducto as id,
    NombreProducto 
    FROM
    hgpqgijw_ventas.venta_productos";
    
    return $this->_Read($query, null);

}


function lsSubCat($array){
   $query ="SELECT * FROM hgpqgijw_ventas.venta_subcategoria WHERE id_categoria = ?";
   return $this->_Read($query, $array);
}

function lsFolio($array){
   $query ="SELECT *  FROM hgpqgijw_ventas.lista_productos WHERE id_Estado = 1";
   return $this->_Read($query, $array);
}

function get_id_cliente($array){
    $id_Proveedor = '';

    $sql="
    SELECT
    idCliente
    FROM
    hgpqgijw_ventas.cliente WHERE NombreCliente = ? ";
    $ps	=	$this->_Read($sql,$array);
    foreach($ps as $key){
    $id_Proveedor = $key['idCliente'];
    }
    return	$id_Proveedor;
}

function NumFolio(){

    $folio = 5;
    $query = "SELECT count(*) as total FROM hgpqgijw_ventas.lista_productos WHERE id_Estado = 2";
    $sql   = $this->_Read($query,null);
    $folio = count($sql);

    foreach($sql as $row){
        $folio = $row['total'];
    }

    return $folio;
}

function list_productos($array){
   $query ="SELECT
   venta_productos.idProducto,
   venta_productos.NombreProducto,
   venta_productos.Costo,
   venta_productos.url_image,
   venta_productos.id_subcategoria
   FROM
   hgpqgijw_ventas.venta_productos WHERE id_subcategoria = ? ";
   return $this->_Read($query,$array);
}

 function	DeleteMovimiento($array){
  $query	=	"
  DELETE FROM hgpqgijw_ventas.listaproductos
  WHERE idListaProductos = ?
  ";

   return $this->_CUD($query,$array);
 }
  

function crear_pedido($array){
    $query	=	
    "INSERT INTO
    hgpqgijw_ventas.lista_productos
    (id_cliente,foliofecha,id_Estado,folio)
    VALUES(?,?,?,?)";
        
        return $this->_CUD($query,$array);
}

 function ver_folio($array){
 
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

function update_ticket($array) {
$query = '
UPDATE 
hgpqgijw_ventas.lista_productos
SET id_Estado   = ?

WHERE  idLista = ? ';

return $this->_CUD($query,$array);
}

function get_id($array){
    $id = 0;

    $query =
    "
    SELECT 
    idProducto
    FROM hgpqgijw_ventas.venta_productos
    WHERE NombreProducto = ?
    ";   
   $sql = $this->_Read($query, $array);

   foreach ($sql as $key ) {
     $id = $key['idProducto'];  
   }

    return $id;

}

function _get_producto($array){
    $precio = 0;

    $query =
    "
    SELECT 
    Venta
    FROM hgpqgijw_ventas.venta_productos
    WHERE NombreProducto = ?

    ";
    $sql = $this->_Read($query, $array);
    
    foreach ($sql as $key ) {
      $precio = $key['Venta'];  
    }

    return $precio;
    
}

/* --------------------------
 >> agregar-producto
----------------------------*/

function get_producto_vendido($array){
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

    $sql = $this->_Read($query, $array);

    return $sql;    
}

function update_producto_vendido($array) {
    $query = '
    UPDATE 
    hgpqgijw_ventas.listaproductos
    SET cantidad   = ?,
    total = ?

    WHERE  idListaProductos = ? ';

    return $this->_CUD($query,$array);
}

function add_producto($array) {

    $query = "INSERT INTO hgpqgijw_ventas.listaproductos
    (id_lista,id_productos,cantidad,costo,total,id_unidad) VALUES (?,?,?,?,?,?)";


    return $this->_CUD($query,$array);
}


/* --------------------------
 >> Cancelar Pedido
----------------------------*/
function cancelar_pedido($array){
   
    $query	=	"
    DELETE FROM 
    hgpqgijw_ventas.lista_productos
    WHERE 
    idLista = ?
    ";

    return $this->_CUD($query,$array);
}



/* --------------------------
 >> Complementos
----------------------------*/
function agregar_complemento($array){
    $query	=	
    "INSERT INTO
    hgpqgijw_ventas.productos_adicionales
    (id_referencia,id_producto_adicional,cantidad,Nombre_insumo)
    VALUES(?,?,?,?)";

    return $this->_CUD($query,$array);
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

   ";
   
   return $this->_Read($query,$array);
}

function ls_complemento_id_producto($array){
   
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
   
   return $this->_Read($query,$array);
}

 function	quitar_complemento($array){
  $query	=	"
  DELETE FROM hgpqgijw_ventas.productos_adicionales
  WHERE idInsumo = ?
  ";

   return $this->_CUD($query,$array);
 }

}
?>