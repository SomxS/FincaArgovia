/* -- Modulo subir archivos-- */

class filesUpload extends Complements {

    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    initComponents() {
        this.idContentForm = 'contentFiles';
        this.idContentTable = 'contentTableFiles';

      
       
        this.lsFiles();
    }



    lsFiles() {
        this.dataPluginForm = {
            data: this.jsonFiles()
        };

        this.data_table = {
            opc: "lsArchivos",
            date: $('#iptDate').val(),
        };

        this.attr_table = {
            center: [3, 4, 5],
            id:'tbFilesUpload'
        };




        this.formTable({ extends: true });
        this.lsTable({ id: 'contentTableFiles' });

    }

    // lsTableFiles() {
    //     this.lsTable({ id: 'contentTableFiles' });
    // }

    uploadFile() {

    }

    removeFile(id) {

        this.attr_question = {
            text: "¿Deseas quitar el archivo del sistema ?",
        };

        this.data_question = {
            opc: "QuitarArchivo",
            status: "0",
            idSobre: id,

        };


        this.modal_question({ fn: 'lsArchivos' });

    }

    jsonFiles() {
        return [

            {
                opc: "input-file-btn",
                lbl: "",
                id: "archivos",
                text: "Nuevo Producto",
                required: true,
                class: "col-sm-12 mb-3",
            },

            {
                opc: "select",
                lbl: "Vincular con",
                id: "link",
                required: false,
                class: "col-sm-12 ",
                data: checklist,
            },

            {
                opc: "textarea",
                lbl: "Observaciones",
                id: "Detalles",
                text: "Nuevo Producto",
                tipo: "texto",
                required: false,
                class: "col-sm-12 ",
            },

            {
                opc: "btn",
                color_btn: "secondary",
                id: "btnSubirArchivo",
                text: "Subir Archivo",
                fn: "subirArchivo()",
                class: "col-sm-12 ",
            },

        ];
    }

}

function lsArchivos() {
    files.lsTableFiles();
}


function subirArchivo() {

    // $("#archivos-form").validar_contenedor({
    //     opc: "files",
    // },(datos)=>{
    //   console.log(... datos);
    // });

    var archivos = document.getElementById("archivos");
    var archivo = archivos.files;

    console.log(archivo);


    cant_file = archivo.length;
    //   valor = true;

    if (cant_file > 0) {
        var filarch = new FormData();

        for (i = 0; i < archivo.length; i++) {
            filarch.append("archivo" + i, archivo[i]);
        }

        filarch.append("opc", "files");
        filarch.append("date", $("#iptDate").val());
        filarch.append("Detalles", $("#Detalles").val());
        filarch.append("modulo", $("#link").val());

        $.ajax({
            url: link,
            type: "POST",
            contentType: false,
            data: filarch,
            processData: false,
            cache: false,

            success: function (data) {


                $("#Detalles").val("");
                $("#link").val("");
                $("#archivos").val("");
                lsArchivos();
            },
        });

    } else {
        alert("No se seleciono ningun archivo");
    }


}

function QuitarArchivo(id) {
    files.removeFile(id);

}

/* -----     [ Subir fichero de excel ]       -----*/
url_link = "ctrl/ctrl-subir-xls.php";

/*  Subir Excel de productos */
function subir_fichero_ventas(idList) {
    var InputFile = document.getElementById("btnSubir");

    var file = InputFile.files;
    var data = new FormData();
    cant_file = file.length;

    for (i = 0; i < file.length; i++) {
        data.append("excel_file" + i, file[i]);
    }

    data.append("idList", idList);
    data.append("UDN", $("#txtUDN").val());
    //   console.log(...data);

    form_data_ajax(data, url_link, "").then((data) => { });

    //   $.ajax({
    //     url: url_link,
    //     contentType: false,
    //     processData: false,
    //     dataType: "json",
    //     type: "POST",
    //     cache: false,
    //     data: data,

    //     beforeSend: function () {
    //       $("#content-data").Loading();
    //     },

    //     success: function (datos) {
    //         $("#content-data").rpt_json_table2({
    //           data: datos,
    //           center: [0, 1, 2],
    //           right: [4, 7],
    //           color_col: [4, 5],
    //           color: "bg-disabled5",
    //         });

    //       // simple_data_table_no("#file-table", 35);
    //       document.getElementById("btnSubir").value = "";
    //     },
    //   });

    //     } else { //  Fin del ciclo if
    //   document.getElementById("btn" + idList).value = "";
    //     }

    // });
}


