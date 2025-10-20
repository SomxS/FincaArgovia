          <div style="margin-top:20px; " class="row ">
           <div class="col-sm-3">

          
            <div class="input-group">
             <input  type="text" class="select_input input-sm form-control" id="txtDestino"
             placeholder="Lugar o Destino" onchange="" onkeypress="BuscarLugar()">
             <span class="input-group-text "><i class=" icon-user"></i></span>
            </div>
            <span id="txtLugarDir" class="text-muted" style="font-weight:500;"> </span>
           </div>


           <div class="col-sm-3">
           
            <div class="input-group date calendariopicker">
             <input type="text" class="select_input form-control input-sm" value="" id="txtDate">
             <span class="input-group-text "><i class="icon-calendar"></i>
             </span>
            </div>
           </div>

           <div class="col-sm-6 ">
          
            <div class="">
             <button id="txtNuevo" class="btn btn-success  hide" onclick="CrearPedido()"> <i
              class="bx bx-file-blank ico-md"></i> Nuevo </button>

              <button id="txtSubir" class="btn btn-primary " onclick="subirPedidos()"> <i class='bx bxs-send'></i>
               Terminar pedido </button>

               <label class="btn btn-default" id="btnArchivo">
                <input type="file" accept=".doc,.docx,.pdf" style="display: none;" id="txtArchivos" onchange="head_tablas()">
               </label>


               <a class="btn btn-default hide" title="Imprimir " onclick="div_print('plan_acciones_correctivas')">
                <i class="bx bxs-printer"></i> Imprimir </a>
               </div>

              </div>

              <div class="col-sm-2 line text-right hide">
               <br>
               No de orden: <span class="text-danger format-folio" id="lblFolio">000</span>
              </div>

             </div><!-- row -->


             <div class="row">
              <br>
              <input type="hidden" id="txtFolio">

              <div class="col-sm-12 col-xs-12 " id="content-pedidos"></div>

             </div>