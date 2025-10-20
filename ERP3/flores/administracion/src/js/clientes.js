
window.ctrl = 'ctrl/ctrl-clientes.php';


$(function () {
    
   let clientes = new Cliente(ctrl, 'content');
   clientes.init();
});

class Cliente extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    init() {
        this.filterBar();

        // this.listTable();
    }


    filterBar() {

        const filter = [
           
            {
                opc: "select",
                id: "UDNs",
                class: "col-sm-3 col-lg-2",
                lbl: "UDN",
                // data: data_udn
            },


            {
                opc: "btn",
                text: "Nuevo Cliente",
                color_btn:'primary',
                class: "col-sm-3",
            }


        ];

        this.createFilterBar({ parent: 'filterBar', data: filter, });
      

    }



    get_data() {
        this.fnAjax({
            data: {
                opc: 'Notificaciones'
            },

            methods: {
                request: (data) => {
                    const countNotification = Object.keys(data).length;

                    $('.notification-count').html(countNotification);
                    toast.Notifications(data);
                }
            }
        });
    }
  
  
    listTable() {

        this.createTable({
            
            idFilterBar: 'filterBar', // especificar id de la barra de filtros
            parent: 'content', // especificar donde se imprimira la tabla.

            data: {
                opc: 'ls'
            },

            attr: {
                f_size: '12',
                center: [1],
                color_col: [0, 1],
                color: 'aliceblue',
                collapase: true
            },

            conf: {

                datatable: true,
                fn_datable: 'ScrollX'
            }
            









        });
    }
}
