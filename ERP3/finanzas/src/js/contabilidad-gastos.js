const url = "ctrl/ctrl-subir-xls.php";

class Gastos extends Templates {

    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    initComponents() {
        this.createPlantilla();
        this.createFormGastos();
        this.lsGastosEfectivo();

        range_picker_now('fechaGasto');
    }

    createPlantilla(options) {

        let json_components = {
            id: "mdlGastos",
            class: "card-body row m-2",

            contenedor: [
                {
                    type: "form",
                    id: "formGastos",
                    class: " col-lg-4  line col-12 p-3",
                    novalidate: true,
                },

                {
                    type: "div",
                    id: "contentGast",
                    class: "col-lg-8 col-12 ",
                    children: [
                        { class: 'col-12', id: 'filterGastos' },
                        { class: 'col-12 mt-3', id: 'tableGastos' }
                    ]
                },
            ]
        };

        var defaults = { data: json_components, design: false };
        let opts     = Object.assign(defaults, options);

        this.createLayaout(opts);

        // -- agregar filter bar a gastos

        let json_filter_gastos = [
            {
                opc: 'input-group',
                id: 'dateGasto',
                lbl: 'Rango de búsqueda:',
                class: 'col-4  col-lg-4 col-sm-4',
                icon: 'icon-calendar',
                required: false,
            },
           
            {
                opc: 'btn',
                class: 'col-2 col-sm-2 col-lg-2',
                id: 'btnBuscarGasto',
                text: 'Buscar',
                fn: 'gastos.lsGastosEfectivo()',
                color_btn: 'outline-secondary',
            },

            {
                opc: "input-file",
                id: "btnSubir",
                class: "col-4 col-sm-3 col-lg-3",
                text: "Subir archivo",
                fn: "gastos.subirGasto()",
                color_btn: "success",
            },


        ];

        $('#filterGastos').content_json_form({ data: json_filter_gastos, type: '' });
        iptDate("dateGasto");
    }

    lsGastosEfectivo() {
        let dates = ipt_date('dateGasto');

        this.createTable(
            {
                parent: 'tableGastos',
                idFilterBar: 'content-bar',
                
                data: {
                    opc: 'lsGastos',
                    fi: dates.fi,
                    ff: dates.ff
                },

                attr: {
                    color_th: 'bg-primary-1',
                    id: 'tableGasto',
                    center: [1],
                    right: [3],
                    f_size:12,
                },

                conf: {
                    datatable: true,
                    fn_datatable: 'data_table_export',
                    validateData: {data:[],referencia:1},

                }

            }
        );



    }

    createFormGastos() {
        let formulario = [


            {
                opc: 'input-group',
                id: 'fechaGasto',
                lbl: 'Fecha:',
                class: 'col-12 mt-2',
                icon: 'icon-calendar',

            },
            {
                opc: 'select',
                id: 'id_proveedor',
                lbl: 'Nombre/Proveedor/Beneficiario:',
                class: 'col-12 mt-2',
                data: Proveedor,
                // tipo: 'texto',
            },

            {
                opc: 'input-group',
                id: 'Monto',
                lbl: 'Importe:',
                class: 'col-12 mt-2',
                icon: 'icon-dollar',
                placeholder: '0.00',
                tipo: 'cifra',
                // required: false,

            },

            {

                opc: 'input',
                id: 'descripcion',
                lbl: 'Concepto:',
                // required: false,

                class: 'col-12 mt-2',
                // tipo: 'texto',

            },

            {
                opc: 'btn-submit',
                class: 'col-12 mt-2',
                id: 'btnGuardarGasto',
                text: 'Guardar',
                color_btn: 'primary',
            },

          
        ];

        this.createForm({

            parent: 'formGastos',
            id: 'formGastos',
            json: formulario,

            data: { opc: 'setGastos' },

            methods: {
                send: () => {
                    // alert('se ha creado con exito.');
                    this.lsGastosEfectivo();
                }
            }
        });






        $('#id_proveedor').select2({
            theme: "bootstrap-5",
            width: '100%',
            // dropdownParent: $(".bootbox"),
        });


    }

