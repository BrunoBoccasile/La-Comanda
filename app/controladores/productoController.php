<?php

require_once __DIR__ . '/../models/producto.php';

class ProductoController {

    public function insertarProducto($nombre, $tipo, $precio, $estado) {
        $producto = new Producto();
        $producto->nombre = $nombre;
        $producto->tipo = $tipo;
        $producto->precio = $precio;
        $producto->estado = $estado;
        return $producto->InsertarElProductoParametros();
    }

    public function modificarProducto($id, $nombre, $tipo, $precio, $estado) {
        $producto = new Producto();
        $producto->id = $id;
        $producto->nombre = $nombre;
        $producto->tipo = $tipo;
        $producto->precio = $precio;
        $producto->estado = $estado;
        return $producto->ModificarProductoParametros();
    }

    public function borrarProducto($id) {
        $producto = new Producto();
        $producto->id = $id;
        return $producto->BorrarProducto();
    }

    
    public function listarProductos() {
        return Producto::TraerTodosLosProductos();
    }
    
    
    public function listarProductosJson() {
        return json_encode(Producto::TraerTodosLosProductos(), JSON_PRETTY_PRINT);
    }
    

    public function buscarProductoPorId($id) {
        $retorno = Producto::TraerUnProducto($id);
        return $retorno;
    }

    public function buscarProductoPorNombre($nombre) {
        $retorno = Producto::TraerUnProductoPorNombre($nombre);
        return $retorno;
    }

}