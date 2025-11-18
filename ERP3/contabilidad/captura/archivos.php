<?php
session_start();
if (!isset($_SESSION['idUser'])) {
    header('Location: ../../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Módulo de Archivos - Contabilidad</title>
    
    <?php include '../../layout/head.php'; ?>
    
    <!-- Estilos adicionales para el módulo -->
    <style>
        .file-stats-card {
            transition: transform 0.2s ease-in-out;
        }
        .file-stats-card:hover {
            transform: translateY(-2px);
        }
        .file-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .module-ventas { color: #28a745; }
        .module-compras { color: #dc3545; }
        .module-almacen { color: #ffc107; }
        .module-tesoreria { color: #17a2b8; }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php include '../../layout/sidebar.php'; ?>
        
        <div class="main">
            <?php include '../../layout/navbar.php'; ?>
            
            <main class="content">
                <div class="container-fluid p-0">
                    <!-- Encabezado del módulo -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <h1 class="h3 mb-3">
                                <i class="align-middle" data-feather="folder"></i>
                                Módulo de Archivos
                            </h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="../../index.php">Inicio</a></li>
                                    <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
                                    <li class="breadcrumb-item active">Archivos</li>
                                </ol>
                            </nav>
                        </div>
                    </div>

                    <!-- Tarjetas de estadísticas -->
                    <div class="row mb-4" id="statsContainer">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Resumen de Archivos por Módulo</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row" id="statsCards">
                                        <!-- Las tarjetas se cargarán dinámicamente -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros y búsqueda -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="align-middle" data-feather="search"></i>
                                        Filtros de Búsqueda
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="filterBarfiles"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de archivos -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">
                                        <i class="align-middle" data-feather="list"></i>
                                        Lista de Archivos
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div id="contentfiles"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <?php include '../../layout/footer.php'; ?>
        </div>
    </div>

    <!-- Contenedor para notificaciones toast -->
    <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

    <!-- Scripts -->
    <?php include '../../layout/script.php'; ?>
    
    <!-- Scripts del módulo -->
    <script src="files.js"></script>
    
    <!-- Script de inicialización -->
    <script>
        $(document).ready(function() {
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Configurar API endpoint
            api = '../../contabilidad/captura/ctrl-files.php';
            
            // Inicializar el módulo
            if (typeof files !== 'undefined') {
                files.api = api;
            }
        });
    </script>
</body>
</html>