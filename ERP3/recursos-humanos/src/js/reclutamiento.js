$(document).ready(function () {
    const NOMBRE = $("#iptNombre");
    const APEPAT = $("#iptApePat");
    const APEMAT = $("#iptApeMat");
    const EMAIL = $("#iptEmail");
    const CURP = $("#iptCurp");
    const TEL = $("#iptTelefono");
    const GRADOESTUDIO = $("#cbGradoEstudio");
    const CARRERA = $("#iptCarrera");
    const LUGARNAC = $("#iptLugarNac");
    const CP = $("#iptCodigoPostal");
    const DIRECCION = $("#iptDireccion");
    const INGRESO = $("#iptIngreso");
    const SALARIO = $("#iptSalarioDiario");
    const SALARIOFISCAL = $("#iptSalarioFiscal");
    const PORCENTAJE = $("#iptPorcentajeAnti");
    const IMSS = $("#iptImss");
    const NSS = $("#iptNSS");
    const CUENTA = $("#iptCuenta");
    const OPINIONES = $("#iptOpiniones");

    let CRECIMIENTO = $("#iptCrecimiento");
    let RFC = $("#iptRFC");
    let GENERO = $("#iptGenero");
    let FNAC = $("#iptNacimiento");
    let EDAD = $("#iptEdad");

    let bodyModal = "";
    // let arrayFile = [];
    let solicitud = {};
    let curp = {};
    let nss = {};
    let credito = {};
    let acta = {};
    let ine = {};
    let comprobante = {};
    let carta = {};

    // Validar campos
    NOMBRE.on("keyup", () => {
        const expReg = /^[a-zA-ZÀ-ÿ\s]{1,40}$/;
        const isValid = expReg.test(NOMBRE.val());
        NOMBRE.toggleClass("is-invalid", !isValid);
        NOMBRE.next("span").toggleClass("d-none", isValid);
    });

    APEPAT.on("keyup", () => {
        const expReg = /^[a-zA-ZÀ-ÿ\s]{1,40}$/;
        const isValid = expReg.test(APEPAT.val());
        APEPAT.toggleClass("is-invalid", !isValid);
        APEPAT.next("span").toggleClass("d-none", isValid);
    });

    APEMAT.on("keyup", () => {
        const expReg = /^[a-zA-ZÀ-ÿ\s]{1,40}$/;
        const isValid = expReg.test(APEMAT.val());
        APEMAT.toggleClass("is-invalid", !isValid);
        APEMAT.next("span").toggleClass("d-none", isValid);
    });

    EMAIL.on("keyup", () => {
        const expReg = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
        const isValid = expReg.test(EMAIL.val());
        EMAIL.toggleClass("is-invalid", !isValid);
        EMAIL.next("span").toggleClass("d-none", isValid);
    });

    CURP.on("input", () => {
        const expReg = /^[a-zA-Z0-9\s]*$/;
        const isValid = expReg.test(CURP.val());
        CURP.toggleClass("is-invalid", !isValid);
        CURP.next("span").toggleClass("d-none", isValid);
        if (CURP.val().length > 18) {
            CURP.val(CURP.val().slice(0, 18));
        }
    });

    CURP.on("blur", () => {
        let curp = CURP.val();
        let mes = curp.slice(6, 8);
        let dia = curp.slice(8, 10);
        let year = new Date().getFullYear();
        let ynow = year.toString().slice(2, 4);
        let y1 = curp.slice(4, 6);

        if (parseInt(y1) > parseInt(ynow)) {
            year = parseInt(y1) + 1900;
        } else if (parseInt(y1) <= parseInt(ynow)) {
            year = parseInt(y1) + 2000;
        }

        let estructura = {};
        estructura.date = year + "-" + mes + "-" + dia;
        estructura.genero = curp.slice(10, 11);
        estructura.rfc = curp.slice(0, 10);
        let edad = calcularEdadYDiferencia(estructura.date);

        if (estructura.genero == "H" || estructura.genero == "h") {
            GENERO.val("Masculino");
        } else if (estructura.genero == "M" || estructura.genero == "m") {
            GENERO.val("Femenino");
        }
        RFC.val(estructura.rfc);
        EDAD.val(edad);
        FNAC.val(estructura.date);
    });

    TEL.on("keyup", () => {
        TEL.val(TEL.val().replace(/[^\d]/g, "").slice(0, 10));
    });

    GRADOESTUDIO.on("change", () => {
        CARRERA.prop(
            "disabled",
            ["0", "1", "2", "3"].includes(GRADOESTUDIO.val())
        );
    });

    LUGARNAC.on("keyup", () => {
        const expReg = /^[a-zA-ZÀ-ÿ\s.,]{1,40}$/;
        const isValid = expReg.test(LUGARNAC.val());
        LUGARNAC.toggleClass("is-invalid", !isValid);
        LUGARNAC.next("span").toggleClass("d-none", isValid);
    });

    CP.on("keyup", () => {
        CP.val(CP.val().replace(/[^\d]/g, "").slice(0, 5));
    });

    DIRECCION.on("keyup", () => {
        let expReg = /^[a-zA-Z0-9À-ÿ\s.]{1,90}$/;
        const isValid = expReg.test(DIRECCION.val());
        DIRECCION.toggleClass("is-invalid", !isValid);
        DIRECCION.next("span").toggleClass("d-none", isValid);
    });

    SALARIO.on("input", () => {
        let expReg = SALARIO.val().replace(/[^\d.]/g, "");
        let decimal = expReg.split(".").length;
        if (decimal > 1) {
            let parts = expReg.split(".");
            expReg = parts[0] + "." + parts.slice(1).join("");
        }
        SALARIO.val(expReg);
    });

    SALARIOFISCAL.on("input", () => {
        let expReg = SALARIOFISCAL.val().replace(/[^\d.]/g, "");
        let decimal = expReg.split(".").length;
        if (decimal > 1) {
            let parts = expReg.split(".");
            expReg = parts[0] + "." + parts.slice(1).join("");
        }
        SALARIOFISCAL.val(expReg);
    });

    PORCENTAJE.on("input", () => {
        let expReg = PORCENTAJE.val().replace(/[^\d.]/g, "");
        let decimal = expReg.split(".").length;
        if (decimal > 1) {
            let parts = expReg.split(".");
            expReg = parts[0] + "." + parts.slice(1).join("");
        }
        PORCENTAJE.val(expReg);
    });

    NSS.on("keyup", () => {
        NSS.val(NSS.val().replace(/[^\d]/g, "").slice(0, 11));
    });

    CUENTA.on("keyup", () => {
        CUENTA.val(CUENTA.val().replace(/[^\d]/g, "").slice(0, 16));
    });

    OPINIONES.on("keyup", () => {
        let expReg = /^[a-zA-Z0-9À-ÿ\s.]{1,90}$/;
        const isValid = expReg.test(OPINIONES.val());
        OPINIONES.toggleClass("is-invalid", !isValid);
        OPINIONES.next("span").toggleClass("d-none", isValid);
    });

    INGRESO.on("blur", () => {
        let edad = calcularEdadYDiferencia(INGRESO.val());
        CRECIMIENTO.val(edad);
    });

    $("#file-profile").on("change", () => {
        var file = $("#file-profile")[0].files[0];
        if (file) {
            $("#photo__perfil span").css({ display: "flex" });
            $("#photo__perfil span").html(
                '<i class="animate-spin icon-spin6"></i> Actualizando'
            );
            setTimeout(() => {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $("#imgPerfil").attr("src", e.target.result);
                };
                reader.readAsDataURL(file);
                $("#photo__perfil span").hide();
                $("#photo__perfil span").removeAttr("style");
                $("#photo__perfil span").html(
                    '<i class="icon-camera"></i><br>SUBIR FOTO'
                );
            }, 1000);
        } else {
            $("#imgPerfil").attr("src", "#");
        }
    });

    $("#formDatos").on("submit", function (e) {
        e.preventDefault();
        let formData = new FormData();
        formData.append("solicitud", JSON.stringify(solicitud));
        formData.append("acta", JSON.stringify(acta));
        formData.append("curp", JSON.stringify(curp));
        formData.append("ine", JSON.stringify(ine));
        formData.append("nss", JSON.stringify(nss));
        formData.append("comprobante", JSON.stringify(comprobante));
        formData.append("credito", JSON.stringify(credito));
        formData.append("carta", JSON.stringify(carta));

        formData.append("photo", $("#file-profile")[0].files[0]);
        formData.append("nombre", NOMBRE.val());
        formData.append("apepat", APEPAT.val());
        formData.append("apemat", APEMAT.val());
        formData.append("email", EMAIL.val());
        formData.append("curp", CURP.val());
        formData.append("tel", TEL.val());
        formData.append("gradoestudio", GRADOESTUDIO.val());
        formData.append("carrera", CARRERA.val());
        formData.append("lugarnac", LUGARNAC.val());
        formData.append("cp", CP.val());
        formData.append("direccion", DIRECCION.val());
        formData.append("ingreso", INGRESO.val());
        formData.append("salario", SALARIO.val());
        formData.append("salariofiscal", SALARIOFISCAL.val());
        formData.append("porcentaje", PORCENTAJE.val());
        formData.append("imss", IMSS.val());
        formData.append("nss", NSS.val());
        formData.append("cuenta", CUENTA.val());
        formData.append("opiniones", OPINIONES.val());
        formData.append("crecimiento", CRECIMIENTO.val());
        formData.append("rfc", RFC.val());
        formData.append("genero", GENERO.val());
        formData.append("fnac", FNAC.val());
        formData.append("edad", EDAD.val());
        console.log(formData);
        $.ajax({
            url: "ctrl/avatar.php",
            type: "POST",
            data: formData,
            success: function (data) {},
            cache: false,
            contentType: false,
            processData: false,
        });
    });
});

