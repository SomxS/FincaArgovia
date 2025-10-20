
    window.link = 'ctrl/ctrl-remisiones.php';
    window.modal = window.modal || '';

 /* Init components */ 
$(function () {
 range_picker_now("iptDate");
 lsFolios();
});


function lsFolios() {

  console.log("list_folio");

  dtx = {
    opc: "lsFolios",
    fi : ipt_date().fi,
    ff : ipt_date().ff,
  };

  fn_ajax(dtx, link, "#content-folios").then((data) => {
    
    $("#content-folios").rpt_json_table2({
      data  : data,
      name  : "table_pedidos",
      color_th: "bg-primary",
      center: [1],
      right  : [2],
    });

    simple_data_table_no("#simple-table", 10);
  });
}

function verTicket(id) {
 
  dtx = {
    opc: 'ver-ticket',
    id: id,
  }

  fn_ajax(dtx, link, "#content-visor").then((data) => {

    $("#content-visor").rpt_json_table2({
      color_th: "bg-primary",
      data: data,
      f_size: 14,
      right: [2, 3, 4],
      parametric: true,
    });
  });

 
}

function VerPedidos(id) {
  dtx = {
    opc: "ver-pedidos",
    id: id,
  };

  fn_ajax(dtx, link, "#content-visor").then((data) => {
    
    $("#content-visor").rpt_json_table2({
      color_th: "bg-primary",
      data    : data,
      f_size  : 14,
      right   : [2, 3, 4],
      parametric: true,
    });
  });
  
}


/* ver ticket especifico */

function ver_tickets(id) {
  dtx = { opc: "ver-ticket-cliente", id: id };

  fn_ajax(dtx, link, "#content-visor").then((data) => {
    $("#content-visor").rpt_json_table2({
      data      : data,
      right     : [3,4,5],
    });
  });
}


/* Complementos */
function range_picker_now(id) {
  rangepicker(
    "#" + id,
    false,
    moment().subtract(6, "days"),
    moment(),
    {
      Hoy: [moment(), moment()],
      Ayer: [moment().subtract(1, "days"), moment().subtract(1, "days")],
      "Última semana": [moment().subtract(6, "days"), moment()],
      "Mes anterior": [
        moment().subtract(1, "month").startOf("month"),
        moment().subtract(1, "month").endOf("month"),
      ],
    },
    "",
    function (startDate, endDate) {
      date1 = startDate.format("YYYY/MM/DD");
      date2 = endDate.format("YYYY/MM/DD");
    }
  );
}

function ipt_date() {
  const fi = $("#iptDate")
    .data("daterangepicker")
    .startDate.format("YYYY-MM-DD");
  const ff = $("#iptDate").data("daterangepicker").endDate.format("YYYY-MM-DD");

  return { fi, ff };
}

function print_formato() {

  var divToPrint = document.getElementById("content-rpt");
  var html =
    "<html>" +
    "<head>" +
    '<link href="recursos/css/print.css" rel="stylesheet" type="text/css">' +
    '<link rel="stylesheet" href="recursos/css/bootstrap/bootstrap.min.css">' +
    '<link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">' +
    '<style type="text/css" media="print"> @page{  margin-top:  20px;' +
    "margin-bottom:   20px;" +
    "margin-left:   20px;" +
    "margin-right:    30px;" +
    "} </style>" +
    '<body onload="window.print(); ">' +
    divToPrint.innerHTML +
    "</body>" +
    "</html>";
  //  window.close();

  var popupWin = window.open();
  popupWin.document.open();
  popupWin.document.write(html);
  popupWin.document.close();
}

