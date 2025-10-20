

class moduloIngresos extends Complements {
    constructor(link, contenedor) {
        super(link, contenedor);
    }

   
    filtroBusqueda() {
        $("#contentFilterIngresos").simple_json_content({
            data: this.jsonFiltroIngresos(),
            type: "",
        });
    }

    lsIngresos() {

        if (collector) {
            let date = ipt_date();

            $("#contentFilterIngresos").validar_contenedor(
                {
                    tipo: "text",
                    opc: "lsIngresos",
                    date: date.fi,
                    idFolio: collector.id
                },
                (datos) => {
                    fn_ajax(datos, link, "#contentIngresos").then((data) => {
                        console.warn('rows', data.row.length );

                        if (data.row.length > 0){

                            $("#contentIngresos").rpt_json_table2({
                                data: data,
                                right: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                                color_th: "bg-primary-1",
                                id: 'tbIngs',
                                extends: true,
                            });

                        }else{
                            $('#contentIngresos').empty_state();
                        }



                        // datable_export_excel('#tbIngs', 10);

                    });

                }
            );
        } else{
            // alert({ icon: 'warning', text: 'Debes abrir un turno para continuar...' });
            $('#contentIngresos').empty_state();
        
        }

    }

    jsonFiltroIngresos() {
        return [
            {
                opc: "select",
                lbl: "Categoria",
                id: "Categoria",
                id: "Categoria",
                tipo: "texto",
                data: categoria,
                class: 'col-12 col-sm-3',
                onchange: "ingresos.lsIngresos()",
            },


            {
                opc: "btn",
                id: "btnCapturar",
                tipo: "texto",
                text: "<i class='icon-plus'></i> Agregar Mov.",
                fn: "ingresos.formIngresos()",
                class: 'col-12 col-sm-3',
                color_btn: "success",
            },

            {
                opc: "input-file",
                id: "btnSubir",
                text: "Subir XLS",
                fn: "SubirArchivo()",
                color_btn: "info",
            },
        ];
    }


