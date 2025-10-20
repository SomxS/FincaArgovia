<?php
date_default_timezone_set('America/Mexico_City');
setlocale(LC_TIME, 'es_MX.UTF-8');

if(empty($_POST['opc'])){
exit(0);
}

require_once('../mdl/mdl-ingresos-diarios.php');
$Activos = new Activos;

require_once('../mdl/mdl-analisis-de-ingresos.php');



require_once('../mdl/mdl-movimientos-diarios.php');
$obj  = new Movimientosdiarios;
$rpt  = new rpt_gral;
$tc   = new TC;
$gral = new RptGral;
// $ing  = new Ingresos;

$sub  = new Cortesias;

require_once('../../conf/_Utileria.php');
$util = new Utileria;

# -- variables --
$encode = [];

switch ($_POST['opc']) {
    case 'initComponents':
        $cat          = $obj->lsCategorias([1]);
        // $now          = date("Y-m-d");
        $folio        = $obj->__getFolio([$_POST['date']]);
        $turnos       = $obj->Select_Turnos([$_POST['date']]);
        $tipoTerminal = $obj->lsTipoTerminal();
        $Terminal     = $obj->lsTerminal();
        $checklist    = $obj->lsCheckList();

        $cortesias    = $sub->listCortesias();
        
        $encode = [
            "categoria"    => $cat,
            "folio"        => $folio[0],
            "turnos"       => $turnos,
            "tipoTerminal" => $tipoTerminal,
            "terminal"     => $Terminal,
            "checklist"    => $checklist,
            "cortesias"    => $cortesias
        
        ];
    break;

    case 'frm-ingresos':
        $cat = $rpt->VER_SUBCATEGORIAS([$_POST['idCategoria']]);
        
        $encode = [
        "categoria" => $cat,
        ];
    break;

    case 'agregar-movimiento':
        
        /*--      Subtotal        --*/
        $subTotal        = 0;
        switch ($_POST['idCategoria']) {
            case 1:
            case 11:    
               $subTotal        = $_POST['Monto'] / 1.18;
            break;
            case 9:
               $subTotal        = $_POST['Monto'];
            break;
            default:
            $subTotal        = $_POST['Monto'] / 1.16;
            break;
        }

        /*  --  Agregar ingresos   --*/ 
        $data_ingresos = [
            "id_Subcategoria" => $_POST['id_grupo'],
            "pax"             => $_POST['pax'],
            "Noche"           => $_POST['Noche'],
            "Tarifa"          => $_POST['Monto'],
            "Subtotal"        => $subTotal,
            "id_Folio"        => $_POST['idFolio']
        ];

        $modelo  = $util->sql($data_ingresos);
        $ok      = $rpt->agregar_ingresos($modelo);
        
        $id_Bitacora = $rpt->obtener_id_ingreso([$_POST['idFolio'],$_POST['id_grupo']]); 

        /* ----------------------------- */
        
        /* Insertar las formas de pago */ 
        $ls = $rpt-> lsformas_pago([1]);
        foreach ($ls as $key) {

            $monto = $_POST[$key['name']];

            if($monto != ''){
                $data_formas_pago = [
                    "id_Bitacora"   => $id_Bitacora,
                    "id_FormasPago" => $key['id'],
                    "Monto"         => $monto
                ];
                $array  = $util->sql($data_formas_pago);
                $okfp[]           = $rpt->formas_pago($array);
            }

        }

        // * -- agregar impuestos --*

        $imp = $rpt->Select_Impuestos( $_POST['idCategoria']);


        foreach ($imp as $val) {
            $impuesto =  $val['Valor'] / 100;

            $Valor    = $subTotal * $impuesto;

            $data_impuesto = [
            'id_Bitacora' => $id_Bitacora,
            'id_Impuesto' => $val['idImpuesto'],
            'Monto'       => $Valor,
            ];

            $arreglo_impuesto  = $util->sql($data_impuesto);
            $ok_impuesto[] = $rpt->agregar_impuesto($arreglo_impuesto);
        }
     
        
        $encode = [

            "data_ingresos" => $data_ingresos,
            "formas_pago"   => $okfp,
            "impuestos"     => $ok_impuesto,
            "id_Bitacora"   => $id_Bitacora,
            'impuestos'     => $imp
        ];

    break;
    
    case 'lsMovCapturados':
        $ls = $rpt -> mov_capturados([$_POST['idFolio'],$_POST['idMovimiento']]);

        $encode = [
            'mov'=> $ls
        ];
    break;
    
    case 'QuitarMovimiento':
        $id = $_POST['idMovimiento'];
        $ok = $rpt->quitarMovimiento([$id]);
        $encode = [
            'ok' => $ok
        ];
    break;    
    
    case 'EditarMovimientos':

        $mov = $rpt -> ver_mov([$_POST['idMovimiento']]);
        $fp  = $rpt -> ver_pago([$_POST['idMovimiento']]);
        
        foreach ($fp as $key => $value) {
        $encode[$value['FormasPago']] = $value['Monto'];
        }

        foreach ($mov as $_key) {
            $encode['Tarifa'] = $_key['Tarifa'];
            $encode['pax']    = $_key['pax'];
            $encode['Noche']  = $_key['Noche'];
        }
        
    
   
    break;

    case 'guardarEditMovimientos':
        $id_bitacora = $_POST['mov'];
        /*-- Editar movimientos de bitacora -- */ 
        
        $upd = [
            "pax"    => $_POST['pax'],
            "Noche"  => $_POST['Noche'],
            "Tarifa" => $_POST['Tarifa'],
            'idVentasBit' => $id_bitacora
        ];
        
        $bitacoraIngresos = $util->sql($upd,1);

        $ok  = $rpt->editar_movimiento($bitacoraIngresos);
        


        /*¨-- Editar movimientos de formas de pago --*/ 
        $lsFormasPago = $rpt-> lsformas_pago([$_POST['idgrupo']]);

        $dataFP = [];
        $newFP  = [];

        foreach ($lsFormasPago as $key => $value) {

            $idFormaPago = $value['id'];
            
            // Buscar si hay un registro en la tabla de formas de pago
            $idFP_bitacora = $rpt-> __getBitacoraFormasPago([
                $id_bitacora,
                $idFormaPago
            ]);


            
            if($idFP_bitacora){ // Si hay un registro , edita
                
                $upd_formas_de_pago = [
                    "Monto"         => $_POST[$value['name']],
                    "idFP_Bitacora" => $idFP_bitacora   
                ];

                $data_fp = $util->sql($upd_formas_de_pago,1);

                $dataFP[] = $data_fp;

                $ok_fp[] = $rpt->editar_formas_pago($data_fp);
            
            }else{ // No hay un registro Inserta


                
                if($_POST[$value['name']] != ''){ // se agrego cifra en el formulario ?
                
                
                
                
                    
                    $data_formas_pago = [
                        "id_Bitacora"   => $id_bitacora,
                        "id_FormasPago" => $idFormaPago,
                        "Monto"         => $_POST[$value['name']]
                    ];

                    $newFP[] = $data_formas_pago;

                   

                    $array              = $util->sql($data_formas_pago);
                    $insert_fp[]        = $rpt->formas_pago($array);
                }
            }
        }

        /*-- Editar Impuestos --*/
        
        

        

        $encode = [
            
            'bitacora_ingresos'  => $bitacoraIngresos,
            'formas_pago'        => $dataFP,
            'nuevas_formas_pago' => $newFP,

            'ok_insert_formas_pago' => $insert_fp,


            'ok_edit_mov'         => $ok,
            'ok_edit_formas_pago' => $ok_fp,
            // 'new_formas_pago'   => $insert_fp
        ];
    break;

    case 'crear-folio':
      
        $NoFol          = $obj->obtenerFolio();
        $_POST['Folio'] = 'P-'.$NoFol;
        $array          = $util  -> sql($_POST);
        $ok             = $obj   -> crearFolio($array);

        $now = date("Y-m-d");
       
        // -- Init Component --

         $cat          = $obj->lsCategorias([1]);
        $now          = date("Y-m-d");
        $folio        = $obj->__getFolio([$now]);
        $turnos       = $obj->Select_Turnos([$now]);
        $tipoTerminal = $obj->lsTipoTerminal();
        $Terminal     = $obj->lsTerminal();
        $checklist    = $obj->lsCheckList();

        $cortesias    = $sub->listCortesias();
        
        $encode = [
            "categoria"    => $cat,
            "folio"        => $folio[0],
            "turnos"       => $turnos,
            "tipoTerminal" => $tipoTerminal,
            "terminal"     => $Terminal,
            "checklist"    => $checklist,
            "cortesias"    => $cortesias
        
        ];

        // $encode = [

        //     "folio"     => $folio[0],
        //     "turnos"    => $turnos,
        //     "ok"       => $ok
        // ];
            
    break;


    case 'cerrar-folio':
        // Obtener data de cerrar turno y actualizar
        $fecha  = $_POST['FechaModificacion'];
        
        unset($_POST['FechaModificacion']) ;

        $array  = $util  -> sql($_POST,1);


        $obj->cerrar_folio($array);
        // // Enviar init component para resetear tabla
        $now   = date("Y-m-d");

         $turnos       = $obj->Select_Turnos([$fecha]);
        
        $folio = $obj->__getFolio([$now]);
        
        $encode = [
         'getData'   => $array,   
         "folio"     => $folio[0],
         "turnos"    => $turnos
        ];
    break;


    case 'consultarRegistros':
        
       $folio     = $obj->__getFolio([$_POST['date']]);
       $encode = [
        "folio"     => $folio[0]
        ];
    
    break;

    case 'lsIngresos':
     $encode = lsIngresos($rpt);
    // $encode = listIngresos($Activos);
    break;

    /* Modulo de TC */ 


    case 'lsTC':
     $encode = lsTC($tc);
    break;

    case 'add-frm-data':

     $array  = $util->sql($_POST);
     $ok     = $tc->add_tc($array);
     $encode = $array;
    break;

    

    /* Modulo de archivos */ 

    case 'lsArchivos':
    $encode = lsArchivos($tc);
    break;

    case 'files':

        $file_C = 'Mayo_2024';

        $ruta = 'ERP/recursos/sobres_file/'.$file_C.'/'; 
        $carpeta = '../../../'.$ruta;

        $estatus = true;
        // //Busca si existe la carpeta si no la crea
        if (!file_exists($carpeta)) {
        $estatus = false;

            mkdir($carpeta, 0777, true);
        }

        foreach ($_FILES as $cont => $key):
    
        if($key['error'] == UPLOAD_ERR_OK ){


            $NombreOriginal = $key['name'];              //Obtenemos el nombre original del archivo
            $temporal       = $key['tmp_name'];          //Obtenemos la ruta Original del archivo
            $size           = ($key['size']/1024)/1024;  
            
            $destino        = $carpeta.$NombreOriginal;

            $trozos = explode(".", $NombreOriginal);
            $extension = end($trozos);
            
            
            move_uploaded_file($temporal, $destino); 
          
          
            $data = [

                'UDN_Sobre'    => 1,
                'Ruta'         => $ruta,
                'Fecha'        => $_POST['date'],
                'Hora'         => date("H:i:s"),
                'Archivo'      => $NombreOriginal,
                'Peso'         => $key['size'],
                'Descripcion'  => $_POST['Detalles'],
                'id_checklist' => $_POST['modulo'],
                'Type_File'    => $extension,
                'status'       => 1,
                
            ];

            $dtx = $util->sql($data);

            $ok  = $tc->add_archivo($dtx);
        }
        
    
        endforeach;
   
        $encode = ['ok' => $ok,
                   'estatus' => $estatus]; 
    break;

    case 'QuitarArchivo':
        $array  = $util->sql($_POST,1);
        $ok     = $tc->quitarArchivo($array);
        $encode = ['ok' => $ok];
    break;

    /* Modulo Rpt General */ 
    case 'rptGeneral':
        $encode = rptGeneral($gral);
    break; 

    case 'addObservaciones':
        $array  = $util->sql($_POST,1);
        $ok     = $gral->addObservaciones($array);
        $encode = ['ok' => $ok];
    break;



    // -- Desgloze de turnos --
    case 'lsTurnos':
     
       $encode = [
         'lsTurnos'   => rpt_turno($Activos),
         'lsTarjetas' => mdlTC($tc)
       ];
    break;

  
}


