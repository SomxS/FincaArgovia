let cliente_preferencial;


class Clientes extends Templates {

    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.groups = '';
    }

    render() {

        // this.initComponents();
        // this.groups = grupo;

        this.createLayaoutClientes({ parent: this.div_modulo  });
        this.lsClientes();
        this.btnGroups();
        
    }

    initComponents() {
        fn_ajax({ opc: 'init' }, this._link).then((data) => {

            this.groups = data.groups;
            
        });
    }

    createLayaoutClientes(options) {

        let jsonComponents = {
            id: "contentClientes",
            class: "d-flex mx-3 my-3",
            
            contenedor: [

                {
                    type: "div",
                    class: "col-4 col-lg-5 col-sm-4 me-2  py-2 ", id: '',

                    children: [

                        { class: "col-12 ", id: 'filterClientes' },
                        { class: " my-2 mx-1 ", id: 'listClientes' },

                    ]
                },

                {
                    type: "div",
                    class: "col-8  col-lg-7 col-sm-8", id: '',
                    
                    children: [
                        { class: 'col-12 block fw-bold', text: 'Cliente: ', id: 'lblCliente' },
                        { class: "col-12 block p-3", id: 'filterGroupClientes' },
                        { class: "col-12 block", id: 'listPreferentials' },
                    ]
                },
            ],
        };

        // initialized.
        this.createPlantilla({ data: jsonComponents, parent: options.parent, design: false });
        this.filterClientes();

    }

    filterClientes(){
        $('#filterClientes').content_json_form({
            
            data: [{
                opc:'button',
                id:'btnFilterClientes',
                text:'Agregar Cliente',
                class: "col-sm-4" ,
                className: '',
                onClick: ()=>{ this.addCliente() }

            }],

            type:''
        });
    }


    // Modulos.

    lsClientes() {
        this.createTable({

            parent: 'listClientes',

            data: {
                opc: 'lsClientes',
            },

            conf: { datatable: true, fn_datatable:'data_table_clientes'},
            attr: {
                id: 'tbClientesPreferenciales',
                class: 'table table-bordered table-sm table-hover text-uppercase',

                center: [1]
            },
            extends: false
        });
    }

    lsProducts(id) {

        this.createTable({

            parent: 'listPreferentials',

            data: {
                opc      : 'lsProductsPreferentials',
                idCliente: cliente_preferencial,
                idGroup  : id
            },

            conf: { datatable: true },

            attr: {
                id: 'tbProductos',
                extends: true,
                center: [1, 3]
            },
            extends: false
        });


    }

    btnGroups() {
        this.createButtonGroup({
            fn    : 'clients.initComponents',
            parent: 'filterGroupClientes',
            
            dataEl: { 
               
                data    : subgrupo,
                icon    : 'icon-shopping-basket',
                onClick : 'clientes.lsProducts',
                disabled: true
            
            }
            
        });
    }

    onshowDescuentos(id) {

        const tr                   = $(event.target).closest("tr");
        let   cliente              = tr.find("td").eq(0).text();
        cliente_preferencial       = id;


        let label = $('<label>', { class: 'fw-bold', text: 'Cliente: ' });
        label.append($('<span>', { class: 'text-success text-uppercase', text: cliente }));

        $('#lblCliente').html(label);

        // Iniatilized.
        this.enabledGroupButtons({ parent:'filterGroupClientes'} );
        this.lsProducts(1);


    }


    addCliente(){

        let clientes = [
            {
                opc: "input",
                lbl: "Nombre",
                id: "Name_Cliente",
                class: 'col-12',
                tipo: "texto",
            },

            {
                opc: "input",
                lbl: "Correo",
                id: "Email",
                placeholder: '*No obligatorio',
                class: 'col-12',
                required: false

            },

            {
                opc  : "input",
                lbl  : "Telefono",
                id   : "Telefono",
                class: 'col-6',
                tipo : "cifra",
                required: false

            },

            


            {
                opc  : "input",
                lbl  : "Dias de credito",
                id   : "dias_credito",
                tipo:'cifra',
                class: 'col-6',
                tipo : "cifra",
                required:false
            },

            {
                opc: 'btn-submit',
                class: 'col-12',
                color_btn: 'primary',
                text: 'Guardar',
            }
        
        ];

        this.createModalForm({
            id: 'modal',

            bootbox: { title: 'Nuevo Cliente' },         
            json          : clientes,
            autovalidation: true,
            data: { opc: 'addCliente',estado_cliente:1 },

            success: (data) => {

                alert({ title: 'Se ha creado un nuevo cliente ' });
                
                this.lsClientes();

            }
        });

    }

    onshowEdit(id) {

        const tr          = $(event.target).closest("tr");
        let   nameCliente = tr.find("td").eq(0).text();

        let clientes = [
            {
                opc: "input",
                lbl: "Nombre",
                id: "Name_Cliente",
                class: 'col-12',
                tipo: "texto",
            },

            {
                opc: "input",
                lbl: "Correo",
                id: "Email",
                placeholder: '*No obligatorio',
                class: 'col-12',
                required: false
            },

            {
                opc: "input",
                lbl: "Telefono",
                id: "Telefono",
                class: 'col-6',
                tipo: "cifra",
                required: false

            },




            {
                opc: "input",
                lbl: "Dias de credito",
                id: "dias_credito",
                tipo: 'cifra',
                class: 'col-6',
                tipo: "cifra",
                required: false
            },

            {
                opc: 'btn-submit',
                class: 'col-12',
                color_btn: 'primary',
                text: 'Guardar',
            }

        ];


  

        this.createModalForm({
            id: 'modal',

            bootbox       : { title: nameCliente },          // agregar conf. bootbox
            json          : clientes,
            autovalidation: true,
            data          : { opc: 'newTicket' },

            success: (data) => {

                // alert({ title: 'Se ha creado un nuevo ticket ' });
                // $('#NoFolio').option_select({ data: data.lsFolio });
         

            }
        });


        // initialized. 
    

    }

    // add and updte
    
    setPriceDiscount(event){


        this.useFetch({

            data: { 

                opc       : 'setPriceDiscount',
                idProducto: event.target.id,
                idCliente : cliente_preferencial,
                Costo     : event.target.value
                
            },
        });



    }

    // Operations.

    enabledGroupButtons(options) {

        const buttons = document.querySelectorAll(`#${options.parent} a ` );
      
        buttons.forEach((button, index) => {

                button.classList.remove('disabled');
                button.removeAttribute('disabled');
        
        });

    }

    disabledGroupButtons(options) {

        const buttons = document.querySelectorAll(`#${options.parent} a` );
        const file    = document.getElementById('btnfile');

        buttons.forEach((button, index) => {
            
                // Deshabilitamos todos los demás botones
                
                file.classList.add('disabled');
                button.classList.add('disabled');

                

                button.setAttribute('disabled', 'true');
            
        });

    }


}


// Complementos..
function data_table_clientes(table, no = 15) {
    $(table).DataTable({
        pageLength: no,
        destroy: true,
        searching: true,
        bLengthChange: false,
        bFilter: false,
        order: [],
        bInfo: false,

    });
}