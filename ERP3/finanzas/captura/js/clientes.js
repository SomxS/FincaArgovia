let api = 'ctrl/ctrl-clientes.php';
let app, concentrado;

let lsClientes, lsTiposMovimiento, lsMetodosPago;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    lsClientes = data.clientes;
    lsTiposMovimiento = data.tiposMovimiento;
    lsMetodosPago = data.metodosPago;

    app = new App(api, "root");
    concentrado = new Concentrado(api, "root");
    
    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "clientes";
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

        this.tabLayout({
            parent: `container${this.PROJECT_NAME}`,
            id: `tabs${this.PROJECT_NAME}`,
            theme: "light",
            type: "short",
            json: [
                {
                    id: "dashboard",
                    tab: "Dashboard",
                    class: "mb-1",
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
    }

    filterBar() {

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "input-calendar",
                    class: "col-sm-3",
                    id: `calendar${this.PROJECT_NAME}`,
                    lbl: "Fecha de captura:"
                },
                {
                    opc: "select",
                    id: "tipoMovimiento",
                    lbl: "Tipo de movimiento",
                    class: "col-sm-3",
                    data: [
                        { id: "todos", valor: "Todos" },
                        { id: "consumo", valor: "Consumos" },
                        { id: "abono_parcial", valor: "Abonos parciales" },
                        { id: "pago_total", valor: "Pagos totales" }
                    ],
                    onchange: `app.ls()`
                },
                {
                    opc: "button",
                    class: "col-sm-3",
                    id: "btnNuevoMovimiento",
                    text: "Registrar nuevo movimiento",
                    onClick: () => this.addMovimiento()
                },
                {
                    opc: "button",
                    class: "col-sm-3",
                    id: "btnConcentrado",
                    text: "Concentrado de clientes",
                    color_btn: "secondary",
                    onClick: () => {
                        $(`#tabs${this.PROJECT_NAME}-concentrado`).click();
                    }
                }
            ]
        });

        dataPicker({
            parent: `calendar${this.PROJECT_NAME}`,
            type: 'simple',
            onSelect: () => this.ls()
        });
    }

    ls() {
        const fecha = $(`#calendar${this.PROJECT_NAME}`).val() || moment().format('YYYY-MM-DD');
        const tipoMovimiento = $('#tipoMovimiento').val();

        $(`#container-dashboard`).html(`
            <div id="totales-cards" class="mb-4"></div>
            <div id="tabla-movimientos"></div>
        `);

        this.updateTotales(fecha);

        this.createTable({
            parent: 'tabla-movimientos',
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'ls', fecha: fecha, tipo: tipoMovimiento },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: 'corporativo',
                title: '📋 Movimientos del día',
                subtitle: `Movimientos registrados el ${moment(fecha).format('DD/MM/YYYY')}`,
                center: [1, 2],
                right: [3]
            }
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
            $('#cliente_id').on('change', async function() {
                const clienteId = $(this).val();
                if (clienteId) {
                    const data = await useFetch({
                        url: api,
                        data: { opc: 'getDeudaActual', cliente_id: clienteId }
                    });
                    $('#deuda_actual').val(formatPrice(data.deuda));
                }
            });

            $('#tipo_movimiento').on('change', function() {
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
                    class: "col-sm-4",
                    id: "calendarConcentrado",
                    lbl: "Rango de fechas:"
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

        this.createTable({
            parent: 'tabla-concentrado',
            idFilterBar: 'filterbar-concentrado',
            data: { opc: 'lsConcentrado', fi: rangePicker.fi, ff: rangePicker.ff },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: 'tbConcentrado',
                theme: 'corporativo',
                title: '📊 Concentrado de Clientes',
                subtitle: `Período: ${moment(rangePicker.fi).format('DD/MM/YYYY')} - ${moment(rangePicker.ff).format('DD/MM/YYYY')}`,
                center: [1, 2, 3, 4],
                right: [5]
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
