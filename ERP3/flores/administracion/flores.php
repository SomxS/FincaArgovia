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
        <li class='breadcrumb-item text-uppercase text-muted'>administracion</li>
        <li class='breadcrumb-item fw-bold active'>Flores</li>
    </ol>
</nav>

<div class="row">

    <div class="col-lg-4 col-12 mt-3" id="lsTabla">
 

        <div class="mt-4 table-responsive" id="content-form">


        </div>



    </div><!-- lsTabla  -->


    <div class="col-lg-8 col-12 line mt-3 p3" >
        <div class="mt-3" id="content-visor"></div>
    </div> <!-- Layout visor  -->

</div>


<script src='src/js/flores.js?t=<?php echo time(); ?>'></script>
<!-- externo -->
<script src="https://15-92.com/ERP3/src/js/complementos.js?t=<?php echo time(); ?>"></script>

        </div>
    </main>
</body>
</html>