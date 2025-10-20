<?php
if (empty($_COOKIE["IDU"])) {
    require_once '../acceso/ctrl/ctrl-logout.php';
}

require_once 'layout/head.php';
require_once 'layout/script.php';

?>

   <style>

    .box-panel{
        height: 70vh;
    }
    
    .ticket-panel{
        height: 70vh;
        display: flex;
        flex-direction: column;
    }
    
    .box-content{
          flex-grow: 1;
        height: 450px; /* Ajusta la altura del contenedor */
        width: 100%; /* O el ancho que prefieras */
        overflow-y: auto; /* Activa el scroll cuando el contenido exceda el tamaño */
        flex-wrap: wrap; /* Para que las cajas se ajusten en filas */
    }

    .ticket-content{
            overflow-y: auto; /* Activa el scroll cuando el contenido exceda el tamaño */
    }

    </style>


<body>
    <?php require_once '../../layout/navbar.php';?>
    <main>
        <section id="sidebar"></section>

        <div id="main__content">

            <nav aria-label='breadcrumb'>

                <ol class='breadcrumb'>
                    <li class='breadcrumb-item text-uppercase text-muted'>rio-cuilco</li>
                    <li class='breadcrumb-item text-uppercase text-muted'>punto-de-venta</li>
                    <li class='breadcrumb-item fw-bold active'>Pedidos</li>
                </ol>

            </nav>


            <div  id="root"></div>


            </div>

            <script  src='src/js/cerrarPedidos.js?t=<?php echo time(); ?>'></script>
            <script  src='src/js/pedidos.js?t=<?php echo time(); ?>'></script>
            <script  src='src/js/pedidos-historial.js?t=<?php echo time(); ?>'></script>
            <script  src='src/js/clientes.js?t=<?php echo time(); ?>'></script>

            <script  src='src/js/pos.js?t=<?php echo time(); ?>'></script>

        </div>
    </main>
</body>

</html>