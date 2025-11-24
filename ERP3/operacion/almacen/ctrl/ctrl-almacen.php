<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-almacen.php';

class ctrl extends mdl {

    function init() {
        return [
            'zones'      => $this->lsZones(),
            'categories' => $this->lsCategories(),
            'areas'      => $this->lsAreas()
        ];
    }

    function lsMateriales() {
        $filters = [
            'zone'     => $_POST['zone'] ?? '',
            'category' => $_POST['category'] ?? '',
            'area'     => $_POST['area'] ?? '',
            'search'   => $_POST['search'] ?? ''
        ];

        $data = $this->listMateriales($filters);
        $rows = [];
        $totalValue = 0;

        foreach ($data as $item) {
            $value = floatval($item['cantidad']) * floatval($item['Costo']);
            $totalValue += $value;

            $a = [
                [
                    'class'   => 'btn btn-sm btn-primary me-1',
                    'html'    => '<i class="icon-pencil"></i>',
                    'onclick' => 'app.editMaterial(' . $item['id'] . ')'
                ],
                [
                    'class'   => 'btn btn-sm btn-danger',
                    'html'    => '<i class="icon-trash"></i>',
                    'onclick' => 'app.deleteMaterial(' . $item['id'] . ')'
                ]
            ];

            $rows[] = [
                'id'       => $item['id'],
                'Foto'     => [
                    'class' => 'justify-start px-2 py-2',
                    'html'  => renderProductImage($item['rutaImagen'], $item['Equipo'])
                ],
                'Código'   => $item['CodigoEquipo'],
                'Zona'     => $item['zona'] ?? '-',
                'Equipo'   => $item['Equipo'],
                'Categoría' => $item['categoria'] ?? '-',
                'Área'     => $item['area'] ?? '-',
                'Cantidad' => $item['cantidad'],
                'Costo'    => [
                    'html'  => '$' . number_format($item['Costo'], 2),
                    'class' => 'text-end'
                ],
                'Estado'   => renderStatus($item['Estado']),
                'a'        => $a
            ];
        }

        return [
            'row'         => $rows,
            'total_value' => '$' . number_format($totalValue, 2)
        ];
    }

    function getMaterial() {
        $id = $_POST['id'];
        $status = 404;
        $message = 'Material no encontrado';
        $data = null;

        $material = $this->getMaterialById($id);

        if ($material) {
            $status = 200;
            $message = 'Material encontrado';
            $data = $material;
        }

        return [
            'status'  => $status,
            'message' => $message,
            'data'    => $data
        ];
    }

    function addMaterial() {
        $status = 500;
        $message = 'No se pudo agregar el material';
        
        $_POST['FechaIngreso'] = date('Y-m-d H:i:s');
        $_POST['Estado'] = 1;

        $exists = $this->existsMaterialByCode($_POST['CodigoEquipo']);

        if (!$exists) {
            $create = $this->createMaterial($this->util->sql($_POST));
            if ($create) {
                $status = 200;
                $message = 'Material agregado correctamente';
            }
        } else {
            $status = 409;
            $message = 'Ya existe un material con ese código';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function editMaterial() {
        $status = 500;
        $message = 'Error al editar material';

        $edit = $this->updateMaterial($this->util->sql($_POST, 1));

        if ($edit) {
            $status = 200;
            $message = 'Material editado correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }

    function deleteMaterial() {
        $status = 500;
        $message = 'No se pudo eliminar el material';

        $delete = $this->deleteMaterialById($_POST['id']);

        if ($delete) {
            $status = 200;
            $message = 'Material eliminado correctamente';
        }

        return [
            'status'  => $status,
            'message' => $message
        ];
    }
}

// Complements

function renderProductImage($foto, $nombre) {
    $src = !empty($foto) ? $foto : '';

    $img = !empty($src)
        ? '<img src="' . htmlspecialchars($src) . '" alt="Imagen Material" class="w-10 h-10 bg-gray-500 rounded-md object-cover" />'
        : '<div class="w-12 h-12 bg-[#1F2A37] rounded-md flex items-center justify-center">
                <i class="icon-box text-gray-500"></i>
           </div>';

    return '
        <div class="flex items-center justify-start gap-2">
            ' . $img . '
            <div class="text-sm">' . htmlspecialchars($nombre) . '</div>
        </div>';
}

function renderStatus($estatus) {
    switch ($estatus) {
        case 1:
            return '<span class="px-2 py-1 rounded-md text-sm font-semibold bg-[#014737] text-[#3FC189]">Activo</span>';
        case 0:
            return '<span class="px-2 py-1 rounded-md text-sm font-semibold bg-[#721c24] text-[#ba464d]">Inactivo</span>';
        default:
            return '<span class="px-2 py-1 rounded-md text-sm font-semibold bg-gray-500 text-white">Desconocido</span>';
    }
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
