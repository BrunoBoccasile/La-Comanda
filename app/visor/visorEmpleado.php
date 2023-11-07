<?php
require_once __DIR__ . "/../controladores/empleadoController.php";

function registroEmpleado($datos)
{
    if( isset($datos["nombre"]) && isset($datos["apellido"]) && isset($datos["usuario"]) && isset($datos["clave"]) && isset($datos["tipo"]) )
    {
        if(Empleado::ValidarDatos($datos["nombre"], $datos["apellido"], $datos["usuario"], $datos["clave"], $datos["tipo"]))
        {
           $controlador = new EmpleadoController();
           if($controlador->buscarEmpleadoPorUsuario($datos["usuario"]) == false)
           {
               $retornoInsertar = $controlador->insertarEmpleado($datos["nombre"], $datos["apellido"], $datos["usuario"], $datos["clave"], "activo", $datos["tipo"]);
               echo "Se insertó el empleado con éxito con el ID: " . $retornoInsertar; 
           }
           else
           {
                echo "Error: El nombre de usuario ya esta en uso.\n";
           }
        }
        else
        {
            echo "Error: Datos invalidos.\n";
            echo "Nombre, apellido, usuario y clave deben estar compuestos de 1 a 50 caracteres. El tipo debe ser socio, bartender, cervecero, cocinero o mozo.";
        }
    }
    else
    {
        echo "Error: faltan datos necesarios para el registro del empleado.\n";
        echo "Se requiere nombre, apellido, usuario, clave y tipo";
    }
}

function loginEmpleado($datos)
{
    session_set_cookie_params(3600*12); //12 horas
    session_start();
    if(isset($datos["usuario"]) && isset($datos["clave"]))
    {
        
           $controlador = new EmpleadoController();

           if($controlador->buscarEmpleadoPorUsuario($datos["usuario"]) != false)
           {
               if($controlador->buscarEmpleadoPorUsuarioClave($datos["usuario"], $datos["clave"]) == false)
               {
                   echo "Error: Clave incorrecta.\n";
               }
               else
               {
               
                    if(isset($_SESSION["usuario"]) && $_SESSION["usuario"] == $datos["usuario"])
                    {
                        echo "La sesion ya se encuentra iniciada.\n";
                    }
                    else
                    {
                        $_SESSION["usuario"] = $datos["usuario"];
                        echo "Sesion iniciada.\n";
                    }
               }

           }
           else
           {
                echo "Error: Usuario inexistente.\n";
           }
        
    }
    else
    {
        echo "Error: faltan datos necesarios para el login.\n";
        echo "Se requiere usuario y clave";
    }
}

function bajaEmpleado($datos)
{
    if(isset($datos["id"]))
    {

           $controlador = new EmpleadoController();
           $empleadoRetornado = $controlador->buscarEmpleadoPorId($datos["id"]);
           if($empleadoRetornado == false)
           {
               echo "Error: El empleado no existe o ya esta dado de baja.\n"; 
           }
           else
           {
                $controlador->modificarEmpleado($empleadoRetornado->id, $empleadoRetornado->nombre, $empleadoRetornado->apellido, $empleadoRetornado->usuario, $empleadoRetornado->clave, "borrado", $empleadoRetornado->tipo);
                echo "Empleado borrado con exito (baja logica).\n";
           }

    }
    else
    {
        echo "Error: faltan datos necesarios para la baja del empleado.\n";
        echo "Se requiere id";
    }
}

function modificarEmpleado($datos)
{
    if(isset($datos["id"]))
    {

           $controlador = new EmpleadoController();
           $empleadoRetornado = $controlador->buscarEmpleadoPorId($datos["id"]);
           if($empleadoRetornado == false)
           {
               echo "Error: El empleado no existe.\n"; 
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
                        echo "El nombre de usuario ya esta en uso, se deja el original.\n";
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
                        echo "El tipo es invalido. Se espera socio, bartender, cervecero, cocinero o mozo.\n";
                    }
                }
                if($flagModificacion)
                {
                    $controlador->modificarEmpleado($empleadoRetornado->id, $nombreModificado, $apellidoModificado, $usuarioModificado, $claveModificada, $empleadoRetornado->estado, $tipoModificado);      
                    echo "Empleado modificado con exito.\n";
                }
                else
                {
                    echo "No se realizo ninguna modificacion.\n";
                }
           }

    }
    else
    {
        echo "Error: faltan datos necesarios para la modificacion del empleado.\n";
        echo "Se requiere id para identificar al usuario a modificar";
    }
}

function listadoEmpleado($datos)
{
    if(isset($datos["parametro"]))
    {
        $controladorEmpleado = new EmpleadoController();
        if($datos["parametro"] == "uno")
        {
            if(isset($datos["id"]))
            {
                $empleadoRetornado = $controladorEmpleado->buscarEmpleadoPorId($datos["id"]);
                if($empleadoRetornado == false)
                {
                    echo "Error: No existe un empleado con ese ID.\n";
                }
                else
                {
                    echo $empleadoRetornado->mostrarDatos();
                }
            }
            else
            {
                echo "Error: Para listar un empleado se necesita el ID.\n";
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
            echo "Error: El 'parametro' debe ser 'uno' o 'todos'.\n";
        }
    }
    else
    {
        echo "Error: faltan datos necesarios para el listado de empleado.\n";
        echo "Se requiere parametro para listar uno o todos.\n";
    }
}
?>