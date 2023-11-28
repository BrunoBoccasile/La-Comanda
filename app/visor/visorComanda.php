<?php
require_once __DIR__ . "/../controladores/comandaController.php";
require_once __DIR__ . "/../controladores/mesaController.php";
require_once __DIR__ . "/../controladores/productoController.php";

class VisorComanda
{
    
    static function altaComanda($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio" || $datos["datos"]["tipoEmpleado"] == "mozo")
            {
                $retornoValidacion = Comanda::ValidarDatos($datos["detalle"]);
                if($retornoValidacion == 1)
                {
                    $controlador = new ComandaController();
                    $retornoInsertar = $controlador->insertarComanda(strtolower($datos["detalle"]), $datos["idMesa"], $datos["idCliente"]);
                    if($retornoInsertar == 0)
                    {
                        $arrayRespuesta["status"] = "ERROR";
                        $arrayRespuesta["message"] = "El ID de la mesa no corresponde a una mesa existente";
                    }
                    else if($retornoInsertar == -1)
                    {
                        $arrayRespuesta["status"] = "ERROR";
                        $arrayRespuesta["message"] = "El ID del cliente no corresponde a un cliente existente";
                    }
                    else
                    {
                        $arrayRespuesta["status"] = "OK";
                        $arrayRespuesta["message"] = "Se insertó la comanda con éxito con el ID: {$retornoInsertar}";
                    }
                }
                else
                {
                    $arrayRespuesta["status"] = "ERROR";
                    switch($retornoValidacion)
                    {
                        case false:
                            $arrayRespuesta["message"] = "El detalle debe estar compuesto por nombres de productos existentes separados por coma. Por ejemplo: 'hamburguesa,quilmes'";
                            break;
                        case -1:
                            $arrayRespuesta["message"] = "No se pueden pedir tragos porque no hay bartender";
                            break;
                        case -2:
                            $arrayRespuesta["message"] = "No se pueden pedir cervezas porque no hay cervecero";
                            break;
                        case -3:
                            $arrayRespuesta["message"] = "No se pueden pedir comidas ni postres porque no hay cocinero";
                            break;
                    }
                }
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser mozo o socio para dar de alta una comanda";
            }
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function altaFotoComanda($datos, $archivo)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if(isset($archivo["foto"]))
            {
                if($datos["datos"]["tipoEmpleado"] == "socio" || $datos["datos"]["tipoEmpleado"] == "mozo")
                {
                    $controlador = new ComandaController();
                    $archivo = $archivo["foto"];
                    $tamanoArchivo = $archivo->getSize();
                    $tipoArchivo = $archivo->getClientMediaType();
                    $comandaRetornada = $controlador->buscarComandaPorId($datos["id"]);
                    if($comandaRetornada != false)
                    {
                        $nombreArchivo = $comandaRetornada->idMesa . "-" . $datos["id"];
                        $rutaDestino = __DIR__ . "/../fotos/" . $nombreArchivo;
                    
                        if( !((strpos($tipoArchivo, "png") || strpos($tipoArchivo, "jpeg")) && ($tamanoArchivo < 5242880 )))
                        {
                            $arrayRespuesta["status"] = "ERROR";
                            $arrayRespuesta["message"] = "La extensión o el tamaño del archivo no es correcta";
                        }
                        else
                        {
                            if(strpos($tipoArchivo, "png"))
                            {
                                $rutaDestino .= ".png";
                            }
                            else if(strpos($tipoArchivo, "jpeg"))
                            {
                                $rutaDestino .= ".jpg";
                            }
                    
                            $archivo->moveTo($rutaDestino);
                            $arrayRespuesta["status"] = "OK";
                            $arrayRespuesta["message"] = "Foto subida con exito";
                        }
                    }
                    else
                    {
                        $arrayRespuesta["status"] = "ERROR";
                        $arrayRespuesta["message"] = "No existe una comanda con el id ingresado";
                    }
                }
                else
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "Debe ser mozo o socio para subir una foto de la comanda";  
                }

            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe subir una foto";  
            }
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }

    static function bajaComanda($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] = "mozo" || $datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new ComandaController();
                $comandaRetornada = $controlador->buscarComandaPorId($datos["id"]);
                if($comandaRetornada == false)
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "La comanda no existe o ya esta concluida";
                }
                else
                {
                    $retornoBaja = $controlador->bajaComanda($datos["id"]);
                    if($retornoBaja)
                    {
                        $controladorMesa = new MesaController();
                        //chequear que todas las comandas de una mesa esten concluidas antes de cambiar el estado de la mesa a disponible
                        if(count($controlador->listarComandasPorMesa($comandaRetornada->idMesa)) == 0)
                        {
                           $controladorMesa->modificarMesa($comandaRetornada->idMesa, "disponible");
                        }
                        $arrayRespuesta["status"] = "OK";
                        $arrayRespuesta["message"] = "Comanda concluida con exito";
                    }
                    else
                    {
                       $arrayRespuesta["status"] = "ERROR";
                       $arrayRespuesta["message"] = "No se pudo concluir la comanda";
                    }
                }
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser mozo o socio para dar de baja una comanda";
            }
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function modificarComanda($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] = "mozo" || $datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new ComandaController();
               $comandaRetornada = $controlador->buscarComandaPorId($datos["id"]);
               if($comandaRetornada == false)
               {        
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "La comanda no existe";
               }
               else
               {
                    $idMesaModificado = $comandaRetornada->idMesa;
                    $flagModificacion = false;
                    if(isset($datos["detalle"]) && $datos["detalle"] != $comandaRetornada->detalle)
                    {
                        $retornoValidacion = Comanda::ValidarDatos($datos["detalle"]);
                        if($retornoValidacion == 1)
                        {
                            $detalleModificado = $datos["detalle"];
                            $flagModificacion = true;
                        }
                        else
                        {
                            $arrayRespuesta["status"] = "ERROR";
                            switch($retornoValidacion)
                            {
                                case false:
                                    $arrayRespuesta["message"] = "El detalle debe estar compuesto por un producto existente";
                                case -1:
                                    $arrayRespuesta["message"] = "No se pueden pedir tragos porque no hay bartender";
                                    break;
                                case -2:
                                    $arrayRespuesta["message"] = "No se pueden pedir cervezas porque no hay cervecero";
                                    break;
                                case -3:
                                    $arrayRespuesta["message"] = "No se pueden pedir comidas ni postres porque no hay cocinero";
                                    break;
                            }
                        }
                    }
                    if(isset($datos["idMesa"]) && $datos["idMesa"] != $idMesaModificado)
                    {
                        $idMesaModificado = $datos["idMesa"];
                        $flagModificacion = true;
                    }
                    if($flagModificacion)
                    {
                        $arrayProductosOriginal = explode(",", $comandaRetornada->detalle);
                        $arrayProductosModificar = explode(",", $datos["detalle"]);
                        if(count($arrayProductosOriginal) >= count($arrayProductosModificar))
                        {
                            $arrayProductosNuevo = array();
                            for($i=0 ; $i<count($arrayProductosOriginal) ; $i++)
                            {
                                if(isset($arrayProductosModificar[$i]))
                                {
                                    array_push($arrayProductosNuevo, $arrayProductosModificar[$i]);
                                }
                                else
                                {
                                    array_push($arrayProductosNuevo, $arrayProductosOriginal[$i]);
                                }
                            }
                            
                            $flagPrimero = false;
                            $stringProductosNuevo = "";
                            foreach($arrayProductosNuevo as $producto)
                            {
                                if(!$flagPrimero)
                                {
                                    $stringProductosNuevo = $producto;
                                    $flagPrimero = true;
                                }
                                else
                                {
                                    $stringProductosNuevo .= "," . $producto;
                                }
                            }
    
    
    
                            $retornoModificacion = $controlador->modificarComanda($datos["id"], $stringProductosNuevo, $idMesaModificado, $comandaRetornada->estado);      
                            if($retornoModificacion == false)
                            {
                                $arrayRespuesta["status"] = "ERROR";
                                $arrayRespuesta["message"] = "El id de la mesa no corresponde a una mesa existente";
                            }
                            else
                            {
                                $arrayRespuesta["status"] = "OK";
                                $arrayRespuesta["message"] = "Comanda modificada con exito";
                            }
                        }
                        else
                        {
                            $arrayRespuesta["status"] = "ERROR";
                            $arrayRespuesta["message"] = "No puede agregar productos a la comanda, solo modificar";
                        }
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
                $arrayRespuesta["message"] = "Debe ser mozo o socio para modificar una comanda";
            }

                  

        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function listadoComanda($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            $controladorComanda = new ComandaController();
            if($datos["parametro"] == "una")
            {
                if(isset($datos["id"]))
                {
                    $comandaRetornada = $controladorComanda->buscarComandaPorId($datos["id"]);
                    
                    if($comandaRetornada == false)
                    {
                        $arrayRespuesta["status"] = "ERROR";
                        $arrayRespuesta["message"] = "No existe una comanda con ese ID";
                    }
                    else
                    {
                        $arrayDetalle = explode(",", $comandaRetornada->detalle);
                        $arrayEstado = explode(",", $comandaRetornada->detalle);
                        $arrayIdProducto = explode(",", $comandaRetornada->idProducto);
                        $arrayTiempos = explode(",", $comandaRetornada->tiempoEstimadoFinalizacion);
                        $controladorProducto = new ProductoController();
    
                        $i = 0;
                        $arrayListado = array();
                        foreach($arrayDetalle as $producto)
                        {
                            $tipoProducto = ($controladorProducto->buscarProductoPorNombre($producto))->tipo;
        
                            switch($datos["datos"]["tipoEmpleado"])
                            {
                                case "cocinero":
                                    if($tipoProducto == "comida" || $tipoProducto == "postre")
                                    {
                                        $arrayRespuesta["status"] = "OK";
                                        $arrayRespuesta["message"] = "Listado realizado con exito";
                                        array_push($arrayListado, $controladorComanda->buscarProductoComanda($arrayIdProducto[$i])->mostrarProductoComanda());
                                    }
                                    else
                                    {
                                        $arrayRespuesta["status"] = "WARNING";
                                        $arrayRespuesta["message"] = "Un cocinero solo puede listar comidas y postres";
                                    }
                                break;
                                case "bartender":
                                    if($tipoProducto == "trago")
                                    {
                                        $arrayRespuesta["status"] = "OK";
                                        $arrayRespuesta["message"] = "Listado realizado con exito";
                                        array_push($arrayListado, $controladorComanda->buscarProductoComanda($arrayIdProducto[$i])->mostrarProductoComanda());
                                    }
                                    else
                                    {
                                        $arrayRespuesta["status"] = "WARNING";
                                        $arrayRespuesta["message"] = "Un bartender solo puede listar tragos";
        
                                    }
                                break;
                                case "cervecero":
                                    if($tipoProducto == "cerveza")
                                    {
                                        $arrayRespuesta["status"] = "OK";
                                        $arrayRespuesta["message"] = "Listado realizado con exito";
                                        array_push($arrayListado, $controladorComanda->buscarProductoComanda($arrayIdProducto[$i])->mostrarProductoComanda());
                                    }
                                    else
                                    {
                                        $arrayRespuesta["status"] = "WARNING";
                                        $arrayRespuesta["message"] = "Un cervecero solo puede listar cervezas";
                                    }
                                break;
                                case "mozo":
                                case "socio":
                                    $arrayRespuesta["status"] = "OK";
                                    $arrayRespuesta["message"] = "Listado realizado con exito";
                                    array_push($arrayListado, $controladorComanda->buscarProductoComanda($arrayIdProducto[$i])->mostrarProductoComanda());
                                break;
                            }
                            $i++;
                        }  
                        $arrayRespuesta["listado"] = $arrayListado;
                    }
                }
                else
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "Para listar una comanda se necesita el ID";
                }
            }
            else if($datos["parametro"] == "todas")
            {
                if(isset($datos["estado"]))
                {
                    if($datos["estado"] == "pendiente" || $datos["estado"] == "en preparacion" || $datos["estado"] == "listo para servir")
                    {
                        switch($datos["datos"]["tipoEmpleado"])
                        {
                            case "cocinero":
                                $comandasRetornadas = $controladorComanda->listarProductosComandaPorTipoYEstado("comida", $datos["estado"]);
                                $postresRetornados = $controladorComanda->listarProductosComandaPorTipoYEstado("postre", $datos["estado"]);
                                foreach($postresRetornados as $postre)
                                {
                                    array_push($comandasRetornadas, $postre);
                                }
                            break;
                            case "bartender":
                                $comandasRetornadas =  $controladorComanda->listarProductosComandaPorTipoYEstado("trago", $datos["estado"]);
                            break;
                            case "cervecero":
                                $comandasRetornadas =  $controladorComanda->listarProductosComandaPorTipoYEstado("cerveza", $datos["estado"]);
                            break;
                            case "mozo":
                            case "socio":
                                $comandasRetornadas =  $controladorComanda->listarProductosComandaPorEstado($datos["estado"]);
                            break;
                        }
        
                        if($comandasRetornadas)
                        {
                            $arrayListado = array();
                            foreach($comandasRetornadas as $comanda)
                            {
                                array_push($arrayListado, $comanda->mostrarProductoComanda());
                            }
                            $arrayRespuesta["status"] = "OK";
                            $arrayRespuesta["message"] = "Listado realizado con exito";
                            $arrayRespuesta["listado"] = $arrayListado;

                        }
                        else
                        {
                            $arrayRespuesta["status"] = "ADVERTENCIA";
                            $arrayRespuesta["message"] = "No hay ninguna comanda del tipo y estado correspondiente";
                        }
                    }
                    else
                    {
                        $arrayRespuesta["status"] = "ERROR";
                        $arrayRespuesta["message"] = "El parametro estado debe ser 'pendiente', 'en preparacion' o 'listo para servir'";
                    }
                }
                else
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "Si va a listar todas, debe ingresar el estado de las comandas a listar";
                }
                
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "El 'parametro' debe ser 'una' o 'todas'";
            }
            

        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function preparacionComanda($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            $controladorComanda = new ComandaController();
            //chequear si el idproducto existe
            $productoComandaRetornado = $controladorComanda->buscarProductoComanda($datos["idProducto"]);
            if($productoComandaRetornado)
            {
                if($datos["estado"] == "en preparacion" || $datos["estado"] == "listo para servir")
                {
                    if($productoComandaRetornado->estado != $datos["estado"])
                    {
                        $controladorProducto = new ProductoController();
                        $comandaRetornada = $controladorComanda->buscarComandaPorId($productoComandaRetornado->id);
                        $tipoProducto = ($controladorProducto->buscarProductoPorNombre($productoComandaRetornado->detalle))->tipo;
                        
                        $flagPreparacion = false;
                        switch($datos["datos"]["tipoEmpleado"])
                        {
                            case "cocinero":
                                if($tipoProducto == "comida" || $tipoProducto == "postre")
                                {
                                    $flagPreparacion = true;     
                                }
                                else
                                {
                                    $arrayRespuesta["status"] = "ERROR";
                                    $arrayRespuesta["message"] = "Un cocinero solo puede preparar comidas y postres";
                                }
                            break;
                            case "bartender":
                                if($tipoProducto == "trago")
                                {
                                    $flagPreparacion = true;     
          
                                }
                                else
                                {
                                    $arrayRespuesta["status"] = "ERROR";
                                    $arrayRespuesta["message"] = "Un bartender solo puede preparar tragos";
                                }
                            break;
                            case "cervecero":
                                if($tipoProducto == "cerveza")
                                {
                                    $flagPreparacion = true;      
                                }
                                else
                                {
                                    $arrayRespuesta["status"] = "ERROR";
                                    $arrayRespuesta["message"] = "Un cervecero solo puede preparar cervezas";
                                }
                            break;
                            case "mozo":
                            case "socio":
                                $flagPreparacion = true;      
                            break;
                        }
    
                        if($flagPreparacion)
                        {
                            $productoComandaRetornado->estado = $datos["estado"];
                            if($controladorComanda->PrepararProductoComanda($productoComandaRetornado))
                            {
                                $arrayRespuesta["status"] = "OK";
                                $arrayRespuesta["message"] = "El estado de la comanda fue cambiado con exito";
                                //creo que el mozo es quien debe cambiar el estado de la mesa, pero este codigo comentado lo hace automaticamente
                                // $flagTodoListo = false;
                                // $arrayIdProducto = explode(",", $comandaRetornada->idProducto);
                                // $arrayEstado = explode(",", $comandaRetornada->estado);
                                // for($i=0 ; $i<count($arrayEstado) ; $i++)
                                // {
                                //     if($arrayEstado[$i] == "listo para servir" || $arrayIdProducto[$i] == $productoComandaRetornado->idProducto)
                                //     {
                                //         $flagTodoListo = true;
                                //     }
                                //     else
                                //     {
                                //         $flagTodoListo = false;
                                //         break;
                                //     }
                                // }
                                // if($flagTodoListo)
                                // {
                                //     $controladorMesa = new MesaController();
                                //     $mesaRetornada = $controladorMesa->buscarMesaPorId($productoComandaRetornado->idMesa);
                                //     $controladorMesa->modificarMesa($productoComandaRetornado->idMesa, "con cliente comiendo");
                                // }
                            }
                            else
                            {
                                $arrayRespuesta["status"] = "ERROR";
                                $arrayRespuesta["message"] = "Ocurrio un error al cambiar el estado de la comanda";
                            }
    
                        }
                    }
                    else
                    {
                        $arrayRespuesta["status"] = "WARNING";
                        $arrayRespuesta["message"] = "No se realizo ninguna modificacion, el estado ya era '{$productoComandaRetornado->estado}'";
                    }
                }
                else
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "El estado debe ser 'en preparacion' o 'listo para servir'";
                }
                
    
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "No existe un producto de comanda con ese ID";
            }


        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function cobrarComanda($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] = "mozo" || $datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new ComandaController();
                $comandaRetornada = $controlador->buscarComandaPorId($datos["id"]);
                if($comandaRetornada == false)
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "La comanda no existe";
                }
                else
                {
                    
                    $retornoBaja = $controlador->bajaComanda($datos["id"]);
                     if($retornoBaja)
                     {
                        $controladorMesa = new MesaController();
                        $mesaRetornada = $controladorMesa->buscarMesaPorId($comandaRetornada->idMesa);
                        //chequear que la mesa este en cliente pagando
                        if($mesaRetornada->estado == "con cliente pagando")
                        {
                            $controladorMesa->modificarMesa($comandaRetornada->idMesa, "disponible");
                            $arrayRespuesta["status"] = "OK";
                            $arrayRespuesta["message"] = "Comanda cobrada con exito";
                        }
                        else
                        {
                            $arrayRespuesta["status"] = "ERROR";
                            $arrayRespuesta["message"] = "El cliente no pidio la cuenta";
                        }
                         
                     }
                     else
                     {
                        $arrayRespuesta["status"] = "ERROR";
                        $arrayRespuesta["message"] = "No se pudo cobrar la comanda";
    
                     }
                }
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser mozo o socio para cobrar una comanda";
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