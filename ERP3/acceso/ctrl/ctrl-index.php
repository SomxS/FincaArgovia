<?php
if(!isset($_POST['opc'])) exit(0);

include_once('../mdl/mdl-index.php'); $obj = new Index;

$encode = [];

switch($_POST['opc']){
    case 'login':
            $user       = mb_strtoupper(str_replace("'","",$_POST['usuario']), 'UTF-8');
            $pass       = str_replace("'","",$_POST['clave']);
            $usr_datos  = $obj->consulta_user([$user,$pass,$pass]);
            
            if(isset($usr_datos) && count($usr_datos) > 0 ){ 
                
                $expira = time() + (365 * 24 * 60 * 60);
                setcookie( "IDU", $usr_datos[0]['IDU'] , $expira, "/");
                setcookie( "IDP", $usr_datos[0]['IDP'] , $expira, "/");
                
                unset($usr_datos[0]['IDU']);
                unset($usr_datos[0]['IDP']);
                $encode = $usr_datos[0];
            } else {
                $encode = false;
            }
        break;
}
echo json_encode($encode);

?>