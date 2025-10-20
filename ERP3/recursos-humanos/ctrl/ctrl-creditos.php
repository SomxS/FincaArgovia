<?php
                    if(empty($_POST['opc'])){
                        exit(0);
                    }
                    
                    require_once('../mdl/mdl-creditos.php');
                    $obj = new Creditos;

                    $encode = [];
                    switch ($_POST['opc']) {
                        case 'listUDN':
                                $encode = $obj->lsUDN();
                            break;
                    }
                    
                    echo json_encode($encode);
                    ?>