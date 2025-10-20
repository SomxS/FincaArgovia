<?php
require_once('../../conf/_CRUD.php');
class Index extends CRUD {
    function consulta_user($array) {
        return $this->_Read('SELECT
        	idUser AS IDU,
        	usr_perfil AS IDP,
        	dir_ruta AS ruta
        FROM
        	usuarios
        	INNER JOIN permisos ON id_Perfil = usr_perfil
        	INNER JOIN directorios ON id_Directorio = idDirectorio 
        WHERE
        	usser = ? 
        	AND (
        		keey = MD5( ? ) 
        	OR keey2 = MD5( ? )) 
        	AND usr_estado = 1
        	AND directorios.dir_estado = 1 
        	AND directorios.dir_visible = 1 
        ORDER BY
        	dir_orden ASC 
        	LIMIT 1
        ',$array);
    }
}
?>