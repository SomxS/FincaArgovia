let api = 'ctrl/ctrl-costos.php';
let app;

let lsudn, userLevel;

$(async () => {
    const data = await useFetch({ url: api, data: { opc: "init" } });
    lsudn = data.udn;
    userLevel = data.userLevel || 2;

    app = new App(api, "root");
    app.render();
});

class App extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "costos";
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
                <h2 class="text-2xl font-semibold">💰 Concentrado Diario de Costos</h2>
                <p class="text-gray-400">Consulta de costos directos y salidas de almacén por rango de fechas</p>
            </div>
        `);
    }

    filterBar() {
        const filters = [
            {
                opc: "input-calendar",
                class: "col-sm-4",
                id: `calendar${this.PROJECT_NAME}`,
                lbl: "Rango de fechas:"
            }
        ];

        if (userLevel === 3) {
            filters.push({
                opc: "select",
                id: "udn",
                lbl: "Unidad de Negocio",
                class: "col-sm-3",
                data: lsudn,
                onchange: `app.ls()`
            });
        }

        filters.push({
            opc: "button",
            class: "col-sm-2",
            id: "btnExportExcel",
            text: "Exportar a Excel",
            icono: "fas fa-file-excel",
            color_btn: "success",
            onClick: () => this.exportExcel()
        });

        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: filters
        });

        dataPicker({
            parent: `calendar${this.PROJECT_NAME}`,
            onSelect: () => this.ls()
        });
    }

    ls() {
        const rangePicker = getDataRangePicker(`calendar${this.PROJECT_NAME}`);
        const fi = rangePicker.fi;
        const ff = rangePicker.ff;

        if (!fi || !ff) {
            alert({
                icon: "warning",
                text: "Por favor selecciona un rango de fechas"
            });
            return;
        }

        const diffMonths = moment(ff).diff(moment(fi), 'months', true);
        if (diffMonths > 12) {
            alert({
                icon: "warning",
                text: "El rango de fechas no puede exceder 12 meses"
            });
            return;
        }

        if (moment(fi).isAfter(moment(ff))) {
            alert({
                icon: "error",
                text: "La fecha inicial debe ser menor o igual a la fecha final"
            });
            return;
        }

        const udn = userLevel === 3 ? $(`#filterBar${this.PROJECT_NAME} #udn`).val() : null;

        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'ls', fi, ff, udn },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: 'corporativo',
                title: `Concentrado Diario de Costos`,
                subtitle: `Período: ${moment(fi).format('DD/MM/YYYY')} - ${moment(ff).format('DD/MM/YYYY')}`,
                center: [1, 2, 3, 4, 5, 6],
                right: [2, 3, 4, 5, 6]
            },
            success: (response) => {
                if (response.status && response.status !== 200) {
                    alert({
                        icon: "error",
                        text: response.message || "Error al cargar los datos"
                    });
                }
            }
        });
    }

    exportExcel() {
        const rangePicker = getDataRangePicker(`calendar${this.PROJECT_NAME}`);
        const fi = rangePicker.fi;
        const ff = rangePicker.ff;

        if (!fi || !ff) {
            alert({
                icon: "warning",
                text: "Por favor selecciona un rango de fechas para exportar"
            });
            return;
        }

        const udn = userLevel === 3 ? $(`#filterBar${this.PROJECT_NAME} #udn`).val() : null;

        alert({
            icon: "info",
            text: "Generando archivo Excel...",
            timer: 2000
        });

        useFetch({
            url: api,
            data: { opc: 'exportExcel', fi, ff, udn },
            success: (response) => {
                if (response.status === 200) {
                    window.location.href = response.file;
                    alert({
                        icon: "success",
                        text: "Archivo Excel generado correctamente"
                    });
                } else {
                    alert({
                        icon: "error",
                        text: response.message || "Error al generar el archivo Excel"
                    });
                }
            }
        });
    }
}
