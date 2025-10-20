<?php
require_once('../../conf/_CRUD.php');

class rpt_gral extends CRUD{
    private $bd;

    public function __construct() {
      $this->bd = "hgpqgijw_finanzas2.";
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
 
    function VER_SUBCATEGORIAS($array){
        $sql = "SELECT
        idSubcategoria as id,
        Subcategoria as valor
        FROM
        {$this->bd}subcategoria
        WHERE
        id_Categoria = ? ";
        return $this->_Read($sql, $array);
    }

    function ver_ingresos_turismo($array){
        $query = "
        SELECT
            SUM(Monto) as Monto 
            FROM
            {$this->bd}folio
            INNER JOIN {$this->bd}bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
            INNER JOIN {$this->bd}bitacora_formaspago ON bitacora_formaspago.id_Bitacora = bitacora_ventas.idVentasBit
            INNER JOIN {$this->bd}subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        WHERE
            idFolio = ?
            and id_Categoria = ?
        ";
        $ls =  $this->_Read($query, $array);
        $monto = 0;
        foreach($ls as $row){
            $monto = $row['Monto'];
        } 
        return $monto;
    }

    function Select_Impuestos($id){
        $array = array($id);
        $query = "SELECT idImpuesto,Impuesto,Valor 
        FROM {$this->bd}categoria,
        {$this->bd}impuestos,
        {$this->bd}categoria_impuesto 
        WHERE idCategoria = id_Categoria AND idImpuesto = id_Impuesto AND id_Categoria = ?";
        $sql = $this->_Read($query,$array);
        return $sql;
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
        WHERE id_Folio = ? and id_Subcategoria = ? and id_Impuesto = ?";
        
    
        $sql = $this->_Read($query,$array);

        foreach($sql as $row){
            $__getMonto = $row['Monto'];      
        }    

        return $__getMonto;
    }


    function agregar_impuesto($array) {

        return $this->_Insert([
        'table'  => "{$this->bd}bitacora_impuesto",
        'values' => $array['values'],
        'data'   => $array['data']
        ]);

    }


    function ConsultarFolioActual($array){
        $__getFolio = 0; 
        $query = "
        SELECT idFolio
        FROM {$this->bd}folio
        WHERE Fecha = ?";

        $sql = $this->_Read($query,$array);

        foreach($sql as $row){
            $__getFolio = $row['idFolio'];      
        }    

        return $__getFolio;
    }

    function Select_group($array){

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

    function listIngresosPorGrupo($array){

        // $sql = "
        // SELECT
        // folio.Folio,
        // folio.Fecha,
        // bitacora_ventas.Subtotal,
        // bitacora_ventas.pax,
        // bitacora_ventas.Noche,
        // bitacora_ventas.Tarifa,
        // subcategoria.Subcategoria,
        // subcategoria.id_Categoria,
        // subcategoria.id_grupo,
        // subcategoria.idSubcategoria,
        // grupo.gruponombre
        // FROM
        // {$this->bd}folio
        // INNER JOIN {$this->bd}bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
        // INNER JOIN {$this->bd}subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        // INNER JOIN {$this->bd}grupo ON subcategoria.id_grupo = grupo.idgrupo
        // WHERE id_grupo = ? and id_Categoria = ? and Folio = ? ";


        // return $this->_Read($sql, $array);
    }

    function Select_Subcategoria_x_grupo($id,$grupo){
        $array = array($id,$grupo);
        $query = "SELECT idSubcategoria,Subcategoria,id_grupo
        FROM {$this->bd}subcategoria
        WHERE id_Categoria = ? and Stado = 1 and
        id_grupo=? order by idSubcategoria asc";
        $sql = $this->_Read($query,$array);
        return $sql;
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

    function quitarMovimiento($array){
        $query = "DELETE FROM {$this->bd}bitacora_ventas WHERE idVentasBit = ?";
        return $this->_CUD($query,$array);     
    }


    function Select_MontoSubtotal($array){

        $query = "
        SELECT
            ROUND(SUM(Subtotal),2) as subtotal,
            folio.Fecha,
            folio.Folio
        FROM
            {$this->bd}folio
        INNER JOIN {$this->bd}bitacora_ventas ON 
            bitacora_ventas.id_Folio = folio.idFolio
        WHERE idFolio = ? and id_Subcategoria = ? ";


        $sql = $this->_Read($query,$array);
        foreach($sql as $row2);
        if ( !isset($row2['subtotal']) ) { $row2['subtotal'] = 0; }

        return $row2['subtotal'];

    }

    function ExisteEnBitacora($array,$campo){

        $res = 0;

        $sql = " SELECT
        SUM(".$campo.") as campo,
        folio.Fecha,
        folio.Folio
        FROM
        {$this->bd}folio
        INNER JOIN {$this->bd}bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
        WHERE idFolio = ? and id_Subcategoria = ?";



        $ps 	 =	$this->_Read($sql,$array);
        foreach ($ps as $key ) { 
            $res = $key['campo']; 
        }


        return	$res;
    }

    function agregar_ingresos($array) {

        return $this->_Insert([
            'table'  => "{$this->bd}bitacora_ventas",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);

       
    }
    
    function obtener_id_ingreso($array){
        $id  = 0;
        $query	=
        "
        SELECT
            idVentasBit,
            id_Folio,
            id_Subcategoria
        FROM {$this->bd}bitacora_ventas
        Where id_Folio = ? and id_Subcategoria = ?
        ";

        $sql	=	$this->_Read($query,$array);

        foreach ($sql as $key) {
        $id = $key['idVentasBit'];
        }

        
        return	$id;

    }

    /* --  formas de pago   --*/ 

    function formas_pago($array) {

        return $this->_Insert([
            'table'  => "{$this->bd}bitacora_formaspago",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);

       
    }
    
    function lsformas_pago($array ){
        $query ="
            SELECT
                formas_pago.idFormas_Pago as id,
                formas_pago.FormasPago as name

            FROM {$this->bd}formas_pago
            WHERE grupo = ?

        ";
        return $this->_Read($query, $array);
    }

    function Select_MontoFPago($array){
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


    function editar_formas_pago($array) {

        return $this->_Update([
            'table'  => "{$this->bd}bitacora_formaspago",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);

       
    }


    function __getBitacoraFormasPago($array){
        $idFP_Bitacora = 0;    
        $query = "
        SELECT
            idFP_Bitacora
        FROM
        {$this->bd}bitacora_formaspago
        WHERE id_Bitacora = ? and id_FormasPago = ?";
        $sql =  $this->_Read($query, $array);
        
        foreach($sql as $row){
            $idFP_Bitacora = $row['idFP_Bitacora'];
        }
        return $idFP_Bitacora;    
    
    }




    function mov_capturados($array){
        $query = "
        SELECT
        bitacora_ventas.idVentasBit as id,
        CONCAT(Subcategoria,' - ',bitacora_ventas.Tarifa) as valor,
        bitacora_ventas.Tarifa
        FROM
        {$this->bd}bitacora_ventas
        INNER JOIN {$this->bd}subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        Where id_Folio = ? and id_Subcategoria = ?
        ";

        return  $this->_Read($query, $array);
    }

    function ver_mov($array){
        $query = "
        SELECT
            bitacora_ventas.idVentasBit AS id,
            bitacora_ventas.Tarifa,
            pax,
            Noche
        FROM
        hgpqgijw_finanzas2.bitacora_ventas
        INNER JOIN hgpqgijw_finanzas2.subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        WHERE
        idVentasBit = ?
        ";

        $sql =  $this->_Read($query, $array);
        // foreach($sql as $row);
        return $sql;
    }

    function ver_pago($array){
        $query = "
            SELECT
            formas_pago.FormasPago,
            bitacora_formaspago.Monto,
            bitacora_formaspago.id_Bitacora
            FROM
            {$this->bd}bitacora_formaspago
            INNER JOIN {$this->bd}bitacora_ventas ON bitacora_formaspago.id_Bitacora = bitacora_ventas.idVentasBit
            INNER JOIN {$this->bd}formas_pago ON bitacora_formaspago.id_FormasPago = formas_pago.idFormas_Pago
            WHERE id_Bitacora =?

        ";

        $sql =  $this->_Read($query, $array);
        foreach($sql as $row);
        return $sql;
    }

    function editar_movimiento($array) {

        return $this->_Update([
            'table'  => "{$this->bd}bitacora_ventas",
            'values' => $array['values'],
            'where' => $array['where'],
            'data' => $array['data']
        ]);

       
    }



    function __getBitacoraImpuesto($array){
    
        $idFP_Bitacora = 0;    
        $query = "

        SELECT
            idImp_Bitacora
        FROM
        {$this->bd}bitacora_impuesto
        WHERE id_Bitacora = ? and id_Impuesto = ?";


       

        $sql =  $this->_Read($query, $array);

        foreach($sql as $row){
            $idFP_Bitacora = $row['idImp_Bitacora'];

        }

        return $idFP_Bitacora;    
    
    }




}

class TC extends CRUD {
    private $bd;

    public function __construct() {
      $this->bd = "hgpqgijw_finanzas2.";
    }

    function lsTC($array){
        $query = "
        SELECT
            tc.idTC as id,
            tc.Monto,
            tc.Autorizacion,
            terminal.TCodigo,
            tipo_terminal.TTCodigo,
            tc.Concepto,
            tc.Especificacion,
            tc.Cliente,
            propina,
            tc.Observaciones,
            folio.Fecha
        FROM
        {$this->bd}tc
        INNER JOIN {$this->bd}terminal ON tc.id_Terminal = terminal.idTerminal
        INNER JOIN {$this->bd}tipo_terminal ON tc.id_TipoTerminal = tipo_terminal.idTipoTerminal
        INNER JOIN {$this->bd}folio ON tc.id_Folio = folio.idFolio 
        WHERE id_Folio = ?

        ORDER BY idTC DESC



        ";
        return $this->_Read($query,$array);
    }

    function add_tc($array){

        return $this->_Insert([
            'table'  => "{$this->bd}tc",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function remove_tc($array){
        $query = "DELETE FROM {$this->bd}tc WHERE idTC = ?";
        return $this->_CUD($query,$array);     
    }

    function Select_Archivos($array){
     $query = "
     SELECT 
        idSobre as id,
        Archivo,
        Descripcion,
        ROUND(Peso,2) as peso,
        Hora,
        Type_File,
        Ruta 
     FROM {$this->bd}sobres WHERE Fecha = ? and status = 1
     ORDER BY idSobre DESC
     ";
     return $this->_Read($query,$array);
    }


     function add_archivo($array){

        return $this->_Insert([
            'table'  => "{$this->bd}sobres",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function quitarArchivo($array) {

        return $this->_Update([
            'table'  => "{$this->bd}sobres",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }


}

class RptGral extends CRUD {
    private $bd;

    public function __construct() {
        $this->bd = "hgpqgijw_finanzas2.";
    }

    function lsFolio($array){
        $query ="
            SELECT
            *
            FROM {$this->bd}folio
            WHERE idFolio = ?
        ";
        $sql = $this->_Read($query, $array);

        foreach ($sql as $row);
        return $row;
    }

    function VER_CATEGORIAS($array){
        $sql = "SELECT
            categoria.idCategoria as id,
            categoria.Categoria,
            categoria.id_TMovimiento
        FROM
            hgpqgijw_finanzas.categoria
        WHERE
            id_UDN = ? and idCategoria <> 12 and idCategoria <> 13";
        
        
        return $this->_Read($sql, $array);
    }

    function getIngresosTurismo($array){

        $query = "
        
            SELECT
                SUM(Monto) as Monto 
                FROM
                {$this->bd}folio
                INNER JOIN {$this->bd}bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
                INNER JOIN {$this->bd}bitacora_formaspago ON bitacora_formaspago.id_Bitacora = bitacora_ventas.idVentasBit
                INNER JOIN {$this->bd}subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
            WHERE
                folio.Fecha = ?
                and id_Categoria = ?
                AND id_estado = 2 
        ";


        $ls =  $this->_Read($query, $array);
        $monto = 0;

        foreach($ls as $row){
            $monto = $row['Monto'];
        } 


        return $monto;
    
    }

    function VER_FORMAS_PAGO(){
        $rs = "";
        $array = array(null);

        $sql = "
            SELECT
            idFormas_Pago as id,
            FormasPago as name
            FROM
            {$this->bd}formas_pago

            WHERE 
            idFormas_Pago != 5 AND
            idFormas_Pago != 6 AND
            idFormas_Pago != 7 AND
            idFormas_Pago != 8 AND
            idFormas_Pago != 9 AND
            idFormas_Pago != 10 
        ";

        $ps = $this->_Read($sql,$array);
        return $ps;
    }

    function addObservaciones($array){

        return $this->_Update([
            'table'  => "{$this->bd}folio",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
   }

    function lsPropina($array){
        $rs = 0;

        $sql="
            SELECT
                id_FormasPago,
                FormasPago,
                SUM(Monto) as monto,
                Fecha
            FROM
                {$this->bd}formas_pago,
                {$this->bd}bitacora_formaspago,
                {$this->bd}bitacora_ventas,
                {$this->bd}folio,
                {$this->bd}subcategoria
            WHERE
                idFormas_Pago =id_FormasPago AND
                idVentasBit   =id_Bitacora   AND
                id_Folio      =idFolio       AND
                id_Subcategoria = idSubcategoria AND
                id_FormasPago = ? and subcategoria.id_categoria = ?

            AND Fecha = ?
            Group by id_FormasPago " ;



        $ps = $this->_Read($sql,$array);
        foreach ($ps as $key ) {
        $rs = $key['monto'];
        }
        return $rs;

    }



   function lsFormasPago(){
        $rs = "";
        $array = array(null);

        $sql = "
        SELECT
            idFormas_Pago as id,
            FormasPago as name
        FROM
        {$this->bd}formas_pago

        WHERE 
            grupo = 1
        ";

        $ps = $this->_Read($sql,$array);
        return $ps;
    }

    function	Select_empleadosCortesia($array){
        $sql="
            SELECT
                subcategoria.id_Categoria,
                subcategoria.Subcategoria,
                SUM(bitacora_ventas.Subtotal) total,
                bitacora_ventas.Tarifa,
                folio.Fecha,
                folio.Folio
            FROM
            hgpqgijw_finanzas2.subcategoria
            INNER JOIN hgpqgijw_finanzas2.bitacora_ventas ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
            INNER JOIN hgpqgijw_finanzas2.folio ON bitacora_ventas.id_Folio = folio.idFolio
            WHERE
            subcategoria.id_Categoria = 12 AND
            subcategoria.Stado = 1
            AND Fecha = ?

            Group by Subcategoria
        ";

        $ps	=	$this->_Read($sql,$array);
        return	$ps;
    }



    function  VER_TIPOSPAGOS_FECHA($array){
        $rs = 0;
        $OPC1 ="";

        $sql="
            SELECT
                id_FormasPago,
                FormasPago,
                SUM(Monto) as suma_monto,
                Fecha
                FROM
                {$this->bd}formas_pago,
                {$this->bd}bitacora_formaspago,
                {$this->bd}bitacora_ventas,
                {$this->bd}folio,
                {$this->bd}subcategoria
            WHERE
                idFormas_Pago =id_FormasPago AND
                idVentasBit   =id_Bitacora   AND
                id_Folio      =idFolio       AND
                id_Subcategoria = idSubcategoria AND
                subcategoria.id_categoria <> 9   AND
                id_FormasPago =?
            AND Fecha = ?
            AND id_estado = 2
        ";



        $ps = $this->_Read($sql,$array);
        foreach ($ps as $key ) {
            $rs = $key['suma_monto'];
        }
        return $rs;

    }
    







}



class Cortesias extends CRUD {
    private $bd;

    public function __construct() {
        $this->bd = "hgpqgijw_finanzas2.";
    }

    function lsCortesias($array){
        $query = "
            SELECT
            bitacora_ventas.idVentasBit as id,
            folio.Folio,
            folio.Fecha,
            bitacora_ventas.id_Subcategoria,
            bitacora_ventas.Subtotal,
            subcategoria.Subcategoria,
            bitacora_ventas.Tarifa
            FROM
            {$this->bd}folio
            INNER JOIN {$this->bd}bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
            INNER JOIN {$this->bd}subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
            INNER JOIN {$this->bd}categoria ON subcategoria.id_Categoria = categoria.idCategoria
            WHERE idCategoria = 12 
            And Fecha = ?
            order by idVentasBit desc
        ";
        return $this->_Read($query,$array);
    }

    function listCortesias(){

         $query = "

            SELECT
                subcategoria.idSubcategoria as id,
                subcategoria.Subcategoria as valor
            FROM
                {$this->bd}categoria
            INNER JOIN {$this->bd}subcategoria ON subcategoria.id_Categoria = categoria.idCategoria
            WHERE 
            idCategoria = 12 AND subcategoria.Stado = 1
            
         
         ";

        return $this->_Read($query,$array);
    }

    function insert_cortesia($array){

        return $this->_Insert([
            'table'  => "{$this->bd}bitacora_ventas",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }

    function remove_producto($array){

        $query = "
            DELETE FROM 
                {$this->bd}bitacora_ventas
            WHERE idVentasBit = ?
        ";

        return $this->_CUD($query, $array);
    }

   

    


}


class CxC extends CRUD {
    private $bd;

    public function __construct() {
    $this->bd = "hgpqgijw_finanzas2.";
    }

    function  Ver_Folio($array){

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

    function lsformas_pago(){
        $query ="
        SELECT
            idFormasPago as id,
            nombreFormasPago as name

        FROM {$this->bd}tipo_formaspago
        WHERE statusFormasPago = 1
        ";
        return $this->_Read($query,null);
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
            id_Folio = ?  ";

        $ps = $this->_Read($sql,$array);

        return $ps;

    }

    function  bitacora_formas_pago($array){

        $sql = "
        SELECT
            idFP_Bitacora,
            id_FormasPago,
            id_Bitacora,
            Monto,
            id_tipoPago
        FROM
            {$this->bd}bitacora_formaspago
        WHERE
            id_Bitacora = ?  and id_FormasPago = 3";

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

    function updateCxC($array) {

        return $this->_Update([
            'table' => "{$this->bd}revision_cxc",
            'values' => $array['values'],
            'where' => $array['where'],
            'data' => $array['data']
        ]);
    }

    function addCxC($array){

        return $this->_Insert([
            'table'  => "{$this->bd}revision_cxc",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }




}


class Movimientosdiarios extends CRUD{
    private $bd;

    public function __construct() {
      $this->bd = "hgpqgijw_finanzas2.";
    }

    function lsUDN(){
        $query ="SELECT idUDN AS id, UDN AS valor FROM udn WHERE Stado = 1";
        return $this->_Read($query, null);
    }

    function lsTipoTerminal(){
        return $this->_Select([
        "table"  => "{$this->bd}tipo_terminal",
        "values" => "idTipoTerminal AS id, TTCodigo AS valor"
        // "where"  => "Stado = 1, idUDN != 10",
        // "order"  => ["ASC" => "Antiguedad"]
        ]);
    }

    function lsTerminal(){
        return $this->_Select([
            "table"  => "{$this->bd}terminal",
            "values" => "idTerminal AS id, TCodigo AS valor"

        ]);
    }

     function lsCheckList(){
        return $this->_Select([
            "table"  => "{$this->bd}check_list",
            "values" => "idcheck AS id, check_name AS valor"

        ]);
    }

    function __getFolio($array){
        $query = "
        SELECT
        idFolio AS id,
        Folio,
        encargado,
        DATE_FORMAT(Fecha,'%Y-%m-%d') AS fecha,
        DATE_FORMAT(FechaModificacion, '%r') AS hora
        FROM
        {$this->bd}folio

        WHERE Fecha = ? and id_estado <> 2";

        return  $this->_Read($query,$array);
    }

    function Select_Turnos($array){
        $query = "
        SELECT
        idFolio AS id,
        CONCAT(Folio, ' | ',encargado, ' | ', FechaModificacion ) as valor
       
        FROM
        {$this->bd}folio

        WHERE Fecha = ? and id_estado = 2";

        return  $this->_Read($query,$array);
    }


    function crearFolio($array){

        return $this->_Insert([
        'table'  => "{$this->bd}folio",
        'values' => $array['values'],
        'data'   => $array['data']
        ]);
    }

    function	obtenerFolio(){
        $folio  = 0;
        $query	=
        "
        SELECT
        count(*) as folio
        FROM
        {$this->bd}folio
        ";

        $sql	=	$this->_Read($query,null);

        foreach ($sql as $key) {
        $folio = $key['folio'];
        }

        $new_folio = $folio + 1;
        return	$new_folio;

    }


    function cerrar_folio($array){
        return $this->_Update([
            'table'  => "{$this->bd}folio",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function VER_CATEGORIAS($array){
        $sql = "SELECT
                categoria.idCategoria as id,
                categoria.Categoria,
                categoria.id_TMovimiento
            FROM
                hgpqgijw_finanzas.categoria
            WHERE
                id_UDN = ? and idCategoria <> 12 and idCategoria <> 13";
        return $this->_Read($sql, $array);
    }

    
    function ver_ingresos_turismo($array){

        $query = "
        SELECT
            SUM(Monto) as Monto 
            FROM
            hgpqgijw_finanzas.folio
            INNER JOIN hgpqgijw_finanzas.bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
            INNER JOIN hgpqgijw_finanzas.bitacora_formaspago ON bitacora_formaspago.id_Bitacora = bitacora_ventas.idVentasBit
            INNER JOIN hgpqgijw_finanzas.subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        WHERE
            folio.Fecha BETWEEN ? AND ?
            and id_Categoria = ?
        ";


        $ls =  $this->_Read($query, $array);
        $monto = 0;

        foreach($ls as $row){
            $monto = $row['Monto'];
        } 


        return $monto;
    
    }
    

    function lsCategorias($array){
        $sql = "SELECT
        categoria.idCategoria as id,
        categoria.Categoria as valor

        FROM
        hgpqgijw_finanzas.categoria
        WHERE
        id_UDN = ? and idCategoria <> 12 and idCategoria <> 13";
     return $this->_Read($sql, $array);
    }

    function Ayer(){
        $query = "SELECT DATE_SUB(CURDATE() , INTERVAL 1 DAY) as ayer";
        $sql = $this->_Read($query,null);
        foreach($sql as $row);
        return $row['ayer'];
    }

   

  

 

    function Select_Total2($date,$idS){
        $array = array($date);
        $query = "SELECT idFolio FROM hgpqgijw_finanzas.folio WHERE Fecha = ?";
        $sql = $this->_Read($query,$array);
        foreach ($sql as $row);
        if ( !isset($row['idFolio']) ) { $row['idFolio'] = 0; }
        $idF = $row['idFolio'];

        $array = array($idF,$idS);
        $query = "SELECT idVentasBit FROM hgpqgijw_finanzas.bitacora_ventas WHERE id_Folio = ? AND id_Subcategoria = ?";
        $sql = $this->_Read($query,$array);
        foreach ($sql as $row2);
        if ( !isset($row2['idVentasBit']) ) { $row2['idVentasBit'] = 0; }
        $idSBit = $row2['idVentasBit'];

        $array = array($idSBit);
        $query = "SELECT SUM(Monto) as monto FROM hgpqgijw_finanzas.bitacora_formaspago WHERE id_Bitacora = ?";
        $sql = $this->_Read($query,$array);
        foreach($sql as $row3);
        if ( !isset($row3['monto']) ) { $row3['monto'] = 0; }
        return $row3['monto'];
    }

    function Select_MontoSubtotal($date1,$date2,$idS){
        $array = array($date1,$date2,$idS);

        $query = "
        SELECT
        ROUND(SUM(Subtotal),2) as subtotal,
        folio.Fecha,
        folio.Folio
        FROM
        hgpqgijw_finanzas.folio
        INNER JOIN hgpqgijw_finanzas.bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
        WHERE FECHA BETWEEN ? and ? and id_Subcategoria = ? ";


        $sql = $this->_Read($query,$array);
        foreach($sql as $row2);
        if ( !isset($row2['subtotal']) ) { $row2['subtotal'] = 0; }

        return $row2['subtotal'];

    }



    // Consultar archivos --
    
    function Select_Archivos($array){
     $query = "
     SELECT 
     idSobre as id,
     Archivo,
     Descripcion,
     ROUND(Peso,2) as peso,
     Hora,
     Type_File,
     Ruta 
     FROM hgpqgijw_finanzas.sobres WHERE Fecha = ?";
     return $this->_Read($query,$array);
    }


    function Select_TC_Data($array){
    $query = "SELECT
    terminal.idTerminal as id, 
    Monto,
    TCodigo,
    TTCodigo,
    Concepto,
    Especificacion,
    Cliente,Autorizacion,Observaciones,Fecha,idTC 
    FROM hgpqgijw_finanzas.tc,
    hgpqgijw_finanzas.terminal,
    hgpqgijw_finanzas.tipo_terminal,
    hgpqgijw_finanzas.folio 
    WHERE idTipoTerminal = id_TipoTerminal 
    AND idTerminal = id_Terminal AND id_Folio = idFolio  AND Fecha = ?";
    $sql = $this->_Read($query,$array);
    return $sql;
    }

}
?>