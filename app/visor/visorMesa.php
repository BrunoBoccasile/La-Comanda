<?php
require_once __DIR__ . "/../controladores/mesaController.php";

function altaMesa($datos)
{
    if(isset($datos["estado"]))
    {
        if(Mesa::ValidarDatos($datos["estado"]))
        {
            $controlador = new MesaController();
            $retornoInsertar = $controlador->insertarMesa($datos["estado"]);
            echo "Se insertó la mesa con éxito con el ID: " . $retornoInsertar; 
        }
        else
        {
            echo "Error: Datos invalidos.\n";
            echo "El estado deberia ser 'con cliente esperando pedido', 'con cliente comiendo', 'con cliente pagando' o 'disponible'.\n";
        }
    }
    else
    {
        echo "Error: faltan datos necesarios para el alta de la mesa.\n";
        echo "Se requiere estado.\n";
    }
}

function cierreMesa($datos)
{
    if(isset($datos["id"]))
    {

           $controlador = new MesaController();
           $mesaRetornada = $controlador->buscarMesaPorId($datos["id"]);
           if($mesaRetornada == false)
           {
               echo "Error: La mesa no existe o ya esta cerrada.\n"; 
           }
           else
           {
                $controlador->modificarMesa($mesaRetornada->id, "cerrada");
                echo "Mesa cerrada con exito.\n";
           }

    }
    else
    {
        echo "Error: faltan datos necesarios para el cierre de la mesa.\n";
        echo "Se requiere id";
    }
}

function modificarMesa($datos)
{
    if(isset($datos["id"]))
    {

           $controlador = new MesaController();
           $mesaRetornada = $controlador->buscarMesaPorId($datos["id"]);
           if($mesaRetornada == false)
           {
               echo "Error: La mesa no existe.\n"; 
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
                        echo "El estado deberia ser 'con cliente esperando pedido', 'con cliente comiendo', 'con cliente pagando' o 'disponible'.\n";
                    }
                }
                if($flagModificacion)
                {
                    $controlador->modificarMesa($mesaRetornada->id, $estadoModificado);      
                    echo "Mesa modificada con exito.\n";
                }
                else
                {
                    echo "No se realizo ninguna modificacion.\n";
                }
           }

    }
    else
    {
        echo "Error: faltan datos necesarios para la modificacion de la mesa.\n";
        echo "Se requiere id para identificar la mesa a modificar";
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
                    echo "Error: No existe una mesa con ese ID.\n";
                }
                else
                {
                    echo $mesaRetornada->mostrarDatos();
                }
            }
            else
            {
                echo "Error: Para listar una mesa se necesita el ID.\n";
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
            echo "Error: El 'parametro' debe ser 'una' o 'todas'.\n";
        }
    }
    else
    {
        echo "Error: faltan datos necesarios para el listado de las mesas.\n";
        echo "Se requiere parametro para listar una o todas.\n";
    }
}
?>