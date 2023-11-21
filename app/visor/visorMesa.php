<?php
require_once __DIR__ . "/../controladores/mesaController.php";
require_once __DIR__ . "/../controladores/comandaController.php";

function altaMesa($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["estado"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "mozo" || $datos["datos"]["tipoEmpleado"] == "socio")
            {
                if(Mesa::ValidarDatos($datos["estado"]))
                {
                    $controlador = new MesaController();
                    $retornoInsertar = $controlador->insertarMesa($datos["estado"]);
                    echo json_encode(array("OK" => "Se insertó la mesa con éxito con el ID: {$retornoInsertar}"));
                }
                else
                {
                    echo json_encode(array("ERROR" => "Datos invalidos. El estado deberia ser 'con cliente esperando pedido', 'con cliente comiendo', 'con cliente pagando' o 'disponible'"));
                }
    
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser mozo o socio para dar de alta una mesa"));
            }
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para el alta de la mesa. Se requiere estado"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function cierreMesa($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["id"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new MesaController();
                $mesaRetornada = $controlador->buscarMesaPorId($datos["id"]);
                if($mesaRetornada == false)
                {
                     echo json_encode(array("ERROR" => "La mesa no existe o ya esta cerrada"));
                }
                else
                {
                    $controlador->modificarMesa($mesaRetornada->id, "cerrada");
                    echo json_encode(array("OK" => "Mesa cerrada con exito"));
                }
    
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser socio para cerrar una mesa"));
            }
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para el cierre de la mesa. Se requiere id"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function modificarMesa($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["id"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "mozo" || $datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new MesaController();
                $mesaRetornada = $controlador->buscarMesaPorId($datos["id"]);
                if($mesaRetornada == false)
                {
                    echo json_encode(array("ERROR" => "La mesa no existe"));
                }
                   else
                   {
                        $estadoModificado = $mesaRetornada->estado;
                        $flagModificacion = false;
                        if(isset($datos["estado"]) && $datos["estado"] != $estadoModificado)
                        {
                            if($datos["estado"] == "con cliente esperando pedido" || $datos["estado"] == "con cliente comiendo" || $datos["estado"] == "con cliente pagando" || $datos["estado"] == "disponible")
                            {
                                $estadoModificado = $datos["estado"];
                                $flagModificacion = true;
                            }
                            else
                            {
                                echo json_encode(array("ERROR" => "El estado deberia ser 'con cliente esperando pedido', 'con cliente comiendo', 'con cliente pagando' o 'disponible'"));
                                echo "\n";
                            }
                        }
                        if($flagModificacion)
                        {
                            $controlador->modificarMesa($mesaRetornada->id, $estadoModificado);  
                            echo json_encode(array("OK" => "Mesa modificada con exito"));
                        }
                        else
                        {
                            echo json_encode(array("ADVERTENCIA" => "No se realizo ninguna modificacion"));
                        }
                   }
    
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser mozo o socio para modificar una mesa"));
            }
            
    
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para la modificacion de la mesa. Se requiere id para identificar la mesa a modificar"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function listadoMesa($datos)
{
        if(isset($datos["parametro"]))
        {
            $controladorMesa = new MesaController();
            if($datos["parametro"] == "una")
            {
                if(isset($datos["id"]))
                {
                    $mesaRetornada = $controladorMesa->buscarMesaPorId($datos["id"]);
                    if($mesaRetornada == false)
                    {
                        echo json_encode(array("ERROR" => "No existe una mesa con ese ID"));
                    }
                    else
                    {
                        echo $mesaRetornada->mostrarDatos();
                    }
                }
                else
                {
                    echo json_encode(array("ERROR" => "Para listar una mesa se necesita el ID"));
                }
            }
            else if($datos["parametro"] == "todas")
            {
                $mesasRetornadas = $controladorMesa->listarMesas();
                foreach($mesasRetornadas as $mesa)
                {
                    echo $mesa->mostrarDatos() . "\n";
                }
            }
            else
            {
                echo json_encode(array("ERROR" => "El 'parametro' debe ser 'una' o 'todas'"));
            }
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para el listado de las mesas. Se requiere parametro para listar una o todas"));
        }
        echo "\n";
    
}

function listarMesaMasUsada($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if($datos["datos"]["tipoEmpleado"] == "socio")
        {
            $controladorComanda = new ComandaController();
    
            $comandasRetornadas = $controladorComanda->listarComandasHistoricas();
            $arrayMesasUsadas = array();
            foreach($comandasRetornadas as $comanda)
            {
                array_push($arrayMesasUsadas, $comanda->idMesa);
            }
            $conteoMesas = array_count_values($arrayMesasUsadas);
            $mesaMasUsada = array_search(max($conteoMesas), $conteoMesas);
            echo json_encode(array("OK" => "La mesa mas usada es la mesa " . $mesaMasUsada . ", con un historial de ". $conteoMesas[$mesaMasUsada] ." pedidos."));
        }
        else
        {
            echo json_encode(array("ERROR" => "Debe ser socio para listar la mesa mas usada"));
        }
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function compararPorUsos($a, $b) {
    $sumaA = $a->puntosRestaurante + $a->puntosCocinero + $a->puntosMozo + $a->puntosMesa;
    $sumaB = $b->puntosRestaurante + $b->puntosCocinero + $b->puntosMozo + $b->puntosMesa;
    return $sumaB - $sumaA;
}
?>