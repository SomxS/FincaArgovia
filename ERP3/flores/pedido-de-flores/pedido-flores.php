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
      <li class='breadcrumb-item fw-bold active'>Pedido-flores</li>
   </ol>
</nav>

<link rel="stylesheet" href="src/css/argovia.css">
<script src="src/js/pedido-flores.js?t=<?php echo time(); ?>"></script>
<script src="src/js/pedido-flores-cliente.js?t=<?php echo time(); ?>"></script>
<script src="src/js/pedido-historial.js?t=<?php echo time(); ?>"></script>


<div style="height:600px;" class="row">
   <div class="col-sm-12">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
         <li class="nav-item" role="presentation">
            <button onclick="__tab_pedidos()" class="nav-link active" id="home-tab" data-bs-toggle="tab"
               data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true"><i
                  class="icon-doc"></i> Formato Pedido  </button>
         </li>

    
         <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#historial" type="button"
               role="tab" aria-controls="profile" aria-selected="false" onclick="list_pedidos()">
               Historial de pedidos
            </button>
         </li>

         <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button"
               role="tab" aria-controls="profile" aria-selected="false" onclick="list_pedidos()">
               <i class=" icon-th-list"></i> Imprimir Lista
            </button>
         </li>

         <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#clientes" type="button"
               role="tab" aria-controls="profile" aria-selected="false" onclick="listado_clientes()">
               <i class=" icon-users"></i> Clientes
            </button>
         </li>



      </ul>



      <div class="tab-content" id="myTabContent">
         <div class="tab-pane fade show active" id="home">
            <?php 
            require_once('tab_formato_pedidos.php');
             ?>
         </div>

      <div class="tab-pane fade" id="historial" role="tabpanel" aria-labelledby="historial-tab">
      
         <?php
          require_once('tab_historial_pedidos.php');
           ?>
      </div>      


         <div class="tab-pane fade" id="clientes" role="tabpanel" aria-labelledby="clientes-tab">
            <div style="margin-top:15px; " class=" row">
               <div class="col-sm-2">
                  <a class="btn btn-outline-primary">Crear Cliente </a>
               </div>
            </div>

            <div id="content-clientes"></div>
            <?php 
            // include 'list_pedidos.php';
             ?>
         </div>




      </div>
   </div>
</div>

        </div>
    </main>
</body>
</html>