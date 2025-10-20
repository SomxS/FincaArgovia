<?php 
    if( empty($_COOKIE["IDU"]) )  require_once('../../acceso/ctrl/ctrl-logout.php');

    require_once('layout/head.php');
    require_once('layout/script.php'); 
?>
<body>
    <?php require_once('../../layout/navbar.php'); ?>
    <main>
        <section id="sidebar"></section>
        <div id="main__content">
            
            <nav aria-label='breadcrumb'>
   <ol class='breadcrumb'>
      <li class='breadcrumb-item text-uppercase text-muted'>direccion</li>
      <li class='breadcrumb-item text-uppercase text-muted'>reportes</li>
      <li class='breadcrumb-item fw-bold active'>Reportes ingreso</li>
   </ol>
</nav>

<script src='src/js/reportes-ingreso.js'></script>


<div class="row mb-3">
   <div class="col-12 col-sm-6 col-lg-2 mb-3">
      <label for="cbUDN">Seleccionar UDN</label>
      <select class="form-select" id="cbUDN"></select>
   </div>
    
    <div class="col-12 col-sm-6 col-lg-2 mb-3">
      <label for="cbUDN">Seleccionar reporte</label>
      <select class="form-select" id="txtReporte">

      <option value="1">Reporte detallado de ingreso</option>
      <option value="1">Reporte anual de ingreso </option>
      </select>
   </div>

  
   

   <div class="col-12 col-sm-6 col-lg-2 mb-3">
      <label col="col-12"> </label>
      <button type="button" class="btn btn-primary col-12" onclick="Consultar_ingresos()">Consultar</button>
   </div>
</div>

<div class="row" id="tbDatos">

</div>


        </div>
    </main>
</body>
</html>