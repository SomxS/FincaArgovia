let api = 'ctrl/ctrl-movimientos.php';
let app;

let almacenes, meses, anios;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    almacenes = data.almacenes;
    meses = data.meses;
    anios = data.anios;

    app = new App(api, "root");
    app.render();
});

class App extends Templates {
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
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: "w-full p-3",
            card: {
                filterBar: { class: "w-full mb-3", id: `filterBar${this.PROJECT_NAME}` },
                container: { class: "w-full h-full", id: `container${this.PROJECT_NAME}` }
            }
        });

        $(`#container${this.PROJECT_NAME}`).prepend(`
           
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
                    onchange: "app.lsMovimientos()"
                },
                {
                    opc: "select",
                    id: "anio",
                    lbl: "Año",
                    class: "col-12 col-md-2",
                    data: anios,
                    onchange: "app.lsMovimientos()"
                },
                {
                    opc: "select",
                    id: "almacen",
                    lbl: "Almacén",
                    class: "col-12 col-md-3",
                    data: [
                        { id: "Todos", valor: "Todos los almacenes" },
                        ...almacenes
                    ],
                    onchange: "app.lsMovimientos()"
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
        const container = $("<div>", {
            id: `cards${this.PROJECT_NAME}`,
            class: "grid grid-cols-1 md:grid-cols-4 gap-4 px-2"
        });

        const cards = [
            {
                id: "cardTotal",
                title: "Total Movimientos",
                value: "0",
                icon: "icon-box",
                color: "text-blue-600"
            },
            {
                id: "cardEntradas",
                title: "Entradas",
                value: "0",
                icon: "icon-up-circled",
                color: "text-green-600"
            },
            {
                id: "cardSalidas",
                title: "Salidas",
                value: "0",
                icon: "icon-down-circled",
                color: "text-red-600"
            },
            {
                id: "cardBalance",
                title: "Balance",
                value: "0",
                icon: "icon-chart-line",
                color: "text-purple-600"
            }
        ];

        cards.forEach(card => {
            const cardElement = $("<div>", {
                class: "bg-white rounded-lg border p-4"
            }).html(`
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">${card.title}</p>
                        <p id="${card.id}" class="text-2xl font-bold ${card.color}">0</p>
                    </div>
                    <div class="text-3xl ${card.color}">
                        <i class="${card.icon}"></i>
                    </div>
                </div>
            `);
            container.append(cardElement);
        });

        $(`#summaryCards${this.PROJECT_NAME}`).html(container);
    }

    async lsMovimientos() {
        const mes = $(`#filterBar${this.PROJECT_NAME} #mes`).val();
        const anio = $(`#filterBar${this.PROJECT_NAME} #anio`).val();
        const almacen = $(`#filterBar${this.PROJECT_NAME} #almacen`).val();

        const response = await useFetch({
            url: this._link,
            data: {
                opc: "lsMovimientos",
                mes: mes,
                anio: anio,
                almacen: almacen
            }
        });

        if (response.resumen) {
            $("#cardTotal").text(response.resumen.total);
            $("#cardEntradas").text(response.resumen.entradas);
            $("#cardSalidas").text(response.resumen.salidas);
            
            const balance = response.resumen.balance;
            const balanceColor = balance >= 0 ? "text-green-600" : "text-red-600";
            const balanceSign = balance >= 0 ? "+" : "";
            
            $("#cardBalance")
                .text(balanceSign + balance)
                .removeClass("text-green-600 text-red-600 text-purple-600")
                .addClass(balanceColor);
        }

        this.createTable({
            parent: `tableContainer${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: {
                opc: "lsMovimientos",
                mes: mes,
                anio: anio,
                almacen: almacen
            },
            coffeesoft: true,
            conf: { datatable: true, pag: 5 },
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: "corporativo",
                title: "Historial de Movimientos",
                subtitle: "Mostrando máximo 5 movimientos inicialmente",
                center: [1, 2, 3, 5, 6]
            }
        });
    }
}
