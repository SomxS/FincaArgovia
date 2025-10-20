function interface() {  // sirve para cargar las configuraciones iniciales y settear los valores establecidos

    cortesias.id_content_frm = 'content-frm-cortesias';
    cortesias.id_content_table = 'content-table-cortesias';

    cortesias.id_frm = 'frm-cortesias';
    cortesias.id_table = 'table-cortesias';


    // Configuración para cortesias:
    cortesias.data_form = {
        opc  : 'frm-cortesias',
        id   : collector.id,
        fecha: $('#iptDate').val()
    };

    
    cortesias.attr_table = {
        color_th: 'bg-default',
        f_size  : '16',
        center  : [1, 2],
        right   : [4]
    };


}

function cortesia_empleados() {
    // 1.- Crear el json del formulario

    let json_cortesia = [

        {
            opc: 'select',
            lbl: 'Sub categoría',
            id: 'idSubcategoria',
            required: false,
            data: sub_cortesias,
            class: 'col-12'
        },

        {
            opc: 'input-group',
            lbl: 'Monto',
            id: 'cantidad',
            tipo: 'cifra',
            icon: 'icon-dollar',
            class: 'col-12'
        }

    ];

    cortesias.data_plugin_form = { // data para crear el formulario (json)
        data: json_cortesia
    };


    // asignamos la data para enviar al backend
    cortesias.data_form = {
        opc: 'frm-cortesias',
        id: collector.id,
        fecha: $('#iptDate').val()
    }

 

    cortesias.modulo_tabla_formulario({extends:true});
}

function BuscarCortesias() {
    
    if(collector){

        cortesias.data_table = {
            opc: 'lsCortesias',
            id: collector.id,
            fecha: $('#iptDate').val(),
        };

        cortesias.lsTable();

    }else{
        $('#tab-cortesias').empty_state();

    }
   

}


function quitarCortesia(idCortesia) {

    data = { // data para enviar al backend
        opc: 'quitar_cortesia',
        idCortesia: idCortesia
    };

    cortesias.quitar_registro({
        data: data
    });
}


/* -- Tarjetas de credito  -- */

class tarjetaCredito {
  

}


function interface_tc() {

    // reinicio de valores x defecto (caprichitos):

    tcx.id_content_frm = 'content-frm-tc';
    tcx.id_content_table = 'content-table-tc';

    tcx.id_frm = 'frm-tc';
    tcx.id_table = 'table-tcx';

    tcx.attr_content_table = {
        class: 'col-12 mt-3',
    };
    tcx.attr_content_form = {
        class: 'col-12 line',
    };


    // data para enviar al backend :

    tcx.data_form = {
        opc: 'frm-tc',
        id_Folio: collector.id,
    };

    tcx.attr_table = {
      right: [4,8],
      center: [5, 6]
    };

    //  data plugin  form (JSON ... ):

    tcx.data_plugin_form = {
        data: JSON_TC(),
        type: ''
    };

    // crear modulo :
    tcx.modulo_tabla_formulario();

    // Complementos para formulario :
    $('#id_TipoTerminal').select2({
        theme: "bootstrap-5",
        width: '100%',
    });

    BuscarTCx();
}

function BuscarTCx() {
    if(collector){

    // data para enviar al backend:

    tcx.data_table = {
        opc: 'lsTC',
        fol: collector.id,
        date: $('#iptDate').val()
    };


    tcx.lsTable();
   
    }else{

        $('#tab-tcx').empty_state();
    
    }
}

function JSON_TC() {

    return [

        {
            opc: "input-group",
            lbl: "Monto",
            id: "Monto",
            tipo: "cifra",
            icon: "icon-dollar",
            placeholder: "00.00",
            class: "col-sm-3 col-12",
        },

        {
            opc: "select",
            lbl: "Terminal",
            id: "id_Terminal",
            data: terminal,
            class: "col-sm-3 col-12",

        },

        {
            opc: "select",
            lbl: "Tipo de Terminal",
            id: "id_TipoTerminal",
            data: tipoterminal,
            class: "col-sm-3 col-12",
        },


       

        {
            opc: "input",
            lbl: "Concepto de Pago",
            id: "Concepto",
            tipo: "texto",
            placeholder: " Restaurant,Hospedaje, etc..",
            class: "col-sm-3 col-12",
        },

        {
            opc: "input-group",
            lbl: "Nombre del Cliente",
            id: "Cliente",
            icon: "icon-user",
            tipo: "texto",
            class: "col-sm-3 col-12",

        },

        // {
        //     opc: "input",
        //     lbl: "Especificación",
        //     id: "Especificacion",
        //     tipo: "texto",
        //     required: false,
        //     class: "col-sm-3 col-12",
        // },

        {
            opc: "input",
            lbl: "No. autorización",
            id: "Autorizacion",
            tipo: "number",
            class: "col-sm-3 col-12",
        },

        {
            opc: "input-group",
            lbl: "Propina",
            id: "propina",
            tipo: "cifra",
            required: false,
            icon: "icon-dollar",
            class: "col-sm-3 col-12",
        },

        {
            opc: "textarea",
            lbl: "Observaciones",
            id: "Observaciones",
            required: false,
            class: "col-sm-3 col-12",

        },

        {
            opc: "btn-submit",
            text: "Agregar",
            id: "btnTC",
            color_btn: "primary",
            class: "col-12 col-sm-4 offset-sm-4",
        }
    ];
}

function QuitarTC(id) {

    tcx.data_question = {
        opc: "QuitarTC",
        id: id,
    };

    tcx.attr_question = {
        title: "¿Deseas eliminar el registro ?",
    };
    
    tcx.modal_question();

 
}