    formIngresos() {

        let idCategoria = $("#Categoria").val();

        let dtx = {
            opc: "frm-ingresos",
            idCategoria: $("#Categoria").val(),
        };

        fn_ajax(dtx, link, "").then((rtx) => {

            let jsonMovimientos = [
                {
                    opc: "select",
                    lbl: "Selecciona un grupo:",
                    id: "id_grupo",
                    data: rtx.categoria,
                    required: false,
                    class: 'col-12 col-sm-12',
                },
            ];



            if (idCategoria == 1) {

                let hospedaje = [

                    {
                        opc: "input",
                        lbl: "Pax:",
                        id: "pax",

                        tipo: "cifra",
                        required: true,
                        value: "",
                        class: 'col-12 col-sm-6',

                    },

                    {
                        opc: "input-group",
                        lbl: "Noche:",
                        id: "Noche",
                        icon: ' icon-calendar-2',
                        tipo: "cifra",
                        required: true,
                        value: "",
                        class: 'col-12 col-sm-6',

                    },

                    {
                        opc: "input-group",
                        lbl: "Monto:",
                        id: "iptMonto",
                        name: "Monto",
                        tipo: "cifra",
                        required: true,
                        icon: "icon-dollar",
                        placeholder: "0.00",
                        class: 'col-12 col-sm-6',

                        onkeyup: "CalculoDiferencia()",
                    },




                ];

                jsonMovimientos.push(...hospedaje);

            }

            if (idCategoria == 2) {

                let Noche = [
                    {
                        opc: "input",
                        lbl: "Pax:",
                        id: "pax",
                        tipo: "cifra",
                        required: true,
                        class: "col-12 col-sm-6",
                    },
                    {
                        opc: "input-group",
                        lbl: "Mesas:",
                        id: "Noche",
                        icon: 'icon-bell-5',
                        tipo: "cifra",
                        required: true,
                        class: "col-12 col-sm-6",

                    },

                    {
                        opc: "input-group",
                        lbl: "Monto:",
                        id: "iptMonto",
                        name: "Monto",
                        tipo: "cifra",
                        required: true,
                        icon: "icon-dollar",
                        placeholder: "0.00",
                        class: "col-12 col-sm-6",

                        onkeyup: "CalculoDiferencia()",
                    },

                ];

                jsonMovimientos.push(...Noche);

            } else if (idCategoria == 3 || idCategoria == 4 || idCategoria == 5) {

                let Tarifa = [
                    {
                        opc: "input-group",
                        lbl: "Tarifa",
                        id: "Tarifa",
                        icon: 'icon-bell-5',
                        tipo: "cifra",
                        required: true,
                        class: "col-12 col-sm-6",

                    },
                    {
                        opc: "input",
                        lbl: "Pax:",
                        id: "pax",
                        tipo: "cifra",
                        required: true,
                        class: "col-12 col-sm-6",

                    },

                    {
                        opc: "input-group",
                        lbl: "Monto:",
                        id: "iptMonto",
                        name: "Monto",
                        tipo: "cifra",
                        required: true,
                        icon: "icon-dollar",
                        placeholder: "0.00",
                        class: "col-12 col-sm-6",

                        onkeyup: "CalculoDiferencia()",
                    },



                ];

                jsonMovimientos.push(...Tarifa);

            }

            else if (idCategoria == 6 || idCategoria == 7 || idCategoria == 8) {

                let Monto = [

                    {
                        opc: "input",
                        lbl: "Pax:",
                        id: "pax",
                        tipo: "cifra",
                        required: true,
                        class: "col-12 col-sm-6",
                    },

                    {
                        opc: "input-group",
                        lbl: "Monto:",
                        id: "iptMonto",
                        name: "Monto",
                        tipo: "cifra",
                        required: true,
                        icon: "icon-dollar",
                        placeholder: "0.00",
                        class: "col-12 col-sm-6",

                        onkeyup: "CalculoDiferencia()",
                    },



                ];

                jsonMovimientos.push(...Monto);

            }


            else if (idCategoria == 9) {

                let Monto = [
                    {
                        opc: "input-group",
                        lbl: "Monto:",
                        id: "iptMonto",
                        name: "Monto",
                        tipo: "cifra",
                        required: true,
                        icon: "icon-dollar",
                        placeholder: "0.00",
                        class: "col-12 col-sm-6",


                    },

                ];

                jsonMovimientos.push(...Monto);
            }


            let formasPago = [


                {
                    opc: "input-group",
                    lbl: "Efectivo:",
                    id: "Efectivo",
                    tipo: "cifra",
                    icon: "icon-dollar",
                    required: false,
                    value: "",
                    onkeyup: "CalculoDiferencia()",
                    class: 'col-12 col-sm-6',

                },

                {
                    opc: "input-group",
                    lbl: "Tarjeta de Credito:",
                    id: "TC",
                    tipo: "cifra",
                    icon: "icon-dollar",
                    required: false,
                    onkeyup: "CalculoDiferencia()",
                    class: 'col-12 col-sm-6',


                },

                {
                    opc: "input-group",
                    lbl: "Cuenta por Cobrar",
                    id: "CxC",
                    tipo: "cifra",
                    icon: "icon-dollar",
                    required: false,
                    class: 'col-12 col-sm-6',

                    onkeyup: "CalculoDiferencia()",
                },

                {
                    opc: "input-group",
                    lbl: "Anticipo",
                    id: "Anticipo",
                    tipo: "cifra",
                    icon: "icon-dollar",
                    required: false,
                    class: 'col-12 col-sm-6',

                    onkeyup: "CalculoDiferencia()",
                },

                {
                    opc: "input-group",
                    lbl: "Diferencia",
                    id: "iptDiferencia",
                    tipo: "cifra",
                    icon: "icon-dollar",
                    class: 'col-12 col-sm-6',

                    required: false,
                    disabled: true,
                },

            ];


            jsonMovimientos.push(...formasPago);


            this._data_modal = {
                title: 'AGREGAR MOVIMIENTO [ ' + $("#Categoria option:selected").text() + ' / ' + $('#Categoria').val() + ']',
                message: '<form  class="" id="frm-modal"  novalidate></form>',

            };

            this._data_form = {
                tipo: "text",
                opc: "agregar-movimiento",
                idFolio: collector.id,
                idCategoria: $("#Categoria").val(),
            };


            this.modal_formulario({
                json: jsonMovimientos,
                fn: "listIngresos",
                extends: true
            });


            $('#id_grupo').select2({
                theme: "bootstrap-5",
                width: '100%',
                dropdownParent: $(".bootbox"),
            });

        });



    }

}






