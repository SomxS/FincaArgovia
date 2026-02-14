<?php


# Libreria.
require '../src/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

require_once '../mdl/mdl-administracion.php';
$obj = new Gastos();

require_once('../../conf/_Utileria.php');
$util = new Utileria;

require_once('../../conf/coffeSoft.php');

$newProveedor = [];
$_notNumeric  = [];

foreach ($_FILES as $cont => $key) {
  if($key['error'] == UPLOAD_ERR_OK ){

    // Obteniendo data de xls 

    $fichero     = $key['name'];
    $rutaArchivo = $key['tmp_name'];
    $documento   = IOFactory::load($rutaArchivo);
    $hojaActual  = $documento->getSheet(0);

    $numeroMayorDeFila    = $hojaActual->getHighestRow(); // Numérico
    $letraMayorDeColumna  = $hojaActual->getHighestColumn(); // Letra
    $numeroMayorDeColumna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($letraMayorDeColumna);


    $inicio_fila = 1;
    $Noproductos  =  $numeroMayorDeFila-6;

    $listGastos = [];

    # Recorrido de archivo Excel
    for ($filas=15 ; $filas <= $numeroMayorDeFila  ; $filas++) {

        $fecha   = $hojaActual->getCell('A'.$filas)->getValue();
        $nombre  = $hojaActual->getCell('B'.$filas)->getValue();
        $Importe = $hojaActual->getCell('C'.$filas)->getValue();
        $desc    = $hojaActual->getCell('D'.$filas)->getValue();

        $date    = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha);
        $f       = $date->format('Y-m-d');
        
        if($nombre ):

        $idProveedor = $obj-> getProveedor([$nombre]);

        if(!$idProveedor['idProveedor']){

            $newProveedor[] = [ 'nombre' => $nombre, 'descripcion' => $desc];
            
            // Crear nuevo proveedor:

            $values      = [ 'Name_Proveedor' => $nombre, 'id_categoria'   => 1, 'EstadoProveedor'  => 2];
            $data        = $util -> sql($values);

            $ok          = $obj  -> addProveedor($data);
            $idProveedor = $obj  -> getProveedor([$nombre]);

                    
        }    
          
        $isValidImport = is_numeric($Importe); // validar si es numero y no una formula de excel.
        
            if($isValidImport){ // si los registros son validos, proceder a insertar:


                $isCreatedGasto = $obj -> lsCreatedGasto([$idProveedor['idProveedor'],$desc]);

                $status         = $isCreatedGasto['idgastos'] ? iconState('warn','Ya existe') : iconState('ok','Agregado');

          
                if(!$isCreatedGasto['idgastos']):
          
                        $values = [
                            'id_proveedor' => $idProveedor['idProveedor'],
                            'fechaGasto'   => $f,
                            'Monto'        => $Importe,
                            'descripcion'  => $desc
                        ];

                  

                        $ok = $obj -> insertGastos($util -> sql($values));
                endif;    

                        $__row[] = array(

                            'id'        => '',
                            'fecha'    => $f,
                            'proveedor' => $nombre,
                            'Importe'   => evaluar($Importe),
                            // 'is'        => $isValidImport,
                            'desc'      => $desc,
                            'Estado'    => $status,
                            "opc"       => 0
                        );

            } else{

                $_notNumeric[] = array(

                            'id'        => '',
                            'fecha'    => $f,
                            'proveedor' => $nombre,
                            'Importe'   => evaluar($Importe),
                            // 'is'        => $isValidImport,
                            'desc'      => $desc,
                            'Estado'    => $status,
                            "opc"       => 0
                        );

            }
        
         endif;
    
    
}

   


  }
  
}

 $agregados = [
    "thead"             => '',
    "row"               => $__row,
 ];

 $noAgregados = [
    'thead' => '',
    'row'   => $_notNumeric
 ];


 # Encapsular arreglos
    $encode = [
        'agregados' => $agregados,
        'noAgregados' =>$noAgregados,
        'nuevosProveedores' => $newProveedor
    ];

function iconState($icon,$text = 'OK'){
 
    return $icon == 'ok' 
    ? "<span class='text-success icon-ok-circled'></span> {$text}"
    : "<span class='text-warning icon-warning-1'></span> {$text}";

}
# ----------------------- #
#  JSON DECODE            #
# ----------------------- #
echo json_encode($encode);