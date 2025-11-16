let api = 'ctrl/ctrl-clientes.php';
let app, clientes, concentrado;

let lsClientes, lsTiposMovimiento, lsMetodosPago, userLevel, dailyClosure;
let udn, idDailyClousure;

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
    lsClientes        = data.clientes;
    lsTiposMovimiento = data.tiposMovimiento;
    lsMetodosPago     = data.metodosPago;
    udn               = data.udn;
    userLevel         = data.userLevel || 1;

    app = new App(api, "root");
    app.render();
    app.renderDaily();

});



class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "clientess";
    }

    async renderDaily() {

        const daily = await useFetch({
            url: api,
            data: {
                opc: "DailyClousure",
                date: $(`#calendar${this.PROJECT_NAME}`).val(),
                udn_id: 1
            }
        });

        dailyClosure = daily.data;
        idDailyClousure = dailyClosure?.id || null;

        this.updateFolioDisplay();

        // Clients. 
        concentrado = new Concentrado(api, "root");
        clientes = new Clientes(api, "root");
        clientes.render();
    }

    updateFolioDisplay() {
        if (idDailyClousure) {
            $('#lblFolio').html(`
                <div class="p-3 text-sm">
                    <span class="text-gray-500">Folio:</span> 
                    <span class="font-semibold text-gray-800">${dailyClosure.id}</span>
                </div>
            `);
        } else {
            $('#lblFolio').html(`
                <div class="p-3 text-sm">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-yellow-50 text-yellow-700 border border-yellow-200">
                        <i class="icon-alert-triangle"></i>
                        <span>No se ha creado un registro de cierre diario</span>
                    </span>
                </div>
            `);
        }
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

                onClick: () => this.ls()
            }
        ];

        if (PERMISOS[userLevel].ver_concentrado) {
            tabs.push({
                id: "concentrado",
                tab: "Concentrado",
                active: true,
                // onClick: () => 
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
                    opc: "div",
                    id: 'lblFolio',
                    class: "col-sm-6 ",
                    html: ` 
                    <div class="p-3 text-sm text-gray-400"> Folio: 
                    </div>
                    `
                },
                {
                    opc: "input-calendar",
                    class: "col-sm-3 offset-sm-3",
                    id: `calendar${this.PROJECT_NAME}`,
                    lbl: "Fecha de captura:"
                },

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
                filterBar: { class: 'w-full  pb-2', id: `filterBar${this.PROJECT_NAME}` },
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
                    class: "col-sm-3",
                    className: 'w-100',
                    id: "btnConcentrado",
                    icon: 'icon-toggle-off',
                    text: "Concentrado de clientes",
                    color_btn: "outline-info",
                    onClick: () => {
                        concentrado.render()
                    }
                },
                {
                    opc: "button",
                    class: "col-sm-3",
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
                    class: "col-sm-3",
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
            data: { opc: 'getTotales', fecha: fecha, id: idDailyClousure }
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

    showEmpty(){
        return `
            <div class="flex items-center justify-center py-16">
                <div class="text-center max-w-md">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-100 mb-6">
                    <i class="icon-doc-text-1 text-gray-600 text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-3">No hay cierre diario</h3>
                    <p class="text-gray-500 text-sm">No se ha creado un registro de cierre diario para la fecha seleccionada.</p>
                </div>
            </div>
        `;
    }

    lsClientes() {

        if (!idDailyClousure) {
            $(`#containerClientes`).html(this.showEmpty());
            return;
        }

        this.createTable({
            parent: "containerClientes",
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'ls', daily_closure_id: idDailyClousure },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: 'corporativo',
                center: [2, 3],
                right: [4],
                extends: true
            },
        });

    }

    addMovimiento() {
        // if (!this.checkPermiso('registrar_movimiento')) return;

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
                    this.lsClientes();
                } else {
                    alert({ icon: "error", text: response.message });
                }
            }
        });

        // this.setupMovimientoLogic();
    }

    deleteMovimiento(id) {
        const row = event.target.closest('tr');
        const cliente = row.querySelectorAll('td')[0]?.innerText || 'Cliente';
        const monto = row.querySelectorAll('td')[3]?.innerText || '$0.00';

        this.swalQuestion({
            opts: {
                title: "¿Está seguro?",
                html: `¿Desea eliminar el movimiento de <strong>${cliente}</strong> por un monto de <strong>${monto}</strong>?`,
                icon: "warning"
            },
            data: { opc: "deleteMovimiento", id: id },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message });
                        this.lsClientes();
                    } else {
                        alert({ icon: "error", text: response.message });
                    }
                }
            }
        });
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

    async viewDetalle(id) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getMovimiento", id: id }
        });

        const mov = request.data;



        bootbox.dialog({
            title: this.createTitleModal({
                title: 'Detalle de Movimiento de Crédito',
                subtitle: ` Actualizado por última vez: ${moment(mov.date_update).format('DD/MM/YYYY HH:mm')}`
                // , < br > Por: ${ mov.usuario_nombre }
            }),
            message: `
            <div class="p-2 text-gray-800 space-y-2 my-3">

                <!-- 🧍 Cliente -->
                <div>
                    <h3 class="font-semibold text-xs tracking-widest text-gray-500 mb-1">INFORMACIÓN DEL CLIENTE</h3>
                    <div class= p-3 ">
                        <p class="text-sm"><strong>Nombre:</strong> ${mov.cliente_nombre}</p>
                    </div>
                </div>

                <!-- 📄 Detalles del Movimiento -->
                <div>
                    <h3 class="font-semibold text-xs tracking-widest text-gray-500 mb-1">DETALLES DEL MOVIMIENTO</h3>
                    <div class="p-3 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Tipo de movimiento</p>
                            <p class="text-gray-900 font-medium ">${mov.movement_type}</p>
                        </div>
                        <div>
                            <p class="text-gray-600 text-sm mb-1">Método de Pago</p>
                            <p class="text-gray-900 font-medium ">${mov.method_pay}</p>
                        </div>
                    </div>
                </div>

                <!-- 📝 Descripción -->
                <div>
                    <h3 class="font-semibold text-xs tracking-widest text-gray-500 mb-1">DESCRIPCIÓN</h3>
                    <div class=" p-3  text-sm">
                        ${mov.descripcion || 'Ninguna'}
                    </div>
                </div>

                <!-- 💰 Resumen Financiero -->
                <div>
                    <h3 class="font-semibold text-xs tracking-widest text-gray-500 mb-1">RESUMEN FINANCIERO</h3>
                    <div class="bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-200 space-y-3 text-sm">

                        <div class="flex justify-between">
                            <span>Deuda actual:</span>
                            <span class="font-bold">${formatPrice(mov.deuda_anterior)}</span>
                        </div>

                        <div class="flex justify-between">
                            <span>${mov.movement_type === 'consumo' ? 'Consumo a crédito:' : 'Pago:'}</span>
                            <span class="font-bold ${mov.movement_type === 'consumo' ? 'text-red-600' : 'text-green-600'}">
                                ${formatPrice(mov.amount)}
                            </span>
                        </div>

                        <div class="border-t pt-3 flex justify-between text-base font-semibold">
                            <span>Nueva deuda:</span>
                            <span>${formatPrice(mov.deuda_nueva)}</span>
                        </div>

                    </div>
                </div>

            </div>
            `
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
                id: "customer_id",
                lbl: "Nombre del Cliente",
                class: "col-6 mb-3",
                data: lsClientes,
                text: "name",
                value: "id",
                required: true
            },
            {
                opc: "select",
                id: "movement_type",
                lbl: "Tipo de movimiento",
                class: "col-12 col-md-6 mb-3",
                data: lsTiposMovimiento,
                required: true
            },
            {
                opc: "select",
                id: "method_pay",
                lbl: "Método de pago",
                class: "col-12 col-md-6 mb-3",
                data: lsMetodosPago,
                required: true
            },
            {
                opc: "input",
                id: "amount",
                lbl: "Cantidad",
                tipo: "cifra",
                class: "col-6 mb-3",
                required: true
            },
            // {
            //     opc: "input",
            //     id: "daily_closure_id",
            //     lbl: "ID Cierre Diario",
            //     tipo: "numero",
            //     class: "col-6 mb-3",
            //     readonly: true
            // }
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
                    class: "col-sm-4",
                    className: 'w-100',
                    id: "btnConcentrado",
                    icon: 'icon-toggle-on',
                    text: "Concentrado de clientes",
                    color_btn: "outline-info",
                    onClick: () => {
                        clientes.render()
                    }
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
                },
                // {
                //     opc: "button",
                //     class: "col-sm-3",
                //     id: "btnExportarExcel",
                //     text: "Exportar a Excel",
                //     color_btn: "success",
                //     onClick: () => this.exportExcel()
                // }
            ]
        });

        dataPicker({
            parent: "calendarConcentrado",
            type: 'all',
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
                    title: 'Saldo inicial',
                    data: {
                        value: formatPrice(data.saldoInicial || 0),
                        color: 'text-gray-700'
                    }
                },
                {
                    title: 'Consumos a crédito',
                    data: {
                        value: formatPrice(data.totalConsumos || 0),
                        color: 'text-green-600'
                    }
                },
                {
                    title: 'Pagos y anticipos',
                    data: {
                        value: formatPrice(data.totalPagos || 0),
                        color: 'text-orange-600'
                    }
                },
                {
                    title: 'Saldo final',
                    data: {
                        value: formatPrice(data.saldoFinal || 0),
                        color: 'text-gray-700'
                    }
                }
            ]
        });
    }

    lsConcentrado() {
        const rangePicker = getDataRangePicker("calendarConcentrado");
        const udn = $('#udn').val();

        this.createTable({
            parent: 'tabla-concentrado',
            idFilterBar: 'filterbar-concentrado',
            data: { opc: 'lsConcentrado', fi: rangePicker.fi, ff: rangePicker.ff},
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: 'tbConcentrado',
                theme: 'corporativo',
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
