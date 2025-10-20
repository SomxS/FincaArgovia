const ctrl = "ctrl/ctrl-administracion.php";

let flores;            // clase flores.
let category, classe;  // variables globales.

$(function () {
    flores = new App(ctrl, "");

    initComponents(ctrl).then((data) => {
        category = data.category;
        classe = data.subcategory;
        flores.initComponents();
    });
});

// Clase flores.

class App extends Templates {
    constructor(ctrl, div_module) {
        super(ctrl, div_module);
    }

    initComponents() {
        this.createTableFlower();
        this.filterBarFlower();
    }

    filterBarFlower() {
        const filter = [
            {
                opc: "select",
                class: "col-sm-3",
                lbl: "Categoria",
                id: "category",
                name: "id_category",
                data: category,
            },
            {
                opc: "select",
                class: "col-sm-3",
                lbl: "Clase",
                id: "classe",
                name: "id_classe",
                data: classe,
            },
            {
                opc: "btn",
                class: "col-sm-3",
                text: '<i class="icon-plus"></i> Agregar flor',
                fn: "flores.addProducts()",
            },
        ];

        this.createfilterBar({ parent: "containerBar", data: filter });
    }

    addProducts() {
        // Crea form.
        this.createModalForm({
            id: "modal",
            bootbox: { title: "Agregar producto" },   // agregar conf. bootbox
            json: [
                {
                    opc: "input",
                    class: "col-12 mb-3",
                    lbl: "Nombre del producto",
                    id: "product",
                    name: "id_product",
                },
                {
                    opc: "select",
                    class: "col-6 mb-3",
                    lbl: "Categoria",
                    id: "category",
                    name: "id_category",
                    data: category,
                },
                {
                    opc: "select",
                    class: "col-6 mb-3",
                    lbl: "Clase",
                    id: "classe",
                    name: "id_classe",
                    data: classe,
                },
                {
                    opc: "input",
                    class: "col-6 mb-3",
                    lbl: "Stock mínimo",
                    id: "stock_min",
                    name: "stock_min",
                    tipo: "cifra",
                },
                {
                    opc: "input",
                    class: "col-6 mb-3",
                    lbl: "Cantidad mayoreo",
                    id: "quantity",
                    name: "quantity",
                    tipo: "cifra",

                },
                {
                    opc: "input-group",
                    class: "col-6 mb-3",
                    lbl: "Precio mayoreo",
                    id: "price_mayoreo",
                    name: "price_mayoreo",
                    tipo: "cifra",
                    icon: "icon-dollar",
                },
                {
                    opc: "input-group",
                    class: "col-6 mb-3",
                    lbl: "Precio menudeo",
                    id: "price_menudeo",
                    name: "price_menudeo",
                    tipo: "cifra",
                    icon: "icon-dollar",
                },
                {
                    opc: "input-group",
                    class: "col-6 mb-3",
                    lbl: "Precio costo",
                    id: "price_cost",
                    name: "price_cost",
                    tipo: "cifra",
                    icon: "icon-dollar",
                },
                {
                    opc: "input-group",
                    class: "col-6 mb-3",
                    lbl: "Precio venta",
                    id: "price_sale",
                    name: "price_sale",
                    tipo: "cifra",
                    icon: "icon-dollar",
                },
                {
                    opc: "input",
                    class: "col-6 mb-3",
                    lbl: "Unidad",
                    id: "price",
                    name: "price",
                },
            ],

            autovalidation: true,
            data: { opc: "set", id: 1 },

            success: (data) => { },
        });
    }

    createTableFlower() {
        this.createTable({
            idFilterBar: "containerBar",     // especificar id de la barra de filtros
            parent: "containerTable",   // especificar donde se imprimira la tabla.

            data: {
                opc: "lsFlowers",
            },

            attr: {
                id: "tbFlowers",
                f_size: "14",
                center: [1, 3, 4],
                extends: true,
            },

            conf: {
                datatable: false,
            },

            success: (data) => { },   // unicamente si necesitas trabajar con la tabla
        });
    }
}
