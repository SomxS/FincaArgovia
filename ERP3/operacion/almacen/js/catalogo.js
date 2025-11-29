let api = 'ctrl/ctrl-catalogo.php';
let app, category, area, zone;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });

    app = new App(api, "root");
    category = new Category(api, "root");
    area = new Area(api, "root");
    zone = new Zone(api, "root");
    
    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "catalogo";
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
            type: "button",
            json: [
                {
                    id: "categorias",
                    tab: "Categorías",
                    class: "mb-1",
                    active: true,
                    onClick: () => category.lsCategory()
                },
                {
                    id: "areas",
                    tab: "Áreas",
                    onClick: () => area.lsArea()
                },
                {
                    id: "zonas",
                    tab: "Zonas",
                    onClick: () => zone.lsZone()
                }
            ]
        });

        category.filterBarCategory();
        area.filterBarArea();
        zone.filterBarZone();

        category.lsCategory();
    }
}

class Category extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "category";
    }

    filterBarCategory() {
        const container = $("#container-categorias");
        container.html('<div id="filterbar-category" class="mb-2"></div><div id="table-category"></div>');

        this.createfilterBar({
            parent: "filterbar-category",
            data: [
                {
                    opc: "select",
                    id: "active",
                    lbl: "Estado",
                    class: "col-12 col-md-2",
                    data: [
                        { id: "1", valor: "Activos" },
                        { id: "0", valor: "Inactivos" }
                    ],
                    onchange: "category.lsCategory()"
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnNewCategory",
                    text: "Nueva Categoría",
                    onClick: () => this.addCategory()
                }
            ]
        });
    }

    lsCategory() {
        this.createTable({
            parent: "table-category",
            idFilterBar: "filterbar-category",
            data: { opc: "lsCategory" },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbCategory",
                theme: "light",
                title: "Lista de Categorías",
                subtitle: "Clasificación de materiales e insumos",
                center: [1, 2, 3]
            }
        });
    }

    addCategory() {
        this.createModalForm({
            id: "formCategoryAdd",
            data: { opc: "addCategory" },
            bootbox: {
                title: "Agregar Categoría",
                closeButton: true
            },
            json: this.jsonCategory(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsCategory();
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

    async editCategory(id) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getCategory", id: id }
        });

        if (request.status === 200) {
            const data = request.data;

            this.createModalForm({
                id: "formCategoryEdit",
                data: { opc: "editCategory", idcategoria: id },
                bootbox: {
                    title: "Editar Categoría",
                    closeButton: true
                },
                autofill: data,
                json: this.jsonCategory(),
                success: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                        this.lsCategory();
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

    statusCategory(id, active) {
        const action = active === 1 ? "desactivar" : "activar";
        const actionTitle = active === 1 ? "Desactivar" : "Activar";

        this.swalQuestion({
            opts: {
                title: `¿${actionTitle} Categoría?`,
                text: `Esta acción ${action}á la categoría`,
                icon: "warning"
            },
            data: {
                opc: "statusCategory",
              
                active: active === 1 ? 0 : 1,
                idcategoria: id,
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true
                        });
                        this.lsCategory();
                    } else {
                        alert({
                            icon: "error",
                            text: response.message,
                            btn1: true
                        });
                    }
                }
            }
        });
    }

    jsonCategory() {
        return [
            {
                opc: "input",
                id: "nombreCategoria",
                lbl: "Nombre de la Categoría",
                tipo: "texto",
                class: "col-12 mb-3",
                required: true
            }
        ];
    }
}

