window.ctrl = window.ctrl || "ctrl/ctrl-lista-de-colaboradores.php";
window.bodyModal = window.bodyModal || "";
$(document).ready(function () {
    let datos = new FormData();
    datos.append("opc", "listUDN");
    send_ajax(datos, ctrl).then((data) => {
        let option = "";
        data.forEach((p) => {
            option += `<option value="${p.id}">${p.valor}</option>`;
        });
        $("#cbUDN").html(option);
    });

    prioridad = [
      {
        responsivePriority: 1,
        targets: 0,
      },
    ];
    dataTable_responsive("#tbColaborador", prioridad);

    $("#btnNuevoColaborador").on("click", () => {
      redireccion("recursos-humanos/reclutamiento.php");
    });
    $("#btnBajaColaborador").on("click", () => {
      modalColaborador();
    });
    $("#btnEditarColaborador").on("click", () => {
      editarColaborador(1);
    });
    $("#btnCreditosColaborador").on("click", () => {
      creditosColaborador();
    });
  });
  
  function editarColaborador(id) {
    localStorage.setItem("idColaborador", id);
    redireccion("recursos-humanos/editar-colaborador.php");
  }
  function creditosColaborador(id) {
    localStorage.setItem("idColaborador", id);
    redireccion("recursos-humanos/creditos.php");
  }
  
  function modalColaborador() {
    bodyModal = bootbox.dialog({
      title: "Baja de '$Colaborador'",
      centerVertical: true,
      message: `
          <div class="row mb-3">
              <div class="col-12 col-sm-6 col-md-5 ms-auto">
                  <input type="date" class="form-control" id="iptFechaBajaColaborador" placeholder="01-01-2005"
                      aria-label="Fecha baja">
              </div>
          </div>
          <div class="row mb-3">
              <div class="col-12 mb-3 ms-auto">
              <label for="iptObservacionBaja" class="form-label fw-bold">Observaciones</label>
                  <textarea class="form-control" id="iptObservacionBaja" placeholder="Escribe algo..."></textarea>
              </div>
          </div>
          <hr/>
          <div class="col-12 mb-3 d-flex justify-content-between">
              <button  class="btn btn-primary col-5" onclick="">Confirmar</button>
              <button class="btn btn-outline-danger col-5 bootbox-close-button" >Cancelar</button>
          </div> 
      `,
    });
  }
  