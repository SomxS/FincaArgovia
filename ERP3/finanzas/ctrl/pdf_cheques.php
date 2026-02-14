<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <title> * Poliza *</title>

 <link rel="stylesheet" href="../../src/plugin/bootstrap-5/css/bootstrap.min.css">

 <script type="text/javascript">
 function imprimir() {
  if (window.print) {
   window.print();

  }
  else {
   alert("La función de impresión no esta disponible en este navegador, intentelo con otro diferente.");
  }
 }
 </script>


 <style type="text/css" media="print">
 @page{
  margin-top:  25px;
  margin-bottom:   25px;
  margin-left:   25px;
  margin-right:    35px;
 }
 </style>
<!--  -->
 <body onload="imprimir()">
 <main class="container-fluid" >

<?php 

require_once('../mdl/mdl-contabilidad.php');
$obj = new Cheques;

    $sql =  $obj->  Select_Datos_Cheques($_GET['top']);
    foreach($sql as $row);

    $ruta = '../../../'.$row['Ruta'].''.$row['Archivo'];


    echo '
    <div class="row">
        <div class="form-group col-sm-12 col-xs-12" >

        <label style=" font-size:16px; border-radius: 1px;" class="form-control line text-center">
        POLIZA DE CHEQUE</label>

        <h4> 
        <label class="col-sm-12 col-xs-12 text-center  mt-2">'.$row['NombreCuenta'].'</label>
        </h4>

        </div>
    </div>


    <div class="row line">
  
    <div class="form-group col-sm-12 col-xs-12 mt-3" style="margin:0px;">
        <label class="col-sm-2 col-xs-2 fw-bold">FECHA: </label>
        <span class="col-sm-10 col-xs-10">'.$row['fecha'].'</span>
    </div>

    <div class="form-group col-sm-12 col-xs-12" style="margin:0px;">
        <label class="col-sm-2 col-xs-2 fw-bold">NOMBRE: </label>
        <span class="col-sm-10 col-xs-10">'.$row['Nombre'].'</span>
    </div>

    <div class="form-group col-sm-12 col-xs-12" style="margin:0px;">
        <label class="col-sm-2 col-xs-2 fw-bold">IMPORTE: </label>
        <span class="col-sm-10 col-xs-10">'.evaluar($row['Importe']).'</span>
    </div>

    <div class="form-group col-sm-12 col-xs-12" style="margin:0px;">
        <label class="col-sm-2 col-xs-2 fw-bold">BANCO: </label>
        <span class="col-sm-10 col-xs-10">'.$row['Banco'].'</span>
    </div>

    <div class="form-group col-sm-12 col-xs-12" style="margin:0px;">
    <label class="col-sm-2 col-xs-2 fw-bold">CUENTA: </label>
    <span class="col-sm-10 col-xs-10">'.$row['Cuenta'].'</span>
    </div>

    <div class="form-group col-sm-12 col-xs-12" style="margin:0px;">
    <label class="col-sm-2 col-xs-2 fw-bold">CHEQUE: </label>
    <span class="col-sm-10 col-xs-10">'.$row['Cheque'].'</span>
    </div>

    <div class="form-group col-sm-12 col-xs-12">
    <label class="col-sm-12 col-xs-12 fw-bold">CONCEPTO: </label>
    <span class="col-sm-12 col-xs-12">'.$row['Concepto'].'</span>
    </div>

    </div>


    <div class="row mt-4">
    <div class="form-group col-sm-4 col-xs-4 text-center">
        <label for="">RECIBIÓ</label>
        </div>
        </div>
        <div class="row">
        <div class="form-group col-sm-4 col-xs-4">
        <label class="col-sm-4 col-4 line ">Nombre</label>
        <label class="col-sm-8 col-8 line" style="border-bottom:1px solid #000;"> </label>
        </div>
        </div>
        <div class="row">
        <div class="form-group col-sm-4 col-xs-4">
        <label class="col-sm-4 col-4">Fecha</label>
        <label class="col-sm-8 col-8" style="border-bottom:1px solid #000;"> </label>
        </div>
        </div>
        <div class="row">
        <div class="form-group col-sm-4 col-xs-4">
        <label class="col-sm-4 col-4">Firma</label>
        <label class="col-sm-8 col-8" style="border-bottom:1px solid #000;"> </label>
    </div>
    </div>

    <div class="row mt-3">
    <div class="form-group col-sm-6 col-xs-6 text-center">
    <label for="">ELABORÓ</label>
    <br><br><br>
    <span class="col-sm-12 col-xs-12" style="border-top:0.5px solid #000 ;">NOMBRE Y FIRMA</span>
    </div>
    <div class="form-group col-sm-6 col-xs-6 text-center">
    <label for="">AUTORIZÓ</label>
    <br><br><br>
    <span class="col-sm-12 col-xs-12" style="border-top:0.5px solid #000 ;">NOMBRE Y FIRMA</span>
    </div>
    </div>

    <hr>

    <div class="row">
    
    <div class="form-group col-sm-12 col-xs-12">
    <span for="">COPIA CHEQUE</span>
   
    </div>

    </div>
    
    <div class="row">
   
        <div class="form-group col-sm-12 col-xs-12">
            <img src="'.$ruta.'" width="100%" class="img-rounded center-block">
        </div>
    
    </div>

    ';



    function evaluar($val, $signo = '$ '){

        return $val ?   $signo . number_format($val, 2, '.', ',') : '-';
    }
?>


 <!-- -->





</main>
</body>
</html>
