<?php

session_start();
if (empty($_POST['opc'])) exit(0);
require_once '../mdl/mdl-archivos.php';

class ctrl extends mdl {

    public function list() {
        $fi = $_POST['fi'];
        $ff = $_POST['ff'];

        $files = $this->getFilesByDateRange(['fi' => $fi, 'ff' => $ff]);

        $rows = [];
        foreach ($files as $file) {
            $rows[] = [
                'Fecha subida' => $file['upload_date'],
                'Módulo' => $file['module'],
                'Subido por' => $file['user_name'],
                'Nombre del archivo' => $file['file_name'],
                'Tipo/Tamaño' => $file['extension'] . ' / ' . $file['size_bytes'] . ' KB',
                'a' => [
                    [
                        'icon' => 'icon-download',
                        'fn' => "archivos.download({$file['id']})",
                    ],
                    [
                        'icon' => 'icon-trash',
                        'fn' => "archivos.cancel({$file['id']})",
                    ],
                ],
            ];
        }

        return [
            'status' => 200,
            'data' => $rows,
        ];
    }

    public function get() {
        $id = $_POST['id'];
        $file = $this->getFileById(['id' => $id]);

        return [
            'status' => 200,
            'data' => $file,
        ];
    }

    public function add() {
        $data = $this->util->sql($_POST);

        $exists = $this->fileExists(['file_name' => $data['file_name']]);
        if ($exists) {
            return [
                'status' => 400,
                'message' => 'El archivo ya existe.',
            ];
        }

        $result = $this->createFile($data);

        return $result ? [
            'status' => 200,
            'message' => 'Archivo agregado exitosamente.',
        ] : [
            'status' => 500,
            'message' => 'Error al agregar el archivo.',
        ];
    }

    public function edit() {
        $data = $this->util->sql($_POST);
        $result = $this->updateFile($data);

        return $result ? [
            'status' => 200,
            'message' => 'Archivo actualizado exitosamente.',
        ] : [
            'status' => 500,
            'message' => 'Error al actualizar el archivo.',
        ];
    }

    public function cancel() {
        $id = $_POST['id'];
        $result = $this->deleteFile(['id' => $id]);

        return $result ? [
            'status' => 200,
            'message' => 'Archivo eliminado exitosamente.',
        ] : [
            'status' => 500,
            'message' => 'Error al eliminar el archivo.',
        ];
    }
}

$obj = new ctrl();
$fn = $_POST['opc'];
$encode = $obj->$fn();
echo json_encode($encode);