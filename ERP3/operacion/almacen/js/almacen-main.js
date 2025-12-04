let api = 'ctrl/ctrl-almacen.php';
let app, materiales, inventario, movimientos, existencias, catalogo;
let zones, categories, areas, tipoMovimiento, productos, meses, anios;
let apiExistencias = 'ctrl/ctrl-existencias.php';


$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    zones = data.zones || [];
    categories = data.categories || [];
    areas = data.areas || [];
    tipoMovimiento = data.tipoMovimiento || [];
    productos = data.productos || [];
    meses = data.meses || [];
    anios = data.anios || [];

    app = new App(api, "root");
    materiales = new Materiales(api, "root");
    inventario = new Inventario(api, "root");
    movimientos = new Movimientos(api, "root");
    existencias = new Existencias(api, "root");
    catalogo = new Catalogo(api, "root");

    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "almacenMain";
    }

    render() {
        this.layout();
        materiales.render();
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

        $(`#container${this.PROJECT_NAME}`).prepend(`
            <div class="px-4 pt-3 pb-3">
                <h2 class="text-2xl font-semibold">📦 Sistema de Almacén ERP</h2>
                <p class="text-gray-400">Gestión integral de materiales, inventarios y movimientos</p>
            </div>
        `);

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            type: "short",
            json: [
                {
                    id: "existencias",
                    tab: "Existencias",
                    icon: "icon-archive",
                    active: true,
                    onClick: () => existencias.render()
                },
                {
                    id: "materiales",
                    tab: "Materiales",
                    icon: "icon-box",
                    class: "mb-1",
              
                    onClick: () => materiales.render()
                },
                {
                    id: "inventario",
                    tab: "Inventario",
                    icon: "icon-clipboard",
                    onClick: () => inventario.render()
                },
                {
                    id: "movimientos",
                    tab: "Movimientos",
                    icon: "icon-exchange",
                    onClick: () => movimientos.render()
                },
                
                {
                    id: "catalogo",
                    tab: "Catálogo",
                    icon: "icon-book",
                    onClick: () => catalogo.render()
                }
            ]
        });
    }
}


// Materiales