    async editarGasto(id){
        
        // data
        const data  = await fn_ajax({opc:'getGasto',id:id},this._link);

  

        let formulario = [


            // {
            //     opc: 'input-group',
            //     id: 'fechaGastoEdit',
            //     lbl: 'Fecha:',
            //     class: 'col-12 mt-2',
            //     icon: 'icon-calendar',
            //     value: data.fechaGasto,

            // },
            {
                opc: 'select',
                id: 'id_proveedorEdit',
                lbl: 'Nombre/Proveedor/Beneficiario:',
                class: 'col-12 mt-2',
                data: Proveedor,
                value : data.id_proveedor,
                // tipo: 'texto',
            },

            {
                
                opc        : 'input-group',
                id         : 'Monto',

                value        : data.Monto,
                lbl        : 'Importe:',
                class      : 'col-12 mt-2',
                
                icon       : 'icon-dollar',
                placeholder: '0.00',
                tipo       : 'cifra',
                // required: false,

            },

            {

                opc: 'input',
                id: 'descripcion',
                lbl: 'Concepto:',
                value: data.descripcion,
                // required: false,

                class: 'col-12 mt-2',
                

                // tipo: 'texto',

            },

            {
                opc: 'btn-submit',
                class: 'col-12 mt-2',
                id: 'btnGuardarGasto',
                text: 'Guardar',
                color_btn: 'primary',
            },


        ];

        // crear modal.

        let modal = this.createModalForm({
            
            id: 'editGasto',

            bootbox :{
                title: ' Editar gasto'
            },

            json: formulario,

        });


        $("#editGasto").validation_form({ opc:'editGasto',idGastos:id,tipo:'text'}, (datos) => { 
            fn_ajax(datos, this._link);
            modal.modal('hide');
            this.lsGastosEfectivo();



        });

    }


    quitarGasto(id, e) {


        this.swalQuestion({

            opts: {
                title: '¿Deseas quitar el siguiente gasto?',
            },
            data: {
                opc: 'quitarGasto',
                idgastos: id


            },
            methods: {
                request: (data) => {
                    this.lsGastosEfectivo();

                    //   const row =   e.target.closest('tr');
                    //   row.remove();


                }
            }

        });

    }

    tabsFiles(id){

        let json = [
            {
                tab: "Agregados",
                id: "tab-agregados",
                active: true, // indica q pestaña se activara por defecto
            },

            {
                tab: "No agregados",
                id: "tab-no-agregados",
            },


        ];


        $('#'+id).simple_json_tab({data:json});

    }

    subirGasto(idList) {
     
    var InputFile = document.getElementById("btnSubir");

    var file      = InputFile.files;
    var data      = new FormData();
    var cant_file = file.length;

    for (var i = 0; i < file.length; i++) {
        data.append("excel_file" + i, file[i]);
    }


    $.ajax({
        url: url,
        contentType: false,
        processData: false,
        dataType: "json",
        type: "POST",
        cache: false,
        data: data,
        beforeSend: function () {
            $("#tableGastos").html('Cargando...');
        },
   
        success: function (datos) {
            let {agregados,noAgregados} = datos;

            gastos.tabsFiles('tableGastos');

            $("#tab-agregados").rpt_json_table2({
                data: agregados,
                color_th: 'bg-primary-1',
                id: 'tbGastosAgregados',
                center: [1],
                right: [3],
                f_size: 12,
            });

            $("#tab-no-agregados").rpt_json_table2({
                data: noAgregados,
                color_th: 'bg-primary-1',
                id: 'tbGastosNoAgregados',
                center: [1],
                right: [3],
                f_size: 12,
            });

              



            simple_data_table_no('#tbGastosAgregados',15); 
            
            data_table_export('#tbGastosNoAgregados',15);


            const news = Object.keys(datos.nuevosProveedores).length;

            if (news)
            alert({ icon: 'info', title: `Se han agregado ${news} nuevos proveedores`,timer:3000});

            document.getElementById("btnSubir").value = "";
        },
    });

  
}



 




}





