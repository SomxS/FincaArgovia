const ctrl = "ctrl/ctrl-pedidos.php";
const ctrl_cerrar = "ctrl/ctrl-cerrar-pedidos.php";
const ctrlClientes = "ctrl/ctrl-clientes.php";


let historial, pedidos, pos, cerrar_pedidos, clients, seguimiento_evaluacion;
let category, fols, clientes, products,idFolio,lsFolio;


$(function () {

    pedidos                = new App(ctrl, "root");
    historial              = new historialPedidos(ctrl, "historial_pedidos");
    pos                    = new Pos(ctrlPos, '');

    cerrar_pedidos = new CerrarPedidos(ctrl_cerrar, '');
    clients        = new Clientes(ctrlClientes, 'tab-clientes');
    // seguimiento_evaluacion = new Rosa(ctrl_cerrar, '');

    fn_ajax({ opc: 'initComponents' }, ctrl).then((data) => {

        // vars.
        category = data.lsCategory;
        fols = data.lsFolio;
        clientes = data.lsClientes;
        products = data.lsProducts;


        // init Methods.

        pedidos.initComponents();
        pos.initComponents();
        pos.btnGroups();

        cerrar_pedidos.initComponents();

        clients.render();


    });

});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    initComponents() {
        this.tabPedidos();
        this.createLayaoutHistorialPedidos();
        this.createLayaoutPedido();
        historial.lsHistorial();
    }

    tabPedidos() {

        let jsonTabs = [

            { tab: "Historial Pedidos", id: "historial_pedidos", onClick: 'historial.listHistorial()' },
            { tab: "Realizar Pedidos", id: "realizar_pedidos" },
            { tab: "Cerrar Pedidos", id: "tab-cerrar-pedidos", },
            { tab: "Clientes", id: "tab-clientes", active: true, fn:'clients.lsClientes()' },
        ];


        $("#" + this._div_modulo).simple_json_tab({ data: jsonTabs });
    }

    createLayaoutHistorialPedidos() {
        let jsonComponents = {
            id: "contentPedidos",
            class: "row p-3",
            contenedor: [
                {
                    type: "div",
                    id: "ContainerHistorial",
                    class: "col-lg-7 col-sm-8 col-7",
                    children: [
                        { class: "col-12 line ", id: "filterPedido" },
                        { class: "col-12  mt-2", id: "lsHistorial", text: "listHistorial" },
                    ],
                },
                { type: "div", id: "ContainerFolio", class: "col-lg-5 col-sm-4 col-5" },
            ],
        };

        this.createPlantilla({
            data: jsonComponents,
            parent: "historial_pedidos",
            design: false,
        });

        // extra.
        this.filterBarHistorial();
    }

    filterBarHistorial() {
        const filter = [
            {
                opc: "input-calendar",
                lbl: "Fecha",
                id: "iptCalendar",
                class: 'col-sm-6 col-12'

            },

            {
                opc: 'btn',
                class: 'col-sm-3',
                color_btn: 'secondary',
                text: 'Buscar',
                fn: 'historial.lsHistorial()',

            },

            // {
            //     opc: 'btn',
            //     class: 'col-sm-3',
            //     text: 'Consultar',
            //     fn: 'historial.lsEventos()',
            // },
        ];

        this.createfilterBar({ parent: 'filterPedido', data: filter });

        dataPicker({ parent: 'iptCalendar' });
    }

    // tab pedidos.

    createLayaoutPedido() {

        let jsonComponents = {
            id: "contentPedidos",
            class: "d-flex mx-2 my-3",
            contenedor: [
                
                {
                    type: "div",
                    id: "ContainerFolio",
                    class: "col-4 col-lg-3 col-sm-4 ticket-panel py-2 px-3 line",

                    children: [
                        { class: "col-12  mb-2", id: "filterTicket" },
                        { class: "col-12  mb-2 ", id: "btnGroups" },
                        { class: "col-12  flex-grow-1  mt-2 ", id: "containerTicket" },
                        { class: "col-12  py-2 mt-auto", id: "containerTotal" },
                    ],


                },
                
                {
                
                    type: "div",
                    id: "listBox",
                    class: "col-8  col-lg-9 col-sm-8 me-2 box-panel  line",

                    children: [

                        { class: "col-12  ", id: "groupButtons" },
                        { class: "col-12 text-end mb-2 d-flex  ", id: "searchBox" },
                        { class: "col-12 block box-content", id: "containerBox" },
                    ],

                },

               
            ],
        };



        this.createPlantilla({ data: jsonComponents, parent: "realizar_pedidos", design: false });
        this.filterBarPedidos();
        this.filterTicket();
    }

    filterBarPedidos() {

        const filter = [

            {
                id     : "btnCerrarCuenta",
                text   : "Terminar",
                icon   : 'icon-truck',
                color  : 'outline-primary',
                onClick: () => { pos.cerrarTicket() },
                class  : 'col-sm-6 col-12'
            },

            {
                color  : 'outline-danger',
                class  : 'col-sm-6 col-12',
                icon   : ' icon-cancel-circle',
                onClick: () => { pos.cancelarTicket() },
                text   : 'Cancelar'
            }

        ];
       

        this.createButtonGroup({ parent: 'containerTotal', data: filter, size: 'sm', cols: 'w-50' });



        // add search filter.

        this.createfilterBar({
            parent: 'searchBox', data: [
                {
                    opc: 'input-group',
                    class: 'form-control',
                    id: 'searchInput',
                    class: 'col-3 w-100',
                    icon: 'icon-search',
                    placeholder: 'Buscar producto',
                    onkeyup: 'pedidos.searchProdcts()'

                }
            ]
        });


    }

    searchProdcts() {
        const busqueda = $('#searchInput').val();
        const products = document.querySelectorAll('.grid-container .grid-item-soft');

        products.forEach(function (product) {
            const productName = product.textContent.toLowerCase();
            if (productName.includes(busqueda)) {

                product.classList.remove('d-none');
            } else {
                product.classList.add('d-none');
            }
        });


    }

    filterTicket() {

        const filter = [

            {
                opc: "btn-select",
                id: "NoFolio",
                class: 'col-12 ',
                fn: 'pos.openTicket()',
                icon: 'icon-search',
                data: fols,
                selected: '-- Elige un folio para continuar--'
            },

            // {
            //     opc: "btn",
            //     id: "btnAgregar",
            //     color_btn: 'outline-warning',
            //     icon: "icon-user-3",
            //     fn: 'pos.modalNuevoTicket()',
            //     class: 'col-12 col-lg-4 col-sm-3'
            // },




        ];

        this.createfilterBar({ parent: 'filterTicket', data: filter ,id:'frm'});

        const group = [
            {
                'icon': 'icon-user-3',
                'color': 'outline-success ',
                onClick: () => { pos.modalNuevoTicket() }
            },
            {
                'icon' : 'icon-print',
                'color': 'outline-primary ',
                onClick: () => { pos.printTicket() }
            },
            
            {
                icon    : 'icon-attach-1',
                color   : 'outline-primary',
                type    : 'file',
                accept  : 'image/*',
                id      : 'iptFile',
                fn: () => ' pos.addFile()' 
            },

            {
                icon: 'icon-comment-7',
                color: 'outline-primary ',
                onClick  : ()=>{pos.addComments()}
            },

            {
                icon: 'icon-flash',
                color: 'outline-primary ',
                onClick: () => { pos.addComments() }
            },



        ];

        this.createButtonGroup({ parent: 'btnGroups', data: group, size: 'sm', cols: 'w-10' });

        $('#frm label').remove();

    }

   // end tab pedidos.




}





