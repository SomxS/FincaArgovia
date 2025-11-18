<?php

if (empty($_POST['opc'])) exit(0);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

require_once '../mdl/mdl-costos.php';

class ctrl extends mdl {

    function init() {
        return [
            'udn' => $this->lsUDN(),
            'userLevel' => $_POST['userLevel'] ?? 2
        ];
    }

    function ls() {
        $fi = $_POST['fi'];
        $ff = $_POST['ff'];
        $udn = $_POST['udn'] ?? null;

        if (!$this->validateDateFormat($fi) || !$this->validateDateFormat($ff)) {
            return [
                'status' => 400,
                'message' => 'Formato de fecha inválido'
            ];
        }

        if ($udn && !$this->udnExists($udn)) {
            return [
                'status' => 404,
                'message' => 'Unidad de negocio no encontrada'
            ];
        }

        $data = $this->listCostos([$fi, $ff, $udn]);
        $rows = [];
        $currentClass = null;
        $classTotal = ['costo_directo' => 0, 'salida_almacen' => 0, 'total' => 0];
        $grandTotal = ['costo_directo' => 0, 'salida_almacen' => 0, 'total' => 0];

        foreach ($data as $item) {
            if ($currentClass !== $item['product_class']) {
                if ($currentClass !== null) {
                    $rows[] = [
                        'Categoría' => "<strong>Total {$currentClass}</strong>",
                        'Fecha' => '',
                        'Costo Directo' => [
                            'html' => '<strong>' . formatPrice($classTotal['costo_directo']) . '</strong>',
                            'class' => 'text-end bg-gray-200'
                        ],
                        'Salida Almacén' => [
                            'html' => '<strong>' . formatPrice($classTotal['salida_almacen']) . '</strong>',
                            'class' => 'text-end bg-gray-200'
                        ],
                        'Total' => [
                            'html' => '<strong>' . formatPrice($classTotal['total']) . '</strong>',
                            'class' => 'text-end bg-gray-200'
                        ],
                        'opc' => 0
                    ];
                    $classTotal = ['costo_directo' => 0, 'salida_almacen' => 0, 'total' => 0];
                }
                $currentClass = $item['product_class'];
            }

            $costoDirecto = floatval($item['costo_directo']);
            $salidaAlmacen = floatval($item['salida_almacen']);
            $total = $costoDirecto + $salidaAlmacen;

            $rows[] = [
                'Categoría' => $item['product_class'],
                'Fecha' => formatSpanishDate($item['fecha'], 'normal'),
                'Costo Directo' => [
                    'html' => formatPrice($costoDirecto),
                    'class' => 'text-end'
                ],
                'Salida Almacén' => [
                    'html' => formatPrice($salidaAlmacen),
                    'class' => 'text-end'
                ],
                'Total' => [
                    'html' => formatPrice($total),
                    'class' => 'text-end'
                ],
                'opc' => 0
            ];

            $classTotal['costo_directo'] += $costoDirecto;
            $classTotal['salida_almacen'] += $salidaAlmacen;
            $classTotal['total'] += $total;

            $grandTotal['costo_directo'] += $costoDirecto;
            $grandTotal['salida_almacen'] += $salidaAlmacen;
            $grandTotal['total'] += $total;
        }

        if ($currentClass !== null) {
            $rows[] = [
                'Categoría' => "<strong>Total {$currentClass}</strong>",
                'Fecha' => '',
                'Costo Directo' => [
                    'html' => '<strong>' . formatPrice($classTotal['costo_directo']) . '</strong>',
                    'class' => 'text-end bg-gray-200'
                ],
                'Salida Almacén' => [
                    'html' => '<strong>' . formatPrice($classTotal['salida_almacen']) . '</strong>',
                    'class' => 'text-end bg-gray-200'
                ],
                'Total' => [
                    'html' => '<strong>' . formatPrice($classTotal['total']) . '</strong>',
                    'class' => 'text-end bg-gray-200'
                ],
                'opc' => 0
            ];
        }

        $rows[] = [
            'Categoría' => '<strong>TOTAL GENERAL</strong>',
            'Fecha' => '',
            'Costo Directo' => [
                'html' => '<strong>' . formatPrice($grandTotal['costo_directo']) . '</strong>',
                'class' => 'text-end bg-blue-900 text-white'
            ],
            'Salida Almacén' => [
                'html' => '<strong>' . formatPrice($grandTotal['salida_almacen']) . '</strong>',
                'class' => 'text-end bg-blue-900 text-white'
            ],
            'Total' => [
                'html' => '<strong>' . formatPrice($grandTotal['total']) . '</strong>',
                'class' => 'text-end bg-blue-900 text-white'
            ],
            'opc' => 0
        ];

        return [
            'row' => $rows,
            'thead' => ''
        ];
    }

