let api = 'ctrl/ctrl-inventario.php';
let inventario, captura;

let tipoMovimiento, productos;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    tipoMovimiento = data.tipoMovimiento;
    productos = data.productos;

    inventario = new Inventario(api, "root");
    captura = new CapturaMovimiento(api, "root");
    
    inventario.render();
});

class Inventario extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "inventario";
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsMovimientos();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: "w-full p-3",
            card: {
                filterBar: { class: "w-full mb-3 border rounded p-3", id: `filterBar${this.PROJECT_NAME}` },
                container: { class: "w-full h-full", id: `container${this.PROJECT_NAME}` }
            }
        });

     
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "tipo_movimiento",
                    lbl: "Tipo de Movimiento",
                    class: "col-12 col-md-3",
                    data: [
                        { id: "Todos", valor: "Todos" },
                        ...tipoMovimiento
                    ],
                    onchange: "inventario.lsMovimientos()"
                },
                {
                    opc: "input-calendar",
                    id: "calendar",
                    lbl: "Rango de Fechas",
                    class: "col-12 col-md-4"
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
            parent: "calendar",
            onSelect: () => this.lsMovimientos()
        });
    }

    lsMovimientos() {
        let rangePicker = getDataRangePicker("calendar");

        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { 
                opc: "lsMovimientos", 
                fi: rangePicker.fi, 
                ff: rangePicker.ff 
            },
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
            bootbox: {
                title: "Nueva Lista de Movimiento",
                closeButton: true
            },
            json: this.jsonMovimiento(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        title: "Lista Creada",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    
                    captura.render(response.id_movimiento);
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

    cancelMovimiento(id, event) {
        const row = event.target.closest('tr');
        const folio = row.querySelectorAll('td')[0]?.innerText || '';

        this.swalQuestion({
            opts: {
                title: "¿Cancelar Movimiento?",
                html: `¿Deseas cancelar el movimiento con folio <strong>${folio}</strong>?<br>
                       Esta acción revertirá los cambios de stock realizados.`,
                icon: "warning"
            },
            data: { 
                opc: "cancelMovimiento", 
                id: id 
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            title: "Movimiento Cancelado",
                            text: response.message,
                            btn1: true
                        });
                        this.lsMovimientos();
                    } else {
                        alert({
                            icon: "error",
                            text: response.message,
                            btn1: true
                        });
                    }
                }
            }
        });
    }

    jsonMovimiento() {
        return [
            {
                opc: "input",
                id: "fecha",
                lbl: "Fecha del Movimiento",
                type: "date",
                class: "col-12 mb-3",
                value: moment().format('YYYY-MM-DD')
            },
            {
                opc: "select",
                id: "tipo_movimiento",
                lbl: "Tipo de Movimiento",
                class: "col-12 mb-3",
                data: tipoMovimiento,
                text: "valor",
                value: "id"
            },
    
        ];
    }
}


