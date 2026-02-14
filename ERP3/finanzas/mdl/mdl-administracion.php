<?php
require_once('../../conf/_CRUD.php');

class Administracion extends CRUD{
    private $bd;
    private $bd2;
    
    public function __construct() {
        $this->bd2 = "hgpqgijw_finanzas2.";
        $this->bd = "hgpqgijw_finanzas.";
    }
    
    function lsUDN(){
        $query ="SELECT idUDN AS id, UDN AS valor FROM udn WHERE Stado = 1";
        return $this->_Read($query, null);
    }

    function lsCategorias($array){
        $sql = "SELECT
        categoria.idCategoria as id,
        categoria.Categoria as valor,
        categoria.Categoria,
        categoria.id_TMovimiento
        FROM
        {$this->bd}categoria
        WHERE
        id_UDN = ? and idCategoria <> 12 and idCategoria <> 13";
        return $this->_Read($sql, $array);
    }

    function lsSubs(){

        $sql = "SELECT
        idSubcategoria as id,
        Subcategoria   as valor
        FROM
        {$this->bd}subcategoria";
        return $this->_Read($sql, $array);

    }
    
    function Grupos(){
        $query = "SELECT idgrupo as id, gruponombre as valor FROM {$this->bd}grupo";
        return $this->_Read($query, null);
    }

    function gruopByCategory($array){
        $sql="
        SELECT
            grupo.idgrupo as id,
            grupo.gruponombre as valor,
            subcategoria.id_Categoria
        FROM
            {$this->bd}grupo
            INNER JOIN {$this->bd}subcategoria ON subcategoria.id_grupo = grupo.idgrupo
            WHERE  Stado = 1
    
        GROUP BY gruponombre,id_Categoria";
        $ps	=	$this->_Read($sql,$array);
        return	$ps;
    }


    function subCategorias($array){

        $query = "
        SELECT
            subcategoria.idSubcategoria as id,
            subcategoria.Subcategoria as valor,
            grupo.gruponombre,
            grupo.idgrupo,
            subcategoria.Stado,
            subcategoria.tarifa,
            subcategoria.activo
        FROM
        {$this->bd}grupo
        INNER JOIN {$this->bd}subcategoria ON subcategoria.id_grupo = grupo.idgrupo
        
        
        WHERE id_Categoria = ? and id_grupo = ? and activo = ?
        
        ORDER BY idSubcategoria desc
        ";
        return $this->_Read($query, $array);
    }

    function getIngresos($array){
        $query = "

        SELECT
        SUM(bitacora_ventas.Tarifa) as total
        FROM
        {$this->bd}bitacora_ventas
        INNER JOIN {$this->bd}subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        INNER JOIN {$this->bd}folio ON bitacora_ventas.id_Folio = folio.idFolio
        WHERE YEAR(Fecha) = 2024 and idSubcategoria = ?
        
        ";
        return $this->_Read($query, $array);
    }

    function updateSubCategoria($array) {

        return $this->_Update([
            'table'  => "{$this->bd}subcategoria",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);

    }

    function insertSubCategoria($array){

        return $this->_Insert([
            'table'  => "{$this->bd}subcategoria",
            'values' => $array['values'],
            'data'   => $array['data']
        ] );

    }

    


}

class Gastos extends CRUD {
    private $bd;
    private $bd2;
    
    public function __construct() {
        $this->bd2 = "hgpqgijw_finanzas2.";
        $this->bd = "hgpqgijw_finanzas.";
    }

    function lsUDN(){
        $query ="SELECT idUDN AS id, UDN AS valors FROM udn WHERE Stado = 1";
        return $this->_Read($query, null);
    }

    // Gastos en efectivo :

    function listGastos($array){

        $query ="
            SELECT
            contabilidad_gastos.idgastos as id,
            contabilidad_gastos.id_tipoGastos,
            contabilidad_gastos.Monto,
            contabilidad_gastos.descripcion,
            proveedor.Name_Proveedor,
            contabilidad_gastos.fechaGasto
            FROM
            {$this->bd}contabilidad_gastos
            INNER JOIN {$this->bd}proveedor ON contabilidad_gastos.id_proveedor = proveedor.idProveedor
            WHERE fechaGasto Between ? AND ? 

            ORDER BY fechaGasto desc
        ";
        return $this->_Read($query, $array);
    }