function calcularEdadYDiferencia(fechaNacimiento) {
    var fechaActual = new Date();
    var fechaNac = new Date(fechaNacimiento);

    var años = fechaActual.getFullYear() - fechaNac.getFullYear();
    var meses = fechaActual.getMonth() - fechaNac.getMonth();
    var dias = fechaActual.getDate() - fechaNac.getDate();

    if (fechaActual.getFullYear() === fechaNac.getFullYear()) {
        if (fechaActual.getMonth() === fechaNac.getMonth()) {
            if (fechaActual.getDate() <= fechaNac.getDate()) {
                años = 0;
                meses = 0;
                dias = 0;
            }
        }
    }
    if (fechaActual.getMonth() < fechaNac.getMonth()) {
        años = 0;
        meses = 0;
        dias = 0;
    }

    if (meses < 0 || (meses === 0 && dias < 0)) {
        años--;
        meses += 12;
    }

    if (dias < 0) {
        var ultimoDiaMesAnterior = new Date(
            fechaActual.getFullYear(),
            fechaActual.getMonth(),
            0
        ).getDate();
        dias += ultimoDiaMesAnterior;
        meses--;
    }

    let datos = {
        edad: años,
        mesesDesdeUltimoCumple: meses,
        diasDesdeUltimoCumple: dias,
    };

    let edadText = "";
    if (!isNaN(datos.diasDesdeUltimoCumple)) {
        if (datos.edad != 0) {
            edadText += datos.edad + " años ";
        }
        if (datos.mesesDesdeUltimoCumple != 0) {
            if ((datos.edad = 1)) {
                edadText += datos.mesesDesdeUltimoCumple + " mes ";
            } else {
                edadText += datos.mesesDesdeUltimoCumple + " meses ";
            }
        }
        if (datos.diasDesdeUltimoCumple != 0) {
            edadText += datos.diasDesdeUltimoCumple + " días";
        }
    } else {
        edadText = "Inválido";
    }

    return edadText;
}