function mdlTC($obj){
    # Declarar variables
    $__row   = [];
    
    #Consultar a la base de datos
    #Consultar a la base de datos
    $ls    = $obj->lsTC([$_POST['idFolio']]);

     foreach ($ls as $_key) {
        $btn   = [];

        $btn[] = [
            "fn"    => 'QuitarTC',
            "color" => 'danger',
            "icon"  => 'icon-trash-2'
        ];


         $__row[] = array(

            'id'           => $_key['id'],
            'Cliente'      => $_key['Cliente'],
            'Concepto'     => $_key['Concepto'],
            'TTCodigo'     => $_key['TTCodigo'],
            'Monto'        => evaluar($_key['Monto']),
            'Autorizacion' => $_key['Autorizacion'],
            'TCodigo'      => $_key['TCodigo'],
            "opc"          => 0        
        );

    } 
    
    #encapsular datos
        return [
            "thead" => '',
            "row"   => $__row
        ];
}


function rpt_turno($obj){
    # Declarar variables
    $__row          = [];
    $date           = $_POST['date'];
    $ls             = $obj->VER_CATEGORIAS([1]);
    $total_ingresos = 0;

    $fpago = $obj->lsFormasPago(array(1));

    $totalGral = 0;
    foreach ($ls as $key) {
        
        $lsIngresos    = $obj -> lsIngresos([$_POST['idFolio'],$key['id']]);


        if($lsIngresos)
   
         $__row[] = array(
            'id'            => $key['id'],
            'Categoria'     => $key['Categoria'],
            "colgroup"      => true
        );

        foreach($lsIngresos as $_key): 

            $col    = [];
            $Total  = 0;

            foreach ($fpago as $key => $valfpago) {

                $montoFP = $obj->MontoFormasPago([
                    $_POST['idFolio'],
                    $_key['idSubcategoria'],$valfpago['id']]);


                $col[$valfpago['name']] = evaluar($montoFP);

                $Total += $montoFP;
                
            }

            $totalGral += $Total;

            $col['Total'] = evaluar($Total);
            $col['opc'] = 0;


            $row = array(
                'id'        => $key['id'],
                'Categoria' => $_key['Subcategoria'],
                'Tarifa'    => evaluar($_key['Tarifa']),
                );


            $__row[] = array_merge($row, $col);

           
        endforeach;
    }

    $__row[] = [

            'id'           => $key['id'],
            'Categoria'    => '<b>TOTAL GENERAL</b>',
            'Tarifa'    => '',
            'efectivo'     => '',
            'TC'           => '',
            'CxC'          => '',
            'Anticipo'     => '',
            'Total'        => evaluar($totalGral),
            "opc"          => 2
        ];

 
    /*-- Titulo del reporte --*/ 
  

     #encapsular datos
    return [
        'frm_head' => frmHead(['title'=> 'Pre corte','folio'=>$_POST['idFolio']]),
        "thead"    => ['Categoria','Tarifa','Efectivo','TC','CxC','Anticipo','Total'],
        "row"      => $__row
    ];
}

