let api = 'ctrl/ctrl-compras.php';
let app;

let lsProductClass, lsPurchaseType, lsMethodPay, lsSupplier, lsUDN;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    
    lsProductClass = data.productClass;
    lsPurchaseType = data.purchaseType;
    lsMethodPay = data.methodPay;
    lsSupplier = data.supplier;
    lsUDN = data.udn;

    app = new App(api, "root");
    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "compras";
    }

    render() {
        this.layout();
        this.filterBar();
        this.ls();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: 'w-full',
            card: {
                filterBar: { class: 'w-full mb-3', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full h-full', id: `container${this.PROJECT_NAME}` }
            }
        });

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            type: "short",
            json: [
                {
                    id: "compras",
                    tab: "Compras",
                    active: true,
                    onClick: () => this.ls()
                },
                {
                    id: "concentrado",
                    tab: "Concentrado de compras",
                    onClick: () => this.lsConcentrado()
                },
                {
                    id: "archivos",
                    tab: "Archivos",
                    onClick: () => this.lsArchivos()
                }
            ]
        });

        $(`#container${this.PROJECT_NAME}`).prepend(`
            <div class="px-4 pt-3 pb-3">
                <h2 class="text-2xl font-semibold">🛒 Módulo de Compras</h2>
                <p class="text-gray-400">Gestiona las compras de la unidad de negocio</p>
            </div>
        `);
    }

    filterBar() {
        const container = $(`#container-compras`);
        container.html('<div id="filterbar-compras" class="mb-2"></div><div id="totales-compras" class="mb-3"></div><div id="tabla-compras"></div>');

        this.createfilterBar({
            parent: "filterbar-compras",
            data: [
                {
                    opc: "input-calendar",
                    class: "col-12 col-md-4",
                    id: "calendar-compras",
                    lbl: "Rango de fechas"
                },
                {
                    opc: "select",
                    id: "tipo-compra",
                    lbl: "Tipo de compra",
                    class: "col-12 col-md-3",
                    data: [
                        { id: "", valor: "Todas las compras" },
                        ...lsPurchaseType
                    ],
                    text: "valor",
                    value: "id",
                    onchange: "app.ls()"
                },
                {
                    opc: "select",
                    id: "metodo-pago",
                    lbl: "Método de pago",
                    class: "col-12 col-md-3",
                    data: [
                        { id: "", valor: "Todos los métodos" },
                        ...lsMethodPay
                    ],
                    text: "valor",
                    value: "id",
                    onchange: "app.ls()"
                },
                {
                    opc: "button",
                    class: "col-12 col-md-2",
                    id: "btnNuevaCompra",
                    text: "Nueva Compra",
                    onClick: () => this.addCompra()
                }
            ]
        });

        dataPicker({
            parent: "calendar-compras",
            onSelect: () => this.ls()
        });
    }

    ls() {
        const rangePicker = getDataRangePicker("calendar-compras");
        const tipoCompra = $("#tipo-compra").val();
        const metodoPago = $("#metodo-pago").val();

        this.createTable({
            parent: "tabla-compras",
            idFilterBar: "filterbar-compras",
            data: { 
                opc: "ls", 
                fi: rangePicker.fi, 
                ff: rangePicker.ff,
                tipo_compra: tipoCompra,
                metodo_pago: metodoPago
            },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbCompras",
                theme: 'corporativo',
                title: '📊 Lista de Compras',
                subtitle: 'Compras registradas en el sistema',
                center: [1, 2, 3],
                right: [5]
            }
        });
    }

    lsConcentrado() {
        alert({ icon: "info", text: "Funcionalidad en desarrollo" });
    }

    lsArchivos() {
        alert({ icon: "info", text: "Funcionalidad en desarrollo" });
    }

    addCompra() {
        alert({ icon: "info", text: "Funcionalidad en desarrollo" });
    }
}
