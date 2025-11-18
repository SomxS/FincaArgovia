let api = 'ctrl-archivos.php';
let app;

$(async () => {
    app = new Archivos(api, 'root');
    app.init();
});

class Archivos extends Template {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "archivos";
    }

    init() {
        this.render();
    }

    render() {
        this.layout();
        this.filterBar();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            class: "flex p-2",
            id: this.PROJECT_NAME,
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "input-calendar",
                    class: "col-sm-4",
                    id: "calendar" + this.PROJECT_NAME,
                    lbl: "Consultar fecha: ",
                },
                {
                    opc: "btn",
                    class: "col-sm-2",
                    color_btn: "primary",
                    id: "btnBuscar",
                    text: "Buscar",
                    fn: `${this.PROJECT_NAME}.ls()`
                },
            ],
        });

        dataPicker({
            parent: "calendar" + this.PROJECT_NAME,
            onSelect: () => this.ls(),
        });
    }

    ls() {
        this.createTable({
            parent: `container-${this.PROJECT_NAME}`,
            data: [],
            config: {
                responsive: true,
                pagination: true,
            },
        });
    }

    add() {
        this.createModalForm({
            parent: "root",
            id: "modalAdd",
            title: "Agregar archivo",
            fields: [
                { opc: "input", id: "fileName", lbl: "Nombre del archivo" },
                { opc: "file", id: "fileUpload", lbl: "Subir archivo" },
            ],
            onSubmit: (data) => {
                fn_ajax({ opc: "add", ...data }, api).then((response) => {
                    if (response.status === 200) {
                        this.ls();
                    }
                });
            },
        });
    }

    edit(id) {
        fn_ajax({ opc: "get", id }, api).then((data) => {
            this.createModalForm({
                parent: "root",
                id: "modalEdit",
                title: "Editar archivo",
                fields: [
                    { opc: "input", id: "fileName", lbl: "Nombre del archivo", value: data.fileName },
                ],
                onSubmit: (updatedData) => {
                    fn_ajax({ opc: "edit", id, ...updatedData }, api).then((response) => {
                        if (response.status === 200) {
                            this.ls();
                        }
                    });
                },
            });
        });
    }

    cancel(id) {
        swalQuestion({
            title: "¿Está seguro de querer eliminar el archivo?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            confirmButtonText: "Eliminar",
            cancelButtonText: "Cancelar",
            onConfirm: () => {
                fn_ajax({ opc: "cancel", id }, api).then((response) => {
                    if (response.status === 200) {
                        this.ls();
                    }
                });
            },
        });
    }
}