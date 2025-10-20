/* -- Cuentas por cobrar  -- */
function interfaceCXC() {

    // settear id de los elementos
    cxc.id_table = 'table-cxc';

    cxc.attr_content_table = {
     class: 'col-12',
    };

}

function lsCXC() {

    cxc.data_table = {
        opc  : 'lsCXC',
        // id   : collector.id,
        fecha: $('#iptDate').val()
    };

    cxc.attr_table = {
        center: [1, 2],
        color:'bg-default',
        color_col:[5,6,7],
        right: [4,5,6,7],
    };

    // options:

    cxc.opts_table = {
        datatable:false,
    };


    cxc.lsTable({ id: 'tab-cxc' });
}

function interface(){
    obj = new Complements();
};

function definirFormaPago(idCxC,event){

    // Rellenar formulario con las formas de pago
    fn_ajax({ opc: 'editCxC', idFP_Bitacora: idCxC }, ctrl_cxc, "").then((data) => {
      $('#frm-modal').edit_json_form({data:data});
    });

    // Obtener datos de la fila seleccionada
    let tr      = $(event.target).closest("tr");
    let idfolio = tr.find('td').eq(0).text();
    let sub     = tr.find('td').eq(2).text();
    let val     = tr.find('td').eq(3).text();
    

    json = [
        {
            opc: "input",
            lbl: "Descripción:",
            id: "subcategoria",
            tipo: "texto",
            value: sub,
            required: false,
            disabled: true,
            class: 'col-12 col-sm-6',

        },

        {
            opc: "input-group",
            lbl: "CxC:",
            id: "cxc",
            tipo: "cifra",
            value:val,
            icon: "icon-dollar",
            required: false,
            disabled: true,
            class: 'col-12 col-sm-6',

        },


        {
            opc: "input-group",
            lbl: "Efectivo:",
            id: "Efectivo",
            tipo: "cifra",
            icon: "icon-dollar",
            required: false,
            value: "",
            class: 'col-12 col-sm-6',

        },

        {
            opc: "input-group",
            lbl: "Tarjeta de Credito:",
            id: "TC",
            tipo: "cifra",
            icon: "icon-dollar",
            required: false,
            class: 'col-12 col-sm-6',
        },

       
        {
            opc: "input-group",
            lbl: "Anticipo",
            id: "Anticipo",
            tipo: "cifra",
            icon: "icon-dollar",
            required: false,
            class: 'col-12 col-sm-6',
        },

        {
            opc: "textarea",
            lbl: "Observaciones",
            id: "observaciones",
            tipo: "texto",
            required: false,
            class: 'col-12 ',
        },

    ];




    cxc.data_modal = {
        title: 'Definir forma de pago - #<span class="text-success"> ' + idfolio+'</span>',
    }; 
    
    cxc._data_form = {
     
        tipo: "text",
        opc : "setCxC",
        id  : idCxC,
    
    };


    cxc.modal_formulario({
        json   : json,
        fn: 'lsCXC',
        extends: true
    });


  
  
}





