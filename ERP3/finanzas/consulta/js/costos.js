let api = 'ctrl/ctrl-costos.php';
let app;
let lsudn;
let userLevel;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    lsudn = data.udn;
    userLevel = data.userLevel;

    app = new App(api, "root");
    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "costos";
        this.isLoading = false;
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsCostos();
    }

    layout() {
        this.primaryLayout({
            parent: 'root',
            id: this.PROJECT_NAME,
            class: 'w-full',
            card: {
                filterBar: { class: 'w-full mb-3 border-b pb-3', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full h-full', id: `container${this.PROJECT_NAME}` }
            }
        });
    }

    filterBar() {
        const filters = [
            {
                opc: "input-calendar",
                class: "col-sm-4",
                id: `calendar${this.PROJECT_NAME}`,
                lbl: "Rango de fechas:",
            }
        ];

        if (userLevel === 3) {
            filters.push({
                opc: "select",
                id: "udn",
                lbl: "Unidad de negocio:",
                class: "col-sm-3",
                data: lsudn,
                onchange: `app.lsCostos()`,
            });
        }

        filters.push({
            opc: "button",
            class: "col-sm-2",
            id: "btnExportExcel",
            text: "Exportar a Excel",
            color_btn: "success",
            icono: "icon-download",
            onClick: () => this.exportExcel(),
        });

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: filters,
        });

        dataPicker({
            parent: `calendar${this.PROJECT_NAME}`,
            onSelect: () => this.lsCostos(),
        });
    }

    validateDateRange() {
        const rangePicker = getDataRangePicker(`calendar${this.PROJECT_NAME}`);
        const fi = new Date(rangePicker.fi);
        const ff = new Date(rangePicker.ff);

        if (fi > ff) {
            alert({
                icon: "error",
                title: "Rango de fechas inválido",
                text: "La fecha inicial debe ser menor o igual a la fecha final",
                btn1: true,
                btn1Text: "Ok"
            });
            return false;
        }

        return true;
    }

    lsCostos() {
        if (!this.validateDateRange()) {
            return;
        }

        if (this.isLoading) {
            return;
        }

        this.isLoading = true;
        const rangePicker = getDataRangePicker(`calendar${this.PROJECT_NAME}`);
        const udn = userLevel === 3 ? $(`#filterBar${this.PROJECT_NAME} #udn`).val() : null;

        $(`#container${this.PROJECT_NAME}`).html(`
            <div class="px-4 pt-3 pb-3">
                <h2 class="text-2xl font-semibold">📊 Concentrado de Costos</h2>
                <p class="text-gray-400">Consulta de costos directos y salidas de almacén por día</p>
            </div>
            <div id="table-${this.PROJECT_NAME}">
                <div class="flex justify-center items-center py-8">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
            </div>
        `);

        this.createTable({
            parent: `table-${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { 
                opc: 'lsCostos', 
                fi: rangePicker.fi, 
                ff: rangePicker.ff,
                udn: udn
            },
            coffeesoft: true,
            conf: { datatable: false, pag: 15 },
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: 'corporativo',
                title: 'Concentrado Diario de Costos',
                subtitle: `Período: ${this.formatDateDisplay(rangePicker.fi)} - ${this.formatDateDisplay(rangePicker.ff)}`,
                center: [],
                right: []
            },
            success: (response) => {
                this.isLoading = false;
                
                if (response.status !== 200) {
                    alert({
                        icon: "error",
                        text: response.message || "Error al consultar costos",
                        btn1: true,
                        btn1Text: "Ok"
                    });
                    
                    $(`#table-${this.PROJECT_NAME}`).html(`
                        <div class="text-center py-8 text-gray-500">
                            <i class="icon-attention text-4xl mb-3"></i>
                            <p>No se pudieron cargar los datos</p>
                        </div>
                    `);
                } else if (!response.row || response.row.length === 0) {
                    $(`#table-${this.PROJECT_NAME}`).html(`
                        <div class="text-center py-8 text-gray-500">
                            <i class="icon-inbox text-4xl mb-3"></i>
                            <p>No hay datos disponibles para el período seleccionado</p>
                        </div>
                    `);
                }
            },
            error: () => {
                this.isLoading = false;
                alert({
                    icon: "error",
                    text: "Error de conexión con el servidor",
                    btn1: true,
                    btn1Text: "Ok"
                });
            }
        });
    }

    async exportExcel() {
        if (!this.validateDateRange()) {
            return;
        }

        const rangePicker = getDataRangePicker(`calendar${this.PROJECT_NAME}`);
        const udn = userLevel === 3 ? $(`#filterBar${this.PROJECT_NAME} #udn`).val() : null;

        const btnExport = $(`#btnExportExcel`);
        btnExport.prop('disabled', true).html('<i class="icon-spin1 animate-spin"></i> Generando...');

        try {
            const response = await useFetch({
                url: api,
                data: {
                    opc: 'exportExcel',
                    fi: rangePicker.fi,
                    ff: rangePicker.ff,
                    udn: udn
                }
            });

            if (response.status === 200) {
                alert({
                    icon: "success",
                    title: "Exportación exitosa",
                    text: `Archivo generado: ${response.file}`,
                    btn1: true,
                    btn1Text: "Ok"
                });
            } else {
                alert({
                    icon: "error",
                    text: response.message || "Error al exportar a Excel",
                    btn1: true,
                    btn1Text: "Ok"
                });
            }
        } catch (error) {
            alert({
                icon: "error",
                text: "Error de conexión al exportar",
                btn1: true,
                btn1Text: "Ok"
            });
        } finally {
            btnExport.prop('disabled', false).html('<i class="icon-download"></i> Exportar a Excel');
        }
    }

    formatDateDisplay(date) {
        const d = new Date(date);
        const day = String(d.getDate()).padStart(2, '0');
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const year = d.getFullYear();
        return `${day}/${month}/${year}`;
    }
}
