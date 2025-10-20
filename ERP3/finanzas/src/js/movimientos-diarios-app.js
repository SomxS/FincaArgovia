// Control de folios:

class MovDiarios extends Complements {

    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    getFolio(Fecha) {

        let data = {
            opc: "initComponents",
            date: Fecha,
        };

        this.sendAjax(data).then((data) => {

            collector = data.folio;
            turnos = data.turnos;
            categoria = data.categoria;
            tipoterminal = data.tipoTerminal;
            terminal = data.terminal;
            checklist = data.checklist;
            sub_cortesias = data.cortesias;

            this.dataInitFolio();

        });
    }

    dataInitFolio() {

        let ticketAbierto = collector;



        if (ticketAbierto) {


            $("#btnApertura").addClass("d-none");
            $("#btnCierre").removeClass("d-none");

            $("#content-folio").html('No: <span class="text-danger">' + ticketAbierto.id + "</span>");
            $("#content-encargado").html('Encargado: <span class="text-info">' + ticketAbierto.encargado + "</span>");


            // limpiar barra de filtro
            $('#contentFilterIngresos').html('');

            // Ingresos:
            ingresos.filtroBusqueda();
            ingresos.lsIngresos();

            //Cortesias:
            cortesia_empleados();
            BuscarCortesias();

            //tarjeta de credito:
            interface_tc();

            // tab subir archivos:
            files.initComponents();


            // cortes diarios:
            turnosCerrados.initComponents();

        } else {

            // Ingresos
            // $('#contentIngresos').Loading();
            $('#contentIngresos').empty_state();
            $('#contentFilterIngresos').html('');

            // Cortesias
            $('#tab-cortesias').empty_state();

            // Tarjeta de credito
            $('#tab-tcx').empty_state();

            // Subir archivos
            $('#tab-files').empty_state();

            $("#btnCierre").addClass("d-none");
            $("#btnApertura").removeClass("d-none");

            $("#content-folio").html("No se ha creado ningun turno");
            $("#content-hora").html("");

            $("#content-ingresos").html("");
            $("#content-encargado").html("");
            $("#tab-turno").html('');

            turnosCerrados.initComponents();



            $("#rptGeneral").empty_state();

        }



    }


    sendAjax(data, div = '') {

        let isFormData = data instanceof FormData;
        let objType = {};
        let datos = {};

        //Tipo de objeto data que se recibe:

        if (isFormData) {

            objType = {
                contentType: false,
                processData: false,
                cache: false,
            };

            datos = data;

        } else {

            datos = {
                opc: 'ls',
                ...data // combina los elementos 
            };

        }

        // verificar before send
        let objBeforeSend = {};
        if (div !== '')
            objBeforeSend = {
                beforeSend: () => {
                    $('#' + div).Loading();
                }
            };

        //Ejecutar ajax con una promesa : 
        return new Promise(function (resolve, reject) {
            // console.log('link', link);

            $.ajax({
                type: "POST",
                url: link,
                data: datos,
                dataType: "json",
                ...objType,
                ...objBeforeSend,
                success: (data) => {
                    resolve(data);
                },
                error: function (xhr, status, error) {
                    swal_error(xhr, status, error);
                },
            });


        });





    }


}


function range_picker_now(id) {
    rangepicker(
        "#" + id,
        true,
        moment(),
        moment(),
        "",
        "",
        function (startDate, endDate) {

            // Consultar los registros del día seleccionado:

            let iptDate = startDate.format("YYYY-MM-DD");
            var fechaActual = new Date().toISOString().split('T')[0];
            finanzas.getFolio(iptDate);



            console.warn('iptDate:', iptDate, 'fechaActual:', fechaActual)
   

        }
    );
}


// Creamos el objecto q tendra sus respectivas pestañas

let json_tab = [
    {
        tab: "Ingresos",
        id: "tab-ingresos",
        fn: 'ingresos.lsIngresos()',


        contenedor: [
            {
                id: "contentFilterIngresos",
                class: "col-12 line mb-3"
            },
            {
                id: "contentIngresos",
                class: "col-12 mt-4"
            }
        ]

    },

    {
        tab: "Cortesias y empleados",
        id: "tab-cortesias",
        fn: 'BuscarCortesias()',
    },

    {
        tab: "Tarjetas de credito",
        id: "tab-tcx",
        fn: 'BuscarTCx()',


    },

    {
        tab: "Subir Archivos",
        id: "tab-files",

        fn: 'files.lsFiles()',
        active: true,// indica q pestaña se activara por defecto

    },


    {
        tab: "CxC",
        id: "tab-cxc",
        fn: 'lsCXC()',

    },

    {
        tab: "Reporte general",
        id: "tab-rpt-gral",
        fn: 'rptGeneral()',


        contenedor: [

            {
                id: "content-rpt-filter",
                class: "col-12"
            },

            {
                id: "content-rpt",
                class: "col-12 line p-3"
            },


        ],
    },

    {
        tab: "Cortes diarios",
        id: "tab-turno",
        fn: 'turnosCerrados.initComponents()',
    },




];


