<?php
require_once __DIR__ . "/../controladores/mesaController.php";
require_once __DIR__ . "/../controladores/comandaController.php";

class VisorMesa
{
    static function altaMesa($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "mozo" || $datos["datos"]["tipoEmpleado"] == "socio")
            {
                if(Mesa::ValidarDatos($datos["estado"]))
                {
                    $controlador = new MesaController();
                    $retornoInsertar = $controlador->insertarMesa($datos["estado"]);
                    $arrayRespuesta["status"] = "OK";
                    $arrayRespuesta["message"] = "Se insertó la mesa con éxito con el ID: {$retornoInsertar}";
                }
                else
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "Datos invalidos. El estado deberia ser 'con cliente esperando pedido', 'con cliente comiendo', 'con cliente pagando' o 'disponible'";
                }
    
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser mozo o socio para dar de alta una mesa";
            }
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function cierreMesa($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new MesaController();
                $mesaRetornada = $controlador->buscarMesaPorId($datos["id"]);
                if($mesaRetornada == false)
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "La mesa no existe o ya esta cerrada";
                }
                else
                {
                    $controlador->modificarMesa($mesaRetornada->id, "cerrada");
                    $arrayRespuesta["status"] = "OK";
                    $arrayRespuesta["message"] = "Mesa cerrada con exito";
                }
    
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser socio para cerrar una mesa";
            }
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function modificarMesa($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "mozo" || $datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new MesaController();
                $mesaRetornada = $controlador->buscarMesaPorId($datos["id"]);
                if($mesaRetornada == false)
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "La mesa no existe";
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
                                $arrayRespuesta["status"] = "ERROR";
                                $arrayRespuesta["message"] = "El estado deberia ser 'con cliente esperando pedido', 'con cliente comiendo', 'con cliente pagando' o 'disponible'";
                            }
                        }
                        if($flagModificacion)
                        {
                            $controlador->modificarMesa($mesaRetornada->id, $estadoModificado);  
                            $arrayRespuesta["status"] = "OK";
                            $arrayRespuesta["message"] = "Mesa modificada con exito";
                        }
                        else
                        {
                            $arrayRespuesta["status"] = "ERROR";
                            $arrayRespuesta["message"] = "No se realizo ninguna modificacion";
                        }
                   }
    
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser mozo o socio para modificar una mesa";
            }
            
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function listadoMesa($datos)
    {
        
        $controladorMesa = new MesaController();
        if($datos["parametro"] == "una")
        {
            if(isset($datos["id"]))
            {
                $mesaRetornada = $controladorMesa->buscarMesaPorId($datos["id"]);
                if($mesaRetornada == false)
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "No existe una mesa con ese ID";
                }
                else
                {
                    $arrayRespuesta["status"] = "OK";
                    $arrayRespuesta["message"] = "Listado realizado con exito";
                    $arrayRespuesta["listado"] = $mesaRetornada->mostrarDatos();
                }
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Para listar una mesa se necesita el ID";
            }
        }
        else if($datos["parametro"] == "todas")
        {
            $mesasRetornadas = $controladorMesa->listarMesas();
            $arrayListado = array();
            foreach($mesasRetornadas as $mesa)
            {
                array_push($arrayListado, $mesa->mostrarDatos());
            }
            $arrayRespuesta["status"] = "OK";
            $arrayRespuesta["message"] = "Listado realizado con exito";
            $arrayRespuesta["listado"] = $arrayListado;
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El 'parametro' debe ser 'una' o 'todas'";
        }
            return $arrayRespuesta;
    }
    
    static function listarMesaMasUsada($datos)
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
                $arrayRespuesta["status"] = "OK";
                $arrayRespuesta["message"] = "La mesa mas usada es la mesa " . $mesaMasUsada . ", con un historial de ". $conteoMesas[$mesaMasUsada] ." pedidos.";
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser socio para listar la mesa mas usada";
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
function compararPorUsos($a, $b) {
    $sumaA = $a->puntosRestaurante + $a->puntosCocinero + $a->puntosMozo + $a->puntosMesa;
    $sumaB = $b->puntosRestaurante + $b->puntosCocinero + $b->puntosMozo + $b->puntosMesa;
    return $sumaB - $sumaA;
}
?>