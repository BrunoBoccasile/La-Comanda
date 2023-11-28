<?php
require_once __DIR__ . "/../controladores/empleadoController.php";

class VisorEmpleado
{
    static function registroEmpleado($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                if(Empleado::ValidarDatos($datos["nombre"], $datos["apellido"], $datos["usuario"], $datos["clave"], $datos["tipo"]))
                {
                   $controlador = new EmpleadoController();
                   if($controlador->buscarEmpleadoPorUsuario($datos["usuario"]) == false)
                   {
                        $retornoInsertar = $controlador->insertarEmpleado($datos["nombre"], $datos["apellido"], $datos["usuario"], password_hash($datos["clave"], PASSWORD_DEFAULT), "activo", $datos["tipo"]);
                        if($retornoInsertar != -1)
                        {
                            $arrayRespuesta["status"] = "OK";
                            $arrayRespuesta["message"] = "Se insertó el empleado con éxito con el ID: {$retornoInsertar}";
                        }
                        else
                        {
                            $arrayRespuesta["status"] = "ERROR";
                            $arrayRespuesta["message"] = "No pueden haber mas de 3 socios";
                        }
                   }
                   else
                   {
                        $arrayRespuesta["status"] = "ERROR";
                        $arrayRespuesta["message"] = "El nombre de usuario ya esta en uso";
                   }
                }
                else
                { 
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "Datos invalidos. Nombre, apellido, usuario y clave deben estar compuestos de 1 a 50 caracteres. El tipo debe ser socio, bartender, cervecero, cocinero o mozo";
                }
    
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser socio para registrar un empleado";
            }
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function loginEmpleado($datos)
    {
        $arrayRespuesta = array("status" => "", "message" => "");
        $controlador = new EmpleadoController();
         $empleadoRetornado = $controlador->buscarEmpleadoPorUsuario($datos["usuarioLogin"]);
        if($empleadoRetornado != false)
        {
            $hashRetornado = $controlador->obtenerClavePorUsuario($datos["usuarioLogin"]);

            if(password_verify($datos["claveLogin"], $hashRetornado))
            {
                $arrayRespuesta["status"] = "OK";
                $arrayRespuesta["message"] = "Sesion iniciada";
                $arrayRespuesta["tipoEmpleado"] = $empleadoRetornado->tipo; ;
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
    
    
    static function bajaEmpleado($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new EmpleadoController();
                $empleadoRetornado = $controlador->buscarEmpleadoPorId($datos["id"]);
                if($empleadoRetornado == false)
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "El empleado no existe o ya esta dado de baja";
                }
                else
                {
                    $controlador->bajaEmpleado($datos["id"]);
                    $arrayRespuesta["status"] = "OK";
                    $arrayRespuesta["message"] = "Empleado borrado con exito (baja logica)";
                }
    
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser socio para dar de baja un empleado";
            } 
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    
    static function modificarEmpleado($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
    
                $controlador = new EmpleadoController();
                $empleadoRetornado = $controlador->buscarEmpleadoPorId($datos["id"]);
                if($empleadoRetornado == false)
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "El empleado no existe";
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
                            $arrayRespuesta["status"] = "WARNING";
                            $arrayRespuesta["message"] = "El nombre de usuario ya esta en uso, se deja el original";
                        }
                    }
                    if(isset($datos["clave"]) && !password_verify($datos["clave"], $claveModificada))
                    {
                        $claveModificada = password_hash($datos["clave"], PASSWORD_DEFAULT);
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
                            $arrayRespuesta["status"] = "ERROR";
                            $arrayRespuesta["message"] = "El tipo es invalido. Se espera socio, bartender, cervecero, cocinero o mozo";
                        }
                    }
                    if($flagModificacion)
                    {
                        $controlador->modificarEmpleado($empleadoRetornado->id, $nombreModificado, $apellidoModificado, $usuarioModificado, $claveModificada, $empleadoRetornado->estado, $tipoModificado);      
                        $arrayRespuesta["status"] = "OK";
                        $arrayRespuesta["message"] = "Empleado modificado con exito";
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
                $arrayRespuesta["message"] = "Debe ser socio para modificar un empleado";
            }

        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function listadoEmpleado($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
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
                                $arrayRespuesta["status"] = "ERROR";
                                $arrayRespuesta["message"] = "No existe un empleado con ese ID";
                            }
                            else
                            {
                                $arrayRespuesta["status"] = "OK";
                                $arrayRespuesta["message"] = "Listado realizado con exito";
                                $arrayRespuesta["listado"] = $empleadoRetornado->mostrarDatos();
                            }
                        }
                        else
                        {
                            $arrayRespuesta["status"] = "ERROR";
                            $arrayRespuesta["message"] = "Para listar un empleado se necesita el ID";
                        }
                    }
                    else if($datos["parametro"] == "todos")
                    {
                        $empleadosRetornados = $controladorEmpleado->listarEmpleados();
                        $arrayListado = array();
                        foreach($empleadosRetornados as $empleado)
                        {
                            array_push($arrayListado, $empleado->mostrarDatos());
                        }
                        $arrayRespuesta["status"] = "OK";
                        $arrayRespuesta["message"] = "Listado realizado con exito";
                        $arrayRespuesta["listado"] = $arrayListado;
                    }
                    else
                    {
                        $arrayRespuesta["status"] = "ERROR";
                        $arrayRespuesta["message"] = "El 'parametro' debe ser 'uno' o 'todos'";
            
                    }
        
                }
                else
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "Debe ser socio para listar empleados";
                }
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
}
?>