<?php
require_once('../../conf/_CRUD.php');

class Cheques extends CRUD {
    private $bd;

    public function __construct() {
        $this->bd = "hgpqgijw_finanzas.";
    }

    function Select_Datos_Cheques($id){
        $array = array($id);

        $query = "
            SELECT
                date_format(cheques.Fecha,' %d/%m/%Y ') as fecha,
                cheques.Nombre,
                cheques.Importe,
                banco.Banco,
                cuentacheque.Cuenta,
                cheques.Cheque,
                cheques.Concepto,
                cheques.Ruta,
                cheques.Archivo,
                cuentacheque.NombreCuenta
            FROM
                {$this->bd}cheques
            INNER JOIN {$this->bd}cuentacheque  ON cheques.Cuenta = cuentacheque.idCuenta
            INNER JOIN {$this->bd}banco         ON cheques.id_Banco = banco.idBanco
            WHERE idCheque = ?";

        $sql = $this->_Read($query,$array);
        return $sql;
    }

}

class RptGral extends CRUD {
    private $bd;

    public function __construct() {
        $this->bd = "hgpqgijw_finanzas.";
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

    function VER_INGRESOS_TURISMO($array){

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
            {$this->bd}subcategoria
            INNER JOIN {$this->bd}bitacora_ventas ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
            INNER JOIN {$this->bd}folio ON bitacora_ventas.id_Folio = folio.idFolio
            WHERE
            subcategoria.id_Categoria = 12 AND
            subcategoria.Stado = 1
            AND Fecha = ?

            Group by Subcategoria
        ";

        $ps	=	$this->_Read($sql,$array);
        return	$ps;
    }

    function lsFolio($array){
        
        $query ="
            SELECT
            *
            FROM {$this->bd}folio
            WHERE Fecha = ?
        ";
        $sql = $this->_Read($query, $array);

        return $sql[0];
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
        ";



        $ps = $this->_Read($sql,$array);
        foreach ($ps as $key ) {
            $rs = $key['suma_monto'];
        }
        return $rs;

    }
    




}

class Turnos extends CRUD {
    private $bd;
    private $bd2;
    
    public function __construct() {
        $this->bd = "hgpqgijw_finanzas.";
        $this->bd2 = "hgpqgijw_finanzas2.";
    }

    function lsCategorias($array){
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

    function lsGroup($array){

        $sql="
            SELECT

            grupo.idgrupo,
            grupo.gruponombre
            FROM
            {$this->bd}grupo
            INNER JOIN {$this->bd}subcategoria ON subcategoria.id_grupo = grupo.idgrupo
            WHERE id_Categoria = ? and Stado = 1
            GROUP BY
            grupo.idgrupo
        ";

        return	$this->_Read($sql,$array);
    }

    function Select_Subcategoria_x_grupo($array){
        $query = "SELECT idSubcategoria,Subcategoria,id_grupo 
        FROM {$this->bd}subcategoria WHERE id_Categoria = ? 
        and Stado = 1 
        and id_grupo=?  order by idSubcategoria asc";
        return $this->_Read($query,$array);
       
    }

    function Select_formaspago_by_categoria($array){
        $query = "
        SELECT idFormas_Pago,FormasPago 
        FROM {$this->bd}formas_pago WHERE grupo = ?";
        $sql = $this->_Read($query,$array);
        return $sql;
    }

    function Select_MontoFPago($array){

        $query = "
            SELECT
            ROUND(SUM(Monto),2) as monto,
            bitacora_formaspago.id_FormasPago,
            bitacora_ventas.pax
            FROM
            {$this->bd}folio
            INNER JOIN {$this->bd}bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
            INNER JOIN {$this->bd}bitacora_formaspago ON bitacora_formaspago.id_Bitacora = bitacora_ventas.idVentasBit
            WHERE
            folio.Fecha BETWEEN ? AND ? AND
            bitacora_ventas.id_Subcategoria = ?
             and id_FormasPago = ? ";

        $sql = $this->_Read($query,$array);

        foreach ($sql as $row3);

        if ( !isset($row3['monto']) ) { $row3['monto'] = 0; }

        return $row3['monto'];

    }

    function listIngresosPorGrupo($array){

        $sql = "
        SELECT
        folio.Folio,
        folio.Fecha,
        bitacora_ventas.Subtotal,
        bitacora_ventas.pax,
        bitacora_ventas.Noche,
        bitacora_ventas.Tarifa,
        subcategoria.Subcategoria,
        subcategoria.id_Categoria,
        subcategoria.id_grupo,
        subcategoria.idSubcategoria,
        grupo.gruponombre
        FROM
        {$this->bd}folio
        INNER JOIN {$this->bd}bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
        INNER JOIN {$this->bd}subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        INNER JOIN {$this->bd}grupo ON subcategoria.id_grupo = grupo.idgrupo
        WHERE id_grupo = ? and id_Categoria = ? and Fecha = ?
        
        ";


        return $this->_Read($sql, $array);
    }

    function listVentasPorCategoria($array){

        $sql = "
        SELECT
            folio.Folio,
            folio.Fecha,
            bitacora_ventas.Subtotal,
            bitacora_ventas.pax,
            bitacora_ventas.Noche,
            SUM(bitacora_ventas.Tarifa) as total,
            subcategoria.Subcategoria,
            subcategoria.id_Categoria,
            subcategoria.id_grupo,
            grupo.gruponombre
        FROM
        {$this->bd}folio
        INNER JOIN {$this->bd}bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
        INNER JOIN {$this->bd}subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        INNER JOIN {$this->bd}grupo ON subcategoria.id_grupo = grupo.idgrupo
        WHERE id_Categoria = ? and Fecha = ?
        
        ";


        return $this->_Read($sql, $array);

    }



}

class Contabilidad extends CRUD{
    private $bd;
    private $bd2;

