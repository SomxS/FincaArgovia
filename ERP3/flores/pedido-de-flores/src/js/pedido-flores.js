url_file = 'ctrl/';
const dest = [];

$(function () {

   consultar_datos();

   rangepicker(
      "#txtDate",
      true,
      moment(),
      moment(),
      '',
      '',
      function (startDate, endDate) {
         date1 = startDate.format("YYYY/MM/DD");
         date2 = endDate.format("YYYY/MM/DD");

      }
   );

   rangepicker(
      "#reportrange",
      false,
      moment(),
      moment(),
      '',
      '',
      function (startDate, endDate) {
         date1 = startDate.format("YYYY/MM/DD");
         date2 = endDate.format("YYYY/MM/DD");

      }
   );

});

// { EVENTOS AL TOCAR UN TAB }
function __tab_pedidos() {
   cargar_destino();
   list_tabla_pedido();

   $("#txtDestino").autocomplete({
      source: dest
   });
}

function list_tabla_pedido() {
   opc = 'list_tabla_pedido';


   $.ajax({
      type: "POST",
      url: url_file + "ctrl-pedido-flores.php",
      data: 'opc=' + opc,
      dataType: "json",
      beforeSend: function () {
         $('#content-pedidos').Loading();
      },
      success: function (data) {
         $('#content-pedidos').form_json_table({
            data: data,
            select: [1,4,5]
         });
      }
   });
}

function __modal(id) {
   nombre_producto = $('#txtItem' + $key).val();

   // $.ajax({
   //    type: "POST",
   //    url: "controlador/flores/user/pedidos.php",
   //    data: "opc=12&id=" + id,

   //    success: function (rp) {
   //       data = eval(rp);

         modal_complementos = bootbox.dialog({
            title: `${nombre_producto}`,
            // size: "small",
            message: 's'
         });
   
   
   //    }
   // });
}

function Quitar(id) {

   title = `¿Deseas quitar de la lista el producto `;
  
   swal_question(title, '').then((result) => {
      if (result.isConfirmed) {

      }

   });        
   // $(".console").html(id);
   // $.ajax({
   //    type: "POST",
   //    url: "controlador/flores/user/pedidos.php",
   //    data: "opc=9&id=" + id,
   //    success: function (rp) {
   //       var data = eval(rp);
   //       consultar_datos();
   //    }
   // });
}

function cancelarPedido(id) {
   title = `¿Deseas cancelar la nota `;

   swal_question(title, "Esto quitara la nota actual.").then((result) => {
     if (result.isConfirmed) {
     }
   });
}







function consultar_datos() {
   tipo = $("input[name=rdBtn]:checked").val();


   opc = 'init';


   $.ajax({
      type: "POST",
      url: url_file + 'ctrl-pedido-flores.php',
      data: "opc=" + opc,
      // dataType: "json",
      beforeSend: function () {
         // $("#content-pedidos").html(` <i class="icon-spin4 animate-spin"></i>
         //                ANALIZANDO`);
      },
      success: function (rp) {
         var data = eval(rp);
         if (data[0] != 0) { // Se creo un formato
            $("#content-pedidos").html(data[1]);
            $("#txtFolio").val(data[2]);
            $("#txtDestino").val(data[4]);
            $("#txtDate").val(data[3]);
            $("#txtNuevo").addClass("hide");
            $("#txtSubir").removeClass("hide");
            //    $(".btn-group button").attr("disabled", "disabled");

            $("#lblFolio").html(data[5]);

            //    $('#btnArchivo').removeAttr('disabled');
            //    $('#txtArchivos').removeAttr('disabled');

         } else {
            //    $("#content-pedidos").html(default_img("logo_c"));
            $("#txtNuevo").removeClass("hide");
            $("#txtSubir").addClass("hide");
            //    $('#btnArchivo').attr('disabled', 'disabled');
            //    $('#txtArchivos').attr('disabled', 'disabled');
         }
      }
   });
}

function subirPedidos() {

   datos = _get_simple_data(["Folio"], "terminar-formato");

   title = `¿Deseas terminar el pedido ?`;
   const TEXT = "";

   swal_question(title, TEXT).then((result) => {

      if (result.isConfirmed) {

         $.ajax({
            type: "POST",
            url: url_file + 'ctrl-pedido-flores.php',
            data: datos,
            dataType: "json",

            success: function (rp) {
               var data = eval(rp);
               $("#content-pedidos").html("");
               $("#txtDestino").val("");
               $("#txtLugarDir").html("");

            }
         });

      }

   });



}

function list_folio() {
   opc = 'historial-pedidos';

   $.ajax({
      type: "POST",
      url: url_file + 'ctrl-pedido-flores.php',
      data: 'opc=' + opc,
      dataType: "json",
      beforeSend: function () {
         $("#tbTicket").Loading();
      },
      success: function (data) {
         
         
         
         $("#tbTicket").btn_json_table(
               {
                  data: data,
                  name: 'table_pedidos'

               });

        

         simple_data_table_no("#table_pedidos",5);
      }
   });
}

function verTicket(id) {
   opc = 'ver-ticket';

   $.ajax({
      type: "POST",
      url: url_file + 'ctrl-pedido-flores.php',
      data: 'opc=' + opc + '&id=' + id,
      dataType: "json",
      beforeSend: function () {
         $("#content-folio").Loading();
      },

      success: function (data) {
         $("#content-folio").report_json_table({ data });
      }
   });
}

function cargar_destino() {
   opc = 'cargar_clientes';

   $.ajax({
      type: 'POST',
      url: url_file + 'ctrl-pedido-flores.php',
      data: 'opc=' + opc,
      dataType: "json",

      success: function (data) {
         res = eval(data)
         cont = res.length
         for (var i = 0; i < cont; i++) {
            dest[i] = res[i]
         }
      },
   })

}

/* 
Complementos **
*/
function _get_simple_data(multiple, name_opc) {
   url = "";

   for (var i = 0; i < multiple.length; i++) {
      url = url + multiple[i] + "=" + $("#txt" + multiple[i]).val() + "&";
   }

   url = url + 'opc=' + name_opc;
   return url;
}

function swal_warn(title) {
   Swal.fire({
      icon: "warning",
      title: title,
      showConfirmButton: false,
      timer: 2000,
   });
}



