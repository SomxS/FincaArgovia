let app, categorias, areas, zonas;
let api = 'ctrl/ctrl-catalogo.php';

(async () => {
    app = new CatalogoManager(api, "root");
    categorias = new CategoriasManager(api, "root");
    areas = new AreasManager(api, "root");
    zonas = new ZonasManager(api, "root");

    app.render();
    categorias.render();
    areas.render();
    zonas.render();
});

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    zones = data.zones;
    categories = data.categories;
    areas = data.areas;

    app = new App(api, "root");
    app.render();
});



class CatalogoManager extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Catalogo";
    }

    render() {
        this.layout();
        this.layoutHeader();
    }

    layout() {
        this.primaryLayout({
            parent: 'root',
            id: this.PROJECT_NAME,
            class: 'w-full min-h-screen bg-gray-50 p-2',
            card: {
                filterBar: { class: 'w-full', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full h-full', id: `container${this.PROJECT_NAME}` }
            }
        });

        this.layoutTabs();
    }

    layoutHeader() {
        const userName = "Usuario";
        const currentDate = moment().format('dddd, D [de] MMMM [del] YYYY');

        const header = $(`
            <div class="bg-white py-2 mb-3">
                <div class="flex justify-between items-center">
                    <div>
                        <button onclick="window.location.href='../index.php'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition flex items-center gap-2">
                            <i class="icon-arrow-left"></i>
                            Menú principal
                        </button>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-semibold text-gray-800">Bienvenido, ${userName}</p>
                        <p class="text-sm text-gray-500">${currentDate}</p>
                    </div>
                </div>
            </div>
        `);

        $(`#filterBar${this.PROJECT_NAME}`).html(header);
    }

    layoutTabs() {
        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            type: "short",
            json: [
                {
                    id: "categorias",
                    tab: "Categorías",
                    active: true,
                    onClick: () => categorias.lsCategorias()
                },
                {
                    id: "areas",
                    tab: "Áreas",
                    onClick: () => areas.lsAreas()
                },
                {
                    id: "zonas",
                    tab: "Zonas",
                    onClick: () => zonas.lsZonas()
                }
            ]
        });
    }
}

class CategoriasManager extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Categorias";
    }

    render() {
        this.layout();
        this.lsCategorias();
    }

    layout() {
        $(`#container-categorias`).html(`
            <div class="px-6 pt-4 pb-2">
                <h2 class="text-2xl font-semibold text-gray-800">📦 Categorías</h2>
                <p class="text-gray-500">Gestión de categorías de materiales del almacén</p>
            </div>
            <div id="filterbar-categorias" class="px-6 py-3"></div>
            <div id="table-categorias" class="px-6"></div>
        `);

        this.filterBar();
    }

    filterBar() {
        this.createfilterBar({
            parent: "filterbar-categorias",
            data: [
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnAgregarCategoria",
                    className: 'w-full',
                    text: "Agregar Categoría",
                    color_btn: "primary",
                    onClick: () => this.addCategoria()
                }
            ]
        });
    }

    lsCategorias() {
        this.createTable({
            parent: "table-categorias",
            idFilterBar: "filterbar-categorias",
            data: { opc: "lsCategorias" },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbCategorias",
                theme: 'corporativo',
                center: [0, 2],
                right: []
            }
        });
    }

    addCategoria() {
        this.createModalForm({
            id: 'formCategoria',
            data: { opc: 'addCategoria' },
            bootbox: {
                title: 'AGREGAR CATEGORÍA',
                closeButton: true
            },
            json: this.jsonCategoriaForm(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsCategorias();
                } else {
                    alert({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Ok"
                    });
                }
            }
        });
    }

    deleteCategoria(id) {
        this.swalQuestion({
            opts: {
                title: `¿Confirmar eliminación?`,
                html: `¿Estás seguro de eliminar esta categoría?`,
                icon: "warning"
            },
            data: {
                opc: "deleteCategoria",
                id: id
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true
                        });
                        this.lsCategorias();
                    } else {
                        alert({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                            btn1: true
                        });
                    }
                }
            }
        });
    }

    jsonCategoriaForm() {
        return [
            {
                opc: "input",
                id: "nombreCategoria",
                lbl: "Nombre de la categoría",
                type: "text",
                class: "col-12 mb-3",
                placeholder: "Ej: Herramientas",
                required: true
            }
        ];
    }
}

