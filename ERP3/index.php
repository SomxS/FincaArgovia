<?php
if(isset($_COOKIE['IDU'])){
    echo '<script> 
        let ruta = localStorage.getItem("url");
        const MODELO = ruta.split("/").filter(Boolean)[0];
        const HREF = new URL(window.location.href);
        const ERP = HREF.pathname.split("/").filter(Boolean)[0];
        const RUTA = HREF.origin + "/" + ERP + "/" + ruta;
        window.location.href = HREF.origin + "/" + ERP + "/" + ruta;
    </script>';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="src/img/logos/logo_icon.png" type="image/x-icon">
    <title>15-92</title>
    <link rel="stylesheet" href="src/plugin/fontello/css/fontello.css">
    <link rel="stylesheet" href="src/plugin/fontello/css/animation.css">
    <link rel="stylesheet" href="src/plugin/bootstrap-5/css/bootstrap.min.css">
    <link rel="stylesheet" href="src/plugin/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="src/css/index.css">
</head>

<body>
    <section>
        <div id="logo">
            <img src="src/img/logos/logo_row.png" alt="">
        </div>
        <div id="form" class="p-5">
            <form id="form_login" novalidate>
                <h4>¡ B i e n v e n i d o !</h4>
                <div class="col-12 input-group mt-5 mb-4">
                    <span class="input-group-text"><i class="icon-user"></i></span>
                    <input type="text" class="form-control" name="usuario" id="usuario" placeholder="Usuario" required>
                </div>
                <div class="col-12 input-group mb-4">
                    <span class="input-group-text"><i class="icon-key"></i></span>
                    <input type="password" class="form-control" name="clave" id="clave" placeholder="• • • • • • •" required>
                    <span class="input-group-text pointer" id="btnEye"><i class="icon-eye"></i></span>
                </div>
                <div class="col-12 mb-4">
                    <button type="submit" class="col-12 btn btn-success">Iniciar sesión</button>
                </div>
                <div class="col-12 text-center">
                    <u class="pointer">¿Olvidaste tu contraseña?</u>
                </div>
            </form>
        </div>
    </section>


    <script src="src/plugin/jquery/jquery-3.7.0.min.js"></script>
    <script src="src/plugin/bootstrap-5/js/bootstrap.min.js"></script>
    <script src="src/plugin/bootbox.min.js"></script>
    <script src="src/plugin/sweetalert2/sweetalert2.all.min.js"></script>
    <script src="src/js/complementos.js"></script>
    <script src="src/js/plugin-forms.js"></script>
    <script src="acceso/src/js/index.js?t=<?php echo time(); ?>"></script>
</body>

</html>