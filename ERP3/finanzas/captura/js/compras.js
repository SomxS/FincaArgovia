let api = 'ctrl/ctrl-compras.php';
let app, concentrado;
let productClass, purchaseType, supplier, methodPay, userLevel, moduleLocked;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init", udn: 1 } });
    productClass = data.productClass;
    purchaseType = data.purchaseType;
    supplier = data.supplier;
    methodPay = data.methodPay;
    userLevel = data.userLevel;
    moduleLocked = data.moduleLocked;

    app = new App(api, "root");
    concentrado = new ConcentradoCompras(api, "root");
    
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
                    tab: "Concentrado",
                    onClick: () => concentrado.render()
                }
            ]
        });

        const lockBadge = moduleLocked 
            ? '<span class="ml-3 px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">🔒 Módulo Bloqueado</span>'
            : '<span class="ml-3 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">🔓 Módulo Activo</span>';

        const lockButton = userLevel === 'Contabilidad' 
            ? `<button onclick="app.toggleModuleLock()" class="ml-3 px-4 py-2 ${moduleLocked ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'} text-white rounded-lg text-sm">
                ${moduleLocked ? '🔓 Desbloquear' : '🔒 Bloquear'} Módulo
               </button>`
            : '';

        $(`#container${this.PROJECT_NAME}`).prepend(`
            <div class="px-4 pt-3 pb-3 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-semibold">🛒 Módulo de Compras ${lockBadge}</h2>
                    <p class="text-gray-400">Gestiona las compras de la unidad de negocio - Nivel: ${userLevel}</p>
                </div>
                <div>
                    ${lockButton}
                </div>
            </div>
        `);
    }

    filterBar() {
        const container = $("#container-compras");
        container.html('<div id="filterbar-compras" class="mb-2"></div><div id="totals-compras" class="mb-3"></div><div id="tabla-compras"></div>');

        const canAdd = ['Captura', 'Gerencia', 'Dirección', 'Contabilidad'].includes(userLevel);

        const filterData = [
            {
                opc: "select",
                id: "purchase_type_id",
                lbl: "Tipo de compra",
                class: "col-12 col-md-3",
                data: [{ id: "", valor: "Todas" }, ...purchaseType],
                onchange: 'app.ls()'
            },
            {
                opc: "select",
                id: "method_pay_id",
                lbl: "Método de pago",
                class: "col-12 col-md-3",
                data: [{ id: "", valor: "Todos" }, ...methodPay],
                onchange: 'app.ls()'
            },
            {
                opc: "input-calendar",
                id: "calendar",
                lbl: "Rango de fechas",
                class: "col-12 col-md-4"
            }
        ];

        if (canAdd) {
            filterData.push({
                opc: "button",
                class: "col-12 col-md-2",
                id: "btnNuevaCompra",
                text: "Nueva Compra",
                onClick: () => this.addPurchase()
            });
        }

        this.createfilterBar({
            parent: "filterbar-compras",
            data: filterData
        });

        dataPicker({
            parent: "calendar",
            onSelect: () => this.ls()
        });
    }

    ls() {
        let rangePicker = getDataRangePicker("calendar");

        this.createTable({
            parent: "tabla-compras",
            idFilterBar: "filterbar-compras",
            data: { 
                opc: "ls", 
                fi: rangePicker.fi, 
                ff: rangePicker.ff,
                udn: 1
            },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbCompras",
                theme: 'corporativo',
                title: 'Lista de Compras',
                subtitle: 'Compras registradas en el sistema',
                center: [1, 4],
                right: [5]
            },
            success: (data) => {
                this.updateTotals(data.totals, data.balance);
            }
        });
    }

    updateTotals(totals, balance) {
        let totalGeneral = 0;
        let totalFondoFijo = 0;
        let totalCorporativo = 0;
        let totalCredito = 0;

        totals.forEach(t => {
            totalGeneral += parseFloat(t.total);
            if (t.type_id == 1) totalFondoFijo = parseFloat(t.total);
            if (t.type_id == 2) totalCorporativo = parseFloat(t.total);
            if (t.type_id == 3) totalCredito = parseFloat(t.total);
        });

        const html = `
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="text-sm text-gray-600">Total Compras</p>
                    <p class="text-2xl font-bold text-gray-800">$${totalGeneral.toFixed(2)}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-green-600">Fondo Fijo</p>
                    <p class="text-2xl font-bold text-green-800">$${totalFondoFijo.toFixed(2)}</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-blue-600">Corporativo</p>
                    <p class="text-2xl font-bold text-blue-800">$${totalCorporativo.toFixed(2)}</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg shadow">
                    <p class="text-sm text-orange-600">Crédito</p>
                    <p class="text-2xl font-bold text-orange-800">$${totalCredito.toFixed(2)}</p>
                </div>
            </div>
            <div class="mt-3 bg-blue-50 p-4 rounded-lg shadow">
                <p class="text-sm text-blue-600">Balance Fondo Fijo</p>
                <div class="grid grid-cols-3 gap-4 mt-2">
                    <div>
                        <p class="text-xs text-gray-600">Saldo Inicial</p>
                        <p class="text-lg font-bold">$${balance.saldo_inicial.toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Salidas</p>
                        <p class="text-lg font-bold text-red-600">-$${balance.salidas.toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Saldo Final</p>
                        <p class="text-lg font-bold text-green-600">$${balance.saldo_final.toFixed(2)}</p>
                    </div>
                </div>
            </div>
        `;

        $("#totals-compras").html(html);
    }

    jsonPurchase() {
        return [
            {
                opc: "label",
                id: "lblProducto",
                text: "Información del Producto",
                class: "col-12 fw-bold text-lg mb-2 border-b p-1"
            },
            {
                opc: "select",
                id: "product_class_id",
                lbl: "Categoría de producto",
                class: "col-12 col-md-6 mb-3",
                data: productClass,
                text: "valor",
                value: "id",
                onchange: "app.loadProducts()"
            },
            {
                opc: "select",
                id: "product_id",
                lbl: "Producto",
                class: "col-12 col-md-6 mb-3",
                data: [],
                text: "valor",
                value: "id"
            },
            {
                opc: "label",
                id: "lblCompra",
                text: "Información de la Compra",
                class: "col-12 fw-bold text-lg mb-2 border-b p-1"
            },
            {
                opc: "select",
                id: "purchase_type_id",
                lbl: "Tipo de compra",
                class: "col-12 col-md-4 mb-3",
                data: purchaseType,
                text: "valor",
                value: "id",
                onchange: "app.toggleConditionalFields()"
            },
            {
                opc: "select",
                id: "supplier_id",
                lbl: "Proveedor",
                class: "col-12 col-md-4 mb-3 d-none",
                data: supplier,
                text: "valor",
                value: "id"
            },
            {
                opc: "select",
                id: "method_pay_id",
                lbl: "Método de pago",
                class: "col-12 col-md-4 mb-3 d-none",
                data: methodPay,
                text: "valor",
                value: "id"
            },
            {
                opc: "input",
                id: "subtotal",
                lbl: "Subtotal",
                tipo: "cifra",
                class: "col-12 col-md-4 mb-3",
                onkeyup: "validationInputForNumber('#subtotal')"
            },
            {
                opc: "input",
                id: "tax",
                lbl: "Impuesto",
                tipo: "cifra",
                class: "col-12 col-md-4 mb-3",
                onkeyup: "validationInputForNumber('#tax')"
            },
            {
                opc: "textarea",
                id: "description",
                lbl: "Descripción",
                rows: 3,
                class: "col-12 mb-3"
            },
            {
                opc: "btn-submit",
                id: "btnGuardarCompra",
                text: "Guardar Compra",
                class: "col-12 col-md-6 offset-md-6"
            }
        ];
    }

    addPurchase() {
        this.createModalForm({
            id: 'formCompraAdd',
            data: { opc: 'addPurchase', udn: 1 },
            bootbox: {
                title: 'Nueva Compra',
            },
            json: this.jsonPurchase(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.ls();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message, btn1: true, btn1Text: "Ok" });
                }
            }
        });

        $("#lblProducto").addClass("border-b p-1");
        $("#lblCompra").addClass("border-b p-1");
    }

    async loadProducts() {
        const classId = $('#product_class_id').val();
        if (!classId) return;

        const products = await useFetch({
            url: this._link,
            data: { opc: 'getProducts', product_class_id: classId }
        });

        $('#product_id').option_select({
            data: products.data || [],
            placeholder: "Seleccionar producto"
        });
    }

    toggleConditionalFields() {
        const typeId = $('#purchase_type_id').val();
        
        $('#supplier_id').closest('.col-12').addClass('d-none');
        $('#method_pay_id').closest('.col-12').addClass('d-none');

        if (typeId == 2) {
            $('#method_pay_id').closest('.col-12').removeClass('d-none');
        } else if (typeId == 3) {
            $('#supplier_id').closest('.col-12').removeClass('d-none');
        }
    }

    async editPurchase(id) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getPurchase", id: id }
        });

        const purchase = request.data;

        this.createModalForm({
            id: 'formCompraEdit',
            data: { opc: 'editPurchase', id: id, udn: 1 },
            bootbox: {
                title: 'Editar Compra',
            },
            autofill: purchase,
            json: this.jsonPurchase(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.ls();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message });
                }
            }
        });

        $("#lblProducto").addClass("border-b p-1");
        $("#lblCompra").addClass("border-b p-1");
    }

    deletePurchase(id) {
        this.swalQuestion({
            opts: {
                title: "¿Desea eliminar esta compra?",
                text: "Esta acción no se puede deshacer.",
                icon: "warning",
            },
            data: {
                opc: "deletePurchase",
                id: id,
                udn: 1
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message });
                        this.ls();
                    } else {
                        alert({ icon: "info", title: "Oops!...", text: response.message });
                    }
                }
            }
        });
    }

    async showDetails(id) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getPurchase", id: id }
        });

        const purchase = request.data;

        this.detailCard({
            parent: "modalDetails",
            title: "Detalle de Compra",
            class: "cols-2",
            data: [
                { text: "Categoría", value: purchase.product_class_id },
                { text: "Producto", value: purchase.product_id },
                { text: "Tipo de compra", value: purchase.purchase_type_id },
                { text: "Subtotal", value: `$${parseFloat(purchase.subtotal).toFixed(2)}` },
                { text: "Impuesto", value: `$${parseFloat(purchase.tax).toFixed(2)}` },
                { text: "Total", value: `$${parseFloat(purchase.total).toFixed(2)}` },
                { type: "observacion", text: "Descripción", value: purchase.description }
            ]
        });

        bootbox.dialog({
            title: "Detalle de Compra",
            message: $("#modalDetails").html(),
            size: 'large',
            closeButton: true
        });
    }

    async toggleModuleLock() {
        const action = moduleLocked ? 'unlockModule' : 'lockModule';
        const confirmText = moduleLocked 
            ? '¿Desea desbloquear el módulo de compras?' 
            : '¿Desea bloquear el módulo de compras? Esto impedirá ediciones y eliminaciones.';

        this.swalQuestion({
            opts: {
                title: "Confirmar acción",
                text: confirmText,
                icon: "warning",
            },
            data: {
                opc: action,
                udn: 1,
                month: moment().format('YYYY-MM')
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message });
                        moduleLocked = !moduleLocked;
                        location.reload();
                    } else {
                        alert({ icon: "error", text: response.message });
                    }
                }
            }
        });
    }

    canEdit() {
        return ['Gerencia', 'Dirección', 'Contabilidad'].includes(userLevel) && !moduleLocked;
    }

    canDelete() {
        return ['Dirección', 'Contabilidad'].includes(userLevel) && !moduleLocked;
    }
}

