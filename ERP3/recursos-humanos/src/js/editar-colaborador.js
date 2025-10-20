$(document).ready(function () {
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
});