class AreasManager extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Areas";
    }

    render() {
        this.layout();
        this.lsAreas();
    }

    layout() {
        $(`#container-areas`).html(`
            <div class="px-6 pt-4 pb-2">
                <h2 class="text-2xl font-semibold text-gray-800">🏢 Áreas</h2>
                <p class="text-gray-500">Gestión de áreas físicas del almacén</p>
            </div>
            <div id="filterbar-areas" class="px-6 py-3"></div>
            <div id="table-areas" class="px-6"></div>
        `);

        this.filterBar();
    }

    filterBar() {
        this.createfilterBar({
            parent: "filterbar-areas",
            data: [
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnAgregarArea",
                    className: 'w-full',
                    text: "Agregar Área",
                    color_btn: "primary",
                    onClick: () => this.addArea()
                }
            ]
        });
    }

    lsAreas() {
        this.createTable({
            parent: "table-areas",
            idFilterBar: "filterbar-areas",
            data: { opc: "lsAreas" },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbAreas",
                theme: 'corporativo',
                center: [0, 2],
                right: []
            }
        });
    }

    addArea() {
        this.createModalForm({
            id: 'formArea',
            data: { opc: 'addArea' },
            bootbox: {
                title: 'AGREGAR ÁREA',
                closeButton: true
            },
            json: this.jsonAreaForm(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsAreas();
                } else {
                    alert({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Ok"
                    });
                }
            }
        });
    }

    deleteArea(id) {
        this.swalQuestion({
            opts: {
                title: `¿Confirmar eliminación?`,
                html: `¿Estás seguro de eliminar esta área?`,
                icon: "warning"
            },
            data: {
                opc: "deleteArea",
                id: id
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true
                        });
                        this.lsAreas();
                    } else {
                        alert({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                            btn1: true
                        });
                    }
                }
            }
        });
    }

    jsonAreaForm() {
        return [
            {
                opc: "input",
                id: "Nombre_Area",
                lbl: "Nombre del área",
                type: "text",
                class: "col-12 mb-3",
                placeholder: "Ej: Almacén Principal",
                required: true
            }
        ];
    }
}

class ZonasManager extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Zonas";
    }

    render() {
        this.layout();
        this.lsZonas();
    }

    layout() {
        $(`#container-zonas`).html(`
            <div class="px-6 pt-4 pb-2">
                <h2 class="text-2xl font-semibold text-gray-800">📍 Zonas</h2>
                <p class="text-gray-500">Gestión de zonas internas del almacén</p>
            </div>
            <div id="filterbar-zonas" class="px-6 py-3"></div>
            <div id="table-zonas" class="px-6"></div>
        `);

        this.filterBar();
    }

    filterBar() {
        this.createfilterBar({
            parent: "filterbar-zonas",
            data: [
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnAgregarZona",
                    className: 'w-full',
                    text: "Agregar Zona",
                    color_btn: "primary",
                    onClick: () => this.addZona()
                }
            ]
        });
    }

    lsZonas() {
        this.createTable({
            parent: "table-zonas",
            idFilterBar: "filterbar-zonas",
            data: { opc: "lsZonas" },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbZonas",
                theme: 'corporativo',
                center: [0, 2],
                right: []
            }
        });
    }

    addZona() {
        this.createModalForm({
            id: 'formZona',
            data: { opc: 'addZona' },
            bootbox: {
                title: 'AGREGAR ZONA',
                closeButton: true
            },
            json: this.jsonZonaForm(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsZonas();
                } else {
                    alert({
                        icon: "error",
                        title: "Error",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Ok"
                    });
                }
            }
        });
    }

    deleteZona(id) {
        this.swalQuestion({
            opts: {
                title: `¿Confirmar eliminación?`,
                html: `¿Estás seguro de eliminar esta zona?`,
                icon: "warning"
            },
            data: {
                opc: "deleteZona",
                id: id
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true
                        });
                        this.lsZonas();
                    } else {
                        alert({
                            icon: "error",
                            title: "Error",
                            text: response.message,
                            btn1: true
                        });
                    }
                }
            }
        });
    }

    jsonZonaForm() {
        return [
            {
                opc: "input",
                id: "nombreZona",
                lbl: "Nombre de la zona",
                type: "text",
                class: "col-12 mb-3",
                placeholder: "Ej: Zona A",
                required: true
            }
        ];
    }
}

