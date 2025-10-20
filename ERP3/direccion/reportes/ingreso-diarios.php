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
      <li class='breadcrumb-item text-uppercase text-muted'>direccion</li>
      <li class='breadcrumb-item text-uppercase text-muted'>reportes</li>
      <li class='breadcrumb-item fw-bold active'>Ingreso diarios</li>
   </ol>
</nav>


<div class="row mb-3 d-flex ">


   <div class="col-12 col-sm-6 col-lg-4 mb-4">
      <label for="iptDate">Fecha</label>
      <div class="input-group">
         <input type="text" class="form-control" id="iptDate">
         <span class="input-group-text"><i class="icon-calendar"></i></span>
      </div>
   </div>
   <div class="col-12 col-sm-6 col-lg-2 mb-2">
      <label col="col-12"> </label>
      <button type="button" class="btn btn-primary col-12" id="btnOk">Buscar</button>
   </div>
</div>

<!-- CONTENEDOR -->
<div class="row" id="tbDatos">

   <div class="col-sm-12">
      <ul class="nav nav-tabs" id="myTab" role="tablist">

         <li class="nav-item" role="presentation">
            <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#historial"
               type="button" role="tab" aria-controls="profile" aria-selected="false" onclick="reporte_gral()">
               <i class="icon-doc-text"></i> Reporte General
            </button>
         </li>

         <li class="nav-item" role="presentation">
            <button onclick="ver_archivos()" class="nav-link " id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
               type="button" role="tab" aria-controls="home" aria-selected="true"><i class="icon-doc"></i> lista de
               archivos </button>
         </li>




         <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button"
               role="tab" aria-controls="profile" aria-selected="false" onclick="list_pedidos()">
               <i class=" icon-th-list"></i> Hospedaje
            </button>
         </li>

         <li class="nav-item" role="presentation">
            <button class="nav-link" id="clientes-tab" data-bs-toggle="tab" data-bs-target="#clientes" type="button"
               role="tab" aria-controls="clientes" aria-selected="false" onclick="ver_tc()">
               <i class=" icon-users"></i> T.C
            </button>
         </li>



      </ul>



      <div class="tab-content" id="myTabContent">
         <div class="tab-pane fade " id="home">
            <div id="content-file"></div>
         </div>

         <div class="tab-pane fade show active" id="historial" role="tabpanel" aria-labelledby="historial-tab">
            <div style="" id="content-report-gral" class="col-sm-12 col-xs-12">
              

            </div>

         </div> <!-- -->


         <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

            <div class="">




               <table id="size1" class="table  table-bordered table-hover table-stripped"
                  style="width:100%; font-size:.9em">
                  <thead>
                     <tr class="bg-primary">
                        <th class="text-center">SubCategoria</th>
                        <th>Pax</th>
                        <th>Noche</th>
                        <th>Monto</th>
                        <th class="text-center col-sm-1 col-xs-1 ">Efectivo</th>
                        <th class="text-center col-sm-1 col-xs-1 ">TC</th>
                        <th class="text-center col-sm-1 col-xs-1 ">CxC</th>
                        <th class="text-center col-sm-1 col-xs-1 ">Anticipo</th>
                        <th class="text-center col-sm-1 col-xs-1">Subtotal</th>
                        <th class="text-center col-sm-1 col-xs-1"> IVA</th>
                        <th class="text-center col-sm-1 col-xs-1"> Hospedaje</th>
                        <th class="text-center col-sm-1 col-xs-1">Total</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td class="">MARIAS</td>
                        <td class="text-right bg-info" id="pax1">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche1">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa1">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp1-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp1-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp1-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp1-4">
                           -
                        </td>
                        <td id="sub1-1" class="text-right">
                           -
                        </td>
                        <td id="Imp1-1" class="text-right">
                           -
                        </td>
                        <td id="Imp1-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total21">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">HUEHUE</td>
                        <td class="text-right bg-info" id="pax2">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche2">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa2">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp2-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp2-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp2-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp2-4">
                           -
                        </td>
                        <td id="sub1-2" class="text-right">
                           -
                        </td>
                        <td id="Imp2-1" class="text-right">
                           -
                        </td>
                        <td id="Imp2-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total22">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">BOQUERON</td>
                        <td class="text-right bg-info" id="pax3">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche3">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa3">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp3-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp3-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp3-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp3-4">
                           -
                        </td>
                        <td id="sub1-3" class="text-right">
                           -
                        </td>
                        <td id="Imp3-1" class="text-right">
                           -
                        </td>
                        <td id="Imp3-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total23">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">PRIMAVERA</td>
                        <td class="text-right bg-info" id="pax4">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche4">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa4">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp4-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp4-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp4-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp4-4">
                           -
                        </td>
                        <td id="sub1-4" class="text-right">
                           -
                        </td>
                        <td id="Imp4-1" class="text-right">
                           -
                        </td>
                        <td id="Imp4-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total24">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">GUAYABO</td>
                        <td class="text-right bg-info" id="pax5">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche5">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa5">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp5-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp5-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp5-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp5-4">
                           -
                        </td>
                        <td id="sub1-5" class="text-right">
                           -
                        </td>
                        <td id="Imp5-1" class="text-right">
                           -
                        </td>
                        <td id="Imp5-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total25">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">GUANACASTLE</td>
                        <td class="text-right bg-info" id="pax6">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche6">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa6">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp6-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp6-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp6-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp6-4">
                           -
                        </td>
                        <td id="sub1-6" class="text-right">
                           -
                        </td>
                        <td id="Imp6-1" class="text-right">
                           -
                        </td>
                        <td id="Imp6-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total26">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">TOMA</td>
                        <td class="text-right bg-info" id="pax7">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche7">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa7">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp7-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp7-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp7-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp7-4">
                           -
                        </td>
                        <td id="sub1-7" class="text-right">
                           -
                        </td>
                        <td id="Imp7-1" class="text-right">
                           -
                        </td>
                        <td id="Imp7-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total27">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">RISTRETTO</td>
                        <td class="text-right bg-info" id="pax8">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche8">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa8">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp8-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp8-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp8-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp8-4">
                           -
                        </td>
                        <td id="sub1-8" class="text-right">
                           -
                        </td>
                        <td id="Imp8-1" class="text-right">
                           -
                        </td>
                        <td id="Imp8-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total28">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">LATTE</td>
                        <td class="text-right bg-info" id="pax9">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche9">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa9">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp9-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp9-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp9-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp9-4">
                           -
                        </td>
                        <td id="sub1-9" class="text-right">
                           -
                        </td>
                        <td id="Imp9-1" class="text-right">
                           -
                        </td>
                        <td id="Imp9-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total29">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">LATTE</td>
                        <td class="text-right bg-info" id="pax10">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche10">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa10">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp10-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp10-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp10-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp10-4">
                           -
                        </td>
                        <td id="sub1-10" class="text-right">
                           -
                        </td>
                        <td id="Imp10-1" class="text-right">
                           -
                        </td>
                        <td id="Imp10-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total210">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">CEIBA</td>
                        <td class="text-right bg-info" id="pax11">
                           4
                        </td>
                        <td class="text-right bg-info" id="Noche11">
                           1
                        </td>

                        <td class="text-right bg-info" id="Tarifa11">
                           5220.00
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp11-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp11-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp11-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp11-4">
                           $ 5,220.00
                        </td>
                        <td id="sub1-11" class="text-right">
                           $ 4,423.73
                        </td>
                        <td id="Imp11-1" class="text-right">
                           $ 707.80
                        </td>
                        <td id="Imp11-2" class="text-right">
                           $ 88.47
                        </td>
                        <td class=" text-right" id="total211">
                           $ 5,220.00
                        </td>
                     </tr>
                     <tr>
                        <td class="">MARAGO</td>
                        <td class="text-right bg-info" id="pax12">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche12">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa12">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp12-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp12-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp12-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp12-4">
                           -
                        </td>
                        <td id="sub1-12" class="text-right">
                           -
                        </td>
                        <td id="Imp12-1" class="text-right">
                           -
                        </td>
                        <td id="Imp12-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total212">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">BOURBON</td>
                        <td class="text-right bg-info" id="pax13">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche13">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa13">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp13-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp13-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp13-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp13-4">
                           -
                        </td>
                        <td id="sub1-13" class="text-right">
                           -
                        </td>
                        <td id="Imp13-1" class="text-right">
                           -
                        </td>
                        <td id="Imp13-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total213">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">CARAJILLO</td>
                        <td class="text-right bg-info" id="pax14">
                           -
                        </td>
                        <td class="text-right bg-info" id="Noche14">
                           -
                        </td>

                        <td class="text-right bg-info" id="Tarifa14">
                           -
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp14-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp14-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp14-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp14-4">
                           -
                        </td>
                        <td id="sub1-14" class="text-right">
                           -
                        </td>
                        <td id="Imp14-1" class="text-right">
                           -
                        </td>
                        <td id="Imp14-2" class="text-right">
                           -
                        </td>
                        <td class=" text-right" id="total214">
                           -
                        </td>
                     </tr>
                     <tr>
                        <td class="">EXPRESSO</td>
                        <td class="text-right bg-info" id="pax15">
                           2
                        </td>
                        <td class="text-right bg-info" id="Noche15">
                           1
                        </td>

                        <td class="text-right bg-info" id="Tarifa15">
                           800.00
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp15-1">
                           $ 800.00
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp15-2">
                           -
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp15-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp15-4">
                           -
                        </td>
                        <td id="sub1-15" class="text-right">
                           $ 677.97
                        </td>
                        <td id="Imp15-1" class="text-right">
                           $ 108.47
                        </td>
                        <td id="Imp15-2" class="text-right">
                           $ 13.56
                        </td>
                        <td class=" text-right" id="total215">
                           $ 800.00
                        </td>
                     </tr>
                     <tr>
                        <td class="">CAPPUCINO</td>
                        <td class="text-right bg-info" id="pax16">
                           2
                        </td>
                        <td class="text-right bg-info" id="Noche16">
                           1
                        </td>

                        <td class="text-right bg-info" id="Tarifa16">
                           800.00
                        </td>
                        <td title="Efectivo" class="text-right bg-warning text-primary-1" id="fp16-1">
                           -
                        </td>
                        <td title="TC" class="text-right bg-warning text-primary-1" id="fp16-2">
                           $ 800.00
                        </td>
                        <td title="CxC" class="text-right bg-warning text-primary-1" id="fp16-3">
                           -
                        </td>
                        <td title="Anticipo" class="text-right bg-warning text-primary-1" id="fp16-4">
                           -
                        </td>
                        <td id="sub1-16" class="text-right">
                           $ 677.97
                        </td>
                        <td id="Imp16-1" class="text-right">
                           $ 108.47
                        </td>
                        <td id="Imp16-2" class="text-right">
                           $ 13.56
                        </td>
                        <td class=" text-right" id="total216">
                           $ 800.00
                        </td>
                     </tr>
                  </tbody>

                  <tfoot>
                     <tr class="text-right">
                        <td class="bg-info-1">TOTAL</td>
                        <td class="bg-info-1">8</td>
                        <td class="bg-info-1">$ 3.00</td>
                        <td class="bg-info-1">$ 6,820.00</td>
                        <td class="bg-info-1">$ 800.00</td>
                        <td class="bg-info-1">$ 800.00</td>
                        <td class="bg-info-1">-</td>
                        <td class="bg-info-1">$ 5,220.00</td>
                        <td class="bg-info-1">-</td>
                        <td class="bg-info-1">$ 924.74</td>
                        <td class="bg-info-1">$ 115.59</td>
                        <td class="bg-info-1">$ 6,820.00</td>
                     </tr>
                  </tfoot>
               </table>
            </div>

         </div>



         <div class="tab-pane fade" id="clientes" role="tabpanel" aria-labelledby="clientes-tab">


            <div id="content-tc"></div>
         </div>




      </div>
   </div>

</div>


<script src='src/js/ingreso-diarios.js'></script>
        </div>
    </main>
</body>
</html>