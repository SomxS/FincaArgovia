<?php
if (empty($_COOKIE["IDU"])) {
    require_once '../acceso/ctrl/ctrl-logout.php';
}

require_once 'layout/head.php';
require_once 'layout/script.php';
?>
<body>
    <?php require_once '../layout/navbar.php'; ?>
    <main>
        <section id="sidebar"></section>
        <div id="main__content">

<nav aria-label='breadcrumb'>
    <ol class='breadcrumb'>
        <li class='breadcrumb-item text-uppercase text-muted'>finanzas</li>

        <li class='breadcrumb-item fw-bold active'>Análisis de ingresos</li>
    </ol>
</nav>

<div class="row mb-3">
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
        <label class="fw-bold" for="iptDate">Rango de consulta</label>
        <div class="input-group">
            <input type="text" class="form-control" id="iptDate">
            <span class="input-group-text pointer"><i class="icon-calendar"></i></span>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3 mb-3">

        <label class="fw-bold" for="cbAnioComparativo">Año comparativo</label>

        <select class="form-select" id="cbAnioComparativo">
            <option value="2025">2025</option>
            <option value="2024">2024</option>
            <option value="2023">2023</option>
            <option value="2022">2022</option>
            <option value="2021">2021</option>
        </select>
    </div>

    <div class="col-12 col-sm-6 col-lg-2 mb-3">
        <label col="col-12"> </label>
        <button type="button" class="btn btn-primary col-12" id="btnBuscar" onclick="ConsultarIngresos()">Consultar</button>
    </div>

</div>

<div class="mb-3" id="contentData"></div>

<script src='src/js/analisis-de-ingresos.js?t=<?php echo time(); ?>'></script>
<script src='src/js/analisis-ingresos-resumen.js?t=<?php echo time(); ?>'></script>

        </div>
    </main>
</body>
</html>