function frmHead($data){

    if($data['folio'] == null){
        $data['folio'] = '0000';
    }

    return '
    <div class="row m-0 mb-2">
         <div class="col-3 ">
            <p class="fw-bold m-0" id="folio"></span></p>
        </div>
        <div class="col-6 text-center">
            <p class="fw-bold text-uppercase m-0 fs-5" id="format_title">'.$data['title'].'</p>
            <p class="text-uppercase m-0" id="udn"></p>
        </div>
        <div class="col-3 text-end">
            <p class="fw-bold m-0" id="folio"> <span class="text-primary"></span></p>
        </div>
	</div>';

}



function rptGeneral($obj){
    # Declarar variables
    $__row   = [];
    $date      = $_POST['date'];

    #Consultar a la base de datos
    $ls             = $obj->VER_CATEGORIAS([1]);
    $total_ingresos = 0;


    foreach ($ls as $key) {
        
        $ingreso_categoria    = $obj -> getIngresosTurismo([$date,$key['id']]);
        
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

   foreach ($cortesias as $cortesia ) {

        $__row[] = array(
            'id'           => $cortesia['id_Categoria'],
            'Categoria'    => '<b>TOTAL '.$cortesia['Subcategoria'].'</b>',
            'SubTotal'     => '',
            'IVA'          => '',
            '2% HOSPEDAJE' => '',
            'Total'        => evaluar($cortesia['total']),
            "opc"  => 2
        );
    
   }

    
    $__row[] = array(
        'id'           => $key['id'],
        'Categoria'    => ['html'=>'TOTAL INGRESOS' , 'class'=>'fw-bold', 'style'=>'font-size:1rem;'],
        'SubTotal'     => '',
        'IVA'          => '',
        '2% HOSPEDAJE' => '',
        'Total'        => ['html'=>evaluar($total_ingresos) , 'class'=>'fw-bold text-end', 'style'=>'font-size:1rem;'],
        "opc"  => 0
    );

    #   -- Formas de pago  -- 

    $lsfolio = $obj-> lsFolio([$_POST['folio']]);
    
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
            'OBSERVACIONES' => '',            
            "opc"           => 0
        );
    }

    $__row_fp[] = array(
        'id'            => '',
        'Categoria'     => '<b>TOTAL</b>',
        'SubTotal'      => evaluar($total_formaspago),
        'OBSERVACIONES' => '<span id="Obs">'.$lsfolio['Observacion'].'</span>',            
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
      "titulo" => 'REPORTE GENERAL DE INGRESOS',
    ]);
    
    $footer = ticket_footer([
        "total" => $total_formaspago
    ]);

    # -- Tablas de datos --

    $table_fp = [

      'thead' => ['Forma de pago','Total','Observaciones'],
      'row'   =>$__row_fp
    ];

    $table_propina = [

      'thead'    => ['Propina','Total'],
      'row'      => $__row_propina,
      'frm_foot' => $footer
    ];

    $table_rpt = [
        'frm_head' => $head,
        'thead' => '',
        'row'   => $__row
    ];


    /* btn para imprimir formato */ 

    $CierreRpt = 1;

    $btn  = '
    <a class="btn btn-outline-success" onclick="CierreHotel()">
     <i class=" icon-print"></i> 
       Imprimir caratula </a> ';


    return [
        'btn' =>$btn,
        'table_rpt'=>$table_rpt,
        'table_fp'=>$table_fp,
        'table_propina'=>$table_propina];



     #encapsular datos
    // return [
    // "frm_head" => $head,
    // "thead"    => '',
    // "row"      => $__row
    // ];
}

