let api = 'ctrl/ctrl-archivos.php';
let app, files;
let modules, lsudn, counts, userLevel;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    modules   = data.modules;
    lsudn     = data.udn;
    counts    = data.counts;
    userLevel = data.userLevel;

    app   = new App(api, "root");
    files = new Files(api, "root");

    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Captura";
    }

    render() {
        this.layout();
        this.filterBar();
        files.render();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: "w-full",
            card: {
                filterBar: { class: "w-full mb-3 p-3", id: `filterBar${this.PROJECT_NAME}` },
                container: { class: "w-full h-full p-2", id: `container${this.PROJECT_NAME}` }
            }
        });

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            type: "short",
            json: [
                {
                    id: "archivos",
                    tab: "Archivos",
                    icon: 'icon-folder-open-empty',
                    active: true,
                    onClick: () => files.lsFiles()
                },
                {
                    id: "ventas",
                    tab: "Ventas",
                    onClick: () => console.log('Ventas')
                },
                {
                    id: "clientes",
                    tab: "Clientes",
                    onClick: () => console.log('Clientes')
                },
                {
                    id: "compras",
                    tab: "Compras",
                    onClick: () => console.log('Compras')
                },
                {
                    id: "almacen",
                    tab: "Salidas de almacén",
                    onClick: () => console.log('Almacén')
                },
                {
                    id: "costos",
                    tab: "Costos",
                    onClick: () => console.log('Costos')
                },
                {
                    id: "proveedores",
                    tab: "Pagos a proveedor",
                    onClick: () => console.log('Proveedores')
                },
                
            ]
        });
    }

    filterBar() {
        const filterData = [];

        if (userLevel >= 3) {
            filterData.push({
                opc: "select",
                id: "udn",
                lbl: "Unidad de Negocio:",
                class: "col-sm-3 offset-sm-6",
                data: [{ id: "", valor: "Todas las UDN" }, ...lsudn],
                onchange: () => {
                    const activeTab = $(`#tabs${this.PROJECT_NAME} .active`).attr('id');
                    if (activeTab === 'tab-archivos') files.lsFiles();
                }
            });
        }

        filterData.push({
            opc: "input-calendar",
            class: "col-sm-3",
            id: `calendar${this.PROJECT_NAME}`,
            lbl: "Rango de fechas:"
        });

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: filterData
        });

        const dateType = userLevel === 1 ? 'simple' : 'all';
        
        dataPicker({
            parent: `calendar${this.PROJECT_NAME}`,
            type: dateType,
            onSelect: () => {
                files.lsFiles();
                
                // const activeTab = $(`#tabs${this.PROJECT_NAME} .active`).attr('id');
                // if (activeTab === 'tab-archivos') files.lsFiles();
            }
        });
    }
}

