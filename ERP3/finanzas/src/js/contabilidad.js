// import { Gastos } from "./contabilidad-gastos.js";


window.link = window.link || "ctrl/ctrl-contabilidad.php";
window.ctrl = "ctrl/ctrl-contabilidad-turnos.php";
window.modal = window.modal || "";

ctrlGastos = 'ctrl/ctrl-administracion-gastos.php';


// Creacion de objectos
var historial    = {};
var cheques2     = {};
var contabilidad = {};
var tc           = {};
var archivos     = {};
let tabCheque    = {};

var cxc ;

let ctrlTurnos       = {};

var gastos = {};


// Crear variables globales
let bancos = [];
let cta = [];
let destino = [];
let turnos  = [];

let Proveedor = [];


$(function () {

    $("#content-data").simple_json_tab({ data: json_tab });
      // Instancia del objeto
    contabilidad = new Modulo_Tab(link, "content-bar");
    contabilidad.filterBar();

    tabCheque   = new tabCheques(link, "tab-cheque");
 


    cheques2    = new Modulo_formulario(link, "tab-cheque");
    historial   = new Modulo_busqueda(link, "content-tabla-bar");
    tc          = new Modulo_busqueda(link, "content-table-tc");
    archivos    = new Modulo_busqueda(link, "content-table-files");
    rptIngresos = new reporteIngresos(link, "content-rpt-ingresos");
    // turnos
    ctrlTurnos = new ControlTurnos(ctrl, "tab-turno");
    gastos     = new Gastos(ctrlGastos, "tab-gastos");
    cxc = new CxC(ctrlGastos, "tab-cxc");
    

    range_picker_now("iptDate");

    fn_ajax({ opc: 'initComponents', iptFecha: $('#iptDate').val()}, link, "").then((data) => {
        
        bancos    = data.bancos;
        cta       = data.cuenta;
        destino   = data.destino;
        turnos    = data.turnos;
        Proveedor = data.proveedor;
        /* --  Modulos --  */

        ctrlTurnos.initComponents();

        RptGral();
        Cheque();

        historial_bar();
        tabCheque.listCheques();

        historial_tc();
        BuscarHistorialTC();

        historial_Archivos();
        BuscarHistorialArchivos();

        rptIngresos.initComponents();

        gastos.initComponents();
        cxc.listCxC();

    });








    // initComponents(link).then((data) => {

      
        
    // });

  

});




class reporteIngresos extends Complements {

    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    initComponents() {
        this.idFilterBar = "content-bar";

        $('#content-btn-rpt').content_json_form({
            data: this.jsonBarIngresos(),
            type: "",
        });

        this.rptIngresos();
    }

    jsonComponents() {

        return  {

            id: "rpt-general-ingresos",
                class: "card-body  m-2",

                    contenedor: [
                        {
                            type: "div",
                            id: "ingresos",
                            class: " col-12 ",
                        },

                        {
                            type: "div",
                            id: "ingresosD",
                            class: "row ",

                            children: [
                                { class: 'col-6 ', id: 'FP' },
                                { class: 'col-6 mt-2', html: '<strong>Observaciones: </strong>', id: 'lblObs' }
                            ]

                        },



                        {
                            type: "div",
                            id: "Propina",
                            class: "col-12",
                        },
                    ]
        };

    }

    jsonBarIngresos() {
        return [
            {
                opc: "btn",
                fn: "printReport()",
                text: "Imprimir",
                class: "col-12 col-sm-2",
                color_btn: "secondary",
            },
        ];

    }


    rptIngresos() {


        this._dataSearchTable = {
            tipo: "text",
            opc: "rpt",
        };



        var ajax = this.searchTable({ id: "content-rpt-ingresos", extends: true });

        ajax.then((data) => {
            // this.createDivs({ data: this.jsonComponents() });// crear formato de reporte
            
            var defaults = { data: this.jsonComponents(), design: false };

            this.createLayaout(defaults);

            $('#lblObs').append($('<div>',{
                class: 'p-2 ',
                text:data.Observaciones
            }));

            $("#ingresos").rpt_json_table2({
                data: data.table_rpt,
                right: [2, 3, 4, 5],
                f_size: "12",
                color_th: "bg-secondary",
                extends: true,
            });

            $("#FP").rpt_json_table2({
                data: data.table_fp,
                right: [2],
                color_th: "bg-secondary",
            });

            $("#Propina").rpt_json_table2({
                data: data.table_propina,
                right: [2],
                color_th: "bg-secondary",
            });

        });






    }

