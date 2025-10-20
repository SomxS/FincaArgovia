<?php
                    require_once('../../..//conf/_CRUD.php');
                
                    class Pedidos-mtto extends CRUD{
                        function now(){
                            return $this->('SELECT NOW()',null);
                        }
                    }
                    ?>