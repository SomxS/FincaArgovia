function CancelarFolio(id) {

   title = `¿Esta seguro de cancelar el pedido No ${id} ?`;
   const TEXT = "Este pedido ya no sera visible en el historial";

   swal_question(title, TEXT).then((result) => {
      if (result.isConfirmed) {

         datos = _get_simple_data(null, 'cancelar-pedido');

         send_ajax(datos, url_file + 'ctrl-pedido-flores.php').then((data) => {



         });

      }

   });

}