    public function __construct() {
        $this->bd = "hgpqgijw_finanzas.";
        $this->bd2 = "hgpqgijw_finanzas.";
    }

    function lsBanco(){
        return $this->_Select([
        "table"  => "{$this->bd}banco",
        "values" => "idBanco AS id, Banco AS valor"

        ]);
    }


    function lsProveedores(){
        return $this->_Select([
        "table"  => "{$this->bd2}proveedor",
        "values" => "idProveedor AS id,Name_Proveedor  AS valor"

        ]);
    }

     function lsCuenta(){
        return $this->_Select([
        "table"  => "{$this->bd}cuentacheque",
        "values" => "idCuenta AS id,  CONCAT(Cuenta,' | ',NombreCuenta) AS valor"

        ]);
    }


    
    function lsFoliosCerrados($array){
        $query = "
        SELECT
        idFolio AS id,
        CONCAT(Folio ,' | ', FechaModificacion) as valor
       
        FROM
        {$this->bd}folio

        WHERE Fecha = ?
        -- and id_estado = 2";

        return  $this->_Read($query,$array);
    }

    function lsDestinos() {
        $query = "
            SELECT
                idUI as id,
                Name_IC as valor
            FROM
            {$this->bd}insumos_udn
            INNER JOIN {$this->bd}insumos_clase ON insumos_udn.id_IC = insumos_clase.idIC
            WHERE
            id_UDN = 2
            AND Stado = 1
        ";
        return $this->_Read($query,null);

    }

    
    function add_archivo($array){

        return $this->_Insert([
            'table'  => "{$this->bd}cheques",
            'values' => $array['values'],
            'data'   => $array['data']
        ]);
    }
    
    function lsUDN(){
        $query ="SELECT idUDN AS id, UDN AS valor FROM {$this->bd}udn WHERE Stado = 1";
        return $this->_Read($query, null);
    }
    
    function lsChequesHistorial($array){
    
        $query ="
            SELECT
                idCheque as id,
                Fecha,
                cheques.Nombre,
                banco.Banco,
                ROUND(Importe, 2) as importe,
                cheques.Concepto,
                cheques.idCheque,
                cuentacheque.NombreCuenta,
                Name_IC
            FROM
            {$this->bd}cheques
            INNER JOIN {$this->bd}cuentacheque ON cheques.Cuenta = cuentacheque.idCuenta
            INNER JOIN {$this->bd}banco ON cheques.id_Banco = banco.idBanco
            INNER JOIN {$this->bd}insumos_udn ON cheques.id_destino = insumos_udn.idUI
            INNER JOIN {$this->bd}insumos_clase ON insumos_udn.id_IC = insumos_clase.idIC

            WHERE Fecha BETWEEN   ? AND ?

            -- AND status = 1

            Order by idCheque desc
        ";

        return $this->_Read($query, $array);
    }

    function lsCheques($array){
    
        $query ="
            SELECT
                idCheque as id,
                cheques.Nombre,
                banco.Banco,
                ROUND(Importe, 2) as importe,
                cheques.Concepto,
                cheques.idCheque,
                cuentacheque.NombreCuenta,
                Name_IC
            FROM
            {$this->bd}cheques
            INNER JOIN {$this->bd}cuentacheque ON cheques.Cuenta = cuentacheque.idCuenta
            INNER JOIN {$this->bd}banco ON cheques.id_Banco = banco.idBanco
            INNER JOIN {$this->bd}insumos_udn ON cheques.id_destino = insumos_udn.idUI
            INNER JOIN {$this->bd}insumos_clase ON insumos_udn.id_IC = insumos_clase.idIC

            WHERE Fecha = ?

            Order by idCheque desc
        ";

        return $this->_Read($query, $array);
    }

    function quitarArchivo($array) {

        return $this->_Delete([
            'table'  => "{$this->bd}cheques",
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    }

    function removeCheque($array) {

        return $this->_Update([
            'table'  => "{$this->bd}cheques",
            'values' => $array['values'],
            'where'  => $array['where'],
            'data'   => $array['data']
        ]);
    
    }




    function lsHistorialTC($array){
        $query = "
            SELECT 
            Fecha,
            Monto,TCodigo,
            TTCodigo,Concepto,Especificacion,
            Cliente,Autorizacion,Observaciones,Fecha,
            
            propina,idTC
            FROM 
            {$this->bd2}tc,
            {$this->bd2}terminal,
            {$this->bd2}tipo_terminal,
            {$this->bd2}folio 
            WHERE idTipoTerminal = id_TipoTerminal 
            AND idTerminal = id_Terminal 
            AND id_Folio = idFolio 
            AND DATE_FORMAT(Fecha,'%Y-%m-%d') BETWEEN ? AND ?
            Order by fecha DESC 


        ";
        return $this->_Read($query,$array);
    }

    function lsArchivos($array){
        $query = "
            SELECT
            sobres.idSobre AS id,
            sobres.Fecha,
            sobres.Archivo,
            sobres.Descripcion,
            ROUND(Peso, 2) AS peso,
            sobres.Hora,
            sobres.Type_File,
            sobres.Ruta,
            check_list.check_name,
            check_list.id_categoria,
            categoria.Categoria,
            sobres.Peso,
            sobres.motivo
            FROM
            {$this->bd2}sobres
            INNER JOIN {$this->bd2}check_list ON sobres.id_checklist = check_list.idcheck
            INNER JOIN {$this->bd2}categoria ON check_list.id_categoria = categoria.idCategoria
            WHERE DATE_FORMAT(Fecha,'%Y-%m-%d') BETWEEN ? AND ?
        
            ";
        return $this->_Read($query,$array);
    }



}
?>