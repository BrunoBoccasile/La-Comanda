<?php
require_once __DIR__ . "/../controladores/productoController.php";

function altaProducto($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["nombre"]) && isset($datos["tipo"]) && isset($datos["precio"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                if(Producto::ValidarDatos($datos["nombre"], $datos["tipo"], $datos["precio"]))
                {
                   $controlador = new ProductoController();
                   if($controlador->buscarProductoPorNombre($datos["nombre"]) == false)
                   {
                       $retornoInsertar = $controlador->insertarProducto(strtolower($datos["nombre"]), strtolower($datos["tipo"]), $datos["precio"], "activo");
                       echo json_encode(array("OK" => "Se insertó el producto con éxito con el ID: {$retornoInsertar}"));
                   }
                   else
                   {
                        echo json_encode(array("ERROR" => "El producto ya existe"));
                   }
                }
                else
                {
                    echo json_encode(array("ERROR" => "Datos invalidos. Nombre y tipo producto deben estar compuestos de 1 a 50 caracteres. El tipo debe ser trago, cerveza, comida o postre. El precio debe ser positivo y de hasta 7 cifras"));
                }
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser socio para dar de alta un producto"));
            }
        }
        else
        {
            echo json_encode(array("ERROR" => "faltan datos necesarios para el alta del producto. Se requiere nombre, tipo y precio"));
        }
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function bajaProducto($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["id"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new ProductoController();
                $productoRetornado = $controlador->buscarProductoPorId($datos["id"]);
                if($productoRetornado == false)
                {
                    echo json_encode(array("ERROR" => "El producto no existe o ya esta dado de baja"));
                }
                else
                {
                    $controlador->modificarProducto($productoRetornado->id, strtolower($productoRetornado->nombre), strtolower($productoRetornado->tipo), $productoRetornado->precio, "borrado");
                    echo json_encode(array("OK" => "Producto borrado con exito (baja logica)"));
                }
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser socio para dar de baja un producto"));
            }
               
    
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para la baja del producto. Se requiere id"));        
        }
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function modificarProducto($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if(isset($datos["id"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new ProductoController();
                $productoRetornado = $controlador->buscarProductoPorId($datos["id"]);
                if($productoRetornado == false)
                {
                     echo json_encode(array("ERROR" => "El producto no existe"));
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
                             echo json_encode(array("ADVERTENCIA" => "El nombre del producto ya esta en uso, se deja el original"));
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
                             echo json_encode(array("ERROR" => "El tipo es invalido. Se espera trago, cerveza, comida o postre"));
                         }
                     }
                     if($flagModificacion)
                     {
                         $controlador->modificarProducto($productoRetornado->id, strtolower($nombreModificado), strtolower($tipoModificado), $precioModificado, $productoRetornado->estado);      
                         echo json_encode(array("OK" => "Producto modificado con exito"));
     
                     }
                     else
                     {
                         echo json_encode(array("ADVERTENCIA" => "No se realizo ninguna modificacion"));
                     }
                }
            }
            else
            {
                echo json_encode(array("ERROR" => "Debe ser socio para dar de baja un producto"));
            }
               
    
        }
        else
        {
            echo json_encode(array("ERROR" => "Faltan datos necesarios para la modificacion del producto. Se requiere id para identificar el producto a modificar"));
        }
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
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
                    echo json_encode(array("ERROR" => "No existe un producto con ese ID"));
                }
                else
                {
                    echo $productoRetornado->mostrarDatos();
                }
            }
            else
            {
                echo json_encode(array("ERROR" => "Para listar un producto se necesita el ID"));
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
            echo json_encode(array("ERROR" => "El 'parametro' debe ser 'uno' o 'todos'"));
        }
    }
    else
    {
        echo json_encode(array("ERROR" => "Faltan datos necesarios para el listado de los productos. Se requiere parametro para listar uno o todos"));
    }
}
?>