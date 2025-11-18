let api = '';
let app;
let files;

$(async () => {
    app = new App(api, 'root');
    app.init();
    files = new Files(api, 'root');
    files.init();
});

class App extends Templates {
    constructor(api, root) {
        super(api, root);
        this.PROJECT_NAME = "files";
        this.modules = [];
        this.userPermission = null;
    }

    init() {
        this.render();
    }

    render() {
        this.layout();
        this.filterBar();
        this.loadInitialData();
    }

    layout() {
        this.primaryLayout({
            parent: 'root',
            class: 'flex p-2',
            id: this.PROJECT_NAME,
        });
    }

    filterBar() {
        const filterData = [
            {
                opc: "select",
                class: "col-sm-3",
                id: "moduleSelect" + this.PROJECT_NAME,
                lbl: "Módulo: ",
                fn: `${this.PROJECT_NAME.toLowerCase()}.filterByModule()`,
            }
        ];

        // Agregar selector de fechas según permisos
        if (this.userPermission !== 'Captura') {
            filterData.push({
                opc: "input-calendar",
                class: "col-sm-4",
                id: "calendar" + this.PROJECT_NAME,
                lbl: "Rango de fechas: ",
            });
        } else {
            filterData.push({
                opc: "input-calendar",
                class: "col-sm-4",
                id: "calendar" + this.PROJECT_NAME,
                lbl: "Fecha: ",
                singleDate: true
            });
        }

        filterData.push(
            {
                opc: "btn",
                class: "col-sm-2",
                color_btn: "primary",
                id: "btnSearch",
                text: "Buscar",
                fn: `${this.PROJECT_NAME.toLowerCase()}.ls()`,
            },
            {
                opc: "btn",
                class: "col-sm-2",
                color_btn: "success",
                id: "btnStats",
                text: "Estadísticas",
                fn: `${this.PROJECT_NAME.toLowerCase()}.showStats()`,
            }
        );

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: filterData,
        });

        dataPicker({
            parent: "calendar" + this.PROJECT_NAME,
            onSelect: () => this.ls(),
        });
    }

    async loadInitialData() {
        try {
            const response = await fn_ajax({ opc: 'init' }, api);
            if (response.status === 200) {
                this.modules = response.data.modules;
                this.userPermission = response.data.userPermission[0]?.permission_level || 'Captura';
                this.populateModuleSelect();
                this.ls();
            }
        } catch (error) {
            console.error('Error loading initial data:', error);
        }
    }

    populateModuleSelect() {
        const select = $(`#moduleSelect${this.PROJECT_NAME}`);
        select.empty();
        select.append('<option value="">Todos los módulos</option>');
        
        this.modules.forEach(module => {
            select.append(`<option value="${module.id}">${module.module_name}</option>`);
        });
    }

    ls() {
        const fi = $(`#calendar${this.PROJECT_NAME} input[name="fi"]`).val() || '';
        const ff = $(`#calendar${this.PROJECT_NAME} input[name="ff"]`).val() || '';
        const module_id = $(`#moduleSelect${this.PROJECT_NAME}`).val() || '';

        const data = {
            opc: 'ls',
            fi: fi,
            ff: ff,
            module_id: module_id
        };

        fn_ajax(data, api).then(response => {
            if (response.status === 200) {
                this.createTable({
                    parent: `content${this.PROJECT_NAME}`,
                    data: response,
                    f_size: '12',
                    color_th: "bg-primary-1 bg-primary",
                    id: "filesTable",
                    striped: true,
                    datatable: true,
                    no: 15,
                    attr: {
                        column: [
                            { title: "Fecha de subida", field: "upload_date", class: "text-center" },
                            { title: "Módulo", field: "module_name", class: "text-center" },
                            { title: "Subido por", field: "uploaded_by", class: "text-center" },
                            { title: "Nombre del archivo", field: "file_name", class: "text-left" },
                            { title: "Tipo/Tamaño", field: "type_size", class: "text-center" },
                            { title: "Acciones", field: "a", class: "text-center" }
                        ]
                    }
                });
            }
        }).catch(error => {
            console.error('Error loading files:', error);
            alert('Error al cargar los archivos');
        });
    }

    filterByModule() {
        this.ls();
    }

    showStats() {
        const module_id = $(`#moduleSelect${this.PROJECT_NAME}`).val() || '';
        
        fn_ajax({ opc: 'stats', module_id: module_id }, api).then(response => {
            if (response.status === 200) {
                this.showStatsModal(response.data);
            }
        }).catch(error => {
            console.error('Error loading stats:', error);
        });
    }

    showStatsModal(stats) {
        let html = '<div class="row">';
        stats.forEach(stat => {
            html += `
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5 class="card-title">${stat.module_name}</h5>
                            <h2 class="text-primary">${stat.file_count}</h2>
                            <p class="text-muted">archivos</p>
                            <small class="text-muted">${this.formatBytes(stat.total_size || 0)}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        this.createModal({
            id: 'statsModal',
            title: 'Estadísticas de Archivos',
            body: html,
            size: 'lg',
            footer: '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>'
        });

        $('#statsModal').modal('show');
    }

    formatBytes(bytes) {
        if (bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

class Files extends App {
    constructor(api, root) {
        super(api, root);
        this.PROJECT_NAME = "files";
    }

    init() {
        // Files specific initialization
    }

    download(id) {
        swalQuestion({
            title: '¿Descargar archivo?',
            text: 'El archivo será descargado a su dispositivo.',
            confirmButtonText: 'Descargar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                fn_ajax({ opc: 'download', id: id }, api).then(response => {
                    if (response.status === 200) {
                        this.triggerDownload(response.token, response.filename);
                        toast('success', 'Archivo descargado exitosamente');
                    } else {
                        toast('error', response.message || 'Error al descargar el archivo');
                    }
                }).catch(error => {
                    console.error('Download error:', error);
                    toast('error', 'Error al procesar la descarga');
                });
            }
        });
    }

    view(id) {
        fn_ajax({ opc: 'get', id: id }, api).then(response => {
            if (response.status === 200) {
                this.showFileDetails(response.data);
            } else {
                toast('error', response.message || 'Error al cargar detalles del archivo');
            }
        }).catch(error => {
            console.error('View error:', error);
            toast('error', 'Error al cargar detalles del archivo');
        });
    }

    delete(id) {
        swalQuestion({
            title: '¿Está seguro de querer eliminar el archivo?',
            text: 'Esta acción no se puede deshacer.',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                fn_ajax({ opc: 'delete', id: id }, api).then(response => {
                    if (response.status === 200) {
                        toast('success', 'Archivo eliminado exitosamente');
                        this.ls();
                    } else {
                        toast('error', response.message || 'Error al eliminar el archivo');
                    }
                }).catch(error => {
                    console.error('Delete error:', error);
                    toast('error', 'Error al procesar la eliminación');
                });
            }
        });
    }

    triggerDownload(token, filename) {
        const link = document.createElement('a');
        link.href = `download.php?token=${token}`;
        link.download = filename;
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    showFileDetails(file) {
        const html = `
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr><td><strong>Nombre:</strong></td><td>${file.original_name}</td></tr>
                        <tr><td><strong>Tipo:</strong></td><td>${file.extension?.toUpperCase() || 'N/A'}</td></tr>
                        <tr><td><strong>Tamaño:</strong></td><td>${file.formatted_size}</td></tr>
                        <tr><td><strong>Módulo:</strong></td><td>${file.module_name}</td></tr>
                        <tr><td><strong>Subido por:</strong></td><td>${file.uploaded_by}</td></tr>
                        <tr><td><strong>Fecha:</strong></td><td>${file.upload_date}</td></tr>
                        <tr><td><strong>Unidad de negocio:</strong></td><td>${file.business_unit}</td></tr>
                    </table>
                </div>
            </div>
        `;

        this.createModal({
            id: 'fileDetailsModal',
            title: 'Detalles del Archivo',
            body: html,
            size: 'md',
            footer: `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="files.download(${file.id})">
                    <i class="icon-download"></i> Descargar
                </button>
            `
        });

        $('#fileDetailsModal').modal('show');
    }
}

// Función auxiliar para mostrar notificaciones toast
function toast(type, message) {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;
    
    $('#toastContainer').append(toastHtml);
    const toast = new bootstrap.Toast($('#toastContainer .toast').last());
    toast.show();
}

// Función auxiliar para sweetalert
function swalQuestion(options) {
    return Swal.fire({
        title: options.title || '¿Está seguro?',
        text: options.text || '',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: options.confirmButtonText || 'Sí',
        cancelButtonText: options.cancelButtonText || 'No',
        confirmButtonColor: options.confirmButtonColor || '#3085d6',
        cancelButtonColor: '#d33'
    });
}