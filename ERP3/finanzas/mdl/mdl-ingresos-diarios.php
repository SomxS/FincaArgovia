<?php
require_once('../../conf/_CRUD.php');

class Activos extends CRUD{
    private $bd;

    public function __construct() {
        $this->bd = "hgpqgijw_finanzas2.";
    }

    function lsGroup($array){

        $sql="
        SELECT

            grupo.idgrupo as id,
            grupo.idgrupo,
            grupo.gruponombre
        FROM
            {$this->bd}grupo
            INNER JOIN {$this->bd}subcategoria ON subcategoria.id_grupo = grupo.idgrupo
            WHERE id_Categoria = ? and Stado = 1
        GROUP BY
        grupo.idgrupo
        ";

        $ps	=	$this->_Read($sql,$array);
        return	$ps;
    }

    function lsIngresosbySubGrupo($array){
        
        $sql="
       
        SELECT
            bitacora_ventas.idVentasBit as id,
            subcategoria.Subcategoria,
            bitacora_ventas.Subtotal,
            bitacora_ventas.Noche,
            bitacora_ventas.Tarifa,
            bitacora_ventas.pax,
            bitacora_ventas.entrada,
            bitacora_ventas.salida,
            subcategoria.idSubcategoria,
            subcategoria.id_grupo
        FROM
            hgpqgijw_finanzas2.bitacora_ventas
        INNER JOIN hgpqgijw_finanzas2.subcategoria ON 
        bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        WHERE id_Folio = ? and id_grupo = ?
        ";

        return $this->_Read($sql,$array);
        
    }

    function __getMovimientos($array){
        $__getMov = 0;

        $query    = "
            SELECT
                count(*) as mov
            FROM hgpqgijw_finanzas2.bitacora_ventas
            WHERE id_Folio = ? and 
            id_Subcategoria = ?";

        $sql = $this->_Read($query,$array);

        foreach ($sql as $key) {
            $__getMov = $key['mov'];
        }

        return $__getMov;
    }

    function __getMontoImpuesto($array){

        $__getMonto = 0; 
        
        $query = "
        SELECT
            bitacora_ventas.id_Folio,
            bitacora_ventas.id_Subcategoria,
            bitacora_impuesto.id_Impuesto,
            bitacora_impuesto.Monto
        FROM
        
        {$this->bd}bitacora_ventas
        
        INNER JOIN {$this->bd}bitacora_impuesto ON bitacora_impuesto.id_Bitacora = bitacora_ventas.idVentasBit
        
        WHERE id_Folio = ? 
        AND id_Subcategoria = ? 
        AND id_Impuesto = ?";
        
        
       

        $sql = $this->_Read($query,$array);

        foreach($sql as $row){
            $__getMonto = $row['Monto'];      
        }    

        return $__getMonto;
    }

    function Select_Impuestos($id){
        $array = array($id);
        
        $query = "
            SELECT idImpuesto,Impuesto,Valor 
            FROM {$this->bd}categoria,
            {$this->bd}impuestos,
            {$this->bd}categoria_impuesto 
            WHERE idCategoria = id_Categoria AND idImpuesto = id_Impuesto AND id_Categoria = ?";
        
        $sql = $this->_Read($query,$array);
        return $sql;
    }


    




    function VER_CATEGORIAS($array){
        $sql = "SELECT
        categoria.idCategoria as id,
        categoria.Categoria,
        categoria.id_TMovimiento
        FROM
        {$this->bd}categoria
        WHERE
        id_UDN = ? and idCategoria <> 12 and idCategoria <> 13";
        return $this->_Read($sql, $array);
    }


    function lsIngresos($array){
        $sql="
        SELECT
            subcategoria.Subcategoria,
            bitacora_ventas.Subtotal,
            bitacora_ventas.Noche,
            bitacora_ventas.Tarifa,
            bitacora_ventas.pax,
            bitacora_ventas.entrada,
            bitacora_ventas.salida,
            subcategoria.idSubcategoria
        FROM
         {$this->bd}bitacora_ventas
        INNER JOIN {$this->bd}subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        WHERE id_Folio = ? and id_Categoria = ?";
        $ps	=	$this->_Read($sql,$array);
        return	$ps;
    }

    function lsSubcategoria($array){
        $sql = "

        SELECT
            subcategoria.idSubcategoria,
            subcategoria.Subcategoria,
            subcategoria.Stado
        FROM hgpqgijw_finanzas2.subcategoria

        WHERE id_Categoria = ?
        
        ";

        return $this->_Read($sql,$array);

    }

    function lsBitacoraVentas($array){
        
        $sql="
            SELECT
                subcategoria.Subcategoria,
                bitacora_ventas.Subtotal,
                bitacora_ventas.Noche,
                bitacora_ventas.Tarifa,
                bitacora_ventas.pax,
                bitacora_ventas.entrada,
                bitacora_ventas.salida,
                subcategoria.idSubcategoria
            FROM
                hgpqgijw_finanzas2.bitacora_ventas
            INNER JOIN hgpqgijw_finanzas2.subcategoria ON 
            bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
            WHERE id_Folio = ? and idSubcategoria = ?";     
        
   
        
        $ps	=	$this->_Read($sql,$array);
        
        return	$ps;
    }


    function lsFormasPago($array ){
        $query ="
        SELECT
            formas_pago.idFormas_Pago as id,
            formas_pago.FormasPago as name

        FROM {$this->bd}formas_pago
        WHERE grupo = ?
        ";
        return $this->_Read($query, $array);
    }

    function MontoFormasPago($array){
        $query = "
        SELECT
        bitacora_formaspago.Monto,
        bitacora_formaspago.id_FormasPago,
        bitacora_ventas.id_Folio
        FROM
        {$this->bd}bitacora_formaspago
        INNER JOIN {$this->bd}bitacora_ventas ON bitacora_formaspago.id_Bitacora = bitacora_ventas.idVentasBit
        WHERE id_Folio = ? and id_Subcategoria = ? and id_FormasPago = ?
        ";
        $sql = $this->_Read($query, $array);
        foreach ($sql as $row3);
        if ( !isset($row3['Monto']) ) { $row3['Monto'] = 0; }
        return $row3['Monto'];
    }




    function Select_Subcategoria_x_grupo($array){

    $query = "SELECT 
        idSubcategoria,Subcategoria,id_grupo 
    FROM {$this->bd}subcategoria 
    WHERE id_Categoria = ? and Stado = 1 
    -- and id_grupo = ?  
    order by idSubcategoria asc";

    $sql = $this->_Read($query,$array);

    return $sql;
    }

  



}
?>