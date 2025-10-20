<?php
    require_once('../conf/_CRUD.php');
    
    class Sidebar extends CRUD {
        function allDirectory(){
            $query = "SELECT
                    	modulos.idModulo AS idM,
                    	modulos.modulo,
                    	directorios.idDirectorio AS id,
                    	directorios.directorio AS directorio,
                    	directorios.dir_ruta AS ruta,
                    	directorios.dir_estado AS estado,
                    	directorios.dir_block AS validacion,
                    	directorios.dir_visible AS oculto,
                    	directorios.dir_submodulo AS idS,
                    	(SELECT submodulos.submodulo FROM submodulos WHERE submodulos.idSubmodulo = idS) AS submodulo
                    FROM
                    	directorios
                     INNER JOIN modulos ON modulos.idModulo = directorios.dir_modulo
                    WHERE
                     directorios.dir_visible = 1
                    GROUP BY
                    	directorios.idDirectorio 
                    ORDER BY
                    	modulo ASC,
                    	id ASC,
                    	directorios.dir_estado DESC";
            return $this->_Read($query,null);
        }
    }

    $obj           = new Sidebar;
    $tempModulo    = '';
    $tempSubModulo = '';
    $modulos       = [];
    $submodulos    = [];

    $sql = $obj->allDirectory();
    foreach ($sql as $row) {
          // Modulos
        if($tempModulo != $row['idM']){
            $tempModulo = $row['idM'];
            $modulos[]  = [
                "id"          => $row['idM'],
                "modulo"      => mb_strtoupper($row['modulo'],'utf-8'),
                "submodulos"  => [],
                "directorios" => [],
            ];
        }
          // Modulos/Submodulos
        if ( $tempSubModulo != $row['idS'] && isset($row['idS']) ) {
            $tempSubModulo = $row['idS'];
            foreach ($modulos as &$mod) {
                if ( $tempModulo == $mod["id"]) {
                    $mod['submodulos'][] = [
                        "id"          => $row['idS'],
                        "submodulo"   => mb_strtoupper($row['submodulo'],'utf-8'),
                        "directorios" => []
                    ];
                }
            }
        }
          // Modulos/Submodulos/Directorios
        if ( isset($row['idS']) ) {
            foreach ($modulos as &$mod) {
                if ( $tempModulo == $mod["id"]) {
                    foreach ($mod["submodulos"] as &$sub) {
                        if ( $sub["id"] == $row["idS"]) {
                            $sub['directorios'][] = [
                                "id"         => $row['id'],
                                "directorio" => $row['directorio'],
                                "estado"     => $row['estado'],
                                "validacion" => $row['validacion'],
                                "ruta"       => $row['ruta'],
                                "oculto"     => $row['oculto']
                            ];
                        }
                    }
                }
            }
        }
          // Modulos/Directorios
        if(!isset($row['idS'])){
            foreach ($modulos as &$mod) {
                if ( $tempModulo == $mod["id"]) {
                    $mod['directorios'][] = [
                        "id"         => $row['id'],
                        "directorio" => $row['directorio'],
                        "estado"     => $row['estado'],
                        "validacion" => $row['validacion'],
                        "ruta"       => $row['ruta'],
                        "oculto"     => $row['oculto']
                    ];
                }
            }
        }
    }

    $encode = $modulos;

    echo json_encode($encode);
?>