let api = 'ctrl/ctrl-clientes.php';
let app, clientes, concentrado;

let lsClientes, lsTiposMovimiento, lsMetodosPago, userLevel;
let udn;

const PERMISOS = {
    1: {
        ver_dashboard: true,
        registrar_movimiento: true,
        editar_movimiento: true,
        eliminar_movimiento: true,
        ver_concentrado: false,
        exportar_excel: false,
        ver_administracion: false
    },
    2: {
        ver_dashboard: true,
        registrar_movimiento: false,
        editar_movimiento: false,
        eliminar_movimiento: false,
        ver_concentrado: true,
        exportar_excel: true,
        ver_administracion: false
    },
    3: {
        ver_dashboard: true,
        registrar_movimiento: false,
        editar_movimiento: false,
        eliminar_movimiento: false,
        ver_concentrado: true,
        exportar_excel: true,
        filtrar_udn: true,
        ver_administracion: false
    },
    4: {
        ver_dashboard: true,
        registrar_movimiento: true,
        editar_movimiento: true,
        eliminar_movimiento: true,
        ver_concentrado: true,
        exportar_excel: true,
        ver_administracion: true,
        gestionar_clientes: true
    }
};

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    lsClientes = data.clientes;
    lsTiposMovimiento = data.tiposMovimiento;
    lsMetodosPago = data.metodosPago;
    udn = data.udn;
    userLevel = data.userLevel || 1;

    app = new App(api, "root");
    concentrado = new Concentrado(api, "root");
    clientes = new Clientes(api, "root");

    app.render();
    clientes.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "clientess";
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

    render() {
        this.layout();
        this.filterBar();
        // this.ls();
        // this.renderAdmin()
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

        $(`#container${this.PROJECT_NAME}`).prepend(`
            <div class="px-4 pt-3 pb-3">
                <h2 class="text-2xl font-semibold">👥 Módulo de Clientes</h2>
                <p class="text-gray-400">Gestión de créditos y movimientos de clientes</p>
            </div>
        `);

        const tabs = [
            {
                id: "dashboard",
                tab: "Dashboard",
                class: "mb-1",

                onClick: () => this.ls()
            }
        ];

        if (PERMISOS[userLevel].ver_concentrado) {
            tabs.push({
                id: "concentrado",
                tab: "Concentrado",
                active: true,
                onClick: () => concentrado.render()
            });
        }

        if (PERMISOS[userLevel].ver_administracion) {
            tabs.push({
                id: "administracion",
                tab: "Administración",
                onClick: () => this.renderAdmin()
            });
        }

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            type: "short",
            json: tabs
        });
    }

    renderAdmin() {
        $(`#container-administracion`).html(`
            <div class="p-4">
                <h3 class="text-xl font-bold mb-4">⚙️ Administración de Clientes</h3>
                <div id="admin-clientes-table"></div>
            </div>
        `);

        this.createTable({
            parent: 'admin-clientes-table',
            data: { opc: 'lsClientesAdmin' },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: 'tbClientesAdmin',
                theme: 'corporativo',
                title: '👥 Gestión de Clientes',
                subtitle: 'Administrar clientes del sistema',
                center: [1, 2],
                right: [3]
            }
        });
    }

    filterBar() {

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "input-calendar",
                    class: "col-sm-3 offset-sm-9",
                    id: `calendar${this.PROJECT_NAME}`,
                    lbl: "Fecha de captura:"
                },

            ]
        });

        dataPicker({
            parent: `calendar${this.PROJECT_NAME}`,
            type: 'simple',
            onSelect: () => this.ls()
        });
    }



    async updateTotales(fecha) {
        const data = await useFetch({
            url: this._link,
            data: { opc: 'getTotales', fecha: fecha }
        });

        this.infoCard({
            parent: 'totales-cards',
            theme: 'light',
            json: [
                {
                    title: 'Total de consumos',
                    data: {
                        value: formatPrice(data.totalConsumos || 0),
                        color: 'text-green-600'
                    }
                },
                {
                    title: 'Total pagos en efectivo',
                    data: {
                        value: formatPrice(data.totalPagosEfectivo || 0),
                        color: 'text-blue-600'
                    }
                },
                {
                    title: 'Total pagos en banco',
                    data: {
                        value: formatPrice(data.totalPagosBanco || 0),
                        color: 'text-purple-600'
                    }
                }
            ]
        });
    }

    addMovimiento() {
        if (!this.checkPermiso('registrar_movimiento')) return;

        this.createModalForm({
            id: 'formMovimientoAdd',
            data: { opc: 'addMovimiento' },
            bootbox: {
                title: 'Nuevo Movimiento de Crédito'
            },
            json: this.jsonMovimiento(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.ls();
                } else {
                    alert({ icon: "error", text: response.message });
                }
            }
        });

        this.setupMovimientoLogic();
    }

    async editMovimiento(id) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getMovimiento", id: id }
        });

        this.createModalForm({
            id: 'formMovimientoEdit',
            data: { opc: 'editMovimiento', id: id },
            bootbox: {
                title: 'Editar Movimiento de Crédito'
            },
            autofill: request.data,
            json: this.jsonMovimiento(),
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.ls();
                } else {
                    alert({ icon: "error", text: response.message });
                }
            }
        });

        this.setupMovimientoLogic();
    }

    deleteMovimiento(id) {
        this.swalQuestion({
            opts: {
                title: "¿Está seguro?",
                text: "¿Esta seguro de querer eliminar el movimiento a crédito?",
                icon: "warning"
            },
            data: { opc: "deleteMovimiento", id: id },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message });
                        this.ls();
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
            data: { opc: "getMovimiento", id: id }
        });

        const mov = request.data;

        bootbox.dialog({
            title: 'Detalle de Movimiento de Crédito',
            message: `
                <div class="p-4">
                    <p class="text-sm text-gray-500 mb-4">
                        Actualizado por última vez: ${moment(mov.date_update).format('DD/MM/YYYY HH:mm')}, 
                        Por: ${mov.usuario_nombre}
                    </p>
                    
                    <h3 class="font-bold text-lg mb-2">INFORMACIÓN DEL CLIENTE</h3>
                    <p class="mb-4"><strong>Nombre del cliente:</strong> ${mov.cliente_nombre}</p>
                    
                    <h3 class="font-bold text-lg mb-2">DETALLES DEL MOVIMIENTO</h3>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div><strong>Tipo de movimiento:</strong> ${mov.tipo_movimiento}</div>
                        <div><strong>Método de Pago:</strong> ${mov.metodo_pago}</div>
                    </div>
                    
                    <h3 class="font-bold text-lg mb-2">DESCRIPCIÓN</h3>
                    <p class="mb-4">${mov.descripcion || 'Ninguna'}</p>
                    
                    <h3 class="font-bold text-lg mb-2">RESUMEN FINANCIERO</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span>Deuda actual:</span>
                            <span class="font-bold">${formatPrice(mov.deuda_anterior)}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>${mov.tipo_movimiento === 'consumo' ? 'Consumo a crédito' : 'Pago'}:</span>
                            <span class="font-bold ${mov.tipo_movimiento === 'consumo' ? 'text-red-600' : 'text-green-600'}">
                                ${formatPrice(mov.cantidad)}
                            </span>
                        </div>
                        <div class="flex justify-between border-t pt-2">
                            <span class="font-bold">Nueva deuda:</span>
                            <span class="font-bold text-lg">${formatPrice(mov.deuda_nueva)}</span>
                        </div>
                    </div>
                </div>
            `,
            buttons: {
                ok: {
                    label: 'Cerrar',
                    className: 'btn-primary'
                }
            }
        });
    }

    setupMovimientoLogic() {
        setTimeout(() => {
            $('#cliente_id').on('change', async function () {
                const clienteId = $(this).val();
                if (clienteId) {
                    const data = await useFetch({
                        url: api,
                        data: { opc: 'getDeudaActual', cliente_id: clienteId }
                    });
                    $('#deuda_actual').val(formatPrice(data.deuda));
                }
            });

            $('#tipo_movimiento').on('change', function () {
                const tipo = $(this).val();
                const metodoPago = $('#metodo_pago');

                if (tipo === 'consumo') {
                    metodoPago.val('N/A').prop('disabled', true);
                } else {
                    metodoPago.prop('disabled', false);
                    if (metodoPago.val() === 'N/A') {
                        metodoPago.val('');
                    }
                }
            });
        }, 300);
    }

    jsonMovimiento() {
        return [
            {
                opc: "select",
                id: "cliente_id",
                lbl: "Nombre del Cliente",
                class: "col-12 mb-3",
                data: lsClientes,
                text: "name",
                value: "id",
                required: true
            },
            {
                opc: "input",
                id: "deuda_actual",
                lbl: "Deuda actual",
                tipo: "texto",
                class: "col-12 mb-3",
                readonly: true
            },
            {
                opc: "select",
                id: "tipo_movimiento",
                lbl: "Tipo de movimiento",
                class: "col-12 col-md-6 mb-3",
                data: lsTiposMovimiento,
                required: true
            },
            {
                opc: "select",
                id: "metodo_pago",
                lbl: "Método de pago",
                class: "col-12 col-md-6 mb-3",
                data: lsMetodosPago,
                required: true
            },
            {
                opc: "input",
                id: "cantidad",
                lbl: "Cantidad",
                tipo: "cifra",
                class: "col-12 mb-3",
                required: true,
                onkeyup: "validationInputForNumber('#cantidad')"
            },
            {
                opc: "textarea",
                id: "descripcion",
                lbl: "Descripción (opcional)",
                class: "col-12 mb-3",
                rows: 3
            }
        ];
    }
}