function lsTC($obj){

    # Declarar variables
    $__row   = [];
    
    #Consultar a la base de datos
    $ls    = $obj->lsTC([$_POST['fol']]);

     foreach ($ls as $_key) {
        $btn   = [];

        $btn[] = [
            "fn"    => 'QuitarTC',
            "color" => 'danger',
            "icon"  => 'icon-trash-2'
        ];


         $__row[] = array(

            'id'           => $_key['id'],
            'Cliente'      => $_key['Cliente'],
            'Concepto'     => $_key['Concepto'],
            'TTCodigo'     => $_key['TTCodigo'],
            'Monto'        => evaluar($_key['Monto']),
            'Autorizacion' => $_key['Autorizacion'],
            'TCodigo'      => $_key['TCodigo'],
            "btn"          => $btn
        
        );

    } 
    
    #encapsular datos
        return [
            "thead" =>'',
            "row"   => $__row
        ];
}


function lsArchivos($obj){
    # Declarar variables
    $__row   = [];
    
    #Consultar a la base de datos
    $ls    = $obj->Select_Archivos([$_POST['date']]);

     foreach ($ls as $_key) {
        $btn   = [];




        $btn[] = [
            "onclick" => 'DownloadFile()',
            "class"   => 'btn btn-sm btn-outline-secondary ',
            "html"    => '<i class="icon-download-cloud"></i>',
            'title'   => 'Descargar archivo',
            'target'  => '_blank',
            "href"    => "https://www.15-92.com/" . $_key['Ruta'].$_key['Archivo'],
        ];

          $btn[] = [
            "onclick" => 'QuitarArchivo('.$_key['id'].')',
            "class"   => 'btn btn-sm btn-outline-danger m-1',
            "html"    => '<i class="icon-trash-empty"></i>'
        ];



        // if($_key['Descripcion'])

         $__row[] = array(

            'id'          => $_key['id'],
            'Archivo'     => $_key['Archivo'],
            'Descripcion' => $_key['Descripcion'],
            'Hora'        => $_key['Hora'],
            'peso'        => $_key['peso'],
            'tipo'        => $_key['Type_File'],
            "a"           => $btn
        
        );

    } 
    
    #encapsular datos
        return [
            "thead" => '',
            "row"   => $__row
        ];
}

