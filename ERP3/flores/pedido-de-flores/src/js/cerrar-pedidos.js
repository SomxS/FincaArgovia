// Init Components
$(function () {});

/*  Variables  */

cerrar = "ctrl/ctrl-cerrar-pedidos.php";

/* Funciones  */
function view_cerrar_pedidos() {
  $("#content-historial").addClass("d-none");
  $("#content-pos").addClass("d-none");

  datos = _get_data(null, "list-formato-cerrar-pedido");

  simple_send_ajax(datos, cerrar).then((data) => {

    if(data.rows != null){
        $("#content-tabla-cerrar-pedidos").doc_2_cols({
        data: data,
        right: [3],
        center: [1],
        });
    }else{

        $("#content-tabla-cerrar-pedidos").html('<h5 class="text-center mb-5 mt-5">No se encontraron pedidos activos</h5>');
    }

    
  });
}

names = [];
function list_productos_complementarios() {
  datos = _get_data(null, "list-flores");

  simple_send_ajax(datos, cerrar).then((data) => {
    res = eval(data);

    cont = res.length;
    for (var i = 0; i < cont; i++) {
      names[i] = res[i];
    }
  });
}


function cerrarGrupoPedidos() {
  Swal.fire({
    title: "¿Desea cerrar los pedidos pendientes?",
    text: "Esto creara un grupo de pedidos para realizar el proceso de envio",
    icon: "warning",
    showCancelButton: true,
  }).then((result) => {
    if (result.isConfirmed) {
        datos = { opc: "cerrarGrupoPedidos" }; 
        simple_send_ajax(datos, cerrar).then((data) => {

        });
        
    //   swal_success();
    //   
    }
  });
}



/* -----------------------
 ** Complementos
--------------------------*/

$.fn.doc_2_cols = function (options) {
  tbody = "";

  var defaults = {
    data: [],
    right: [],
    center: [],
  };

  // Carga opciones por defecto
  var opts = $.fn.extend(defaults, options);
  var col = 0;

  for (const x of opts.data.cols) {
    if (col == 0) {
      tbody += `<div class="col-6">`;
    }

    col = col + 1;

    const columnas = Object.values(x);
    let dimension = columnas.length;
    let ultima_col = dimension - 1;

    // Imprimir grupo
    tbody += `<table class="table table-bordered table-sm mt-3 "><thead><tr>`;

    tbody += `<th class="fw-bold" 
      colspan="${ultima_col}" style="font-size:12px;">  ${x.titulo}  </th>`;

    tbody += `</tr></thead><tbody>`;

    // Variables de posicionamiento & color
    var r = opts.right;
    var ct = opts.center;

    // Imprimir rows
    for (const y of x.row) {
      const columnas_row = Object.values(y);
      let dimension_row = columnas_row.length;
      let ultima_row = dimension_row - 1;

      tbody += `<tr>`;

      for (let col = 1; col < dimension_row - 1; col++) {
        // Variables de posicionamiento & color
        right = "";
        center = "";

        for (let $i = 0; $i < r.length; $i++) {
          if (r[$i] == col) {
            right = "text-right text-end";
          }
        }

         for (let x = 0; x < ct.length; x++) {
           if (ct[x] == col) {
             center = "text-right text-end";
           }
         }

        tbody += `<td class="${right} ${center}" style="font-size:.76em;">  ${columnas_row[col]}  </td>`;
      }

      tbody += `</tr>`;
    } // end row

    tbody += `</tbody></table>`;

    if (col == 2) {
      tbody += `</div>`;
      col = 0;
    }
  }

  frm = ` <div class="row">  ${tbody} </div>`;

  $(this).html(frm);
};

$.fn.add_item = function (options) {
  input = "";

  var defaults = {
    lbl: "Input default",
    id: null,
    type: "text",
    d: "",

    holder: "",
    icon: "",
    value: "",
    position: "left",
  };

  // Carga opciones por defecto
  var opts = $.fn.extend(defaults, options);

  id_input = opts.id;
  if (opts.id == null) {
    id_input = opts.lbl;
  }

  // Imprime los inputs
  if (opts.icon == "") {
    input += `
    <div class="col-sm-12 mb-3">
      <label class="fw-bold"> ${opts.lbl}</label>
     
      <input ${opts.d} 
      type="text" class="form-control ui-autocomplete-input" 
      placeholder="${opts.holder}" id="txt${id_input}"
      aria-label="" name="${id_input}" >
    </div>
    `;
  } else {
    r = "";
    l = "";
    if ((opts.position = "left")) {
      r = `  <span class="input-group-text" id="basic-addon2"><i class="${opts.icon}" ></i></span>`;
    } else {
      l = `  <span class="input-group-text" id="basic-addon2"><i class="${opts.icon}" ></i></span>`;
    }

    input += `
     <div class="col-sm-12 mb-3">
        
        <div class="input-group  input-sm">
        ${r}
        <input ${opts.d} type="text" class="form-control ui-autocomplete-input" placeholder="${opts.holder}" id="txt${id_input}"
        aria-label="" name="${id_input}" aria-describedby="basic-addon2">
        ${l}
        </div>
    </div>
    `;
  }

  $(this).append(input);
  //    return $frm ;
};
