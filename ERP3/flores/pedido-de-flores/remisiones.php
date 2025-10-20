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
        <li class='breadcrumb-item text-uppercase text-muted'>flores</li>
        <li class='breadcrumb-item text-uppercase text-muted'>pedido-de-flores</li>
        <li class='breadcrumb-item fw-bold active'>Remisiones</li>
    </ol>
</nav>

<!-- Template Layout consulta  -->

<div class="row">

    <div class="col-lg-4 col-12 mt-3" id="lsTabla">
        <div class="row">
            <div class="col-12 col-sm-6 col-lg-6  col-sm-5  mt-3">

                <div class="input-group">
                    <input type="text" class="form-control" id="iptDate">
                    <span class="input-group-text pointer"><i class="icon-calendar"></i></span>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-lg-3 col-sm-3 mt-3">
                <button type="button" class="btn btn-primary col-12" onclick="lsFolios()">Consultar</button>
            </div>


        </div>

        <div class="mt-4 table-responsive" id="content-folios"> </div>



    </div><!-- lsTabla  -->


    <div class="col-lg-8 col-12 line mt-3" id="content-visor"></div> <!-- Layout visor  -->

</div>


<script src='src/js/remisiones.js?t=<?php echo time(); ?>'></script>
        </div>
    </main>
</body>
</html>