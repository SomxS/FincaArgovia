<nav aria-label='breadcrumb'>
   <ol class='breadcrumb'>
      <li class='breadcrumb-item text-uppercase text-muted'>compras</li>
      <li class='breadcrumb-item text-uppercase text-muted'>pedidos</li>
      <li class='breadcrumb-item fw-bold active'>Pedidos-mtto</li>
   </ol>
</nav>

<div>
   <div class="card-body p-sm-2 p-md-4 p-lg-4 p-xl-4">
      <div class="card-body">

         <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
               <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button"
                  role="tab" aria-controls="home" aria-selected="true">Formato Pedido</button>
            </li>
            <li class="nav-item" role="presentation">
               <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button"
                  role="tab" aria-controls="profile" aria-selected="false" onclick="list_pedidos()">List Pedido</button>
            </li>

         </ul>

         <div class="tab-content" id="myTabContent">

            <div class="tab-pane fade show active" id="home">
               <div class="mt-2 row">

                  <div class="col-6 col-md-4 col-lg-3">
                     <strong>Destino</strong>
                     <select id="txtDestino" class="form-select">

                     </select>
                  </div>

                  <div style="margin-top:20px;" class="col-6 col-md-3 col-lg-3">

                     <div class="input-group mb-3   date calendariopicker">
                        <input type="text" class="form-control dateRange" value="" id="txtFecha">
                        <span class="input-group-text dateRange" id="basic-addon2"><i
                              class="icon-calendar-2"></i></span>
                     </div>
                  </div>

                  <div style="margin-top:20px;" class="col-12 col-md-4 col-lg-3">
                     <a onclick="CrearFormato()" class="btn btn-primary btn-xs w-100" id="btnNuevo">Crear Formato</a>
                     <a onclick="FinalizarFormato()" class="btn btn-success btn-xs w-100 d-none" id="btnCerrar">Terminar
                        Formato</a>
                  </div>

                  <div style="margin-top:20px;" class=" col-md-4 col-lg-3 ">
                     <input type="hidden" value="" id="txtcontrolIDFolio">
                     <div id="txtcontrolFolio"></div>


                  </div>

               </div>


               <div class="card-body">


                  <div class="row">
                     <div class="col-sm-3">
                        <label><strong>Producto </strong> </label>
                        <input class="form-control" id="txtProducto" placeholder="">
                     </div>

                     <div class="col-sm-2">
                        <label><strong>Cantidad </strong></label>
                        <input type="number" class="form-control" id="txtCantidad" placeholder="">
                     </div>

                     <div class="col-sm-2">
                        <label><strong>Justificación </strong></label>
                        <input class="form-control" id="txtJustificacion">
                     </div>

                     <div class="col-sm-2">
                        <label><strong>Destino </strong></label>
                        <input class="form-control" id="txtDestino">
                     </div>

                     <div style="margin-top:20px;" class="col-sm-2">
                        <a class="btn btn-success" onclick="AgregarProducto()"> ADD</a>
                     </div>


                  </div>


                  <h4 style="margin-top:20px;" class="card-title text-center">
                     Por medio del presente me permite solicitar el siguiente material
                  </h4>

                  <div class="line" id="content-pedidos-lista">

                  </div>

               </div>

               <div class="card-body">
                  <div class="col-12">
                     <table class="table">
                        <thead>
                           <tr class="text-center">
                              <th scope="col">SOLICITÓ</th>
                              <th scope="col">VO. BO.</th>
                              <th scope="col">AUTORIZÓ</th>
                              <th scope="col">RECIBIÓ</th>
                              <th scope="col">ENVIÓ</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr>
                              <td><input class="form-control"></td>
                              <td><input class="form-control"></td>
                              <td><input class="form-control"></td>
                              <td><input class="form-control"></td>
                              <td><input class="form-control"></td>

                           </tr>
                           <tr class="text-center">
                              <td>
                                 <label for="">ENOC</label>
                              </td>
                              <td>MIRIAM</td>
                              <td>ING. BRUNO</td>
                              <td></td>
                              <td></td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>

            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
               <div clas="row">

                  <div class="col-sm-6" id="content-list-pedidos">
                     hola
                  </div>
               </div>
            </div>
         </div>





      </div>

   </div>
</div>