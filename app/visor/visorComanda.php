<?php
require_once __DIR__ . "/../controladores/comandaController.php";
require_once __DIR__ . "/../controladores/mesaController.php";
function altaComanda($datos)
{
    if(isset($datos["detalle"]) && isset($datos["idMesa"]))
    {
        $retornoValidacion = Comanda::ValidarDatos($datos["detalle"]);
        if($retornoValidacion == 1)
        {
            $controlador = new ComandaController();
            $retornoInsertar = $controlador->insertarComanda(strtolower($datos["detalle"]), $datos["idMesa"]);
            if($retornoInsertar == 0)
            {
                echo "Error: El ID de la mesa no corresponde a una mesa existente.\n";
            }
            else
            {
                echo "Se insertó la comanda con éxito con el ID: " . $retornoInsertar; 
            }
        }
        else
        {
            switch($retornoValidacion)
            {
                case false:
                    echo "Error: Detalle invalido.\n";
                    echo "El detalle debe estar formado por productos existentes separados con coma. Ejemplo: milanesa a caballo,quilmes,daikiri.\n";
                    break;
                case -1:
                   echo "Error: No se pueden pedir tragos porque no hay bartender.\n";
                    break;
                case -2:
                    echo "Error: No se pueden pedir cervezas porque no hay cervecero.\n";
                    break;
                case -3:
                    echo "Error: No se pueden pedir comidas ni postres porque no hay cocinero.\n";
                    break;
            }
        }
    }
    else
    {
        echo "Error: faltan datos necesarios para el alta de la comanda.\n";
        echo "Se requiere detalle e id de la mesa.\n";
    }
}

function bajaComanda($datos)
{
    if(isset($datos["id"]))
    {

           $controlador = new ComandaController();
           $comandaRetornada = $controlador->buscarComandaPorId($datos["id"]);
           if($comandaRetornada == false)
           {
               echo "Error: La comanda no existe o ya esta concluida.\n"; 
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
                echo "Comanda concluida con exito.\n";
           }

    }
    else
    {
        echo "Error: faltan datos necesarios para la baja de la comanda.\n";
        echo "Se requiere id";
    }
}

function modificarComanda($datos)
{

    if(isset($datos["id"]))
    {
           $controlador = new ComandaController();
           $comandaRetornada = $controlador->buscarComandaPorId($datos["id"]);
           if($comandaRetornada == false)
           {
               echo "Error: La comanda no existe.\n"; 
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
                                echo "Error: Detalle invalido.\n";
                                echo "El detalle debe estar formado por productos existentes separados con coma. Ejemplo: milanesa a caballo,quilmes,daikiri.\n";
                                break;
                            case -1:
                               echo "Error: No se pueden pedir tragos porque no hay bartender.\n";
                                break;
                            case -2:
                                echo "Error: No se pueden pedir cervezas porque no hay cervecero.\n";
                                break;
                            case -3:
                                echo "Error: No se pueden pedir comidas ni postres porque no hay cocinero.\n";
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
                        echo "Error: el id de la mesa no corresponde a una mesa existente.\n";
                    }
                    else
                    {
                        echo "Comanda modificada con exito.\n";
                    }
                }
                else
                {
                    echo "No se realizo ninguna modificacion.\n";
                }
           }

    }
    else
    {
        echo "Error: faltan datos necesarios para la modificacion de la comanda.\n";
        echo "Se requiere id para identificar la comanda a modificar";
    }
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
                    echo "Error: No existe una mesa con ese ID.\n";
                }
                else
                {
                    echo $comandaRetornada->mostrarDatos();
                }
            }
            else
            {
                echo "Error: Para listar una mesa se necesita el ID.\n";
            }
        }
        else if($datos["parametro"] == "todas")
        {
            $comandasRetornadas = $controladorComanda->listarComandas();
            foreach($comandasRetornadas as $comanda)
            {
                echo $comanda->mostrarDatos() . "\n";
            }
        }
        else
        {
            echo "Error: El 'parametro' debe ser 'una' o 'todas'.\n";
        }
    }
    else
    {
        echo "Error: faltan datos necesarios para el listado de las comandas.\n";
        echo "Se requiere parametro para listar una o todas.\n";
    }
}
?>