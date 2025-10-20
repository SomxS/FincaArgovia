function listado_clientesw() {
   $.ajax({
      type: "POST",
      url: "controlador/flores/user/clientes.php",
      data: "opc=1",
      beforeSend: function () {
         $('#content-clientes').html(Load_sm());
      },
      success: function (rp) {
         data = eval(rp);
         setTimeout(function () {

            // $('#content-clientes').html(data[0]);
            // export_data_table('#tbClientes');

         }, 500);

      }
   });
}


function listado_clientes() {

   opc = 'list-clientes';


   $.ajax({
      type: "POST",
      url: url_file + "ctrl-pedido-flores.php",
      data: 'opc=' + opc,
      dataType: "json",
      beforeSend: function () {

         $('#content-clientes').Loading({
            texto: 'Cargando lista',
            tipo : 'simple'
         });

      },

      success: function (data) {
    
         $("#content-clientes").simple_json_table({
            datos: data,
            name:'tb_clientes'
         });
      }
   });
}