// Creación modal
function modalCheckbox(id, title) {
    let multiple = "";
    if (id == 8) multiple = "multiple";
    if (!$(`#iptCk${id}`).is(":checked")) {
        bodyModal = bootbox.dialog({
            title: title,
            centerVertical: true,
            message: `
              <div class="col-12 mb-3">  
                  <input type="file" id="file-7" class="hide" onchange="spanFile();" ${multiple}/>
                  <button class="btn btn-primary" onClick="$('#file-7').click();" >Seleccionar archivo</button>
                  <br>
                  <span class="text-info" id="spanFile"></span>
              </div>
              <div>
                  <table class="table table-bordered table-sm">
                      <thead>
                          <tr>
                              <th>#</th>
                              <th>Nombre</th>
                              <th>Peso</th>
                              <th>Tipo</th>
                          </tr>
                      </thead>
                      <tbody id="trFile">
                      </tbody>
                  </table>
              </div>
              <hr/>
              <div class="col-12 mb-3 d-flex justify-content-between">
                  <button  class="btn btn-primary col-5" onclick="afterCheckbox(${id});" id="gv-btn-modalCheckbox" disabled>Continuar</button>
                  <button class="btn btn-danger col-5" onClick="closeModalCheckbox(${id});">Cancelar</button>
              </div> 
          `,
        });
    } else {
        $(`#iptCk${id}`).next("label").html('<i class=""></i>');
        $("#spanFile").html("");
        $("#trFile").html("");
        $(`#labelCk${id}`).next("span").html("");
        $("#file-7").val("");
        // arrayFile = [];
        if (id == 1) {
            solicitud = {};
        } else if (id == 2) {
            acta = {};
        } else if (id == 3) {
            curp = {};
        } else if (id == 4) {
            ine = {};
        } else if (id == 5) {
            nss = {};
        } else if (id == 6) {
            comprobante = {};
        } else if (id == 7) {
            credito = {};
        } else if (id == 8) {
            carta = {};
        }
    }
}

