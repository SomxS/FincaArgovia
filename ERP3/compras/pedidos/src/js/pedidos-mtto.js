$(function(){
                        let datos = new FormData();
                        datos.append('opc', 'prueba');
                        send_ajax(datos, 'pedidos/ctrl/ctrl-conexion.php').then((data) => {
                            alert(data);
                        });
                    });