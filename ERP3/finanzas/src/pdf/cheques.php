<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <title> * Poliza *</title>


 <link rel="stylesheet" href="../../../src/plugin/bootstrap-5/css/bootstrap.min.css">
 <!-- <link rel="stylesheet" href="../../../src/css/navbar.css"> -->


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
  margin-top:  20px;
  margin-bottom:   20px;
  margin-left:   20px;
  margin-right:    30px;
 }
 </style>
<!-- onload="imprimir();" -->
 <body>


<?php 



require_once('../../../mdl/mdl-contabilidad.php');
// $obj = new Contabilidad;


?>


 <div class="row">
  <div class="form-group col-sm-12 col-xs-12" >
   
   <label style=" font-size:16px; border-radius: 1px;" class="form-control line text-center">
    POLIZA DE CHEQUE</label>
    
    <h3> 
        <label class="col-sm-12 col-xs-12 text-center  "></label>
    </h3>
  
   </div>
 </div>


    <div class="row line">
  
    <div class="form-group col-sm-12 col-xs-12 mt-3" style="margin:0px;">
        <label class="col-sm-2 col-xs-2">FECHA: </label>
        <span class="col-sm-10 col-xs-10"></span>
    </div>

    <div class="form-group col-sm-12 col-xs-12" style="margin:0px;">
        <label class="col-sm-2 col-xs-2">NOMBRE: </label>
        <span class="col-sm-10 col-xs-10"></span>
    </div>

    <div class="form-group col-sm-12 col-xs-12" style="margin:0px;">
        <label class="col-sm-2 col-xs-2">IMPORTE: </label>
        <span class="col-sm-10 col-xs-10"></span>
    </div>

    <div class="form-group col-sm-12 col-xs-12" style="margin:0px;">
        <label class="col-sm-2 col-xs-2">BANCO: </label>
        <span class="col-sm-10 col-xs-10"></span>
    </div>

    <div class="form-group col-sm-12 col-xs-12" style="margin:0px;">
    <label class="col-sm-2 col-xs-2">CUENTA: </label>
    <span class="col-sm-10 col-xs-10"></span>
    </div>

    <div class="form-group col-sm-12 col-xs-12" style="margin:0px;">
    <label class="col-sm-2 col-xs-2">CHEQUE: </label>
    <span class="col-sm-10 col-xs-10"></span>
    </div>

    <div class="form-group col-sm-12 col-xs-12">
    <label class="col-sm-12 col-xs-12">CONCEPTO: </label>
    <span class="col-sm-12 col-xs-12"></span>
    </div>

    </div>


    <div class="row">
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






</body>
</html>
