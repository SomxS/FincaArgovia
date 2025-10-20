<?php
require_once '../../conf/_CRUD.php';

class Ingresos extends CRUD
{
    private $bd;
    private $bd2;

    public function __construct()
    {
        $this->bd2 = "hgpqgijw_finanzas2.";
        $this->bd = "hgpqgijw_finanzas.";
    }

    public function lsCategorias($array) {
        $sql = "SELECT
        categoria.idCategoria as id,
        categoria.Categoria as valor,
        categoria.Categoria,
        categoria.id_TMovimiento
        FROM
        hgpqgijw_finanzas.categoria
        WHERE
        id_UDN = ? and idCategoria <> 12 and idCategoria <> 13";
        return $this->_Read($sql, $array);
    }

    public function lsSubcategorias(){
        $query = "
        SELECT idSubcategoria as id, Subcategoria as valor
        FROM {$this->bd}subcategoria WHERE Stado = 1";
        return $this->_Read($query, null);
    }

    public function lsGrupo($array){

        $sql = "
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

        return $this->_Read($sql, $array);
    }

    public function Select_Subcategoria_x_grupo($array){
        $query = "SELECT idSubcategoria,Subcategoria,id_grupo
        FROM {$this->bd}subcategoria WHERE id_Categoria = ?
        and Stado = 1
        and id_grupo=?  order by idSubcategoria asc";
        return $this->_Read($query, $array);

    }

    public function lsFormasPago($array){
        $query = "
        SELECT idFormas_Pago,FormasPago
        FROM {$this->bd}formas_pago WHERE grupo = ?";
        $sql = $this->_Read($query, $array);
        return $sql;
    }

    public function Select_MontoFPago($array){

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

        $sql = $this->_Read($query, $array);

        foreach ($sql as $row3);

        if (!isset($row3['monto'])) {$row3['monto'] = 0;}

        return $row3['monto'];

    }

    public function getMontoMensual($array){

        $query = "

            SELECT
            folio.Fecha,
            bitacora_ventas.Subtotal,
            bitacora_formaspago.id_FormasPago,
            SUM(bitacora_formaspago.Monto) as total
            FROM
            {$this->bd}bitacora_ventas
            INNER JOIN {$this->bd}folio ON bitacora_ventas.id_Folio = folio.idFolio
            INNER JOIN {$this->bd}bitacora_formaspago ON bitacora_formaspago.id_Bitacora = bitacora_ventas.idVentasBit
            WHERE
            id_Subcategoria = ? AND
            MONTH(Fecha) = ?
            AND YEAR(Fecha) = ?

        ";

        $sql = $this->_Read($query, $array);

        return $sql[0];

    }

    public function getIngresoMensual($array){
        $monto = 0;
        $sql = "
        
        SELECT
            ROUND(SUM(Monto),2) as monto,
            bitacora_formaspago.id_FormasPago
            FROM
            {$this->bd}folio
            INNER JOIN {$this->bd}bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
            INNER JOIN {$this->bd}bitacora_formaspago ON bitacora_formaspago.id_Bitacora = bitacora_ventas.idVentasBit
        WHERE
            MONTH(folio.Fecha) = ?
            AND YEAR(folio.Fecha) = ?
            AND
        bitacora_ventas.id_Subcategoria = ? and id_FormasPago = ?
        ";

        $ps = $this->_Read($sql, $array);
        foreach ($ps as $key) {
            $monto = $key['monto'];
        }

        return $monto;
    }

    public function getChequePromedio($array)
    {

        $sql = "
            SELECT
                bitacora_ventas.id_Folio,
                SUM(bitacora_ventas.pax) AS pax,
                bitacora_ventas.id_Subcategoria,
                bitacora_ventas.Subtotal,
                bitacora_ventas.Noche,
                folio.Fecha
            FROM
            hgpqgijw_finanzas.bitacora_ventas
            INNER JOIN hgpqgijw_finanzas.folio ON bitacora_ventas.id_Folio = folio.idFolio
            WHERE
            id_Subcategoria = ?
            AND MONTH(folio.Fecha) = ?
            AND YEAR(folio.Fecha) = ?
            ";

        $ps = $this->_Read($sql, $array);
        return $ps[0];
    }

