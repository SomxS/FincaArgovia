window.ctrl =  "ctrl/ctrl-mantenimiento-almacen.php";
window.modal = window.modal || "";
$(function () {
  let datos = new FormData();
  datos.append("opc", "listUDN");
  send_ajax(datos, ctrl).then((data) => {
    let option = "";
    data.forEach((p) => {
      option += `<option value="${p.id}">${p.valor}</option>`;
    });
    $("#cbUDN").html(option);
  });
  if (typeof moment === "function") {
    rangepicker(
      "#iptDate",
      false,
      moment().subtract(1, "days"),
      moment(),
      {
        Hoy: [moment(), moment()],
        Ayer: [moment().subtract(1, "days"), moment().subtract(1, "days")],
        "Última semana": [moment().subtract(6, "days"), moment()],
        "Mes actual": [moment().startOf("month"), moment().endOf("month")],
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

    $("#iptDate")
      .next("span")
      .on("click", () => {
        $("#iptDate").click();
      });
  } else {
    Swal.fire({
      title: "404 Not Found",
      text: "Moment.js",
      icon: "error",
      width: 300,
      showConfirmButton: false,
      timer: 1000,
    });
  }

  $("#btnOk").on("click", () => {
    swal_question("¿Esta seguro de realizar esta acción?").then((result) => {
      if (result.isConfirmed) {
        //let datos = new FormData();
        //datos.append('opc','');
        //send_ajax(datos,ctrl).then((data)=>{
        //console.log(data);
        swal_success();
        //});
      }
    });
  });
});

function ver_list_mtto() {
  opc = "list";
    udn = $("#cbUDN").val();
    $("#content-item").addClass("d-none");
    $("#content-tabla").removeClass("d-none");

  $.ajax({
    type: "POST",
    url: ctrl,
    data: "opc=" + opc+"&udn="+udn,
    dataType: "json",
    beforeSend: function () {
      $("#content-tabla").Loading();
    },

    success: function (data) {
      $("#content-tabla").btn_json_table({
        data: data,
        name: "tb-mtto",
      });
        
      simple_data_table_no("#tb-mtto", 10);  
        
    },
  });
}

function consulta_item(id) {
  opc = "list-esp";
 
  $("#content-item").addClass("d-none");
  $("#content-tabla").removeClass("d-none");

  $.ajax({
    type: "POST",
    url: ctrl,
    data: "opc=" + opc + "&udn=" + id,
    dataType: "json",
    beforeSend: function () {
      $("#content-tabla").Loading();
    },

    success: function (data) {
      $("#content-tabla").btn_json_table({
        data: data,
        name: "tb-mtto-esp",
      });

      simple_data_table_no("#tb-mtto-esp", 10);
    },
  });
}



function list_item() {
     $("#content-item").removeClass("d-none");
     $("#content-tabla").addClass("d-none");

  datos = _get_simple_datax(null, "list-item");

  simple_send_ajax(datos, ctrl).then((data) => {
      $("#content-item").item_json({ data: data }); 
  });
}