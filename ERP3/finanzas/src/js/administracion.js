window.ctrl = window.ctrl || 'ctrl/ctrl-administracion.php';

let admin  = {};

let categorias    = [];
let subCategorias = [];
let grupos        = [];
let groupByCategory = [];


$(function () { // init
    
    admin = new Administracion(ctrl, "contentData");
    
    initComponents(ctrl).then((data) => {

        categorias      = data.categorias;
        subCategorias   = data.subcategorias;
        grupos          = data.grupos;
        groupByCategory = data.groupByCategory;

        admin.initComponents();

    });


   
  

});

class Administracion extends Templates {

    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    initComponents() {

  
        // Inicializar variables:
        this.idFilterBar = "filterBarAdmin";

        //inicializar metodos: 
        this.createContent();
       
        this.filterBar();
        admin.listGroupsByCategory(1)
        this.lsSubcategorias();
        
    }


    createContent(){

        let json_components = {
        
            id: "contentAdmins",
            class: " row p-3 line-1 ",
        
            contenedor: [

                {
                    type      : "form",
                    id        : "formAdmin",
                    class     : "col-12 col-lg-4 line pt-2",
                    novalidate: '',
                   
                },
                {
                    type: "div",
                    id: "tableAdmin",
                    class: "col-lg-8 col-12 line",
                
                    
                    children: [
                        {class: "",id:'filterBarAdmin'},
                        { class: "col-12 ", id:'contentTableAdmin'},
                    ]
                },
                
                
            ]
        };


        this.createLayaout({ data: json_components ,design:true});// crear formato de reporte
        this.createFormAdmin();

    }

    createFormAdmin(){

        let formulario = [
            {
                opc: 'select',
                id: 'id_Categoria',
                lbl: 'Categoria:',
                class: 'col-12 mt-2',
                data: categorias,

            },
            {
                opc: 'select',
                id: 'id_grupo',
                lbl: 'Grupo:',
                class: 'col-12 mt-2',
                data: grupos,

            },
            
            {
                opc        : 'input-group',
                id         : 'Subcategoria',
                lbl        : 'Subcategoria:',
              
                class      : 'col-12 mt-2',
                icon       : 'icon-cube',
                align      : 'left',
                placeholder: 'Nombre de la subcategoria',
                // tipo       : 'texto',
                required: true,

            },

            {
                opc        : 'input-group',
                id         : 'tarifa',
                lbl        : 'Tarifa:',
                class      : 'col-12 mt-2',
                icon       : 'icon-dollar',
                placeholder: '0.00',
                tipo       : 'cifra',
                required   : false,

            },


        ];


        $('#formAdmin').simple_json_form({
            data: formulario,
            type: "",           
        });

        $("#formAdmin").validation_form({
            tipo  : 'text',
            activo: 1,
            Stado : 1,
            opc   : 'createSubcategoria',
            }, (datos) => {

            let findSub = subCategorias.some(sub => (sub.valor).toLowerCase() == $('#txtSubcategoria').val().toLowerCase());


                if(findSub){

                    alert({text: 'Ya existe una subcategoria con el mismo nombre', icon: 'error', title: 'Error',timer: 1500});

                }else {

                fn_ajax(datos,this._link).then((data) => {
                    this.lsSubcategorias(); 
                });
                
                
                }

        });
    }

    
    filterBar(opts = {}) {

        const jsonFilterBar = [

            {

                opc: 'select',
                lbl: 'Categoria',
                id: 'categoria',
                class: 'col-12 col-sm-4',
                data: categorias,
                onchange: "admin.listGroupsByCategory(this.value)"

            },

            {
                opc: 'select',
                id: 'id_grupo',
                lbl: 'Grupo:',
                class: 'col-12 col-sm-4 ',
                data: [],
                onchange: "admin.lsSubcategorias()",


            },


            {
                opc: 'select',
                lbl: 'Estado',
                id: 'activo',
                class: 'col-12 col-sm-4',
                onchange: "admin.lsSubcategorias()",

                data: [
                    { id: 1, valor: "Activos" },
                    { id: 0, valor: "Inactivos" },
                ]
            },

            // {
            //     opc: "btn",
            //     fn: "admin.lsSubcategorias()",
            //     text: "Buscar",
            //     class: "col-12 col-sm-3",
            //     color_btn: "secondary",
            // },
        ];


        $('#filterBarAdmin').content_json_form({
            data: jsonFilterBar, 
            type: "",
        });


    }

    

    lsSubcategorias() {

        this.createTable({

            parent: 'contentTableAdmin',
            filterBar:'filterBarAdmin',

            data: {
                opc: 'lsSubcategorias',
            },

            conf: {
                // datatable:false,
              
            },

            attr: {
                color_th: 'bg-primary-1',
                right: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
            },

            extends: false




        });

     
        // this._dataSearchTable = {

        //     tipo: "text",
        //     opc: "lsSubcategorias",
        // };


        // this.searchTable({ id:'contentTableAdmin',datatable: true })

        // .then((data) => {  
        //     $('#contentTableAdmin').rpt_json_table2({data:data});
        //  });

    }

    listGroupsByCategory(idCategoria) {

        let grupos = groupByCategory.filter(grupo => grupo.id_Categoria == idCategoria);
   
        $('#id_grupo').html('');

        grupos.forEach((grupo) => {
            $('#id_grupo').append(`<option value="${grupo.id}">${grupo.valor}</option>`);
        });

        admin.lsSubcategorias();
    }

    disabledSub(id,estatus, event){
        
        let button     = $(event.target).closest("tr").find('td').eq(5).find('button#btnDisabledSub' + id);
        let sub        = $(event.target).closest("tr").find('td').eq(0).text();
        var toggleText = (estatus == 1) ? 'desactivar' : 'activar';

        this.swalQuestion({
            
            opts:{
                title: '¿Deseas ' + toggleText + ' <span class="text-primary"> ' + sub +'</span> ?',
            },
            
            extends:true,
        
        }).then((result) => {

            if (result.isConfirmed) {

                (estatus ==1) ? button.removeClass('btn-outline-success') : button.removeClass('btn-outline-danger');
                (estatus ==1) ? button.addClass('btn-outline-success') : button.addClass('btn-outline-danger');

                fn_ajax({ 
                    
                    opc: 'disabledSub',
                    activo: (estatus == 1) ? 0 : 1,
                    idSubcategoria: id,

                 }, this._link).then((data) => {
                    this.lsSubcategorias();
                });
            }
        });

    }

    getSubcategorias(idCategoria) {
    }


 
}

function lsSub(){
    admin.lsSubcategorias();
}