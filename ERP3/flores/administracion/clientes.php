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
                    <li class='breadcrumb-item fw-bold active'>Clientes</li>
                </ol>
            </nav>
            <div class="row" id="filterBar"></div>

            <div class="row mt-3" id="contentData"></div>

            <script src='src/js/clientes.js?t=<?php echo time(); ?>'></script>

        </div>
    </main>
</body>

</html>