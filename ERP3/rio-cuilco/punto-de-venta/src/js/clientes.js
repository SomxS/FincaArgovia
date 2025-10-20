class Rosa extends Templates {

    constructor(link, div_modulo) {
        super(link, div_modulo);
       
    }

    createTypeQuestion(opts){

 
        let divs = $('#' + opts.parent);
        divs.empty();

  
        if (opts.type == 1) { // Formulario normal.

            let answer_data = opts.data.data[0] || {};

            let nota_venta = [
            {
                opc        : "input-group",
                id         : "minutes",
                placeholder: "Minutos",
                class      : 'col-6',
                icon       : 'icon-clock',
                lbl        : 'Minutos',
                tipo       : "cifra",
                required   : false,
                value      : answer_data.minutes || ''
            },

            {
                opc        : "input-group",
                id         : "seconds",
                placeholder: "Segundos",
                lbl        : 'Segundos',
                class      : 'col-6',
                icon       : 'icon-clock',
                tipo       : "cifra",
                required   : false,
                value      : answer_data.seconds || ''
            },


            {
                opc  : "textarea",
                lbl  : "Observación:",
                id   : "observation",
                rows : 5,
                class: 'col-12 mt-2',
                tipo : "texto",
                value: answer_data.obs || ''
            },

            {
                opc      : 'btn-submit',
                class    : 'col-12 col-sm-4 offset-sm-8',

                color_btn: Object.keys(answer_data).length > 0 ? 'primary'    : 'success',
                text     : Object.keys(answer_data).length > 0 ? 'Actualizar' : 'Guardar',
            }
            ];

            // Metodo update / insert
            let opc = Object.keys(answer_data).length > 0 ? 'editAnswered' : 'setAnswered';


            this.createForm({
                
                id    : 'formEvaluations',
                parent: 'content-list-question',
                json  : nota_venta,
             
                data: { opc:opc,id_Folio : idFolio, id_Question:opts.idQuestion, },
                methods: {
                    send:(data) =>{

                        $('#question_' + opts.idQuestion).addClass('answered');
                        $('#question').addClass('hide');

                        this.cleanCard({ parent:'content-question'});

                       
                    }
                }
            });
            

          
        } else if (opts.type == 3) { // multiple

            if (opts.data.data[0]){ // Existe una respuesta.
             
                if (opts.data.data[0].getout == 1){ // ya tuvo respuesta fue 
                    this.createMotiveComponent(divs, opts);
                }else { // 0
                    this.createYNComponent(divs,opts);
                }

            }else{
                this.createYNComponent(divs, opts);
            }

        }


    }

    createYNComponent(divs,opts){

        console.log('YN',opts);

      
        divs.append($('<div>', { class: 'col-12 ', id: 'containerButtonsOptions' }));
        let class_button = 'outline-info';
        let isGetout     = false; // no salio


        if ( opts.data.data[0] ) {

            class_button = opts.data.data[0].getout == 0 ?  'info' : 'outline-info';
            isGetout     = true;
        
        }


        $('#containerButtonsOptions').content_json_form({ class: 'row mt-2', type: '',data: [


            {
                opc      : 'button',
                text     : "SI",
                class    : 'col-12',
                className: 'w-100 py-2 ',
                color_btn: 'outline-info',

                onClick: () => {

                    if (isGetout) {


                        this.setAnswered({

                            opc        : 'editAnswered',
                            id_Folio   : idFolio,
                            observation: 'mod SI ',
                            getout     : 1,
                            
                            id_Question: opts.idQuestion

                        });

                        this.createMotiveComponent(divs, opts);


                    }else{

                        this.setAnswered({ 
                             id_Folio: idFolio,
                             id_Question: opts.idQuestion,
                             observation: 'add yes ',
                             getout: 1 
                            });

                        this.createMotiveComponent(divs, opts);

                    }

                  
                },


            },


            {
                opc      : 'button',
                text     : 'NO',
                color_btn: class_button,
                class    : 'col-12',
                className: 'w-100 py-2 ',

                onClick: () => {

                    if(isGetout){

                        this.setAnswered({ 
                             opc        : 'editAnswered',
                             observation: 'mod',
                             getout     : 0,
                             id_Folio   : idFolio,
                             id_Question: opts.idQuestion 
                        });


                        $('#question_' + opts.id_Question).addClass('answered');
                        $('#question').addClass('hide');
                  
                    }else{

                        this.setAnswered({ id_Folio: idFolio, id_Question: opts.idQuestion, getout: 0 });

                        $('#question_' + opts.id_Question).addClass('answered');
                        $('#question').addClass('hide');
                    }

                    
                 
                },
            }


        ]});

    }

    createMotiveComponent(divs,opts){

        divs.empty();
        // Create layout options.
        
        divs.append($('<label>', { class: 'fw-semibold text-uppercase  my-3', text: '¿Cual fue el motivo ?' } ));
        divs.append($('<div>',   { class: 'father-options col-12 line', id: 'Options' } ));
        divs.append($('<div>',   { class: 'w-100 mt-2 text-end line', id: 'headersOptionsEnd' } ));

        
        // Options - set motive.

        this.creategroupCard({

            parent : 'Options',
            data   : opts.data.dataPackage,
            type   : 'options',
        
            success: (data) => {  

                console.log(data);
            
                this.listPackage(data,opts); 
            }
        
        });


        // Buttons.

        let buttons = [
            
            {
                'icon' : '',
                'color': 'outline-danger',
                'text' : 'Cancelar',

                onClick: () => { 

                    this.swalQuestion({

                        opts: {   title: '¿Deseas eliminar la respuesta anterior?',  },

                        data: { opc: 'removeQuestion', id_Folio: idFolio, id_Question: opts.idQuestion },

                        methods: {
                           
                            request: (data) => {
                           
                                $('#question_' + opts.idQuestion).removeClass('answered');
                                $('#question').addClass('hide');
                            }
                        }

                    });
               
                }
            },

            {
                'icon' : '',
                'color': 'primary',
                'text' : 'Finalizar',

                onClick: () => { // Guardar la salida y las opciones 

                    this.createCardWithForm(divs, opts); 
                }
            }
        ];

      
        this.createButtonGroup({

            class : 'justify-content-end gap-2',
            parent: 'headersOptionsEnd',
            data  : buttons,
            size  : 'sm',
            cols  : 'w-25 p-2'
        
        });


    }

    createCardWithForm(divs,opts){
        divs.empty();
        divs.append($('<form>',{class:'col-12',id:'idFormulario'}));

        let answer = opts.data.data[0] || {};

        let json = [
            {
                opc        : "input-group",
                id         : "minutes",
                placeholder: "Minutos",
                lbl        : 'Minutos',
                class      : 'col-6',
                icon       : 'icon-clock',
                tipo       : "cifra",
                required   : false,
                value      : answer.minutes || ''
            },
            {
                opc        : "input-group",
                id         : "seconds",
                placeholder: "Segundos",
                lbl        : 'Segundos',
                class      : 'col-6',
                icon       : 'icon-clock',
                tipo       : "cifra",
                required   : false,
                value      : answer.seconds || ''
            },
            {
                opc  : "textarea",
                lbl  : "Observación:",
                id   : "observation",
                rows : 5,
                class: 'col-12 mt-2',
                value: answer.obs || ''
            },
            {
                opc      : 'btn-submit',
                class    : 'col-12 col-sm-4 py-2 offset-sm-8',
                color_btn: Object.keys(answer).length > 0 ? 'info'      : 'primary',
                text     : Object.keys(answer).length > 0 ? 'Actualizar': 'Guardar',
            }
        ];



        this.createForm({

            id: 'formQuest',
            parent: 'idFormulario',
            json: json,
            type: 'form',
            data: { opc: 'editAnswered', id_Folio: idFolio, id_Question: opts.idQuestion },

            methods: {
                send: (data) => {

                    this.cleanCard({ parent: 'content-question' });
                    
                    $('#question_' + opts.idQuestion).addClass('answered');
                    $('#question').addClass('hide');


                }
            }
        });





    }


    // Components.

    createTimesComponent(divs,opts){

        
        // //             // Limpiar la card.
        // //             this.cleanCard({ parent: 'content-list-question' });
        // //             // create card.
       
        // //             // Metodo update / insert.
        // //             divs.append($('<form>', { class: '', id: 'formAnswered', novalidate: true }))
        // //             this.createForm({
        // //                 id: 'formEvaluations',
        // //                 parent: 'formAnswered',
        // //                 json: json,
        // //                 
        // //                 methods: {
        // //                     send: (data) => {
        // //                         // this.cleanCard({ parent: 'question' }); 
        // //                         $('#question').addClass('hide');

        // //                     }
        // //                 }
        // //             });
        // //         }
        // //     });
        // //     $('#headersOptionsEnd').append(buttonEnd);


    }

    listPackage(data, opts) {

        // crear nuevo componente.
        this.createLayaout({

            design: false, parent: 'content-list-question', clean: true, data: {
                id: '',
                class: 'd-flex flex-column h-100 gap-2',

                elements: [

                    { type: 'form', class: 'col-sm-12 flex-grow-1 mb-auto line', id: 'listPackageOptions', novalidate: true },
                    { type: 'div', class: 'mt-auto line w-100 text-end', id: 'content-end-options' },

                ]

            }
        });


        // initializad.
        let buttons = [
            
            {
                'icon' : '',
                'color': 'outline-primary',
                'text' : 'Regresar',

                onClick: () => { //  layaout motivos de salida.

                    let divs = $('#' + opts.parent);
                    this.createMotiveComponent(divs, opts);
                }
            },

            {
                'icon' : '',
                'color': 'Primary',
                'text' : 'Guardar',

                onClick: () => { // Guardar la salida y las opciones 
                    
                    this.setOptions(data.id);

                    fn_ajax({ opc: 'getAnswered', idFolio: idFolio, id: data.id }, this._link).then((data) => {


                        // this.createLayaoutQuestion({ // Render motivos Component.

                        //     text : opts.text_,
                        //     id   : opts.idQuestion,
                        //     data : data,

                        //     type : opts.type,
                        //     index: opts.index_
                        
                        // });

                    });
                }
            }
        ];

        this.createButtonGroup({ class: 'justify-content-end gap-2',parent: 'content-end-options', data: buttons, size: 'sm', cols: 'w-25 p-2' });

        // asigna propiedades para crear el arreglo de checkbox:
        const json = addProperties(data.data, { opc: 'checkbox', class: 'form-check  options bg-pink-200' });
        
        $('#listPackageOptions').content_json_form({ data: json, type: '',class: 'father-options' });


    }

    // Operations.

    setAnswered(props){

        console.log('setAnswered',props);

        fn_ajax({
            opc: 'setAnswered',
            ...props
        },this._link).then((data) => {

           
        });
    }

    setOptions(idPackage) {

        const checkboxes  = document.querySelectorAll('#jsonForm input[type="checkbox"]');
        const listChecked = Array.from(checkboxes).filter(checkbox => checkbox.checked).map(checkbox => checkbox.id).join(',');


        this.useAjax({

            data: {
                opc      : 'setAnsweredOption',
                id       : listChecked,
                id_Folio : idFolio,
                idPackage: idPackage,
                valid    : 1
            }

        });



    }

}


