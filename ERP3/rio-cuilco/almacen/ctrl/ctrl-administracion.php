<?php

if (empty($_POST['opc']))

    exit(0);
require_once('../mdl/mdl-administracion.php');
require_once('../../../conf/_Utileria.php');
$encode = [];

class AdministracionFlowersCtrl extends Administracion
{

    public $util;

    public function __construct()
    {
        // parent::__construct(); // Llama al constructor de la clase padre
        $this->util = new Utileria();
    }



    public function initComponents()
    {

        # Declarar variables
        $lsCategory = $this->lsUDN();
        $lsSubCategory = $this->lsUDN();
        // $data = $this -> util :: sql(data,1);


        return [
            'category'     => $lsCategory,
            'subcategory' => $lsSubCategory
        ];
    }

    function lsFlowers()
    {
        # Declarar variables
        $__row = [];

        # Consultar a la base de datos
        // $ls = $this->obj->lsFlower([$_POST['id_category']]);
        $ls = [
            [
                'id' => 1,
                'code' => '001',
                'valor' => 'Flor 1',
                'extension' => 'jpg',
                'udn' => 'UDN 1',
                'areas' => 'Area 1',

            ],
            [
                'id' => 2,
                'code' => '002',
                'valor' => 'Flor 2',
                'extension' => 'jpg',
                'udn' => 'UDN 2',
                'areas' => 'Area 2',

            ],
            [
                'id' => 3,
                'code' => '003',
                'valor' => 'Flor 3',
                'extension' => 'jpg',
                'udn' => 'UDN 3',
                'areas' => 'Area 3',

            ]
        ];


        foreach ($ls as $key) {
            $btn = [];

            $btn[] = [
                'class' => 'btn me-1 btn-outline-info',
                'html' => '<i class="icon-pencil"></i>',
                "onclick" => "modalEditFile(" . $key['id'] . ")",
                "color" => 'info',
                "icon" => 'icon-pencil',
            ];

            $btn[] = [
                'class' => 'btn btn-outline-danger',
                'html' => '<i class="icon-trash"></i>',
                "onclick" => "deleteFile(" . $key['id'] . ")",
                "color" => 'danger',
                "icon" => 'icon-trash',
            ];

            $__row[] = [
                'id' => $key['id'],
                'codigo' => $key['code'],
                'nombre' => '<strong class="text-primary">' . $key['valor'] . '</strong>' . '.<label  >' . $key['extension'] . '</label>',
                'udn' => $key['udn'],
                'area' => $key['areas'],
                'area6' => $key['areas'],
                'area1' => $key['areas'],
                'area2' => $key['areas'],
                'area3' => $key['areas'],
                'area4' => $key['areas'],
                "a" => $btn,
            ];
        }

        # Encapsular datos
        return [
            // "thead" => ['FLOR', 'COSTO', 'VENTA', 'MENUDEO', 'MAYOREO', 'CANTIDAD MAYOREO', 'STOCK MÍNIMO', 'CATEGORIA', 'CLASE', '<i class="icon-cog"></i>'],
            'thead' => $this->createThead(),
            "row" => $__row,
        ];
    }

    function createThead()
    {
        return array(
            [
                '1' => ['text' => 'PRODUCTO', 'rowspan' => 2, 'class' => 'align-middle'],
                '2' => ['text' => 'PRECIOS', 'colspan' => 4, 'class' => 'align-middle'],
                '3' => ['text' => 'CANTIDAD MAYOREO', 'rowspan' => 2, 'class' => 'align-middle'],
                '4' => ['text' => 'STOCK MÍNIMO', 'rowspan' => 2, 'class' => 'align-middle'],
                '5' => ['text' => 'CATEGORÍA', 'rowspan' => 2, 'class' => 'align-middle'],
                '6' => ['text' => 'CLASE', 'rowspan' => 2, 'class' => 'align-middle'],
                '7' => ['html' => '<i class="icon-cog"></i>', 'rowspan' => 2, 'class' => 'align-middle'],
            ],
            [
                '1' => ['text' => 'Costo'],
                '2' => ['text' => 'Venta'],
                '3' => ['text' => 'Menudeo'],
                '4' => ['text' => 'Mayoreo'],
            ]
        );
    }
}


// Instancia del objeto
$flower = new AdministracionFlowersCtrl();
$fn = $_POST['opc'];
$encode = $flower->$fn();

echo json_encode($encode);