function spanFile() {
    const files = $("#file-7").prop("files");
    let tr = "";
    $("#trFile").html(tr);
    if (files.length > 0) {
        $("#gv-btn-modalCheckbox").removeAttr("disabled");
        $("#spanFile").html(
            `<i class="icon-clipboard"></i> [${files.length}] Archivos seleccionados`
        );
        for (var i = 0; i < files.length; i++) {
            $("#trFile").append(`<tr>
          <td>${i + 1}</td>
          <td>${files[i].name}</td>
          <td>${files[i].size} bytes</td>
          <td>${files[i].type}</td>
        </tr>`);
        }
    }
}

function closeModalCheckbox(id) {
    bodyModal.modal("hide");
    $(`#iptCk${id}`).prop("checked", false);
}

function afterCheckbox(id) {
    $(`#iptCk${id}`).prop("checked", true);
    $(`#iptCk${id}`).next("label").html('<i class="icon-ok"></i>');
    bodyModal.modal("hide");

    let files = $("#file-7").prop("files");
    cantidad = files.length;
    $(`#labelCk${id}`).next("span").html(`(${cantidad})`);

    let formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        if (id == 1) {
            solicitud[i] = files[i];
        } else if (id == 2) {
            acta[i] = files[i];
        } else if (id == 3) {
            curp[i] = files[i];
        } else if (id == 4) {
            ine[i] = files[i];
        } else if (id == 5) {
            nss[i] = files[i];
        } else if (id == 6) {
            comprobante[i] = files[i];
        } else if (id == 7) {
            credito[i] = files[i];
        } else if (id == 8) {
            carta[i] = files[i];
        }

        // arrayFile.push(
        //   solicitud[i],
        //   curp[i],
        //   nss[i],
        //   credito[i],
        //   acta[i],
        //   ine[i],
        //   comprobante[i],
        //   carta[i]
        // );
        // formData.append("file", files[i]);
    }
    //   console.log(formData);
    //   console.log(arrayFile);
    //   console.log(acta);
}