    imprimir() {
        var divToPrint = document.getElementById("content-rpt-ingresos");
        var html =
            "<html><head>" +
            '<link href="../src/plugin/bootstrap-5/css/bootstrap.min.css" rel="stylesheet" type="text/css">' +
            '<link href="../src/css/navbar.css" rel="stylesheet" type="text/css">' +
            '<body style="background-color:white;" onload="window.print(); window.close();  ">' +
            divToPrint.innerHTML +
            "</body></html>"

        var popupWin = window.open();
        popupWin.document.open();
        popupWin.document.write(html);
        popupWin.document.close();
    }



}




function printReport() {
    rptIngresos.imprimir();
}


/* -- Modulo Historial -- */
function historial_bar() {
    historial.json_filter_bar = [
        {
            opc: "input-calendar",
            id: "fechaHistorial",
            tipo: "text",
            class: "col-3",
            lbl: "Rango de fecha",
        },

        {
            opc: "btn",
            color_btn: "secondary",
            id: "btnBuscarHistorial",
            text: "Busqueda",
            class: "col-2",
            fn: "tabCheque.listCheques()",
        },
    ];

    historial.filterBar('content-historial-bar');
    iptDate("fechaHistorial");
}

function BuscarHistorial() {

    historial.data_table = {
        opc: "historial",

    };

    historial.attr_table = {
        f_size: 12,
        center: [1, 3],
        right: [6],
    };

    historial.lsTable({ id: 'contentHistorialCheques' });
}


