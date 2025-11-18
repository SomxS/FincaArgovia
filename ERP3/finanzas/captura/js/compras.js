let api = 'ctrl/ctrl-compras.php';
let app, compras, concentrado;

let lsProductClass, lsProduct, lsPurchaseType, lsSupplier, lsMethodPay, userLevel, udn;

let idUDN;

const PERMISOS = {
    1: {
        ver_dashboard: true,
        registrar_compra: true,
        editar_compra: true,
        eliminar_compra: true,
        ver_concentrado: false,
        exportar_excel: false,
        ver_administracion: false
    },
    2: {
        ver_dashboard: true,
        registrar_compra: false,
        editar_compra: false,
        eliminar_compra: false,
        ver_concentrado: true,
        exportar_excel: true,
        ver_administracion: false
    },
    3: {
        ver_dashboard: true,
        registrar_compra: false,
        editar_compra: false,
        eliminar_compra: false,
        ver_concentrado: true,
        exportar_excel: true,
        filtrar_udn: true,
        ver_administracion: false
    },
    4: {
        ver_dashboard: true,
        registrar_compra: true,
        editar_compra: true,
        eliminar_compra: true,
        ver_concentrado: true,
        exportar_excel: true,
        ver_administracion: true,
        gestionar_productos: true
    }
};

$(async () => {

    // Sustituir variable por cookie o session_storage.
    idUDN = 4;


    const data = await useFetch({ url: api, data: { opc: "init", udn:idUDN } });
    lsProductClass  = data.productClass;
    lsProduct       = data.product;
    lsPurchaseType  = data.purchaseType;
    lsSupplier      = data.supplier;
    lsMethodPay     = data.methodPay;
    udn             = data.udn;
    userLevel       = data.userLevel || 1;

    app = new App(api, "root");
    app.render();
    app.renderDaily();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "compras";
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

        const tabs = [
            {
                id: "dashboard",
                tab: "Dashboard",
                class: "mb-1",
                active: true,
                onClick: () => compras.render()
            }
        ];

        if (PERMISOS[userLevel].ver_concentrado) {
            tabs.push({
                id: "concentrado",
                tab: "Concentrado",
                onClick: () => concentrado.render()
            });
        }

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            type: "button",
            json: tabs
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "div",
                    id: 'lblInfo',
                    class: "col-sm-6",
                    html: `<div class="p-3 text-sm text-gray-600">Módulo de Compras</div>`
                },
                {
                    opc: "input-calendar",
                    class: "col-sm-3 offset-sm-3",
                    id: `calendar${this.PROJECT_NAME}`,
                    lbl: "Fecha de captura:"
                }
            ]
        });

        dataPicker({
            parent: `calendar${this.PROJECT_NAME}`,
            type: 'simple',
            rangeDefault: {
                singleDatePicker: true,
                showDropdowns: true,
                autoApply: true,
                locale: {
                    format: "YYYY-MM-DD"
                }
            },
            onSelect: (start) => {
                const fechaSeleccionada = start.format('YYYY-MM-DD');
                $(`#calendar${this.PROJECT_NAME}`).val(fechaSeleccionada);
                setTimeout(() => {
                    this.renderDaily();
                }, 150);
            }
        });
    }

    async renderDaily() {
        const fecha = $(`#calendar${this.PROJECT_NAME}`).val() || moment().format('YYYY-MM-DD');

        compras = new Compras(api, "root");
        concentrado = new Concentrado(api, "root");
        compras.render();
    }

    checkPermiso(accion) {
        const permisos = PERMISOS[userLevel];
        if (!permisos || !permisos[accion]) {
            alert({
                icon: "warning",
                title: "Acceso Denegado",
                text: "No tiene permisos para realizar esta acción"
            });
            return false;
        }
        return true;
    }
}

