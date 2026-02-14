<?php 
    if( empty($_COOKIE["IDU"]) )  require_once('../acceso/ctrl/ctrl-logout.php');

    require_once('layout/head.php');
    require_once('layout/script.php'); 
?>
<script src="https://cdn.tailwindcss.com"></script>
<body>
    <?php require_once('../layout/navbar.php'); ?>
    <main>
        <section id="sidebar"></section>
        <div id="main__content">
            <nav aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                    <li class='breadcrumb-item text-uppercase text-muted'>finanzas</li>

                    <li class='breadcrumb-item fw-bold active'>Contabilidad</li>
                </ol>
            </nav>

            <div class="line mb-3 " id="content-bar"></div>
            <div id="content-data">




                <ul class="nav nav-tabs" id="myTabs">



                    <li class="nav-item">
                        <a class="nav-link active" id="tab2-tab" data-bs-toggle="tab" href="#tab2"
                            onclick="RptGral()">Reporte
                            general</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link " id="tab3-tab" data-bs-toggle="tab" href="#tab3" onclick="lsTC()">T.C.</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link " id="tab-tab" data-bs-toggle="tab" href="#tab"
                            onclick="lsArchivos()">Archivos</a>
                    </li>



                </ul>

                <div class="tab-content mt-2">

                    <div class="tab-pane fade" id="tab">
                        <div id="content-ingresos"></div>
                        <div id="" class="col-12"></div>
                    </div>

                    <div class="tab-pane fade show active" id="tab3"></div>





                    <div class="tab-pane  fade " id="tab2">
                        <div id="content-rptTurnos"></div>


                        <div class="mt-2 p-6" id="content-rpt"></div>
                        <div class="p-6" id="content-fp"></div>
                        <div class="p-6" id="content-propinas"></div>


                    </div>



                </div>


            </div>

            <script src='src/js/contabilidad.js?t=<?php echo time(); ?>'></script>
            <script src='src/js/contabilidad-gastos.js?t=<?php echo time(); ?>'></script>
            <script src='src/js/contabilidad-turnos.js?t=<?php echo time(); ?>'></script>


          

        </div>
    </main>
</body>

</html>