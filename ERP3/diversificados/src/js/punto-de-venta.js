
const ctrl          = 'ctrl/ctrl-punto-de-venta.php';
const ctrlVentas    = 'ctrl/ctrl-ventas.php';
const ctrlInventary = 'ctrl/ctrl-inventary.php';
const ctrlPos       = 'ctrl/ctrl-punto-de-venta-pos.php';


let app,pos ,sub, ventas, clientes,products,inventary ;
let grupo, subgrupo, tipo, productos, clients, typeSales, tickets;

/* Init components */
$(function () {
    fn_ajax({ opc: 'init' }, ctrl).then((data) => {
        grupo    = data.grupo;
        subgrupo = data.subgrupo;
        tipo     = data.tipo;
        clients  = data.clients;
        typeSales = data.typeSales;
        tickets = data.tickets;
        // instancias.
        app      = new App(ctrl, 'root');
        sub      = new SubGrupo(ctrl, '');
        // -- 
        products = new Products(ctrl, 'tab-products');
        ventas   = new Ventas(ctrlVentas, 'tab-ventas');
        pos      = new Pos(ctrlPos,'tab-pos');
        clientes      = new Clientes(ctrl,'tab-clientes');
        inventary = new Inventary(ctrlInventary, 'tab-Inventary');

       
        app.initComponents();
        pos.render();
        ventas.initComponents();
        products.initComponents();
        sub.render();
        clientes.render();
        inventary.init();
    });

});


class App extends Templates {
    
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    initComponents() {
        this.render();
    }

    render(){
        this.tabs();
        this.Layaout_ui();
    
    }

    tabs() {

        let jsonTabs = [

            { tab: "Inventarios", id: "tab-Inventary", active: true },
            { tab: "POS", id: "tab-pos"},
            { tab: "Ventas", id: "tab-ventas", fn:'ventas.ls()' },
            { tab: "Productos", id: "pedidos" },
            { tab: "Grupos",id: "tab-subgrupos" },
            { tab: "Clientes Preferenciales", id: "tab-clientes"},
           
        ];

        $("#root" ).simple_json_tab({ data: jsonTabs });
    }

    Layaout_ui(components) {

        let nameComponent = {
           name: 'container',
           parent:'pedidos',
           
           cardwithform:{
                form:'containerForm'
           },

           cardfree: {

               groupbuttons : 'groupButtons',
               containerlist: 'containerProducts',
               endcardfree  : 'containerForm',

           },

        };

        let ui = Object.assign(nameComponent, components);



        let jsonComponents = {
            id: ui.name,
            class: "d-flex mx-2 my-3",


            contenedor: [

                {
                    type      : "form",
                    id        : ui.cardwithform.form,
                    class     : "col-4 col-lg-4 col-sm-4 h-64 line",
                    novalidate: true

                },


                {

                    type: "div",
                    id: "box",
                    class: "col-8 col-lg-8 col-sm-8 me-2 box-panel line",

                    children: [

                        { class: "col-12 items-center line", id: ui.cardfree.groupbuttons },
                        { class: "col-12 block line ", id: ui.cardfree.containerlist },
                    ],

                },




            ],
        };



        this.createPlantilla({ 
            data  : jsonComponents,
            
            parent: ui.parent,
            design: false
        
        });

    }

