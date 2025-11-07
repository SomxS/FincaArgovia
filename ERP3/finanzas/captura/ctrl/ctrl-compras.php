<?php
session_start();

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-compras.php';

class ctrl extends mdl {

    function init() {
        return [
            'productClass' => $this->lsProductClass([1]),
            'purchaseType' => $this->lsPurchaseType([1]),
            'methodPay' => $this->lsMethodPay([1]),
            'supplier' => $this->lsSupplier([1]),
            'udn' => $this->lsUDN([1])
        ];
    }

    function ls() {
        $__row = [];

        $fi = $_POST['fi'];
        $ff = $_POST['ff'];
        $tipo_compra = $_POST['tipo_compra'] ?? '';
        $metodo_pago = $_POST['metodo_pago'] ?? '';

        $ls = $this->listCompras([
            $fi,
            $ff,
            $tipo_compra,
            $metodo_pago,
            1
        ]);

        foreach ($ls as $key) {
            $__row[] = [
                'id' => $key['id'],
                'Folio' => '#' . str_pad($key['id'], 6, '0', STR_PAD_LEFT),
                'Fecha' => formatSpanishDate($key['operation_date'], 'normal'),
                'Clase producto' => $key['product_class_name'],
                'Producto' => $key['product_name'],
                'Tipo de compra' => $key['purchase_type_name'],
                'Total' => [
                    "html" => evaluar($key['total']),
                    "class" => "text-end"
                ],
                'dropdown' => dropdown($key['id'], $key['purchase_type_id'])
            ];
        }

        return [
            "row" => $__row,
            'ls' => $ls
        ];
    }
}

function dropdown($id, $tipo_compra) {
    $options = [
        ['icon' => 'icon-eye', 'text' => 'Ver detalles', 'onclick' => "app.viewDetails($id)"],
        ['icon' => 'icon-pencil', 'text' => 'Editar', 'onclick' => "app.editCompra($id)"],
        ['icon' => 'icon-trash', 'text' => 'Eliminar', 'onclick' => "app.deleteCompra($id)"]
    ];

    return $options;
}

$obj = new ctrl();
$fn = $_POST['opc'];
$encode = $obj->$fn();
echo json_encode($encode);
