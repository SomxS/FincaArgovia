class Pos extends Templates {
    constructor(link, div_modulo) {
        super(link, div_modulo);
    }

    render() {
        this.layout();
        // ticket.
        this.filterBarTicket();
        this.cardEmpty();
        this.cardEndTicket();
        // box. 
        this.ButtonGroups();
        this.searchItems();

        this.initialState();
    }

    layout(options) {
        let jsonComponents = {
            id: "content",
            class: "d-flex mx-2 my-3 gap-2 h-100",

            contenedor: [
                {
                    type: "div",
                    id: "containerPos",
                    class:
                        "flex flex-col gap-2  col-6 col-lg-5 col-sm-6 flex-grow-1 h-[75vh]  line",

                    children: [
                        { class: "w-full py-2", id: "cardPosSelected" },
                        { class: "w-full  line flex-grow-1 overflow-x-auto scrollbar scrollbar-thin scrollbar-thumb-gray-500 scrollbar-track-gray-200", id: "cardPosTicket" },
                        { class: 'w-full line',id:'cardTotal'},
                        { class: "w-100 ", id: "cardEndTicket" },
                    ],

                },

                {
                    type: "div",
                    id: "cardPos",

                    class: " col-6 col-lg-7 col-sm-6 me-2  line",

                    children: [
                        
                        {class:'w-full text-muted font-semibold mb-2',text:'Grupos: ',type:'h2'},
                        { class: "col-12 items-center ", id: "cardPosGroup" },
                        { class: "col-12 text-end mb-2 d-flex  ", id: "containerSearch" },
                        { class: "col-12 block line ", id: "cardPosContainer" },
                    ],
                },
            ],

        };

        this.createPlantilla({
            data: jsonComponents,
            parent: this._div_modulo,
            design: false,
        });
    }

    filterBarTicket() {
       
        this.createfilterBar({
            id    : "frm",
            parent: "cardPosSelected",
            data  : [
                {
                    opc     : "btn-select",
                    id      : "NoFolio",
                    class   : "col-8 ",
                    lbl     : 'Elige una nota de venta:',
                    onchange: 'pos.cardTicket()',
                    fn: 'pos.cardTicket()',
                    icon    : "icon-search",
                    data    : tickets,
                    required: false,
                    selected: "-- Elige un folio para continuar--",
                },
                {
                    opc:'button',
                    id:'btnNewTicket',
                    class:'col-4',
                    className:'w-full',
                    text:'Nueva venta',
                    icon:' icon-shop',
                    color_btn:'success',
                    onClick:()=>{
                        this.modalNewTicket();
                    }
                }
            ],
        });


    }

    renderTicket(){
        this.cardTicket();
        this.enabledGroupButtons();
    }

    initialState(){
        
        // ticket.
        this.disabledGroupButtons({ parent: 'cardPosGroup'});
        this.disabledGroupButtons({ parent: 'cardEndTicket' });
        this.cardEmpty({ text: 'Selecciona un folio para continuar...' });
        // box.
        $('#cardPosContainer').empty();
        $('#cardTotal').empty();
    }
   
    // ticket.
    cardTicket(options) {

        
        if ($('#NoFolio').val() > 0) {
            this.enabledGroupButtons({ parent: 'cardPosGroup' });
            this.enabledGroupButtons({ parent: 'cardEndTicket' });

            this.createTable({
                parent     : 'cardPosTicket',
                idFilterBar: 'cardPosSelected',
                
                data       : { opc: 'lsTicket' },
                
                conf       : { datatable: false },

                attr       : {
                    id      : 'tablePos',
                    class   : 'table table-sm',
                    color_th: 'bg-fw-bold',
                    f_size  : '14',
                    right   : [3,4, 5],
            
                    extends : true
                },

                success:(data)=>{

                    $('#cardTotal').html(data.cardtotal);

                }
            });


        } else {

            this.initialState();        
        }
    }

    enabledGroupButtons(){

        const activeCard = $('#cardPosGroup .active');
        // Verificar si existe y obtener su id
       
        if (activeCard.length > 0) {

       
            this.CardItems({ id: activeCard.attr('id') });
       
        } else {
          
       
        }


    }
    
    modalNewTicket() {
     
        this.createModalForm({
            id: "modal",

            bootbox: { title: "NUEVA VENTA" },
            json: [
                {
                    opc: "select",
                    lbl: "Cliente",
                    id: "elaboro",
                    class: "col-12",
                    data: clients,
                },
                
                {
                    opc: "input-calendar",
                    lbl: "Fecha",
                    id: "fecha",
                },

                {
                    opc  : "select",
                    lbl  : "Tipo de venta",
                    id   : "id_tipo",
                    class: "col-12",
                    data : typeSales,
                },
                {
                    opc      : "btn-submit",
                    class    : "col-12",
                    color_btn: "primary",
                    text     : "Guardar",
                },
            ] ,
            autovalidation: true,
            data: { opc: "setNewFolio", id_Estado: 0},

            success: (data) => {
              
                data.lsFolio.unshift({ opc: 0, text: '-- seleccionar folio --' });

                $("#NoFolio").option_select({ data: data.lsFolio });
                $('#NoFolio').val(data.idFolio);
                this.cardTicket();
            },
        });

        // initialized.
        dataPicker({ parent: "fecha", type: "simple" });
        $('#elaboro').select2({ theme: "bootstrap-5", width: '100%', dropdownParent: $(".bootbox"), });
    }

    modalPaymentMethod(){
        let total = parseFloat($('#total').text().trim().replace('$', '').replace(',', '')) || 0;

        this.createModalForm({
            id: 'modalPayment',
            bootbox: { title: 'TICKET DE VENTA' }, // agregar conf. bootbox
            json: [
                {
                    opc: 'label',
                    text: 'Método de pago:',
                    class:'w-100 font-bold mb-2'
                },
                {
                    opc     : 'radio',
                    name    : 'tipo',
                    value   : 1,
                    text    : 'Efectivo',
                    class   : 'col-sm-3 col-12 text-center py-3',
                    onchange: 'toggleInputs(false)',
                    checked : true
                },
                {
                    opc     : 'radio',
                    name    : 'tipo',
                    value   : 2,
                    text    : 'Credito',
                    class   : 'col-sm-3 col-12 text-center py-3',
                    onchange: 'toggleInputs(false)',
                    checked : false
                },
                {
                    opc     : 'radio',
                    name    : 'tipo',
                    value   : 3,
                    text    : 'TDC',
                    class   : 'col-sm-3 col-12 text-center py-3',
                    onchange: 'toggleInputs(false)',
                    checked : false
                },
                {
                    opc     : 'radio',
                    name    : 'tipo',
                    value   : 4,
                    text    : 'Mixto',
                    class   : 'col-sm-3 col-12 text-center py-3',
                    onchange: 'toggleInputs(true)',
                    checked : false
                },
                {
                    opc: 'hr',
                    class:'w-full mt-1'
                },
                {
                    opc  : 'label',
                    text : '¿Como se realizo el pago ?',
                    class: 'fw-bold my-2'
                },
                {
                    opc        : 'input-group',
                    lbl        : 'Efectivo',
                    id         : 'Efectivo',
                    tipo       : 'cifra',
                    icon       : 'icon-dollar',
                    class      : 'col-sm-4 col-12 py-2',
                    placeholder: '0.00',
                    required   : false,
                    onkeyup    : 'CalculoDiferencia()'
                },
                {
                    opc        : 'input-group',
                    lbl        : 'Credito',
                    id         : 'Credito',
                    tipo       : 'cifra',
                    icon       : 'icon-dollar',
                    class      : 'col-sm-4 col-12 py-2',
                    placeholder: '0.00',
                    required   : false,
                    onkeyup    : 'CalculoDiferencia()',
                },
                {
                    opc: 'input-group',
                    lbl: 'TDC',
                    id: 'TDC',
                    tipo: 'cifra',
                    icon: 'icon-dollar',
                    class: 'col-sm-4 col-12 py-2',
                    onkeyup: 'CalculoDiferencia()',
                    required: false,
                    placeholder: '0.00',
                },
                {
                    opc: 'input-group',
                    lbl: 'Monto total:',
                    id: 'Total',
                    tipo: 'cifra',
                    icon: 'icon-dollar',
                    class: 'col-sm-6 col-12 py-2',
                    required: false,
                    disabled: true,
                    value:total,
                    placeholder: '0.00',
                },
                {
                    opc        : 'input-group',
                    lbl        : 'Diferencia',
                    tipo       : 'cifra',
                    id         : 'diferencia',
                    icon       : 'icon-dollar',
                    class      : 'col-sm-6 col-12 py-2',
                    placeholder: '0.00',
                    required   : false,
                    disabled   : true
                },
                {
                    opc  : 'btn-submit',
                    tipo : 'cifra',
                    color: 'outline-primary',
                    text : 'Cerrar venta',
                    class: 'col-12 py-3'
                },
            ],
            autovalidation: true,
            data: { 
                opc      : 'endTicket',
                Total    : total,
                id_Estado: 1,
                idLista  : $("#NoFolio").val(),
            },
            success: (data) => {
                $('#NoFolio option[value="' + $("#NoFolio").val() + '"]').remove();
                this.initialState();

            }
        });

        toogleViewDiv({ 
            parent: 'modalPayment',
            positions: [6,7,8,9,10,11]
        
        });
    
    }

    cardEndTicket() {
        // events.
        let closeTicket = () => {

            this.modalPaymentMethod();

        };

        let cancelTicket = () => {
            this.swalQuestion({
                opts: { title: `¿Esta seguro de cancelar el pedido ?` },
                data: { opc: "cancelTicket", idFolio: $("#NoFolio").val() },
                methods: {
                    request: (data) => {
                        $('#NoFolio option[value="' + $("#NoFolio").val() + '"]').remove();
                        this.cardEmpty();
                    },
                },
            });
        };

        // Components.
        this.createButtonGroup({
            parent: "cardEndTicket",
            data  : [
                {
                    id     : "btnCerrarCuenta",
                    text   : "Terminar",
                    icon   : "icon-truck",
                    color  : "outline-primary",
                    onClick: () => {
                        closeTicket();
                    },
                    class: "col-sm-6 col-12",
                },
                {
                    color  : "outline-danger",
                    class  : "col-sm-6 col-12",
                    icon   : " icon-cancel-circle",
                    onClick: () => {
                        cancelTicket();
                    },
                    text: "Cancelar",
                },
            ],
            size: "sm",
            cols: "w-50",
        });
    }

    async removeItem(idProduct) {

     await  useFetch({
        url: this._link,
        data: {
            opc: 'removeItem',
            idListaProductos: idProduct
        },

        success: ()=>{
            this.updateStatusAlmacen(idProduct,'add');
            this.cardTicket();

        }
     });

   
    }

    async addCortesia(event,idList){
        let tr = $(event.target).closest("tr");

        // Obtén la celda de la cantidad (columna 1) y el precio (columna 3)
        let cant = tr.find("td").eq(1).find("input").val();
        let costo  = tr.find("td").eq(2).text();
        let precio = tr.find("td").eq(3).text();
        let price = parseFloat(costo.replace(/[\$,\-]/g, ""));

        let totalPrice  = cant * price;

        let setCosto = price;


        if (precio.trim() === '-') {

            tr.find("td").eq(3).text(formatPrice(totalPrice)); // Restaurar la cantidad original
            tr.find("td").eq(2).removeClass("text-danger text-decoration-line-through");

        } else {
            setCosto = 0;

            tr.find("td").eq(3).text('-');
            tr.find("td").eq(2).addClass("text-danger text-decoration-line-through");
        }

        let total = getColumnTotal('tablePos', 3);

        console.log(total);

        $('#total').html(formatPrice(total));



        await useFetch({
                url: this._link,
                data: {
                    opc             : 'setCortesia',
                    costo           : setCosto,
                    idListaProductos: idList
                }
            });

    }

    updateCantidad(idProduct){
        let input = event.target;
        let row   = $(input).closest("tr");

        let cant  = row.find("td").eq(1).find("input").val();
        let price = row.find("td").eq(2).text();
        price = parseFloat(price.replace(/[\$, \-]/g, ""));
       
        let totalPrice  = cant * price;

        console.log(cant, price, totalPrice, 'id:', idProduct);
        row.find("td").eq(3).text(formatPrice(totalPrice)); 

        let total = getColumnTotal('tablePos', 3);

        $('#total').html(formatPrice(total));

       
        useFetch({
            url: this._link,
            data: {
                opc             : 'setCantidad',
                cantidad        : cant,
                idListaProductos: idProduct
            },
            success: ()=>{
                this.updateStatusAlmacen(idProduct, 'add');
            }
        });

    }

    // Box -items

    ButtonGroups() {
        this.createButtonGroup({
            parent: "cardPosGroup",
            onClick: () => {
                this.CardItems();
            },
            dataEl: {
                data: subgrupo,
                icon: "icon-shop",
            },
        });
    }

    async CardItems(options) {
        const card    = event.currentTarget;
        let   idCard  = card.getAttribute("id");
        let   idFolio = $('#NoFolio').val();


        fn_ajax({ opc: "getProductsBy", id: idCard, idFolio: idFolio , ...options }, this._link, "").then((products) => {
            this.createGrid({
                
                data  : products,
                type  : 'almacen',
                color : "bg-gray-100 text-xs",
                parent: "cardPosContainer",
                class : 'flex gap-2 grid grid-cols-5',
                image : false,
                onClick: () => {
                    this.setProducts();
                },
            });
        });

       

      
    }

    searchItems() {

        const search = () => {
            const busqueda = $('#searchInput').val();
            const products = document.querySelectorAll('.grid-container .grid-item-soft');

            products.forEach(function (product) {
                const productName = product.textContent.toLowerCase();
                if (productName.includes(busqueda.toLowerCase())) {

                    product.classList.remove('d-none');
                } else {
                    product.classList.add('d-none');
                }
            });

        };

        // Crear component de busqueda.
        let ipt = $('<input>', {
            id: 'searchInput', class: 'form-control w-100', placeholder: 'Buscar producto'
        }).on('keyup', search);

        let div = $('<div>', { class: 'col-sm-4 col-12 mt-2' }).append(ipt);
        $('#containerSearch').empty().append(div);
    }

    setProducts() {

        const card   = event.currentTarget;
        let   idCard = card.getAttribute("id");
        let   costo  = card.getAttribute("costo");

        
        const data = {
            opc        : 'setProducts',
            idFolio    : $("#NoFolio").val(),
            id_producto: idCard,
            costo      : costo,
            cantidad   : 1
        };

        fn_ajax(data, this._link).then((data) => {

            this.updateStatusAlmacen(idCard,'minus');
            this.cardTicket();
        });

    }


    updateStatusAlmacen(idCard,type){
        let activeElement = $('#cardPosGroup .active');
        let activeId = activeElement.attr('id');  // Obtén el ID del elemento activo
        let lsProducts = ()=> {
            fn_ajax({ opc: "getProductsBy", id: activeId, idFolio: $('#NoFolio').val() }, this._link, "").then((products) => {
                this.createGrid({
                    data: products,
                    type: 'almacen',
                    color: "bg-gray-100 text-xs",
                    parent: "cardPosContainer",
                    class: 'flex gap-2 grid grid-cols-5',
                    image: false,
                    onClick: () => {
                        this.setProducts();
                    },
                });
            });
        }
        if(type == 'add'){
            if (activeId) lsProducts();
        }else{
            let cantidad = parseInt($('#cantidad' + idCard).text(), 10);
        // Verifica si es un número válido
        if (!isNaN(cantidad)) {
            if(type == 'add'){
                cantidad += 1;
            }
            if(type == 'minus'){
                cantidad -= 1;
            }
            if(cantidad == 0){
                lsProducts();
            }else{
                $('#' + idCard).removeClass('disabled');
                // Actualiza el texto del elemento
                $('#cantidad' + idCard).text(cantidad);
            }
        } else {
            console.error('El valor no es un número válido');
        }
        }

    }

  

    cardEmpty() {
        $("#cardPosTicket").empty();
        $("#cardTotal").empty();

        let div = $("<div>", {
            class: "flex flex-col items-center justify-center py-4 bg-gray-100 h-100",
        });
        let icon = $("<i>", { class: "icon-cart-plus text-6xl text-primary" });
        let text = $("<p>", { class: "text-gray-500", text: "Agrega un producto" });

        div.append(icon, text);
        $("#cardPosTicket").append(div);
    }

  
    // Options.
    disabledGroupButtons(options) {
        const defaults = {
            parent: '',
            idButtonFile: 'btnfile',
            file: false,
            extends: true,
            index: null
        };

        const { parent, idButtonFile, file, index } = { ...defaults, ...options };

        const buttons = document.querySelectorAll(`#${parent} a`);
        const fileButton = file ? document.getElementById(idButtonFile) : null;

        buttons.forEach((button, i) => {
            const shouldDisable = index !== null ? i !== 0 : true;

            if (shouldDisable) {
                button.classList.add('disabled');
                button.setAttribute('disabled', 'true');

                if (fileButton) {
                    fileButton.classList.add('disabled');
                    fileButton.setAttribute('disabled', 'true');
                }
            }
        });
    }

    enabledGroupButtons(options) {

        const defaults = {
            parent: '',
            idButtonFile: 'btnfile',
            file: false,
            index: 0
        };

        const { parent, idButtonFile, file } = { ...defaults, ...options };

        const buttons = document.querySelectorAll(`#${parent} a`);
        const fileButton = file ? document.getElementById(idButtonFile) : null;

        buttons.forEach((button) => {

            button.classList.remove('disabled');
            button.removeAttribute('disabled');

            if (fileButton) {
                fileButton.classList.remove('disabled');
                fileButton.removeAttribute('disabled');
            }
        });

    }

}

// Complementos

function CalculoDiferencia() {

    let efectivo = parseFloat($("#Efectivo").val());
    let credito  = parseFloat($("#Credito").val());
    let tdc      = parseFloat($("#TDC").val());

        efectivo = isNaN(efectivo) ? 0 : efectivo;
        credito  = isNaN(credito) ? 0 : credito;
        tdc      = isNaN(tdc) ? 0 : tdc;

    let total    = efectivo + credito + tdc;

    $('#diferencia').val(total);
   
}

function toggleInputs(estatus) {


    if(estatus){
        toogleViewDiv({
            parent: 'modalPayment',
            positions: []

        });
    }else{
        toogleViewDiv({
            parent: 'modalPayment',
            positions: [6,7,8,9,10,11]

        });
    }
   
  
}
