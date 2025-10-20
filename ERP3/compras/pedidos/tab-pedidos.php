  <!-- PRODUCTOS -->
      <section class="container">
         <div class="row">
            <div class=" mt-25 card p-0">

               <div class="card-header fw-bold">
                  FORMATO DE PEDIDO DE MATERIALES
               </div>


               <div class="card-body p-sm-2 p-md-4 p-lg-4 p-xl-4">
                  <div class="card-body">

                     <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                           <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                              type="button" role="tab" aria-controls="home" aria-selected="true">Formato Pedido</button>
                        </li>
                        <li class="nav-item" role="presentation">
                           <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                              type="button" role="tab" aria-controls="profile" aria-selected="false"
                              onclick="list_pedidos()">List Pedido</button>
                        </li>

                     </ul>

                     <div class="tab-content" id="myTabContent">

                        <div class="tab-pane fade show active" id="home">
                           <?php
                           //  include 'tab_formato_pedido.php';
                             ?>
                        </div>

                        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                           <?php
                           //  include 'list_pedidos.php';
                             ?>
                        </div>
                     </div>





                  </div>

               </div>
               <div class="card-footer text-center text-md-center text-lg-end">

               </div>
            </div>
         </div>
      </section>