    LayoutConsulta(components) {
        let nameComponent = {
            name: 'container',
            parent: 'tab-grupo',
            cardtable: { 
                className: 'col-4 col-lg-4 col-sm-4 h-64 line', name: 'containerForm' 
            },
            cardfree: {
                containerlist: { className: 'col-8 col-lg-8 col-sm-8 me-2 box-panel line', name: 'containerProducts' },
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
                },
                {
                    type: "div",
                    id: "box",
                    class: ui.cardfree.containerlist.className,
                    children: [
                        { class: "col-12   ", id: ui.cardfree.containerlist.name },
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


 
    async onshowEditProducts(id) {


        let dataProducts = await useFetch({ url: this._link, data: { opc:'InfoeditProducts',idProduct:id } ,});
  

        this.createModalForm({
            id: 'modal',

            bootbox: { title: '' },          // agregar conf. bootbox
            json: [


                {
                    opc: 'select',
                    id: 'id_subcategoria',
                    lbl: 'Sub-grupo:',
                    class: 'col-12 mt-2',
                    data: subgrupo,
                    value: dataProducts.id_subcategoria,
                },

                {
                    opc: 'input',
                    id: 'NombreProducto',
                    lbl: 'Producto:',
                    required: true,
                    value: dataProducts.NombreProducto,
                    class: 'col-12 mt-2',

                },

                {
                    opc        : 'input',
                    id         : 'presentacion',
                    lbl        : 'Presentacion:',
                    required   : true,
                    value      : dataProducts.presentacion,
                    placeholder: '200 gr example',
                    class      : 'col-6 mt-2',

                },

                {
                    opc        : 'input',
                    id         : 'min_inventario',
                    lbl        : 'Inventario mínimo:',
                    required   : true,
                    placeholder: '',
                    class      : 'col-6 mt-2',
                    value      : dataProducts.min_inventario,


                },

                {
                    opc     : 'input-group',
                    id      : 'precio',
                    lbl     : 'Precio',
                    tipo    : 'cifra',
                    required: true,
                    value   : dataProducts.precio,

                    placeholder: '0.00',
                    class      : 'col-6 mt-2',
                },

                {
                    opc: 'input-group',
                    id: 'precio_mayoreo',
                    lbl: 'Precio Mayoreo',
                    tipo: 'cifra',
                    value: dataProducts.precio_mayoreo,

                    required: true,
                    placeholder: '0.00',
                    class: 'col-6 mt-2',

                },

                {
                    opc: 'input-group',
                    id: 'min_mayoreo',
                    lbl: 'Cantidad de mayoreo:',
                    required: true,
                    value: dataProducts.min_mayoreo,

                    placeholder: '',
                    class: 'col-6 mt-2',

                },
                {
                    opc: 'select',
                    id: 'id_tipo',
                    lbl: 'Tipo',
                    value: dataProducts.id_tipo,
                    data: tipo,
                    class: 'col-6 mt-2 ',

                },




                {
                    opc: 'btn-submit',
                    id: 'Nombre',
                    text: 'Actualizar',
                    class: 'col-12 mt-2',

                },

           ], 
            autovalidation: true,
            data: { opc: 'editProducts', idProducto: id },

            success: (data) => {

                // alert({ title: 'Se ha creado un nuevo ticket ' });
                // $('#NoFolio').option_select({ data: data.lsFolio });


            }
        });


        // initialized. 


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


    // Complements.

    filterSubGroup(){
        let id = $("#id_Categoria").val();

        let Subgrupos = subgrupo.filter((json) => json.id_categoria == id);

        $("#idSubcategoria").option_select({data: Subgrupos});


    }


   



}

class Products extends App{

    render() {
       
        this.formProducts();
        this.lsProducts(1);
        this.btnGroups();

    }

    formProducts() {

        this.createForm({
            parent: 'containerForm', id: 'formProducts',

            json: [

                {
                    opc: 'select',
                    id: 'id_Categoria',
                    lbl: 'Grupo:',
                    class: 'col-12 mt-2',
                    data: grupo,
                    onchange: 'pos.filterSubGroup()'

                },

                {
                    opc: 'select',
                    id: 'idSubcategoria',
                    lbl: 'Sub-grupo:',
                    class: 'col-12 mt-2',


                    //    data: categorias,

                },

                {
                    opc: 'input',
                    id: 'Nombre',
                    lbl: 'Producto:',
                    required: true,
                    class: 'col-12 mt-2',

                },

                {
                    opc: 'input',
                    id: 'Presentacion',
                    lbl: 'Presentacion:',
                    required: true,
                    placeholder: '200 gr',
                    class: 'col-6 mt-2',

                },

                {
                    opc: 'input',
                    id: 'Presentacion',
                    lbl: 'Inventario mínimo:',
                    required: true,
                    placeholder: '',
                    class: 'col-6 mt-2',

                },

                {
                    opc: 'input-group',
                    id: 'Presentacion',
                    lbl: 'Precio Menudeo:',
                    tipo: 'cifra',
                    required: true,
                    placeholder: '0.00',
                    class: 'col-6 mt-2',
                },

                {
                    opc: 'input-group',
                    id: 'Presentacion',
                    lbl: 'Precio Mayoreo',
                    tipo: 'cifra',

                    required: true,
                    placeholder: '0.00',
                    class: 'col-6 mt-2',

                },

                {
                    opc: 'input-group',
                    id: 'Presentacion',
                    lbl: 'Cantidad de mayoreo:',
                    required: true,
                    placeholder: '',
                    class: 'col-6 mt-2',

                },
                {
                    opc: 'select',
                    id: 'Presentacion',
                    lbl: 'Tipo',
                    data: tipo,
                    class: 'col-6 mt-2 ',

                },




                {
                    opc: 'btn-submit',
                    id: 'Nombre',
                    text: 'Guardar:',
                    required: true,
                    class: 'col-12 mt-2',
                    //    data: categorias,

                },

            ],

            data: { opc: 'setProductos' },

            success: (data) => {
                console.log(data);

            }

        });

        this.filterSubGroup();
    }

    lsProducts(idProducts) {

        this.createTable({

            parent: 'containerProducts',

            data: {
                opc: 'lsProducts', id: idProducts,
            },

            conf: { datatable: true, },

            attr: {
                id: 'tableProducts',
                class: 'table table-bordered table-hover table-sm text-uppercase',
                right: [3, 4],
                center: [2]
            },


        });
    }

    btnGroups() {
        this.createButtonGroup({


            parent: 'groupButtons',
            onClick: (event) => { this.lsProducts(event.currentTarget.id) },
            class: 'btn-sm',
            dataEl: {
                data: subgrupo,
                icon: 'icon-shop',

            }
        });
    }



    setEstatus(id) {

        this.toggleEstatus({
            id: id,
            data: { opc: 'setEstatusProducts' }, // nombre del opc del backend.


            success: (data) => { console.log(data) }

        });

    }


}

class SubGrupo extends App{
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    render(){

        this.Layaout_Sub();
        this.lsSubGroups();
        this.forms();
 
    }

    Layaout_Sub(components) {

      
        let jsonComponents = {
            id: 'componentSub',
            class: "d-flex mx-2 my-3",

            // class: "h-full p-2 gap-1 flex",

            contenedor: [

                {
                    type: "form",
                    id: 'formSubGroup',
                    class: "col-4 col-lg-4 col-sm-4 h-64 line",
                    novalidate: true

                },


                {

                    type: "div",
                    id: "box",
                    class: "col-8 col-lg-8 col-sm-8 me-2 box-panel line",

                    children: [

                     
                        { class: "col-12 ", id: 'listSubGroup'},
                    ],

                },




            ],
        };



        this.createPlantilla({ data: jsonComponents, parent: 'tab-subgrupos',design: false});

    }

    forms() {

        this.createForm({
            parent: 'formSubGroup', id: 'forms',

            json: [

                {
                    opc  : 'select',
                    id   : 'id_Format',
                    lbl  : 'Grupo',
                    class: 'col-12 mt-2',
                    data : grupo
                },

           
                {
                    opc     : 'input',
                    id      : 'name_subgroup',
                    lbl     : 'SubGrupo',
                    required: true,
                    class   : 'col-12 mt-2'

                },

                {
                    opc: 'btn-submit',
                    id: 'Nombre',
                    text: 'Guardar:',
                    required: true,
                    class: 'col-12 mt-2',
                    //    data: categorias,

                },

            ],

            data: { opc: 'setSubGroups' },

            success: (data) => {

            }

        });

    }

    lsSubGroups(){

        this.createTable({

            parent: 'listSubGroup',
            data  : {  opc: 'lsSubGroups' },
            conf  : { datatable: false, },

            attr: {
                id   : 'tableSubGroups',
                class: 'table table-bordered table-sm ',
                right: [3, 4],
            },


        });

    }



    


}
