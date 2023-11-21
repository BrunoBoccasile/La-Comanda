<?php

require_once __DIR__ . '/../models/cliente.php';

class ClienteController {

    public function insertarCliente($nombre, $usuario, $clave) {
        $cliente = new Cliente();
        $cliente->nombre = $nombre;
        $cliente->usuario = $usuario;
        $cliente->clave = $clave;
        $cliente->estado = "activo";
        return $cliente->InsertarElClienteParametros();
    }

    public function modificarCliente($id, $nombre, $usuario, $clave, $estado) {
        $cliente = new Cliente();
        $cliente->id = $id;
        $cliente->nombre = $nombre;
        $cliente->usuario = $usuario;
        $cliente->clave = $clave;
        $cliente->estado = $estado;
        return $cliente->ModificarClienteParametros();
    }

    public function bajaCliente($id) {
        $cliente = Cliente::TraerUnCliente($id);
        $cliente->estado = "borrado";
        return $cliente->ModificarClienteParametros();
    }

    
    public function listarEmpleados() {
        return Empleado::TraerTodosLosEmpleados();
    }
    
    public function listarEmpleadosPorTipo($tipo)
    {
        return Empleado::TraerTodosLosEmpleadosPorTipo($tipo);
    }
    
    public function listarEmpleadosJson() {
        return json_encode(Empleado::TraerTodosLosEmpleados(), JSON_PRETTY_PRINT);
    }
    

    public function buscarClientePorUsuario($usuario) {
       $retorno = Cliente::TraerUnClientePorUsuario($usuario);
       return $retorno;
    }


    public function buscarClientePorUsuarioClave($usuario, $clave) {
        $retorno = Cliente::TraerUnClientePorUsuarioClave($usuario, $clave);
        return $retorno;
    }

    public function obtenerIdDeClienteLogeado($usuario, $clave)
    {
        $cliente = Cliente::TraerUnClientePorUsuarioClave($usuario, $clave);
        return $cliente->id;
    }

    public function buscarClientePorId($id) {
        $retorno = Cliente::TraerUnCliente($id);
        return $retorno;
    }
}