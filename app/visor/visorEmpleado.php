<?php
require_once __DIR__ . "/../controladores/empleadoController.php";

function registroEmpleado($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if( isset($datos["nombre"]) && isset($datos["apellido"]) && isset($datos["usuario"]) && isset($datos["clave"]) && isset($datos["tipo"]) )
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                if(Empleado::ValidarDatos($datos["nombre"], $datos["apellido"], $datos["usuario"], $datos["clave"], $datos["tipo"]))
                {
                   $controlador = new EmpleadoController();
                   if($controlador->buscarEmpleadoPorUsuario($datos["usuario"]) == false)
                   {
                       $retornoInsertar = $controlador->insertarEmpleado($datos["nombre"], $datos["apellido"], $datos["usuario"], $datos["clave"], "activo", $datos["tipo"]);
                       if($retornoInsertar != -1)
                       {
                           echo json_encode(array("OK" => "Se insertó el empleado con éxito con el ID: {$retornoInsertar}"));
                       }
                       else
                       {
                            echo json_encode(array("ERROR" => "No pueden haber mas de 3 socios"));
                       }
                   }
                   else
                   {
                        echo json_encode(array("ERROR" => "El nombre de usuario ya esta en uso"));
                   }
                }
                else
                {
                    echo json_encode(array("ERROR" => "Datos invalidos. Nombre, apellido, usuario y clave deben estar compuestos de 1 a 50 caracteres. El tipo debe ser socio, bartender, cervecero, cocinero o mozo"));
                }
    
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser socio para registrar un empleado"));
            }
            
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para el registro del empleado. Se requiere nombre, apellido, usuario, clave y tipo"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function loginEmpleado($datos)
{
    $retorno = false;
    if(isset($datos["usuarioLogin"]) && isset($datos["claveLogin"]))
    {
        
           $controlador = new EmpleadoController();
            $empleadoRetornado = $controlador->buscarEmpleadoPorUsuario($datos["usuarioLogin"]);
           if($empleadoRetornado != false)
           {
               if($controlador->buscarEmpleadoPorUsuarioClave($datos["usuarioLogin"], $datos["claveLogin"]) == false)
               {
                    
                   echo json_encode(array("ERROR" => "Clave incorrecta"));
               }
               else
               {   
                   echo json_encode(array("OK" => "Sesion iniciada"));
                   $retorno = $empleadoRetornado->tipo;
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


function bajaEmpleado($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["id"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new EmpleadoController();
                $empleadoRetornado = $controlador->buscarEmpleadoPorId($datos["id"]);
                if($empleadoRetornado == false)
                {
                    echo json_encode(array("ERROR" => "El empleado no existe o ya esta dado de baja"));
                }
                else
                {
                    $controlador->bajaEmpleado($datos["id"]);
                     echo json_encode(array("OK" => "Empleado borrado con exito (baja logica)"));
                }
    
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser socio para dar de baja un empleado"));
            }
    
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para la baja del empleado. Se requiere id"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}


function modificarEmpleado($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["id"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
    
                $controlador = new EmpleadoController();
                $empleadoRetornado = $controlador->buscarEmpleadoPorId($datos["id"]);
                if($empleadoRetornado == false)
                {
                     echo json_encode(array("ERROR" => "El empleado no existe"));
                }
                else
                {
                     $nombreModificado = $empleadoRetornado->nombre;
                     $apellidoModificado = $empleadoRetornado->apellido;
                     $usuarioModificado = $empleadoRetornado->usuario;
                     $claveModificada = $empleadoRetornado->clave;
                     $tipoModificado = $empleadoRetornado->tipo;
                     $flagModificacion = false;
                     if(isset($datos["nombre"]) && $datos["nombre"] != $nombreModificado)
                     {
                         $nombreModificado = $datos["nombre"];
                         $flagModificacion = true;
                     }
                     if(isset($datos["apellido"]) && $datos["apellido"] != $apellidoModificado)
                     {
                         $apellidoModificado = $datos["apellido"];
                         $flagModificacion = true;
                     }
                     if(isset($datos["usuario"]) && $datos["usuario"] != $usuarioModificado)
                     {
                         $existeEmpleado = $controlador->buscarEmpleadoPorUsuario($datos["usuario"]);
                         if($existeEmpleado == false)
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
                     if(isset($datos["tipo"]) && $datos["tipo"] != $tipoModificado)
                     {
                         if($datos["tipo"] == "socio" || $datos["tipo"] == "bartender" || $datos["tipo"] == "cervecero" || $datos["tipo"] == "cocinero" || $datos["tipo"] == "mozo")
                         {
                             $tipoModificado = $datos["tipo"];
                             $flagModificacion = true;
                         }
                         else
                         {
                             echo json_encode(array("ERROR" => "El tipo es invalido. Se espera socio, bartender, cervecero, cocinero o mozo"));
                         }
                     }
                     if($flagModificacion)
                     {
                         $controlador->modificarEmpleado($empleadoRetornado->id, $nombreModificado, $apellidoModificado, $usuarioModificado, $claveModificada, $empleadoRetornado->estado, $tipoModificado);      
                         echo json_encode(array("OK" => "Empleado modificado con exito"));
                     }
                     else
                     {
                         echo json_encode(array("ADVERTENCIA" => "No se realizo ninguna modificacion"));
                     }
                }
               
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser socio para modificar un empleado"));
            }
    
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para la modificacion del empleado. Se requiere id para identificar al usuario a modificar"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function listadoEmpleado($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["parametro"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controladorEmpleado = new EmpleadoController();
                if($datos["parametro"] == "uno")
                {
                    if(isset($datos["id"]))
                    {
                        $empleadoRetornado = $controladorEmpleado->buscarEmpleadoPorId($datos["id"]);
                        if($empleadoRetornado == false)
                        {
                            echo json_encode(array("ERROR" => "No existe un empleado con ese ID"));
                        }
                        else
                        {
                            echo $empleadoRetornado->mostrarDatos();
                        }
                    }
                    else
                    {
                        echo json_encode(array("ERROR" => "Para listar un empleado se necesita el ID"));
                    }
                }
                else if($datos["parametro"] == "todos")
                {
                    $empleadosRetornados = $controladorEmpleado->listarEmpleados();
                    foreach($empleadosRetornados as $empleado)
                    {
                        echo $empleado->mostrarDatos() . "\n";
                    }
                }
                else
                {
                    echo json_encode(array("ERROR" => "El 'parametro' debe ser 'uno' o 'todos'"));
        
                }
    
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser socio para listar empleados"));
            }
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para el listado de empleado. Se requiere parametro para listar uno o todos"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}
?>