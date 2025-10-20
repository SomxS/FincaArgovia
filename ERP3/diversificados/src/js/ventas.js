class Ventas extends App {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    render() {
        this.layout();
        this.filterBar();
        this.ls();

        this.filterBarTicket();
        this.disabledGroupButtons({ parent: 'filterBox' });
    }

    layout(components) {
        let nameComponent = {
            name: 'container',
            parent: this._div_modulo,
            cardtable: {
                className: 'col-8 line', name: 'containertable',
                filterTable: {},
                listTable: {},


            },
            cardfree: {
            },
        };

        let ui = this.ObjectMerge(nameComponent, components);

        let jsonComponents = {
            id: ui.name,
            class: "d-flex mx-2 my-3 gap-2",

            contenedor: [
                {
                    type: "div",
                    id: ui.cardtable.name,
                    class: ui.cardtable.className,
                    children: [
                        { class: 'col-12 mb-2 line', id: 'filterTable' },
                        { class: 'col-12 line', id: 'listTable' },

                    ]
                },
                {
                    type: 'div',
                    id: 'box',
                    class: 'col-4 ',
                    children: [
                        { class: 'col-12 line mb-2', id: 'filterBox' },
                        { class: 'col-12 line', id: 'containerBox' },

                    ],
                },
            ],
        };

        this.createPlantilla({
            data: jsonComponents,
            parent: ui.parent,
            design: false
        });
    }

    filterBar() {
        const filter = [
            {
                opc  : "input-calendar",
                lbl  : "Buscar por fecha:",
                id   : "iptCalendar",
                class: 'col-sm-4 col-12'
            },

            {
                opc  : "select",
                lbl  : "Seleccionar estados:",
                id   : "status",
                class: 'col-sm-4 col-12',
                data : [
                    {id:0, valor: '-- Ver todos --'},
                    { id: 1, valor: "Pagado" },
                    { id: 2, valor: "Cancelado" },
                ],
                onchange:'ventas.ls()'
            },

            {
                opc      : 'button',
                className: 'w-100',
                class    : 'col-sm-4',
                color_btn: 'outline-primary',
                text     : 'Buscar',
                onClick  : () => { this.ls() },
            },

        ];

        this.createfilterBar({ parent: 'filterTable', data: filter });

        // initialized.
        dataPicker({

            id: 'iptCalendar',


            onSelect: (start, end) => {
                this.ls();
            }

        });
    }

    ls() {
        let rangePicker = getDataRangePicker('iptCalendar');
        this.createTable({
            parent: 'listTable',
            idFilterBar: 'filterTable',
            data: {
                opc: 'lsVentas', fi: rangePicker.fi, ff: rangePicker.ff,
            },
           
            conf: { datatable: true, pag: 15 },
            attr: {
                id: 'tableVentas',
                class: 'table table-bordered table-sm table-striped text-uppercase',
                right: [4],
                center: [1,5,6,7]
            },
        });

        this.initialState();
    }

    // Component ticket.

    filterBarTicket() {
        this.createButtonGroup({
            data: [
                {
                    'icon': 'icon-print',
                    'color': 'outline-primary ',
                    'text': 'Imprimir',
                    onClick: () => { this.printTicket() }
                },

                {
                    icon: 'icon-attach-1',
                    color: 'outline-primary',
                    type: 'file',
                    text: 'Subir factura',
                    accept: 'image/*',
                    id: 'iptFile',

                },

            ], parent: 'filterBox', size: 'sm', cols: 'w-25'
        });

    }

    onShowTicket(id) {

        this.createTable({

            parent: 'containerBox',
            idFilterBar: 'filterBox',
            data: { opc: 'onShowTicket', NoFolio: id },
            conf: {
                datatable: false,
            },

            attr: {
                class: 'table table-sm',
                color_th: 'bg- fw-bold',
                f_size: '12',
                right: [4, 5],
                center: [2, 3]
            },

            success: (data) => { }


        });

        this.enabledGroupButtons({ parent: 'filterBox' });

    }

    addFile() {

        var archivos = document.getElementById("iptFile");
        var archivo = archivos.files;
        let cant_file = archivo.length;

        if (cant_file > 0) {

            var filarch = new FormData();

            for (let i = 0; i < archivo.length; i++) {
                filarch.append("archivo" + i, archivo[i]);
            }

            filarch.append("opc", "addFile");
            filarch.append("idfolio", $('#NoFolio').val());

            $.ajax({

                url: this._link,
                type: "POST",
                contentType: false,

                data: filarch,
                processData: false,
                cache: false,

                success: function (data) {


                    $("#iptFile").val("");

                },
            });

        }

    }

    printTicket() {


        var divToPrint = document.getElementById("containerBox");

        var html =
            "<html><head>" +
            '<link href="https://15-92.com/ERP3/src/plugin/bootstrap-5/css/bootstrap.min.css" rel="stylesheet" type="text/css">' +
            '<link href="https://15-92.com/ERP3/src/css/navbar.css" rel="stylesheet" type="text/css">' +
            '<body style="background-color:white;" onload="window.print(); window.close();  ">' +
            divToPrint.innerHTML +
            "</body></html>"

        var popupWin = window.open();

        popupWin.document.open();
        popupWin.document.write(html);
        popupWin.document.close();


    }

    removeItem(id){

        this.swalQuestion({
            opts: { title: `¿Esta seguro de cancelar ticket ?` },
            data: { opc: "cancelTicket",id:id },
            methods: {
                request: (data) => {
                   this.ls();
                },
            },
        });
    }
   

    // Operations.
    disabledGroupButtons(options) {

        const buttons = document.querySelectorAll(`#${options.parent} a`);
        const file = document.getElementById('btnfile');

        buttons.forEach((button, index) => {

            // Deshabilitamos todos los demás botones

            file.classList.add('disabled');
            button.classList.add('disabled');



            button.setAttribute('disabled', 'true');

        });

    }

    enabledGroupButtons(options) {
        const buttons = document.querySelectorAll(`#${options.parent} a `);
        buttons.forEach((button, index) => {
            button.classList.remove('disabled');
            button.removeAttribute('disabled');
        });
    }

    initialState() {

        // ticket.
        $('#containerBox').empty();
     
        // box.
       
        $('#cardTotal').empty();
    }











}
