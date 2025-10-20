window.ctrl = "ctrl/";
window.modal = window.modal || "";
$(function () {
    reporte_gral();
 

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
        date1 = startDate.format("YYYY-MM-DD");
        date2 = endDate.format("YYYY-MM-DD");
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
  ver_archivos();

});


/* --------------------------
  Reporte de ingresos  
_____________________________*/ 
ctrl_gral = ctrl + 'ctrl-ingreso-reporte-gral.php';

function reporte_gral() {

  datos = _get_data(null, "report-gral");

  simple_send_ajax(datos, ctrl_gral).then((data) => {
     $('#content-report-gral').simple_json_table({
        data:data,
        right:[1,2,3,4]   
    });

  });
}




function ver_archivos() {
  opc = "files";

  $.ajax({
    type: "POST",
    url: ctrl + "ctrl-ingreso-diarios.php",
    data: "opc=" + opc,
    dataType: "json",
    beforeSend: function () {
      $("#content-file").Loading();
    },

    success: function (data) {
      $("#content-file").simple_json_table({
        data: data,
        right: [2, 3, 4],
        name: "tb",
        color: [4],
      });

      dataTable_responsive("#tb", [{ responsivePriority: 1, targets: 0 }]);
    },
  });
}

function ver_tc() {
  opc = "tc";

  $.ajax({
    type: "POST",
    url: ctrl + "ctrl-ingreso-diarios.php",
    data: "opc=" + opc,
    dataType: "json",
    beforeSend: function () {
      $("#content-tc").Loading();
    },

    success: function (data) {
      $("#content-tc").simple_json_table({
        data: data,
        right: [2, 3, 4],
        name: "tb",
        color: [4],
      });
    },
  });
}
