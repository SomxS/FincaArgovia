let api = 'captura/ctrl/ctrl-almacen.php';
let app, almacenDashboard, concentrado;

let supplyItems = [];
let udnList = [];
let productClassList = [];

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    supplyItems = data.supplyItems;
    udnList = data.udn;
    productClassList = data.productClass;

    if (data.accessLevel) {
        setUserAccessLevel(data.accessLevel);
    }

    app = new App(api, "root");
    almacenDashboard = new AlmacenDashboard(api, "root");
    concentrado = new Concentrado(api, "root");
    
    app.render();
    
    setTimeout(() => {
        initAccessControl();
    }, 500);
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "almacen";
    }

    render() {
        this.layout();
        almacenDashboard.renderDashboard();
    }

    layout() {
        this.primaryLayout({
            parent: "root",
            id: this.PROJECT_NAME,
            class: "w-full",
            card: {
                filterBar: { class: "w-full mb-3", id: "filterBar" + this.PROJECT_NAME },
                container: { class: "w-full h-full", id: "container" + this.PROJECT_NAME }
            }
        });

        this.tabLayout({
            parent: "container" + this.PROJECT_NAME,
            id: "tabs" + this.PROJECT_NAME,
            theme: "light",
            type: "short",
            json: [
                {
                    id: "dashboard",
                    tab: "Dashboard",
                    class: "mb-1",
                    active: true,
                    onClick: () => almacenDashboard.renderDashboard()
                },
                {
                    id: "outputs",
                    tab: "Salidas de almacén",
                    onClick: () => {
                        this.filterBar();
                        this.lsOutputs();
                    }
                },
                {
                    id: "concentrado",
                    tab: "Concentrado de almacén",
                    onClick: () => concentrado.renderConcentrado()
                }
            ]
        });

        const moduleName = typeof getModuleName === 'function' ? getModuleName() : 'Módulo de Almacén';
        
        $("#container" + this.PROJECT_NAME).prepend(`
            <div class="px-4 pt-3 pb-3">
                <h2 class="text-2xl font-semibold">📦 ${moduleName}</h2>
                <p class="text-gray-400">Gestión de salidas de almacén y reportes consolidados.</p>
            </div>
        `);
    }

    filterBar() {
        const container = $("#container-outputs");
        container.html(`
            <div id="filterbar-outputs" class="mb-3 p-3 bg-white rounded shadow-sm"></div>
            <div id="total-outputs" class="mb-2 p-3 bg-green-50 rounded"></div>
            <div id="table-outputs"></div>
        `);

        this.createfilterBar({
            parent: "filterbar-outputs",
            data: [
                {
                    opc: "input-calendar",
                    class: "col-12 col-md-4",
                    id: "dateAlmacen",
                    lbl: "Fecha de consulta"
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnNuevaSalida",
                    text: "Registrar nueva salida",
                    color_btn: "success",
                    onClick: () => this.addOutput()
                }
            ]
        });

        dataPicker({
            parent: "dateAlmacen",
            type: "simple",
            onSelect: () => this.lsOutputs()
        });

        setTimeout(() => {
            $("#dateAlmacen").val(moment().format("YYYY-MM-DD"));
        }, 100);
    }

    async lsOutputs() {
        const date = $("#dateAlmacen").val() || moment().format("YYYY-MM-DD");

        const response = await useFetch({
            url: this._link,
            data: { opc: "ls", date: date }
        });

        $("#total-outputs").html(`
            <div class="flex justify-between items-center">
                <span class="text-lg font-semibold text-gray-700">Total de salidas del día:</span>
                <span class="text-2xl font-bold text-green-600">${formatPrice(response.total || 0)}</span>
            </div>
        `);

        this.createTable({
            parent: "table-outputs",
            idFilterBar: "filterbar-outputs",
            data: { opc: "ls", date: date },
            coffeesoft: true,
            conf: { datatable: false, pag: 15 },
            attr: {
                id: "tbOutputs",
                theme: "corporativo",
                title: `Salidas de almacén - ${moment(date).format("DD/MM/YYYY")}`,
                subtitle: `${response.ls?.length || 0} registros encontrados`,
                center: [1],
                right: [2]
            }
        });
    }

    addOutput() {
        this.createModalForm({
            id: "formOutputAdd",
            data: { opc: "addOutput" },
            bootbox: {
                title: "Nueva salida de almacén",
                closeButton: true
            },
            json: [
                {
                    opc: "label",
                    id: "lblInfo",
                    text: "Todos los campos son obligatorios",
                    class: "col-12 text-sm text-gray-600 mb-3"
                },
                {
                    opc: "select",
                    id: "product_id",
                    lbl: "Almacén (Insumo)",
                    class: "col-12 mb-3",
                    data: supplyItems,
                    text: "valor",
                    value: "id",
                    required: true
                },
                {
                    opc: "input",
                    id: "amount",
                    lbl: "Cantidad",
                    tipo: "cifra",
                    class: "col-12 mb-3",
                    placeholder: "0.00",
                    onkeyup: "validationInputForNumber('#amount')",
                    required: true
                },
                {
                    opc: "textarea",
                    id: "description",
                    lbl: "Descripción",
                    class: "col-12 mb-3",
                    rows: 3,
                    placeholder: "Describe el motivo de la salida...",
                    required: true
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
                    this.lsOutputs();
                    almacenDashboard.renderDashboard();
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

    async editOutput(id) {
        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getOutput",
                id: id
            }
        });

        if (request.status !== 200) {
            alert({
                icon: "error",
                text: request.message || "Error al obtener los datos",
                btn1: true,
                btn1Text: "Ok"
            });
            return;
        }

        const output = request.data;

        this.createModalForm({
            id: "formOutputEdit",
            data: { opc: "editOutput", id: id },
            bootbox: {
                title: "Editar salida de almacén",
                closeButton: true
            },
            autofill: output,
            json: [
                {
                    opc: "label",
                    id: "lblInfo",
                    text: "Todos los campos son obligatorios",
                    class: "col-12 text-sm text-gray-600 mb-3"
                },
                {
                    opc: "select",
                    id: "product_id",
                    lbl: "Almacén (Insumo)",
                    class: "col-12 mb-3",
                    data: supplyItems,
                    text: "valor",
                    value: "id",
                    required: true
                },
                {
                    opc: "input",
                    id: "amount",
                    lbl: "Cantidad",
                    tipo: "cifra",
                    class: "col-12 mb-3",
                    onkeyup: "validationInputForNumber('#amount')",
                    required: true
                },
                {
                    opc: "textarea",
                    id: "description",
                    lbl: "Descripción",
                    class: "col-12 mb-3",
                    rows: 3,
                    required: true
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
                    this.lsOutputs();
                    almacenDashboard.renderDashboard();
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

    deleteOutput(id) {
        const row = event.target.closest("tr");
        const almacen = row.querySelectorAll("td")[0]?.innerText || "este registro";

        this.swalQuestion({
            opts: {
                title: "¿Está seguro?",
                html: `¿Deseas eliminar la salida de almacén <strong>${almacen}</strong>?<br><br>
                       <small class="text-muted">Esta acción se registrará en la bitácora del sistema.</small>`,
                icon: "warning",
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar"
            },
            data: { opc: "deleteOutput", id: id },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                        this.lsOutputs();
                        almacenDashboard.renderDashboard();
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

    viewDescription(description) {
        bootbox.alert({
            title: '<i class="icon-eye"></i> Descripción de la salida',
            message: `
                <div class="p-3 bg-light rounded">
                    <p class="mb-0">${description || "Sin descripción"}</p>
                </div>
            `,
            className: "modal-description",
            backdrop: true
        });
    }
}

class AlmacenDashboard extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "almacenDashboard";
    }

    async renderDashboard() {
        const date = moment().format("YYYY-MM-DD");
        const data = await useFetch({
            url: this._link,
            data: { opc: "getDashboardData", date: date }
        });

        const lockStatus = await useFetch({
            url: this._link,
            data: { opc: "checkModuleLock", module_id: 1, udn_id: 1 }
        });

        const container = $("#container-dashboard");
        
        let lockIndicator = '';
        if (lockStatus.isLocked && lockStatus.lockData) {
            lockIndicator = `
                <div class="alert alert-warning mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <i class="icon-lock"></i>
                            <strong>Módulo bloqueado</strong> - ${lockStatus.lockData.lock_reason}
                            <br><small>Bloqueado el: ${moment(lockStatus.lockData.lock_date).format("DD/MM/YYYY HH:mm")}</small>
                        </div>
                        ${hasPermission('canLockModule') ? `
                            <button class="btn btn-sm btn-success" onclick="almacenDashboard.unlockModuleModal(${lockStatus.lockData.id})">
                                <i class="icon-unlock"></i> Desbloquear
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
        }
        
        container.html(`
            <div class="p-4">
                ${lockIndicator}
                
                <div class="mb-4">
                    <h3 class="text-xl font-semibold mb-3">Resumen del día - ${moment().format("DD/MM/YYYY")}</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg shadow-md border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Total de salidas del día</p>
                                <p class="text-3xl font-bold text-green-700">${formatPrice(data.total || 0)}</p>
                            </div>
                            <div class="bg-green-500 p-3 rounded-full">
                                <i class="icon-cart text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg shadow-md border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Número de movimientos</p>
                                <p class="text-3xl font-bold text-blue-700">${data.count || 0}</p>
                            </div>
                            <div class="bg-blue-500 p-3 rounded-full">
                                <i class="icon-list text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-lg shadow-md border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Promedio por salida</p>
                                <p class="text-3xl font-bold text-purple-700">${formatPrice(data.count > 0 ? data.total / data.count : 0)}</p>
                            </div>
                            <div class="bg-purple-500 p-3 rounded-full">
                                <i class="icon-chart text-white text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-lg font-semibold mb-3">Acciones rápidas</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button class="btn btn-lg btn-primary d-flex align-items-center justify-content-center gap-2 py-3" 
                            onclick="$('#tabs-almacen-tab-concentrado').click()">
                        <i class="icon-chart"></i>
                        <span>Concentrado de almacén</span>
                    </button>
                    <button class="btn btn-lg btn-info d-flex align-items-center justify-content-center gap-2 py-3" 
                            onclick="almacenDashboard.uploadFileModal()">
                        <i class="icon-upload"></i>
                        <span>Subir archivos de almacén</span>
                    </button>
                    <button class="btn btn-lg btn-success d-flex align-items-center justify-content-center gap-2 py-3" 
                            onclick="app.addOutput()">
                        <i class="icon-plus"></i>
                        <span>Registrar nueva salida</span>
                    </button>
                </div>
                
                ${hasPermission('canLockModule') && !lockStatus.isLocked ? `
                    <div class="mt-4">
                        <button class="btn btn-warning btn-sm" onclick="almacenDashboard.lockModuleModal()">
                            <i class="icon-lock"></i> Bloquear módulo
                        </button>
                    </div>
                ` : ''}
                
                <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <h4 class="text-sm font-semibold text-blue-800 mb-2">
                        <i class="icon-info"></i> Información
                    </h4>
                    <p class="text-sm text-gray-700">
                        Este módulo permite gestionar las salidas de almacén de forma eficiente. 
                        Puedes registrar nuevas salidas, consultar el historial y generar reportes consolidados.
                    </p>
                </div>
            </div>
        `);
    }

    uploadFileModal() {
        bootbox.dialog({
            title: '<i class="icon-upload"></i> Subir archivo de respaldo',
            message: `
                <form id="formUploadFile" enctype="multipart/form-data">
                    <div class="alert alert-info">
                        <small>
                            <i class="icon-info"></i>
                            Los archivos de respaldo permiten mantener evidencia documental de las operaciones.
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Archivo (máx. 20MB)</label>
                        <input type="file" class="form-control" id="fileInput" name="file" 
                               accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls" required>
                        <small class="text-muted">
                            <i class="icon-paperclip"></i>
                            Formatos permitidos: PDF, JPG, PNG, Excel
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha de operación</label>
                        <input type="date" class="form-control" id="operationDate" name="operation_date" 
                               value="${moment().format('YYYY-MM-DD')}" required>
                        <small class="text-muted">
                            <i class="icon-calendar"></i>
                            Fecha a la que corresponde el archivo
                        </small>
                    </div>
                </form>
            `,
            size: 'large',
            buttons: {
                cancel: {
                    label: '<i class="icon-cross"></i> Cancelar',
                    className: "btn-secondary"
                },
                ok: {
                    label: '<i class="icon-upload"></i> Subir archivo',
                    className: "btn-primary",
                    callback: () => {
                        const fileInput = document.getElementById("fileInput");
                        const file = fileInput.files[0];
                        
                        if (!file) {
                            alert({ 
                                icon: "warning", 
                                text: "Por favor selecciona un archivo",
                                btn1: true,
                                btn1Text: "Ok"
                            });
                            return false;
                        }
                        
                        if (file.size > 20 * 1024 * 1024) {
                            alert({ 
                                icon: "error", 
                                text: "El archivo excede el tamaño máximo de 20MB",
                                btn1: true,
                                btn1Text: "Ok"
                            });
                            return false;
                        }
                        
                        const formData = new FormData();
                        formData.append("file", file);
                        formData.append("operation_date", $("#operationDate").val());
                        formData.append("opc", "uploadFile");
                        
                        Swal.fire({
                            title: 'Subiendo archivo...',
                            text: 'Por favor espera',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        fetch(api, {
                            method: "POST",
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close();
                            if (data.status === 200) {
                                alert({ 
                                    icon: "success", 
                                    text: data.message,
                                    btn1: true,
                                    btn1Text: "Aceptar"
                                });
                            } else {
                                alert({ 
                                    icon: "error", 
                                    text: data.message,
                                    btn1: true,
                                    btn1Text: "Ok"
                                });
                            }
                        })
                        .catch(error => {
                            Swal.close();
                            alert({ 
                                icon: "error", 
                                text: "Error al subir el archivo. Por favor intenta nuevamente.",
                                btn1: true,
                                btn1Text: "Ok"
                            });
                            console.error(error);
                        });
                    }
                }
            }
        });
    }

    lockModuleModal() {
        bootbox.prompt({
            title: '<i class="icon-lock"></i> Bloquear módulo de almacén',
            message: '<p class="mb-3">Por favor proporciona una razón para bloquear el módulo:</p>',
            inputType: 'textarea',
            placeholder: 'Ej: Cierre mensual, auditoría, mantenimiento...',
            callback: async (reason) => {
                if (reason && reason.trim()) {
                    Swal.fire({
                        title: 'Bloqueando módulo...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const response = await useFetch({
                        url: api,
                        data: {
                            opc: 'lockModule',
                            module_id: 1,
                            udn_id: 1,
                            lock_reason: reason
                        }
                    });

                    Swal.close();

                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                        this.renderDashboard();
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

    unlockModuleModal(lockId) {
        Swal.fire({
            title: '¿Desbloquear módulo?',
            text: 'El módulo volverá a estar disponible para operaciones',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, desbloquear',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d'
        }).then(async (result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Desbloqueando módulo...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const response = await useFetch({
                    url: api,
                    data: {
                        opc: 'unlockModule',
                        id: lockId
                    }
                });

                Swal.close();

                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.renderDashboard();
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
}

class Concentrado extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "concentrado";
    }

    renderConcentrado() {
        const container = $("#container-concentrado");
        container.html(`
            <div class="p-3">
                <div class="mb-3">
                    <h3 class="text-xl font-semibold">Concentrado de Almacén</h3>
                    <p class="text-sm text-gray-600">Reporte consolidado de entradas y salidas por almacén</p>
                </div>
                <div id="filterbar-concentrado" class="mb-3 p-3 bg-white rounded shadow-sm"></div>
                <div id="totals-concentrado" class="mb-3"></div>
                <div id="table-concentrado"></div>
            </div>
        `);

        this.filterBarConcentrado();
        this.lsConcentrado();
    }

    filterBarConcentrado() {
        this.createfilterBar({
            parent: "filterbar-concentrado",
            data: [
                {
                    opc: "input-calendar",
                    class: "col-12 col-md-6",
                    id: "dateRangeConcentrado",
                    lbl: "Rango de fechas"
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnGenerarReporte",
                    text: "Generar reporte",
                    color_btn: "primary",
                    onClick: () => this.lsConcentrado()
                },
                {
                    opc: "button",
                    class: "col-12 col-md-3",
                    id: "btnExportarExcel",
                    text: "Exportar a Excel",
                    color_btn: "success",
                    onClick: () => this.exportToExcel()
                }
            ]
        });

        dataPicker({
            parent: "dateRangeConcentrado",
            type: "all",
            onSelect: () => this.lsConcentrado()
        });

        setTimeout(() => {
            const startOfMonth = moment().startOf('month').format("YYYY-MM-DD");
            const today = moment().format("YYYY-MM-DD");
            $("#dateRangeConcentrado").val(`${startOfMonth} - ${today}`);
        }, 100);
    }

    async lsConcentrado() {
        const rangePicker = getDataRangePicker("dateRangeConcentrado");

        const response = await useFetch({
            url: this._link,
            data: { opc: "getConcentrado", fi: rangePicker.fi, ff: rangePicker.ff }
        });

        if (response.totals) {
            $("#totals-concentrado").html(`
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <div class="bg-blue-50 p-3 rounded border border-blue-200">
                        <p class="text-xs text-gray-600 mb-1">Saldo Inicial</p>
                        <p class="text-lg font-bold text-blue-700">${formatPrice(response.totals.initial)}</p>
                    </div>
                    <div class="bg-green-50 p-3 rounded border border-green-200">
                        <p class="text-xs text-gray-600 mb-1">Total Entradas</p>
                        <p class="text-lg font-bold text-green-700">${formatPrice(response.totals.inputs)}</p>
                    </div>
                    <div class="bg-orange-50 p-3 rounded border border-orange-200">
                        <p class="text-xs text-gray-600 mb-1">Total Salidas</p>
                        <p class="text-lg font-bold text-orange-700">${formatPrice(response.totals.outputs)}</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded border border-purple-200">
                        <p class="text-xs text-gray-600 mb-1">Saldo Final</p>
                        <p class="text-lg font-bold text-purple-700">${formatPrice(response.totals.final)}</p>
                    </div>
                </div>
            `);
        }

        this.createTable({
            parent: "table-concentrado",
            idFilterBar: "filterbar-concentrado",
            data: { opc: "getConcentrado", fi: rangePicker.fi, ff: rangePicker.ff },
            coffeesoft: true,
            conf: { datatable: false, pag: 20 },
            attr: {
                id: "tbConcentrado",
                theme: "corporativo",
                title: `Concentrado del ${moment(rangePicker.fi).format("DD/MM/YYYY")} al ${moment(rangePicker.ff).format("DD/MM/YYYY")}`,
                subtitle: `${response.ls?.length || 0} productos con movimientos`,
                center: [0, 2, 3, 4, 5],
                right: [6]
            }
        });
    }

    async exportToExcel() {
        const rangePicker = getDataRangePicker("dateRangeConcentrado");

        Swal.fire({
            title: 'Generando Excel...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const response = await useFetch({
                url: this._link,
                data: { opc: "getConcentrado", fi: rangePicker.fi, ff: rangePicker.ff }
            });

            if (!response.ls || response.ls.length === 0) {
                Swal.close();
                alert({
                    icon: "warning",
                    text: "No hay datos para exportar en el rango seleccionado",
                    btn1: true,
                    btn1Text: "Ok"
                });
                return;
            }

            const data = response.ls.map(item => ({
                'Clasificación': item.product_class_name,
                'Almacén': item.product_name,
                'Saldo Inicial': parseFloat(item.initial_balance),
                'Entradas': parseFloat(item.total_inputs),
                'Salidas': parseFloat(item.total_outputs),
                'Saldo Final': parseFloat(item.final_balance)
            }));

            data.push({
                'Clasificación': '',
                'Almacén': 'TOTALES',
                'Saldo Inicial': response.totals.initial,
                'Entradas': response.totals.inputs,
                'Salidas': response.totals.outputs,
                'Saldo Final': response.totals.final
            });

            const ws = XLSX.utils.json_to_sheet(data);
            
            ws['!cols'] = [
                { wch: 20 },
                { wch: 30 },
                { wch: 15 },
                { wch: 15 },
                { wch: 15 },
                { wch: 15 }
            ];

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Concentrado");

            const fileName = `Concentrado_Almacen_${moment(rangePicker.fi).format("YYYYMMDD")}_${moment(rangePicker.ff).format("YYYYMMDD")}.xlsx`;
            
            XLSX.writeFile(wb, fileName);

            Swal.close();
            alert({
                icon: "success",
                text: "Archivo Excel generado correctamente",
                btn1: true,
                btn1Text: "Aceptar"
            });

        } catch (error) {
            Swal.close();
            console.error(error);
            alert({
                icon: "error",
                text: "Error al generar el archivo Excel. Verifica que la librería XLSX esté cargada.",
                btn1: true,
                btn1Text: "Ok"
            });
        }
    }

    async expandWarehouseDetails(productId) {
        const rangePicker = getDataRangePicker("dateRangeConcentrado");

        const response = await useFetch({
            url: this._link,
            data: { 
                opc: "getWarehouseDetails", 
                product_id: productId,
                fi: rangePicker.fi,
                ff: rangePicker.ff
            }
        });

        if (response.status === 200 && response.data) {
            let detailsHtml = '<div class="p-3"><table class="table table-sm table-striped">';
            detailsHtml += '<thead><tr><th>Fecha</th><th>Descripción</th><th class="text-end">Monto</th></tr></thead><tbody>';
            
            response.data.forEach(item => {
                detailsHtml += `
                    <tr>
                        <td>${item.formatted_date}</td>
                        <td>${item.description}</td>
                        <td class="text-end">${formatPrice(item.amount)}</td>
                    </tr>
                `;
            });
            
            detailsHtml += '</tbody></table></div>';
            
            bootbox.alert({
                title: 'Detalle de movimientos',
                message: detailsHtml,
                size: 'large'
            });
        }
    }
}