class Clientes extends Templates {

    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Clientes";
    }

    render() {
        this.layout()
        this.filterBar()
        this.updateTotales()
        this.lsClientes()
    }

    layout() {


        this.primaryLayout({
            parent: "container-concentrado",
            id: this.PROJECT_NAME,
            class: 'w-full',
            card: {
                filterBar: { class: 'w-full border-b pb-2', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full my-2 h-full', id: `container${this.PROJECT_NAME}` }
            }
        });

        $(`#container-concentrado`).prepend(`<div id="showCards" class="mb-5"></div>`);

    }

    filterBar() {

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "button",
                    class: "col-sm-2",
                    className: 'w-100',
                    id: "btnConcentrado",
                    icon: 'icon-toggle-off',
                    text: "Concentrado de clientes",
                    color_btn: "outline-info",
                    onClick: () => {
                        $(`#tabs${this.PROJECT_NAME}-concentrado`).click();
                    }
                },
                {
                    opc: "button",
                    class: "col-sm-2",
                    className: 'w-100',
                    id: "btnNuevoMovimiento",
                    text: "Registrar nuevo movimiento",
                    onClick: () => this.addMovimiento()
                },

                {
                    opc: "select",
                    id: "tipoMovimiento",
                    className: 'w-100',
                    lbl: "Tipo de movimiento",
                    class: "col-sm-2",
                    data: [
                        { id: "todos", valor: "Todos" },
                        { id: "consumo", valor: "Consumos" },
                        { id: "abono_parcial", valor: "Abonos parciales" },
                        { id: "pago_total", valor: "Pagos totales" }
                    ],
                    onchange: `clientes.lsClientes()`
                },


            ]
        });

    }

    async updateTotales() {

        const fecha = $(`#calendar${this.PROJECT_NAME}`).val() || moment().format('YYYY-MM-DD');


        const data = await useFetch({
            url: this._link,
            data: { opc: 'getTotales', fecha: fecha }
        });

        this.infoCard({
            parent: 'showCards',
            theme: 'light',
            json: [
                {
                    title: 'Total de consumos',
                    data: {
                        value: formatPrice(data.totalConsumos || 0),
                        color: 'text-green-600'
                    }
                },
                {
                    title: 'Total pagos en efectivo',
                    data: {
                        value: formatPrice(data.totalPagosEfectivo || 0),
                        color: 'text-blue-600'
                    }
                },
                {
                    title: 'Total pagos en banco',
                    data: {
                        value: formatPrice(data.totalPagosBanco || 0),
                        color: 'text-purple-600'
                    }
                }
            ]
        });
    }

    lsClientes() {
        const fecha = $(`#calendar${this.PROJECT_NAME}`).val() || moment().format('YYYY-MM-DD');

        this.createTable({
            parent: "containerClientes",
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'ls', fecha: fecha, },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: 'corporativo',
                title: '📋 Movimientos del día',
                subtitle: `Movimientos registrados el ${moment(fecha).format('DD/MM/YYYY')}`,
                center: [1, 2],
                right: [3]
            },
        });

    }


}

