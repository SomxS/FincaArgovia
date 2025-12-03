<?php
session_start();

// Validar sesión de usuario
if (empty($_COOKIE["IDU"])) {
    require_once('../../acceso/ctrl/ctrl-logout.php');
    exit();
}

require_once('layout/head.php');
require_once('layout/core-libraries.php');
?>

<<<<<<< HEAD
    <link rel="stylesheet" href="../../src/css/compact.css">
=======
<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest"></script>
>>>>>>> d77e6fc7d11310b31f952fa9300f07b56a1ce959

<!-- CoffeeSoft Framework -->
<script src="../../src/js/coffeeSoft.js"></script>
<script src="https://rawcdn.githack.com/SomxS/Grupo-Varoch/refs/heads/main/src/js/plugins.js"></script>
<script src="https://www.plugins.erp-varoch.com/ERP/JS/complementos.js"></script>

<body>
    <?php require_once('../../layout/navbar.php'); ?>

    <main>
        <section id="sidebar"></section>

        <div id="main__content">
            <!-- Breadcrumb Navigation -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item text-uppercase text-muted">Operación</li>
                    <li class="breadcrumb-item fw-bold active">Almacén</li>
                </ol>
            </nav>

            <!-- Main Container -->
            <div class="main-container" id="root"></div>

<<<<<<< HEAD
            <!-- Módulo de Almacén Principal -->
            <script src="js/almacen.js?t=<?php echo time(); ?>"></script>
=======
            <!-- Componente tabLayout -->
            <script src="../../src/js/components/tabLayout.js?t=<?php echo time(); ?>"></script>
            
            <!-- Módulo de Catálogo -->
            <script src="js/catalogo.js?t=<?php echo time(); ?>"></script>
>>>>>>> d77e6fc7d11310b31f952fa9300f07b56a1ce959
        </div>
    </main>
</body>
</html>
