


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
        <li class='breadcrumb-item fw-bold active'>Punto de venta</li>
    </ol>
</nav>

<link rel="stylesheet" href="pedido-de-flores/src/css/argovia.css">

<div class="row mb-3 d-flex ">

    <div class="col-12 col-sm-6 col-lg-2">

        <button type="button" class="btn btn-primary col-12 mb-2 mt-2" id="btnHistorial" onclick="list_folio()"><i
                class="icon-history"></i> Historial</button>
    </div>


    <div class="col-12 col-sm-6 col-lg-3 mb-2 mt-2">

        <button type="button" class="btn btn-info col-12" id="btnPedido">
            <i class="icon-truck"></i>
            Realizar Pedido</button>
    </div>

    <div class="col-12 col-sm-6 col-lg-2 mb-2 mt-2">

        <button type="button" class="btn btn-warning col-12" id="btnCerrarP" onclick="view_cerrar_pedidos()">
            <i class="icon-truck"></i>
            Cerrar Pedidos</button>
    </div>


</div>

<div class="div-content" id="content-pedidos">


    <!-- Content-Historial -->

    <div class="row d-none" id="content-historial">



        <div class="col-12  col-md-6 col-sm-12 ">

            <div class="row">
                <div class="col-12 col-md-6 col-lg-5  col-sm-6  mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="iptDate">
                        <span class="input-group-text pointer"><i class="icon-calendar"></i></span>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3 col-sm-3 mb-3">
                    <button type="button" class="btn btn-primary col-12" 
                        onclick="list_folio()">Consultar</button>
                </div>


            </div>

            <div class="mt-3 table-responsive" id="tbTicket"> </div>

        </div>


        <div class="col-12  col-md-6 col-sm-12 " id="content-folio"></div>

    </div><!-- content historial -->







    <div class="" id="content-pos">
        <?php 
        include('agregar_pedido.php');?>
    </div>
    <div id="content-cerrar-pedidos">
        <div class="row">
            <div class="col-6 col-lg-4 col-md-4 col-sm-4">
                <button class="btn btn-success" onclick="cerrarGrupoPedidos()">
                    <i class="icon-truck"></i> Cerrar Pedidos </button>
            </div>
        </div>
        <div id="content-tabla-cerrar-pedidos"></div>
    </div>
</div>

<style>
.ui-autocomplete {
    position: absolute;
    z-index: 2150000000 !important;
    cursor: default;
    border: 2px solid #ccc;
    padding: 5px 0;
    border-radius: 2px;
}
</style>


<script src="src/js/cerrar-pedidos.js?t=<?php echo time(); ?>"></script>
<script src='src/js/punto-de-venta.js?t=<?php echo time(); ?>'></script>
<script src='src/js/json_punto_de_venta.js?t=<?php echo time(); ?>'></script>
<script src='src/js/historial_punto_de_venta.js?t=<?php echo time(); ?>'></script>

        </div>
    </main>
</body>
</html>