class Concentrado extends App {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    render() {
        this.filterBarConcentrado();
        this.lsConcentrado();
    }

    filterBarConcentrado() {
        $(`#container-concentrado`).html(`
            <div id="filterbar-concentrado" class="mb-3"></div>
            <div id="tabla-concentrado"></div>
        `);

        this.createfilterBar({
            parent: "filterbar-concentrado",
            data: [
                {
                    opc: "input-calendar",
                    class: "col-sm-3",
                    id: "calendarConcentrado",
                    lbl: "Rango de fechas:"
                },
                {
                    opc: "select",
                    id: "udnConcentrado",
                    lbl: "Unidad de Negocio",
                    class: "col-sm-3",
                    data: udn,
                    onchange: `concentrado.lsConcentrado()`
                },
                {
                    opc: "button",
                    class: "col-sm-3",
                    id: "btnExportarExcel",
                    text: "Exportar a Excel",
                    color_btn: "success",
                    onClick: () => this.exportExcel()
                }
            ]
        });

        dataPicker({
            parent: "calendarConcentrado",
            type: 'all',
            onSelect: () => this.lsConcentrado()
        });
    }

    lsConcentrado() {
        const rangePicker = getDataRangePicker("calendarConcentrado");
        const udn = $('#udnConcentrado').val();

        this.createTable({
            parent: 'tabla-concentrado',
            idFilterBar: 'filterbar-concentrado',
            data: { opc: 'lsConcentrado', fi: rangePicker.fi, ff: rangePicker.ff, udn: udn },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: 'tbConcentrado',
                theme: 'corporativo',
                title: '📊 Concentrado de Clientes',
                subtitle: `Período: ${moment(rangePicker.fi).format('DD/MM/YYYY')} - ${moment(rangePicker.ff).format('DD/MM/YYYY')}`,
                center: [1, 2, 3, 4],
                right: [5],
                extends: true
            },
            success: (data) => {
                this.setupExpandableRows(data.ls);
            }
        });
    }

    setupExpandableRows(data) {
        setTimeout(() => {
            $('#tbConcentrado tbody tr').each(function (index) {
                const clienteId = data[index]?.cliente_id;
                if (clienteId) {
                    $(this).css('cursor', 'pointer');
                    $(this).on('click', function () {
                        concentrado.expandCliente(clienteId);
                    });
                }
            });
        }, 500);
    }

    async expandCliente(clienteId) {
        const rangePicker = getDataRangePicker("calendarConcentrado");

        const data = await useFetch({
            url: this._link,
            data: {
                opc: 'getDetalleCliente',
                cliente_id: clienteId,
                fi: rangePicker.fi,
                ff: rangePicker.ff
            }
        });

        bootbox.dialog({
            title: `Detalle de Movimientos - ${data.cliente_nombre}`,
            size: 'large',
            message: `
                <div class="p-4">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Método</th>
                                <th>Cantidad</th>
                                <th>Deuda Nueva</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${data.movimientos.map(m => `
                                <tr>
                                    <td>${moment(m.fecha_captura).format('DD/MM/YYYY')}</td>
                                    <td>${m.tipo_movimiento}</td>
                                    <td>${m.metodo_pago}</td>
                                    <td class="${m.tipo_movimiento === 'consumo' ? 'text-danger' : 'text-success'}">
                                        ${formatPrice(m.cantidad)}
                                    </td>
                                    <td>${formatPrice(m.deuda_nueva)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `,
            buttons: {
                ok: {
                    label: 'Cerrar',
                    className: 'btn-primary'
                }
            }
        });
    }

    async exportExcel() {
        const rangePicker = getDataRangePicker("calendarConcentrado");

        const data = await useFetch({
            url: this._link,
            data: { opc: 'exportExcel', fi: rangePicker.fi, ff: rangePicker.ff }
        });

        if (data.status === 200) {
            window.location.href = data.fileUrl;
            alert({ icon: "success", text: "Archivo Excel generado correctamente" });
        } else {
            alert({ icon: "error", text: "Error al generar el archivo Excel" });
        }
    }
}
