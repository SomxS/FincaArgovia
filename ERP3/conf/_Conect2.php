<?php
	class Conection {
		protected $mysql;
        protected $connected = false;

		public function connect() {
            // if (!$this->connected) {
                // $conf = require_once('_conf.php'); 
    			$host   = "localhost";
    			$user   = "hgpqgijw_desarrollo";
    			$pass   = "c(SomxD)3";
    			$db     = "hgpqgijw_erp";
                
                //  La opción especifica que se debe ejecutar el comando "SET NAMES utf8" para asegurarse de que la conexión use la codificación de caracteres UTF-8.
                $opc = array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    );

                try {
                    $this->mysql = new PDO('mysql:host='.$host.';dbname='.$db,$user,$pass,$opc);
                    $this->mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    $message =  "[ ERROR CONECT ] :: ". $e->getMessage();
                    $this->writeToLog($message);
                }
            // }
		}

        function writeToLog($message) {
            $logFile = 'error.log';
            $this->crearArchivoLog($logFile);
            
            // Formato del mensaje: [Fecha y hora] Mensaje de error
            date_default_timezone_set('America/Chihuahua');
            $logMessage  = "[ ".date('Y-m-d H:i:s'). " ]";
            $logMessage .= "\n[ UNIX_USR ] :: " . trim(exec('whoami'));
            $logMessage .= "\n[ WINDOWS_USR ] :: " . trim(exec('echo %USERNAME%'));
            $logMessage .= "\n$message" . PHP_EOL;
            
            // Escribir el mensaje en el archivo de registro
            file_put_contents($logFile, $logMessage, FILE_APPEND);
        }

        function crearArchivoLog($filename) {
            // Verificar si el archivo existe
            if (!file_exists($filename)) {
                // Crear el archivo y establecer permisos adecuados
                $file = fopen($filename, 'w');
                fclose($file);
        
                // Asignar permisos adecuados al archivo (por ejemplo, 0644) - el propietario puede leer y escribir, otros solo pueden leer
                chmod($filename, 0644);
            }
        }
	}
?>