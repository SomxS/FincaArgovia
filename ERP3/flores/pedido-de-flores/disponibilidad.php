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
                                <li class='breadcrumb-item fw-bold active'>Disponibilidad</li>
                            </ol>
                        </nav>
                        <div class="row mb-3">
                            <div class="col-6 col-sm-6 col-lg-3 mb-3">
                                <label for="cbUDN">Seleccionar UDN</label>
                                <select class="form-select" id="cbUDN"></select>
                            </div>
                            <div class="col-6 col-sm-6 col-lg-3 mb-3">
                                <label for="ipt">Búsqueda</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="ipt">
                                    <span class="input-group-text"><i class="icon-search"></i></span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6 col-lg-3 mb-3">
                                <label for="iptDate">Fecha</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="iptDate">
                                    <span class="input-group-text"><i class="icon-calendar"></i></span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6 col-lg-3 mb-3">

                                <button style="margin-top:15px;" type="button" class="btn btn-primary col-6" id="btnOk">Disponibilidad</button>
                            </div>
                        </div>

                        <div  id="tbDatos">

                          <div class="" style="margin-bottom:35px">
                           <div class=" text-center col-sm-12 " style="font-size:1em;">
                           <b> DISPONIBILIDAD 2023 FLORES &amp; FOLLAJES </b> Semana del 05 al 11 de SEPTIEMBRE del 2023</div>
                          </div>

                          <div class="row">
                            <div class="col-sm-6">
                              <table id="size1" class="table table-bordered ">
                                <thead><tr class="text-center"><th colspan="6"> Flores Tropicales</th></tr></thead>
                              </table>

                               <table id="size1" class="table table-bordered ">
                               <tbody><tr>
                               <td>N°</td>
                               <td>Especie</td>
                               <td>Costo unitario</td>
                               <td>U / Venta</td>
                               <td>Cantidad </td>
                               <td>Sub Total </td>
                               </tr>
                               </tbody></table>

                               <table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>Heliconias chicas 2 </b></td></tr>
                               <tr>
                               <td>N°</td>
                               <td>Especie</td>
                               <td>Costo unitario</td>
                               <td>U / Venta</td>
                               <td>Cantidad </td>
                               <td>Sub Total </td>
                               </tr>
                               <tr>
                                <td></td>
                                <td class="col-xs-4">Andrómeda</td>
                                <td class="text-right">$ 3.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="12"></td>
                                <td>36</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">Dwarf Jamaican</td>
                                <td class="text-right">$ 5.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="12"></td>
                                <td>60</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">Giant Jamaican</td>
                                <td class="text-right">$ 8.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">Golden Adrian</td>
                                <td class="text-right">$ 5.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">Golden Opal</td>
                                <td class="text-right">$ 5.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">Golden Torch</td>
                                <td class="text-right">$ 5.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">Pelicano</td>
                                <td class="text-right">$ 10.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">San Vincent red</td>
                                <td class="text-right">$ 3.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">Nickeriensis</td>
                                <td class="text-right">$ 5.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">Tropica</td>
                                <td class="text-right">$ 5.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr></tbody></table><table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>Heliconias medianas 3 </b></td></tr>
                               <tr>
                               <td>N°</td>
                               <td>Especie</td>
                               <td>Costo unitario</td>
                               <td>U / Venta</td>
                               <td>Cantidad </td>
                               <td>Sub Total </td>
                               </tr>
                               <tr>
                                <td></td>
                                <td class="col-xs-4">BIHAI</td>
                                <td class="text-right">$ 10.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">bUTTER</td>
                                <td class="text-right">$ 10.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">Fire Bird</td>
                                <td class="text-right">$ 10.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">Lobster Claw</td>
                                <td class="text-right">$ 10.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">Pinky Peach</td>
                                <td class="text-right">$ 10.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">She peluda</td>
                                <td class="text-right">$ 10.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">She Lisa</td>
                                <td class="text-right">$ 10.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr><tr>
                                <td></td>
                                <td class="col-xs-4">Tagamy</td>
                                <td class="text-right">$ 10.00</td>
                                <td> PIEZA</td>
                                <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                <td>0</td>

                                </tr></tbody></table>
                            </div>



                            <div class="col-sm-6">

                              <table id="size1" class="table table-bordered "><tbody><tr class="text-center"><th colspan="6"> Follajes Tropicales</th></tr></tbody></table>



                                                           <table id="size1" class="table table-bordered ">
                                                           <tbody><tr>
                                                           <td>N°</td>
                                                           <td>Especie</td>
                                                           <td>Costo unitario</td>
                                                           <td>U / Venta</td>
                                                           <td>Cantidad </td>
                                                           <td>Sub Total </td>
                                                           </tr>
                                                           </tbody></table>



                                                           <table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>Dracaenas 8 </b></td></tr>
                                                           <tr>
                                                           <td>N°</td>
                                                           <td>Especie</td>
                                                           <td>Costo unitario</td>
                                                           <td>U / Venta</td>
                                                           <td>Cantidad </td>
                                                           <td>Sub Total </td>
                                                           </tr>
                                                           <tr>
                                                            <td></td>
                                                            <td class="col-xs-4">Sandareana</td>
                                                            <td class="text-right">$ 5.00</td>
                                                            <td> PIEZA</td>
                                                            <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                            <td>0</td>

                                                            </tr><tr>
                                                            <td></td>
                                                            <td class="col-xs-4">Sandereana Lemon</td>
                                                            <td class="text-right">$ 6.00</td>
                                                            <td> PIEZA</td>
                                                            <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                            <td>0</td>

                                                            </tr></tbody></table><table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>Cordylines 9 </b></td></tr>
                                                           <tr>
                                                           <td>N°</td>
                                                           <td>Especie</td>
                                                           <td>Costo unitario</td>
                                                           <td>U / Venta</td>
                                                           <td>Cantidad </td>
                                                           <td>Sub Total </td>
                                                           </tr>
                                                           </tbody></table>


                                                           <table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>Otros Follajes 10 </b></td></tr>
                                                              <tr>
                                                              <td>N°</td>
                                                              <td>Especie</td>
                                                              <td>Costo unitario</td>
                                                              <td>U / Venta</td>
                                                              <td>Cantidad </td>
                                                              <td>Sub Total </td>
                                                              </tr>
                                                              </tbody></table><table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>Palmas 15 </b></td></tr>
                                                              <tr>
                                                              <td>N°</td>
                                                              <td>Especie</td>
                                                              <td>Costo unitario</td>
                                                              <td>U / Venta</td>
                                                              <td>Cantidad </td>
                                                              <td>Sub Total </td>
                                                              </tr>
                                                              <tr>
                                                               <td></td>
                                                               <td class="col-xs-4">Palma Rafia</td>
                                                               <td class="text-right">$ 20.00</td>
                                                               <td> PIEZA</td>
                                                               <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                               <td>0</td>

                                                               </tr><tr>
                                                               <td></td>
                                                               <td class="col-xs-4">Palma Fish Tail</td>
                                                               <td class="text-right">$ 12.00</td>
                                                               <td> PIEZA</td>
                                                               <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                               <td>0</td>

                                                               </tr><tr>
                                                               <td></td>
                                                               <td class="col-xs-4">Palma Karyota</td>
                                                               <td class="text-right">$ 12.00</td>
                                                               <td> PIEZA</td>
                                                               <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                               <td>0</td>

                                                               </tr><tr>
                                                               <td></td>
                                                               <td class="col-xs-4">Palma Licuala</td>
                                                               <td class="text-right">$ 10.00</td>
                                                               <td> PIEZA</td>
                                                               <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                               <td>0</td>

                                                               </tr><tr>
                                                               <td></td>
                                                               <td class="col-xs-4">PALMAS LICUALA GRANDE</td>
                                                               <td class="text-right">$ 15.00</td>
                                                               <td> PIEZA</td>
                                                               <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                               <td>0</td>

                                                               </tr><tr>
                                                               <td></td>
                                                               <td class="col-xs-4">Palma Plizada</td>
                                                               <td class="text-right">$ 13.00</td>
                                                               <td> PIEZA</td>
                                                               <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                               <td>0</td>

                                                               </tr><tr>
                                                               <td></td>
                                                               <td class="col-xs-4">Palma Robelina</td>
                                                               <td class="text-right">$ 13.00</td>
                                                               <td> PIEZA</td>
                                                               <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                               <td>0</td>

                                                               </tr><tr>
                                                               <td></td>
                                                               <td class="col-xs-4">Palma Robelina Extgde</td>
                                                               <td class="text-right">$ 15.00</td>
                                                               <td> PIEZA</td>
                                                               <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                               <td>0</td>

                                                               </tr><tr>
                                                               <td></td>
                                                               <td class="col-xs-4">Boriquense</td>
                                                               <td class="text-right">$ 5.00</td>
                                                               <td> PIEZA</td>
                                                               <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                               <td>0</td>

                                                               </tr><tr>
                                                               <td></td>
                                                               <td class="col-xs-4">Clio estandar</td>
                                                               <td class="text-right">$ 5.00</td>
                                                               <td> PIEZA</td>
                                                               <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                               <td>0</td>

                                                               </tr><tr>
                                                               <td></td>
                                                               <td class="col-xs-4">Massangeana Chica</td>
                                                               <td class="text-right">$ 10.00</td>
                                                               <td> PIEZA</td>
                                                               <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                               <td>0</td>

                                                               </tr><tr>
                                                               <td></td>
                                                               <td class="col-xs-4">Massangeana Grande</td>
                                                               <td class="text-right">$ 15.00</td>
                                                               <td> PIEZA</td>
                                                               <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                                                               <td>0</td>

                                                               </tr></tbody></table><table id="size1" class="table table-bordered "><tbody><tr class="text-center"><th class="col-xs-8">Subtotal</th><th class="text-right">-</th></tr></tbody></table></div><div class="formato col-xs-12 col-sm-6 col-lg-6 "><table id="size1" class="table table-bordered "><tbody><tr class="text-center"><th colspan="6"> Anthurios</th></tr></tbody></table><table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>S/N 11 </b></td></tr>
                                                              <tr>
                                                              <td>N°</td>
                                                              <td>Especie</td>
                                                              <td>Costo unitario</td>
                                                              <td>U / Venta</td>
                                                              <td>Cantidad </td>
                                                              <td>Sub Total </td>
                                                              </tr>
                                                              </tbody></table><table id="size1" class="table table-bordered "><tbody><tr class="text-center"><th class="col-xs-8">Subtotal</th><th class="text-right">-</th></tr></tbody></table></div><div class="formato col-xs-12 col-sm-6 col-lg-6 "><table id="size1" class="table table-bordered "><tbody><tr class="text-center"><th colspan="6"> Empaques</th></tr></tbody></table><table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>S/N 12 </b></td></tr>
                                                              <tr>
                                                              <td>N°</td>
                                                              <td>Especie</td>
                                                              <td>Costo unitario</td>
                                                              <td>U / Venta</td>
                                                              <td>Cantidad </td>
                                                              <td>Sub Total </td>
                                                              </tr>
                                                              </tbody></table><table id="size1" class="table table-bordered "><tbody><tr class="text-center"><th class="col-xs-8">Subtotal</th><th class="text-right">-</th></tr></tbody></table></div>
                                                             <div class="">
                                                             <div class=" col-xs-12 col-sm-6">
                                                             <table class="table table-bordered">
                                                             <tbody><tr>
                                                             <td class="col-xs-8"><h4><b>TOTAL</b></h4></td>
                                                             <td class="text-right"><h4><b>$ 96.00<b></b></b></h4></td>
                                                             </tr>
                                                             </tbody></table>

                            </div>

                          </div>







                          <div id="tbDisponibilidad">


                             <div class="formato col-sm-6 col-lg-6 ">






                          <table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>Heliconeas Grandes 4 </b></td></tr>
                             <tr>
                             <td>N°</td>
                             <td>Especie</td>
                             <td>Costo unitario</td>
                             <td>U / Venta</td>
                             <td>Cantidad </td>
                             <td>Sub Total </td>
                             </tr>
                             <tr>
                              <td></td>
                              <td class="col-xs-4">Caribe rojo</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Iris Red</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Red apple</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Splash</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Wagneriana</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr></tbody></table><table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>Heliconeas Colgantes 5 </b></td></tr>
                             <tr>
                             <td>N°</td>
                             <td>Especie</td>
                             <td>Costo unitario</td>
                             <td>U / Venta</td>
                             <td>Cantidad </td>
                             <td>Sub Total </td>
                             </tr>
                             <tr>
                              <td></td>
                              <td class="col-xs-4">Sexy scarleth</td>
                              <td class="text-right">$ 15.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Rostrata</td>
                              <td class="text-right">$ 12.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Collinsiana</td>
                              <td class="text-right">$ 12.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Sexy Pink</td>
                              <td class="text-right">$ 20.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Sexy Orange</td>
                              <td class="text-right">$ 20.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">She Kong</td>
                              <td class="text-right">$ 25.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr></tbody></table><table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>Calatheas 6 </b></td></tr>
                             <tr>
                             <td>N°</td>
                             <td>Especie</td>
                             <td>Costo unitario</td>
                             <td>U / Venta</td>
                             <td>Cantidad </td>
                             <td>Sub Total </td>
                             </tr>
                             <tr>
                              <td></td>
                              <td class="col-xs-4">Coffee</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Leutea</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr></tbody></table><table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>Musas 13 </b></td></tr>
                             <tr>
                             <td>N°</td>
                             <td>Especie</td>
                             <td>Costo unitario</td>
                             <td>U / Venta</td>
                             <td>Cantidad </td>
                             <td>Sub Total </td>
                             </tr>
                             <tr>
                              <td></td>
                              <td class="col-xs-4">Lilah</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Velutina</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Bronce</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr></tbody></table>
                              <table id="size1" class="table table-bordered "><tbody><tr><td colspan="6"><b>Gingers 14 </b></td></tr>
                             <tr>
                             <td>N°</td>
                             <td>Especie</td>
                             <td>Costo unitario</td>
                             <td>U / Venta</td>
                             <td>Cantidad </td>
                             <td>Sub Total </td>
                             </tr>
                             <tr>
                              <td></td>
                              <td class="col-xs-4">Antorcha</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Hawaiana Rojo</td>
                              <td class="text-right">$ 5.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Hawaiana Rosa</td>
                              <td class="text-right">$ 5.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Maraca Borneo Red</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Maraca amarilla</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Phalaenopsis</td>
                              <td class="text-right">$ 50.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Cardamomo rojo </td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr><tr>
                              <td></td>
                              <td class="col-xs-4">Cardamomo verde</td>
                              <td class="text-right">$ 10.00</td>
                              <td> PIEZA</td>
                              <td class="bg-f"><input class="input-sm form-control" value="0"></td>
                              <td>0</td>

                              </tr></tbody></table>
                              <table id="size1" class="table table-bordered "><tbody><tr class="text-center"><th class="col-xs-8">Subtotal</th><th class="text-right">$ 96.00</th></tr></tbody></table></div>


                              <div class="formato col-xs-12 col-sm-6 col-lg-6 ">






                            </div></div></div>

                        </div><!-- -->
                        <script src='src/js/disponibilidad.js?t=1695604523'></script>

        </div>
    </main>
</body>
</html>