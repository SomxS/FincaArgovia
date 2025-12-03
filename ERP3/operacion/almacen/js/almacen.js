let api = 'ctrl/ctrl-almacen.php';
let app;
let zones, categories, areas;

$(async () => {
    const data       = await useFetch({ url: api, data: { opc: "init" } });
          zones      = data.zones;
          categories = data.categories;
          areas      = data.areas;

    app = new App(api, "root");
    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "almacen";
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsMateriales();
    }

    layout() {
        this.primaryLayout({
            parent: 'root',
            id: this.PROJECT_NAME,
            class: 'w-full p-3',
            card: {
                filterBar: { class: 'w-full mb-3', id: 'filterBar' + this.PROJECT_NAME },
                container: { class: 'w-full h-full', id: 'container' + this.PROJECT_NAME }
            }
        });

        $(`#container${this.PROJECT_NAME}`).prepend(`
            <div class="px-4 pt-3 pb-3">
                <h2 class="text-2xl font-semibold">📦 Materiales</h2>
                <p class="text-gray-400">Gestión de materiales del almacén</p>
            </div>
        `);
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "zone",
                    lbl: "Departamento",
                    class: "col-12 col-md-2",
                    data: zones,
                    onchange: 'app.lsMateriales()'
                },
                {
                    opc: "select",
                    id: "category",
                    lbl: "Presentación",
                    class: "col-12 col-md-2",
                    data: categories,
                    onchange: 'app.lsMateriales()'
                },
                {
                    opc: "select",
                    id: "area",
                    lbl: "Área",
                    class: "col-12 col-md-2",
                    data: areas,
                    onchange: 'app.lsMateriales()'
                },
                // {
                //     opc: "input",
                //     id: "search",
                //     lbl: "Buscar",
                //     placeholder: "Código o nombre...",
                //     class: "col-12 col-md-2",
                //     onkeyup: 'app.lsMateriales()'
                // },
                {
                    opc: "button",
                    id: "btnNuevoMaterial",
                    text: "+ Nuevo Material",
                    class: "col-12 col-md-3",
                    color_btn: "primary",
                    onClick: () => this.addMaterial()
                }
            ]
        });
    }

    lsMateriales() {

        const zone     = $(`#filterBar${this.PROJECT_NAME} #zone`).val() || '';
        const category = $(`#filterBar${this.PROJECT_NAME} #category`).val() || '';
        const area     = $(`#filterBar${this.PROJECT_NAME} #area`).val() || '';
     
        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { 
                opc: 'lsMateriales',
            },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: 'tbMateriales',
                theme: 'shadcdn',
                title: 'Lista de productos',
                class:'w-100 lowercase',
                subtitle: 'Productos registrados en el sistema',
                center: [1, 6, 7, 8],
                right: [9],
                f_size:12,
            },
            success: (response) => {
                if (response.total_value) {
                    $(`#container${this.PROJECT_NAME}`).append(`
                        <div class="px-4 py-3 bg-white border-t">
                            <div class="flex justify-end">
                                <span class="text-lg font-bold">Valor Total del Inventario: ${response.total_value}</span>
                            </div>
                        </div>
                    `);
                }
            }
        });
    }

    jsonMaterial() {
        return [
            // {
            //     opc: "label",
            //     id: "lblFoto",
            //     text: "Foto del Material",
            //     class: "col-12 fw-bold text-sm mb-2"
            // },
            // {
            //     opc: "input-file",
            //     id: "rutaImagen",
            //     lbl: "Seleccionar archivo",
            //     class: "col-12 mb-3",
            //     accept: "image/jpeg,image/png,image/gif"
            // },
            // {
            //     opc: "label",
            //     id: "lblInfo",
            //     text: "Formatos: JPG, PNG, GIF. Máximo 5MB",
            //     class: "col-12 text-xs text-gray-500 mb-3"
            // },
            {
                opc: "input",
                id: "CodigoEquipo",
                lbl: "Código *",
                placeholder: "AR-01-29-XXX",
                class: "col-12 col-md-6 mb-3",
                required: true
            },
            {
                opc: "select",
                id: "id_zona",
                lbl: "Departamento *",
                class: "col-12 col-md-6 mb-3",
                data: zones,
                required: true
            },
            {
                opc: "input",
                id: "Equipo",
                lbl: "Nombre del Equipo/Material *",
                placeholder: "Ej: MARCADORES DE COLOR",
                class: "col-12 mb-3",
                required: true
            },
            {
                opc: "select",
                id: "id_categoria",
                lbl: "Presentación *",
                class: "col-12 col-md-6 mb-3",
                data: categories,
                required: true
            },
            {
                opc: "select",
                id: "Area",
                lbl: "Área *",
                class: "col-12 col-md-6 mb-3",
                data: areas,
                required: true
            },
            {
                opc: "input",
                id: "cantidad",
                lbl: "Cantidad *",
                tipo: "numero",
                class: "col-12 col-md-6 mb-3",
                required: true
            },
            {
                opc: "input",
                id: "Costo",
                lbl: "Costo Unitario *",
                tipo: "cifra",
                class: "col-12 col-md-6 mb-3",
                required: true
            }
        ];
    }

    addMaterial() {
        this.createModalForm({
            id: 'formMaterialAdd',
            data: { opc: 'addMaterial' },
            bootbox: {
                title: 'Nuevo Material',
                closeButton: true
            },
            json: this.jsonMaterial(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsMateriales();
                } else {
                    alert({
                        icon: "error",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Ok"
                    });
                }
            }
        });
    }

    async editMaterial(id) {
        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getMaterial",
                id: id
            }
        });

        if (request.status === 200) {
            this.createModalForm({
                id: 'formMaterialEdit',
                data: { opc: 'editMaterial', idAlmacen: id },
                bootbox: {
                    title: 'Editar Material',
                    closeButton: true
                },
                autofill: request.data,
                json: this.jsonMaterial(),
                success: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                        this.lsMateriales();
                    } else {
                        alert({
                            icon: "error",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Ok"
                        });
                    }
                }
            });
        }
    }

    deleteMaterial(id) {
        const row = event.target.closest('tr');
        const codigo = row.querySelectorAll('td')[1]?.innerText || '';
        const nombre = row.querySelectorAll('td')[3]?.innerText || '';

        this.swalQuestion({
            opts: {
                title: "¿Está seguro?",
                html: `Esta acción eliminará permanentemente el material <strong>${codigo} - ${nombre}</strong>. Esta acción no se puede deshacer.`,
                icon: "warning"
            },
            data: {
                opc: "deleteMaterial",
                id: id
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                        this.lsMateriales();
                    } else {
                        alert({
                            icon: "error",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Ok"
                        });
                    }
                }
            }
        });
    }
}
