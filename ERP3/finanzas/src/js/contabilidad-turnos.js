class ControlTurnos extends Complements {

    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    initComponents() {
        this.filterTurnos();

        // Especificar datos de consulta:
        this._idFilterBar = "contentfilterTurnos";

        this.attr_table = {
            color_th: 'bg-primary-1',
            color_group: 'bg-disabled2',
            right: [2, 3, 4, 5, 6, 7, 8],
            extends: true,
            f_size: 14
        };

        this.listTurnos();

    }

    filterTurnos() {

        $('#filterTurnos').content_json_form({

            id: "contentfilterTurnos",
            data: this.jsonFilter(), type: ''

        });

    }

    listTurnos() {
        // this.idFilterBar = "contentfilterTurnos";
        this._dataSearchTable = {
            tipo: "text",

            opc: "lsTurnos",
            fecha: $('#iptDate').val(),

        };


        this.searchTable({ id: "contentTurnos" });
    }

    jsonFilter() {

        return [
            {
                opc: "select",
                lbl: "Cortes del día ",
                id: "idFolio",
                tipo: "texto",
                data: turnos,
                class: "col-12 col-md-6 col-lg-3",
                onchange: "ctrlTurnos.listTurnos()",
                selected: '--Selecciona un turno --'
            },

            {
                opc: 'btn',
                id: 'btnImprimir',
                text: 'Imprimir',
                class: 'col-12 col-lg-2 col-sm-3',

                fn: 'turnosCerrados.printCorte()'

            },



        ];
    }

}

class CxC extends Complements {

    constructor(link, div_modulo) {
        super(link, div_modulo);
    }


    async listCxC() {

        const response = await useFetch({
            url: ctrlGastos,
            data: {
                tipo: 'text',
                fecha: $('#iptDate').val(),
                opc: 'lsCxC'
            }
        });


        const mesTexto = this.getMonthText($('#iptDate').val());

        this.createCoffeTable({
            parent: `container-CxC`,
            id: `tbCxC`,
            data: response,
            theme: "corporativo",
            title: "Lista de Cuentas por Cobrar",
            subtitle: `Movimientos de cuentas por cobra realizados en el mes de <b>${mesTexto}</b>`,
            center: [1, 6],
            right: [4],
        });

        simple_data_table(`#tbCxC`, 10);



    }

    getMonthText(fecha) {
        const meses = [
            "enero", "febrero", "marzo", "abril", "mayo", "junio",
            "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"
        ];
        return meses[new Date(fecha).getMonth()];
    }


    newTable(options) {


        this.searchTable({

            id: (options.id) ? options.id : "tableGastos",
            // datatable: true

        });


        // .then((result) => {

        //     $('#'+options.id).rpt_json_table2({ data: result });

        // });



    }



}