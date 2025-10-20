<?php
require_once('../../../conf/_CRUD.php');

class Mantenimientoalmacen extends CRUD{
function lsUDN(){
$query ="SELECT idUDN AS id, UDN AS valor FROM udn WHERE Stado = 1";
return $this->_Read($query, null);
}

 function lsZona(){
  $query="SELECT idzona as id, zona as valor  FROM hgpqgijw_usuarios.zona";
  return $this->_Read($query, null);
 }

function list_equipos($array){
   
    $query = "
    SELECT

    CodigoEquipo,
    Nombre_Equipo,
    Nombre_Area,
    hgpqgijw_usuarios.udn.UDN,
    idAlmacen,

    cantidad,
    Abreviatura,
    nombrecategoria,
    ruta,
    id_categoria,

    Costo,
    (cantidad * costo) as total,
    date_format(FechaIngreso,'%Y-%m-%d') as fecha_i,
    TiempodeVida,
    EstadoProducto,

    Observaciones,
    zona,
    rutaImagen,
    nombreProveedor
    FROM
    hgpqgijw_operacion.mtto_almacen,
    hgpqgijw_operacion.mtto_almacen_area,
    hgpqgijw_operacion.mtto_almacen_equipos,
    hgpqgijw_usuarios.udn,
    hgpqgijw_usuarios.zona,
    hgpqgijw_operacion.mtto_categoria,
    hgpqgijw_operacion.mtto_proveedores
    WHERE idUDN = UDN_Almacen and
    idArea = Area and
    idEquipo = Equipo and
    id_categoria=idcategoria and
    id_zona       = idzona  AND
    id_proveedor  =idProveedor AND

    Estado= ? 
    AND id_zona = ?
    ORDER BY idAlmacen desc
    LIMIT 80
    ";

    return $this->_Read($query, $array);
}

function lsarea(){
  $query="SELECT idArea as id, Nombre_Area as valor  FROM hgpqgijw_operacion.mtto_almacen_area";
  return $this->_Read($query, null);
 }

 function list_equipos_count($array){
   $total = 0;
    $query = "
    SELECT

    count(*) as r
    FROM
    hgpqgijw_operacion.mtto_almacen,
    hgpqgijw_operacion.mtto_almacen_area,
    hgpqgijw_operacion.mtto_almacen_equipos,
    hgpqgijw_usuarios.udn,
    hgpqgijw_usuarios.zona,
    hgpqgijw_operacion.mtto_categoria,
    hgpqgijw_operacion.mtto_proveedores
    WHERE idUDN = UDN_Almacen and
    idArea = Area and
    idEquipo = Equipo and
    id_categoria=idcategoria and
    id_zona       = idzona  AND
    id_proveedor  =idProveedor AND

    Estado= ? 
    AND Area = ?
    ORDER BY idAlmacen desc
    
    ";
    $sql = $this->_Read($query, $array);

    foreach ($sql as $key) {
       $total = $key['r'];
    }
   return $total;
}

function list_equipos_esp($array){
   
    $query = "
    SELECT

    CodigoEquipo,
    Nombre_Equipo,
    Nombre_Area,
    hgpqgijw_usuarios.udn.UDN,
    idAlmacen,

    cantidad,
    Abreviatura,
    nombrecategoria,
    ruta,
    id_categoria,

    Costo,
    (cantidad * costo) as total,
    date_format(FechaIngreso,'%Y-%m-%d') as fecha_i,
    TiempodeVida,
    EstadoProducto,

    Observaciones,
    zona,
    rutaImagen,
    nombreProveedor
    FROM
    hgpqgijw_operacion.mtto_almacen,
    hgpqgijw_operacion.mtto_almacen_area,
    hgpqgijw_operacion.mtto_almacen_equipos,
    hgpqgijw_usuarios.udn,
    hgpqgijw_usuarios.zona,
    hgpqgijw_operacion.mtto_categoria,
    hgpqgijw_operacion.mtto_proveedores
    WHERE idUDN = UDN_Almacen and
    idArea = Area and
    idEquipo = Equipo and
    id_categoria=idcategoria and
    id_zona       = idzona  AND
    id_proveedor  =idProveedor AND

    Estado= ? 
    AND Area = ?
    ORDER BY idAlmacen desc
 
    ";

    return $this->_Read($query, $array);
}


}
?>