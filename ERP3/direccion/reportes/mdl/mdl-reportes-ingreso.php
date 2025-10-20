<?php
require_once('../../../conf/_CRUD.php');

class Reportesingreso extends CRUD{
    function lsUDN(){
        $query ="SELECT idUDN AS id, UDN AS valor FROM udn WHERE Stado = 1";
        return $this->_Read($query, null);
    }

    function VER_CATEGORIAS($array){
        $sql = "SELECT
                categoria.idCategoria,
                categoria.Categoria,
                categoria.id_TMovimiento
            FROM
                hgpqgijw_finanzas.categoria
            WHERE
                id_UDN = ? and idCategoria <> 12 and idCategoria <> 13";
        return $this->_Read($sql, $array);
    }

    function Select_group($array){
        $sql="SELECT
                grupo.idgrupo,
                grupo.gruponombre
            FROM
                hgpqgijw_finanzas.grupo
                INNER JOIN hgpqgijw_finanzas.subcategoria ON subcategoria.id_grupo = grupo.idgrupo
            WHERE 
                id_Categoria = ? and Stado = 1
            GROUP BY grupo.idgrupo";
        return $this->_Read($sql, $array);
    }

 function Select_Subcategoria_x_grupo($array){
  $query = "SELECT
                idSubcategoria,
                Subcategoria,
                id_grupo 
            FROM
                hgpqgijw_finanzas.subcategoria 
            WHERE
                id_Categoria = ? 
                AND Stado = 1 
                AND id_grupo = ? 
            ORDER BY
                idSubcategoria ASC";
   return $this->_Read($query, $array);
 }

  function Select_formaspago_by_categoria($array){
  $query = "SELECT idFormas_Pago,FormasPago FROM hgpqgijw_finanzas.formas_pago WHERE grupo = ?";
  return $this->_Read($query, $array);

 }

 function ver_reporte_mensual($array){
    $monto = 0;
  $sql="
  SELECT
      SUM(Monto) as s_monto,
      bitacora_formaspago.id_FormasPago
  FROM
      hgpqgijw_finanzas.folio
      INNER JOIN hgpqgijw_finanzas.bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
      INNER JOIN hgpqgijw_finanzas.bitacora_formaspago ON bitacora_formaspago.id_Bitacora = bitacora_ventas.idVentasBit
  WHERE
   MONTH(folio.Fecha) = ?
  AND YEAR(folio.Fecha) = ?
  AND
  bitacora_ventas.id_Subcategoria = ? and id_FormasPago = ?
  ";

  $ps	=	$this->_Read($sql, $array);
  foreach($ps as $key){
      $monto = $key['s_monto'];
  }

  return	$monto;
}

}
?>