
url = 'ctrl/ctrl-analisis-de-ingresos-resumen.php';
let resumen;
class ResumenOperativo extends AnalisisIngresos {


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

    lsResumenOperativo() {


        this.createTable({
            idFilterBar: 'filterBarResumenOperativo', // especificar id de la barra de filtros
            parent: 'contentResumenOperativo', // especificar donde se imprimira la tabla.

            data: {
                opc: 'lsResumenOperativo',
                mesLetra: $('#mesResumen option:selected').text(),
            },

            conf: { datatable: true, fn_datatable:'exportTable'},
        
            attr: {
                id      : 'tbResumenPromedio',
                color_th: 'bg-primary-1',
                color_group :'bg-disabled2',
                f_size  : '14',
                right   : [2,3],
                extends:true
            }


        });

        exportTable();
    }
}


function exportTable(id,no){
    data_table_export(id,30);
}