function listIngresos() {
    ingresos.lsIngresos();
}


//  Editar movimientos :

let json_mov_edit = [];
let modal = null;

function EditarMovimientos(id) {
    let dtx = {
        opc: "lsMovCapturados",
        idMovimiento: id,
        idFolio: collector.id,

    };

    fn_ajax(dtx, link, "").then((data) => {

        modal = bootbox.dialog({
            title: `<strong> Registros: ${id}</strong> `,
            message: `
            <div id="content-mov-edit"></div>
            <form class="row row-cols-sm-2 row-col-lg-2" id="modalEditarIngreso" novalidate></form>
            <div class="mt-3" id="content-table"></div>`,
        });



        json_mov_edit = [
            {
                opc: "select",
                lbl: "Mov capturados",
                id: "id_grupo",
                required: false,
                class: "col-12 col-sm-6 mb-3",
                selected: "-- Elige un movimiento --",
                // onchange: "MostrarMovimiento()",
                data: data.mov,
            },

            {
                opc: "btn",
                text: "Consultar",
                class: "col-12 col-sm-3 mb-3",
                fn: "mov_capturados()",
            },

            {
                opc: "btn",
                text: "Quitar",
                color_btn: "danger",
                class: "col-12 col-sm-3 mb-3",
                fn: "QuitarMovimiento()",
            },
        ];

        $("#content-mov-edit").simple_json_content({
            data: json_mov_edit,
            type: false,
        });


    }); // end fn ajax

}

function QuitarMovimiento() {

    if ($("#id_grupo").val() != 0) {
        Swal.fire({
            title: "",
            text: "¿Deseas cancelar el siguiente movimiento?",
            icon: "warning",
            showCancelButton: true,

            confirmButtonText: "Aceptar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {


                let dtx = {
                    opc: "QuitarMovimiento",
                    idMovimiento: $("#id_grupo").val(),
                };

                fn_ajax(dtx, link, "").then((data) => {

                    ingresos.lsIngresos();
                    modal.modal("hide");

                });


            }
        });

    } else {
        alert({ icon: 'info', text: 'Selecciona un movimiento para continuar...' });
    }

}


function mov_capturados() {
    id_grupo = $("#id_grupo").val();


    if (id_grupo != 0) { // validar que el formulario tenga registros para editar

        let dtx = {
            opc: "EditarMovimientos",
            idMovimiento: $("#id_grupo").val(),
        };

        /*--  Crear Formulario  -- */
        $("#modalEditarIngreso").html("");


        $("#modalEditarIngreso").simple_json_form({
            data: JSON_edit,
            type_btn: "two_btn",
        });


        fn_ajax(dtx, link, "").then((data) => {
            $("#modalEditarIngreso")[0].reset();
            $("#modalEditarIngreso").edit_json_form({ data: data });
        });


        // /*-- Valir Formulario -- */
        $("#modalEditarIngreso").validation_form(
            {
                tipo: "text",
                opc: "guardarEditMovimientos",
                mov: $("#id_grupo").val(),
                idgrupo: $("#Categoria").val(),
            },
            (datos) => {
                fn_ajax(datos, link, "").then((data) => {
                    ingresos.lsIngresos();
                    // modal.modal("hide");

                });
            }
        );



    } else {
        $("#content-frm").html("");

        alert({ icon: 'info', text: 'Selecciona un movimiento para continuar...' });

    }

}