function listIngresos($obj){

    // # Declarar variables
    $__row      = [];
    $now        = date("Y-m-d");
    
    $id         = $_POST['Categoria'];
    $date       = $_POST['date'];
    $__getFolio = $_POST['idFolio'];
    $totalGral  = 0;

    $imp   = $obj -> Select_Impuestos($id);
    $fpago = $obj -> lsFormasPago(array(1));
    $group = $obj -> lsGroup([$id]);

    foreach ($group as $key ) {
         $row        = [];

         $lsIngresos = $obj->lsIngresosbySubGrupo([$_POST['idFolio'],$key['idgrupo']]);


        // if($lsIngresos)
            $__row[] = array(
                'id'            => $key['id'],
                'Categoria'     => $key['gruponombre'],
                "colgroup"      => true
            );

        // foreach ($lsIngresos as $_key) {


        //     // if()
        //     $th  = ['Sub Categoria ('.$_key['idSubcategoria'].')','Tarifa','Pax','Subtotal'];


        //     $btn = [];

        //     $movimientos  = $obj ->__getMovimientos([$__getFolio,$_key['idSubcategoria']]);

        //     if($date == $now)
            
        //     $btn[] = [
        //         "fn"    => 'EditarMovimientos',
        //         "color" => 'primary ',
        //         "icon"  => ' icon-pencil ',
        //         "text"  => ' ( '.$movimientos.' ) '
        //     ];


        //     // -- Formas de pago --
            
        //     $total_formasPago = 0;

        //     $col    = [];
            
        //     if ( $id != 12) {
                

        //         foreach ($fpago as $key => $valfpago) {
        //             $th[] = $valfpago['name'];

        //             $montoFP                 = $obj->MontoFormasPago([$__getFolio,$_key['idSubcategoria'],$valfpago['id']]);
        //             $col[$valfpago['name']]  = evaluar($montoFP);
        //             $total_formasPago       += $montoFP;


        //             // $totalFormasPago[$valfpago['name']] += $montoFP;
        //         }

        //     }

        //     // -- Impuestos --


        //     $suma_temporal = 0;

        //     foreach ($imp as $key => $valimp) {
        //         $th[] = $valimp['Impuesto'];

        //         $Impuesto = $obj-> __getMontoImpuesto([
        //             $__getFolio,
        //             $_key['idSubcategoria'],
        //             $valimp['idImpuesto']
        //         ]);

        //         $col[$valimp['Impuesto']] = evaluar($Impuesto);

        //         $totalImpuesto[$valimp['Impuesto']] += $Impuesto;

        //         $suma_temporal += $Impuesto;

        //     }

        //     $diferencia = $_key['Tarifa'] - $total_formasPago; 

            
        //     array_push($th, 'Diferencia','opc');

            
        //     $col['Diferencia'] = evaluar($diferencia);
        //     $col['btn'] = $btn;


            
        //     $row = [
        //         'id'        => $_key['idSubcategoria'],
        //         'Categoria' => $_key['Subcategoria'],
        //         'Tarifa'    => evaluar($_key['Tarifa']),
        //         'Pax'       => $_key['pax'],
        //         'Monto'     => evaluar($_key['Subtotal']),
                      
        //     ];


        //      $__row[] = array_merge($row, $col);

        // }


        
    }    
    
    //   --  Total general  --


  

 
 
     #encapsular datos
    return [
        "thead"    => $th,
        "row"      => $__row
    ];

}



