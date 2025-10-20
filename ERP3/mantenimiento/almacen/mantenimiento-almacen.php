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
        <li class='breadcrumb-item text-uppercase text-muted'>mantenimiento</li>
        <li class='breadcrumb-item text-uppercase text-muted'>almacen</li>
        <li class='breadcrumb-item fw-bold active'>Mantenimiento almacen</li>
    </ol>
</nav>
<div class="row mb-3">
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <label for="cbUDN">Seleccionar zona</label>
        <select class="form-select" id="cbUDN"></select>
    </div>

    <div class="col-12 col-sm-6 col-lg-2 mb-2">
        <label col="col-12"> </label>
        <button type="button" class="btn btn-primary col-12" id="sd" onclick="ver_list_mtto()">Consultar</button>
    </div>

    <div class="col-12 col-sm-6 col-lg-2 mb-2">
        <label col="col-12"> </label>
        <button type="button" class="btn btn-secondary col-12" id="sd" onclick="list_item()">Valorizar</button>
    </div>

    <div class="col-12 col-sm-6 col-lg-2 mb-2">
        <label col="col-12"> </label>
        <button type="button" class="btn btn-warning col-12" id="sd" onclick="list_item()">Subir
            Registros</button>
    </div>

</div>

<div id="tbDatos" style="height:800px; ">
    <div class="d-none row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 d-flex justify-content-center"
        id="content-item"></div>
    <div class="d-none row" id="content-tabla"></div>
</div>


<script src='src/js/mantenimiento-almacen.js?t=<?php echo time(); ?>'></script>
        </div>
    </main>
</body>
</html>