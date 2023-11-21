<?php
require_once __DIR__ . "/../controladores/clienteController.php";
require_once __DIR__ . "/../controladores/comandaController.php";
require_once __DIR__ . "/../controladores/mesaController.php";

function registroCliente($datos)
{
    if( isset($datos["nombre"]) && isset($datos["usuario"]) && isset($datos["clave"]))
    {
        $controlador = new ClienteController();
        if($controlador->buscarClientePorUsuario($datos["usuario"]) == false)
        {
            $retornoInsertar = $controlador->insertarCliente($datos["nombre"], $datos["usuario"], $datos["clave"]);
            echo json_encode(array("OK" => "Se insertó el cliente con exito con el ID: {$retornoInsertar}"));
        }
        else
        {
             echo json_encode(array("ERROR" => "El nombre de usuario ya esta en uso"));
        }
    }
    else
    {
        echo json_encode(array("ERROR" => "Faltan datos necesarios para el registro del cliente. Se requiere nombre, usuario y clave"));
    }
    echo "\n";
}

function loginCliente($datos)
{
    $retorno = false;
    if(isset($datos["usuarioLogin"]) && isset($datos["claveLogin"]))
    {
        
           $controlador = new ClienteController();
           $clienteRetornado = $controlador->buscarClientePorUsuario($datos["usuarioLogin"]);
           if($clienteRetornado != false)
           {
            $clienteRetornado = $controlador->buscarClientePorUsuarioClave($datos["usuarioLogin"], $datos["claveLogin"]);
               if($clienteRetornado == false)
               {
                   echo json_encode(array("ERROR" => "Clave incorrecta"));
               }
               else
               {   
                   echo json_encode(array("OK" => "Sesion iniciada"));
                   $retorno = $clienteRetornado->id;
               }

           }
           else
           {
                echo json_encode(array("ERROR" => "Usuario inexistente"));
           }
        
    }
    else
    {
        echo json_encode(array("ERROR" => "faltan datos necesarios para el login. Se requiere usuarioLogin y claveLogin"));
    }
    echo "\n";
    return $retorno;
}


function bajaCliente($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["id"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new ClienteController();
                $clienteRetornado = $controlador->buscarClientePorId($datos["id"]);
                if($clienteRetornado == false)
                {
                    echo json_encode(array("ERROR" => "El cliente no existe o ya esta dado de baja"));
                }
                else
                {
                    $controlador->bajaCliente($datos["id"]);
                    echo json_encode(array("OK" => "Cliente borrado con exito (baja logica)"));
                }

            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser socio para dar de baja un cliente"));
            }

        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para la baja del cliente. Se requiere id"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function modificarCliente($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {

        if(isset($datos["id"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
    
                $controlador = new ClienteController();
                $clienteRetornado = $controlador->buscarClientePorId($datos["id"]);
                if($clienteRetornado == false)
                {
                    echo json_encode(array("ERROR" => "El cliente no existe"));
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
                            echo json_encode(array("ADVERTENCIA" => "El nombre de usuario ya esta en uso, se deja el original"));
                        }
                    }
                    if(isset($datos["clave"]) && $datos["clave"] != $claveModificada)
                    {
                        $claveModificada = $datos["clave"];
                        $flagModificacion = true;
                    }
                    if($flagModificacion)
                    {
                        $controlador->modificarCliente($clienteRetornado->id, $nombreModificado, $usuarioModificado, $claveModificada, $clienteRetornado->estado);      
                        echo json_encode(array("OK" => "Cliente modificado con exito"));
                    }
                    else
                    {
                        echo json_encode(array("ADVERTENCIA" => "No se realizo ninguna modificacion"));
                    }
                }
               
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser socio para modificar un cliente"));
            }
    
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para la modificacion del cliente. Se requiere id para identificar al cliente a modificar"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function pedirCuenta($datos)
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
                        echo json_encode(array("OK" => "Cuenta pedida con exito"));  
                    }
                    else
                    {
                        echo json_encode(array("ERROR" => "No puede pedir la cuenta hasta que no esten todos sus pedidos listos para servir"));  
                    }
                }
                else
                {
                    echo json_encode(array("ERROR" => "La cuenta ya fue pedida previamente"));  
                }
            }
            else
            {
                echo json_encode(array("ERROR" => "Esa comanda no corresponde a su usuario"));  
            }
        }
        else
        {
            echo json_encode(array("ERROR" => "No existe una comanda con ese id"));  
        }
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un cliente"));  
    }
}

function verEstadoComanda($datos)
{
    if(isset($datos["datos"]["idUsuario"]))
    {
        $controladorComanda = new ComandaController();
        $comandaRetornada = $controladorComanda->buscarComandaPorId($datos["id"]); 
        if($comandaRetornada)
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
                    $i = 0;
                    foreach($arrayEstados as $estado)
                    {
                        echo json_encode(array($arrayProductos[$i] => "Tiempo: " . $arrayTiempoEstimado[$i] . " minutos"));
                        echo "\n";  
                        $i++;
                    }
                }
                else
                {
                    echo json_encode(array("ERROR" => "Para ver el tiempo estimado de su comanda, todos los productos deben estar en preparacion"));  
                }
    
            }
            else
            {
                echo json_encode(array("ERROR" => "Esa comanda no corresponde a su usuario"));  
            }
        }
        else
        {
            echo json_encode(array("ERROR" => "No existe una comanda con ese id"));  
        }
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un cliente"));  
    }
}
?>