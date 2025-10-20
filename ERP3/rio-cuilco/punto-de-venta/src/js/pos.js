
const ctrlPos = 'ctrl/ctrl-pos.php';

class Pos extends Templates {

    constructor(link, div_modulo) {
        super(link, div_modulo);

    }

    initComponents() {
        this.cleanTicket();
        this.disabledGroupButtons();
    }

    listFols() {
        fn_ajax({ opc: 'listFols' }, this._link).then((data) => {
            listFols = data.listFols;
        });
    }

    modalNuevoTicket() {

        // data for modal.

        let nota_venta = [
            {
                opc: "select",
                lbl: "Cliente/Destino",
                id: "id_cliente",
                data: clientes

            },
            {
                opc: "input-calendar",
                lbl: "Fecha de envio",
                id: "foliofecha",
            },

            {
                opc: "textarea",
                lbl: "Nota:",
                id: "observacion",
                class: 'col-12',
                tipo: "texto",
            },

            {
                opc: 'btn-submit',
                class: 'col-12',
                color_btn: 'primary',
                text: 'Guardar',
                // fn: 'pos.nuevoTicket()',
            }
        ];


        this.createModalForm({
            id: 'modal',

            bootbox       : { title: '' },          // agregar conf. bootbox
            json          : nota_venta,
            autovalidation: true,
            data          : { opc: 'newTicket' },

            success: (data) => {

                alert({ title: 'Se ha creado un nuevo ticket ' });
                $('#NoFolio').option_select({ data: data.lsFolio });
                // $('#NoFolio').val(data.idFolio);


                this.openTicket();

            }
        });


        // initialized. 
        dataPicker({ parent: 'foliofecha', type: 'simple' });
        $('#Destino').select2({ theme: "bootstrap-5", width: '100%', dropdownParent: $(".bootbox"), });




    }

    openTicket(options) {

      if( $('#NoFolio').val() ){

          this.enabledGroupButtons();

          this.createTable({
  
              parent: 'containerTicket',
              idFilterBar: 'filterTicket',
              data: { opc: 'openTicket' },
  
              conf: {
                  datatable: false,
                  ...options
              },
  
              attr: {
                  class   : 'table table-sm',
                  color_th: 'bg- fw-bold',
                  f_size  : '12',
                  right   : [4, 5],
                  center  : [2, 3]
              },
  
  
          });
      }  else{
          this.disabledGroupButtons();
      }
    }

    cerrarTicket(id) {
        let title = $('#NoFolio option:selected ').text();


        this.swalQuestion({
            opts: {
                title: '¿ Deseas concluir el siguiente pedido ' + title + ' ?',
            },
            data: { opc: 'cerrarTicket', idFolio: $('#NoFolio').val() },
            methods: {
                request: (data) => {
                    this.cleanTicket();
                }
            }
        });


    }

    cleanTicket() {
        $('#containerTicket').empty_state({ text: '', icon: '' });
    }

    cancelarTicket() {

        let title = $('#NoFolio option:selected ').text();


        this.swalQuestion({

            opts: {
                title: `¿Esta seguro de cancelar el pedido ${title} ?`,
            },
            data: {
                opc: 'cancelarTicket',
                idFolio: $('#NoFolio').val()


            },
            methods: {
                request: (data) => {
                    $('#NoFolio option[value="' + $('#NoFolio').val() + '"]').remove();
                    this.cleanTicket();
                }
            }

        });



    }

    // Tablero elements

    addComments(){


        this.createModalForm({
            id: 'modalComments',

            bootbox: { title: 'Nota.' }, // agregar conf. bootbox
            json: [
            
             { 
                opc: 'textarea',
                id: 'newComments',
                value: '',
                placeholder:'Escribe un comentario.'
             }
            
            ],
            autovalidation: true,
            data: { opc: 'addComments', id: 1 },

            success: (data) => { }
        });

    }

    addFile(){

        var archivos  = document.getElementById("iptFile");
        var archivo   = archivos.files;
        let cant_file = archivo.length;

        if (cant_file > 0) {

            var filarch = new FormData();

            for (let i = 0; i < archivo.length; i++) {
                filarch.append("archivo" + i, archivo[i]);
            }

            filarch.append("opc", "addFile");
            filarch.append("idfolio", $('#NoFolio').val());

            $.ajax({

                url        : this._link,
                type       : "POST",
                contentType: false,
                
                data       : filarch,
                processData: false,
                cache      : false,

                success: function (data) {


                    $("#iptFile").val("");
                 
                },
            });

        }

    }




    // complements.