function lsIngresos(){

    // Instancia a ingresos 
    $obj = new Ingresos;

    # Declarar variables
    $__row    = [];
  
    $date       = $_POST['date'];
    $id         = $_POST['Categoria'];
    $__getFolio = $_POST['idFolio'];
    $now        = date("Y-m-d");
    // if ($id == 13) $id_g  = 2; // CARGOS DE HABITACION
    $imp     = $obj ->lsImpuestos($id);
    $fpago   = $obj ->lsFormasPago(array(1));
    $lsGrupo = $obj->lsGrupo([$id]);


    // totales generales.
    $totalFormasPago = [];
    $totalImpuesto   = [];
    
    foreach ($lsGrupo as $key): 
        
        $ventas = $obj ->listIngresosPorGrupo([$key['idgrupo'],$id,$__getFolio]);
        
        if($ventas): // muestra solo si existen ventas.
            
        
            $__row[] = array(
                'id'           => $key['idgrupo'],
                'nombre'       => $key['gruponombre'],
                'colgroup'     => 1
            );
        
            foreach ($ventas as $venta) {
                $conceptos = [];
                $col       = [];
                $btn       = [];

                $movimientos  = $obj ->__getMovimientos([$__getFolio,$venta['idSubcategoria']]);

        
                $btn[]     = [
                    "fn"    => 'EditarMovimientos',
                    "color" => 'primary ',
                    "icon"  => ' icon-pencil ',
                    "text"  => ' ( '.count($movimientos).' ) '
                ];


                if($id ==1): // aplica a hospedaje.

                    $conceptos = array(
                    'id'     => $venta['idSubcategoria'],
                    'nombre' => $venta['Subcategoria'],
                    'Pax'    => $venta['pax'],
                    'Noche'  => $venta['Noche'],
                    'Monto' => evaluar($venta['Tarifa']),
                    );
                
                else:

                    $conceptos = array(
                    'id'     => $venta['idSubcategoria'],
                    'nombre' => $venta['Subcategoria'],
                    'Tarifa' => $venta['Tarifa'],
                    'Pax'    => $venta['pax'],
                    'Monto'  => evaluar($venta['Tarifa']),
                    );

                endif;

                    
                // añadir como se pago
                $total_formasPago = 0;

                foreach ($fpago as  $_key) {

                    $Montofpago                            = $obj ->lsMontoFormasPago([$venta['idVentasBit'],$_key['idFormas_Pago']]);
                    $col[$_key['FormasPago']]              = ['class'=>' bg-warning-1 text-end', 'html'=> evaluar($Montofpago)];
                    $totalFormasPago[$_key['FormasPago']] += $Montofpago;

                    $total_formasPago += $Montofpago;
                }

                // mostrar impuestos:

                foreach ($imp as $valimp) {

                    $Impuesto = $obj-> __getMontoImpuesto([
                    
                        $__getFolio,
                        $venta['idSubcategoria'],
                        $valimp['idImpuesto']
                    
                    ]);

                    $col[$valimp['Impuesto']] = ['class'=>'text-info text-end','html'=>evaluar($Impuesto)];

                    $totalImpuesto[$valimp['Impuesto']] += $Impuesto;

                    // $suma_temporal += $Impuesto;

                }


                // calcular diferencia: 

                $diferencia        = $venta['Tarifa'] - $total_formasPago;
                $col['Diferencia'] = evaluar($diferencia);
                $col['btn']        = $btn;
            

                $__row[] = array_merge($conceptos,$col);                
                $totalIngresos += $venta['Tarifa'];

            }
        
    
        endif;
          
    endforeach;


    // --  Total general  --
    $col = [];

    $row = array(
    'id'       => '',
    'grupo'    => 'TOTAL',

    'pax'      => evaluar2($pax),
    'Noche'    => evaluar2($N_M),
    'Monto'    => evaluar($totalIngresos),
    );

    foreach ($fpago as $key => $valfpago) {
        $col[$valfpago['FormasPago']] = evaluar($totalFormasPago[$valfpago['FormasPago']]);
    }

    foreach ($imp as $valimp) {
        $col[$valimp['Impuesto']] = evaluar($totalImpuesto[$valimp['Impuesto']]);
    }



    $col['Diferencia'] = '-';
    $col['btn'] = null;
    $col['opc'] = 2;

    $__row[] = array_merge($row, $col);
   
   



    return [
        "thead" => getColumns($id),
        // "thead" => '',
        "row"   => $__row
    
    ];

}


