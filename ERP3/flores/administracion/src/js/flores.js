window.ctrl = "ctrl/ctrl-flores.php";
window.modal = window.modal || "";
$(function () {
// initComponents();

// lsFolios();
});

/* -- Variables iniciales --*/ 
let data_categoria = [];

function initComponents() {

     simple_send_ajax({ opc: "initComponents" }, ctrl).then((data) => {
        data_categoria = data.data_categoria;

        json_ctas = [
        {
            opc: "input",
            lbl: "Producto",
            id: "producto",
            value: "Nuevo Producto",
        },

        {
            opc: "input-group",
            lbl: "Precio Costo",
            id: "PrecioCosto",
            value: "0.00",
            tipo: "cifra",
            icon: "icon-dollar",
        },

        {
            opc: "input-group",
            lbl: "Precio Venta",
            id: "PrecioVenta",
            value: "0.00",
            tipo: "cifra",
            icon: "icon-dollar",
        },

        {
            opc: "select",
            lbl: "Categoria",
            id: "Categoria",
            tipo: "texto",
            data: data_categoria,
        },

        {
            opc: "select",
            lbl: "SubCategoria",
            id: "SubCategoria",
            tipo: "texto",
            data: [],
        },
        ];

        $("#content-form").simple_json_form({
            data: json_ctas,
            // type_btn: "simple_btn",
            // name_fn: "agregar_cta_menor",
        });


     });
  

}

function lsFolios() {


  fn_ajax({ opc: "lsFolios" }, ctrl, "#content-visor").then((data) => {
    $("#content-visor").rpt_json_table2({
      data: data,
      name: "table_pedidos",
      color_th: "bg-primary",
      center: [1],
      right: [2],
    });

    simple_data_table_no("#simple-table", 40);
  });
}





