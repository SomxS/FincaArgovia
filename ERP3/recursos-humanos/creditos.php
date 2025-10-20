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
            <nav aria-label="breadcrumb" class="p-2 p-sm-0">
    <ol class="breadcrumb">
        <li class="breadcrumb-item text-uppercase text-muted">RH</li>
        <li class="breadcrumb-item text-uppercase pointer" onClick="redireccion('recursos-humanos/lista-de-colaboradores.php');"><u><i>Colaboradores</i></u>
        </li>
        <li class="breadcrumb-item fw-bold active">Créditos</li>
    </ol>
</nav>
<div class="row mb-3">
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <label for="cbUDN">Seleccionar UDN</label>
        <select class="form-select" id="cbUDN"></select>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <label for="ipt">Búsqueda</label>
        <div class="input-group">
            <input type="text" class="form-control" id="ipt">
            <span class="input-group-text"><i class="icon-search"></i></span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <label for="iptDate">Fecha</label>
        <div class="input-group">
            <input type="text" class="form-control" id="iptDate">
            <span class="input-group-text"><i class="icon-calendar"></i></span>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <label col="col-12"> </label>
        <button type="button" class="btn btn-primary col-12" id="btnOk">BOTÓN</button>
    </div>
</div>

<div class="row" id="tbDatos">
    <table class="table table-sm table-hover">
        <thead>
            <tr>
                <th>Titulo 1</th>
                <th>opciones</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Celda 1</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary" onClick="updateModal(1,'Celda 1');">
                        <i class="icon-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" estado="1" id="btnStatus1"
                        onClick="toggleStatus(1)">
                        <i class="icon-toggle-on"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td>Celda 2</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary" onClick="updateModal(2,'Celda 2');">
                        <i class="icon-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" estado="1" id="btnStatus2"
                        onClick="toggleStatus(2)">
                        <i class="icon-toggle-on"></i>
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<script src='src/js/creditos.js?t=1695499400'></script>
        </div>
    </main>
</body>
</html>