function getColumns($idCategoria) {
    
    if ($idCategoria == 1 ) {
       return ['SubCategoria', 'Pax',  'Noche' , 'Monto', 'Efectivo', 'TC', 'CxC', 'Anticipo', 'iva', 'Hospedaje', 'Diferencia',''];

    }
    if ($idCategoria == 2) {
       return ['SubCategoria', 'Pax',  'Mesas', 'Monto', 'Efectivo', 'TC', 'CxC', 'Anticipo', 'iva', 'Diferencia',''];

    }
    else if ($idCategoria == 3 || $idCategoria == 4 || $idCategoria == 5 || $idCategoria == 8 ) {
        return ['SubCategoria','Tarifa','Pax', 'Monto', 'Efectivo', 'TC', 'CxC', 'Anticipo', 'Iva', 'Diferencia',''];
    }else if ($idCategoria == 6 || $idCategoria == 7  ) {
        return ['SubCategoria','Pax', 'Monto', 'Efectivo', 'TC', 'CxC', 'Anticipo', 'Iva', 'Diferencia',''];
    }
    
    else {
        return ['SubCategoria', 'Monto', 'Efectivo', 'TC', 'CxC', 'Anticipo', 'Diferencia',''];;
    }

}





function lsIngresosx($obj){
    // # Declarar variables
    $__row    = [];
    $date     = $_POST['date'];
    $id       = $_POST['Categoria'];
    $now      = date("Y-m-d");
    
    /* -- VARIABLES POR REVISAR  --*/ 
    // ------------
    $Sub       = 0;
    $pax       = 0;
    $tarifa    = 0;
    $N_M       = 0;
    $IVA       = 0;
    $IVA2      = 0;
    $TotalGral = 0;
    $DIF       = 0;
    $t2        = 0;
    $txt       = '';
    // -------------
    $Efectivo  = 0;
    $TC        = 0;
    $CxC       = 0;
    $Anticipo  = 0;
    $CargosHab = 0;
    $Cargos2   = 0;
    // -------------
    $id_g      = 1;

    $totalFormasPago = [];
    $totalImpuesto   = [];
    $totalSubtotal   = 0;
    


    $imp   = $obj ->Select_Impuestos($id);
    $fpago = $obj->lsformas_pago(array($id_g));

    if ($id == 13) { // CARGOS DE HABITACION
        $id_g  = 2;
    }
    
    $__getFolio = $_POST['idFolio'];

    // // // #Consultar a la base de datos
    $lsGrupo    = $obj->Select_group([$id]);

    foreach ($lsGrupo as $key) {
            $row   = [];
            $sub   = $obj->Select_Subcategoria_x_grupo($id,$key['id']);
            
           
            foreach ($sub as $_key) { 

                    /*-----------------------------*/
                    /*	Detalles de Tarifas
                    /*-----------------------------*/
                    $monto    = $obj-> Select_MontoSubtotal([$__getFolio,$_key['idSubcategoria']]);
                    $paxID    = $obj-> ExisteEnBitacora([$__getFolio,$_key['idSubcategoria']],'pax');
                    $noID     = $obj-> ExisteEnBitacora([$__getFolio,$_key['idSubcategoria']],'Noche');
                    $tar      = $obj-> ExisteEnBitacora([$__getFolio,$_key['idSubcategoria']],'Tarifa');

                    $btn          = [];
                    $movimientos  = $obj ->__getMovimientos([$__getFolio,$_key['idSubcategoria']]);

                    if($tar != 0){

                        
                        if($date == $now)

                        $btn[] = [
                            "fn"    => 'EditarMovimientos',
                            "color" => 'primary ',
                            "icon"  => ' icon-pencil ',
                            "text"  => ' ( '.$movimientos.' ) '
                        ];


                    /*---------------------------*/
                    /* Formas de Pago
                    /*---------------------------*/
                    $total_formasPago = 0;
                    if ( $id != 12) {
                        $col    = [];

                        foreach ($fpago as $key => $valfpago) {

                            $montoFP = $obj->Select_MontoFPago([$__getFolio,$_key['idSubcategoria'],$valfpago['id']]);
                            
                            $col[$valfpago['name']] = evaluar($montoFP);

                            $total_formasPago += $montoFP;
                            
                            $totalFormasPago[$valfpago['name']] += $montoFP;
                        }

                    }


                    /*-----------------------------------*/
                    /*	SubTotal
                    /*-----------------------------------*/
                    if ( $id != 12 ) {
                     $col['Sub-Total'] = $monto;
                     $totalSubtotal += $monto;
                    }

                    $suma_temporal = 0;

                    foreach ($imp as $key => $valimp) {

                        $Impuesto = $obj-> __getMontoImpuesto([
                        $__getFolio,
                        $_key['idSubcategoria'],
                        $valimp['idImpuesto']
                        ]);

                        $col[$valimp['Impuesto']] = evaluar($Impuesto);

                        $totalImpuesto[$valimp['Impuesto']] += $Impuesto;

                        $suma_temporal += $Impuesto;

                    }

                    $diferencia = $tar - $total_formasPago; 
                    $col['Diferencia'] = evaluar($diferencia);
                    $col['btn'] = $btn;



                    /*Crear --- Fila de datos --- */ 

                    $pax    += $paxID;
                    $tarifa += $tar;
                    $N_M    += $noID;

                    if($id==9){


                        $row = array(
                            'id'       => $_key['idSubcategoria'],
                            'grupo'    => $_key['Subcategoria'],
                            'Monto'    => evaluar($tar),
                        );


                    }else{

                        $row = array(
                        'id'       => $_key['idSubcategoria'],
                        'grupo'    => $_key['Subcategoria'],
                        'pax'      => evaluar2($paxID),
                        'Noche'    => evaluar2($noID),
                        'Monto'    => evaluar($tar),
                        );


                    }


                    $__row[] = array_merge($row, $col);

                    }        

            }


    }// end sub grupo

    $col    = [];


    foreach ($fpago as $key => $valfpago) {
        $col[$valfpago['name']] = evaluar($totalFormasPago[$valfpago['name']]);
    }
    
    if ( $id != 12 ) {
        $col['Sub-Total'] = evaluar($totalSubtotal);
    }

    foreach ($imp as $key => $valimp) {
        $col[$valimp['Impuesto']] = evaluar($totalImpuesto[$valimp['Impuesto']]);
    }
    


    $col['Diferencia'] = '-';
    $col['btn'] = null;
    $col['opc'] = 2;

    if($id == 9){
    
        $row = array(
            'id'       => '',
            'grupo'    => 'Total',
            'Monto'    => evaluar($tarifa),
        );
    
    }else{

        $row = array(
            'id'       => '',
            'grupo'    => 'Total',

            'pax'      => evaluar2($pax),
            'Noche'    => evaluar2($N_M),
            'Monto'    => evaluar($tarifa),
        );


    }

    
   
    $__row[] = array_merge($row, $col);
   
    # encapsular datos
    return [
        "frm_head" => '',
        "thead"    => '',
        "row"      => $__row
    ];

 

}