    function exportExcel() {
        $fi = $_POST['fi'];
        $ff = $_POST['ff'];
        $udn = $_POST['udn'] ?? null;

        if (!$this->validateDateFormat($fi) || !$this->validateDateFormat($ff)) {
            return [
                'status' => 400,
                'message' => 'Formato de fecha inválido'
            ];
        }

        // $data = $this->listCostos([$fi, $ff, $udn]);

        // require_once '../../vendor/autoload.php';

        // use PhpOffice\PhpSpreadsheet\Spreadsheet;
        // use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
        // use PhpOffice\PhpSpreadsheet\Style\Fill;
        // use PhpOffice\PhpSpreadsheet\Style\Alignment;

        // $spreadsheet = new Spreadsheet();
        // $sheet = $spreadsheet->getActiveSheet();

        // $sheet->setCellValue('A1', 'Concentrado Diario de Costos');
        // $sheet->mergeCells('A1:E1');
        // $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        // $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // $sheet->setCellValue('A2', "Período: {$fi} - {$ff}");
        // $sheet->mergeCells('A2:E2');
        // $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // $sheet->setCellValue('A4', 'Categoría');
        // $sheet->setCellValue('B4', 'Fecha');
        // $sheet->setCellValue('C4', 'Costo Directo');
        // $sheet->setCellValue('D4', 'Salida Almacén');
        // $sheet->setCellValue('E4', 'Total');

        // $sheet->getStyle('A4:E4')->getFont()->setBold(true);
        // $sheet->getStyle('A4:E4')->getFill()
        //     ->setFillType(Fill::FILL_SOLID)
        //     ->getStartColor()->setARGB('FF4472C4');
        // $sheet->getStyle('A4:E4')->getFont()->getColor()->setARGB('FFFFFFFF');

        // $row = 5;
        // foreach ($data as $item) {
        //     $sheet->setCellValue("A{$row}", $item['product_class']);
        //     $sheet->setCellValue("B{$row}", $item['fecha']);
        //     $sheet->setCellValue("C{$row}", floatval($item['costo_directo']));
        //     $sheet->setCellValue("D{$row}", floatval($item['salida_almacen']));
        //     $sheet->setCellValue("E{$row}", floatval($item['costo_directo']) + floatval($item['salida_almacen']));

        //     $sheet->getStyle("C{$row}:E{$row}")->getNumberFormat()
        //         ->setFormatCode('$#,##0.00');

        //     $row++;
        // }

        // $sheet->getColumnDimension('A')->setWidth(25);
        // $sheet->getColumnDimension('B')->setWidth(15);
        // $sheet->getColumnDimension('C')->setWidth(18);
        // $sheet->getColumnDimension('D')->setWidth(18);
        // $sheet->getColumnDimension('E')->setWidth(18);

        // $filename = "concentrado_costos_" . date('YmdHis') . ".xlsx";
        // $filepath = "../../temp/{$filename}";

        // $writer = new Xlsx($spreadsheet);
        // $writer->save($filepath);

        // return [
        //     'status' => 200,
        //     'file' => "temp/{$filename}",
        //     'message' => 'Archivo Excel generado correctamente'
        // ];
    }

    private function validateDateFormat($date) {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    private function udnExists($udn) {
        $result = $this->lsUDN();
        foreach ($result as $item) {
            if ($item['id'] == $udn) {
                return true;
            }
        }
        return false;
    }
}

$allowedOperations = ['init', 'ls', 'exportExcel'];
if (!in_array($_POST['opc'], $allowedOperations)) {
    echo json_encode([
        'status' => 403,
        'message' => 'Operación no permitida en módulo de solo lectura'
    ]);
    exit;
}

$obj = new ctrl();
echo json_encode($obj->{$_POST['opc']}());
