<?php
                    if(empty($_POST['opc'])){
                        exit(0);
                    }
                    
                    require_once('../mdl/mdl-disponibilidad.php');
                    $obj = new Disponibilidad;

                    $encode = [];
                    switch ($_POST['opc']) {
                        case 'listUDN':
                                $encode = $obj->lsUDN();
                            break;
                    }
                    
                    echo json_encode($encode);
                    ?>