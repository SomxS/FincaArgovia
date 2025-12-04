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

<!-- CoffeeSoft Framework -->
   <!-- Compact Styles -->
<script src="../../src/js/coffeeSoft.js"></script>
<script src="https://rawcdn.githack.com/SomxS/Grupo-Varoch/refs/heads/main/src/js/plugins.js"></script>
<script src="https://www.plugins.erp-varoch.com/ERP/JS/complementos.js"></script>

<body>
    <div id="menu-navbar"></div>
    <div id="menu-sidebar"></div>
    <main>
        <div id="main__content">
            <div class="" id="root"></div>
        </div>
    </main>

    <script src="../../acceso/src/js/navbar.js"></script>
    <script src="../../acceso/src/js/sidebar.js"></script>
     <!-- Módulo de Catálogo -->
    <script src="js/catalogo.js?t=<?php echo time(); ?>"></script>

</body>
</html>