class CapturaMovimiento extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "capturaInventario";
        this.idMovimiento = null;
    }

    async render(idMovimiento) {
        this.idMovimiento = idMovimiento;
        
        const movimiento = await useFetch({
            url: this._link,
            data: { opc: "getMovimiento", id: idMovimiento }
        });

        if (movimiento.status === 200) {
            this.movimientoData = movimiento.data;
            this.layout();
            this.lsDetalleMovimiento();
        }
    }

    layout() {
        const container = $("<div>", {
            id: this.PROJECT_NAME,
            class: "w-full p-4"
        });

        const header = $("<div>", {
            class: "mb-4 pb-4 border-b"
        }).html(`
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-semibold">📝 Captura de Productos</h2>
                    <p class="text-gray-400">Folio: ${this.movimientoData.folio} | Tipo: ${this.movimientoData.tipo_movimiento}</p>
                </div>
                <div class="flex gap-2">
                    <button id="btnGuardarMovimiento" class="btn btn-success">
                        <i class="icon-floppy"></i> Guardar Lista
                    </button>
                    <button id="btnCancelarCaptura" class="btn btn-secondary">
                        <i class="icon-cancel"></i> Cancelar
                    </button>
                </div>
            </div>
        `);

        const mainContent = $("<div>", {
            class: "grid grid-cols-1 lg:grid-cols-3 gap-4"
        });

        const leftSection = $("<div>", {
            class: "lg:col-span-2"
        }).html(`
            <div id="seccionAgregarProducto" class="mb-4"></div>
            <div id="tablaProductos"></div>
        `);

        const rightSection = $("<div>", {
            class: "lg:col-span-1"
        }).html(`
            <div id="resumenMovimiento"></div>
        `);

        mainContent.append(leftSection, rightSection);
        container.append(header, mainContent);
        $("#root").html(container);

        $("#btnGuardarMovimiento").on("click", () => this.guardarMovimiento());
        $("#btnCancelarCaptura").on("click", () => this.cancelarCaptura());

        this.renderSeccionAgregar();
        this.updateResumen();
    }

    renderSeccionAgregar() {
        const seccion = $("<div>", {
            class: "bg-white p-4 rounded-lg shadow mb-4"
        }).html(`
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
                    <button id="btnAgregarProducto" class="btn btn-primary w-full">
                        <i class="icon-plus"></i> Agregar
                    </button>
                </div>
            </div>
        `);

        $("#seccionAgregarProducto").html(seccion);

        $("#selectProducto").option_select({
            data: productos,
            placeholder: "Seleccionar producto",
            select2: true
        });

        $("#btnAgregarProducto").on("click", () => this.addProducto());
    }

    async addProducto() {
        const idProducto = $("#selectProducto").val();
        const cantidad = parseInt($("#inputCantidad").val());

        if (!idProducto) {
            alert({
                icon: "warning",
                text: "Selecciona un producto",
                btn1: true
            });
            return;
        }

        if (cantidad <= 0) {
            alert({
                icon: "warning",
                text: "La cantidad debe ser mayor a cero",
                btn1: true
            });
            return;
        }

        const response = await useFetch({
            url: this._link,
            data: {
                opc: "addProductoMovimiento",
                id_movimiento: this.idMovimiento,
                id_producto: idProducto,
                cantidad: cantidad
            }
        });

        if (response.status === 200) {
            alert({
                icon: "success",
                text: "Producto agregado",
                btn1: true
            });

            $("#selectProducto").val(null).trigger("change");
            $("#inputCantidad").val(1);

            this.lsDetalleMovimiento();
            this.updateResumen();
        } else {
            alert({
                icon: "error",
                text: response.message,
                btn1: true
            });
        }
    }

    lsDetalleMovimiento() {
        this.createTable({
            parent: "tablaProductos",
            data: { 
                opc: "lsDetalleMovimiento", 
                id_movimiento: this.idMovimiento 
            },
            coffeesoft: true,
            conf: { datatable: false },
            attr: {
                id: "tbDetalleMovimiento",
                theme: "light",
                title: "Productos Agregados",
                center: [0, 1, 2, 3, 4]
            },
            success: () => {
                this.updateResumen();
            }
        });
    }

    async deleteProducto(idDetalle) {
        const response = await useFetch({
            url: this._link,
            data: {
                opc: "deleteProductoMovimiento",
                id_detalle: idDetalle
            }
        });

        if (response.status === 200) {
            alert({
                icon: "success",
                text: "Producto eliminado",
                btn1: true
            });

            this.lsDetalleMovimiento();
            this.updateResumen();
        } else {
            alert({
                icon: "error",
                text: response.message,
                btn1: true
            });
        }
    }

    async updateResumen() {
        const detalles = await useFetch({
            url: this._link,
            data: { 
                opc: "lsDetalleMovimiento", 
                id_movimiento: this.idMovimiento 
            }
        });

        const totalProductos = detalles.ls ? detalles.ls.length : 0;
        const totalUnidades = detalles.ls ? detalles.ls.reduce((sum, item) => sum + parseInt(item.cantidad), 0) : 0;

        const tipoColor = this.movimientoData.tipo_movimiento === 'Entrada' 
            ? 'text-green-600' 
            : 'text-orange-600';

        const tipoIcon = this.movimientoData.tipo_movimiento === 'Entrada' 
            ? '↑' 
            : '↓';

        const resumen = $("<div>", {
            class: "bg-white p-4 rounded-lg shadow sticky top-4"
        }).html(`
            <h3 class="text-lg font-semibold mb-4">📋 Resumen</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-gray-600">Folio:</span>
                    <span class="font-bold">${this.movimientoData.folio}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-gray-600">Tipo:</span>
                    <span class="font-bold ${tipoColor}">${tipoIcon} ${this.movimientoData.tipo_movimiento}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-gray-600">Productos:</span>
                    <span class="font-bold text-2xl">${totalProductos}</span>
                </div>
                <div class="flex justify-between items-center pb-2 border-b">
                    <span class="text-gray-600">Total Unidades:</span>
                    <span class="font-bold text-2xl">${totalUnidades}</span>
                </div>
            </div>
        `);

        $("#resumenMovimiento").html(resumen);
    }

    async guardarMovimiento() {
        const response = await useFetch({
            url: this._link,
            data: {
                opc: "guardarMovimiento",
                id_movimiento: this.idMovimiento
            }
        });

        if (response.status === 200) {
            alert({
                icon: "success",
                title: "Lista Guardada",
                text: response.message,
                btn1: true,
                btn1Text: "Aceptar"
            });

            inventario.render();
        } else {
            alert({
                icon: "error",
                text: response.message,
                btn1: true
            });
        }
    }

    cancelarCaptura() {
        bootbox.confirm({
            title: "¿Cancelar Captura?",
            message: "¿Deseas regresar sin guardar los cambios?",
            buttons: {
                confirm: {
                    label: "Sí, Cancelar",
                    className: "btn-danger"
                },
                cancel: {
                    label: "No, Continuar",
                    className: "btn-secondary"
                }
            },
            callback: (result) => {
                if (result) {
                    inventario.render();
                }
            }
        });
    }
}
