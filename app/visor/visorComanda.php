<?php
require_once __DIR__ . "/../controladores/comandaController.php";
require_once __DIR__ . "/../controladores/mesaController.php";
require_once __DIR__ . "/../controladores/productoController.php";
function altaComanda($datos, $login, $archivo)
{
    if(isset($datos["detalle"]) && isset($datos["idMesa"]))
    {
        if($login["tipoEmpleado"] == "socio" || $login["tipoEmpleado"] == "mozo")
        {
            $retornoValidacion = Comanda::ValidarDatos($datos["detalle"]);
            if($retornoValidacion == 1)
            {
                $controlador = new ComandaController();
                $retornoInsertar = $controlador->insertarComanda(strtolower($datos["detalle"]), $datos["idMesa"]);
                if($retornoInsertar == 0)
                {
                    echo json_encode(array("ERROR" => "El ID de la mesa no corresponde a una mesa existente"));
                }
                else
                {
                    echo json_encode(array("OK" => "Se insertó la comanda con éxito con el ID: {$retornoInsertar}"));
                    if(isset($archivo["foto"]))
                    {
                        $archivo = $archivo["foto"];
                        $tamanoArchivo = $archivo->getSize();
                        $tipoArchivo = $archivo->getClientMediaType();

                        $nombreArchivo = $datos["idMesa"] . "-" . $retornoInsertar;
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
                        }
                    }
                }
            }
            else
            {
                switch($retornoValidacion)
                {
                    case false:
                        echo json_encode(array("ERROR" => "El detalle debe estar compuesto por el nombre de un producto existente"));
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
        echo json_encode(array("ERROR" => "Faltan datos necesarios para el alta de la comanda. Se requiere detalle e id de la mesa"));
    }
    echo "\n";
}

function bajaComanda($datos, $login)
{
    if(isset($datos["id"]))
    {
        if($login["tipoEmpleado"] = "mozo" || $login["tipoEmpleado"] == "socio")
        {
            $controlador = new ComandaController();
            $comandaRetornada = $controlador->buscarComandaPorId($datos["id"]);
            if($comandaRetornada == false)
            {
                 echo json_encode(array("ERROR" => "La comanda no existe o ya esta concluida"));
            }
            else
            {
                 $controlador->modificarComanda($comandaRetornada->id, $comandaRetornada->detalle, $comandaRetornada->idMesa, "concluida");
                 $controladorMesa = new MesaController();
                 //chequear que todas las comandas de una mesa esten concluidas antes de cambiar el estado de la mesa a disponible
                 if(count($controlador->listarComandasPorMesa($comandaRetornada->idMesa)) == 0)
                 {
                     $controladorMesa->modificarMesa($comandaRetornada->idMesa, "disponible");
                 }
                 echo json_encode(array("OK" => "Comanda concluida con exito"));
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

function modificarComanda($datos, $login)
{

    if(isset($datos["id"]))
    {

        if($login["tipoEmpleado"] = "mozo" || $login["tipoEmpleado"] == "socio")
        {
            $controlador = new ComandaController();
           $comandaRetornada = $controlador->buscarComandaPorId($datos["id"]);
           if($comandaRetornada == false)
           {
                echo json_encode(array("ERROR" => "La comanda no existe"));
           }
           else
           {
                $detalleModificado = $comandaRetornada->detalle;
                $idMesaModificado = $comandaRetornada->idMesa;
                $flagModificacion = false;
                if(isset($datos["detalle"]) && $datos["detalle"] != $detalleModificado)
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
                    $retornoModificacion = $controlador->modificarComanda($datos["id"], $detalleModificado, $idMesaModificado, $comandaRetornada->estado);      
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

function listadoComanda($datos)
{
    if(isset($datos["parametro"]))
    {
        $controladorComanda = new ComandaController();
        if($datos["parametro"] == "una")
        {
            if(isset($datos["id"]))
            {
                $comandaRetornada = $controladorComanda->buscarComandaPorId($datos["id"]);
                
                if($comandaRetornada == false)
                {
                    echo json_encode(array("ERROR" => "No existe una comanda con ese ID"));
                }
                else
                {
                    $controladorProducto = new ProductoController();

                    $tipoProducto = ($controladorProducto->buscarProductoPorNombre($comandaRetornada->detalle))->tipo;
                    switch($datos["tipoEmpleado"])
                    {
                        case "cocinero":
                            if($tipoProducto == "comida" || $tipoProducto == "postre")
                            {
                                echo $comandaRetornada->mostrarDatos();
                            }
                            else
                            {
                                echo json_encode(array("ERROR" => "Un cocinero solo puede listar comidas y postres"));
                            }
                        break;
                        case "bartender":
                            if($tipoProducto == "trago")
                            {
                                echo $comandaRetornada->mostrarDatos();
                            }
                            else
                            {
                                echo json_encode(array("ERROR" => "Un bartender solo puede listar tragos"));

                            }
                        break;
                        case "cervecero":
                            if($tipoProducto == "cerveza")
                            {
                                echo $comandaRetornada->mostrarDatos();
                            }
                            else
                            {
                                echo json_encode(array("ERROR" => "Un cervecero solo puede listar cervezas"));
                            }
                        break;
                        case "mozo":
                        case "socio":
                            echo $comandaRetornada->mostrarDatos();
                        break;
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

                switch($datos["tipoEmpleado"])
                {
                    case "cocinero":
                        $comandasRetornadas = $controladorComanda->listarComandasPorTipo("comida");
                        $postresRetornados = $controladorComanda->listarComandasPorTipo("postre");
                        foreach($postresRetornados as $postre)
                        {
                            array_push($comandasRetornadas, $postre);
                        }
                    break;
                    case "bartender":
                        $comandasRetornadas =  $controladorComanda->listarComandasPorTipo("trago");
                    break;
                    case "cervecero":
                        $comandasRetornadas =  $controladorComanda->listarComandasPorTipo("cerveza");
                    break;
                    case "mozo":
                    case "socio":
                        $comandasRetornadas =  $controladorComanda->listarComandas();
                    break;
                }

                if($comandasRetornadas)
                {
                    foreach($comandasRetornadas as $comanda)
                    {
                        echo $comanda->mostrarDatos();
                        echo "\n";
                    }
                }
                else
                {
                    echo json_encode(array("ADVERTENCIA" => "No hay ninguna comanda del tipo correspondiente"));

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

function preparacionComanda($datos)
{
    if(isset($datos["id"]) && isset($datos["estado"]))
    {
        $controladorComanda = new ComandaController();
        $comandaRetornada = $controladorComanda->buscarComandaPorId($datos["id"]);
        
        if($comandaRetornada)
        {
            if($datos["estado"] == "en preparacion" || $datos["estado"] == "listo para servir")
            {
                if($comandaRetornada->estado != $datos["estado"])
                {
                    $controladorProducto = new ProductoController();
    
                    $tipoProducto = ($controladorProducto->buscarProductoPorNombre($comandaRetornada->detalle))->tipo;
                    
                    switch($datos["tipoEmpleado"])
                    {
                        case "cocinero":
                            if($tipoProducto == "comida" || $tipoProducto == "postre")
                            {
                                $controladorComanda->modificarComanda($datos["id"], $comandaRetornada->detalle, $comandaRetornada->idMesa, $datos["estado"]);
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
                                $controlador->modificarComanda($datos["id"], $comandaRetornada->detalle, $comandaRetornada->idMesa, $datos["estado"]);
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
                                $controlador->modificarComanda($datos["id"], $comandaRetornada->detalle, $comandaRetornada->idMesa, $datos["estado"]);     
                                $flagPreparacion = true;      
                            }
                            else
                            {
                                echo json_encode(array("ERROR" => "Un cervecero solo puede preparar cervezas"));
                            }
                        break;
                        case "mozo":
                        case "socio":
                            $controlador->modificarComanda($datos["id"], $comandaRetornada->detalle, $comandaRetornada->idMesa, $datos["estado"]);      
                        break;
                    }

                    if($flagPreparacion)
                    {
                        echo json_encode(array("OK" => "El estado de la comanda fue cambiado con exito"));

                    }
                }
                else
                {
                    echo json_encode(array("ADVERTENCIA" => "No se realizo ninguna modificacion, el estado ya era '{$comandaRetornada->estado}"));
                }
            }
            else
            {
                echo json_encode(array("ERROR" => "El estado debe ser 'en preparacion' o 'listo para servir'"));
            }
            

        }
        else
        {
            echo json_encode(array("ERROR" => "No existe una comanda con ese ID"));
        }
    }
    else
    {
        echo json_encode(array("ERROR" => "Error: faltan datos necesarios para el listado de las comandas. Se requiere id de la comanda y estado"));
    }
    echo "\n"; 
}

?>