    modalComplements(idProducts) {

        //   json.
        let ComplementsEl = [
            {

                opc: "select",
                lbl: "Producto",
                id: "id_producto_adicional",
                data: products,
                onchange: 'pos.getPrice()'

            },

            {
                opc: "input",
                lbl: "Cantidad",
                id: "cantidad",
                tipo: "cifra",
                onkeyup: 'pos.calcularTotal()',
                placeholder: "0",
            },

            {
                opc: "input-group",
                lbl: "Precio",
                id: "Precio",
                tipo: "cifra",
                class: 'col-6',
                placeholder: "0.00",
                icon: "icon-dollar",
                required: false,
                // disabled   : true,
            },

            {
                opc: "input-group",
                lbl: "Total",
                id: "TotalComplemento",
                tipo: "cifra",
                holder: "0.00",
                class: 'col-6',
                disabled: true,
                required: false,
                icon: "icon-dollar",
            },

            {

                opc: 'btn-submit',
                class: 'col-12',
                color_btn: 'primary',
                text: 'Guardar',

            }
        ];


        //  view modal.
        this.createModalForm({

            id: 'frmComplements',
            bootbox: { title: '', closeButton: true, },
            components: this.createLayaoutComplements(),

            json: ComplementsEl,
            autovalidation: true,
            data: { opc: 'addComplements', id_referencia: idProducts },

            success: (data) => { }
        });

        // initialized.
        this.listComplements();
        $('#FlorComplemento').select2({ theme: "bootstrap-5", width: '100%', dropdownParent: $(".bootbox") });
    }

    closeComplements() {

    }


    btnGroups() {
        this.createButtonGroup({
            dataEl: { data: category, icon: 'icon-shop', onClick: 'pos.btnSubGroups' }
        });
    }


    btnSubGroups(id) {


        fn_ajax({ opc: 'subGrupo', idgrupo: id }, ctrl).then((data) => {

            data.unshift({ id: 0, icon: ' icon-left-big', valor: 'Regresar', onClick:'pos.btnGroups()' });


            this.createButtonGroup({
                
                dataEl: { data: data, icon: 'icon-shop', onClick: 'pos.listItem' }
            });
        });
    }


    listComplements() {


        this.createTable({

            parent: 'containerComplements',

            data: { opc: 'listComplements', id: 1 },

            conf: { datatable: false, beforeSend: false },

            attr: {
                right: [2, 3, 4]
            },


        });
    }

    createLayaoutComplements() {
        // Crear el formulario y la tabla dentro de contenedores
        let form = $('<form>', { class: 'col-12 block', id: 'frmComplements', novalidate: true });
        let table = $('<div>', { class: 'col-12 block', id: 'containerComplements' });
        let container = $('<div>').append(form, table);

        return container;
    }


    // Operations.

    disabledGroupButtons(){

        const buttons = document.querySelectorAll('#btnGroups a');
        const file    = document.getElementById('btnfile');

        buttons.forEach((button, index) => {
            if (index === 0) {
                // Este es el botón de agregar cliente, no lo deshabilitamos
                button.classList.remove('disabled');
                button.removeAttribute('disabled');
            } else {
                // Deshabilitamos todos los demás botones
                file.classList.add('disabled');
                button.classList.add('disabled');
                button.setAttribute('disabled', 'true');
            }
        });

    }
    
    enabledGroupButtons(){

        const buttons = document.querySelectorAll('#btnGroups a');
        const file = document.getElementById('btnfile');


        buttons.forEach((button, index) => {
            if (index !== 0) {
                // Habilitamos todos excepto el de agregar cliente
                button.classList.remove('disabled');
                file.classList.remove('disabled');
                button.removeAttribute('disabled');
            }
        });

    }

    listItem(id) {


        fn_ajax({
            opc: 'listItem',
            sub: id
        }, this._link, "").then((data) => {

            this.createGrid({ parent: 'containerBox', data: data.row ,type: '' });


        });


    }

    addItem(idProducts, costo) {


        const data = {
            opc: 'addItem',
            idFolio: $("#NoFolio").val(),
            id_producto: idProducts,
            costo: costo

        };



        fn_ajax(data, this._link).then((data) => {

            this.openTicket({ beforeSend: false });

        });


    }


    removeItem(idProduct) {

        this.swalQuestion({
            opts: {
                title: '¿Deseas quitar el siguiente producto de la lista?',
            },
            data: {
                opc: 'removeItem',
                id: idProduct
            },
            methods: {
                request: (data) => {
                    this.openTicket({ beforeSend: false });
                }
            }
        });
    }

    getPrice() {

        let idProducto = $("#id_producto_adicional").val();

        if (idProducto) {

            let results = products.filter((json) => json.id == idProducto);

            $('#Precio').val(results[0].precio);

        } else {
            //     $('#costo').val(0);
        }

        // setTimeout(() => {

        //     $('#cantidad').focus();
        // }, 200);


    }

    calcularTotal() {

        let cantidad = $("#cantidad").val();
        let precio = $("#Precio").val();

        let total = cantidad * precio;

        $("#TotalComplemento").val(total.toFixed(2));
    }


    useAjax(options) {

        var defaults = {

        };



        $.ajax({
            type: "POST",
            url: url,
            contentType: false,
            data: datos,
            processData: false,
            cache: false,
            dataType: "json",
            beforeSend: () => {
                $(div).Loading();
            },
            success: (data) => {
                resolve(data);
            },
            error: function (xhr, status, error) {
                swal_error(xhr, status, error);
            },
        });



    }






}
