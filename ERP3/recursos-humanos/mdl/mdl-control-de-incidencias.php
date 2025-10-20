<?php
                    require_once('../../conf/_CRUD.php');
                
                    class Incidencias extends CRUD{
                        function lsUDN(){
                            $query ="SELECT idUDN AS id, UDN AS valor FROM udn WHERE Stado = 1";
                            return $this->_Read($query, null);
                        }
                    }
                    ?>