function formas_pago(){
  $fpago = $fin->Select_formaspago_by_categoria(array($id_g));

  if ($id != 12) { // Empleados & cortesia
   foreach ($fpago as $key => $value) { // encabezado formar de pago
    $tb .= '<th class="text-center col-sm-1 col-xs-1 ">'.$value[1].'</th>';
   }

   $tb .= '<th class="text-center col-sm-1 col-xs-1">Subtotal</th>';
  }

  foreach ($imp as $key => $value) { //Formas de Pago
   $tb .= ' <th class="text-center col-sm-1 col-xs-1"> '.$value[1].'</th>';
  }
}

function td_tarifas($categoria){
 $tdx    = [];

 switch ($categoria) {
  case 1:
  
    $tdx    = [
        "Pax"      => '',
        "Noche"    => '',
        "Monto"    => '',
        'Efectivo' => '',
        'TC'       => '',
        'CxC'      => '',
        'Anticipo' => '',
        "SubTotal" => '',
        "opc"   => 1
    ] ;
  break;


  case 2:
   $tdx    = [
        "Pax"   => '',
        "Mesas" => '',
        "Monto" => '',
        "Total" => '',
        "opc"   => 1
    ] ;  
 
  break;

  case 9:
  $td .= '<th>Monto</th>';
  break;
  case 11:
  $td .= '<th>Pax</th>';

  $td .= '<th>Noche</th><th>Tarifa</th>';
  break;

  case 12:
  $td .= '<th>Monto</th>';
  break;
  default:
   $tdx    = [
        "Pax"   => '',
        "Monto" => '',
        "Total" => '',
        "opc"   => 1
    ] ; 

  $td .= '<th>Pax</th>';
  $td .= '<th>Monto</th>';
  break;
 }

 return  $tdx;
}


function DynamicRow($ls) {
    $__row = [];
    foreach ($ls as $fila) {

        foreach ($fila as $key => $valor) {
            
            $fila[$key] = $valor;
        }

        $fila['opc'] = 0;

        $__row[] = $fila;
    }
    return $__row;
}

function evaluar($val){

    if($val < 0) $text = 'text-danger'; else $text = '';
    return $val ? '<span class="'.$text.'"> $ ' . number_format($val, 2, '.', ',').'</span>' : '-';

}

function evaluar2($val){

    return $val ? $val : '-';

}
/* ----- Complementos ----- */ 
function ticket_head($data){

    

    return  '

    <div  class="col-sm-12   ">

    <table class="table table-bordered  table-sm mt-2">


    <tr>
        <td class="col-sm-1 bg-disabled2"> <strong> FECHA :  </strong></td>
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
    <table style="font-size:.1rem; font-weight:bold;" class="table table-bordered mb-3">
          <tr>
            <td class="col-8">
                <b>TOTAL </b>
            </td>
            <td class="text-end"> 
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
 


echo json_encode($encode);
?>        
