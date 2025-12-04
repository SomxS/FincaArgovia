let api = 'ctrl/ctrl-almacen.php';
let main,products;
let zonas, categorias, areas, departamentos, proveedores;

$(async () => {
    const data     = await useFetch({ url: api, data: { opc: "init" } });
    zonas          = data.zonas;
    categorias     = data.categorias;
    areas          = data.areas;
    departamentos  = data.departamentos;
    proveedores    = data.proveedores;

    main = new Main(api, "root");
    main.render();

    // Productos.
    products = new Productos(api, "root");
    products.render();

});

class Main extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "almacenMain";
    }

    render() {
        this.layout();
     
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: "w-full",
            card: {
                filterBar: { class: "w-full", id: `filterBar${this.PROJECT_NAME}` },
                container: { class: "w-full h-full", id: `container${this.PROJECT_NAME}` }
            }
        });


        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            type: "short",
            json: [
                {
                    id: "productos",
                    tab: "Productos",
                    lucideIcon: "package",
                    class: "mb-1",
                    active: true,
                    onClick: () => materiales.render()
                },
                {
                    id: "inventario",
                    tab: "Inventario",
                    lucideIcon: "clipboard-list",
                    onClick: () => inventario.render()
                },
                {
                    id: "movimientos",
                    tab: "Movimientos",
                    lucideIcon: "arrow-left-right",
                    onClick: () => movimientos.render()
                },
                {
                    id: "catalogo",
                    tab: "Catálogo",
                    lucideIcon: "book-open",
                    onClick: () => catalogo.render()
                }
            ]
        });
    }
}



class Productos extends Templates {
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
            parent: 'container-productos',
            id: this.PROJECT_NAME,
            class: 'w-full p-3',
            card: {
                filterBar: { class: 'w-full mb-3', id: 'filterBar' + this.PROJECT_NAME },
                container: { class: 'w-full h-full', id: 'container' + this.PROJECT_NAME }
            }
        });

        $(`#filterBar${this.PROJECT_NAME}`).prepend(`
            <div class="px-2 pb-2">
                <h2 class="text-2xl font-semibold">📦 Productos</h2>
                <p class="text-gray-400">Gestión de productos del almacén</p>
            </div>
        `);
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "zona",
                    lbl: "Negocio",
                    class: "col-12 col-md-2",
                    data: zonas,
                    onchange: 'app.lsMateriales()'
                },
                {
                    opc: "select",
                    id: "categoria",
                    lbl: "Categoría",
                    class: "col-12 col-md-2",
                    data: [{ id: '', valor: 'Todos' }, ...categorias],
                    onchange: 'app.lsMateriales()'
                },
                {
                    opc: "select",
                    id: "area",
                    lbl: "Área",
                    class: "col-12 col-md-2",
                    data: [{ id: '', valor: 'Todos' }, ...areas],
                    onchange: 'app.lsMateriales()'
                },
                {
                    opc: "button",
                    id: "btnNuevoMaterial",
                    text: "Nuevo Producto",
                    class: "col-12 col-md-3",
                    color_btn: "primary",
                    onClick: () => this.addMaterial()
                }
            ]
        });
    }

    lsMateriales() {
        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'lsMateriales' },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: 'tbMateriales',
                theme: 'light',
                class: 'w-100 lowercase',
                // title: 'Lista de productos',
                // subtitle: 'Productos registrados en el sistema',
                striped:true,
                center: [1,4,5,7],
                right: [6],
                f_size: 12
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
            {
                opc: "select",
                id: "id_zona",
                lbl: "Negocio: *",
                class: "col-12 col-md-6 mb-3",
                data: zonas,
                required: true
            },
            {
                opc: "input",
                id: "CodigoEquipo",
                lbl: "Código *",
                placeholder: "AR-01-29-XXX",
                class: "col-12 col-md-6 mb-3",
                // required: true
            },
            {
                opc: "label",
                id: "lblMaterial",
                text: "Información del Material",
                class: "col-12 fw-bold text-lg mb-2  p-1"
            },
            {
                opc: "input",
                id: "Equipo",
                lbl: "Nombre del Equipo/Material *",
                class: "col-12 col-md-6 mb-3",
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
                opc: "select",
                id: "id_categoria",
                lbl: "Categoría *",
                class: "col-12 col-md-6 mb-3",
                data: categorias,
                required: true
            },

            {
                opc: "input",
                id: "Costo",
                lbl: "Costo Unitario *",
                tipo: "cifra",
                class: "col-12 col-md-6 mb-3",
                required: true
            },
            {
                opc: "input",
                id: "PrecioVenta",
                lbl: "Precio de Venta",
                tipo: "cifra",
                class: "col-12 col-md-6 mb-3"
            },
            {
                opc: "input",
                id: "inventario_min",
                lbl: "Inventario Mínimo",
                tipo: "numero",
                class: "col-12 col-md-6 mb-3"
            },
            {
                opc: "textarea",
                id: "Descripcion",
                lbl: "Descripción",
                class: "col-12 mb-3",
                rows: 3
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
            data: { opc: "getMaterial", id: id }
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
        this.swalQuestion({
            opts: {
                title: "¿Está seguro?",
                html: "Esta acción eliminará permanentemente el material. Esta acción no se puede deshacer.",
                icon: "warning"
            },
            data: { opc: "deleteMaterial", id: id },
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