class Compras extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Compras";
    }

    render() {
        this.layout();
        this.filterBar();
        this.updateTotales();
        this.lsCompras();
    }

    layout() {
        this.primaryLayout({
            parent: "container-dashboard",
            id: this.PROJECT_NAME,
            class: 'w-full',
            card: {
                filterBar: { class: 'w-full pb-2', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full my-2 h-full', id: `container${this.PROJECT_NAME}` }
            }
        });

        $(`#container-dashboard`).prepend(`<div id="showCards" class="mb-5"></div>`);
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "button",
                    class: "col-sm-3",
                    className: 'w-100',
                    id: "btnConcentrado",
                    icon: 'icon-toggle-off',
                    text: "Concentrado de compras",
                    color_btn: "outline-info",
                    onClick: () => concentrado.render()
                },
                {
                    opc: "button",
                    class: "col-sm-3",
                    className: 'w-100',
                    id: "btnNuevaCompra",
                    text: "Registrar nueva compra",
                    onClick: () => this.addCompra()
                },
                {
                    opc: "select",
                    id: "tipoCompra",
                    className: 'w-100',
                    lbl: "Tipo de compra",
                    class: "col-sm-3",
                    data: [
                        { id: "todos", valor: "Todos" },
                        { id: "1", valor: "Fondo fijo" },
                        { id: "2", valor: "Corporativo" },
                        { id: "3", valor: "Crédito" }
                    ],
                    onchange: `compras.lsCompras()`
                },
                {
                    opc: "select",
                    id: "metodoPago",
                    className: 'w-100',
                    lbl: "Método de pago",
                    class: "col-sm-3",
                    data: lsMethodPay,
                    onchange: `compras.lsCompras()`
                }
            ]
        });
    }

    async updateTotales() {
        const fecha = $(`#calendar${app.PROJECT_NAME}`).val() || moment().format('YYYY-MM-DD');

        const data = await useFetch({
            url: this._link,
            data: { opc: 'getTotales', fecha: fecha }
        });

        this.infoCard({
            parent: 'showCards',
            theme: 'light',
            json: [
                {
                    title: 'Total de compras',
                    data: {
                        value: formatPrice(data.totalCompras || 0),
                        color: 'text-blue-600'
                    }
                },
                {
                    title: 'Compras fondo fijo',
                    data: {
                        value: formatPrice(data.totalFondoFijo || 0),
                        color: 'text-green-600'
                    }
                },
                {
                    title: 'Compras corporativo',
                    data: {
                        value: formatPrice(data.totalCorporativo || 0),
                        color: 'text-purple-600'
                    }
                },
                {
                    title: 'Compras a crédito',
                    data: {
                        value: formatPrice(data.totalCredito || 0),
                        color: 'text-orange-600'
                    }
                }
            ]
        });
    }

    lsCompras() {
        const fecha = $(`#calendar${app.PROJECT_NAME}`).val() || moment().format('YYYY-MM-DD');

        this.createTable({
            parent: "containerCompras",
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'ls', udn_id: idUDN, fecha: fecha },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: 'corporativo',
                center: [2, 3],
                right: [5],
                extends: true
            }
        });
    }

    addCompra() {
        this.createModalForm({
            id: 'formCompraAdd',
            data: { opc: 'addCompra', udn_id: idUDN },
            bootbox: {
                title: 'Nueva Compra'
            },
            json: this.jsonCompra(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsCompras();
                    this.updateTotales();
                } else {
                    alert({ icon: "error", text: response.message });
                }
            }
        });

        this.setupCompraLogic();
    }

    async editCompra(id) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getCompra", id: id }
        });

        this.createModalForm({
            id: 'formCompraEdit',
            data: { opc: 'editCompra', id: id },
            bootbox: {
                title: 'Editar Compra'
            },
            autofill: request.data,
            json: this.jsonCompra(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsCompras();
                    this.updateTotales();
                } else {
                    alert({ icon: "error", text: response.message });
                }
            }
        });

        this.setupCompraLogic();
    }

    deleteCompra(id) {
        const row = event.target.closest('tr');
        const producto = row.querySelectorAll('td')[1]?.innerText || 'Producto';
        const monto = row.querySelectorAll('td')[4]?.innerText || '$0.00';

        this.swalQuestion({
            opts: {
                title: "¿Está seguro?",
                html: `¿Desea eliminar la compra de <strong>${producto}</strong> por un monto de <strong>${monto}</strong>?`,
                icon: "warning"
            },
            data: { opc: "deleteCompra", id: id },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message });
                        this.lsCompras();
                        this.updateTotales();
                    } else {
                        alert({ icon: "error", text: response.message });
                    }
                }
            }
        });
    }

    async viewDetalle(id) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getCompra", id: id }
        });

        const compra = request.data;

        bootbox.dialog({
            title: this.createTitleModal({
                title: 'Detalle de Compra',
                subtitle: `Actualizado por última vez: ${moment(compra.updated_at).format('DD/MM/YYYY HH:mm')}`
            }),
            message: `
            <div class="p-2 text-gray-800 space-y-2 my-3">
                <div>
                    <h3 class="font-semibold text-xs tracking-widest text-gray-500 mb-1">INFORMACIÓN DEL PRODUCTO</h3>
                    <div class="p-3">
                        <p class="text-sm"><strong>Categoría de producto:</strong> ${compra.product_class_name}</p>
                        <p class="text-sm"><strong>Producto:</strong> ${compra.product_name}</p>
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-xs tracking-widest text-gray-500 mb-1">DETALLES DE LA COMPRA</h3>
                    <div class="p-3 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Tipo de compra</p>
                            <p class="text-gray-900 font-medium">${compra.purchase_type_name}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Método de Pago</p>
                            <p class="text-gray-900 font-medium">${compra.method_pay_name || 'N/A'}</p>
                        </div>
                        ${compra.supplier_name ? `
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Proveedor</p>
                            <p class="text-gray-900 font-medium">${compra.supplier_name}</p>
                        </div>
                        ` : ''}
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-xs tracking-widest text-gray-500 mb-1">DESCRIPCIÓN</h3>
                    <div class="p-3 text-sm">
                        ${compra.description || 'Ninguna'}
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold text-xs tracking-widest text-gray-500 mb-1">RESUMEN FINANCIERO</h3>
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span class="font-bold">${formatPrice(compra.subtotal)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Impuesto:</span>
                            <span class="font-bold">${formatPrice(compra.tax)}</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between text-base font-semibold">
                            <span>Total:</span>
                            <span>${formatPrice(compra.total)}</span>
                        </div>
                    </div>
                </div>
            </div>
            `
        });
    }

    setupCompraLogic() {
        setTimeout(() => {
            $('#product_class_id').on('change', async function () {
                const classId = $(this).val();
                if (classId) {
                    const data = await useFetch({
                        url: api,
                        data: { opc: 'getProductsByClass', product_class_id: classId }
                    });
                    $('#product_id').option_select({
                        data: data.products,
                        placeholder: "Seleccionar producto"
                    });
                }
            });

            $('#purchase_type_id').on('change', function () {
                const tipo = $(this).val();
                const metodoPago = $('#method_pay_id');
                const proveedor = $('#supplier_id');

                if (tipo === '1') {
                    metodoPago.val('').prop('disabled', true);
                    proveedor.val('').prop('disabled', true);
                } else if (tipo === '2') {
                    metodoPago.prop('disabled', false);
                    proveedor.val('').prop('disabled', true);
                } else if (tipo === '3') {
                    metodoPago.val('').prop('disabled', true);
                    proveedor.prop('disabled', false);
                }
            });

            $('#subtotal, #tax').on('input', function () {
                const subtotal = parseFloat($('#subtotal').val()) || 0;
                const tax = parseFloat($('#tax').val()) || 0;
                const total = subtotal + tax;
                $('#total').val(total.toFixed(2));
            });
        }, 300);
    }

    jsonCompra() {
        return [
            {
                opc: "select",
                id: "product_class_id",
                lbl: "Categoría de producto",
                class: "col-12 col-md-6 mb-3",
                data: lsProductClass,
                text: "name",
                value: "id",
                required: true
            },
            {
                opc: "select",
                id: "product_id",
                lbl: "Producto",
                class: "col-12 col-md-6 mb-3",
                data: [],
                required: true
            },
            {
                opc: "select",
                id: "purchase_type_id",
                lbl: "Tipo de compra",
                class: "col-12 col-md-6 mb-3",
                data: lsPurchaseType,
                text: "name",
                value: "id",
                required: true
            },
            {
                opc: "select",
                id: "supplier_id",
                lbl: "Proveedor",
                class: "col-12 col-md-6 mb-3",
                data: lsSupplier,
                text: "name",
                value: "id"
            },
            {
                opc: "select",
                id: "method_pay_id",
                lbl: "Método de pago",
                class: "col-12 col-md-6 mb-3",
                data: lsMethodPay,
                text: "name",
                value: "id"
            },
            {
                opc: "input",
                id: "subtotal",
                lbl: "Subtotal",
                tipo: "cifra",
                class: "col-12 col-md-6 mb-3",
                required: true
            },
            {
                opc: "input",
                id: "tax",
                lbl: "Impuesto",
                tipo: "cifra",
                class: "col-12 col-md-6 mb-3",
                required: true
            },
            {
                opc: "input",
                id: "total",
                lbl: "Total",
                tipo: "cifra",
                class: "col-12 col-md-6 mb-3",
                readonly: true
            },
            {
                opc: "textarea",
                id: "description",
                lbl: "Descripción de la compra",
                rows: 3,
                class: "col-12 mb-3"
            }
        ];
    }
}

