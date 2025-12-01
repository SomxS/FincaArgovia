
// let  lsPaymentMethods, lsBanks;

// $(async () => {
//     const data = await useFetch({ url: api, data: { opc: "init" } });
// //     lsudn = data.udn;
//     lsPaymentMethods = data.paymentMethods;
//     lsBanks = data.banks;


// });

class AdminBankAccounts extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
        this.PROJECT_NAME = "Bank";
    }

    render() {
        this.layout();
        this.filterBar();
        this.lsBankAccounts();
    }

    layout() {
        this.primaryLayout({
            parent: `container-banco`,
            id: this.PROJECT_NAME,
            class: 'w-full',
            card: {
                filterBar: { class: 'w-full mb-3', id: `filterBar${this.PROJECT_NAME}` },
                container: { class: 'w-full h-full', id: `container${this.PROJECT_NAME}` }
            }
        });

     
    }

    filterBar() {
        this.createfilterBar({
            parent: `filterBar${this.PROJECT_NAME}`,
            data: [
                {
                    opc: "select",
                    id: "udn",
                    lbl: "Unidad de negocio",
                    class: "col-12 col-md-2",
                    data: lsudn,
                    onchange: 'banco.lsBankAccounts()'
                },
               
                {
                    opc: "select",
                    id: "active",
                    lbl: "Estado",
                    class: "col-12 col-md-2",
                    data: [
                        { id: "1", valor: "Activas" },
                        { id: "0", valor: "Inactivas" }
                    ],
                    onchange: 'banco.lsBankAccounts()'
                },
                {
                    opc: "button",
                    class: "col-12 col-md-2",
                    id: "btnAddBank",
                    className:'w-100',
                    text: "+  nuevo banco",
                    color_btn: "success",
                    onClick: () => this.addBank()
                },
                {
                    opc: "button",
                    class: "col-12 col-md-2",
                    id: "btnAddBankAccount",
                    className: 'w-100',
                    text: "+  nueva cuenta",
                    color_btn: "primary",
                    onClick: () => this.addBankAccount()
                }
            ]
        });
    }

    lsBankAccounts() {
  
        const active = $(`#filterBar${this.PROJECT_NAME} #active`).val();

        this.createTable({
            parent: `container${this.PROJECT_NAME}`,
            idFilterBar: `filterBar${this.PROJECT_NAME}`,
            data: { 
                opc: 'lsBankAccounts', 
                active: active 
            },
            coffeesoft: true,
            conf: { datatable: true, pag: 15 },
            attr: {
                id: `tb${this.PROJECT_NAME}`,
                theme: 'corporativo',
                center: [2, 5],
                right: []
            }
        });
    }

    addBank() {
        this.createModalForm({
            id: 'formBankAdd',
            data: { opc: 'addBank' },
            bootbox: {
                title: 'Nuevo Banco',
                size:'small',
                closeButton: true
            },
            json: this.jsonBank(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    
                    useFetch({ url: api, data: { opc: "init" } }).then(data => {
                        lsBanks = data.banks;
                    });
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

    addBankAccount() {
        const udn = $(`#filterBar${this.PROJECT_NAME} #udn`).val();

        this.createModalForm({
            id: 'formBankAccountAdd',
            data: { opc: 'addBankAccount', udn_id: udn },
            bootbox: {
                title: 'Nueva Cuenta Bancaria',
                closeButton: true
            },
            json: this.jsonBankAccount(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsBankAccounts();
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

    async editBankAccount(id) {
        const request = await useFetch({
            url: this._link,
            data: { opc: "getBankAccount", id: id }
        });

        if (request.status !== 200) {
            alert({
                icon: "error",
                text: request.message,
                btn1: true,
                btn1Text: "Ok"
            });
            return;
        }

        const account = request.data;

        this.createModalForm({
            id: 'formBankAccountEdit',
            data: { opc: 'editBankAccount', id: account.id },
            bootbox: {
                title: '✏️ Editar Cuenta Bancaria',
                closeButton: true
            },
            autofill: account,
            json: this.jsonBankAccount(),
            success: (response) => {
                if (response.status === 200) {
                    alert({
                        icon: "success",
                        text: response.message,
                        btn1: true,
                        btn1Text: "Aceptar"
                    });
                    this.lsBankAccounts();
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

    toggleStatusAccount(id, currentStatus) {
        const newStatus = currentStatus === 1 ? 0 : 1;
        const action = newStatus === 1 ? 'activar' : 'desactivar';
        const message = newStatus === 1 
            ? 'La cuenta estará disponible para captura de información.'
            : 'La cuenta bancaria ya no estará disponible, pero seguirá reflejándose en los registros contables.';

        this.swalQuestion({
            opts: {
                title: `¿Desea ${action} esta cuenta bancaria?`,
                text: message,
                icon: "warning"
            },
            data: {
                opc: "toggleStatusAccount",
                active: newStatus,
                id: id
            },
            methods: {
                send: (response) => {
                    if (response.status === 200) {
                        alert({
                            icon: "success",
                            text: response.message,
                            btn1: true,
                            btn1Text: "Aceptar"
                        });
                        this.lsBankAccounts();
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

    jsonBank() {
        return [
            {
                opc: "input",
                id: "name",
                lbl: "Nombre del banco",
                tipo: "texto",
                class: "col-12 mb-3",
                placeholder: "Ej.: BBVA, Santander, Banamex, etc.",
                required: true
            }
        ];
    }

    jsonBankAccount() {
        return [
           
            {
                opc: "select",
                id: "bank_id",
                lbl: "Banco",
                class: "col-12 mb-3",
                data: lsBanks,
                required: true
            },
          
            {
                opc: "input",
                id: "account",
                lbl: "Últimos 4 dígitos de la cuenta",
                tipo: "texto",
                class: "col-12 mb-3",
                placeholder: "0000",
                maxlength: 4,
                pattern: "[0-9]{4}",
                required: true,
                onkeyup: "this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4)"
            },

            {
                opc: "input",
                id: "name",
                lbl: "Nombre o alias de la cuenta (opcional)",
                tipo: "texto",
                class: "col-12 mb-3",
                placeholder: "Ej.: JG, Nómina, etc.",
                required:false
            },
          
        ];
    }
}
