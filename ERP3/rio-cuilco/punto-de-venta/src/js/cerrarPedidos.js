class CerrarPedidos extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    initComponents() {
        this.createLayaoutCerrarPedidos();
        this.lsPedidos();
    }



    // tab cerrar pedidos.

    createLayaoutCerrarPedidos() {

        let json_components = {
            id: "component-cerrar-pedidos",
            class: "card-body row m-2",

            contenedor: [
                {
                    type: "div",
                    id: "filter-bar-cerrar-pedidos",
                    class: " col-lg-12 bg-orange pt-2",
                    novalidate: true,
                },

                {
                    type: "div",
                    id: "content-list-pedidos",
                    class: "col-lg-12 block ",
                },
            ]
        };


        var opts = { parent: 'tab-cerrar-pedidos', data: json_components, design: true };
        this.createLayaout(opts);

        // add components.
        this.filterBarCerrarPedidos();

    }

    filterBarCerrarPedidos() {

        const filter = [


            {
                opc      : "button",
                id       : "btnConsulta",
                text     : "Consultar ",
                color_btn: 'outline-primary',
                icon     : 'icon-print',
                className: 'w-100  btn-sm ',
                fn       : 'cerrar_pedidos.lsPedidos()',
                class    : 'col-sm-2 col-12 '
            },

            {
                opc      : "button",
                id       : "btnCerrar",
                text     : "Terminar",
                icon: 'icon-print',

                className: 'w-100 btn-sm ',
                color_btn: 'outline-success',
                fn       : 'cerrar_pedidos.cerrarPedidos()',
                class    : 'col-sm-2 col-12'
            },
        
            {
                opc      : 'button',
                color_btn: 'secondary',
                className: 'w-100 btn-sm ',

                class    : 'col-sm-2 col-12',
                fn       : 'pos.cerrarTickets()',
                text     : 'Pedidos',
                icon     : 'icon-truck'
            }

        ];

      

        this.createfilterBar({ parent: 'filter-bar-cerrar-pedidos', data: filter });


    }

    filterBar() {
        const filter = [
            {
                opc: "input-calendar",
                lbl: "Fecha",
                id: "iptCalendar",
                class: 'col-sm-4 col-12'
            },


            {
                opc: 'btn',
                class: 'col-sm-3',
                color_btn: 'secondary',
                text: 'Buscar',
                fn: '',
            },

        ];

        this.createfilterBar({ parent: 'filterBar', data: filter });


    }

    // interfaz.

    lsPedidos(){
        this.createTable({

            parent: 'content-list-pedidos',

            data: {
                opc: 'lsPedidos',
            },

            conf: { datatable: false, },

            attr: {
                class: 'table table-bordered text-uppercase table-sm',
                center: [2, 3, 4],
                id:'tbcerrarPedidos'
            },

            extends: false




        });
    }

    cerrarPedidos(){
        this.swalQuestion({

            opts: {
                text: '¿Deseas terminar la lista de pedidos ?',
            },
            data: {
                opc: 'cerrarPedidos',
            },
            methods: {
                request: (data) => {

           

                }
            }

        });

    }















}