class Area extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "area";
    }

    filterBarArea() {
        const container = $("#container-areas");
        container.html('<div id="filterbar-area" class="mb-2"></div><div id="table-area"></div>');

        this.createfilterBar({
            parent: "filterbar-area",
            data: [
                {
                    opc: "select",
                    id: "active",
                    lbl: "Estado",
                    class: "col-12 col-md-3",
                    data: [
                        { id: "1", valor: "Activos" },
                        { id: "0", valor: "Inactivos" }
                    ],
                    onchange: "area.lsArea()"
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnNewArea",
                    text: "Nueva Área",
                    onClick: () => this.addArea()
                }
            ]
        });
    }

    lsArea() {
        this.createTable({
            parent: "table-area",
            idFilterBar: "filterbar-area",
            data: { opc: "lsArea" },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbArea",
                theme: "light",
                title: "Lista de Áreas",
                subtitle: "Espacios físicos del almacén",
                center: [1, 2, 3]
            }
        });
    }

    addArea() {
        this.createModalForm({
            id: "formAreaAdd",
            data: { opc: "addArea" },
            bootbox: {
                title: "Agregar Área",
                closeButton: true
            },
            json: this.jsonArea(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsArea();
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

    async editArea(id) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getArea", id: id }
        });

        if (request.status === 200) {
            const data = request.data;

            this.createModalForm({
                id: "formAreaEdit",
                data: { opc: "editArea", id: id },
                bootbox: {
                    title: "Editar Área",
                    closeButton: true
                },
                autofill: data,
                json: this.jsonArea(),
                success: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                        this.lsArea();
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

    statusArea(id, active) {
        const action = active === 1 ? "desactivar" : "activar";
        const actionTitle = active === 1 ? "Desactivar" : "Activar";

        this.swalQuestion({
            opts: {
                title: `¿${actionTitle} Área?`,
                text: `Esta acción ${action}á el área`,
                icon: "warning"
            },
            data: {
                opc: "statusArea",
                idArea: id,
                active: active === 1 ? 0 : 1
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true
                        });
                        this.lsArea();
                    } else {
                        alert({
                            icon: "error",
                            text: response.message,
                            btn1: true
                        });
                    }
                }
            }
        });
    }

    jsonArea() {
        return [
            {
                opc: "input",
                id: "Nombre_Area",
                lbl: "Nombre del Área",
                tipo: "texto",
                class: "col-12 mb-3",
                required: true
            }
        ];
    }
}

class Zone extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "zone";
    }

    filterBarZone() {
        const container = $("#container-zonas");
        container.html('<div id="filterbar-zone" class="mb-2"></div><div id="table-zone"></div>');

        this.createfilterBar({
            parent: "filterbar-zone",
            data: [
                {
                    opc: "select",
                    id: "active",
                    lbl: "Estado",
                    class: "col-12 col-md-3",
                    data: [
                        { id: "1", valor: "Activos" },
                        { id: "0", valor: "Inactivos" }
                    ],
                    onchange: "zone.lsZone()"
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnNewZone",
                    text: "Nueva Zona",
                    onClick: () => this.addZone()
                }
            ]
        });
    }

    lsZone() {
        this.createTable({
            parent: "table-zone",
            idFilterBar: "filterbar-zone",
            data: { opc: "lsZone" },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbZone",
                theme: "light",
                title: "Lista de Zonas",
                subtitle: "Subdivisiones internas del almacén",
                center: [1, 2, 3]
            }
        });
    }

    addZone() {
        this.createModalForm({
            id: "formZoneAdd",
            data: { opc: "addZone" },
            bootbox: {
                title: "Agregar Zona",
                closeButton: true
            },
            json: this.jsonZone(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsZone();
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

    async editZone(id) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getZone", id: id }
        });

        if (request.status === 200) {
            const data = request.data;

            this.createModalForm({
                id: "formZoneEdit",
                data: { opc: "editZone", id: id },
                bootbox: {
                    title: "Editar Zona",
                    closeButton: true
                },
                autofill: data,
                json: this.jsonZone(),
                success: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                        this.lsZone();
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

    statusZone(id, active) {
        const action = active === 1 ? "desactivar" : "activar";
        const actionTitle = active === 1 ? "Desactivar" : "Activar";

        this.swalQuestion({
            opts: {
                title: `¿${actionTitle} Zona?`,
                text: `Esta acción ${action}á la zona`,
                icon: "warning"
            },
            data: {
                opc: "statusZone",
                id_zona: id,
                active: active === 1 ? 0 : 1
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true
                        });
                        this.lsZone();
                    } else {
                        alert({
                            icon: "error",
                            text: response.message,
                            btn1: true
                        });
                    }
                }
            }
        });
    }

    jsonZone() {
        return [
            {
                opc: "input",
                id: "nombre_zona",
                lbl: "Nombre de la Zona",
                tipo: "texto",
                class: "col-12 mb-3",
                required: true
            }
        ];
    }
}