class Materiales extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "materiales";
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsMateriales();
    }

    layout() {
        const container = $("#container-materiales");
        container.html(`
            <div id="filterBar${this.PROJECT_NAME}" class="mb-3"></div>
            <div id="container${this.PROJECT_NAME}"></div>
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
                    onchange: "materiales.lsMateriales()"
                },
                {
                    opc: "select",
                    id: "category",
                    lbl: "Presentación",
                    class: "col-12 col-md-2",
                    data: categories,
                    onchange: "materiales.lsMateriales()"
                },
                {
                    opc: "select",
                    id: "area",
                    lbl: "Área",
                    class: "col-12 col-md-2",
                    data: areas,
                    onchange: "materiales.lsMateriales()"
                },
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
        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: "lsMateriales" },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbMateriales",
                theme: "shadcdn",
                title: "Lista de Materiales",
                subtitle: "Productos registrados en el sistema",
                center: [1, 6, 7, 8],
                right: [9],
                f_size: 12
            }
        });
    }

    jsonMaterial() {
        return [
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
            id: "formMaterialAdd",
            data: { opc: "addMaterial" },
            bootbox: { title: "Nuevo Material", closeButton: true },
            json: this.jsonMaterial(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message, btn1: true });
                    this.lsMateriales();
                } else {
                    alert({ icon: "error", text: response.message, btn1: true });
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
                id: "formMaterialEdit",
                data: { opc: "editMaterial", idAlmacen: id },
                bootbox: { title: "Editar Material", closeButton: true },
                autofill: request.data,
                json: this.jsonMaterial(),
                success: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message, btn1: true });
                        this.lsMateriales();
                    } else {
                        alert({ icon: "error", text: response.message, btn1: true });
                    }
                }
            });
        }
    }

    deleteMaterial(id) {
        this.swalQuestion({
            opts: {
                title: "¿Está seguro?",
                text: "Esta acción eliminará permanentemente el material.",
                icon: "warning"
            },
            data: { opc: "deleteMaterial", id: id },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message, btn1: true });
                        this.lsMateriales();
                    } else {
                        alert({ icon: "error", text: response.message, btn1: true });
                    }
                }
            }
        });
    }
}


// Inventario

class Inventario extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "inventario";
        this.idMovimiento = null;
        this.movimientoData = null;
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsMovimientos();
    }

    layout() {
        const container = $("#container-inventario");
        container.html(`
            <div id="filterBar${this.PROJECT_NAME}" class="mb-3 border rounded p-3"></div>
            <div id="container${this.PROJECT_NAME}"></div>
        `);
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "input-calendar",
                    id: "calendarInventario",
                    lbl: "Rango de Fechas",
                    class: "col-12 col-md-3"
                },
                {
                    opc: "select",
                    id: "tipo_movimiento",
                    lbl: "Tipo de Movimiento",
                    class: "col-12 col-md-2",
                    data: [{ id: "Todos", valor: "Todos" }, ...tipoMovimiento],
                    onchange: "inventario.lsMovimientos()"
                },
                {
                    opc: "button",
                    id: "btnNuevaLista",
                    text: "Nueva Lista",
                    class: "col-12 col-md-3",
                    icono: "icon-plus",
                    color_btn: "primary",
                    onClick: () => this.addMovimiento()
                }
            ]
        });

        dataPicker({
            parent: "calendarInventario",
            onSelect: () => this.lsMovimientos()
        });
    }

    lsMovimientos() {
        let rangePicker = getDataRangePicker("calendarInventario");

        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: "lsInventario", fi: rangePicker.fi, ff: rangePicker.ff },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: "corporativo",
                title: "Lista de Movimientos de Inventario",
                subtitle: "Historial de entradas y salidas",
                center: [1, 2, 3, 4, 5, 6]
            }
        });
    }

    addMovimiento() {
        this.createModalForm({
            id: "formMovimientoAdd",
            data: { opc: "addMovimiento" },
            bootbox: { title: "Nueva Lista de Movimiento", closeButton: true },
            json: [
                {
                    opc: "input",
                    id: "fecha",
                    lbl: "Fecha del Movimiento",
                    type: "date",
                    class: "col-12 mb-3",
                    value: moment().format("YYYY-MM-DD")
                },
                {
                    opc: "select",
                    id: "tipo_movimiento",
                    lbl: "Tipo de Movimiento",
                    class: "col-12 mb-3",
                    data: tipoMovimiento,
                    text: "valor",
                    value: "id"
                }
            ],
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", title: "Lista Creada", text: response.message, btn1: true });
                    this.renderCaptura(response.id_movimiento);
                } else {
                    alert({ icon: "error", text: response.message, btn1: true });
                }
            }
        });
    }

    async renderCaptura(idMovimiento) {
        this.idMovimiento = idMovimiento;

        const movimiento = await useFetch({
            url: this._link,
            data: { opc: "getMovimiento", id: idMovimiento }
        });

        if (movimiento.status === 200) {
            this.movimientoData = movimiento.data;
            this.layoutCaptura();
            this.lsDetalleMovimiento();
        }
    }

    layoutCaptura() {
        const container = $("#container-inventario");
        container.html(`
            <div class="w-full p-4">
                <div class="mb-4 pb-4 border-b">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-semibold">📝 Captura de Productos</h2>
                            <p class="text-gray-400">Folio: ${this.movimientoData.folio} | Tipo: ${this.movimientoData.tipo_movimiento}</p>
                        </div>
                        <div class="flex gap-2">
                            <button id="btnGuardarMovimiento" class="btn btn-success"><i class="icon-floppy"></i> Guardar Lista</button>
                            <button id="btnCancelarCaptura" class="btn btn-secondary"><i class="icon-cancel"></i> Cancelar</button>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-2">
                        <div id="seccionAgregarProducto" class="mb-4"></div>
                        <div id="tablaProductos"></div>
                    </div>
                    <div class="lg:col-span-1">
                        <div id="resumenMovimiento"></div>
                    </div>
                </div>
            </div>
        `);

        $("#btnGuardarMovimiento").on("click", () => this.guardarMovimiento());
        $("#btnCancelarCaptura").on("click", () => this.cancelarCaptura());

        this.renderSeccionAgregar();
        this.updateResumen();
    }

    renderSeccionAgregar() {
        const seccion = $("<div>", { class: "bg-white p-4 rounded-lg shadow mb-4" }).html(`
            <h3 class="text-lg font-semibold mb-3">➕ Agregar Producto</h3>
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label class="form-label">Producto</label>
                    <select id="selectProducto" class="form-control"></select>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" id="inputCantidad" class="form-control" min="1" value="1">
                </div>
                <div class="col-12 col-md-2 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <button id="btnAgregarProducto" class="btn btn-primary w-full"><i class="icon-plus"></i> Agregar</button>
                </div>
            </div>
        `);

        $("#seccionAgregarProducto").html(seccion);
        $("#selectProducto").option_select({ data: productos, placeholder: "Seleccionar producto", select2: true });
        $("#btnAgregarProducto").on("click", () => this.addProducto());
    }

    async addProducto() {
        const idProducto = $("#selectProducto").val();
        const cantidad = parseInt($("#inputCantidad").val());

        if (!idProducto || cantidad <= 0) {
            alert({ icon: "warning", text: "Selecciona un producto y cantidad válida", btn1: true });
            return;
        }

        const response = await useFetch({
            url: this._link,
            data: { opc: "addProductoMovimiento", id_movimiento: this.idMovimiento, id_producto: idProducto, cantidad: cantidad }
        });

        if (response.status === 200) {
            alert({ icon: "success", text: "Producto agregado", btn1: true });
            $("#selectProducto").val(null).trigger("change");
            $("#inputCantidad").val(1);
            this.lsDetalleMovimiento();
            this.updateResumen();
        } else {
            alert({ icon: "error", text: response.message, btn1: true });
        }
    }

    lsDetalleMovimiento() {
        this.createTable({
            parent: "tablaProductos",
            data: { opc: "lsDetalleMovimiento", id_movimiento: this.idMovimiento },
            coffeesoft: true,
            conf: { datatable: false },
            attr: { id: "tbDetalleMovimiento", theme: "light", title: "Productos Agregados", center: [0, 1, 2, 3, 4] },
            success: () => this.updateResumen()
        });
    }

    async updateResumen() {
        const detalles = await useFetch({
            url: this._link,
            data: { opc: "lsDetalleMovimiento", id_movimiento: this.idMovimiento }
        });

        const totalProductos = detalles.ls ? detalles.ls.length : 0;
        const totalUnidades = detalles.ls ? detalles.ls.reduce((sum, item) => sum + parseInt(item.cantidad), 0) : 0;
        const tipoColor = this.movimientoData.tipo_movimiento === "Entrada" ? "text-green-600" : "text-orange-600";
        const tipoIcon = this.movimientoData.tipo_movimiento === "Entrada" ? "↑" : "↓";

        $("#resumenMovimiento").html(`
            <div class="bg-white p-4 rounded-lg shadow sticky top-4">
                <h3 class="text-lg font-semibold mb-4">📋 Resumen</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center pb-2 border-b"><span class="text-gray-600">Folio:</span><span class="font-bold">${this.movimientoData.folio}</span></div>
                    <div class="flex justify-between items-center pb-2 border-b"><span class="text-gray-600">Tipo:</span><span class="font-bold ${tipoColor}">${tipoIcon} ${this.movimientoData.tipo_movimiento}</span></div>
                    <div class="flex justify-between items-center pb-2 border-b"><span class="text-gray-600">Productos:</span><span class="font-bold text-2xl">${totalProductos}</span></div>
                    <div class="flex justify-between items-center pb-2 border-b"><span class="text-gray-600">Total Unidades:</span><span class="font-bold text-2xl">${totalUnidades}</span></div>
                </div>
            </div>
        `);
    }

    async guardarMovimiento() {
        const response = await useFetch({
            url: this._link,
            data: { opc: "guardarMovimiento", id_movimiento: this.idMovimiento }
        });

        if (response.status === 200) {
            alert({ icon: "success", title: "Lista Guardada", text: response.message, btn1: true });
            this.render();
        } else {
            alert({ icon: "error", text: response.message, btn1: true });
        }
    }

    cancelarCaptura() {
        bootbox.confirm({
            title: "¿Cancelar Captura?",
            message: "¿Deseas regresar sin guardar los cambios?",
            buttons: { confirm: { label: "Sí, Cancelar", className: "btn-danger" }, cancel: { label: "No, Continuar", className: "btn-secondary" } },
            callback: (result) => { if (result) this.render(); }
        });
    }

    cancelMovimiento(id, event) {
        const row = event.target.closest("tr");
        const folio = row.querySelectorAll("td")[0]?.innerText || "";

        this.swalQuestion({
            opts: { title: "¿Cancelar Movimiento?", html: `¿Deseas cancelar el movimiento con folio <strong>${folio}</strong>?`, icon: "warning" },
            data: { opc: "cancelMovimiento", id: id },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", title: "Movimiento Cancelado", text: response.message, btn1: true });
                        this.lsMovimientos();
                    } else {
                        alert({ icon: "error", text: response.message, btn1: true });
                    }
                }
            }
        });
    }
}


// Movimientos

class Movimientos extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "movimientos";
    }

    render() {
        this.layout();
        this.filterBar();
        this.summaryCards();
        this.lsMovimientos();
    }

    layout() {
        const container = $("#container-movimientos");
        container.html(`
            <div id="filterBar${this.PROJECT_NAME}" class="mb-3"></div>
            <div id="summaryCards${this.PROJECT_NAME}" class="mb-4"></div>
            <div id="tableContainer${this.PROJECT_NAME}"></div>
        `);
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "mes",
                    lbl: "Mes",
                    class: "col-12 col-md-3",
                    data: meses,
                    onchange: "movimientos.lsMovimientos()"
                },
                {
                    opc: "select",
                    id: "anio",
                    lbl: "Año",
                    class: "col-12 col-md-2",
                    data: anios,
                    onchange: "movimientos.lsMovimientos()"
                }
            ]
        });

        const mesActual = moment().month() + 1;
        const anioActual = moment().year();

        setTimeout(() => {
            $(`#filterBar${this.PROJECT_NAME} #mes`).val(mesActual).trigger("change");
            $(`#filterBar${this.PROJECT_NAME} #anio`).val(anioActual).trigger("change");
        }, 100);
    }

    summaryCards() {
        const cards = [
            { id: "cardTotal", title: "Total Movimientos", icon: "icon-box", color: "text-blue-600" },
            { id: "cardEntradas", title: "Entradas", icon: "icon-up-circled", color: "text-green-600" },
            { id: "cardSalidas", title: "Salidas", icon: "icon-down-circled", color: "text-red-600" },
            { id: "cardBalance", title: "Balance", icon: "icon-chart-line", color: "text-purple-600" }
        ];

        const container = $("<div>", { class: "grid grid-cols-1 md:grid-cols-4 gap-4 px-2" });

        cards.forEach(card => {
            container.append(`
                <div class="bg-white rounded-lg border p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">${card.title}</p>
                            <p id="${card.id}" class="text-2xl font-bold ${card.color}">0</p>
                        </div>
                        <div class="text-3xl ${card.color}"><i class="${card.icon}"></i></div>
                    </div>
                </div>
            `);
        });

        $(`#summaryCards${this.PROJECT_NAME}`).html(container);
    }

    async lsMovimientos() {
        const mes = $(`#filterBar${this.PROJECT_NAME} #mes`).val();
        const anio = $(`#filterBar${this.PROJECT_NAME} #anio`).val();

        const response = await useFetch({
            url: this._link,
            data: { opc: "lsMovimientosResumen", mes: mes, anio: anio }
        });

        if (response.resumen) {
            $("#cardTotal").text(response.resumen.total);
            $("#cardEntradas").text(response.resumen.entradas);
            $("#cardSalidas").text(response.resumen.salidas);

            const balance = response.resumen.balance;
            const balanceColor = balance >= 0 ? "text-green-600" : "text-red-600";
            const balanceSign = balance >= 0 ? "+" : "";

            $("#cardBalance").text(balanceSign + balance).removeClass("text-green-600 text-red-600 text-purple-600").addClass(balanceColor);
        }

        this.createTable({
            parent: `tableContainer${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: "lsMovimientosResumen", mes: mes, anio: anio },
            coffeesoft: true,
            conf: { datatable: true, pag: 10 },
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: "corporativo",
                title: "Historial de Movimientos",
                subtitle: "Registro de entradas y salidas",
                center: [1, 2, 3, 5, 6]
            }
        });
    }
}


// Catalogo

class Catalogo extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "catalogoAlmacen";
    }

    render() {
        this.layout();
        this.lsCategory();
    }

    layout() {
        const container = $("#container-catalogo");
        container.html(`<div id="container${this.PROJECT_NAME}"></div>`);

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            type: "button",
            json: [
                { id: "categorias", tab: "Categorías", class: "mb-1", active: true, onClick: () => this.lsCategory() },
                { id: "areas", tab: "Áreas", onClick: () => this.lsArea() },
                { id: "zonas", tab: "Zonas", onClick: () => this.lsZone() }
            ]
        });

        this.filterBarCategory();
        this.filterBarArea();
        this.filterBarZone();
    }

    // Categorías

    filterBarCategory() {
        const container = $("#container-categorias");
        container.html('<div id="filterbar-category" class="mb-2"></div><div id="table-category"></div>');

        this.createfilterBar({
            parent: "filterbar-category",
            data: [
                { opc: "select", id: "active", lbl: "Estado", class: "col-12 col-md-2", data: [{ id: "1", valor: "Activos" }, { id: "0", valor: "Inactivos" }], onchange: "catalogo.lsCategory()" },
                { opc: "button", class: "col-12 col-md-3", id: "btnNewCategory", text: "Nueva Categoría", onClick: () => this.addCategory() }
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
            attr: { id: "tbCategory", theme: "light", title: "Lista de Categorías", subtitle: "Clasificación de materiales", center: [1, 2, 3] }
        });
    }

    addCategory() {
        this.createModalForm({
            id: "formCategoryAdd",
            data: { opc: "addCategory" },
            bootbox: { title: "Agregar Categoría", closeButton: true },
            json: [{ opc: "input", id: "nombreCategoria", lbl: "Nombre de la Categoría", tipo: "texto", class: "col-12 mb-3", required: true }],
            success: (response) => {
                if (response.status === 200) { alert({ icon: "success", text: response.message, btn1: true }); this.lsCategory(); }
                else { alert({ icon: "error", text: response.message, btn1: true }); }
            }
        });
    }

    async editCategory(id) {
        const request = await useFetch({ url: this._link, data: { opc: "getCategory", id: id } });
        if (request.status === 200) {
            this.createModalForm({
                id: "formCategoryEdit",
                data: { opc: "editCategory", idcategoria: id },
                bootbox: { title: "Editar Categoría", closeButton: true },
                autofill: request.data,
                json: [{ opc: "input", id: "nombreCategoria", lbl: "Nombre de la Categoría", tipo: "texto", class: "col-12 mb-3", required: true }],
                success: (response) => {
                    if (response.status === 200) { alert({ icon: "success", text: response.message, btn1: true }); this.lsCategory(); }
                    else { alert({ icon: "error", text: response.message, btn1: true }); }
                }
            });
        }
    }

    statusCategory(id, active) {
        const actionTitle = active === 1 ? "Desactivar" : "Activar";
        this.swalQuestion({
            opts: { title: `¿${actionTitle} Categoría?`, text: `Esta acción cambiará el estado de la categoría`, icon: "warning" },
            data: { opc: "statusCategory", active: active === 1 ? 0 : 1, idcategoria: id },
            methods: { send: (response) => { if (response.status === 200) { alert({ icon: "success", text: response.message, btn1: true }); this.lsCategory(); } else { alert({ icon: "error", text: response.message, btn1: true }); } } }
        });
    }

    // Áreas

    filterBarArea() {
        const container = $("#container-areas");
        container.html('<div id="filterbar-area" class="mb-2"></div><div id="table-area"></div>');

        this.createfilterBar({
            parent: "filterbar-area",
            data: [
                { opc: "select", id: "active", lbl: "Estado", class: "col-12 col-md-3", data: [{ id: "1", valor: "Activos" }, { id: "0", valor: "Inactivos" }], onchange: "catalogo.lsArea()" },
                { opc: "button", class: "col-12 col-md-3", id: "btnNewArea", text: "Nueva Área", onClick: () => this.addArea() }
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
            attr: { id: "tbArea", theme: "light", title: "Lista de Áreas", subtitle: "Espacios físicos del almacén", center: [1, 2, 3] }
        });
    }

    addArea() {
        this.createModalForm({
            id: "formAreaAdd",
            data: { opc: "addArea" },
            bootbox: { title: "Agregar Área", closeButton: true },
            json: [{ opc: "input", id: "Nombre_Area", lbl: "Nombre del Área", tipo: "texto", class: "col-12 mb-3", required: true }],
            success: (response) => {
                if (response.status === 200) { alert({ icon: "success", text: response.message, btn1: true }); this.lsArea(); }
                else { alert({ icon: "error", text: response.message, btn1: true }); }
            }
        });
    }

    async editArea(id) {
        const request = await useFetch({ url: this._link, data: { opc: "getArea", id: id } });
        if (request.status === 200) {
            this.createModalForm({
                id: "formAreaEdit",
                data: { opc: "editArea", id: id },
                bootbox: { title: "Editar Área", closeButton: true },
                autofill: request.data,
                json: [{ opc: "input", id: "Nombre_Area", lbl: "Nombre del Área", tipo: "texto", class: "col-12 mb-3", required: true }],
                success: (response) => {
                    if (response.status === 200) { alert({ icon: "success", text: response.message, btn1: true }); this.lsArea(); }
                    else { alert({ icon: "error", text: response.message, btn1: true }); }
                }
            });
        }
    }

    statusArea(id, active) {
        const actionTitle = active === 1 ? "Desactivar" : "Activar";
        this.swalQuestion({
            opts: { title: `¿${actionTitle} Área?`, text: `Esta acción cambiará el estado del área`, icon: "warning" },
            data: { opc: "statusArea", idArea: id, active: active === 1 ? 0 : 1 },
            methods: { send: (response) => { if (response.status === 200) { alert({ icon: "success", text: response.message, btn1: true }); this.lsArea(); } else { alert({ icon: "error", text: response.message, btn1: true }); } } }
        });
    }

    // Zonas

    filterBarZone() {
        const container = $("#container-zonas");
        container.html('<div id="filterbar-zone" class="mb-2"></div><div id="table-zone"></div>');

        this.createfilterBar({
            parent: "filterbar-zone",
            data: [
                { opc: "select", id: "active", lbl: "Estado", class: "col-12 col-md-3", data: [{ id: "1", valor: "Activos" }, { id: "0", valor: "Inactivos" }], onchange: "catalogo.lsZone()" },
                { opc: "button", class: "col-12 col-md-3", id: "btnNewZone", text: "Nueva Zona", onClick: () => this.addZone() }
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
            attr: { id: "tbZone", theme: "light", title: "Lista de Zonas", subtitle: "Subdivisiones del almacén", center: [1, 2, 3] }
        });
    }

    addZone() {
        this.createModalForm({
            id: "formZoneAdd",
            data: { opc: "addZone" },
            bootbox: { title: "Agregar Zona", closeButton: true },
            json: [{ opc: "input", id: "nombre_zona", lbl: "Nombre de la Zona", tipo: "texto", class: "col-12 mb-3", required: true }],
            success: (response) => {
                if (response.status === 200) { alert({ icon: "success", text: response.message, btn1: true }); this.lsZone(); }
                else { alert({ icon: "error", text: response.message, btn1: true }); }
            }
        });
    }

    async editZone(id) {
        const request = await useFetch({ url: this._link, data: { opc: "getZone", id: id } });
        if (request.status === 200) {
            this.createModalForm({
                id: "formZoneEdit",
                data: { opc: "editZone", id: id },
                bootbox: { title: "Editar Zona", closeButton: true },
                autofill: request.data,
                json: [{ opc: "input", id: "nombre_zona", lbl: "Nombre de la Zona", tipo: "texto", class: "col-12 mb-3", required: true }],
                success: (response) => {
                    if (response.status === 200) { alert({ icon: "success", text: response.message, btn1: true }); this.lsZone(); }
                    else { alert({ icon: "error", text: response.message, btn1: true }); }
                }
            });
        }
    }

    statusZone(id, active) {
        const actionTitle = active === 1 ? "Desactivar" : "Activar";
        this.swalQuestion({
            opts: { title: `¿${actionTitle} Zona?`, text: `Esta acción cambiará el estado de la zona`, icon: "warning" },
            data: { opc: "statusZone", id_zona: id, active: active === 1 ? 0 : 1 },
            methods: { send: (response) => { if (response.status === 200) { alert({ icon: "success", text: response.message, btn1: true }); this.lsZone(); } else { alert({ icon: "error", text: response.message, btn1: true }); } } }
        });
    }
}
