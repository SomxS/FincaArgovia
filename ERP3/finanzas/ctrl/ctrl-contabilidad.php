<?php
if(empty($_POST['opc'])){
exit(0);
}

require_once('../mdl/mdl-contabilidad.php');
$obj = new Contabilidad;
$ingresos = new RptGral;

require_once('../../conf/_Utileria.php');
$util = new Utileria;

$encode = [];


switch ($_POST['opc']) {

    case 'initComponents':
        $banco   = $obj->lsBanco();
        $destino = $obj->lsDestinos();
        $cuenta  = $obj->lsCuenta();

        // Gastos
        $proveedor = $obj->lsProveedores();

        $turnos  = $obj->lsFoliosCerrados([$_POST['iptFecha']]);

        
        $encode = [
            'bancos'  => $banco,
            'destino' => $destino,
            'cuenta'  => $cuenta,
            'turnos'  => $turnos,
            'proveedor' => $proveedor
        ];
    
    break;
    
    case 'rpt':
      $encode = rptGeneral($ingresos);
    break;

    case 'lsCheque':
        $encode = lsCheque($obj);
    break;
    
    case 'frm-cheques':

        $file_C       = 'JUNIO_2024';
        $ruta_cheques = 'ERP/recursos/cheques/2024/'.$file_C.'/';

        $carpeta      = '../../../'.$ruta_cheques;

        $estatus = true;
        // //Busca si existe la carpeta si no la crea
        if (!file_exists($carpeta)) {


            $estatus  = false;
            mkdir($carpeta, 0777, true);
        }


        foreach ($_FILES as $cont => $key):
    
        
        // if($key['error'] == UPLOAD_ERR_OK ){

            $NombreOriginal = $key['name'];              //Obtenemos el nombre original del archivo
            $temporal       = $key['tmp_name'];          //Obtenemos la ruta Original del archivo
            $size           = ($key['size']/1024)/1024;
            
            $destino        = $carpeta.$NombreOriginal;

            $trozos    = explode(".", $NombreOriginal);
            $extension = end($trozos);


            move_uploaded_file($temporal, $destino); 


            $_POST['Archivo'] = $NombreOriginal;
            $_POST['id_UDN']  = 2;
            $_POST['Ruta']    = $ruta_cheques;
            
            $dtx = $util->sql($_POST);
            $ok  = $obj->add_archivo($dtx);

        // }

        endforeach;
 
        
        
        
        $encode = [
            'ok'   => $ok,
            'file' => $estatus,
            'dtx'  => $dtx
        ]; 


        
        
    break;
    
    case 'QuitarArchivo':
        $array  = $util->sql($_POST,1);
        $ok     = $obj->quitarArchivo($array);
        $encode = ['ok' => $ok];
    break;

    case 'removeCheque':
        
        $array  = $util->sql($_POST,1);
        $ok     = $obj->removeCheque($array);
        $encode = ['ok' => $ok];

    break;

    case 'historial':
      $encode = lsHistorialTicket($obj);
    break;
    
    case 'lsHistorialTC':
       $encode = lsHistorialTC($obj); 
    break;

    case 'lsArchivos':
       $encode = lsHistorialArchivos($obj); 
    break;


}

function lsHistorialArchivos($obj){
    # Variables
    $__row = [];

    # Lista de productos 
    $ls = $obj->lsArchivos([$_POST['fi'],$_POST['ff']]);


    foreach ($ls as $_key) {
        $btn = [];

        
        $btn[] = [
            'color' => 'info',
            'icon'  => 'icon-eye',
            'fn'    => 'fnChequePrint',           
        ];

      
        if($_key['Archivo'])
        $__row[] = array(
            'id'          => $_key['id'],
            'Fecha'       => formatSpanishDate($_key['fecha']),
            'Hora'        => $_key['Hora'],
            'Check'        => $_key['check_name'],
            'Categoria'        => $_key['Categoria'],
            'Descripcion' => mediaIco($_key['Type_File']).$_key['Descripcion'],
            // 'Archivo'     => $_key['Archivo'],
            
          
            'btn'      => $btn
        );

    }// end lista de folios




    // Encapsular arreglos
    return [
        "thead"    => '',
        "row"      => $__row,
        "frm_head" => ''
    ];

}

function mediaIco($TypeFile){

        return '<i  class="fa-2x icon-file-pdf"></i> ';
}

