class Inventary extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    init() {
        this.render();
    }

    render() {
        this.layout();
        this.filterBar();
        this.ls();
    }

    layout(){
        this.primaryLayout({
            parent: 'tab-Inventary',
            id:'Inventary'
        });
    }

    filterBar() {
        this.createfilterBar({
            parent: "filterBarInventary",
            data: [
             
                {
                    opc: "input-calendar",
                    class: "col-sm-2",
                    id: "calendar",
                    lbl: "Consultar fecha: ",
                },
               
                {
                    opc: "btn",
                    class: "col-sm-2",
                    color_btn: "primary",
                    id: "btn",
                    text: "Buscar",
                    fn: "inventary.ls()",
                },
            ],
        });
        
        // initialized.
        dataPicker({
            parent: "calendar",
            type: "simple", // cambia a "normal" si quieres usar rangos
            onSelect: () => this.ls()
        });
    }

    ls() {

        let rangePicker = getDataRangePicker("calendar");
        
        this.createTable({
            parent: "containerInventary",
            idFilterBar: "filterBarInventary",
            data: { opc: "ls", fi: rangePicker.fi, ff: rangePicker.ff },
            conf: { datatable: true, pag: 50, fn_datatable: 'data_table_export' },
            attr: {
                color_th: "bg-primary",
                id      : "tbInventary",
                class   : 'table table-bordered table-sm uppercase',
                f_size  : 16,
                center  : [2,6,7,8,9,10],
                right   : [2,3,4,5],
                extends : true,
                folding : false
            },
           
        });

    }

    







}    

function unfold(id) {

    $(".unfold" + id).toggleClass("d-none");
    $(".ico" + id).toggleClass("icon-right-dir-1");
    $(".ico" + id).toggleClass(" icon-down-dir-1");
}