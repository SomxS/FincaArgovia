link = "ctrl/ctrl-movimientos-diarios.php";
ctrl_mov = "ctrl/ctrl-cortesias.php";
ctrl_cxc = "ctrl/ctrl-cxc.php";
ctrl_tc = "ctrl/ctrl-movimientos-diarios-tc.php";

/* -- Variables --*/

let collector   = [];
let turnos      = [];
let categoria   = [];
let json_filter = [];

let terminal     = [];
let tipoterminal = [];
let checklist    = [];

let cortesias = {};
let cxc = {};
let sub_cortesias = {};

let tcx = {};
let rptGral = {};
let ingresos = {};
let files = {};

let turnosCerrados = {};
let listTurnos = {};
let finanzas = {};

/* -- Init components --*/

$(function () {

    range_picker_now("iptDate");
    $("#content-tab-ingresos").simple_json_tab({ data: json_tab });
    
    
    finanzas = new MovDiarios(link, '');
    // Ingresos:
    ingresos = new moduloIngresos(link, "tab-ingresos");
    cxc = new Complements(ctrl_cxc, "tab-cxc");
    lsCXC();


    // Reporte gral:
    rptGeneral();
    filtroBusqueda();
    
    // Cortes diarios: 
    turnosCerrados = new ControlTurnos(link, "tab-turno");
    listTurnos = new Modulo_busqueda(link, "tab-turno");
    // Tab turnos:
    turnosCerrados.initComponents();
    files = new filesUpload(link, "tab-files");
    
    
    // Tarjeta de credito:
    tcx = new modulo_uno(ctrl_tc, "tab-tcx");

    // tab cortesias & Empleados : mod 
    cortesias = new modulo_uno(ctrl_mov, "tab-cortesias");


    finanzas.sendAjax({ opc: 'initComponents', date: $('#iptDate').val() }).then((data) => {

        // Inicializar colecciones de datos:

        collector     = data.folio;
        turnos        = data.turnos;
        categoria     = data.categoria;
        tipoterminal  = data.tipoTerminal;
        terminal      = data.terminal;
        checklist     = data.checklist;
        sub_cortesias = data.cortesias;


        finanzas.dataInitFolio();


        // // tab subir archivos:
        // files.initComponents();

       
        // interface();
        // cortesia_empleados();

    
        // data_folio();
        

        // // tab Tarjetas de credito :
        // interface_tc();

     

    });


 

});

function lsArchivos() {
    fecha = ipt_date();

    let datos = {
        opc: "lsArchivos",
        date: fecha.fi,
    };

    fn_ajax(datos, link, "#content-table").then((data) => {
        $("#content-table").rpt_json_table2({
            data: data,
            color_th: "bg-primary",
            f_size: 12,
            id: "table",
        });

        // simple_data_table_no('#table',10)
    });
}

function data_folio() {
    let data_ticket_abierto = collector;

    if (data_ticket_abierto) {
        $("#btnApertura").addClass("d-none");
        $("#btnCierre").removeClass("d-none");

        $("#content-folio").html('No: <span class="text-danger">' + data_ticket_abierto.id + "</span>");
       
        $("#content-encargado").html(
            'Encargado: <span class="text-info">' +
            data_ticket_abierto.encargado +
            "</span>"
        );

        ingresos.filtroBusqueda();
        ingresos.lsIngresos();

    } else {

        // Ingresos
        $('#contentIngresos').empty_state();
        $('#contentFilterIngresos').html('');
  


        $("#btnCierre").addClass("d-none");
        $("#btnApertura").removeClass("d-none");
        $("#content-folio").html("No se ha creado ningun turno");
        $("#content-hora").html("");
        $("#content-ingresos").html("");
        $("#content-encargado").html("");
        $("#tab-turno").empty_state();
        $("#rptGeneral").empty_state();

        // Ingresos/ barra de filtros:
        // ingresos.filtroBusqueda();

        // Turnos cerrados:
        turnosCerrados.initComponents();
    }
}

/* Apertura y cierre de turno */

function CrearTurno() {

    modal = bootbox.dialog({
        title: `Bienvenido `,
        message: `<form class="" id="form-turno"  novalidate></form>`,
    });

    /*--  --*/
    $("#form-turno").simple_json_form({
        data: json_apertura,
        type_btn: "two_btn",
    });

    /*--  --*/
    $("#form-turno").validation_form(
        {
            tipo: "text",
            opc: "crear-folio",
            id_UDN: 1,
            Fecha: $("#iptDate").val(),
        },
        (datos) => {
            fn_ajax(datos, link, "").then((data) => {

                alert();

                modal.modal("hide");

                collector = data.folio;

                finanzas.dataInitFolio();

                // data_folio();
                


            });
            // MessageDialogConfirm("...", datos);
        }
    );
}

