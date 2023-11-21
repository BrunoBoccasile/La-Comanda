<?php
require_once __DIR__ . "/../controladores/comandaController.php";
require_once __DIR__ . "/../controladores/mesaController.php";
require_once __DIR__ . "/../controladores/productoController.php";
function altaComanda($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        
        if(isset($datos["detalle"]) && isset($datos["idMesa"]) && isset($datos["idCliente"]))
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
                        echo json_encode(array("ERROR" => "El ID de la mesa no corresponde a una mesa existente"));
                    }
                    else if($retornoInsertar == -1)
                    {
                        echo json_encode(array("ERROR" => "El ID del cliente no corresponde a un cliente existente"));
                    }
                    else
                    {
                        echo json_encode(array("OK" => "Se insertó la comanda con éxito con el ID: {$retornoInsertar}"));
                    }
                }
                else
                {
                    switch($retornoValidacion)
                    {
                        case false:
                            echo json_encode(array("ERROR" => "El detalle debe estar compuesto por nombres de productos existentes separados por coma. Por ejemplo: 'hamburguesa,quilmes'"));
                            break;
                        case -1:
                            echo json_encode(array("ERROR" => "No se pueden pedir tragos porque no hay bartender"));
                            break;
                        case -2:
                            echo json_encode(array("ERROR" => "No se pueden pedir cervezas porque no hay cervecero"));
                            break;
                        case -3:
                            echo json_encode(array("ERROR" => "No se pueden pedir comidas ni postres porque no hay cocinero"));
                            break;
                    }
                }
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser mozo o socio para dar de alta una comanda"));
            }
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para el alta de la comanda. Se requiere detalle, id de la mesa e id del cliente"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function altaFotoComanda($datos, $archivo)
{
    if(isset($archivo["foto"]) && isset($datos["id"]))
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
                    echo json_encode(array("ERROR" => "La extensión o el tamaño del archivo no es correcta"));
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
                    echo json_encode(array("OK" => "Foto subida con exito"));
                }
            }
            else
            {
                echo json_encode(array("ERROR" => "No existe una comanda con el id ingresado"));
            }
    }
    else
    {
        echo json_encode(array("ERROR" => "Faltan datos necesarios para la subida de la foto. Se requiere foto e id"));
    }
}
function bajaComanda($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["id"]))
        {
            if($datos["datos"]["tipoEmpleado"] = "mozo" || $datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new ComandaController();
                $comandaRetornada = $controlador->buscarComandaPorId($datos["id"]);
                if($comandaRetornada == false)
                {
                     echo json_encode(array("ERROR" => "La comanda no existe o ya esta concluida"));
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
                         echo json_encode(array("OK" => "Comanda concluida con exito"));
                     }
                     else
                     {
                        echo json_encode(array("ERROR" => "No se pudo concluir la comanda"));
    
                     }
                }
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser mozo o socio para dar de baja una comanda"));
            }
    
    
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para la baja de la comanda. Se requiere id"));
        }
        echo "\n";
        
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }

}