class Files extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Files";
    }

    render() {
        this.layoutFiles();
        this.renderTotalCards();
        this.filterBarFiles();
        this.lsFiles();
    }

    layoutFiles() {
        const container = $("<div>", {
            id: "filesContainer",
            class: "w-full"
        });

        const cardsContainer = $("<div>", {
            id: "cardsFiles",
            class: "mb-4"
        });

        const filterContainer = $("<div>", {
            id: "filterBarFiles",
            class: "mb-4"
        });

        const tableContainer = $("<div>", {
            id: "tableFiles"
        });

        container.append(cardsContainer, filterContainer, tableContainer);
        $("#container-archivos").html(container);
    }

    filterBarFiles() {
        this.createfilterBar({
            parent: "filterBarFiles",
            data: [
                {
                    opc: "select",
                    id: "module",
                    lbl: "Módulo:",
                    class: "col-sm-3",
                    data: [
                        { id: "", valor: "Mostrar todos los archivos" },
                        { id: "1", valor: "Ventas" },
                        { id: "2", valor: "Compras" },
                        { id: "3", valor: "Proveedores" },
                        { id: "4", valor: "Almacén" }
                    ],
                    onchange: `files.lsFiles()`
                }
            ]
        });
    }

    renderTotalCards() {
        this.infoCard({
            parent: "cardsFiles",
            theme: "light",
            style: "file",
            class: "mb-4",
            json: [
                {
                    id: "kpiTotal",
                    title: "Archivos totales",
                    bgColor: "bg-green-50",
                    data: { value: counts.total || 0, color: "text-[#8CC63F]" }
                },
                {
                    id: "kpiVentas",
                    title: "Archivos de ventas",
                    bgColor: "bg-blue-50",
                    data: { value: counts.ventas || 0, color: "text-blue-500" }
                },
                {
                    id: "kpiCompras",
                    title: "Archivos de compras",
                    bgColor: "bg-blue-50",
                    data: { value: counts.compras || 0, color: "text-blue-500" }
                },
                {
                    id: "kpiProveedores",
                    title: "Archivos de proveedores",
                    bgColor: "bg-blue-50",
                    data: { value: counts.proveedores || 0, color: "text-blue-500" }
                },
                {
                    id: "kpiAlmacen",
                    title: "Archivos de almacén",
                    bgColor: "bg-blue-50",
                    data: { value: counts.almacen || 0, color: "text-blue-500" }
                }
            ]
        });
    }

    lsFiles() {
        const rangePicker = getDataRangePicker(`calendarCaptura`);
        const udn    = $(`#filterBarCaptura #udn`).val();
        const module = $(`#filterBarFiles #module`).val();

        this.createTable({
            parent: `tableFiles`,
            idFilterBar: `filterBarFiles`,
            data: { 
                opc: 'ls', 
                fi: rangePicker.fi, 
                ff: rangePicker.ff,
                module: module,
                udn: udn
            },
            coffeesoft: true,
            conf: { datatable: true, pag: 25 },
            attr: {
                id: `tbFiles`,
                theme: 'light',
             
                center: [1, 2, 5],
                right: [6]
            }
        });
    }

    async downloadFile(id) {
        try {
            const response = await useFetch({
                url: api,
                data: { opc: 'downloadFile', id: id }
            });

            if (response.status === 200) {
                window.open(response.url, '_blank');
                alert({
                    icon: "success",
                    text: "Descarga iniciada correctamente",
                    btn1: true,
                    btn1Text: "Aceptar"
                });
            } else {
                alert({
                    icon: "error",
                    text: response.message,
                    btn1: true,
                    btn1Text: "Ok"
                });
            }
        } catch (error) {
            alert({
                icon: "error",
                text: "Error de conexión. Intente nuevamente.",
                btn1: true,
                btn1Text: "Ok"
            });
        }
    }

    deleteFile(id) {
        this.swalQuestion({
            opts: {
                title: "¿Está seguro de querer eliminar el archivo?",
                text: "Esta acción no se puede deshacer y se registrará en el sistema.",
                icon: "warning"
            },
            data: {
                opc: "deleteFile",
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
                        this.lsFiles();
                        this.updateCounts();
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

    viewFile(id, path) {
        const fileUrl = '../../../' + path;
        window.open(fileUrl, '_blank');
    }

    async updateCounts() {
        const data = await useFetch({
            url: api,
            data: { opc: "getFileCounts" }
        });

        if (data.status === 200) {
            counts = data.data;
            this.renderTotalCards();
        }
    }

    infoCard(options) {
        const defaults = {
            parent: "root",
            id: "infoCardKPI",
            class: "",
            theme: "light",
            style: "default",
            borderColor: "border-[#8CC63F]",
            json: []
        };
        const opts = Object.assign({}, defaults, options);
        const isDark = opts.theme === "dark";

        const renderCard = (card, i = "") => {
            if (opts.style === "file") {
                const bgColor = card.bgColor || "";
                const box = $("<div>", {
                    id: `${opts.id}_${i}`,
                    class: `${bgColor} border rounded-lg ${card.borderColor || opts.borderColor} p-4`
                });
                const title = $("<p>", {
                    class: "text-sm text-gray-500 mb-3",
                    text: card.title
                });
                const valueContainer = $("<div>", {
                    class: "flex items-center justify-end gap-2"
                });
                const icon = $("<i>", {
                    class: `text-lg ${card.data?.color || "text-[#8CC63F]"}`
                });
                const value = $("<span>", {
                    id: card.id || "",
                    class: `text-2xl font-bold ${card.data?.color || "text-gray-700"}`,
                    text: card.data?.value
                });
                valueContainer.append(icon, value);
                box.append(title, valueContainer);
                return box;
            }

            const cardBase = isDark
                ? "bg-[#1F2A37] text-white border rounded"
                : "text-gray-800 border rounded";
            const titleColor = isDark ? "text-gray-300" : "text-gray-600";

            const box = $("<div>", {
                id: `${opts.id}_${i}`,
                class: `${cardBase} p-4`
            });
            const title = $("<p>", {
                class: `text-sm ${titleColor}`,
                text: card.title
            });
            const value = $("<p>", {
                id: card.id || "",
                class: `text-2xl text-end font-bold ${card.data?.color || "text-gray-800"}`,
                text: card.data?.value
            });
            box.append(title, value);
            return box;
        };

        const container = $("<div>", {
            id: opts.id,
            class: `grid grid-cols-2 md:grid-cols-5 gap-4 ${opts.class}`
        });

        opts.json.forEach((item, i) => {
            container.append(renderCard(item, i));
        });

        $(`#${opts.parent}`).html(container);
    }
}
