json_form_ticket = [
    {
        opc: "input-group",
        lbl: "Producto",
        id: "FlorComplemento",
        tipo: "texto",
        placeholder: "Buscar flor complementaria",
    },

    {
        opc: "input",
        lbl: "Cantidad",
        id: "CantidadComplemento",
        tipo: "cifra",
        placeholder: "0",
    },

    {
        opc: "input-group",
        lbl: "Precio",
        id: "PrecioComplemento",
        tipo: "cifra",
        placeholder: "0.00",
        icon: "icon-dollar",
        required: false,
        disabled: true,
    },

    {
        opc: "input-group",
        lbl: "Total",
        id: "TotalComplemento",
        tipo: "cifra",
        holder: "0.00",
        disabled: true,
        required: false,
        icon: "icon-dollar",
    },
];