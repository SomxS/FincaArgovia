<?php 
    if( empty($_COOKIE["IDU"]) )  require_once('../acceso/ctrl/ctrl-logout.php');

    require_once('layout/head.php');
    require_once('layout/script.php'); 
?>
<body>
    <?php require_once('../layout/navbar.php'); ?>
    <main>
        <section id="sidebar"></section>
        <div id="main__content">
            <nav aria-label='breadcrumb'>
    <ol class='breadcrumb'>
        <li class='breadcrumb-item text-uppercase text-muted'>finanzas</li>

        <li class='breadcrumb-item fw-bold active'>Movimientos diarios</li>
    </ol>
</nav>


<div class="row mb-3 d-flex">


    <div class="col-6 col-sm-6 col-lg-3   mb-1">
        <label for="iptDate" class="fw-bold">Fecha</label>
        <div class="input-group">
            <input type="text" class="form-control" id="iptDate">
            <span class="input-group-text"><i class="icon-calendar"></i></span>
        </div>
    </div>

    <div class="col-6 col-sm-6 col-lg-2 mb-1 " id="#btnTurno">
        <label col="col-12"> </label>
        <button type="button" id="btnApertura" class="btn btn-primary col-12" onclick="CrearTurno()">Crear
            turno</button>
        <button type="button" id="btnCierre" class="btn btn-outline-danger d-none col-12" onclick="CerrarTurno()">Cerrar
            turno</button>
    </div>

    <div class="col-12 col-sm-12 col-lg-5  offset-lg-2 text-end ">
        <span class="fw-bold " id="content-encargado"> </span>
        / <span class="fw-bold m-0" id="content-folio"> </span> 
        <p class="fw-bold m-0" id="content-hora"> </p> 
    </div>


</div>
<!-- offset-lg-3   -->



<style>
.content {
    padding-left: 2.5rem;
    padding-right: 2.5rem;
    margin-left: 15.875rem;
    min-height: 950px;
}

</style>

<div id="content-tab-ingresos"  class=" content mb-3"></div>

<!-- style="height:750px;" -->

<div id="content-datax" class="d-none">

    <ul class="nav nav-tabs" id="myTabs">

        <li class="nav-item">
            <a class="nav-link active" id="tab2-tab" data-bs-toggle="tab" href="#tab2" onclick="ingresos()">Ingresos</a>
        </li>

        <li class="nav-item">
            <a class="nav-link " id="tab3-tab" data-bs-toggle="tab" href="#tab3" onclick="lsTC()">T.C.</a>
        </li>

        <li class="nav-item">
            <a class="nav-link " id="tab-tab" data-bs-toggle="tab" href="#tab" onclick="lsArchivos()">Archivos</a>
        </li>

        <li class="nav-item ">
            <a class="nav-link " id="tab4-tab" data-bs-toggle="tab" href="#tab4" onclick="modulo_rpt_gral()">Reporte
                General</a>
        </li>

        <li class="nav-item">
            <a class="nav-link " id="tab5-tab" data-bs-toggle="tab" href="#tab5" onclick="modulo_turnos()">Turnos</a>
        </li>

    </ul>


    <div class="tab-content mt-2 ">

        <div class="tab-pane fade show active" id="tab2">
            <div class="line" id="content-ingresos"></div>
            <div id="content-data" class="col-12"></div>
        </div>

        <div class="tab-pane fade " id="tab3"></div>

        <div class="tab-pane fade  " id="tab"></div>

        <div class="tab-pane fade " id="tab4">
            <div class="col-12 mb-3 mt-3 " id="rptFilter"></div>

            <div id="frm-rpt-general" class="line p-12">
                <div class="" id="content-rpt"></div>


                <div class="col-12" id="rptGeneral"></div>
                <div class="col-12" id="rptFP"></div>
                <div class="col-12" id="rptPropina"></div>
            </div>
        </div>

        <div class="tab-pane  fade " id="tab5">

            <div id="content-rptTurnos"></div>
            <div class="mt-2 p-6" id="content-Turnos"></div>
        </div>



    </div>


</div>




<script src='src/js/movimientos-diarios.js?t=<?php echo time(); ?>'></script>
<script src='src/js/movimientos-diarios-tc.js?t=<?php echo time(); ?>'></script>
<script src='src/js/movimientos-diarios-turnos.js?t=<?php echo time(); ?>'></script>
<script src='src/js/movimientos-diarios-cortesias.js?t=<?php echo time(); ?>'></script>
<script src='src/js/movimientos-diarios-cxc.js?t=<?php echo time(); ?>'></script>
<script src='src/js/movimientos-diarios-rpt.js?t=<?php echo time(); ?>'></script>
<script src='src/js/movimientos-diarios-ingresos.js?t=<?php echo time(); ?>'></script>
<script src='src/js/movimientos-diarios-files.js?t=<?php echo time(); ?>'></script>
<script src='src/js/movimientos-diarios-app.js?t=<?php echo time(); ?>'></script>
</div>
    </main>
</body>
</html>