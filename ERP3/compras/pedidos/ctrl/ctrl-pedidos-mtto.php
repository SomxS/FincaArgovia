<?php
                    if(empty($_POST['opc'])){
                        exit(0);
                    }
                    
                    require_once('../mdl/mdl-pedidos-mtto.php');
                    $obj = new Pedidos-mtto;

                    $encode = [];
                    switch ($_POST['opc']) {
                        case 'opc':
                                $encode = $obj->now();
                            break;
                    }
                    
                    echo json_encode($encode);
                    ?>