class historialPedidos extends Templates {
  
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    openTicket(id) {

        this.createTable({

            parent: 'ContainerFolio',

            data: {
                opc: 'openTicket',
                id: id
            },

            conf: { datatable: false, },

         


        });



    }

    lsHistorial() {

        const fechas = range_date("iptCalendar");

        this.createTable({
            idFilterBar: "filterPedido", // especificar id de la barra de filtros
            parent: "lsHistorial", // especificar donde se imprimira la tabla.
            data: {
                opc: "HistorialPedidos",
                date1: fechas[0],
                date2: fechas[1],
            },

            attr: {
                id: 'tbHistorialPedidos',

                f_size: "14",
                center: [1,3,4],
                extends: true,
            },

            conf: {
             
                // fn_datable: '' // indica si necesitas otra funcion.
            },

            success: (data) => { }, // unicamente si necesitas trabajar con la tabla
        });


    }

    lsEventos() {
        const fechas = range_date("iptCalendar");

        this.createTable({
            idFilterBar: "filterPedido", // especificar id de la barra de filtros
            parent: "lsHistorial", // especificar donde se imprimira la tabla.
            data: {
                opc: "ls",
                date1: fechas[0],
                date2: fechas[1],
            },

            attr: {
                color_th: "bg-primary-1",

                f_size: "16",
                center: [1],
                color_col: [0, 1],
                extends: true,
            },

            conf: {
                datatable: false,
                // fn_datable: '' // indica si necesitas otra funcion.
            },

            success: (data) => { }, // unicamente si necesitas trabajar con la tabla
        });
    }
}




