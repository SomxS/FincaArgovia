<?php
if (empty($_COOKIE["IDU"])) {
    require_once '../acceso/ctrl/ctrl-logout.php';
}

require_once 'layout/head.php';
require_once 'layout/script.php';
?>

<body>
    <?php require_once '../layout/navbar.php';?>
    <main>
        <section id="sidebar"></section>
        <div id="main__content">
            <nav aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                    <li class='breadcrumb-item text-uppercase text-muted'>diversificados</li>

                    <li class='breadcrumb-item fw-bold active'>Punto de venta</li>
                </ol>
            </nav>

            <div id="root"></div>

            <script src='src/js/punto-de-venta.js?t=<?php echo time(); ?>'></script>
            <script src='src/js/punto-de-venta-clientes.js?t=<?php echo time(); ?>'></script>
            <script src='src/js/punto-de-venta-pos.js?t=<?php echo time(); ?>'></script>
            <script src='src/js/ventas.js?t=<?php echo time(); ?>'></script>
            <script src='src/js/inventary.js?t=<?php echo time(); ?>'></script>
        </div>
    </main>
</body>

</html>