function MessageDialogConfirm(mnsj, datos) {
    Swal.fire({
        title: mnsj,
        text: "¿Deseas continuar?",
        icon: "warning",
        showCancelButton: true,

        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
        }
    });
}

let json_apertura = [
    {
        opc: "input-group",
        lbl: "Encargado:",
        id: "encargado",
        placeholder: "Ingresa tu nombre para continuar",
        tipo: "texto",
        icon: "icon-user-o",
    },
];

function CerrarTurno() {
    Swal.fire({
        title: "¿Deseas cerrar turno ?",
        text: "",
        icon: "warning",
        showCancelButton: true,

        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            let dtx_ = {
                opc: "cerrar-folio",
                FechaModificacion: $("#iptDate").val(),
                id_estado: 2,
                idFolio: collector.id
            };

            fn_ajax(dtx_, link, "").then((data) => {
                collector = data.folio;
                turnos = data.turnos;
                console.log('turnos',turnos);
                
                finanzas.dataInitFolio();
                
                // data_folio();
                
                // $("#content-data").empty_state("Vacio");
                // ticket_abierto();
            });
        }
    });
}



/* --  Paneles  -- */
function consultarRegistros() {
    fecha = ipt_date();
    datos = {
        opc: "consultarRegistros",
        date: fecha.fi,
    };

    fn_ajax(datos, link, "").then((data) => {
        collector = data;
        data_folio();
    });
}


function CalculoDiferencia() {

    let monto = $("#txtiptMonto").val();

    let efectivo = parseFloat($("#txtEfectivo").val());
    let tc = parseFloat($("#txtTC").val());
    let cxc = parseFloat($("#txtCxC").val());
    let anticipo = parseFloat($("#txtAnticipo").val());


    efectivo = isNaN(efectivo) ? 0 : efectivo;
    tc = isNaN(tc) ? 0 : tc;
    cxc = isNaN(cxc) ? 0 : cxc;
    anticipo = isNaN(anticipo) ? 0 : anticipo;


    diferencia = monto - (efectivo + tc + anticipo + cxc);


    $("#txtiptDiferencia").val(diferencia);

}

function MostrarMovimiento() {
    let dtx = {
        opc: "EditarMovimientos",
        idMovimiento: $("#id_grupo").val(),
    };


    fn_ajax(dtx, link, "").then((data) => {
        $("#content-frm")[0].reset();
        $("#content-frm").edit_json_form({ data: data });


    });
}



function filtro_busqueda() {
    json_filter = [
        {
            opc: "select",
            lbl: "Categoria",
            id: "Categoria",
            id: "Categoria",
            tipo: "texto",
            data: categoria,
            class: 'col-12 col-sm-3'
        },

        {
            opc: "btn",
            id: "btnBuscar",
            tipo: "texto",
            text: "Buscar",
            fn: "ingresos()",
        },

        {
            opc: "btn",
            id: "btnCapturar",
            tipo: "texto",
            text: "Agregar Mov.",
            fn: "frm_ingresos()",
            color_btn: "secondary",
        },

        {
            opc: "input-file",
            id: "btnSubir",
            text: "Subir XLS",
            fn: "SubirArchivo()",
            color_btn: "info",
        },
    ];

    $("#content-ingresos").simple_json_content({
        data: json_filter,
        type: "",
    });
}



function EnviarDatos() {
    var archivos = document.getElementById("txtarchivos");
    var archivo = archivos.files;
    cant_fotos = archivo.length;
    valor = true;

    if (cant_fotos > 0) {
        var filarch = new FormData();

        for (i = 0; i < archivo.length; i++) {
            filarch.append("archivo" + i, archivo[i]);
        }

        //   link = $("#txtSubcategoria").val();

        filarch.append("date", $("#iptDate").val());
        filarch.append("Detalles", $("#txtDetalles").val());
        filarch.append("opc", "files");

        $.ajax({
            url: link,
            type: "POST",
            contentType: false,
            data: filarch,
            processData: false,
            cache: false,

            success: function (data) { },
        });
    }
}



/*-- Complementos --*/


function modal_apertura() {
    Swal.fire({
        title: "Bienvenido ",
        text: "¿Deseas abrir turno?",
        icon: "warning",
        showCancelButton: true,

        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            alert();
        }
    });
}



function ipt_date() {
    const fi = $("#iptDate")
        .data("daterangepicker")
        .startDate.format("YYYY-MM-DD");
    const ff = $("#iptDate").data("daterangepicker").endDate.format("YYYY-MM-DD");

    return { fi, ff };
}