class Concentrado extends App {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    render() {
        this.filterBarConcentrado();
        this.updateTotalesConcentrado();
        this.lsConcentrado();
    }

    filterBarConcentrado() {
        $(`#container-concentrado`).html(`
            <div id="cards-concentrado" class="mb-4"></div>
            <div id="filterbar-concentrado" class="mb-3"></div>
            <div id="tabla-concentrado"></div>
        `);

        this.createfilterBar({
            parent: "filterbar-concentrado",
            data: [
                {
                    opc: "button",
                    class: "col-sm-3",
                    className: 'w-100',
                    id: "btnConcentrado",
                    icon: 'icon-toggle-on',
                    text: "Regresar a compras",
                    color_btn: "outline-info",
                    onClick: () => compras.render()
                },
                {
                    opc: "input-calendar",
                    class: "col-sm-4",
                    id: "calendarConcentrado",
                    lbl: "Rango de fechas:"
                },
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Unidad de Negocio",
                    class: "col-sm-4",
                    data: udn,
                    onchange: `concentrado.lsConcentrado()`
                }
            ]
        });

        dataPicker({
            parent: "calendarConcentrado",
            type: 'all',
            rangepicker: {
                startDate: moment().subtract(3, 'days'),
                endDate: moment(),
                showDropdowns: true,
                autoApply: true,
                locale: {
                    format: "DD-MM-YYYY"
                },
                ranges: {
                    "Hoy": [moment(), moment()],
                    "Ayer": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                    "Últimos 3 días": [moment().subtract(3, "days"), moment()],
                    "Últimos 7 días": [moment().subtract(6, "days"), moment()],
                    "Mes actual": [moment().startOf("month"), moment()],
                    "Mes anterior": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
                }
            },
            onSelect: () => {
                this.updateTotalesConcentrado();
                this.lsConcentrado();
            }
        });
    }

    async updateTotalesConcentrado() {
        const rangePicker = getDataRangePicker("calendarConcentrado");
        const udnId = $('#udn').val();

        const data = await useFetch({
            url: this._link,
            data: { 
                opc: 'getTotalesConcentrado', 
                fi: rangePicker.fi, 
                ff: rangePicker.ff,
                udn: udnId
            }
        });

        this.infoCard({
            parent: 'cards-concentrado',
            theme: 'light',
            json: [
                {
                    title: 'Saldo inicial fondo fijo',
                    data: {
                        value: formatPrice(data.saldoInicial || 0),
                        color: 'text-gray-700'
                    }
                },
                {
                    title: 'Total compras',
                    data: {
                        value: formatPrice(data.totalCompras || 0),
                        color: 'text-blue-600'
                    }
                },
                {
                    title: 'Salidas fondo fijo',
                    data: {
                        value: formatPrice(data.salidasFondoFijo || 0),
                        color: 'text-orange-600'
                    }
                },
                {
                    title: 'Saldo final fondo fijo',
                    data: {
                        value: formatPrice(data.saldoFinal || 0),
                        color: 'text-gray-700'
                    }
                }
            ]
        });
    }

    async lsConcentrado() {
        const rangePicker = getDataRangePicker("calendarConcentrado");
        const udnId = $('#udn').val();

        const data = await useFetch({
            url: this._link,
            data: { 
                opc: 'lsConcentrado', 
                fi: rangePicker.fi, 
                ff: rangePicker.ff, 
                udn: udnId 
            }
        });

        this.createCoffeTable({
            parent: 'tabla-concentrado',
            id: 'tbConcentrado',
            theme: 'corporativo',
            title: '📊 Concentrado de Compras',
            subtitle: `Del ${rangePicker.fi} al ${rangePicker.ff}`,
            data: {
                thead: data.thead,
                row: data.row
            },
            center: [0, 1, 2, 3, 4, 5],
            right: [6]
        });
    }
}
