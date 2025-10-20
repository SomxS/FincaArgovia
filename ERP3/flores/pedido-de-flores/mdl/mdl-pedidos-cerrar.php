<?php
require_once('../../../conf/_CRUD.php');

class cerrarPedidos extends CRUD {

function	Consultar_folio($array){

    $sql  ="
        SELECT
            lista_productos.idLista,
            lista_productos.folio,
            cliente.NombreCliente,
            date_format(foliofecha,'%Y-%m-%d') AS fecha,
            DATE_FORMAT(foliofecha, '%H:%i:%s') AS hora,
            cliente.NombreCliente,
            lista_productos.Detalles,
            estado_solicitud.EstadoSolicitud,
            lista_productos.id_Estado
        FROM
        hgpqgijw_ventas.cliente
        INNER JOIN hgpqgijw_ventas.lista_productos ON lista_productos.id_cliente = cliente.idCliente
        INNER JOIN hgpqgijw_ventas.estado_solicitud ON lista_productos.id_Estado = estado_solicitud.idEstado
        WHERE id_cierre IS NULL and id_Estado = 2
        ORDER BY foliofecha desc
    ";

    return 	 $this->_Read($sql,$array);
}    

function	row_data($array){
  
  $sql  = "
    SELECT
        listaproductos.idListaProductos,
        listaproductos.descripcion,
        listaproductos.cantidad,
        listaproductos.id_unidad,
        listaproductos.id_lista,
        venta_unidad.Unidad,
        listaproductos.observacion,
        listaproductos.costo,
        venta_productos.NombreProducto
    FROM
    hgpqgijw_ventas.listaproductos
    LEFT JOIN hgpqgijw_ventas.venta_unidad ON listaproductos.id_unidad = venta_unidad.idUnidad
    INNER JOIN hgpqgijw_ventas.venta_productos ON venta_productos.id_unidad = venta_unidad.idUnidad AND listaproductos.id_productos = venta_productos.idProducto

    WHERE  id_lista = ?
    ORDER BY idListaProductos asc";

    return 	 $this->_Read($sql,$array);
}

function list_flores($array){
    $sql = "
        SELECT 
         NombreProducto 
        FROM 
        hgpqgijw_ventas.venta_productos
        
    ";

    return 	 $this->_Read($sql,$array);
}


function crearHojaPedido($array){
    /* Crear cierre de pedido */ 
    $query	=	
    "INSERT INTO
        hgpqgijw_ventas.CierrePedidos
        (FechaCierre,Numero_Pedidos)
    VALUES(?,?)";
        
     $this->_CUD($query,$array);

    /* Regresar id creado */ 
    
    $query =
    "
    SELECT 
    idCierre
    FROM hgpqgijw_ventas.CierrePedidos
    ORDER BY idCierre DESC
    LIMIT 1
    ";   
   $sql = $this->_Read($query, null);

   foreach ($sql as $key ) {
     $id = $key['idCierre'];  
   }

    return $id;


}

function delete_($array){
    $query	=	"
        DELETE FROM tabla
        WHERE id = ?
    ";
    return $this->_CUD($query,$array);
}

 function cerrarPedido($array) {
  $query = '
    UPDATE 
    hgpqgijw_ventas.lista_productos
    SET fecha_cierre   = ?,
    id_cierre = ?

    WHERE  idLista = ? ';

    return $this->_CUD($query,$array);
 }




}

?>