function lsHistorialTicket($obj){
    # Variables
    $__row = [];
    $fi = $_POST['fi'];
    $ff =$_POST['ff'];

    # Lista de productos 
    $ls = $obj->lsChequesHistorial([$fi,$ff]);


    foreach ($ls as $_key) {
        $btn = [];


        $btn[] = [
            'class'   => 'btn btn-outline-info btn-sm me-1',
            'html'    => '<i class=" icon-print"></i> ',
            'onclick' => 'tabCheque.printCheque('.$_key['id'].')',
        ];
        $btn[] = [
            'class'   => 'btn btn-outline-danger btn-sm',
            'html'    => '<i class=" icon-trash"></i> ',
            'onclick' => 'tabCheque.quitarCheque('.$_key['id'].')',
        ];



        $__row[] = array(
        'id'       => $_key['id'],
        'Fecha'    => formatSpanishDate($_key['Fecha']),
        'Destino'  => $_key['Name_IC'],
        'nombre'   => $_key['Nombre'],
        'banco'    => $_key['Banco'],
        'cuenta'   => $_key['NombreCuenta'],
        'importe'  => evaluar($_key['importe']),
        'concepto' => $_key['Concepto'],
        'a'      => $btn
        );

    }// end lista de folios




    // Encapsular arreglos
    return [
        "thead"    => '',
        "row"      => $__row,
        "frm_head" => ''
    ];

}

function lsHistorialTC($obj){

    # Declarar variables
    $__row   = [];
    
    #Consultar a la base de datos
    $ls    = $obj->lsHistorialTC([$_POST['fi'],$_POST['ff']]);

     foreach ($ls as $_key) {
    
         $__row[] = array(

            'id'           => $_key['id'],
            'Fecha'        => formatSpanishDate($_key['Fecha']),
            'Cliente'      => $_key['Cliente'],
            'Concepto'      => $_key['Concepto'],
            'TTCodigo'      => $_key['TTCodigo'],
            'Autorizacion'  => $_key['Autorizacion'],
            'Monto'         => evaluar($_key['Monto']),
            'TCodigo'       => $_key['TCodigo'],
            'Propina'       => $_key['propina'],
            'Observaciones' => $_key['Observaciones'],
            "opc"          => 0
        
        );

    } 
    
    #encapsular datos
        return [
            "thead" =>'',
            "row"   => $__row
        ];
}

function lsCheque($obj){
    # Variables
    $__row = [];

    # Lista de productos 
    $ls = $obj->lsCheques([$_POST['iptDate']]);


    foreach ($ls as $_key) {
        $btn = [];
        
        $btn[] = [
            'class'   => 'btn btn-sm btn-outline-info me-1',
            'html'    => '<i class="icon-file-pdf"> </i>',
            'onclick' => 'tabCheque.printCheque('.$_key['id'].')',
            'title'   => 'Imprimir cheque'
        ];

         $btn[] = [
            'class'   => 'btn btn-sm btn-outline-danger',
            'html'    => '<i class=" icon-trash-empty"> </i>',
            'onclick' => 'quitarArchivo('.$_key['id'].')',
            'title'   => 'Imprimir cheque'
        ];
        
     
        $__row[] = array(
            'id'       => $_key['id'],
            'Destino'  => $_key['Name_IC'],
            'nombre'   => $_key['Nombre'],
            'banco'    => $_key['Banco'],
            'cuenta'   => $_key['NombreCuenta'],
            'importe'  => evaluar($_key['importe']),
            'concepto' => $_key['Concepto'],
            'a'      => $btn
        );

    }// end lista de folios




    // Encapsular arreglos
    return [
        "thead"    => '',
        "row"      => $__row,
        "frm_head" => ''
    ];

}

