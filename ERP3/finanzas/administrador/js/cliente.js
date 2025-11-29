class Clientes extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Cliente";
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsClientes();
    }

    layout() {
        this.primaryLayout({
            parent: `container-client`,
            id: this.PROJECT_NAME,
            class: '',
            card: {
                filterBar: { class: 'w-full ', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full my-2 h-full rounded-lg p-3', id: `container${this.PROJECT_NAME}` }
            }
        });

        $(`#container${this.PROJECT_NAME}`).prepend(`
            <div class="px-4 pt-3 pb-3">
                <h2 class="text-2xl font-semibold">👥 Clientes</h2>
                <p class="text-gray-400">Administración de módulos y conceptos relacionados con la captura de información</p>
            </div>
        `);
    }

    filterBar() {
        
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [

                {
                    opc     : "select",
                    id      : "udn",
                    lbl     : "Unidad de negocio",
                    class   : "col-12 col-md-2",
                    data    : lsudn,
                    text    : "valor",
                    value   : "id",
                    onchange: 'client.lsClientes()'
                },

                {
                    opc  : "select",
                    id   : "active",
                    lbl  : "Estado",
                    class: "col-12 col-md-2",
                    data : [
                        { id: "1", valor: "Activos" },
                        { id: "0", valor: "Inactivos" }
                    ],
                    onchange: 'client.lsClientes()'
                },

                {
                    opc    : "button",
                    class  : "col-12 col-md-3",
                    id     : "btnNuevoCliente",
                    text   : "Agregar cliente",
                    onClick: () => this.addCliente(),
                },
            ],
        });
    }

    lsClientes() {
        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { opc: 'lsClientes' },
            coffeesoft: true,
            conf: { datatable: true, pag: 10 },
            attr: {
                id: `tbClientes`,
                theme: 'light',
                striped:true,
                center: [2]
            },
        });
    }

    addCliente() {
        const udn = $(`#filterBar${this.PROJECT_NAME} #udn`).val();

        this.createModalForm({
            id: 'formClienteAdd',
            data: { opc: 'addCliente' },
            bootbox: {
                title: 'Agregar Cliente',
                size:'small'
            },
            json: [
                {
                    opc: "select",
                    id: "udn_id",
                    lbl: "Unidad de negocio",
                    class: "col-12 mb-3",
                    data: lsudn,
                    text: "valor",
                    value: "id",
                    required: true
                },
                {
                    opc: "input",
                    id: "name",
                    lbl: "Nombre del cliente",
                    class: "col-12 mb-3",
                    required: true
                }
            ],
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsClientes();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message, btn1: true, btn1Text: "Ok" });
                }
            }
        });

        setTimeout(() => {
            $(`#formClienteAdd #udn_id`).val(udn).trigger('change');
        }, 100);
    }

    async editCliente(id) {
        const request = await useFetch({
            url: this._link,
            data: {
                opc: "getCliente",
                id: id,
            },
        });

        const cliente = request.data;

        this.createModalForm({
            id: 'formClienteEdit',
            data: { opc: 'editCliente', id: cliente.id },
            bootbox: {
                title: 'Editar Cliente',
                size: 'small'

            },
            autofill: cliente,
            json: [
                {
                    opc: "select",
                    id: "udn_id",
                    lbl: "Unidad de negocio",
                    class: "col-12 mb-3",
                    data: lsudn,
                    text: "valor",
                    value: "id",
                    required: true
                },
                {
                    opc: "input",
                    id: "name",
                    lbl: "Nombre del cliente",
                    class: "col-12 mb-3",
                    required: true
                }
            ],
            success: (response) => {
                if (response.status === 200) {
                    alert({ icon: "success", text: response.message });
                    this.lsClientes();
                } else {
                    alert({ icon: "info", title: "Oops!...", text: response.message });
                }
            }
        });
    }

    statusCliente(id, active) {
        const action = active === 1 ? 'desactivar' : 'activar';
        const message = active === 1
            ? 'El cliente ya no estará disponible, pero seguirá reflejándose en los registros contables.'
            : 'El cliente estará disponible para captura de información.';

        this.swalQuestion({
            opts: {
                title: `¿Desea ${action} el cliente?`,
                text: message,
                icon: "warning",
            },
            data: {
                opc: "statusCliente",
                active: active === 1 ? 0 : 1,
                id: id,
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({ icon: "success", text: response.message });
                        this.lsClientes();
                    } else {
                        alert({ icon: "info", title: "Oops!...", text: response.message });
                    }
                }
            },
        });
    }
}