let JSON_edit = [
    {
        opc: "input",
        lbl: "Pax:",
        id: "pax",
        tipo: "cifra",
        required: true,
        value: "",
    },

    {
        opc: "input",
        lbl: "Noche:",
        id: "Noche",
        tipo: "cifra",
        required: true,
        value: "",
    },

    {
        opc: "input-group",
        lbl: "Monto:",
        id: "Tarifa",
        tipo: "cifra",
        required: true,
        // disabled: true,
        icon: "icon-dollar",
        value: "",
    },

    {
        opc: "input-group",
        lbl: "Efectivo:",
        id: "Efectivo",
        icon: "icon-dollar",
        tipo: "cifra",
        required: false,
        value: "",
    },

    {
        opc: "input-group",
        lbl: "Tarjeta de Credito:",
        id: "TC",
        tipo: "cifra",
        icon: "icon-dollar",
        required: false,
        value: "",
    },

    {
        opc: "input-group",
        lbl: "Cuenta por Cobrar",
        id: "CxC",
        tipo: "cifra",
        icon: "icon-dollar",
        required: false,
        value: "",
    },

    {
        opc: "input-group",
        lbl: "Anticipo",
        id: "Anticipo",
        tipo: "cifra",
        icon: "icon-dollar",
        required: false,
        value: "",
    },

    {
        opc: "input-group",
        lbl: "Diferencia",
        id: "Diferencia",
        tipo: "cifra",
        icon: "icon-dollar",
        required: false,
        disabled: true,
        value: "0",
    },
];

/* -- Modulo Ingresos -- */

// function frm_ingresos() {

//     let dtx = {
//         opc: "frm-ingresos",
//         idCategoria: $("#Categoria").val(),
//     };

//     fn_ajax(dtx, link, "").then((rtx) => {

//         let _JSON = [
//             {
//                 opc: "Select",
//                 lbl: "Selecciona un grupo:",
//                 id: "id_grupo",
//                 data: rtx.categoria,
//                 required: false,
//             },

//             {
//                 opc: "input",
//                 lbl: "Pax:",
//                 id: "Pax",
//                 tipo: "cifra",
//                 required: true,
//                 value: "",
//             },

//             {
//                 opc: "input",
//                 lbl: "Noche:",
//                 id: "Noche",
//                 tipo: "cifra",
//                 required: true,
//                 value: "",
//             },

//             {
//                 opc: "input-group",
//                 lbl: "Monto:",
//                 id: "iptMonto",
//                 name: "Monto",
//                 tipo: "cifra",
//                 required: true,
//                 icon: "icon-dollar",
//                 placeholder: "0.00",

//                 onkeyup: "CalculoDiferencia()",
//             },

//             {
//                 opc: "input-group",
//                 lbl: "Efectivo:",
//                 id: "Efectivo",
//                 tipo: "cifra",
//                 icon: "icon-dollar",
//                 required: false,
//                 value: "",
//                 onkeyup: "CalculoDiferencia()",
//             },

//             {
//                 opc: "input-group",
//                 lbl: "Tarjeta de Credito:",
//                 id: "TC",
//                 tipo: "cifra",
//                 icon: "icon-dollar",
//                 required: false,
//                 onkeyup: "CalculoDiferencia()",

//             },

//             {
//                 opc: "input-group",
//                 lbl: "Cuenta por Cobrar",
//                 id: "CxC",
//                 tipo: "cifra",
//                 icon: "icon-dollar",
//                 required: false,

//                 onkeyup: "CalculoDiferencia()",
//             },

