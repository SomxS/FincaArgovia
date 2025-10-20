/* ruta de consulta */ 
 link = 'ctrl/ctrl-historial-punto-de-venta.php';

 $(function () {
  rangepicker(
    "#iptDate",
    false,
    moment().startOf("month"),
    moment(),
    {
      Hoy: [moment(), moment()],
      Ayer: [moment().subtract(1, "days"), moment().subtract(1, "days")],
      "Última semana": [moment().subtract(6, "days"), moment()],
      "Mes actual": [moment().startOf("month"), moment()],
      "Mes anterior": [
        moment().subtract(1, "month").startOf("month"),
        moment().subtract(1, "month").endOf("month"),
      ],
      "Año actual": [moment().startOf("year"), moment()],
      "Año anterior": [
        moment().subtract(1, "year").startOf("year"),
        moment().subtract(1, "year").endOf("year"),
      ],
    },
    true,
    function (startDate, endDate) {
      date1 = startDate.format("YYYY/MM/DD");
      date2 = endDate.format("YYYY/MM/DD");
    }
  );



  
 });



/* funciones para crear tablas */ 


function list_folio() {
    console.log("list_folio");

     dtx = {
       opc: "historial-pedidos",
       fi: ipt_date().fi,
       ff: ipt_date().ff,
     };

     fn_ajax(dtx, link, "#tbTicket").then((data) => {
       $("#tbTicket").btn_json_table({
         data: data,
         name: "table_pedidos",
         center: [1, 3, 4, 5],
         left: [6],
       });

       simple_data_table_no("#simple-table", 10);
     });


}

function verTicket(id) {
 
    dtx = { opc: "ver-ticket",id:id };
   
    fn_ajax(dtx,link, "#content-folio").then((data) => {
      
        $("#content-folio").rpt_json_table2({
        data      : data,
        right     : [2, 3, 4],
        f_size    : 14,
        parametric: true,
      });
    });
  
}




function lsHistorialTicket() {

 $("#frm-destino").validar_contenedor({
     tipo: "text",opc: "frm-data",
   },
   (datos) => {

      fn_ajax(datos, ctrl, "#").then((data) => {
         

        // simple_data_table_no("#tb", 35);
      });


   }
 );


}


/* modal & alerts */ 

function verComplementos(id) {
    
    modal_c = bootbox.dialog({
        title: `Producto: `,
        message: `
        <div id="content-complementos-v"></div>
        `,
    });

    let dx = {
        opc : "ver-complementos",
        id: id,
    }
    
    simple_send_ajax(dx, url).then((data) => {

        $("#content-complementos-v").rpt_json_table2({
          data: data,
        });
    });
    

   


}


function ipt_date() {
  const fi = $("#iptDate").data("daterangepicker").startDate.format("YYYY-MM-DD");
  const ff = $("#iptDate").data("daterangepicker").endDate.format("YYYY-MM-DD");

  return {fi,ff};
}

