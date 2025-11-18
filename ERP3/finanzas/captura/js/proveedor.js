let api = 'ctrl/ctrl-proveedor.php';
let app, captura, concentrado;

let lsSuppliers, lsPaymentTypes, lsUDN, userLevel;

let idUDN;

const PERMISOS = {
    1: {
        ver_captura: true,
        registrar_pago: true,
        editar_pago: true,
        eliminar_pago: true,
        ver_concentrado: false,
        exportar_excel: false
    },
    2: {
        ver_captura: false,
        registrar_pago: false,
        editar_pago: false,
        eliminar_pago: false,
        ver_concentrado: true,
        exportar_excel: true
    },
    3: {
        ver_captura: false,
        registrar_pago: false,
        editar_pago: false,
        eliminar_pago: false,
        ver_concentrado: true,
        exportar_excel: true,
        filtrar_udn: true
    },
    4: {
        ver_captura: false,
        registrar_pago: false,
        editar_pago: false,
        eliminar_pago: false,
        ver_concentrado: true,
        exportar_excel: true,
        gestionar_proveedores: true
    }
};

$(async () => {
    idUDN = 4;

    const data = await useFetch({ url: api, data: { opc: "init", udn: idUDN } });
    lsSuppliers    = data.suppliers;
    lsPaymentTypes = data.paymentTypes;
    lsUDN          = data.udn;
    userLevel      = data.userLevel || 1;

    app = new App(api, "root");
    captura = new Captura(api, "root");
    concentrado = new Concentrado(api, "root");

    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "proveedor";
    }

    render() {
        this.layout();
        this.filterBar();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: 'w-full',
            card: {
                filterBar: { class: 'w-full border-b pb-2', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full my-2 h-full', id: `container${this.PROJECT_NAME}` }
            }
        });

        const tabs = [];

        if (PERMISOS[userLevel].ver_captura) {
            tabs.push({
                id: "captura",
                tab: "Captura de Pagos",
                class: "mb-1",
                active: true,
                onClick: () => captura.render()
            });
        }

        if (PERMISOS[userLevel].ver_concentrado) {
            tabs.push({
                id: "concentrado",
                tab: "Concentrado",
                class: "mb-1",
                active: tabs.length === 0,
                onClick: () => concentrado.render()
            });
        }

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            class: '',
            type: "short",
            json: tabs
        });

        $(`#container${this.PROJECT_NAME}`).prepend(`
            <div class="px-4 pt-3 pb-3">
                <h2 class="text-2xl font-semibold">💰 Pagos a Proveedores</h2>
                <p class="text-gray-400">Gestiona los pagos a proveedores de cada unidad de negocio</p>
            </div>
        `);

        if (PERMISOS[userLevel].ver_captura) {
            captura.render();
        } else if (PERMISOS[userLevel].ver_concentrado) {
            concentrado.render();
        }
    }

    filterBar() {
        const filters = [];

        if (PERMISOS[userLevel].filtrar_udn) {
            filters.push({
                opc: "select",
                id: "udn",
                lbl: "Unidad de Negocio",
                class: "col-sm-3",
                data: lsUDN,
                onchange: `app.updateFilters()`
            });
        }

        if (filters.length > 0) {
            this.createfilterBar({
                parent: `filterBar${this.PROJECT_NAME}`,
                data: filters
            });
        }
    }

    updateFilters() {
        if (PERMISOS[userLevel].ver_captura) {
            captura.render();
        } else if (PERMISOS[userLevel].ver_concentrado) {
            concentrado.render();
        }
    }
}

