
class ControlTurnos extends Complements {

    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    initComponents() {
        this._idFilterBar = "contentfilterTurnos";

        this.crearFormato();
        this.filterTurnos();
        this.lsCorteX();
    }

    crearFormato() {
        let json_grupos = {

            id: "grupo",
            class: "col-12",

            contenedor: [

                {
                    id: "contentfilterTurnos",
                    class: "col-12 mb-2"
                },
                {
                    id: "lsIngresos",
                    class: "col-12 "
                },

                {
                    id: "lsTarjetasCredito",
                    class: "col-12 "
                },

            ]
        };

        this.createDivs({ data: json_grupos, div_content: 'tab-turno' });// crear formato de reporte
    }

    filterTurnos() {
  
        listTurnos.jsonFilterBar = this.jsonFilter();
        listTurnos.filterBar('contentfilterTurnos');

    }

    jsonFilter() {

        return [
           
            {
                opc     : "select",
                lbl     : "Cortes del día ",
                id      : "idFolio",
                tipo    : "texto",
                data    : turnos,
                class   : "col-12 col-md-6 col-lg-4",
                onchange: "turnosCerrados.lsCorteX()",
                selected: '--Selecciona un turno --'
            },

            {
                opc  : 'btn',
                id   : 'btnImprimir',
                text : 'Imprimir',
                class: 'col-12 col-lg-2 col-sm-3',
                fn   : 'turnosCerrados.printCorte()'

            },



        ];
    }


    lsCorteX() {


        this._dataSearchTable = {
            tipo: "text",
            opc: "lsTurnos",
            date: $("#iptDate").val(),
        };

        var ajax = this.searchTable({ id: "lsIngresos", extends: true });

        ajax.then((data) => {

            $('#lsTarjetasCredito').rpt_json_table2({ data:data.lsTarjetas});

            $("#lsIngresos").rpt_json_table2({
                data: data.lsTurnos,
                f_size   : "12",
                color_th : "bg-primary-1",
                right    : [2, 3, 4, 5,6,7],
                color_col: [3,4,5,6],
            });
        });

    }

    printCorte() {
        var divToPrint = document.getElementById("lsIngresos");
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


function filtro_reportes() {

    json_filter = [
        {
            opc: "select",
            lbl: "Cortes del día ",
            id: "Turnos",
            tipo: "texto",
            data: turnos,
            class: "col-12 col-md-6 col-lg-3",
            //   selected: "Reporte Z",
        },
    ];

    $("#content-rptTurnos").simple_json_content({
        data: json_filter,
        color_btn: "secondary",
        fn: "modulo_turnos()",
    });


}



class RptTurnos {
    constructor(idContenedor, idFiltro) {
        this._idFiltro = idFiltro;
        this._idContenedor = idContenedor;
    }

    static object_data_table = {};
    static object_data_filtro = {};

    docRPT() {


        $("#" + this._idFiltro).validar_contenedor(
            {
                tipo: "text",
                opc: "lsTurnos",
                idFolio: $("#Turnos").val(),
            },
            (dtx) => {
                fn_ajax(dtx, link, "#" + this._idContenedor).then((data) => {

                    // Configuracion por defecto de la tabla 
                    let object_rp = { data: data };


                    // Si tiene una configuracion extra combina ambos objetos   
                    if (RptTurnos.object_data_table)
                        object_rp = Object.assign(object_rp, RptTurnos.object_data_table);


                    $("#" + this._idContenedor).rpt_json_table2(object_rp);
                });
            }
        );
    }

    set object_table(valor) {
        RptTurnos.object_data_table = valor;
    }

    get object_table() {
        return RptTurnos.object_data_table;
    }
}




// Instancia de la clase 

rpt = new RptTurnos("content-Turnos", "content-rptTurnos");


function modulo_turnos() {

    //   rpt.object_table = { 
    //    color_th : 'bg-primary-1',
    //    right:[2,3,4,5]
    //   };  





    rpt.docRPT(null);
}



