
var tc = new Modulo_formulario(link, "tab3");

function modulo_tc() {
  let JSON_tc = [
    {
      opc: "input-group",
      lbl: "Monto",
      id: "Monto",
      tipo: "cifra",
      icon: "icon-dollar",
      placeholder: "00.00",
    },

    {
      opc: "select",
      lbl: "Terminal",
      id: "id_Terminal",
      data: terminal,
    },

    {
      opc: "select",
      lbl: "Tipo de Terminal",
      id: "id_TipoTerminal",
      data: tipoterminal,
    },

    {
      opc: "input",
      lbl: "Concepto de Pago",
      id: "Concepto",
      tipo: "texto",
      placeholder: " Restaurant,Hospedaje, etc..",
    },

    {
      opc: "input-group",
      lbl: "Nombre del Cliente",
      id: "Cliente",
      icon: "icon-user",
      tipo: "texto",
    },

    {
      opc: "input",
      lbl: "Especificación",
      id: "Especificacion",
      tipo: "texto",
      required: false,
    },

    {
      opc: "input",
      lbl: "No. autorización",
      id: "Autorizacion",
      tipo: "number",
    },

    // {
    //   opc: "textarea",
    //   lbl: "Observaciones",
    //   id: "observaciones",
    //   required: false,

    // },
  ];

    object_frm = {
        id_Folio: collector.id
    };



    /*-- Creacion de una tabla  --*/

    dtx = {
      opc: "lsTC",
      fol: collector.id,
    };

   let object_table = {
     color_th: "bg-primary",

     right: [4],
     center: [5, 6],
   };

  //   /*-- LLenar los datos del plugin  --*/

    $("#tab3").modulo_1({
      frm: "frm_tc",
      json_frm: JSON_tc,
      class_formulario: "row row-col-2 row-cols-sm-2 row-cols-lg-2",
      atributos_frm: object_frm,

      datos: dtx,
    //   datatable:true,
    //   atributo_tabla: object_table,

    });
}



function lsTC() {
  let dtx = {
    opc: "lsTC",
    fol: collector.id,
  };

  fn_ajax(dtx, link, "#content-table").then((data) => {
    
    $("#content-table").rpt_json_table2({
      data: data,
      color_th: "bg-primary",
      right: [4],
      center: [5, 6],
      id: "rpt-table",
    });

    simple_data_table_no('#rpt-table',10);
  });
}


