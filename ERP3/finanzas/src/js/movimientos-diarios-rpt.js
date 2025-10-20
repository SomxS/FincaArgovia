/* -- Modulo Reporte General  -- */

let rpt    = {};
let json_rpt_gral = {};




function filtroBusqueda() {



    let json_filter = [

        {
            opc  : "btn",
            fn   : "CierreHotel()",
            text : "Imprimir ",
            class: "col-sm-2",
        },
        {
            opc: "btn",
            fn: "rpt.agregarObservaciones()",
            text: "Agregar observaciones", 
            class: "col-sm-3",
            color_btn: "info"
        }
    ];

    $("#content-rpt-filter").content_json_form({ data: json_filter, type: false,class:"row mb-3" });

}

function rptGeneral() {

    rpt = new Rpts(link, "content-rpt");


    let json_components =  {
        id: "RptIngresos",
            class: "col-12",
            contenedor: [
              
                {
                    id: "ingreso",
                    class: "col-12 "
                },
                {
                    id: "FP",
                    class: "col-12 "
                },
                {
                    id: "Propina",
                    class: "col-12 "
                }
            ]
    };

    rpt.dataSearchTable = {
        tipo: "text",
        opc : "rptGeneral",
        date: $("#iptDate").val(),
        // folio: collector.id
    };
   
    var ajax = rpt.searchTable({ id: "content-rpt",extends:true});

  

    ajax.then((data) => {

        rpt.createDivs({ data: json_components });// crear formato de reporte
        
        // $('#btnFilter').html(data.btn);

        $("#ingreso").rpt_json_table2({
            data    : data.table_rpt,
            right   : [2, 3, 4, 5],
            f_size  : "12",
            color_th: "bg-secondary",
            extends:true,
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

function CierreHotel() {
    var divToPrint = document.getElementById("content-rpt");
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


class Rpts extends Complements {

    constructor(link, div_modulo) {
        super(link, div_modulo);
    }



    agregarObservaciones(){
        this._data_form = {
            opc: 'addObservaciones',
            idFolio: collector.id
        };

    
        this.modal_formulario({
            json:this.jsonObservaciones(),
            fn: 'rptGeneral',
        });
    }


    jsonObservaciones (){
      
        return [
            {
                opc: 'textarea',
                lbl: 'Observaciones',
                id: 'Observacion',
                required: false,
                class: 'col-12',
                value: $('#Obs').text()
            }
        ];
    }

    printReport(){

    }

   


   

}

