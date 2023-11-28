<?php
require_once __DIR__ . "/../controladores/clienteController.php";
require_once __DIR__ . "/../controladores/comandaController.php";
require_once __DIR__ . "/../controladores/mesaController.php";

class VisorCliente
{

    static function registroCliente($datos)
    {
        $controlador = new ClienteController();
        if($controlador->buscarClientePorUsuario($datos["usuario"]) == false)
        {
            $retornoInsertar = $controlador->insertarCliente($datos["nombre"], $datos["usuario"], password_hash($datos["clave"], PASSWORD_DEFAULT));
            $arrayRespuesta["status"] = "OK";
            $arrayRespuesta["message"] = "Se insertó el cliente con exito con el ID: {$retornoInsertar}";
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El nombre de usuario ya esta en uso";
        }
        return $arrayRespuesta;
    }
    
    static function loginCliente($datos)
    {
        $controlador = new ClienteController();
        $clienteRetornado = $controlador->buscarClientePorUsuario($datos["usuarioLogin"]);
        if($clienteRetornado != false)
        {
            $hashRetornado = $controlador->obtenerClavePorUsuario($datos["usuarioLogin"]);
            if(password_verify($datos["claveLogin"], $hashRetornado))
            {
                $arrayRespuesta["status"] = "OK";
                $arrayRespuesta["message"] = "Sesion iniciada";
                $arrayRespuesta["idCliente"] = $clienteRetornado->id;

            }
            else
            {   
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Clave incorrecta";
            }
            
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "Usuario inexistente";
        }         
        return $arrayRespuesta;
    }
    
    
    static function bajaCliente($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new ClienteController();
                $clienteRetornado = $controlador->buscarClientePorId($datos["id"]);
                if($clienteRetornado == false)
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "El cliente no existe o ya esta dado de baja";
                }
                else
                {
                    $controlador->bajaCliente($datos["id"]);
                    $arrayRespuesta["status"] = "OK";
                    $arrayRespuesta["message"] = "Cliente borrado con exito (baja logica)";
                }

            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser socio para dar de baja un cliente";
            }
            

        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function modificarCliente($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
    
                $controlador = new ClienteController();
                $clienteRetornado = $controlador->buscarClientePorId($datos["id"]);
                if($clienteRetornado == false)
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "El cliente no existe";
                }
                else
                {
                    $nombreModificado = $clienteRetornado->nombre;
                    $usuarioModificado = $clienteRetornado->usuario;
                    $claveModificada = $clienteRetornado->clave;
                    $flagModificacion = false;
                    if(isset($datos["nombre"]) && $datos["nombre"] != $nombreModificado)
                    {
                       $nombreModificado = $datos["nombre"];
                       $flagModificacion = true;
                    }
                    if(isset($datos["usuario"]) && $datos["usuario"] != $usuarioModificado)
                    {
                        $existeCliente = $controlador->buscarClientePorUsuario($datos["usuario"]);
                        if($existeCliente == false)
                        {
                            $usuarioModificado = $datos["usuario"];
                            $flagModificacion = true;
                        }
                        else
                        {
                            $arrayRespuesta["status"] = "WARNING";
                            $arrayRespuesta["message"] = "El nombre de usuario ya esta en uso, se deja el original";
                        }
                    }
                    if(isset($datos["clave"]) && !password_verify($datos["clave"], $claveModificada))
                    {
                        $claveModificada = password_hash($datos["clave"], PASSWORD_DEFAULT);
                        $flagModificacion = true;
                    }
                    if($flagModificacion)
                    {
                        $controlador->modificarCliente($clienteRetornado->id, $nombreModificado, $usuarioModificado, $claveModificada, $clienteRetornado->estado);     
                        $arrayRespuesta["status"] = "OK";
                        $arrayRespuesta["message"] = "Cliente modificado con exito"; 
                    }
                    else
                    {                            
                        $arrayRespuesta["status"] = "WARNING";
                        $arrayRespuesta["message"] = "No se realizo ninguna modificacion";
                    }
                }
               
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser socio para modificar un cliente";
            }
            

        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function pedirCuenta($datos)
    {
        if(isset($datos["datos"]["idUsuario"]))
        {
            $controladorComanda = new ComandaController();
            $comandaRetornada = $controladorComanda->buscarComandaPorId($datos["id"]); 
            if($comandaRetornada)
            {
                if($comandaRetornada->idCliente == $datos["datos"]["idUsuario"])
                {
                    //cambiar estado de la mesa a con cliente pagando
                    $controladorMesa = new MesaController();
                    $mesaRetornada = $controladorMesa->buscarMesaPorId($comandaRetornada->idMesa);
                    if($mesaRetornada->estado != "con cliente pagando")
                    {
                        if($mesaRetornada->estado == "con cliente comiendo")
                        {
                            $controladorMesa->modificarMesa($comandaRetornada->idMesa, "con cliente pagando");
                            $arrayRespuesta["status"] = "OK";
                            $arrayRespuesta["message"] = "Cuenta pedida con exito"; 
                        }
                        else
                        {
                            $arrayRespuesta["status"] = "ERROR";
                            $arrayRespuesta["message"] = "No puede pedir la cuenta hasta que no esten todos sus pedidos listos para servir";
                        }
                    }
                    else
                    {
                        $arrayRespuesta["status"] = "ERROR";
                        $arrayRespuesta["message"] = "La cuenta ya fue pedida previamente";
                    }
                }
                else
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "Esa comanda no corresponde a su usuario";
                }
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "No existe una comanda con ese id";
            }
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un cliente";
        }
        return $arrayRespuesta;
    }
    
    static function verEstadoComanda($datos)
    {
        if(isset($datos["datos"]["idUsuario"]))
        {
            $controladorComanda = new ComandaController();
            $comandaRetornada = $controladorComanda->buscarComandaPorId($datos["id"]); 
            if($comandaRetornada && $comandaRetornada->idMesa == $datos["idMesa"])
            {
                if($comandaRetornada->idCliente == $datos["datos"]["idUsuario"])
                {
                    //verificar que todos los estados de la comanda esten en preparacion
                    $arrayEstados = explode(",", $comandaRetornada->estado);
                    $arrayProductos = explode(",", $comandaRetornada->detalle);
                    $arrayTiempoEstimado = explode(",", $comandaRetornada->tiempoEstimadoFinalizacion);
        
                    $flagEnPreparacion = false;
                    foreach($arrayEstados as $estado)
                    {
                        if($estado != "en preparacion")
                        {
                            $flagEnPreparacion = false;
                            break;
                        }
                        else
                        {
                            $flagEnPreparacion = true;
                        }
                    }
        
                    if($flagEnPreparacion)
                    {
                        $arrayListado = array();
                        $i = 0;
                        foreach($arrayEstados as $estado)
                        {
                            array_push($arrayListado, array("producto" => $arrayProductos[$i], "tiempo" => $arrayTiempoEstimado[$i]));
                            $i++;
                        }
                        $arrayRespuesta["status"] = "OK";
                        $arrayRespuesta["message"] = "Listado realizado con exito";
                        $arrayRespuesta["list"] = $arrayListado;
                    }
                    else
                    {
                        $arrayRespuesta["status"] = "ERROR";
                        $arrayRespuesta["message"] = "Para ver el tiempo estimado de su comanda, todos los productos deben estar en preparacion";
                    }
        
                }
                else
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "Esa comanda no corresponde a su usuario";
                }
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "No existe una comanda con ese id y idMesa";
            }
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un cliente";
        }
        return $arrayRespuesta;
    }
}

?>