function modificarComanda($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        
        if(isset($datos["id"]))
        {
    
            if($datos["datos"]["tipoEmpleado"] = "mozo" || $datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new ComandaController();
               $comandaRetornada = $controlador->buscarComandaPorId($datos["id"]);
               if($comandaRetornada == false)
               {
                    echo json_encode(array("ERROR" => "La comanda no existe"));
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
                            switch($retornoValidacion)
                            {
                                case false:
                                    echo json_encode(array("ERROR" => "El detalle debe estar compuesto por un producto existente"));
                                    break;
                                case -1:
                                    echo json_encode(array("ERROR" => "No se pueden pedir tragos porque no hay bartender"));
                                    break;
                                case -2:
                                    echo json_encode(array("ERROR" => "No se pueden pedir cervezas porque no hay cervecero"));
                                    break;
                                case -3:
                                    echo json_encode(array("ERROR" => "No se pueden pedir comidas ni postres porque no hay cocinero"));
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
                                echo json_encode(array("ERROR" => "El id de la mesa no corresponde a una mesa existente"));
                            }
                            else
                            {
                                echo json_encode(array("OK" => "Comanda modificada con exito"));
                            }
                        }
                        else
                        {
                            echo json_encode(array("ERROR" => "No puede agregar productos a la comanda, solo modificar"));
                        }
                    }
                    else
                    {
                        echo json_encode(array("ADVERTENCIA" => "No se realizo ninguna modificacion"));
                    }
               }
    
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser mozo o socio para modificar una comanda"));
            }
              
        }
        else
        {
            echo json_encode(array("ADVERTENCIA" => "Faltan datos necesarios para la modificacion de la comanda. Se requiere id para identificar la comanda a modificar"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function listadoComanda($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        
        if(isset($datos["parametro"]))
        {
            $controladorComanda = new ComandaController();
            if($datos["parametro"] == "una")
            {
                if(isset($datos["id"]))
                {
                    $comandaRetornada = $controladorComanda->buscarComandaPorId($datos["id"]);
                    $arrayDetalle = explode(",", $comandaRetornada->detalle);
                    $arrayEstado = explode(",", $comandaRetornada->detalle);
                    $arrayIdProducto = explode(",", $comandaRetornada->idProducto);
                    $arrayTiempos = explode(",", $comandaRetornada->tiempoEstimadoFinalizacion);
    
                    if($comandaRetornada == false)
                    {
                        echo json_encode(array("ERROR" => "No existe una comanda con ese ID"));
                    }
                    else
                    {
                        $controladorProducto = new ProductoController();
    
                        $i = 0;
                        foreach($arrayDetalle as $producto)
                        {
                            $tipoProducto = ($controladorProducto->buscarProductoPorNombre($producto))->tipo;
        
                            switch($datos["datos"]["tipoEmpleado"])
                            {
                                case "cocinero":
                                    if($tipoProducto == "comida" || $tipoProducto == "postre")
                                    {
                                        echo $controladorComanda->buscarProductoComanda($arrayIdProducto[$i])->mostrarProductoComanda();
                                    }
                                    else
                                    {
                                        echo json_encode(array("ADVERTENCIA" => "Un cocinero solo puede listar comidas y postres"));
                                    }
                                break;
                                case "bartender":
                                    if($tipoProducto == "trago")
                                    {
                                        echo $controladorComanda->buscarProductoComanda($arrayIdProducto[$i])->mostrarProductoComanda();
                                    }
                                    else
                                    {
                                        echo json_encode(array("ADVERTENCIA" => "Un bartender solo puede listar tragos"));
        
                                    }
                                break;
                                case "cervecero":
                                    if($tipoProducto == "cerveza")
                                    {
                                        echo $controladorComanda->buscarProductoComanda($arrayIdProducto[$i])->mostrarProductoComanda();
                                    }
                                    else
                                    {
                                        echo json_encode(array("ADVERTENCIA" => "Un cervecero solo puede listar cervezas"));
                                    }
                                break;
                                case "mozo":
                                case "socio":
                                    echo $controladorComanda->buscarProductoComanda($arrayIdProducto[$i])->mostrarProductoComanda();
                                break;
                            }
                            echo "\n";
                            $i++;
                        }
                        
                    }
                }
                else
                {
                    echo json_encode(array("ERROR" => "Para listar una comanda se necesita el ID"));
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
                            foreach($comandasRetornadas as $comanda)
                            {
                                echo $comanda->mostrarProductoComanda();
                                echo "\n";
                            }
                        }
                        else
                        {
                            echo json_encode(array("ADVERTENCIA" => "No hay ninguna comanda del tipo y estado correspondiente"));
        
                        }
                    }
                    else
                    {
                        echo json_encode(array("ERROR" => "El parametro estado debe ser 'pendiente', 'en preparacion' o 'listo para servir'"));
                    }
                }
                else
                {
                    echo json_encode(array("ERROR" => "Si va a listar todas, debe ingresar el estado de las comandas a listar"));
                }
                
            }
            else
            {
                echo json_encode(array("ERROR" => "El 'parametro' debe ser 'una' o 'todas'"));
            }
        }
        else
        {
            echo json_encode(array("ERROR" => "Error: faltan datos necesarios para el listado de las comandas. Se requiere parametro para listar una o todas"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function preparacionComanda($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["estado"]) && isset($datos["idProducto"]))
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
                                    echo json_encode(array("ERROR" => "Un cocinero solo puede preparar comidas y postres"));
                                }
                            break;
                            case "bartender":
                                if($tipoProducto == "trago")
                                {
                                    $flagPreparacion = true;     
          
                                }
                                else
                                {
                                    echo json_encode(array("ERROR" => "Un bartender solo puede preparar tragos"));
            
                                }
                            break;
                            case "cervecero":
                                if($tipoProducto == "cerveza")
                                {
                                    $flagPreparacion = true;      
                                }
                                else
                                {
                                    echo json_encode(array("ERROR" => "Un cervecero solo puede preparar cervezas"));
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
                                echo json_encode(array("OK" => "El estado de la comanda fue cambiado con exito"));
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
                                echo json_encode(array("ERROR" => "Ocurrio un error al cambiar el estado de la comanda"));
                            }
    
                        }
                    }
                    else
                    {
                        echo json_encode(array("ADVERTENCIA" => "No se realizo ninguna modificacion, el estado ya era '{$productoComandaRetornado->estado}"));
                    }
                }
                else
                {
                    echo json_encode(array("ERROR" => "El estado debe ser 'en preparacion' o 'listo para servir'"));
                }
                
    
            }
            else
            {
                echo json_encode(array("ERROR" => "No existe un producto de comanda con ese ID"));
            }
        }
        else
        {
            echo json_encode(array("ERROR" => "Error: faltan datos necesarios para el listado de las comandas. Se requiere el id del producto de la comanda y estado"));
        }
        echo "\n"; 
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
    
}

function cobrarComanda($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["id"]))
        {
            if($datos["datos"]["tipoEmpleado"] = "mozo" || $datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new ComandaController();
                $comandaRetornada = $controlador->buscarComandaPorId($datos["id"]);
                if($comandaRetornada == false)
                {
                     echo json_encode(array("ERROR" => "La comanda no existe"));
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
                                echo json_encode(array("OK" => "Comanda cobrada con exito"));
                            }
                            else
                            {
                                echo json_encode(array("ERROR" => "El cliente no pidio la cuenta"));
                            }
                         
                     }
                     else
                     {
                        echo json_encode(array("ERROR" => "No se pudo cobrar la comanda"));
    
                     }
                }
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser mozo o socio para cobrar una comanda"));
            }
    
    
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para cobrar la comanda. Se requiere id"));
        }
        echo "\n";
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

?>