//             {
//                 opc: "input-group",
//                 lbl: "Anticipo",
//                 id: "Anticipo",
//                 tipo: "cifra",
//                 icon: "icon-dollar",
//                 required: false,
//                 onkeyup: "CalculoDiferencia()",
//             },

//             {
//                 opc: "input-group",
//                 lbl: "Diferencia",
//                 id: "iptDiferencia",
//                 tipo: "cifra",
//                 icon: "icon-dollar",
//                 required: false,
//                 disabled: true,
//             },
//         ];


//         if ($("#Categoria").val() == 9) {
//             _JSON = [
//                 {
//                     opc: "Select",
//                     lbl: "Selecciona un grupo:",
//                     id: "id_grupo",
//                     data: rtx.categoria,
//                     required: false,
//                 },


//                 {
//                     opc: "input-group",
//                     lbl: "Monto:",
//                     id: "iptMonto",
//                     name: "Monto",
//                     tipo: "cifra",
//                     required: true,
//                     icon: "icon-dollar",
//                     placeholder: "0.00",

//                     onkeyup: "CalculoDiferencia()",
//                 },

//                 {
//                     opc: "input-group",
//                     lbl: "Efectivo:",
//                     id: "Efectivo",
//                     tipo: "cifra",
//                     icon: "icon-dollar",
//                     required: false,
//                     value: "",
//                     onkeyup: "CalculoDiferencia()",
//                 },

//                 {
//                     opc: "input-group",
//                     lbl: "Tarjeta de Credito:",
//                     id: "TC",
//                     tipo: "cifra",
//                     icon: "icon-dollar",
//                     required: false,
//                     onkeyup: "CalculoDiferencia()",
//                 },

//                 {
//                     opc: "input-group",
//                     lbl: "Cuenta por Cobrar",
//                     id: "CxC",
//                     tipo: "cifra",
//                     icon: "icon-dollar",
//                     required: false,

//                     onkeyup: "CalculoDiferencia()",
//                 },

//                 {
//                     opc: "input-group",
//                     lbl: "Anticipo",
//                     id: "Anticipo",
//                     tipo: "cifra",
//                     icon: "icon-dollar",
//                     required: false,
//                     onkeyup: "CalculoDiferencia()",
//                 },

//                 {
//                     opc: "input-group",
//                     lbl: "Diferencia",
//                     id: "iptDiferencia",
//                     tipo: "cifra",
//                     icon: "icon-dollar",
//                     required: false,
//                     disabled: true,
//                 },
//             ];
//         }




//         modal = bootbox.dialog({
//             title: `<strong> Agregar movimiento [${$("#Categoria").val()}]</strong> `,
//             message: `<form  class="row row-cols-sm-2 row-col-lg-2" id="content-mov"  novalidate></form>`,
//         });

//         $("#content-mov").simple_json_form({
//             data: _JSON,
//             type_btn: "two_btn",
//         });

//         $("#content-mov").validation_form(
//             {
//                 tipo: "text",
//                 opc: "agregar-movimiento",
//                 idFolio: collector.id,
//                 idCategoria: $("#Categoria").val(),
//             },
//             (datos) => {
//                 fn_ajax(datos, link, "").then((data) => {
//                     ingresos();
//                     //   modal.modal("hide");
//                 });
//             }
//         );
//     });

// }

// function ingresos() {
//     date = ipt_date();
//     //   if(collector != null){
//     $("#content-ingresos").validar_contenedor(
//         {
//             tipo: "text",
//             opc: "lsIngresos",
//             date: date.fi,
//             idFolio: collector.id
//         },
//         (datos) => {
//             fn_ajax(datos, link, "#content-data").then((data) => {


//                 $("#content-data").rpt_json_table2({
//                     data: data,
//                     right: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
//                     color_th: "bg-primary-1",
//                     color_col: [2, 3, 4],
//                     //   f_size   : 12,
//                 });
//             });
//         }
//     );
//     //   }else{
//     //       alert("Debes abrir un turno para continuar...");
//     //   }
// }
