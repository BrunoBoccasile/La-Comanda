<?php
require_once __DIR__ . "/../controladores/productoController.php";

function altaProducto($datos)
{
    if(isset($datos["nombre"]) && isset($datos["tipo"]) && isset($datos["precio"]))
    {
        if(Producto::ValidarDatos($datos["nombre"], $datos["tipo"], $datos["precio"]))
        {
           $controlador = new ProductoController();
           if($controlador->buscarProductoPorNombre($datos["nombre"]) == false)
           {
               $retornoInsertar = $controlador->insertarProducto(strtolower($datos["nombre"]), strtolower($datos["tipo"]), $datos["precio"], "activo");
               echo "Se insertó el producto con éxito con el ID: " . $retornoInsertar; 
           }
           else
           {
                echo "Error: El producto ya existe.\n";
           }
        }
        else
        {
            echo "Error: Datos invalidos.\n";
            echo "Nombre y tipo deben estar compuestos de 1 a 50 caracteres. El tipo debe ser trago, cerveza, comida o postre. El precio debe ser positivo y de hasta 7 cifras";
        }
    }
    else
    {
        echo "Error: faltan datos necesarios para el alta del producto.\n";
        echo "Se requiere nombre, tipo y precio";
    }
}

function bajaProducto($datos)
{
    if(isset($datos["id"]))
    {

           $controlador = new ProductoController();
           $productoRetornado = $controlador->buscarProductoPorId($datos["id"]);
           if($productoRetornado == false)
           {
               echo "Error: El producto no existe o ya esta dado de baja.\n"; 
           }
           else
           {
                $controlador->modificarProducto($productoRetornado->id, strtolower($productoRetornado->nombre), strtolower($productoRetornado->tipo), $productoRetornado->precio, "borrado");
                echo "Producto borrado con exito (baja logica).\n";
           }

    }
    else
    {
        echo "Error: faltan datos necesarios para la baja del producto.\n";
        echo "Se requiere id";
    }
}

function modificarProducto($datos)
{
    if(isset($datos["id"]))
    {

           $controlador = new ProductoController();
           $productoRetornado = $controlador->buscarProductoPorId($datos["id"]);
           if($productoRetornado == false)
           {
               echo "Error: El producto no existe.\n"; 
           }
           else
           {
                $nombreModificado = $productoRetornado->nombre;
                $precioModificado = $productoRetornado->precio;
                $tipoModificado = $productoRetornado->tipo;
                $flagModificacion = false;
                if(isset($datos["nombre"]) && $datos["nombre"] != $nombreModificado)
                {
                    if($controlador->buscarProductoPorNombre($datos["nombre"]) == false)
                    {
                        $nombreModificado = $datos["nombre"];
                        $flagModificacion = true;
                    }
                    else
                    {
                        echo "El nombre del producto ya esta en uso, se deja el original.\n";
                    }
                }
                if(isset($datos["precio"]) && $datos["precio"] != $precioModificado)
                {
                    $precioModificado = $datos["precio"];
                    $flagModificacion = true;
                }
                if(isset($datos["tipo"]) && $datos["tipo"] != $tipoModificado)
                {
                    if($datos["tipo"] == "trago" || $datos["tipo"] == "cerveza" || $datos["tipo"] == "comida" || $datos["tipo"] == "postre")
                    {
                        $tipoModificado = $datos["tipo"];
                        $flagModificacion = true;
                    }
                    else
                    {
                        echo "El tipo es invalido. Se espera trago, cerveza, comida o postre.\n";
                    }
                }
                if($flagModificacion)
                {
                    $controlador->modificarProducto($productoRetornado->id, strtolower($nombreModificado), strtolower($tipoModificado), $precioModificado, $productoRetornado->estado);      
                    echo "Producto modificado con exito.\n";
                }
                else
                {
                    echo "No se realizo ninguna modificacion.\n";
                }
           }

    }
    else
    {
        echo "Error: faltan datos necesarios para la modificacion del producto.\n";
        echo "Se requiere id para identificar el producto a modificar";
    }
}

function listadoProducto($datos)
{
    if(isset($datos["parametro"]))
    {
        $controladorProducto = new ProductoController();
        if($datos["parametro"] == "uno")
        {
            if(isset($datos["id"]))
            {
                $productoRetornado = $controladorProducto->buscarProductoPorId($datos["id"]);
                if($productoRetornado == false)
                {
                    echo "Error: No existe un producto con ese ID.\n";
                }
                else
                {
                    echo $productoRetornado->mostrarDatos();
                }
            }
            else
            {
                echo "Error: Para listar un producto se necesita el ID.\n";
            }
        }
        else if($datos["parametro"] == "todos")
        {
            $productosRetornados = $controladorProducto->listarProductos();
            foreach($productosRetornados as $producto)
            {
                echo $producto->mostrarDatos() . "\n";
            }
        }
        else
        {
            echo "Error: El 'parametro' debe ser 'uno' o 'todos'.\n";
        }
    }
    else
    {
        echo "Error: faltan datos necesarios para el listado de los productos.\n";
        echo "Se requiere parametro para listar uno o todos.\n";
    }
}
?>