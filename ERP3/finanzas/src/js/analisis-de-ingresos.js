window.ctrl_ingresos =
  window.ctrl_ingresos || "ctrl/ctrl-analisis-de-ingresos.php";
window.modal = window.modal || "";

// Instancia de la clase .
let analisis ;
let categoria = [];

$(function () {

    analisis = new AnalisisIngresos(
        ctrl_ingresos,
        "modulo"
    );

    resumen = new ResumenOperativo(
        url,
        "contentResumenOperativo"
    );


    initComponents(ctrl_ingresos).then((data) => {
        categoria = data.categorias;
        

        analisis.initComponents();
        resumen.lsResumenOperativo();

    });

   


  rangepicker(
    "#iptDate",
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

  $("#iptDate")
    .next("span")
    .on("click", () => {
      $("#iptDate").click();
    });
// form_cta();
  ConsultarIngresos();

   
});

class AnalisisIngresos extends Templates{


    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    initComponents(){

        this.tabsAnalisisIngresos();
        this.filterBarChequePromedio();
        this.filterBarResumenOperativo();
        // this.ChequePromedio();
    }

    tabsAnalisisIngresos(){
        let json_tab = [
            {
                tab: "Ingresos", // Nombre de la pestaña
                id: "tab-ingresos",
                fn: 'analisis.ingresos()', // funcion para la pestaña
                active: true,// indica q pestaña se activara por defecto
                class: '',
            },
            {
                tab: "Ingreso mensual",
                id: "tab-ingreso-mensual",
                fn: 'analisis.lsIngresoMensual()', // funcion para la pestaña
            },

            {
                tab: "Cheque promedio",
                id: "tab-cheque-promedio",
                fn: 'analisis.ChequePromedio()',


                contenedor: [
                    {
                        id:'filterBarChequePromedio',
                        class: 'line'
                    },
                    {
                        id: 'contentDataChequePromedio',
                        class: 'line'
                    }
                ]
            },

            {
                tab: "Resumen operativo",
                id: "tab-resumen-operativo",
                fn: 'resumen.lsResumenOperativo()',
                contenedor:[
                    {
                        id: 'filterBarResumenOperativo',
                        class: 'line'
                    },
                    
                    {
                        id: 'contentResumenOperativo',
                        class: ''
                    }
                ]
        

            },


            
        ];

        // Traerlo mediante plugin
        $("#contentData").simple_json_tab({ data: json_tab });




    }

    filterBarResumenOperativo(){


        this.createFilterBar({
            parent: 'filterBarResumenOperativo',
            data: [
                {

                    opc: 'select',
                    lbl: 'Mes',
                    id: 'mesResumen',
                    class: 'col-12 col-sm-3',
                    onchange: 'resumen.lsResumenOperativo()',
                    
                    data: [
                        {id:1,valor:'ENERO'},
                        {id:2,valor:'FEBRERO'},
                        {id:3,valor:'MARZO'},
                        {id:4,valor:'ABRIL'},
                        {id:5,valor:'MAYO'},
                        {id:6,valor:'JUNIO'},
                        {id:7,valor:'JULIO'},
                        {id:8,valor:'AGOSTO'},
                        {id:9,valor:'SEPTIEMBRE'},
                        {id:10,valor:'OCTUBRE'},
                        {id:11,valor:'NOVIEMBRE'},
                        {id:12,valor:'DICIEMBRE'},
                    ]

                }
            ]
        });

    }

    filterBarChequePromedio(){
        let elements = [
            {
                opc: 'select',
                lbl: 'Categoria',
                id:'categoria',
                class:'col-12 col-sm-3',
                onchange:'analisis.ChequePromedio()',
                data: categoria
            },
            {
                opc: 'select',
                lbl: 'Año',
                id: 'Anio',
                class: 'col-12 col-sm-3',
                onchange: 'analisis.ChequePromedio()',
                data: [
                     { id: 2025, valor: '2025' },
                     { id: 2024, valor: '2024' },
                     { id: 2023, valor: '2023' }, 
                     { id: 2022, valor: '2022' },
                     { id: 2021, valor: '2021' },
                     { id: 2020, valor: '2020' },
                     { id: 2019, valor: '2019' },
                     { id: 2018, valor: '2018' },
                     { id: 2017, valor: '2017' },

                    
                    ]
            }
        ];

        this.createFilterBar({
            parent:'filterBarChequePromedio',
            data: elements
        });
    }

    createFilterBar(options){


        var defaults = {
            parent: 'filterBar',
            data: []
        };

        var settings = $.extend( defaults, options);

        $(`#${settings.parent}`).content_json_form({data:settings.data,type:''});
    }


    ingresos(){


        const fechas = calcularFechasComparativas();


        this.createTable({

            parent: 'tab-ingresos',
         
            data: {
                opc: 'lsIngresos',
                fi: fechas.fechaActual1,
                ff: fechas.fechaActual2,
                fechaComparativa1: fechas.fechaComparativa1,
                fechaComparativa2: fechas.fechaComparativa2,
            },

            conf: {
                datatable:false,
            },

            attr: {
                color_th: "bg-primary-1",
                center: [0],
                right: [2, 3, 4, 5],
                color_th: 'bg-primary-1',
            },

            // extends: false




        });

    }

    lsIngresoMensual(){
      
        this.createTable({
            
            parent: 'tab-ingreso-mensual',

            data:{
                opc: 'ingresoMensual',
                Anio: 2025
            },
            
            conf:{
                // datatable:false,
                fn_datatable: 'data_table_export'

            },

            attr:{
                color_th: 'bg-primary-1',
                class_table: 'table table-bordered table-sm table-striped',
                right: [2, 3, 4, 5,6,7,8,9,10,11,12]
            },

            extends: false




        });
    }

    ChequePromedio(){
        this.createTable({

            parent: 'contentDataChequePromedio',
            idFilterBar: 'filterBarChequePromedio',

            data: {
                opc: 'chequePromedio',
            },
            conf:{
                datatable:true,
                fn_datatable: 'data_table_export',
                pag:20
            },
        

            attr: {
                color_th: 'bg-primary-1',
                f_size: 11,
                class_table: 'table table-bordered table-sm ',
                right: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                extends:true,
                id:'tbChequePromedio'
            },

            extends: false

        });
    }

 
    

}


function verCategoria(id) {
  const fechas = calcularFechasComparativas();

  // Crear un objeto para almacenar los parámetros
  var parametros = {
    opc: "lsCategoria",
    fi: fechas.fechaActual1,
    ff: fechas.fechaActual2,
    id: id,
  };

  // Convertir el objeto en una cadena de consulta
  var datos = $.param(parametros);

  fn_ajax(datos, ctrl_ingresos, "#tab-ingresos").then((data) => {
    
      $("#tab-ingresos").rpt_json_table2({ 
        data: data,
        color_th: "bg-primary-1",
        color_group: "bg-disabled2",
        class_table: "table table-bordered table-sm table-striped",
        center: [2],
     });
  
  
  
  
  


  });
}


function ConsultarIngresos() {

    const fechas = calcularFechasComparativas();

    datos = _get_data([], "lsIngresos");
    datos +=
        "&fi=" +
        fechas.fechaActual1 +
        "&ff=" +
        fechas.fechaActual2 +
        "&fechaComparativa1=" +
        fechas.fechaComparativa1 +
        "&fechaComparativa2=" +
        fechas.fechaComparativa2;

    fn_ajax(datos, ctrl_ingresos, "#tab-ingresos").then((data) => {
        $("#tab-ingresos").rpt_json_table2({
            data: data,
            center: [0],
            right: [2, 3, 4, 5],
            color_th: 'bg-primary-1',


        });
    });

}



// -- Complementos --
function calcularFechasComparativas() {
  const fechaActual1 = $("#iptDate")
    .data("daterangepicker")
    .startDate.format("YYYY-MM-DD");
  const fechaActual2 = $("#iptDate")
    .data("daterangepicker")
    .endDate.format("YYYY-MM-DD");
  const yearActual = $("#iptDate")
    .data("daterangepicker")
    .endDate.format("YYYY");

  const yearComparativo = $("#cbAnioComparativo").val();
  const dia1 = $("#iptDate").data("daterangepicker").startDate.format("DD");
  const mes1 = $("#iptDate").data("daterangepicker").startDate.format("MM");
  const dia2 = $("#iptDate").data("daterangepicker").endDate.format("DD");
  const mes2 = $("#iptDate").data("daterangepicker").endDate.format("MM");

  yearAnormal = yearComparativo;
  if (mes2 < mes1) yearAnormal = parseInt(yearComparativo) - 1;

  const fechaComparativa1 = yearAnormal + "-" + mes1 + "-" + dia1;
  const fechaComparativa2 = yearComparativo + "-" + mes2 + "-" + dia2;

  return {
    fechaActual1: fechaActual1,
    fechaActual2: fechaActual2,
    fechaComparativa1: fechaComparativa1,
    fechaComparativa2: fechaComparativa2,
    yearActual: yearActual,
    yearComparativo: yearComparativo,
  };
}

$.fn.rep_json_table = function (options) {
  //Variables para la creacion de una tabla
  thead = "";
  tbody = "";
  table = "";

  var defaults = {
    tipo   : "simple",
    data   : json,
    name   : "simple-table",
    right  : [4],
    center : [0],
    color  : null,
    grupo  : "bg-default",
    th     : "bg-default",
    class_table:'table table-bordered ',
    folding: false,
  };

  var opts = $.fn.extend(defaults, options);
  th = opts.th;
  head  = opts.data.head;
  // Imprime las columnas de la tabla
  for (const k of opts.data.thead) {
    thead += `<th class="text-center ${th}">${k}</th>`;
  }

  var r  = opts.right;
  var c  = opts.color;
  var ct = opts.center;
  // Imprime la informacion contenida en rows

  for (const x of opts.data.row) {
    idRow = x.id;
    const v = Object.values(x);
    let dimension = v.length;
    let last = dimension - 1;
    clase = "";

    ico_group = "";

    if (v[last] == 1) {
      clase = opts.grupo;
      ico_group = `<i style="color:white;" class="ico${idRow}  icon-right-open"></i>`;
    }

    fold = "";
    class_fold = "";

    if (opts.folding == true) {
      if (v[last] == 1) {
        fold = `onclick="unfold(${idRow})"`;
      } else {
        class_fold = `unfold${idRow} d-none`;
      }
    }

    tbody += `<tr class="${clase}  ${class_fold}" ${fold}>`;

    for (let col = 1; col < dimension - 1; col++) {
      right = "";
      color = "";
      center = "";

      for (let $i = 0; $i < r.length; $i++) {
        if (r[$i] == col) {
          right = "text-right text-end";
        }
      }

      if (c != null) {
        for (let $x = 0; $x < c.length; $x++) {
          if (c[$x] == col) {
            if (v[last] != 1) {
              color = "bg-default-gv bg-aliceblue";
            }
          }
        }
      }

      for (let $c = 0; $c < ct.length; $c++) {
        if (ct[$c] == col) {
          center = "text-center ";
        }
      }

      ico_span = "";

      if (col == 1) {
        ico_span = ` ${ico_group} `;
      }

      tbody += `<td style="font-size:16px;" class="${right}  ${center}  ${color}"> ${ico_span} ${v[col]}  </td>`;
    }

    tbody += `</tr>`;
  } // end row
  
  // Imprime los resultados en una tabla

    table = `
    <table style="margin-top:15px; font-size:14px;"
    class="${opts.class_table}" width="100%" id="${opts.name}">

    <thead>
        <tr>
        ${thead}
        </tr>
    </thead>

    <tbody>
    ${tbody}
    </tbody>
    </table>   
    `;


    div = `
    <div class=""> ${head} </div>
    <div class="table-responsive">${table}</div>
    `;

  $(this).html(div);
};