    function lsCreatedGasto($array){

        $query ="

            SELECT
                contabilidad_gastos.idgastos,
                contabilidad_gastos.id_proveedor,
                contabilidad_gastos.descripcion
            FROM
            {$this->bd}contabilidad_gastos

            WHERE id_proveedor = ? and descripcion = ?
        
        ";
        $sql = $this->_Read($query, $array);

        return $sql[0];
    }

    function getGastoID($array){


        $query ="
        SELECT
        *
        FROM
        {$this->bd}contabilidad_gastos
        WHERE idgastos = ?
        ";
        return $this->_Read($query, $array);
    }
    
    function getProveedor($array){

        $query ="
        
        SELECT
            *
        FROM
            {$this->bd}proveedor
        WHERE Name_Proveedor = ?";
        
        $sql = $this->_Read($query, $array);

        // return $query;
        return $sql[0];
    }

    // Gastos en efectivo :

    function addProveedor($array){

        return $this->_Insert([
            'table'  => "{$this->bd}proveedor",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }
    
    function insertGastos($array){

        return $this->_Insert([
            'table'  => "{$this->bd}contabilidad_gastos",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);

    }

    function removeGasto($array) {

        return $this->_Delete([
            'table'  => "{$this->bd}contabilidad_gastos",
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);

    }

    function updateGasto($array) {

        return $this->_Update([
            'table' => "{$this->bd}contabilidad_gastos",
            'values' => $array['values'],
            'where' => $array['where'],
            'data' => $array['data']
        ]);
    }

    // 





    // Cuentas por cobrar:
     function  listFolios($array){

        $sql = "
        SELECT
            idFolio as id,
            Folio,
            Fecha
            FROM
            {$this->bd}folio
            WHERE

            date_format(Fecha,'%Y-%m-%d') BETWEEN   ? AND ?
            order by idFolio asc
        ";

        $ps = $this->_Read($sql,$array);
        return $ps;
    }

     function  ver_bitacora_ventas($array){

        $sql = "
        SELECT
            bitacora_ventas.idVentasBit,
            subcategoria.Subcategoria,
            bitacora_ventas.Subtotal
        FROM
            {$this->bd}bitacora_ventas
            INNER JOIN
            {$this->bd}subcategoria
            ON
            bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        WHERE
            id_Folio = ?  order by idVentasBit asc";

        $ps = $this->_Read($sql,$array);

        return $ps;

    }

    function  bitacora_formas_pago($array){

        $sql = "
        SELECT
        bitacora_formaspago.idFP_Bitacora,
        bitacora_formaspago.id_FormasPago,
        bitacora_formaspago.id_Bitacora,
        bitacora_formaspago.Monto,
        bitacora_formaspago.id_tipoPago,
        bitacora_formaspago.payment_date,
        bitacora_formaspago.observation,
        tipo_formaspago.nombreFormasPago
        FROM
            {$this->bd}bitacora_formaspago
        INNER JOIN {$this->bd}tipo_formaspago ON bitacora_formaspago.id_tipoPago = tipo_formaspago.idFormasPago
        WHERE
        id_Bitacora = ? AND  
        id_FormasPago = 3
        ORDER BY idFP_Bitacora DESC    
        ";

        $ps = $this->_Read($sql,$array);

        return $ps;

    }

    function  tipoFormasPago(){

        $sql = "
            SELECT idFormasPago as id, nombreFormasPago as name FROM 
            hgpqgijw_finanzas2.tipo_formaspago WHERE statusFormasPago = 1
        ";

        $ps = $this->_Read($sql,null);

        return $ps;

    }
    
    function getListRevisionCxC($array){
        
        $sql = "
        SELECT 
            idcxc,
            id_bitacora_formaspago,
            id_tipo_formaspago,
            descripcion,
            monto 
        FROM {$this->bd}revision_cxc

        WHERE id_bitacora_formaspago = ? and
         id_tipo_formaspago = ?
        
        ";

        return $this->_Read($sql,$array);
    }
    
    
}

?>