class ConcentradoCompras extends App {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    render() {
        this.filterBarConcentrado();
        this.lsConcentrado();
    }

    filterBarConcentrado() {
        const container = $("#container-concentrado");
        container.html('<div id="filterbar-concentrado" class="mb-2"></div><div id="balance-concentrado" class="mb-3"></div><div id="tabla-concentrado"></div>');

        this.createfilterBar({
            parent: "filterbar-concentrado",
            data: [
                {
                    opc: "input-calendar",
                    id: "calendarConcentrado",
                    lbl: "Rango de fechas",
                    class: "col-12 col-md-6"
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnBuscar",
                    text: "Buscar",
                    onClick: () => this.lsConcentrado()
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnExportar",
                    text: "Exportar Excel",
                    onClick: () => this.exportExcel()
                }
            ]
        });

        dataPicker({
            parent: "calendarConcentrado",
            onSelect: () => this.lsConcentrado()
        });
    }

    lsConcentrado() {
        let rangePicker = getDataRangePicker("calendarConcentrado");

        this.createTable({
            parent: "tabla-concentrado",
            idFilterBar: "filterbar-concentrado",
            data: { 
                opc: "getConcentrado", 
                fi: rangePicker.fi, 
                ff: rangePicker.ff,
                udn: 1
            },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: "tbConcentrado",
                theme: 'corporativo',
                title: 'Concentrado de Compras',
                subtitle: 'Reporte detallado por fecha y categoría',
                center: [0, 1],
                right: [3, 4, 5]
            },
            success: (data) => {
                this.showBalance(data.balance);
            }
        });
    }

    showBalance(balance) {
        const html = `
            <div class="bg-blue-50 p-4 rounded-lg shadow">
                <p class="text-sm text-blue-600 font-bold mb-2">Balance del Fondo Fijo</p>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-600">Saldo Inicial</p>
                        <p class="text-lg font-bold">$${balance.saldo_inicial.toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Salidas</p>
                        <p class="text-lg font-bold text-red-600">-$${balance.salidas.toFixed(2)}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Saldo Final</p>
                        <p class="text-lg font-bold text-green-600">$${balance.saldo_final.toFixed(2)}</p>
                    </div>
                </div>
            </div>
        `;

        $("#balance-concentrado").html(html);
    }

    exportExcel() {
        alert({ icon: "info", text: "Funcionalidad de exportación en desarrollo" });
    }
}
