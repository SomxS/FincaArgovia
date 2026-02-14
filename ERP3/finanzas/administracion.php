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
                    <li class='breadcrumb-item text-uppercase text-muted'>finanzas</li>

                    <li class='breadcrumb-item fw-bold active'>Administracion</li>
                </ol>
            </nav>


            <div class="mt-3" id="contentData"></div>

            <script src='src/js/administracion.js?t=<?php echo time(); ?>'></script>

        </div>
    </main>
</body>

</html>