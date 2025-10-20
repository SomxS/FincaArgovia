<?php
require_once('../../../conf/_CRUD.php');

class mdl extends CRUD{

    function lsUDN(){
        $query ="SELECT idUDN AS id, UDN AS valor FROM udn WHERE Stado = 1";
        return $this->_Read($query, null);
    }


    function	lsFolio($array){
        $sql  ="
            SELECT
                idCierre,
                DATE_FORMAT(FechaCierre,'%Y-%m-%d') as FechaCierre,
                Numero_Pedidos as NoPedidos
            FROM
            hgpqgijw_ventas.CierrePedidos
            WHERE  DATE_FORMAT(FechaCierre,'%Y-%m-%d') Between  ? and ?
        ";
        return 	 $this->_Read($sql,$array);
    }

    function lsFoliosCierre($array){
    $sql  ="
    SELECT
        lista_productos.idLista as id,
        lista_productos.idLista,
        lista_productos.folio,
        cliente.NombreCliente,
        lista_productos.id_Estado,
        estado_solicitud.EstadoSolicitud,
        DATE_FORMAT(foliofecha,'%Y-%m-%d') as fecha,
        lista_productos.foliofecha,
        lista_productos.id_cierre
    FROM
        hgpqgijw_ventas.lista_productos
    INNER JOIN hgpqgijw_ventas.estado_solicitud ON lista_productos.id_Estado = estado_solicitud.idEstado
    INNER JOIN hgpqgijw_ventas.cliente ON lista_productos.id_cliente = cliente.idCliente
    WHERE id_Estado = 2 and id_cierre = ?
    ";
    return 	 $this->_Read($sql,$array);
    }
    
    function lsDataTicket($array) { 

        $sql  = "
            SELECT
                CierrePedidos.idCierre,
                CierrePedidos.FechaCierre,
                CierrePedidos.Numero_Pedidos,
                estado_pedido,
                CierrePedidos.folio
            FROM
                hgpqgijw_ventas.CierrePedidos
            INNER JOIN hgpqgijw_ventas.EstadoPedido ON 
            CierrePedidos.id_estado = EstadoPedido.idEstadoPedido        
            WHERE idCierre = ?
            ";


        return 	 $this->_Read($sql,$array);
    }

       function row_data($array) { 

        $sql  = "
        SELECT
         venta_productos.NombreProducto,
        SUM( cantidad) as cantidad
        FROM
        hgpqgijw_ventas.CierrePedidos
        INNER JOIN hgpqgijw_ventas.lista_productos ON lista_productos.id_cierre = CierrePedidos.idCierre
        INNER JOIN hgpqgijw_ventas.listaproductos  ON listaproductos.id_lista = lista_productos.idLista
        INNER JOIN hgpqgijw_ventas.venta_productos ON listaproductos.id_productos = venta_productos.idProducto
        WHERE idCierre = 1
        GROUP BY id_productos
        ORDER BY NombreProducto ASC
            ";


        return 	 $this->_Read($sql,$array);
    }

    function row_data_cliente($array){
    $sql="
    SELECT
        listaproductos.idListaProductos as id,
        venta_productos.NombreProducto,
        listaproductos.descripcion,
        listaproductos.cantidad,
        listaproductos.id_unidad,
        listaproductos.id_lista,
        venta_unidad.Unidad,
        listaproductos.costo,
        (listaproductos.costo * cantidad) as total,
        listaproductos.observacion
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





}
?>