function data_range(id) {
    rangepicker(
        "#" + id,
        false,
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

/* -- Modulo Cheques -- */


class tabCheques extends Complements {

    constructor(link, div_modulo) {
        super(link, div_modulo);
    }


    initComponents() {
     
    }
    
    printCheque(id){
        myWindow = window.open("ctrl/pdf_cheques.php?top=" + id, "_blank", "width=750, height=700");
    }

    quitarCheque(id){


        this.swalQuestion({

            opts: {
                title: '¿Deseas eliminar el sig. cheque ?',
            },

            extends: true,

        }).then((result) => {

            if (result.isConfirmed) {

                fn_ajax({

                    opc: 'removeCheque',
                    status:0,
                    idCheque: id,

                }, this._link).then((data) => {

                   

                });
            }

        });

    }

    listCheques(){

        let dates = ipt_date('fechaHistorial');

        this.dataSearchTable = {
            opc: "historial",
            fi : dates.fi,
            ff : dates.ff,
        };

        let ajax = this.searchTable({
            extends:true,
            id:'contentHistorialCheques'
        });

       
        ajax.then((data)=>{
            
            $('#contentHistorialCheques').rpt_json_table2({
                data    : data,
                color_th: 'bg-primary-1',
                center  : [2,4],
                right   : [6],
                id      : 'historialCheques',
            });
            
            
            datable_export_excel('#historialCheques',10);
        });



    }

}



function Cheque() {

    let json_cheque = [



        {
            opc: "input",
            lbl: "Nombre",
            id: "NombreCuenta",
            tipo: "texto",
            required: true,
            class: "col-3 ",
        },
        {
            opc: "input-group",
            lbl: "Importe",
            id: "Importe",
            tipo: "cifra",
            type: "number",
            icon: "icon-dollar",
            class: "col-3 ",
        },
        {
            opc: "select",
            lbl: "No Cuenta",
            id: "Cuenta",
            tipo: "cifra",
            class: "col-3",
            data: cta
        },



        {
            opc: "select",
            lbl: "Banco",
            id: "id_Banco",
            tipo: "cifra",
            class: "col-3",
            data: bancos
        },
        {
            opc: "select",
            lbl: "Destino",
            id: "destino",
            tipo: "cifra",
            class: "col-3",
            data: destino
        },



        {
            opc: "input-group",
            lbl: "Cheque",
            id: "idCheque",
            tipo: "cifra",
            class: "col-3",
            icon: 'icon-hash-1'
        },

        {
            opc: "input",
            lbl: "Concepto",
            id: "Concepto",
            tipo: "cifra",
            class: "col-3",
        },

        {
            opc: "input-file-btn",
            lbl: "Seleccionar archivo",
            id: "archivos",

            required: false,
            class: "col-3 mt-3",
        },




        {
            opc: "btn",
            id: "btnCheque",
            text: "Guardar información",
            fn: "frmCheque()",
            class: "col-3 offset-4 mt-3 ",
        },
    ];

    cheques2.object_forms = {
        data: json_cheque,
        type: "",
    };

    // Objetos para configurar tabla
    cheques2.data_table = {
        opc: "lsCheque",
        iptDate: $("#iptDate").val(),
    };

    cheques2.attr_table = {
        f_size: '12',
        id: "table-cheque",
        right: [5],
        center: [1, 3],
    };

    cheques2.modulo_forms_horizontal({datatable:false});

}

function frmCheque(opts = {}) {



    // $("#jsonForm").validation_form({opc:'frm-cheque'}, (datos) => {

    var archivos = document.getElementById("archivos");
    var archivo = archivos.files;


    if (archivo.length > 0) {

        var filarch = new FormData();

        for (i = 0; i < archivo.length; i++) {
            filarch.append("archivo" + i, archivo[i]);
        }

        filarch.append("opc", "frm-cheques");

        filarch.append("Nombre", $('#NombreCuenta').val());
        filarch.append("Importe", $('#Importe').val());
        filarch.append("id_Banco", $('#id_Banco').val());
        filarch.append("Cuenta", $('#Cuenta').val());
        filarch.append("id_destino", $('#destino').val());


        filarch.append("Fecha", $('#iptDate').val());
        filarch.append("Cheque", $('#idCheque').val());
        filarch.append("Concepto", $('#Concepto').val());

        $.ajax({
            url: link,
            type: "POST",
            contentType: false,
            data: filarch,
            processData: false,
            cache: false,

            success: function (data) {

                lsCheque();


                // $("#Detalles").val("");
                // $("#link").val("");
                $('#jsonForm')[0].reset();
                $("#archivos").val("");
                // lsArchivos();
            },

        });


        /*-- Valir Formulario -- */
        // $("#archivos-formx").validar_contenedor(
        //    opc,(datos)=>{




        //         // form_data_ajax(datos, link).then((data) => {


        //         //     $('#archivos').val("");
        //         //     alert({icon:'success',title:'Se ha creado un nuevo cheque'});




        //         // });
        //     });

    } else {

        alert("No se seleciono ningun archivo");


    }

    // form_data_ajax(datos, link).then((data) => {

    // });
    // });


    // }







}

function lsCheque() {
    cheques2.data_table = {
        opc: "lsCheque",
        iptDate: $("#iptDate").val(),
    };

    cheques2.lsTable();

}


async function quitarArchivo(id) {
    let data_quitar_archivo = {
        opc: 'QuitarArchivo',
        idCheque: id,
    };


    Swal.fire({
        title: "",
        text: "¿Deseas quitar el archivo del sistema ?",
        icon: "warning",
        showCancelButton: true,

        confirmButtonText: "Aceptar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {


              fn_ajax(data_quitar_archivo, link, "")
            .then((data) => {
            //     lsCheque();
            });


            
        }
    });




    // cheques2.removeRow();
}


/* -- Modulo Historial Cheques -- */

function historial_tc() {
    tc.json_filter_bar = [
        {
            opc: "input-calendar",
            id: "fechaTC",
            tipo: "text",
            class: "col-3 col-sm-3",
            lbl: "Rango de fecha",
        },

        {
            opc: "btn",
            color_btn: "secondary",
            id: "btnBuscarHistorial",
            text: "Buscar Historial",
            class: "col-3 col-sm-3 col-lg-2",
            fn: "BuscarHistorialTC()",
        },
    ];

    tc.filterBar('content-tc-bar');
    iptDate("fechaTC");
}


function BuscarHistorialTC() {
    data = ipt_date('fechaTC');

    tc.data_table = {
        opc: "lsHistorialTC",
        fi: data.fi,
        ff: data.ff,

    };

    tc.attr_table = {
        center: [1, 3, 4, 5, 7],
        right: [6],
    };

    tc.lsTable({ id: 'content-table-tc' });
}

/* Historial de archivos */

function historial_Archivos() {
    archivos.json_filter_bar = [
        {
            opc: "input-calendar",
            id: "fechaArchivos",
            tipo: "text",
            class: "col-3 col-sm-2",
            lbl: "Rango de fecha",
        },

        {
            opc: "btn",
            color_btn: "secondary",
            id: "btnArchivos",
            text: "Buscar",
            class: "col-6 col-sm-2 col-lg-2",
            fn: "BuscarHistorialArchivos()",
        },
    ];

    archivos.filterBar('content-archivos-bar');
    iptDate("fechaArchivos");
}

function BuscarHistorialArchivos() {
    data = ipt_date('fechaArchivos');

    archivos.data_table = {
        opc: "lsArchivos",
        fi: data.fi,
        ff: data.ff,

    };

    archivos.attr_table = {
        center: [1, 2,],
        f_size: 12,
        color_th: 'bg-default',
        id: "table-archivos",
        // right: [6],
    };

    archivos.lsTable({ id: 'content-table-files' });

    simple_data_table_no('table-archivos');
}



/* -- Modulo RPT ingresos -- */
function Buscar() {
    lsCheque();
    RptGral();

}

function RptGral() {
    rptIngresos.rptIngresos();


    // var contabilidad = new Modulo_Tab(ctrl, "content-bar");
    // contabilidad.rpt('content-rpt');

}


function iptDate(id) {
    rangepicker(
        "#" + id,
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



}

function ipt_date(id) {
    const fi = $("#" + id).data("daterangepicker").startDate.format("YYYY-MM-DD");
    const ff = $("#" + id).data("daterangepicker").endDate.format("YYYY-MM-DD");



    return { fi, ff };
}

// Creamos el objecto q tendra sus respectivas pestañas
let json_tab = [
    {
        tab: "Reporte Ingresos",
        id: "tab-rpt",
        fn: "RptGral()", // funcion q se ejecutara al activar la pestaña
        active: true, // indica q pestaña se activara por defecto

        contenedor: [

            {
                id: "content-btn-rpt",
                class: "col-sm-12  mb-2",
            },

            {
                id: "content-rpt-ingresos",
                class: "col-sm-12 ",
            },


        ],
    },

    {
        tab: "Turnos",
        id: "tab-turnos",
        fn:'ctrlTurnos.listTurnos()',

        contenedor: [
            // si el tab tendra contenedores especificos, se pueden agregar como objetos
            {
                id: "filterTurnos",
                class: "form-control mb-3",
            },
            {
                id: "contentTurnos",
                class: "col-sm-12 form-control mb-3",
            },
        ],
    },

   

    {
        tab: "Gastos en efectivo",
        id: "tab-gastos",
        fn: 'gastos.lsGastosEfectivo()',
   

    },


    {
        tab: "Cheques",
        id: "tab-cheque",
        fn:'lsCheque()',

    },

    {
        tab: "Historial",
        id: "tab-historial",
  

        fn: "tabCheque.listCheques()",


        contenedor: [
            // si el tab tendra contenedores especificos, se pueden agregar como objetos
            {
                id: "content-historial-bar",
                class: "mb-3",
            },
            {
                id: "contentHistorialCheques",
                class: "col-sm-12 line mb-3",
            },
        ],
    },

    {
        tab: "Mov. TC",
        id: "tab-tc",

        contenedor: [
            {
                id: "content-tc-bar",
                class: "mb-3",
            },
            {
                id: "content-table-tc",
                class: "col-sm-12 mb-3",
            },
        ],
    },



    {
        tab: "Archivos",
        id: "tab-archivos",

        contenedor: [
            // si el tab tendra contenedores especificos, se pueden agregar como objetos
            {
                id: "content-archivos-bar",
                class: "mb-3",
            },
            {
                id: "content-table-files",
                class: "col-sm-12 mb-3",
            },
        ],
    },

     {
        tab: "CxC",
        id : "tab-cxc",
        fn : 'cxc.listCxC()',
          contenedor: [
               {
                id: "container-CxC",
                class: "p-3",
            },
          ]
   
    },


   
];