let cliente_preferencial ;


class Clientes extends Templates {

    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.groups = '';
    }

    render(){
        this.initComponents();

        this.createLayaoutClientes({ parent: this.div_modulo });
        this.lsClientes();
    }

    initComponents(){
      fn_ajax({opc:'init'}, this._link).then((data)=>{
    
          this.groups = data.groups;
          this.btnGroups();
      });
    }

    createLayaoutClientes(options) {

        let jsonComponents = {
            id: "contentClientes",
            class: "d-flex mx-3 my-3",
            contenedor: [
                
                {
                    type: "div",
                    class: "col-3 col-lg-4 col-sm-4 me-1  py-2 ", id: '',

                    children: [

                        { class: " my-2 mx-1 ", id: 'listClientes' },
                  
                    ]
                },

                {
                    type: "div",
                    class: "col-9 col-sm-8 col-lg-8 ", id: '',
                    children: [
                        { class: 'col-12 block fw-bold', text: 'Cliente: ',id:'lblCliente' },
                        { class: "col-12 block p-3", id: 'filterGroupClientes' },
                        { class: "col-12 block", id: 'listPreferentials' },
                    ]
                },
            ],
        };

        this.createPlantilla({ data: jsonComponents, parent: options.parent, design: false });
       
    }
    
    lsClientes(){
        this.createTable({

            parent: 'listClientes',

            data: {
                opc: 'lsClientes',
            },

            conf: { datatable: true, fn_datatable: 'data_table_clientes' },
            attr: {
                id:'tbClientesPreferenciales',
                class: 'table table-bordered table-sm table-hover',

                center: [1]
            },
            extends: false
        });
    }

    lsProducts(id){

        this.createTable({

            parent: 'listPreferentials',

            data: {
                opc: 'lsProducts',
                idCliente: cliente_preferencial,
                idGroup: id
            },

            conf: { datatable: false },

            attr: {
                id: 'tbProductos',
                extends: true,
                center: [1,3]
            },
            extends: false
        });
    }

    btnGroups() {
        this.createButtonGroup({
            parent: 'filterGroupClientes',
            fn:'clients.initComponents',
            dataEl: { data: this.groups, icon: 'icon-shopping-basket', onClick: 'clients.lsProducts' }
        });
    }

    onshowDescuentos(id){
        
        const tr      = $(event.target).closest("tr");
        let   cliente = tr.find("td").eq(1).text();
        cliente_preferencial = id;

        let label = $('<label>',{class:'fw-bold' ,text:'Cliente : '});
        label.append( $('<span>',{ class:'text-green-800', text: cliente }) );
      
        $('#lblCliente').html(label);

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