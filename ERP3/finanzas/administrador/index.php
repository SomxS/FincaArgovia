<?php
session_start();

// Validar sesión de usuario
// if (!isset($_SESSION['usuario'])) {
//     // header('Location: ../../login.php');
//     exit();
// }

  require_once('layout/head.php');
require_once('layout/core-libraries.php');
?>

<!-- CoffeeSoft Framework -->
<script src="../../src/js/coffeeSoft.js"></script>
<script src="https://rawcdn.githack.com/SomxS/Grupo-Varoch/refs/heads/main/src/js/plugins.js"></script>
<script src="https://www.plugins.erp-varoch.com/ERP/JS/complementos.js"></script>

<body>
        <?php require_once('../../layout/navbar.php'); ?>



    <main>
        <section id="sidebar"></section>

        <div id="main__content">
            <nav aria-label='breadcrumb'>
                <ol class='breadcrumb'>
                    <li class='breadcrumb-item text-uppercase text-muted'>Contabilidad</li>
                    <li class='breadcrumb-item fw-bold active'>Administrador</li>
                </ol>
            </nav>

          

            <div class=" main-container" id="root"></div>

          <!-- Módulos del Sistema -->
        <script src="js/admin.js?t=<?php echo time(); ?>"></script>
        <script src="js/cta.js?t=<?php echo time(); ?>"></script>



        <!-- <script src="js/cuenta-venta.js?t=<?php echo time(); ?>"></script>
        <script src="js/proveedores.js?t=<?php echo time(); ?>"></script>
        <script src="js/cliente.js?t=<?php echo time(); ?>"></script>
        <script src="js/formasPago.js?t=<?php echo time(); ?>"></script>
        
        <script src="js/efectivo.js?t=<?php echo time(); ?>"></script>
        <script src="js/moneda.js?t=<?php echo time(); ?>"></script>
        <script src="js/banco.js?t=<?php echo time(); ?>"></script> -->
            <!-- <script src="js/pago-proveedor.js"></script> -->

        


        </div>
    </main>
</body>
</html>
