<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-catalogo.php';
require_once '../../../conf/coffeSoft.php';

class ctrl extends mdl {

    function init() {
        return [
            'status' => 200,
            'message' => 'Módulo inicializado correctamente'
        ];
    }

    function lsCategorias() {
        $__row = [];
        
        try {
            $ls = $this->listCategorias();

            foreach ($ls as $key) {
                $__row[] = [
                    'ID' => $key['idcategoria'],
                    'Nombre' => $key['nombreCategoria'],
                    'a' => [
                        [
                            'class' => 'btn btn-sm btn-danger',
                            'html' => '<i class="icon-trash"></i>',
                            'onclick' => 'categorias.deleteCategoria(' . $key['idcategoria'] . ')'
                        ]
                    ]
                ];
            }

            return [
                'status' => 200,
                'message' => 'Categorías obtenidas correctamente',
                'row' => $__row,
                'ls' => $ls
            ];

        } catch (Exception $e) {
            error_log("[CATALOGO] Error en lsCategorias: " . $e->getMessage());
            return [
                'status' => 500,
                'message' => 'Error al obtener las categorías'
            ];
        }
    }

    function addCategoria() {
        $status = 500;
        $message = 'Error al registrar categoría';

        try {
            if (empty(trim($_POST['nombreCategoria']))) {
                return [
                    'status' => 400,
                    'message' => 'El nombre de la categoría no puede estar vacío'
                ];
            }

            $exists = $this->existsCategoria($_POST['nombreCategoria']);

            if ($exists) {
                return [
                    'status' => 409,
                    'message' => 'Ya existe una categoría con ese nombre'
                ];
            }

            $create = $this->createCategoria($this->util->sql($_POST));

            if ($create) {
                $status = 200;
                $message = 'Categoría registrada correctamente';
            }

        } catch (Exception $e) {
            error_log("[CATALOGO] Error en addCategoria: " . $e->getMessage());
            $status = 500;
            $message = 'Error del servidor';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function deleteCategoria() {
        $status = 500;
        $message = 'Error al eliminar categoría';

        try {
            $id = $_POST['id'];

            $categoria = $this->getCategoriaById($id);
            if (!$categoria) {
                return [
                    'status' => 404,
                    'message' => 'La categoría no existe'
                ];
            }

            $usage = $this->countCategoriasUsage($id);
            if ($usage > 0) {
                return [
                    'status' => 409,
                    'message' => 'No se puede eliminar la categoría porque está siendo utilizada por ' . $usage . ' registro(s)'
                ];
            }

            $delete = $this->deleteCategoriaById($id);

            if ($delete) {
                $status = 200;
                $message = 'Categoría eliminada correctamente';
            }

        } catch (Exception $e) {
            error_log("[CATALOGO] Error en deleteCategoria: " . $e->getMessage());
            $status = 500;
            $message = 'Error del servidor';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function lsAreas() {
        $__row = [];
        
        try {
            $ls = $this->listAreas();

            foreach ($ls as $key) {
                $__row[] = [
                    'ID' => $key['idArea'],
                    'Nombre' => $key['Nombre_Area'],
                    'a' => [
                        [
                            'class' => 'btn btn-sm btn-danger',
                            'html' => '<i class="icon-trash"></i>',
                            'onclick' => 'areas.deleteArea(' . $key['idArea'] . ')'
                        ]
                    ]
                ];
            }

            return [
                'status' => 200,
                'message' => 'Áreas obtenidas correctamente',
                'row' => $__row,
                'ls' => $ls
            ];

        } catch (Exception $e) {
            error_log("[CATALOGO] Error en lsAreas: " . $e->getMessage());
            return [
                'status' => 500,
                'message' => 'Error al obtener las áreas'
            ];
        }
    }

    function addArea() {
        $status = 500;
        $message = 'Error al registrar área';

        try {
            // Validar que el nombre no esté vacío
            if (empty(trim($_POST['Nombre_Area']))) {
                return [
                    'status' => 400,
                    'message' => 'El nombre del área no puede estar vacío'
                ];
            }

            // Verificar si ya existe un área con ese nombre
            $exists = $this->existsArea($_POST['Nombre_Area']);

            if ($exists) {
                return [
                    'status' => 409,
                    'message' => 'Ya existe un área con ese nombre'
                ];
            }

            // Sanitizar y crear el área
            $create = $this->createArea($this->util->sql($_POST));

            if ($create) {
                $status = 200;
                $message = 'Área registrada correctamente';
            }

        } catch (Exception $e) {
            error_log("[CATALOGO] Error en addArea: " . $e->getMessage());
            $status = 500;
            $message = 'Error del servidor';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function deleteArea() {
        $status = 500;
        $message = 'Error al eliminar área';

        try {
            $id = $_POST['id'];

            // Verificar que el área existe
            $area = $this->getAreaById($id);
            if (!$area) {
                return [
                    'status' => 404,
                    'message' => 'El área no existe'
                ];
            }

            // Verificar que no esté en uso
            $usage = $this->countAreasUsage($id);
            if ($usage > 0) {
                return [
                    'status' => 409,
                    'message' => 'No se puede eliminar el área porque está siendo utilizada por ' . $usage . ' registro(s)'
                ];
            }

            // Eliminar el área
            $delete = $this->deleteAreaById($id);

            if ($delete) {
                $status = 200;
                $message = 'Área eliminada correctamente';
            }

        } catch (Exception $e) {
            error_log("[CATALOGO] Error en deleteArea: " . $e->getMessage());
            $status = 500;
            $message = 'Error del servidor';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function lsZonas() {
        $__row = [];
        
        try {
            $ls = $this->listZonas();

            foreach ($ls as $key) {
                $__row[] = [
                    'ID' => $key['idZona'],
                    'Nombre' => $key['nombreZona'],
                    'a' => [
                        [
                            'class' => 'btn btn-sm btn-danger',
                            'html' => '<i class="icon-trash"></i>',
                            'onclick' => 'zonas.deleteZona(' . $key['idZona'] . ')'
                        ]
                    ]
                ];
            }

            return [
                'status' => 200,
                'message' => 'Zonas obtenidas correctamente',
                'row' => $__row,
                'ls' => $ls
            ];

        } catch (Exception $e) {
            error_log("[CATALOGO] Error en lsZonas: " . $e->getMessage());
            return [
                'status' => 500,
                'message' => 'Error al obtener las zonas'
            ];
        }
    }

    function addZona() {
        $status = 500;
        $message = 'Error al registrar zona';

        try {
            // Validar que el nombre no esté vacío
            if (empty(trim($_POST['nombreZona']))) {
                return [
                    'status' => 400,
                    'message' => 'El nombre de la zona no puede estar vacío'
                ];
            }

            // Verificar si ya existe una zona con ese nombre
            $exists = $this->existsZona($_POST['nombreZona']);

            if ($exists) {
                return [
                    'status' => 409,
                    'message' => 'Ya existe una zona con ese nombre'
                ];
            }

            // Sanitizar y crear la zona
            $create = $this->createZona($this->util->sql($_POST));

            if ($create) {
                $status = 200;
                $message = 'Zona registrada correctamente';
            }

        } catch (Exception $e) {
            error_log("[CATALOGO] Error en addZona: " . $e->getMessage());
            $status = 500;
            $message = 'Error del servidor';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }

    function deleteZona() {
        $status = 500;
        $message = 'Error al eliminar zona';

        try {
            $id = $_POST['id'];

            // Verificar que la zona existe
            $zona = $this->getZonaById($id);
            if (!$zona) {
                return [
                    'status' => 404,
                    'message' => 'La zona no existe'
                ];
            }

            // Verificar que no esté en uso
            $usage = $this->countZonasUsage($id);
            if ($usage > 0) {
                return [
                    'status' => 409,
                    'message' => 'No se puede eliminar la zona porque está siendo utilizada por ' . $usage . ' registro(s)'
                ];
            }

            // Eliminar la zona
            $delete = $this->deleteZonaById($id);

            if ($delete) {
                $status = 200;
                $message = 'Zona eliminada correctamente';
            }

        } catch (Exception $e) {
            error_log("[CATALOGO] Error en deleteZona: " . $e->getMessage());
            $status = 500;
            $message = 'Error del servidor';
        }

        return [
            'status' => $status,
            'message' => $message
        ];
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
