<?php
require_once('../../../conf/_CRUD.php');

class Ingresodiarios extends CRUD{
function lsUDN(){
$query ="SELECT idUDN AS id, UDN AS valor FROM udn WHERE Stado = 1";
return $this->_Read($query, null);
}

function list_tabla_file(){
    $query ="SELECT Archivo,ROUND(Peso,2),Hora,Type_File,Ruta,Fecha 
    FROM hgpqgijw_finanzas.sobres WHERE MONTH(Fecha)= 10 AND YEAR(Fecha) = 2023 ORDER BY Fecha asc";
    return $this->_Read($query, null);
}

function ver_ingresos_turismo($array){

    $query = "
        SELECT
            SUM(Monto) as monto,
            bitacora_ventas.id_Subcategoria,
            bitacora_ventas.idVentasBit,
            bitacora_formaspago.id_Bitacora,
            subcategoria.id_Categoria,
            subcategoria.Subcategoria
        FROM
            hgpqgijw_finanzas.folio
        INNER JOIN hgpqgijw_finanzas.bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
        INNER JOIN hgpqgijw_finanzas.bitacora_formaspago ON bitacora_formaspago.id_Bitacora = bitacora_ventas.idVentasBit
        INNER JOIN hgpqgijw_finanzas.subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        WHERE
        folio.Fecha BETWEEN ? AND ?
        and id_Categoria = ?
    ";


    $sql = $this->_Read($query, $array);
    foreach($sql as $row3);
    if ( !isset($row3['monto']) ) { $row3['monto'] = 0; }
    return $row3['monto'];
}

function Select_TC_Data($date1,$date2){
    $array = array($date1,$date2);
    $query = "SELECT Monto,TCodigo,
    TTCodigo,Concepto,Especificacion,
    Cliente,Autorizacion,Observaciones,Fecha,idTC
     FROM hgpqgijw_finanzas.tc,hgpqgijw_finanzas.terminal,hgpqgijw_finanzas.tipo_terminal,hgpqgijw_finanzas.folio 
     WHERE idTipoTerminal = id_TipoTerminal 
     AND idTerminal = id_Terminal 
     AND id_Folio = idFolio 
     AND Fecha BETWEEN ? AND ?";
   
    return $this->_Read($query, $array);
  }



  // reporte general
function VER_CATEGORIAS($array){

   $rs = "";
 

   $sql = "
   SELECT

   categoria.idCategoria,
   categoria.Categoria,
   categoria.id_TMovimiento

   FROM

   hgpqgijw_finanzas.categoria

   WHERE
   id_UDN = ? and idCategoria <> 12 and idCategoria <>13";

   return $this->_Read($sql, $array);
}


}
?>