<?php 
    if( empty($_COOKIE["IDU"]) )  require_once('../acceso/ctrl/ctrl-logout.php');

    require_once('layout/head.php');
    require_once('layout/script.php'); 
?>
<body>
    <?php require_once('../layout/navbar.php'); ?>
    <main>
        <section id="sidebar"></section>
        <div id="main__content">
            <link rel="stylesheet" href="src/css/colaboradores.css">
<nav class="p-2 p-sm-0" class="p-2 p-sm-0">
    <ol class="breadcrumb">
        <li class="breadcrumb-item text-uppercase text-muted">RH</li>
        <li class="breadcrumb-item fw-bold active">Colaboradores</li>
    </ol>
</nav>
<div class="row d-flex justify-content-end mb-3">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-3 mb-sm-0">
        <label class="">Unidad de negocio</label>
        <select class="form-select" id="cbUDN"></select>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
        <label class="">Filtro de colaboradores</label>
        <select class="form-select" id="">
            <option value="1">Activos</option>
            <option value="0">Inactivos</option>
        </select>
    </div>
    <div class="col-12 col-sm-12 col-md-4 col-lg-3">
        <label class="col-12 d-block hide">&nbsp;</label>
        <button class="btn btn-primary col-12" title="Añadir nuevo colaborador" id="btnNuevoColaborador">
            <i class="icon-user-add-1"></i> Colaborador
        </button>
    </div>
</div>

<div class="row" id="tbDatos">
    <table class="table table-sm table-hover table-bordered nowrap" style="width:100%;" id="tbColaborador">
        <thead>
            <tr>
                <th>COLABORADOR</th>
                <th>CUMPLEAÑOS</th>
                <th>TELÉFONO</th>
                <th>F.ALTA</th>
                <th>ANTIGUEDAD</th>
                <th>S.D.</th>
                <th>S.F.</th>
                <th>OPCIONES</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Valor 1</td>
                <td>Valor 2</td>
                <td>Valor 3</td>
                <td>Valor 4</td>
                <td>Valor 5</td>
                <td>Valor 6</td>
                <td>Valor 7</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-success" id="btnCreditosColaborador"
                        title="Créditos">
                        <i class="icon-money"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnEditarColaborador"
                        title="Editar">
                        <i class="icon-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnBajaColaborador" title="Baja">
                        <i class="icon-toggle-on"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script src="src/js/lista-de-colaboradores.js?t=<?php echo time(); ?>"></script>
        </div>
    </main>
</body>
</html>