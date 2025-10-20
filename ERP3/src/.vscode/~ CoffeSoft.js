class Complements {

    constructor(link, div_modulo) {
        this._link = link;
        this._div_modulo = div_modulo;
    }
//    HOLA MUNDO
    
    /* Plantilla para crear Layaouts */
    
    createLayaout(options = {}) {
        const defaults = {
            design: true,
            content: this._div_modulo,
            parent: '',
            data: { id: "rptFormat", class: "col-12" },
        };

        const opts = Object.assign({}, defaults, options);
        const lineClass = opts.design ? ' block ' : '';

        const div = $("<div>", {
            class: opts.data.class,
            id: opts.data.id,
        });

        const row = opts.data.contenedor;

        row.forEach(item => {
            let div_cont;

            switch (item.type) {

                case 'div':
                    
                    div_cont = $("<div>", {
                        class: (item.class ? item.class : 'row') + ' ' + lineClass,
                        id: item.id,
                    });

                    if (item.children) {
                        item.children.forEach(child => {
                            child.class = (child.class ? child.class + ' ' : '') + lineClass;
                            div_cont.append($("<div>", child));
                        });
                    }

                    div.append(div_cont);

                break;

                case 'hr':
                    div_cont = $("<hr>");
                    div.append(div_cont);
                break;

                default:
                    div_cont = $("<" + item.type + ">", item);
                    div.append(div_cont);
                break;
            }
        });


        if(!opts.parent){
            $("#" + opts.content).html(div);
        }else{
            $("#" + opts.parent).html(div);
        }

    }


    /*
     plantilla para activar un sweet alert
    */ 

     swalQuestion( options = {}){

     
         /*--  plantilla --*/
         
         let objSwal = {
             title: "",
             text: " ",
             icon: "warning",
    
             showCancelButton: true,
             confirmButtonText: "Aceptar",
             cancelButtonText: "Cancelar",
             ...options.opts
         };

    
         var defaults = {

            data   : { opc: "ls" },
            extends: false,
            fn     : '',
            
            ...options,

             methods: ''

         };
         
         let opts = Object.assign(defaults, options);

         
         let extends_swal = Swal.fire(objSwal);

         

          if (options.extends){
             
            return extends_swal;

          }else{

            extends_swal.then((result) => {

                if (result.isConfirmed) {


                    fn_ajax(opts.data, this._link, "").then((data) => {

                        if (opts.fn) {
                            window[opts.fn]();
                        
                        } else if(opts.methods) {
                            // Obtener las llaves de los métodos
                            let methodKeys = Object.keys(opts.methods);
                            methodKeys.forEach((key) => {
                                const method = opts.methods[key];
                                method(data);
                            });
                           
                        }


                    });
                }
            });



          }


   

     }

    createDivs(options = {}) {

        var defaults = {
            data: { id: "rptFormat", class: "col-12" },
           
           
            div_content: this._div_modulo,

        };

        var opts = Object.assign(defaults, options);

        var div = $("<div>", {
            class: opts.data.class,
            id: opts.data.id,
        });

        let row = opts.data;


        for (const x of row.contenedor) {

            var div_cont = $("<div>", {
                class: x.class,
                id: x.id,
            });

            div.append(div_cont);
        }



        $("#" + opts.div_content).html(div);
    }

    /*getter and setter variables */
    static _data_modal = {};
    /*getter and setter metodos*/
    set data_modal(valor) {
        this._data_modal = valor;
    }
    set attr_content_table(valor) {
        this._attr_content_table = valor;
    }


    // Metodo para un lsSearchFilter :
    static _dataSearchTable = {};
    _idFilterBar             = 'filterBar';


    set dataSearchTable(valor) {
        this._dataSearchTable = valor;
    }
    
    set idFilterBar(valor) {
        this._idFilterBar = valor;
    }

  
    searchTable(options = {}) {

        var defaults = {
            tipo: "text",


        };

        var opts        = Object.assign(defaults, this._dataSearchTable);
        var extendsAjax = null; // extender la funcion ajax 

        $("#"  + this._idFilterBar).validar_contenedor(opts, (datos) => {


           extendsAjax = fn_ajax(datos, this._link, "#" + options.id); 
           
            if (!options.extends) { // si la variable extends no esta definida se ejectuta de forma normal
                
                extendsAjax.then((data) => {
                
                    let attr_table_filter = {
                        data  : data,
                        f_size: '12',
                        id    : 'tbSearch'
                    };

                    attr_table_filter = Object.assign(attr_table_filter, this._attr_table);
                    $("#" + options.id).rpt_json_table2(attr_table_filter);

                    if(options.datatable)
                        simple_data_table_no("#" + attr_table_filter.id, 10);
                
                });
            
            }
          
        });


        if (options.extends) {
            return extendsAjax;
        }


    }




    sendAjaxx(data, div = '') {

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

        
        if (div !== '') objBeforeSend = { beforeSend: () => $('#' + div).Loading(this.objLoading) };

        //Ejecutar ajax con una promesa : 
        return new Promise(function (resolve, reject) {
            console.log('link', this.link);

            $.ajax({
                type: "POST",
                url: this._link,
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


     


    // Crear un metodo AJAX :

    _data = {};

    set data(valor) {
        this._data = valor;
    }
    
    sendAjaxa(options = {}){


        var defaults = {
            id: 'content-data',
            table: false,
            fn: '',   
        };

        var opts = Object.assign(defaults, options);

        
        let content = '';
        if(opts.id)
            content = '#'+opts.id;

        fn_ajax(this._data, this._link, content).then((data) => {
            
            if (opts.table)
                this.lsTable();

            if(opts.fn)
                window[opts.fn]();


        });
    }

    // Crear metodo para un modal form :




    modal_formulario(options = {}) {


        let json_default = [

            {
                opc: "input-group",
                lbl: "Nombre",
                id: "name",
                tipo: "cifra",
                required: true,
                placeholder: "Agrega un dato",
                icon: "icon-phone-2",
            },

        ];

       

        /*--  Asignamos valores por defecto --*/

        let defaults = {
            id_frm        : 'frm-modal',
            id_content    : 'content-modal',
            autovalidation: true,
            json          : json_default,
            fn            : '',
            extends       : false,

        };

       

        var opts = Object.assign(defaults, options);

        /*-- Creamos el objeto de configuracion del modal --*/

        let data_modal = {

            title: "<strong> </strong>",
            closeButton: true,
            message: `<form class="" id="${opts.id_frm}"  novalidate></form> <div id="${opts.id_content}"></div>`,

        };

        data_modal = Object.assign(data_modal, this._data_modal);

        let modal = bootbox.dialog(data_modal);

     
        /*-- Asignar parametros al plugin simple json form --*/
        let data_json_form = {
            data: opts.json,
            type_btn: "two_btn",
            prefijo: "txt",
        };


        if(opts.extends){
            $("#" + opts.id_frm).content_json_form(data_json_form);
        }else{

            $("#" + opts.id_frm).simple_json_form(data_json_form);
        }



        if (opts.autovalidation) {

            let options_validation = {
                tipo: "text",
                opc: "save-frm",
            };

            options_validation = Object.assign(options_validation, this._data_form);

            $("#" + opts.id_frm).validation_form(options_validation, (datos) => {



                fn_ajax(datos, this._link, '').then((data) => {

                    if(opts.fn)
                        window[opts.fn]();

                    modal.modal('hide');

                });



            });


        } else {
            return modal;
        }



    }


    // -- Estructura basica para una tabla --
    _id_table         = 'mdl-table';
    _id_content_table = 'content-table';

    static _attr_table = {};
    static _data_table = {};
    
    
    static _attr_content_table = {};
    static _opts_table = {};

    set attr_table(valor) {
        this._attr_table = valor;
    }

    set id_table(valor) {
        this._id_table = valor;
    }

    set id_content_table(valor) {
        this._id_content_table = valor;
    }

    set data_table(valor) {
        this._data_table = valor;
    }

    set opts_table(valor) {
        this._opts_table = valor;
    }

    

    lsTable(options = {}) {

        var defaults = {

            id         : this._id_content_table,
            extends_fn : function() {},
            datatable  : true,
            no         : 15,
            data_events: "",
        };

        let opts = Object.assign(defaults, this._opts_table, options);

        /* --   Configuracion de la data para backend  -- */

        let data = { // data por default
            opc: "ls",
        };

        data = Object.assign(data, this._data_table);

        // console.log(data);



        // --  Proceso para crear la tabla a partir del backend-- */

        fn_ajax(data, this._link, "#" + opts.id).then((datos) => {

            let atributos_table = {
                data: datos,
                f_size: '12',
                color_th: "bg-primary-1 bg-primary",
                id: this._id_table,
            };

            atributos_table = Object.assign(atributos_table, this._attr_table);

            // console.log('atributos', atributos_table);
            $("#" + opts.id).rpt_json_table2(atributos_table);

            // Asignar eventos a la tabla
            if(opts.data_events) // se activa al crear un objecto data_events
            eventoTabla('#' + this._id_table, this._link, opts.data_events);


            if (opts.datatable) // se activa con la opcion datatable
                simple_data_table_no("#" + atributos_table.id, opts.no);

            // if (opts.fn)
                opts.extends_fn();


            // eventoInput('mdl-table');
        });
    }

    /*--  Estructura basica para una question  --*/

    static _data_question = {};
    static _attr_question = {};

    static _dataQuestion  = {};
  


    set data_question(valor) {
        this._data_question = valor;
    }

    set dataQuestion(valor){
       this._dataQuestion = valor;
    }

    set attr_question(valor) {
        this._attr_question = valor;
    }

    modal_question(options = {}) {

        /*--  Asignamos valores por defecto --*/
        var defaults = {
            fn: '',
            extends:false
        };

        let opts = Object.assign(defaults, options);


        let dtx = { // data por default
            opc: "question",
        };

        if (this._data_question) // si se seteo un nuevo valor se reemplaza
        {
            dtx = this._data_question;
        } else if (this._dataQuestion){
            dtx = this._dataQuestion;
        }   
            



        /*--  atributos para un modal --*/

        let object_alert = {
            title: "",
            text: " ",
            icon: "warning",

            showCancelButton: true,
            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar",
        };

        object_alert = Object.assign(object_alert, this._attr_question);


        /*--  plantilla --*/


        let extends_swal =  Swal.fire(object_alert);

        if(opts.extends){
            return extends_swal;


        }else{

        }

        
        extends_swal.then((result) => {
            if (result.isConfirmed) {


                fn_ajax(dtx, this._link, "").then((data) => {

                    if (opts.fn){

                        window[opts.fn]();
                    }else{

                        // this.lsTable();
                    }


                });
            }
        });
    }


    // -- Estructura para un modulo de consulta

    _idContentForm    = 'contentForms';
    _idContentTable   = 'contentTable';

    _idForm           = 'idFrm';
    _idTable          = 'idTable';

    _dataPluginForm   = {};

    static _attrContentForm   = {};
    static _attrContentTable  = {};


    set idContentForm(valor) {
       this._idContentForm = valor;
    }

    set idContentTable(valor) {
        this._idContentTable = valor;
    }

    set attrContentForm(valor) {
        this._attrContentForm = valor;
    }

    set attrContentTable(valor) {
        this._attrContentTable = valor;
    }

    set idForm(valor) {
        this._idForm = valor;
    }

    set dataPluginForm(valor) {
        this._dataPluginForm = valor;
    }



    jsonDefault = [
        {
            opc  : "input",
            lbl  : "Producto",
            id   : "producto",
            tipo : "texto",
            class: 'col-12'
        },

        {
            opc        : "input-group",
            lbl        : "Cantidad",
            id         : "cantidad",
            placeholder: "0.00",
            tipo       : "cifra",
            class      : 'col-12'
        },


    ];

    formTable(options = {}) {

        /* --  Configuracion inicial del modulo -- */

        let defaults = {

            class_content: "row p-2",
            id_frm       : this._idForm,
            id_tabla     : this._idContentTable,

            datatable     : true,
            autovalidation: true,
            alert         : true,
            toogle_close  : false,
            data_alert    : { title: 'Registro guardado con exito' },
            fn            : ''
        };

        var opts = Object.assign(defaults, options);

        let div = $("<div>", {
            class: opts.class_content,
        });


        //  Contenedor para el div del formulario :
    
        let attrDivForm = {
            class: "col-lg-4 col-12 line",
            id: this._idContentForm,
        };

       
        if (this._attrContentForm) // Si fue cambiado los atributos el nuevo atributo cambia el formulario
            attrDivForm = Object.assign(attrDivForm, this._attrContentForm);


        let div_form = $("<div>", attrDivForm);

          
        let div_formulario = $("<form>",{
            id: opts.id_frm,
            novalidate: true,
        });



        // // let div_close      = $('<div>', {
        // //     class: "col mb-3  float-end",
        // // }).append('<label>',{
        // //     class:'fw-bold'
        // // });

        // // let btn_close = $('<button>', {
        // //     onclick: 'closeForm()',
        // //     class: 'btn-close',
        // //     type: 'button',
        // // });

        // // div_close.append(btn_close);

        // // if (opts.toogle_close)
        // //     div_formulario.append(div_close);

        div_form.append(div_formulario);
        div.append(div_form);



        // Contenedor para el div de la tabla :

        let opts_content_table = {
            class: "col-12 col-lg-8 line",
            id: this._idContentTable,
        };

        opts_content_table = Object.assign(opts_content_table, this._attrContentTable);
        let div_table = $("<div>", opts_content_table);
        div.append(div_table);




        
        //-- Vaciar los contenedores -- */
        $("#" + this._div_modulo).html(div);




       // Proceso para crear un formulario :

       let opc_json_form = {
           data: this.jsonDefault,
           type: "default",
       };
       opc_json_form = Object.assign(opc_json_form, this._dataPluginForm);
      



        // Asignar los plugins a los elementos:
        $("#" + opts.id_frm).content_json_form(opc_json_form);
       



        if (opts.autovalidation) {

        //     let data_validation_form = {
        //         tipo: "text",
        //         opc: "save-frm",

        //     };

        //     data_validation_form = Object.assign(data_validation_form, this._data_form);


        //     $("#" + opts.id_frm).validation_form(data_validation_form, (datos) => {


        //         fn_ajax(datos, this._link, "").then((data) => {
        //             $("#" + opts.id_frm)[0].reset();

        //             if (opts.alert)
        //                 alert(opts.data_alert);

        //             this.lsTable();
        //         });
        //     }
        //     );


        }
    }


}

class Templates extends Complements{

    constructor(link, div_modulo) {
        super(link, div_modulo);
        this._table = null;
    }

    set table(valor){
        this._table = valor;  
    }
    get table(){
         return $(this._table);  
    }

    createTable(options) {1

        var defaults = {

            extends: false,
            parent: this.div_modulo,
            idFilterBar: '',

            parent: 'lsTable',

            conf:{
                datatable :true,
                fn_datatable:'simple_data_table',
                beforeSend:true,
            },

            methods: {
                send: (data) => {  }
            }

            

        };

             // configurations.
        const dataConfig = Object.assign(defaults.conf,options.conf); 


        let opts = Object.assign(defaults, options);
        const idFilter = options.idFilterBar ? options.idFilterBar : '';

 

        if (idFilter) { // se activo la validacion por filtro 

            const sendData = { tipo: 'text', opc: 'ls', ...options.data };
            var extendsAjax = null; // extender la funcion ajax 


            $(`#${idFilter}`).validar_contenedor(sendData, (datos) => {

                // console.log('opts', dataConfig);

                let beforeSend = (dataConfig.beforeSend) ?  '#' + options.parent : '';

                extendsAjax = fn_ajax(datos, this._link, beforeSend);


                if (!options.extends) { // si la variable extends no esta definida se ejectuta de forma normal


                    extendsAjax.then((data) => {

                        let attr_table_filter = {
                            data  : data,
                            f_size: '14',
                            id    : 'tbSearch'
                        };

                        attr_table_filter = Object.assign(attr_table_filter, opts.attr);

                        opts.methods.send(data);

                      
                    
                        $('#' + options.parent).rpt_json_table2(attr_table_filter);

                        if (dataConfig.datatable){
                            window[dataConfig.fn_datatable]('#' + attr_table_filter.id, 15);
                        }

                    });


                }


            });

            if (opts.extends) {
                return extendsAjax;
            }

            





        } else {

            let sendData = {
                opc: 'ls',
                ...opts.data
            };
            
       

            extendsAjax = fn_ajax(sendData, this._link, '#' + opts.parent);


            if (!opts.extends) { // si la variable extends no esta definida se ejectuta de forma normal


                extendsAjax.then((data) => {

                    opts.methods.send(data);

                    this.processData(data, opts, dataConfig);

            
                });


            }



        }





    }

    createForm(options) {
        // Conf:
        let defaults = {
            parent: 'formsContent',
            id: 'idForm',
            plugin: 'content_json_form',
            plugin_validation: 'validation_form',
            extends: false,
            methods:{
               send:(data = '')=>{console.warn('Execute server ', data)} 
            },
        };
        
        let formulario = [
            {
                opc: "input",
                lbl: "Producto",
                class: 'col-12'
            },

            {
                opc: "btn-submit",
                id: "btnEnviar",
                text:'Guardar',
                class: 'col-12'
            },


        ];

        
     

        // Reemplazar formulario:
        const jsonForm = options.json || formulario;
        // Fusionar opciones con valores por defecto
        const opts = Object.assign(defaults,options);
        opts.methods = Object.assign({}, defaults.methods, options.methods); // Asegurar que los métodos personalizados se fusionen correctamente



        $('#' + opts.parent)[opts.plugin]({ data: jsonForm, type: 'default' });

        let dataForm = {
            tipo: 'text',
            opc: 'set',
            ...options.data
        };
        
        var extends_ajax;

        $("#" + opts.parent).validation_form(dataForm, (datos) => {

           extends_ajax = fn_ajax(datos, this._link, '');

            if(!opts.extends){

                extends_ajax.then((data) => {

                    // $("#" + opts.parent)[0].reset();
                    
                    opts.methods.send(data);
                    
                });
                
            }


        });
        // return extends_ajax;
        // if(opts.extends){
        //     return extends_ajax;
        // }



    }

    createPlantilla(options) {

        let json_components = {
            id: "mdlGastos",
            class: "card-body row m-2",

            contenedor: [
                {
                    type: "form",
                    id: "formGastos",
                    class: " col-lg-4  block pt-2",
                    novalidate: true,
                },

                {
                    type: "div",
                    id: "contentGast",
                    class: "col-lg-8 ",
                    children: [
                        { class: 'col-12', id: 'filterGastos' },
                        { class: 'col-12', id: 'tableGastos' }
                    ]
                },
            ]
        };


        var defaults = { data: json_components, design: true };
        let opts = Object.assign(defaults, options);
        this.createLayaout(opts);

    }

    createModalForm(options){

        const idFormulario = options.id ? options.id : 'frmModal';

        const components = options.components 
        
        ? options.components 
        : $("<form>", { novalidate: true, id: idFormulario, class: "" });
  
        
        
        let defaults = {
            id: idFormulario,

            bootbox :{

                title      : 'Modal example',
                closeButton: true,
                message    : components,

            },

            

            json: [
                {
                    opc        : 'input-group',
                    class      : 'col-12',
                    label      : 'Nombre'
                },
                {
                    opc: 'btn-submit',
                    text: 'Guardar',
                    class: 'col-12'
                }
            ],

            autovalidation:false,
         
            data: { opc: 'sendForm' }

        };

        const conf  = this.ObjectMerge(defaults,options);
        let   modal = bootbox.dialog(conf.bootbox);
       
     
        $('#'+conf.id).content_json_form({data:conf.json, type:''});

        if(options.beforeSend)
            options.beforeSend();


        if (conf.autovalidation) {

            let options_validation = {
                tipo: "text",
                opc: "save-frm",
            };

            options_validation = Object.assign(options_validation, conf.data);

         
            $("#" + conf.id).validation_form(options_validation, (datos) => {

                fn_ajax(datos, this._link, '').then((data) => {

                    options.success(data);

                    // if (conf.success)
                    //     window[conf.success](data);

                    modal.modal('hide');

                });



            });


        } else {
            return modal;
        }
        
        
        // return modal;
       
     
        

    }

    createButtonGroup(options) {


        let groups = {

            parent: 'groupButtons',
            cols: 'w-25',

            data: [{
                text: 'FRANCES',
                color: 'success',
                icon: 'icon-shop',
                onClick: '',
                id: '',

            },
            {
                text: 'PASTELERIA',
                color: 'success',
                icon: 'icon-shop',

                onClick: '',

            },
            {
                text: 'BIZCOCHO',
                color: 'success',
                icon: 'icon-shop',
                onClick: '',

            }
            ]

        };


        let configuration = Object.assign(groups, options);

        // align - items - center  justify - content - center

        let divs = $('<div>', { class: 'd-flex overflow-auto ' });


        // Iterate over the group data and create buttons

        if (!configuration.dataEl) {
            configuration.data.forEach((item) => {


                let btn = $('<a>', {
                    class: `btn btn-outline-primary  ${configuration.cols} me-1 d-flex flex-column align-items-center justify-content-center`, // Add dynamic color class
                    id: item.id,
                    onclick: item.onClick // Use 'click' instead of 'onClick' for jQuery event binding
                });


                var itemIcon = item.icon ? item.icon : '';


                let icon = $('<i>', { class: 'mt-2  d-block ' + itemIcon });

                let span = $('<span>', { text: item.text });


                btn.append(icon, span);



                divs.append(btn);
            });
        } else {

            configuration.dataEl.data.forEach((item) => {


                var fn = configuration.dataEl.onClick 
                    ? `${configuration.dataEl.onClick}( ${item.id} ) `
                    : `${configuration.dataEl.fn}`


                let btn = $('<a>', {
                    class: `btn btn-outline-primary ${configuration.cols} d-flex me-1 flex-column w-100align-items-center justify-content-center`, // Add dynamic color class
                    id: item.id,
                    onclick: item.onClick ? item.onClick : fn
                });


                console.log(item.icon);


                var itemIcon = configuration.dataEl.icon ? configuration.dataEl.icon : '';


                let icon = $('<i>', { class: 'ms-2  d-block ' + (item.icon ? item.icon : itemIcon) });

                let span = $('<span>', { text: item.valor });

                // if(item.id){

                    btn.append(icon, span);
                // }else{
                //     btn.append(span);

                // }



                divs.append(btn);
            });


        }


        if (groups.parent) {

            $('#' + groups.parent).html(divs);
        } else {

            return divs;
        }


    }

    createGrid(options){

        let defaults = { 
        
            parent: '',
            color : 'bg-default',
            data  : [],
            size  : 'soft',
            type  : ''
       
        }; 

        let opts = Object.assign(defaults, options);
        let divs = $('<div>', { class: 'grid-container' });


        opts.data.forEach((element) => {

            
            
            if(opts.type == 'catalog'){
                
                var img = "https://15-92.com/ERP3/src/img/default_flower.png";
                
                var grid_item = $('<div>', { class: ` ${opts.color} grid-item  `, onClick: element.onclick });

                var link = (element.attr.src) ?  element.attr.src : img;
                
                var imagen = $('<img>', { src: link , class: 'col-12' });

                // add image.
                var details = $('<div>', { class: 'col-12 div1 pointer' }).append(imagen);

                
                // add text. 
                var description = $('<div>', { class: 'col-12 bg-primary d-flex flex-column pt-1 div2 pointer' });
                var h6       = $('<label>', { text: element.nombre, class: 'fw-bold col-12' });
                var sub      = $('<sub>', { text: element.costo, class: 'fw-bold ' })
                description.append(h6,sub);


                // draw grid items.
                grid_item.append(details, description);
                
                
                
                
            }else{
                
                    var grid_item = $('<div>', { class: ` ${opts.color} grid-item-${opts.size}  `, onClick: element.onclick });


                    // var urls = element.attr.src ? element.attr.src : '';

                    // add cost.

                    var details = $('<div>', { class: 'col-12 pointer' });
                    var lbl = $('<label>', { text: element.costo, class: 'col-12 fw-600' });


                    details.append(lbl);

                    // add text. 
                    var description = $('<div>', { class: 'col-12  d-flex flex-column pt-1 div1 pointer' });
                    var label = $('<label>', { text: element.nombre, class: 'fw-bold col-12' });

                    description.append(label);

                    // draw grid items.
                    grid_item.append(details, description);


        }

            divs.append(grid_item);

            
        });

        $('#'+opts.parent).html(divs);

    }


    createfilterBar(options) {

        const defaults = {
            parent: 'filterBar',
            data: [{ opc: 'btn', text: 'Guardar' }]
        };

        const opts = Object.assign(defaults, options);


        $(`#${opts.parent}`).content_json_form({ data: opts.data, type: '' });


    }

    fnAjax(options) {

        let defaults = {

            idFilterBar: 'filter',

            data: {
                tipo: 'text',
                opc: 'frm-data',
            },

            methods:''




        };

        const settings = this.ObjectMerge(defaults, options);

        console.warn(settings.data);


        $("#" + settings.idFilterBar).validar_contenedor(settings.data, (datos) => {
          
            fn_ajax(datos, this._link).then((data) => {


                if (settings.methods) {
                    // Obtener las llaves de los métodos
                    let methodKeys = Object.keys(settings.methods);
                    methodKeys.forEach((key) => {
                        const method = settings.methods[key];
                        method(data);
                    });

                }




            });

        });
    }




    // Sugerencia de gpt.

    ObjectMerge(target, source) {
    // Iterar sobre todas las claves del objeto fuente
    for (const key in source) {
        // Verificar si la propiedad es propia del objeto fuente
        if (source.hasOwnProperty(key)) {
            // Verificar si el valor es un objeto y si el target tiene la misma propiedad
            if (typeof source[key] === 'object' && source[key] !== null) {
                // Si el target no tiene la propiedad o no es un objeto, inicializarla como un objeto vacío
                if (!target[key] || typeof target[key] !== 'object') {
                    target[key] = {};
                }
                // Llamada recursiva para combinar sub-objetos
                this.ObjectMerge(target[key], source[key]);
            } else {
                // Si no es un objeto, asignar el valor directamente
                target[key] = source[key];
            }
        }
    }
       return target;
    }


    async fetchData(data, opts) {
        return fn_ajax(data, this._link, `#${opts.parent}`);
    }

    processData(data, opts,conf) {

        let attrTable = {
            data: data,
            f_size: '14',
            id: 'tbSearch',
            ...opts.attr
        };

        $(`#${opts.parent}`).rpt_json_table2(attrTable);
        opts.methods.send(data);
      
        if (conf.datatable) {
            window[conf.fn_datatable](`#${attrTable.id}`, 15);
        }
    }


       

}


class Modulo_Tab {
    constructor(link, div_filter) {
        this._link = link;
        this._div_filter = div_filter;
    }

    //   
    static obj_filter = {};

    /* getters and setters */


    set object_table(valor) {
        Modulo_Tab.obj_filter = valor;
    }


    filterBar() {
        let json_filter = [
            {
                opc: "input-calendar",
                id: "iptDate",
                tipo: "text",
                class: "col-6 col-sm-3",
                lbl: "Fecha de movimiento",
            },
            {
                opc: "btn",
                fn: "Buscar()",
                color: 'primary',
                text: "Buscar",
                class: "col-sm-2",
            },
        ];

        let dtx = {
            data: json_filter,
            type: false
        }

        // console.table(Modulo_Tab.obj_filter);

        dtx = Object.assign(dtx, Modulo_Tab.obj_filter);

        $("#content-bar").simple_json_content(dtx);



    }

    rpt(id_rpt, obj_rpt = {}) {
        // init data para crear un reporte
        let data_rpt = {
            tipo: "text",
            opc: "rpt",
        };

        data_rpt = Object.assign(data_rpt, obj_rpt);

        // crea un paquete para enviar al servidor

        $("#" + this._div_filter).validar_contenedor(data_rpt, (dtx) => {
            
            fn_ajax(dtx, this._link, "").then((data) => {
                
                $("#" + id_rpt).rpt_json_table2({
                    color_th: 'bg-primary-1',
                    data: data.table_rpt,
                    right: [2, 3, 4, 5],
                });

                $("#content-fp").rpt_json_table2({
                  color_th: "bg-primary-1",
                  data: data.table_fp,
                  right: [2, 3],
                });

                $("#content-propinas").rpt_json_table2({
                  color_th: "bg-primary-1",
                  data: data.table_propina,
                  right: [2, 3],
                });
            
            
            });

        });
    }

    // metodos mas simples para crear una tabla o un formulario

    simple_json_table(data, idTable) {
        $("#" + idTable).rpt_json_table2({ data: data });
    }
}

class Modulo_formulario {

    constructor(link, div_modulo) {
        this._link = link;
        this._div_modulo = div_modulo;
    }

    /*getter and setter variables */

    static object_data_table = {};
    static object_attr_table = {};

    static object_data_forms = {};
    static object_attr_forms = {};

    static _json_filter_bar = [];

    static json_forms = [];

    static object_forms = [];
    /*getter and setter metodos*/

    set data_table(valor) {
        Modulo_formulario.object_data_table = valor;
    }

    get data_table() {
        return Modulo_formulario.object_data_table;
    }

    set attr_table(valor) {
        Modulo_formulario.object_attr_table = valor;
    }



    set object_forms(valor) {
        Modulo_formulario.object_data_forms = valor;
    }

    /*-- Objetos para la filter bar --*/

    set json_filter_bar(valor) {
        Modulo_formulario._json_filter_bar = valor;
    }


    filterBar(idContent) {
        let json_filter = [
            {
                opc: "input-calendar",
                id: "iptDate",
                tipo: "text",
                class: "col-sm-3",
                lbl: "Fecha de movimiento",
            },
            {
                opc: "btn",
                fn: "Cheque()",
                color: 'primary',
                text: "Buscar",
                class: "col-sm-2",
            },
        ];

        if (Modulo_formulario._json_filter_bar.length > 0)
            json_filter = Modulo_formulario._json_filter_bar;

        let dtx = {
            data: json_filter,
            type: false
        }


        dtx = Object.assign(dtx, Modulo_Tab.obj_filter);

        $("#" + idContent).simple_json_content(dtx);



    }

    modulo_forms() {
        let obj_form = {
            // json_frm: json_cheque,
            class_frm: "col-12 col-md-4 line",
            class_formulario: "row row-col-2 row-cols-sm-2 row-cols-lg-2 ",
            // atributos_frm: object_frm,
        };

        obj_form = Object.assign(obj_form, Modulo_formulario.object_data_forms);

        /*--------------------------
        * Objectos para la tabla 
        ---------------------------*/

        let data = {
            opc: "ls",
        };

        data = Object.assign(data, Modulo_formulario.object_data_table);

        let object_table = {
            color_th: "bg-primary",
        };

        let obj_table = {

            datos: data,
            class_table: "col-12 col-md-8 p-3",
            datatable: true,
            atributos_table: Modulo_formulario.object_attr_table,
        
        };

        /*--------------------------
        * Configuracion
        ---------------------------*/

        let obj_ext = {
            
            /*Configuracion inicial*/
            enlace: this._link,

            /* Configuracion de formularios*/

            /*Configuracion de recetas */

            //   head_frm: true,
            //   alert: false,
            // content_table: "contentTableRecetasIng",

            // atributos_alert: object_alert,
            // atributos_event: object_event_ing,
        };

        let object_modulo = Object.assign(obj_form, obj_table, obj_ext);

        $("#" + this._div_modulo).modulo_1(object_modulo);
    }

    lsTable(opts = {}) {

        var defaults = {

            datatable: true,
            fn: '',
            id: 'tb-archivos',

        };


        opts = Object.assign(defaults, opts);


        /*--------------------------
       * Objectos para la tabla 
       ---------------------------*/

        let data = {
            opc: "ls",
        };

        data = Object.assign(data, Modulo_formulario.object_data_table);

        fn_ajax(data, this._link, "#" + opts.id).then((datos) => {

            let opt_table = {
              data: datos,
              color_th: "bg-primary-1",
            };



            opt_table = Object.assign(opt_table, Modulo_formulario.object_attr_table);

            $("#" + opts.id).rpt_json_table2(opt_table);

            if(opts.datatable)
            simple_data_table_no('#'+opt_table.id, 15);


        });

    }

    modulo_forms_horizontal() {
        let div = $("<div>", {
            class: "row p-2 ",
        });

        //-- Creamos el formulario -- */

        let opts_forms = {
            class: "col-12 col-lg-12  line",
            id: "archivos-formx",
            //   novalidate: true,
        };

        let div_formulario = $("<div>", opts_forms);
        div.append(div_formulario);

        // -- Creamos la tabla -- */

        let div_table = $("<div>", {
            class: "col-12 col-lg-12 mt-2 line",
            id: "tb-archivos",
        });


        div.append(div_table);
        $("#" + this._div_modulo).append(div);


        /*--  Configuracion de tabla  --*/

        this.lsTable({ id: "tb-archivos"});





        $("#" + opts_forms.id).content_json_form(
            Modulo_formulario.object_data_forms
        );
    }
}

class Modulo_busqueda extends Complements {

    constructor(link, div_modulo) {
        super(link, div_modulo);
        // this._link = link;
        // this._content_table = content_table;
    }

    /*getter and setter variables */

    // static _data_table = {};
    // static _attr_table = {};

    static _json_filter_bar = {};
    static _data_filter      = {}

    static _jsonFilterBar = {};
    


    set json_filter_bar(valor) {
        this._json_filter_bar = valor;
    } 
    
    set data_filter(valor) {
        this._data_filter = valor;
    }

    set jsonFilterBar(valor) {
        this._jsonFilterBar = valor;
    }
    
    

   

    filterBar(idContent) {

        // json por defecto para probar su funcionamiento
        let json_filter = [
            {
                opc: "input-calendar",
                id: "iptDate",
                tipo: "text",
                class: "col-sm-3",
                lbl: "Fecha de movimiento",
            },
            {
                opc: "btn",
                fn: "lsHistorial()",
                color: 'danger',
                text: "Buscar",
                class: "col-sm-2",
            },
        ];

        // si el json fue seteado, se reemplaza

        if (this._json_filter_bar) {

            json_filter = this._json_filter_bar;
        } else if (this._jsonFilterBar){
            json_filter = this._jsonFilterBar;
        } 





        let dtx = {
            data: json_filter,
            type: false
        }

        // console.log(dtx);


        // dtx = Object.assign(dtx, this._data_filter);

        // console.log(idContent);

        $("#" + idContent).simple_json_content(dtx);
    }


  

}

class modulo_uno extends Complements{
    
    constructor(link, div_modulo) {
        super(link, div_modulo);
        // this._link = link;
        // this._div_modulo = div_modulo;    
    }

    // setter y getter

    static _attr_content_form  = {};
    static _attr_content_table = {};

    static _attr_table       = {};
    static _data_form        = {};
    static _data_plugin_form = {};
    // static _data_table       = {};

    static _opts_table = {};
    static _attr_form  = {};

    _id_table = 'mdl-table';
    _id_frm   = 'frm';

    _id_content_table  = 'content-table';
    _id_content_frm    = 'content-frm';

    /*getter and setter metodos*/

    set attr_content_form(valor) {
        this._attr_content_form = valor;
    }

    set attr_content_table(valor) {
        this._attr_content_table = valor;
    }
    
    
    set id_content_frm(valor) {
        this._id_content_frm = valor;
    }

    set id_content_table(valor) {
        this._id_content_table = valor;
    }


    set id_table(valor) {
        this._id_table = valor;
    }

    set id_frm(valor) {
        this._id_frm = valor;
    }

    

    set data_form(valor) {
        this._data_form = valor;
    }

   
    // set data_table(valor) {
    //     this._data_table = valor;
    // }

    set attr_table(valor) {
        this._attr_table = valor;
    }

    set opts_table(valor) {
        this._opts_table = valor;
    }

    quitar_registro(options = {}){

            let object_alert = {
                title: "",
                text: "¿ Deseas quitar el registro ?",
                icon: "warning",

                showCancelButton: true,
                confirmButtonText: "Aceptar",
                cancelButtonText: "Cancelar",
            };

            let dtx = {  opc: "Quitar" };

            if (options.data)
                dtx = options.data;



            Swal.fire(
                object_alert,
            ).then((result) => {
                if (result.isConfirmed) {
                    
                    fn_ajax(dtx,this._link, "").then((data) => {
                        this.lsTable();
                    });
                }
            });
    }

    // Configuración para crear una tabla y un formulario :

    set data_plugin_form(valor) {
        this._data_plugin_form = valor;
    }


    
    modulo_tabla_formulario(options = {}) {

        /* --  Configuracion inicial del modulo -- */

        let defaults = {
            
            class_content: "row p-2",
            id_frm: this._id_frm,
            id_tabla     : this._id_content_table,

            datatable     : true,
            autovalidation: true,
            alert         : true,
            toogle_close  : false,
            data_alert: { title: 'Registro guardado con exito' },
            fn: ''
        };

        var opts = Object.assign(defaults, options);

        let div = $("<div>", {
            class: opts.class_content,
        });



        //-- Creamos el contenedor del formulario -- */

        let attr_div_form = {
            class: "col-lg-4 col-12 line",
            id: this._id_content_frm,
        };
        

        // Si fue cambiado los atributos el nuevo atributo cambia el formulario
        if(this._attr_content_form)
        attr_div_form = Object.assign(attr_div_form, this._attr_content_form);
    

        let div_form = $("<div>",attr_div_form);

          
        let opts_forms = {
            id        : opts.id_frm,
            novalidate: true,
        };

        // console.table(opts_forms);

        let div_formulario = $("<form>", opts_forms);



        // let div_close      = $('<div>', {
        //     class: "col mb-3  float-end",
        // }).append('<label>',{
        //     class:'fw-bold'
        // });

        // let btn_close = $('<button>', {
        //     onclick: 'closeForm()',
        //     class: 'btn-close',
        //     type: 'button',
        // });

        // div_close.append(btn_close);

        // if (opts.toogle_close)
        //     div_formulario.append(div_close);


        div_form.append(div_formulario);
        div.append(div_form);

        // -- Creamos la tabla -- */
        let opts_content_table = {
            class: "col-12 col-lg-8 line",
            id: this._id_content_table,
        }; 

        opts_content_table = Object.assign(opts_content_table, this._attr_content_table);
        let div_table = $("<div>", opts_content_table);
        div.append(div_table);


        //-- Vaciar los contenedores -- */

        $("#" + this._div_modulo).html(div);

        /*  --  Proceso para crear el formulario -- */

        let json = [
            {
                opc        : "input",
                lbl        : "Nombre",
                id         : "nombre",
                placeholder: "Nombre",
                tipo       : "texto",
            },
        ];

        let opc_json_form = {
            data: json,
        };

        opc_json_form = Object.assign(opc_json_form, this._data_plugin_form);


        // Asignar los plugins a los elementos
        $("#" + opts.id_frm).content_json_form(opc_json_form);

        
        if (opts.autovalidation) {

            let data_validation_form = {
                tipo: "text",
                opc: "save-frm",
            
            };

            data_validation_form = Object.assign(data_validation_form, this._data_form);


            $("#" + opts.id_frm).validation_form(data_validation_form,(datos) => {


                    fn_ajax(datos, this._link, "").then((data) => {
                        $("#" + opts.id_frm)[0].reset();
                        
                        if(opts.alert)
                            alert(opts.data_alert);
                        
                        this.lsTable();
                    });
                }
            );


        }
    }

}

// function eventoInput(id){}



/* -------
   modulo dos [ visor de datos ]
   ------ */ 

class Modulo_dos {


    constructor(link, div_modulo) {
        this._link = link;
        this._div_modulo = div_modulo;    
    }

    // setter y getter

    static _attr_content_form  = {};
    static _attr_content_table = {};

    static _attr_table       = {};
    static _data_form        = {};
    static _data_plugin_form = {};
    static _data_table       = {};

    static _opts_table = {};
    static _attr_form  = {};

    _id_table = 'mdl-table';
    _id_frm   = 'frm';

    _id_content_table  = 'content-table';
    _id_content_frm    = 'content-frm';

    /*getter and setter metodos*/

    set attr_content_form(valor) {
        this._attr_content_form = valor;
    }

    set attr_content_table(valor) {
        this._attr_content_table = valor;
    }
    
    
    set id_content_frm(valor) {
        this._id_content_frm = valor;
    }

    set id_content_table(valor) {
        this._id_content_table = valor;
    }


    set data_table(valor) {
        this._data_table = valor;
    }

    set attr_table(valor) {
        this._attr_table = valor;
    }

    set opts_table(valor) {
        this._opts_table = valor;
    }



    // setter and getter de filter bar
    static _json_filter_bar = [];
    static _data_filter     = {};

    set json_filter_bar(valor) {
        this._json_filter_bar = valor;
    }

    set data_filter(valor) {
        this._data_filter = valor;
    }


    filterBar(idContent) {
        // json por defecto para probar su funcionamiento
        let json_filter = [
            {
                opc: "input-calendar",
                id: "iptDate",
                tipo: "text",
                class: "col-sm-3",
                lbl: "Fecha de movimiento",
            },
            {
                opc: "btn",
                fn: "lsHistorial()",
                color: 'danger',
                text: "Buscar",
                class: "col-sm-2",
            },
        ];

        console.log(this._json_filter_bar);

        if (this._json_filter_bar.length > 0) // si el json fue seteado, se reemplaza
            json_filter = this._json_filter_bar;

        let dtx = {
            data: json_filter,
            type: false
        }


        dtx = Object.assign(dtx, this._data_filter);

        $("#" + idContent).simple_json_content(dtx);
    }

    lsTable(options = {}) {

        var defaults = {
            id: this._id_content_table,
            fn: "",
            datatable: true,
            no: 15,
        };



        let opts = Object.assign(defaults, options, this._opts_table);

        console.log(opts);

        /* --   Configuracion de la data para backend  -- */

        let data = { // data por default
            opc: "ls",
        };

        data = Object.assign(data, this._data_table);


        // --  Proceso para crear la tabla a partir del backend-- */

        fn_ajax(data, this._link, "#" + opts.id).then((datos) => {

            let atributos_table = {
                data: datos,
                color_th: "bg-primary-1",
                id: this._id_table,
            };

            atributos_table = Object.assign(atributos_table, this._attr_table);


            $("#" + opts.id).rpt_json_table2(atributos_table);


            if (opts.datatable)
                simple_data_table_no("#" + atributos_table.id, opts.no);

            eventoInput('mdl-table');
        });
    }


    


}

// functions complementarias

function sendAjax(options){

    var defaults = {
        data: { tipo: 'text', opc: "ls" },
        config: {
            datatable: false,
            fn: '',
            idFilter: this.idFilterBar,
        },
        extends: false

    };

    // Combinamos ambas opciones para envio al backend.
    console.error(options.data);
    let data_form = Object.assign(defaults.data, options.data);

    // Combinamos config con las opciones por default:
    let opts = Object.assign(defaults.config, options.config);

    $('#' + opts.idFilter).validar_contenedor(data_form, (datos) => {
        let extends_ajax = fn_ajax(datos, this._link, '');

        if (!opts.extends) { // si la variable extends no esta definida se ejectuta de forma normal

            //     extendsAjax.then((data) => {

            //         let attr_table_filter = {
            //             data: data,
            //             f_size: '12',
            //             id: 'tbSearch'
            //         };

            //         attr_table_filter = Object.assign(attr_table_filter, this._attr_table);
            //         $("#" + options.id).rpt_json_table2(attr_table_filter);

            //         if (options.datatable)
            //             simple_data_table_no("#" + attr_table_filter.id, 10);

            //     });

        }

    });

}


function getNameMonth(NoMonth) {
    const Months = {
        1: "Enero",
        2: "Febrero",
        3: "Marzo",
        4: "Abril",
        5: "Mayo",
        6: "Junio",
        7: "Julio",
        8: "Agosto",
        9: "Septiembre",
        10: "Octubre",
        11: "Noviembre",
        12: "Diciembre"
    };

    return Months[NoMonth];
}


// datatable
function data_table_export(table, size = 10) {

  
    $(table).DataTable({
        destroy: true,
        dom: "Bfrtip",
        order: [],

        pageLength: size,
        buttons: [{ extend: 'excel', className: 'btn-outline-primary' }, { extend: 'copy', className: 'btn-outline-primary' }],

        // buttons: ["copy", "excel"],
        oLanguage: {
            sSearch: "Buscar:",
            sInfo:
                "Mostrando del (_START_ al _END_) de un total de _TOTAL_ registros",
            sInfoEmpty: "Mostrando del 0 al 0 de un total de 0 registros",
            sLoadingRecords: "Por favor espere - cargando...",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior",
            },
        },
    });
}

function simple_data_table(table, no) {
    $(table).DataTable({
        pageLength: no,
        destroy: true,
        searching: true,
        bLengthChange: false,
        bFilter: false,
        order: [],
        bInfo: true,
        "oLanguage": {
            "sSearch": "Buscar:",
            "sInfo": "Mostrando del (_START_ al _END_) de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando del 0 al 0 de un total de 0 registros",
            "sLoadingRecords": "Por favor espere - cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "'Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        }
    });
}

// Complementos.
function dataPicker(options) {

    let defaults = {
        parent: 'iptCalendar',

        type: 'all',

        rangepicker: {

            startDate: moment().subtract(6, 'days'),
            endDate: moment(),
            showDropdowns: true,

            ranges: {
                "Prox 7 days": [moment(), moment().subtract(-7, "days")],
                "Prox 15 days": [moment(), moment().subtract(-15, "days")],
                "Mes actual": [moment().startOf("month"), moment().endOf("month")],
                "Mes prox": [
                    moment().subtract(-1, "month").startOf("month"),
                    moment().subtract(-1, "month").endOf("month"),
                ],
                "AÑO actual": [moment().startOf("year"), moment().endOf("year")],
            },

        },

        rangeDefault: {
            singleDatePicker: true,
            showDropdowns: true,

            locale: {
                format: "YYYY-MM-DD",
            }

        }

    };


    const settings = { ...defaults, ...options };

    if (settings.type == 'all') {
        $("#" + settings.parent).daterangepicker(settings.rangepicker);

    } else if (settings.type == 'simple') {

        $("#" + settings.parent).daterangepicker(settings.rangeDefault);
    }




}
