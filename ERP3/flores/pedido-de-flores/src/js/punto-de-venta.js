window.ctrl =  "ctrl/ctrl-punto-de-venta.php";
window.modal = window.modal || "";

url_file   = "ctrl/";
const dest = [];

$(function () {
//   list_folio();
  cargar_destino();
  tab_folio();
  lista_categoria();
});



// Configuracion de botones 

$("#btnPedido").on("click", () => {
  $("#content-cerrar-pedidos").addClass("d-none");
  $("#content-historial").addClass("d-none");
  $("#content-pos").removeClass("d-none");
});

$("#btnHistorial").on("click", () => {
  $("#content-historial").removeClass("d-none");
  $("#content-cerrar-pedidos").addClass("d-none");
  $("#content-pos").addClass("d-none");
});

$("#btnCerrarP").on("click", () => {
  $("#content-cerrar-pedidos").removeClass("d-none");
  $("#content-historial").addClass("d-none");
  $("#content-pos").addClass("d-none");
});



function tab_folio() {
   
  // Crear pestañas dinamicas para las notas
  
  simple_send_ajax({opc:'tab'}, ctrl).then((data) => {
    $("#content-notas").simple_tab({ data: data });
    crearNota();
    range_picker_now("txtDate");
  });
  /* ocultamos contenedores inecesarios*/   
  $("#txtCat").attr("disabled", "disabled");
  $("#txtSubCat").attr("disabled", "disabled");
}

function range_picker_now(id) {
      rangepicker(
        "#"+id,
        true,
        moment(),
        moment(),
        "",
        "",
        function (startDate, endDate) {
          date1 = startDate.format("YYYY/MM/DD");
          date2 = endDate.format("YYYY/MM/DD");
        }
      );
}

/* -----------------------
 > Panel de notas de venta
--------------------------*/
function crearNota() {

  //  Limpiar contenedor
  $("#content-tab0").html("");
  $("#content-tab0").append('<form id="form-crear" novalidate></form>');

  let json_nota = [
    {
      opc: "input-group",
      lbl: "Destino",
      id: "Destino",
      tipo: "texto",
      icon: " icon-user",
      placeholder: "Lugar o Destino",
      class: "ui-autocomplete-input",
    },
    {
      opc: "input-calendar",
      lbl: "Fecha de envio",
      id: "Date",
    },

    {
      opc: "textarea",
      lbl: "Nota:",
      id: "nota",
      tipo: "texto",
     
    },
  ];

    $("#form-crear").simple_json_form({
    data: json_nota,
    }).then(() => {

        $("#txtDestino").autocomplete({
          source: dest,
        });
    
    });

   

    $("#form-crear").validation_form({ opc: "crear-nota" }, function (dtx) {
    title = `¿Desea crear un pedido ?`;

    swal_question(title, "").then((result) => {
    if (result.isConfirmed) {

        console.log(...dtx);
        send_ajax(dtx, ctrl).then((dat) => {
            // $("#form-crear")[0].reset();
             tab_folio();
             
        });

    }

    });
    });
}

function busqueda_categoria() {
  nota = ` `;
  $("#content-buscador").html(nota);
}




let names = [];

function lista_categoria() {
  let datos = new FormData();

  datos.append("opc", "list_cat");

  send_ajax(datos, ctrl).then((data) => {

    names = data.lsName;

    $("#txtCat").option_select({
      data: data.lsCat,
      placeholder: "-- Selecciona una categoria --",
    });
  
  });


}

function cargar_destino() {
  opc = "cargar_clientes";

  $.ajax({
    type: "POST",
    url: url_file + "ctrl-pedido-flores.php",
    data: "opc=" + opc,
    dataType: "json",

    success: function (data) {
      res = eval(data);
      cont = res.length;
      for (var i = 0; i < cont; i++) {
        dest[i] = res[i];
      }
    },
  });
}

function cancelarPedido(id) {
  title = `¿Deseas cancelar la nota `;

  datos = _get_data_opc([], "cancelar-pedido");
  datos = datos + "&id=" + id;

  swal_question(title, "Esto eliminara la nota actual.").then((result) => {
    if (result.isConfirmed) {
      simple_send_ajax(datos, ctrl).then((data) => {
        swal_success();
        crearNota();
      });
    }
  });
}

function __modal(id) {
  flor = $("#td_name" + id).attr("name");

  modal_complementos = bootbox.dialog({
    title: `Producto: ${flor}`,
    message: `
    <form id="miFormulario" novalidate></form>
    <div id="content-complementos"></div>
    `,
  });

  /*Crear formulario y asignar autocomplete */   

  $("#miFormulario").simple_json_form({ data: json_form_ticket });
  $("#txtFlorComplemento").autocomplete({ source: names });

  /* -- Buscar precio  --*/

  $("#txtFlorComplemento").on("blur", () => {

  flor = {
    opc: "buscar-precio",
    flor: $("#txtFlorComplemento").val(),
  };

  simple_send_ajax(flor, ctrl).then((data) => {
    $("#txtPrecioComplemento").val(data.precio);
  });
  });

 /* Cargar tabla de productos adicionales */   

  ls_productos_adicionales(id);

  $("#miFormulario").validation_form(
    {
      opc: "agregar-complemento",
      id: id,
    },

    function (result) {
      send_ajax(result, ctrl).then((dat) => {
        ls_productos_adicionales(id);
        $("#miFormulario")[0].reset();
      });
    }
  );
}