    public function ver_ingresos_turismo($array)
    {

        $query = "
        SELECT
            SUM(Monto) as Monto ,
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

        $ls = $this->_Read($query, $array);
        $monto = count($ls);

        foreach ($ls as $row3) {
            $monto = $row3['Monto'];
        }

        return $monto;

    }

    public function ExisteEnBitacora($date1, $date2, $idSub, $campo)
    {

        $res = null;

        $sql = '
        SELECT
        SUM(' . $campo . ') as total,
        folio.Fecha,
        folio.Folio
        FROM
        hgpqgijw_finanzas.folio
        INNER JOIN hgpqgijw_finanzas.bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
        WHERE FECHA BETWEEN ? and ? and id_Subcategoria = ?';

        $ps = $this->_Read($sql, array($date1, $date2, $idSub));
        foreach ($ps as $key) {$res = $key['total'];}

        return $res;
    }

    public function Select_Total2($date1, $date2, $idS)
    {

        $array = array($date1, $date2, $idS);
        $query = "
        SELECT

        ROUND(SUM(Monto),2) as Monto
        FROM
        hgpqgijw_finanzas.folio
        INNER JOIN hgpqgijw_finanzas.bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
        INNER JOIN hgpqgijw_finanzas.bitacora_formaspago ON bitacora_formaspago.id_Bitacora = bitacora_ventas.idVentasBit
        WHERE
        folio.Fecha BETWEEN ? AND ? AND
        bitacora_ventas.id_Subcategoria = ?";

        $sql = $this->_Read($query, $array);

        foreach ($sql as $row3);
        if (!isset($row3['Monto'])) {$row3['Monto'] = 0;}

        return $row3['Monto'];

    }

    // Ingresos por folio y categoria

    public function listIngresosPorGrupo($array)
    {

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
            grupo.gruponombre,
            idVentasBit
        FROM
        {$this->bd2}folio
        INNER JOIN {$this->bd2}bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
        INNER JOIN {$this->bd2}subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        INNER JOIN {$this->bd2}grupo ON subcategoria.id_grupo = grupo.idgrupo
        WHERE id_grupo = ? and id_Categoria = ? and idFolio = ?
        ";
        return $this->_Read($sql, $array);
    }

    public function lsMontoFormasPago($array)
    {

        $query = "
        SELECT
        bitacora_formaspago.id_FormasPago,
        SUM(bitacora_formaspago.Monto) as monto,
        bitacora_formaspago.id_Bitacora,
        bitacora_ventas.id_Folio
        FROM
        {$this->bd2}bitacora_formaspago
        INNER JOIN {$this->bd2}bitacora_ventas ON bitacora_formaspago.id_Bitacora = bitacora_ventas.idVentasBit
        WHERE id_Bitacora = ? and id_FormasPago = ?


        ";

        $sql = $this->_Read($query, $array);

        foreach ($sql as $row3);

        if (!isset($row3['monto'])) {$row3['monto'] = 0;}

        return $row3['monto'];

    }

    public function listVentasPorCategoria($array)
    {

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
        {$this->bd2}folio
        INNER JOIN {$this->bd2}bitacora_ventas ON bitacora_ventas.id_Folio = folio.idFolio
        INNER JOIN {$this->bd2}subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
        INNER JOIN {$this->bd2}grupo ON subcategoria.id_grupo = grupo.idgrupo
        WHERE id_Categoria = ? and idFolio = ?

        ";

        return $this->_Read($sql, $array);

    }

