<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-catalogo.php';

class ctrl extends mdl {

    function init() {
        return [
            'status' => 200
        ];
    }

    function lsCategory() {
        $active = $_POST['active'] ?? 1;
        $ls     = $this->listCategory([$active]);
        $rows   = [];

        foreach ($ls as $item) {
            $a = [];

            if ($active == 1) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'category.editCategory(' . $item['id'] . ')'
                ];
                $a[] = [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-trash"></i>',
                    'onclick' => 'category.deleteCategory(' . $item['id'] . ')'
                ];
            }

            $rows[] = [
                'id'              => $item['id'],
                'Categoría'       => $item['valor'],
                'Fecha Creación'  => $item['date_creation'],
                'Estado'          => renderStatus($item['active']),
                'a'               => $a
            ];
        }

        return [
            'row' => $rows,
            'ls'  => $ls
        ];
    }

    function getCategory() {
        $id      = $_POST['id'];
        $status  = 404;
        $message = 'Categoría no encontrada';
        $data    = null;

        $category = $this->getCategoryById([$id]);

        if ($category) {
            $status  = 200;
            $message = 'Categoría encontrada';
            $data    = $category;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function addCategory() {
        $status  = 500;
        $message = 'Error al crear categoría';

        $_POST['date_creation'] = date('Y-m-d H:i:s');
        $_POST['active']        = 1;

        $exists = $this->existsCategoryByName([$_POST['nombreCategoria']]);

        if ($exists > 0) {
            return [
                'status'  => 409,
                'message' => 'Ya existe una categoría con ese nombre'
            ];
        }

        $create = $this->createCategory($this->util->sql($_POST));

        if ($create) {
            $status  = 200;
            $message = 'Categoría creada exitosamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function editCategory() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al editar categoría';

        $edit    = $this->updateCategory($this->util->sql($_POST, 1));

        if ($edit) {
            $status  = 200;
            $message = 'Categoría actualizada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function deleteCategory() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al eliminar categoría';

        $delete  = $this->deleteCategoryById([$id]);

        if ($delete) {
            $status  = 200;
            $message = 'Categoría eliminada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function lsArea() {
        $active = $_POST['active'] ?? 1;
        $ls     = $this->listArea([$active]);
        $rows   = [];

        foreach ($ls as $item) {
            $a = [];

            if ($active == 1) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'area.editArea(' . $item['id'] . ')'
                ];
                $a[] = [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-trash"></i>',
                    'onclick' => 'area.deleteArea(' . $item['id'] . ')'
                ];
            }

            $rows[] = [
                'id'              => $item['id'],
                'Área'            => $item['valor'],
                'Fecha Creación'  => $item['date_creation'],
                'Estado'          => renderStatus($item['active']),
                'a'               => $a
            ];
        }

        return [
            'row' => $rows,
            'ls'  => $ls
        ];
    }

    function getArea() {
        $id      = $_POST['id'];
        $status  = 404;
        $message = 'Área no encontrada';
        $data    = null;

        $area    = $this->getAreaById([$id]);

        if ($area) {
            $status  = 200;
            $message = 'Área encontrada';
            $data    = $area;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function addArea() {
        $status  = 500;
        $message = 'Error al crear área';

        $_POST['date_creation'] = date('Y-m-d H:i:s');
        $_POST['active']        = 1;

        $exists = $this->existsAreaByName([$_POST['Nombre_Area']]);

        if ($exists > 0) {
            return [
                'status'  => 409,
                'message' => 'Ya existe un área con ese nombre'
            ];
        }

        $create = $this->createArea($this->util->sql($_POST));

        if ($create) {
            $status  = 200;
            $message = 'Área creada exitosamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function editArea() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al editar área';

        $edit    = $this->updateArea($this->util->sql($_POST, 1));

        if ($edit) {
            $status  = 200;
            $message = 'Área actualizada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function deleteArea() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al eliminar área';

        $delete  = $this->deleteAreaById([$id]);

        if ($delete) {
            $status  = 200;
            $message = 'Área eliminada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function lsZone() {
        $active = $_POST['active'] ?? 1;
        $ls     = $this->listZone([$active]);
        $rows   = [];

        foreach ($ls as $item) {
            $a = [];

            if ($active == 1) {
                $a[] = [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'zone.editZone(' . $item['id'] . ')'
                ];
                $a[] = [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-trash"></i>',
                    'onclick' => 'zone.deleteZone(' . $item['id'] . ')'
                ];
            }

            $rows[] = [
                'id'              => $item['id'],
                'Zona'            => $item['valor'],
                'Fecha Creación'  => $item['date_creation'],
                'Estado'          => renderStatus($item['active']),
                'a'               => $a
            ];
        }

        return [
            'row' => $rows,
            'ls'  => $ls
        ];
    }

    function getZone() {
        $id      = $_POST['id'];
        $status  = 404;
        $message = 'Zona no encontrada';
        $data    = null;

        $zone    = $this->getZoneById([$id]);

        if ($zone) {
            $status  = 200;
            $message = 'Zona encontrada';
            $data    = $zone;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function addZone() {
        $status  = 500;
        $message = 'Error al crear zona';

        $_POST['date_creation'] = date('Y-m-d H:i:s');
        $_POST['active']        = 1;

        $exists = $this->existsZoneByName([$_POST['nombre_zona']]);

        if ($exists > 0) {
            return [
                'status'  => 409,
                'message' => 'Ya existe una zona con ese nombre'
            ];
        }

        $create = $this->createZone($this->util->sql($_POST));

        if ($create) {
            $status  = 200;
            $message = 'Zona creada exitosamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function editZone() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al editar zona';

        $edit    = $this->updateZone($this->util->sql($_POST, 1));

        if ($edit) {
            $status  = 200;
            $message = 'Zona actualizada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function deleteZone() {
        $id      = $_POST['id'];
        $status  = 500;
        $message = 'Error al eliminar zona';

        $delete  = $this->deleteZoneById([$id]);

        if ($delete) {
            $status  = 200;
            $message = 'Zona eliminada correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }
}

// Complements

function renderStatus($active) {
    switch ($active) {
        case 1:
            return '<span class="inline-block px-3 py-1 rounded-2xl text-sm font-semibold bg-green-100 text-green-700 min-w-[100px] text-center">Activo</span>';
        case 0:
            return '<span class="inline-block px-3 py-1 rounded-2xl text-sm font-semibold bg-red-100 text-red-700 min-w-[100px] text-center">Inactivo</span>';
        default:
            return '<span class="inline-block px-3 py-1 rounded-2xl text-sm font-semibold bg-gray-100 text-gray-700 min-w-[100px] text-center">Desconocido</span>';
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