function QuitarComplemento(idComplemento,idProducto) {
  title = `¿Deseas quitar de la lista este producto ?`;

  swal_question(title, "").then((result) => {
    if (result.isConfirmed) {
      datos = _get_simple_datax(null, "quitar-complemento");
      datos = datos + "&idProducto=" + idProducto;
      datos = datos + "&idComplemento=" + idComplemento;
      
      simple_send_ajax(datos, ctrl).then((data) => {
        swal_success();
        ls_productos_adicionales(idProducto);
      });
    }
  });
}

function ls_productos_adicionales(id) {
  
  datos = _get_data([], "ls-complemento");
  datos = datos + "&id=" + id;

  simple_send_ajax(datos, ctrl).then((data) => {
    $("#content-complementos").btn_json_table({
      data: data,
      right: [2,3],   
      id: "tb",
    });

    simple_data_table_no("#tb", 35);
  });
}


/* -----------------------
 > Panel de productos
--------------------------*/
function list_productos() {
  opc = "list-productos";

  $.ajax({
    type: "POST",
    url: url_file + "ctrl-punto-de-venta.php",
    data: "opc=" + opc + "&sub=" + $("#txtSubCat").val(),
    dataType: "json",

    success: function (data) {
      $("#content-productos-1").item_json({
        data: data,
      });
    },
  });
}

function list_tabla_pedido(id) {
  opc = "list_tabla_pedido";

  $("#txtFolio").val(id);

  $.ajax({
    type: "POST",
    url: url_file + "ctrl-punto-de-venta.php",
    data: "opc=" + opc + "&fol=" + id,
    dataType: "json",
    beforeSend: function () {
      $("#content-tab" + id).Loading();
    },
    success: function (data) {
      $("#content-tab" + id).rpt_json_table2({
        data: data,
        right: [2, 3, 4, 5],
      });

      $("#txtCat").removeAttr("disabled");
      $("#txtSubCat").removeAttr("disabled");
    },
  });
}

/* -----------------------
 Historial de pedidos
--------------------------*/

function CancelarFolio(id) {
  title = `¿Esta seguro de cancelar el pedido No ${id} ?`;
  const TEXT = "Este pedido ya no sera visible en el historial";

  swal_question(title, "").then((result) => {
    if (result.isConfirmed) {
      datos = _get_simple_datax(null, "cancelar-folio");
      datos = datos + "&id=" + id;

      simple_send_ajax(datos, ctrl).then((data) => {
        swal_success();
      });
    }
  });
}



function list_sub() {
  let datos = new FormData();
  $("#content-productos-1").html("");

  datos.append("opc", "list_subcat");
  datos.append("sub", $("#txtCat").val());

  send_ajax(datos, ctrl).then((data) => {
    $("#txtSubCat").option_select({
      data: data,
      placeholder: "-- Selecciona una sub-categoria --",
    });
  });
}

function CrearPedido() {
  datos = _get_data(["Destino", "Date", "Nota"], "crear-pedido");

  if ($("#txtDestino").val() == "") {
    swal_msj("El campo lugar o destino no puede quedar vacio");
  } else {
    swal_question(
      `¿Deseas crear el pedio al cliente ${$("#txtDestino").val()} ?`,
      ""
    ).then((result) => {
      if (result.isConfirmed) {
        simple_send_ajax(datos, ctrl).then((data) => {
          tab_folio();
        });
      }
    });
  }
}

function subirPedidos() {
  datos = _get_data(["Folio"], "terminar-formato");

  fol   = $("#txtFolio").val();
  title = `¿Deseas terminar el pedido ?`;

  const TEXT = "";

  swal_question(title, TEXT).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: ctrl,
        data: datos,
        dataType: "json",
        beforeSend: function () {
          $("#content-tab" + fol).Loading();
        },

        success: function (rp) {
          var data = eval(rp);
          $("#content-tab" + fol).html("");
          tab_folio();
        },
      });
    }
  });
}

// REALIZAR PEDIDO ---------------

function agregar_producto(id, costo) {
  fol = $("#txtFolio").val();

  datos = _get_simple_datax(["Folio"], "agregar-producto");
  datos = datos + "&id=" + id + "&costo=" + costo;
  simple_send_ajax(datos, ctrl).then((data) => {
    list_tabla_pedido(fol);
  });
}

function Quitar(id) {
  title = `¿Deseas quitar de la lista el producto ?`;
  fol = $("#txtFolio").val();
  swal_question(title, "").then((result) => {
    if (result.isConfirmed) {
      datos = _get_simple_datax(null, "quitar");
      datos = datos + "&id=" + id;

      simple_send_ajax(datos, ctrl).then((data) => {
        swal_success();
        list_tabla_pedido(fol);
      });
    }
  });
}

function AgregarProductoComplementario(id) {
  data = $("#miFormulario").get_form({ name_opc: "agregar-complemento" });
  data += "&id=" + id;

  simple_send_ajax(data, ctrl).then((data) => {
    $("#content-complementos").btn_json_table({ data: data });
  });
}

$.fn.get_form = function (options) {
  var defaults = {
    tipo: "",
    name_opc: "content-data",
  };

  // Carga opciones por defecto
  var opts = $.fn.extend(defaults, options);

  url = "";

  var formulario = this[0];
  var formData = new FormData(formulario);
  var arregloValores = {};

  formData.forEach(function (valor, clave) {
    url = url + clave + "=" + valor + "&";
  });

  url = url + "opc=" + opts.name_opc;
  return url;
};
