<?php
require_once __DIR__ . "/../controladores/productoController.php";

class VisorProducto
{
    static function altaProducto($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                if(Producto::ValidarDatos($datos["nombre"], $datos["tipo"], $datos["precio"]))
                {
                   $controlador = new ProductoController();
                   if($controlador->buscarProductoPorNombre($datos["nombre"]) == false)
                   {
                       $retornoInsertar = $controlador->insertarProducto(strtolower($datos["nombre"]), strtolower($datos["tipo"]), $datos["precio"], "activo");
                       $arrayRespuesta["status"] = "OK";
                       $arrayRespuesta["message"] = "Se insertó el producto con éxito con el ID: {$retornoInsertar}";
                   }
                   else
                   {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "El producto ya existe";
                   }
                }
                else
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "Datos invalidos. Nombre y tipo producto deben estar compuestos de 1 a 50 caracteres. El tipo debe ser trago, cerveza, comida o postre. El precio debe ser positivo y de hasta 7 cifras";
                }
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser socio para dar de alta un producto";
            }
            
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function bajaProducto($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new ProductoController();
                $productoRetornado = $controlador->buscarProductoPorId($datos["id"]);
                if($productoRetornado == false)
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "El producto no existe o ya esta dado de baja";
                }
                else
                {
                    $controlador->modificarProducto($productoRetornado->id, strtolower($productoRetornado->nombre), strtolower($productoRetornado->tipo), $productoRetornado->precio, "borrado");
                    $arrayRespuesta["status"] = "OK";
                    $arrayRespuesta["message"] = "Producto borrado con exito (baja logica)";
                }
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser socio para dar de baja un producto";
            }
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function modificarProducto($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controlador = new ProductoController();
                $productoRetornado = $controlador->buscarProductoPorId($datos["id"]);
                if($productoRetornado == false)
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "El producto no existe";
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
                            $arrayRespuesta["status"] = "WARNING";
                            $arrayRespuesta["message"] = "El nombre del producto ya esta en uso, se deja el original";
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
                            $arrayRespuesta["status"] = "ERROR";
                            $arrayRespuesta["message"] = "El tipo es invalido. Se espera trago, cerveza, comida o postre";
                         }
                     }
                     if($flagModificacion)
                     {
                        $controlador->modificarProducto($productoRetornado->id, strtolower($nombreModificado), strtolower($tipoModificado), $precioModificado, $productoRetornado->estado);      
                        $arrayRespuesta["status"] = "OK";
                        $arrayRespuesta["message"] = "Producto modificado con exito";
     
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
                $arrayRespuesta["message"] = "Debe ser socio para modificar un producto";
            }
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }
    
    static function listadoProducto($datos)
    {
        $controladorProducto = new ProductoController();
        if($datos["parametro"] == "uno")
        {
            if(isset($datos["id"]))
            {
                $productoRetornado = $controladorProducto->buscarProductoPorId($datos["id"]);
                if($productoRetornado == false)
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "No existe un producto con ese ID";
                }
                else
                {
                    $arrayRespuesta["status"] = "OK";
                    $arrayRespuesta["message"] = "Listado realizado con exito";
                    $arrayRespuesta["listado"] = $productoRetornado->mostrarDatos();
                }
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Para listar un producto se necesita el ID";
            }
        }
        else if($datos["parametro"] == "todos")
        {
            $productosRetornados = $controladorProducto->listarProductos();
            $arrayListado = array();
            foreach($productosRetornados as $producto)
            {
                array_push($arrayListado, $producto->mostrarDatos());
            }
            $arrayRespuesta["status"] = "OK";
            $arrayRespuesta["message"] = "Listado realizado con exito";
            $arrayRespuesta["listado"] = $arrayListado;
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El 'parametro' debe ser 'uno' o 'todos'";
        }
        return $arrayRespuesta;
    }
}
?>