function rptGeneral($obj){
    # Declarar variables
    $__row   = [];
    $date      = $_POST['iptDate'];

    #Consultar a la base de datos
    $ls             = $obj->VER_CATEGORIAS([1]);
    $total_ingresos = 0;


    foreach ($ls as $key) {
        $ingreso_categoria    = $obj -> ver_ingresos_turismo([$date,$key['id']]);
        
        $subT              = $ingreso_categoria;
        $IVA2              = 0;
        switch ($key['id']) {
            case 1:  // HOSPEDAJE cargo del 16 % & 2 %
            $subT  = $subT / 1.18;

            $IVA16  = $subT * .16;
            $IVA2   = $subT * .02;
            break;

            case 9:
            $IVA16 = 0;
            $bgIva = '';
            break;

            case 11:
            $subT   = 0;
            break;

            default:
            $IVA16  = ($ingreso_categoria/100)* 16;
            $subT  = $subT / 1.16;
            break;
        }

        $__row[] = array(
        'id'   => $key['id'],

        'Categoria'    => $key['Categoria'],
        'SubTotal'     => evaluar($subT),
        'IVA'          => evaluar($IVA16),
        '2% HOSPEDAJE' => evaluar($IVA2),
        'Total'        => evaluar($ingreso_categoria),
        
        "opc"  => 0
        );


        $total_ingresos += $ingreso_categoria;
    }


    /*--------- Cortesia y empleados -----------*/

   $cortesias = $obj ->Select_empleadosCortesia([$date]);
   $totalCortesias = 0;

   foreach ($cortesias as $cortesia ) {

        $__row[] = array(
            'id'           => $cortesia['id_Categoria'],
            'Categoria'    => '<b>TOTAL '.$cortesia['Subcategoria'].'</b>',
            'SubTotal'     => '',
            'IVA'          => '',
            '2% HOSPEDAJE' => '',
            'Total'        => evaluar($cortesia['total']),
            "opc"  => 1
        );

        $totalCortesias += $cortesia['total'];
    
   }




    
    $__row[] = array(
        'id'           => $key['id'],
        'Categoria'    => '<b>TOTAL INGRESOS</b>',
        'SubTotal'     => '',
        'IVA'          => '',
        '2% HOSPEDAJE' => '',
        'Total'        => evaluar($total_ingresos + $totalCortesias),
        "opc"  => 1
    );

    # -- Formas de pago --
    $total_formaspago = 0;
    $formas           = $obj -> VER_FORMAS_PAGO();

    $__row_fp = [];

    foreach ($formas as $key) {

        $monto_fp   = $obj -> VER_TIPOSPAGOS_FECHA([$key['id'],$date]);
        $total_formaspago += $monto_fp;
        $__row_fp[] = array(
            'id'            => $key['id'],
            'Categoria'     => $key['name'],
            'SubTotal'      => evaluar($monto_fp),
            // 'OBSERVACIONES' => '',            
            "opc"           => 0
        );
    }

        $__row_fp[] = array(
            'id'            => '',
            'Categoria'     => '<b>TOTAL</b>',
            'SubTotal'      => evaluar($total_formaspago),
            // 'OBSERVACIONES' => '',            
            "opc"           => 1
        );

     
    # -- Formas de pago propina --

   $__row_propina = [];

    $formas  = $obj -> lsFormasPago();

    $totalPropinas = 0;
    
    foreach ($formas as $key) {

        $propina = $obj-> lsPropina([$key['id'],9,$date]);

        $totalPropinas += $propina;
        $__row_propina[] = array(
            'id'            => $key['id'],
            'Categoria'     => $key['name'],
            'SubTotal'      => evaluar($propina),
            "opc"           => 0
        );
    }

    $__row_propina[] = array(
        'id'            => '',
        'Categoria'     => '<b>TOTAL</b>',
        'SubTotal'      => evaluar($totalPropinas),
        "opc"           => 1
    );




    /*-- Titulo del reporte --*/ 
    $head = ticket_head( [
      "fecha"  => formatSpanishDate($date),
      "titulo" => 'REPORTE GENERAL',
    ]);
    
    $footer = ticket_footer([
        "total" => ($total_formaspago+$totalPropinas+$totalCortesias)
    ]);

    // # -- Tablas de datos --

    $table_fp = [

      'thead' => ['Forma de pago','Total'],
      'row'   =>$__row_fp
    ];

    $table_propina = [

      'thead'    => ['FORMAS','Total','Observaciones'],
      'row'      => $__row_propina,
      'frm_foot' => $footer
    ];

    $table_rpt = [
        'frm_head' => $head,
        'thead'    => '',
        'row'      => $__row
    ];

    $lsfolio = $obj  -> lsFolio([ $_POST['iptDate'] ]);
   
    return [

        'btn'           => $btn,
        'table_rpt'     => $table_rpt,
        'table_fp'      => $table_fp,
        'table_propina' => $table_propina,
        'Observaciones' => $lsfolio['Observacion']
    
    ];


}


/* ----- Complementos ----- */ 
function ticket_head($data){

    

    return  '

    <div  class="col-sm-12  mt-3 ">

    <table style="font-size:1.2rem;" class="table table-bordered  table-sm ">


    <tr>
    
    <td class="col-sm-1 bg-default"> <strong> FECHA :  </strong></td>
    <td class="col-sm-3 text-right"> '.$data['fecha'].'</td>

    <td class="col-sm-4 text-center" > <strong>'.$data['titulo'].'</strong>  </td>
    <td class="col-sm-4 "> </td>

    </tr>



    </table>

    </div>

    ';

}

function ticket_footer($data){
  


    return '
    <table  class="table table-bordered mb-3">
          <tr>
            <td style="font-size:1rem; font-weight:bold;" class="col-8">
                <b>TOTAL GENERAL</b>
            </td>
            <td style="font-size:1rem; font-weight:bold;" class="text-end"> 
            '.evaluar($data['total']).'
            </td>
          </tr>
    </table>
    
    
    ';



}

function formatSpanishDate($fecha = null) {
    setlocale(LC_TIME, 'es_ES.UTF-8'); // Establecer la localización a español

    if ($fecha === null) {
        $fecha = date('Y-m-d'); // Utilizar la fecha actual si no se proporciona una fecha específica
    }

    // Convertir la cadena de fecha a una marca de tiempo
    $marcaTiempo = strtotime($fecha);

    $formatoFecha = "%A, %d de %B del %Y"; // Formato de fecha en español
    $fechaFormateada = strftime($formatoFecha, $marcaTiempo);

    return $fechaFormateada;
}

function evaluar($val){
    return $val ? '$ ' . number_format($val, 2, '.', ',') : '-';
}


echo json_encode($encode);
?>