class Captura extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "captura";
    }

    render() {
        this.layoutCaptura();
        this.filterBarCaptura();
        this.lsPayments();
    }

    layoutCaptura() {
        const container = $(`#container-captura`);
        container.html(`
            <div id="summary-cards-${this.PROJECT_NAME}" class="mb-4"></div>
            <div id="filterBar-${this.PROJECT_NAME}" class="mb-3"></div>
            <div id="table-${this.PROJECT_NAME}"></div>
        `);

        this.showSummaryCards();
    }

    async showSummaryCards() {
        const fecha = $(`#filterBar-${this.PROJECT_NAME} #fecha`).val() || moment().format('YYYY-MM-DD');
        const udn = PERMISOS[userLevel].filtrar_udn ? $(`#filterBarproveedor #udn`).val() : idUDN;

        const totales = await useFetch({
            url: api,
            data: { opc: 'getTotales', fecha: fecha, udn: udn }
        });

        this.infoCard({
            parent: `summary-cards-${this.PROJECT_NAME}`,
            theme: "light",
            json: [
                {
                    title: "Total de Pagos",
                    data: {
                        value: formatPrice(totales.totalPagos || 0),
                        color: "text-blue-600"
                    }
                },
                {
                    title: "Pagos Corporativos",
                    data: {
                        value: formatPrice(totales.totalCorporativo || 0),
                        color: "text-blue-800"
                    }
                },
                {
                    title: "Pagos Fondo Fijo",
                    data: {
                        value: formatPrice(totales.totalFondoFijo || 0),
                        color: "text-green-600"
                    }
                }
            ]
        });
    }

    filterBarCaptura() {
        const filters = [
            {
                opc: "input",
                type: "date",
                id: "fecha",
                lbl: "Fecha",
                class: "col-sm-3",
                value: moment().format('YYYY-MM-DD'),
                onchange: `captura.lsPayments(); captura.showSummaryCards()`
            }
        ];

        if (PERMISOS[userLevel].registrar_pago) {
            filters.push({
                opc: "button",
                class: "col-sm-3",
                id: "btnNuevoPago",
                text: "Nuevo Pago",
                onClick: () => this.addPayment()
            });
        }

        this.createfilterBar({
            parent: `filterBar-${this.PROJECT_NAME}`,
            data: filters
        });
    }

    lsPayments() {
        const fecha = $(`#filterBar-${this.PROJECT_NAME} #fecha`).val() || moment().format('YYYY-MM-DD');
        const udn = PERMISOS[userLevel].filtrar_udn ? $(`#filterBarproveedor #udn`).val() : idUDN;

        this.createTable({
            parent: `table-${this.PROJECT_NAME}`,
            idFilterBar: `filterBar-${this.PROJECT_NAME}`,
            data: { opc: 'ls', fecha: fecha, udn: udn },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: 'corporativo',
                title: 'Pagos del Día',
                subtitle: `Pagos registrados para ${moment(fecha).format('DD/MM/YYYY')}`,
                center: [2],
                right: [3]
            }
        });
    }

    addPayment() {
        const fecha = $(`#filterBar-${this.PROJECT_NAME} #fecha`).val() || moment().format('YYYY-MM-DD');
        const udn = PERMISOS[userLevel].filtrar_udn ? $(`#filterBarproveedor #udn`).val() : idUDN;

        this.createModalForm({
            id: 'formPaymentAdd',
            data: { opc: 'addPayment', fecha: fecha, udn_id: udn },
            bootbox: {
                title: 'Nuevo Pago a Proveedor',
                closeButton: true
            },
            json: [
                {
                    opc: "select",
                    id: "supplier_id",
                    lbl: "Proveedor",
                    class: "col-12 mb-3",
                    data: lsSuppliers,
                    required: true
                },
                {
                    opc: "select",
                    id: "payment_type_id",
                    lbl: "Tipo de Pago",
                    class: "col-12 mb-3",
                    data: lsPaymentTypes,
                    required: true
                },
                {
                    opc: "input",
                    id: "amount",
                    lbl: "Cantidad",
                    tipo: "cifra",
                    class: "col-12 mb-3",
                    required: true,
                    onkeyup: "validationInputForNumber('#amount')"
                },
                {
                    opc: "textarea",
                    id: "description",
                    lbl: "Descripción",
                    class: "col-12 mb-3",
                    rows: 3
                }
            ],
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsPayments();
                    this.showSummaryCards();
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

    async editPayment(id) {
        const request = await useFetch({
            url: api,
            data: { opc: "getPayment", id: id }
        });

        if (request.status !== 200) {
            alert({
                icon: "error",
                text: request.message,
                btn1: true,
                btn1Text: "Ok"
            });
            return;
        }

        const payment = request.data;

        this.createModalForm({
            id: 'formPaymentEdit',
            data: { opc: 'editPayment', id: id },
            bootbox: {
                title: 'Editar Pago a Proveedor',
                closeButton: true
            },
            autofill: payment,
            json: [
                {
                    opc: "select",
                    id: "supplier_id",
                    lbl: "Proveedor",
                    class: "col-12 mb-3",
                    data: lsSuppliers,
                    required: true
                },
                {
                    opc: "select",
                    id: "payment_type_id",
                    lbl: "Tipo de Pago",
                    class: "col-12 mb-3",
                    data: lsPaymentTypes,
                    required: true
                },
                {
                    opc: "input",
                    id: "amount",
                    lbl: "Cantidad",
                    tipo: "cifra",
                    class: "col-12 mb-3",
                    required: true,
                    onkeyup: "validationInputForNumber('#amount')"
                },
                {
                    opc: "textarea",
                    id: "description",
                    lbl: "Descripción",
                    class: "col-12 mb-3",
                    rows: 3
                }
            ],
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsPayments();
                    this.showSummaryCards();
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

    deletePayment(id) {
        this.swalQuestion({
            opts: {
                title: "¿Está seguro?",
                text: "¿Desea eliminar este pago a proveedor?",
                icon: "warning"
            },
            data: { opc: "deletePayment", id: id },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                        this.lsPayments();
                        this.showSummaryCards();
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

class Concentrado extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "concentrado";
    }

    render() {
        this.layoutConcentrado();
        this.filterBarConcentrado();
        this.lsBalance();
    }

    layoutConcentrado() {
        const container = $(`#container-concentrado`);
        container.html(`
            <div id="filterBar-${this.PROJECT_NAME}" class="mb-3"></div>
            <div id="table-${this.PROJECT_NAME}"></div>
        `);
    }

    filterBarConcentrado() {
        const filters = [
            {
                opc: "input-calendar",
                id: "dateRange",
                lbl: "Rango de Fechas",
                class: "col-sm-4"
            }
        ];

        if (PERMISOS[userLevel].exportar_excel) {
            filters.push({
                opc: "button",
                class: "col-sm-2",
                id: "btnExportExcel",
                text: "Exportar Excel",
                onClick: () => this.exportExcel()
            });
        }

        this.createfilterBar({
            parent: `filterBar-${this.PROJECT_NAME}`,
            data: filters
        });

        dataPicker({
            parent: "dateRange",
            onSelect: () => this.lsBalance()
        });
    }

    lsBalance() {
        const rangePicker = getDataRangePicker("dateRange");
        const udn = PERMISOS[userLevel].filtrar_udn ? $(`#filterBarproveedor #udn`).val() : idUDN;

        this.createTable({
            parent: `table-${this.PROJECT_NAME}`,
            idFilterBar: `filterBar-${this.PROJECT_NAME}`,
            data: { opc: 'lsBalance', fi: rangePicker.fi, ff: rangePicker.ff, udn: udn },
            coffeesoft: true,
            conf: { datatable: false, pag: 15 },
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: 'corporativo',
                title: 'Balance de Proveedores',
                subtitle: `Del ${moment(rangePicker.fi).format('DD/MM/YYYY')} al ${moment(rangePicker.ff).format('DD/MM/YYYY')}`
            }
        });
    }

    exportExcel() {
        const rangePicker = getDataRangePicker("dateRange");
        const udn = PERMISOS[userLevel].filtrar_udn ? $(`#filterBarproveedor #udn`).val() : idUDN;

        const form = $('<form>', {
            method: 'POST',
            action: api,
            target: '_blank'
        });

        form.append($('<input>', { type: 'hidden', name: 'opc', value: 'exportExcel' }));
        form.append($('<input>', { type: 'hidden', name: 'fi', value: rangePicker.fi }));
        form.append($('<input>', { type: 'hidden', name: 'ff', value: rangePicker.ff }));
        form.append($('<input>', { type: 'hidden', name: 'udn', value: udn }));

        $('body').append(form);
        form.submit();
        form.remove();
    }
}
