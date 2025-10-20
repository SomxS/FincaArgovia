<?php
require_once('../../../conf/_CRUD.php');

class Pedidoflores extends CRUD {
    function now(){
        return $this->_Read('SELECT NOW()',null);
    }

   function pedido_activo($array){
    $sql="
        SELECT
        date_format(foliofecha,'%Y-%m-%d') as fecha,
        NombreCliente,
        folio,
        idLista,
        id_Estado
        FROM
        hgpqgijw_ventas.lista_productos
        INNER JOIN hgpqgijw_ventas.cliente ON lista_productos.id_cliente = cliente.idCliente
        WHERE id_Estado = 1";

    return 	 $this->_Read($sql,$array);
 
   }


    function	get_id_cliente($array){
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
    $sql = $this->_Read($query,null);
      $folio = count($sql);
    foreach($sql as $row){
      $folio = $row['total'];
    }

   return $folio;
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

      observacion,
      costo

      FROM
      hgpqgijw_ventas.listaproductos
      LEFT JOIN hgpqgijw_ventas.venta_unidad
      ON listaproductos.id_unidad = venta_unidad.idUnidad
      WHERE  id_lista = ?
      ORDER BY idListaProductos asc";


      return 	 $this->_Read($sql,$array);
    }

   function	Group_destino(){
      $query="
      SELECT
      NombreCliente
      FROM
      hgpqgijw_ventas.cliente WHERE estado_cliente = 1";
      return  $this->_Read($query,null);
      
   }

  function	VerFormatos($array){

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
    
   # Sugerencia de 15-92 -------
    function _INSERT($array,$campos,$bd){
    $data ='';
    $x    ='';
    $query ='';
    $query = $query."INSERT INTO ".$bd."(" ;

    for ($i=0; $i < count($campos); $i++) {
    $data = $data."".$campos[$i].",";
    $x    = $x.'?,';
    }

    $data  = substr($data,0,strlen($data)-1);
    $x     = substr($x,0,strlen($x)-1);

    $query = $query.$data.") VALUES (".$x.")";
    return $this->_CUD($query,$array);

    // return $query;
    }

    function _UPDATE($array,$campos,$bd,$where){
    $set       = '';
    $Condicion = '';
    $query     = '';

    $query .= " UPDATE  ".$bd." SET " ;

    for ($i=0; $i < count($campos); $i++) {
     $set .= $campos[$i]."=?, ";
    }

    $set  = substr($set,0,strlen($set)-2);


    for ($i=0; $i < count($where); $i++) {
     $Condicion .= $where[$i]." = ? and ";
    }

    $Condicion     = substr($Condicion,0,strlen($Condicion)-4);
    $query .= $set." WHERE ".$Condicion;

    return $this->_CUD($query,$array);


    // return $query;
   }

   # ---------------------------







   function list_cliente(){
   $query = "SELECT * FROM hgpqgijw_ventas.cliente WHERE estado_cliente=1 OR estado_cliente=0 order by idCliente DESC";
    return 	 $this->_Read($query,null);
  }
}
?>