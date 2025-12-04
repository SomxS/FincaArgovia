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
                    opc: "input-calendar",
                    id: "calendar",
                    lbl: "Rango de Fechas",
                    class: "col-12 col-md-2"
                },
                {
                    opc: "select",
                    id: "tipo_movimiento",
                    lbl: "Tipo de Movimiento",
                    class: "col-12 col-md-2",
                    data: [
                        { id: "Todos", valor: "Todos" },
                        ...tipoMovimiento
                    ],
                    onchange: "inventario.lsMovimientos()"
                },
              
                {
                    opc: "button",
                    id: "btnNuevaLista",
                    text: "Nueva Lista",
                    class: "col-12 col-md-2",
                    className:'w-100',
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
                theme: "light",
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
                size:'small',
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

                    console.log('RESPONSE:',response)
                    
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
            class: "mb-4 pb-4 border-b flex justify-between items-center"
        }).html(`
            <div>
                <h2 class="text-xl font-semibold flex items-center gap-2">
                    <span>📦</span> Captura de Productos
                </h2>
                <p class="text-gray-400 text-sm">Folio: ${this.movimientoData.folio} | Tipo: ${this.movimientoData.tipo_movimiento}</p>
            </div>
            <div class="flex gap-2">
                <button id="btnGuardarMovimiento" class="btn btn-success">
                    <i class="icon-floppy"></i> Guardar Lista
                </button>
                <button id="btnCancelarCaptura" class="btn btn-dark">
                    <i class="icon-cancel"></i> Cancelar
                </button>
            </div>
        `);

        const mainContent = $("<div>", {
            class: "flex flex-col lg:flex-row gap-4 mb-4"
        });

        const leftSection = $("<div>", {
            class: "flex-1"
        }).html(`
            <div class="border rounded-lg p-3" id="seccionAgregarProducto"></div>
        `);

        const rightSection = $("<div>", {
            class: "w-full lg:w-72"
        }).html(`
            <div id="resumenMovimiento"></div>
        `);

        mainContent.append(leftSection, rightSection);

        const tableSection = $("<div>", {
            id: "tablaProductos",
            class: "w-full"
        });

        container.append(header, mainContent, tableSection);
        $("#root").html(container);

        $("#btnGuardarMovimiento").on("click", () => this.guardarMovimiento());
        $("#btnCancelarCaptura").on("click", () => this.cancelarCaptura());

        this.renderSeccionAgregar();
        this.updateResumen();
    }

    renderSeccionAgregar() {
        this.createfilterBar({
            parent: "seccionAgregarProducto",
            data: [
                {
                    opc: "select",
                    id: "selectProducto",
                    lbl: "Producto",
                    class: "col-12 col-md-3",
                    data: productos,
                    placeholder: "Seleccionar producto"
                },
                {
                    opc: "input",
                    id: "inputCantidad",
                    lbl: "Cantidad",
                    tipo: "numero",
                    class: "col-12 col-md-2",
                    value: "1",
                    min: "1"
                },
                {
                    opc: "button",
                    id: "btnAgregarProducto",
                    text: "Agregar",
                    class: "col-12 col-md-3",
                    icono: "icon-plus",
                    onClick: () => this.addProducto()
                }
            ]
        });
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
                timer: 1500,
                showConfirmButton: false
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
            idFilterBar: `filterBar`,

            data: { 
                opc: "lsDetalleMovimiento", 
                id_movimiento: this.idMovimiento 
            },
            coffeesoft: true,
            conf: { datatable: true },
            attr: {
                id: "tbDetalleMovimiento",
                theme: "light",
                striped:true,
                title: "Productos Agregados",
                center: [ 1, 3, 4,5]
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
            class: "p-3 rounded-lg border sticky top-4 text-xs"
        }).html(`
            <h3 class="text-xs font-semibold text-gray-800 mb-2">Resumen</h3>
            
            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-xs">Folio:</span>
                    <span class="font-bold text-gray-900 text-xs">${this.movimientoData.folio}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-xs">Tipo:</span>
                    <span class="font-semibold text-xs ${tipoColor}">${tipoIcon} ${this.movimientoData.tipo_movimiento}</span>
                </div>
            </div>
            
            <hr class="my-2 border-gray-200">
            
            <div class="space-y-1">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-xs">Productos:</span>
                    <span class="font-bold text-sm text-gray-900">${totalProductos}</span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 text-xs">Total unidades:</span>
                    <span class="font-bold text-sm text-gray-900">${totalUnidades}</span>
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

    async editMovimiento(idMovimiento) {
        this.idMovimiento = idMovimiento;
        
        const movimiento = await useFetch({
            url: this._link,
            data: { opc: "getMovimiento", id: idMovimiento }
        });

        if (movimiento.status === 200) {
            this.movimientoData = movimiento.data;
            this.layout();
            this.lsDetalleMovimiento();
        } else {
            alert({
                icon: "error",
                text: movimiento.message || "No se pudo cargar el movimiento",
                btn1: true
            });
        }
    }
}