    function lsCuartosOcupados($array){

        $sql = "
            SELECT
                folio.Fecha,
                folio.Folio,
                count(*) as cuartosOcupados
            FROM
                {$this->bd}bitacora_ventas
                INNER JOIN {$this->bd}folio ON bitacora_ventas.id_Folio = folio.idFolio
                INNER JOIN {$this->bd}subcategoria ON bitacora_ventas.id_Subcategoria = subcategoria.idSubcategoria
            WHERE 
            
            id_Categoria      = ? 
            AND YEAR(Fecha)   = ?
            AND MONTH(Fecha)  = ?

            ORDER BY FECHA DESC
        ";

         return $this->_Read($sql, $array)[0];

    }

    public function lsImpuestos($id)
    {
        $array = array($id);

        $query = "
        SELECT
         idImpuesto, Impuesto, Valor
        FROM
        {$this->bd2}categoria,
        {$this->bd2}impuestos,
        {$this->bd2}categoria_impuesto
        WHERE idCategoria = id_Categoria
        AND idImpuesto    = id_Impuesto
        AND id_Categoria  = ? ";
        return $this->_Read($query, $array);

    }

    public function __getMontoImpuesto($array)
    {

        $__getMonto = 0;

        $query = "
            SELECT
                bitacora_ventas.id_Folio,
                bitacora_ventas.id_Subcategoria,
                bitacora_impuesto.id_Impuesto,
                bitacora_impuesto.Monto
            FROM
            {$this->bd2}bitacora_ventas
            INNER JOIN {$this->bd2}bitacora_impuesto ON bitacora_impuesto.id_Bitacora = bitacora_ventas.idVentasBit
            WHERE id_Folio = ?
            AND id_Subcategoria = ?
            AND id_Impuesto = ?
        ";

        $sql = $this->_Read($query, $array);

        foreach ($sql as $row) {
            $__getMonto = $row['Monto'];
        }

        return $__getMonto;
    }

    public function __getMovimientos($array)
    {
        $__getMov = 0;

        $query = "
            SELECT
                count(*) as mov
            FROM {$this->bd2}bitacora_ventas
            WHERE id_Folio = ? and
            id_Subcategoria = ?";

        $sql = $this->_Read($query, $array);

        foreach ($sql as $key) {
            $__getMov = $key['mov'];
        }

        return $__getMov;
    }

}

class Analisisdeingresos extends CRUD
{
    public function lsUDN()
    {
        $query = "SELECT idUDN AS id, UDN AS valor FROM udn WHERE Stado = 1";
        return $this->_Read($query, null);
    }

    public function VER_CATEGORIAS($array)
    {
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

    public function ver_ingresos_turismo($array)
    {

        $query = "
   SELECT
    SUM(Monto) as Monto ,
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

        $ls = $this->_Read($query, $array);
        $monto = count($ls);

        foreach ($ls as $row3) {
            $monto = $row3['Monto'];
        }

        return $monto;

    }

    public function Select_group($array)
    {

        $sql = "
    SELECT

        grupo.idgrupo,
        grupo.gruponombre
    FROM
        hgpqgijw_finanzas.grupo
        INNER JOIN hgpqgijw_finanzas.subcategoria ON subcategoria.id_grupo = grupo.idgrupo
        WHERE id_Categoria = ? and Stado = 1
    GROUP BY
    grupo.idgrupo
    ";

        return $this->_Read($sql, $array);
    }

    public function Select_Subcategoria_x_grupo($array)
    {

        $sql = "SELECT idSubcategoria,Subcategoria,id_grupo
  FROM hgpqgijw_finanzas.subcategoria
  WHERE id_Categoria = ? and Stado = 1 and
  id_grupo=? order by idSubcategoria asc";

        return $this->_Read($sql, $array);
    }

    public function Select_formaspago_by_categoria($array)
    {
        $query = "SELECT idFormas_Pago as id,FormasPago  as formas  FROM hgpqgijw_finanzas.formas_pago WHERE grupo = ?";
        return $this->_Read($query, $array);

    }

}
