<?php

require_once __DIR__ . '/../models/empleado.php';

class EmpleadoController {

    public function insertarEmpleado($nombre, $apellido, $usuario, $clave, $estado, $tipo) {
        $empleado = new Empleado();
        $empleado->nombre = $nombre;
        $empleado->apellido = $apellido;
        $empleado->usuario = $usuario;
        $empleado->clave = $clave;
        $empleado->estado = $estado;
        $empleado->tipo = $tipo;
        return $empleado->InsertarElEmpleadoParametros();
    }

    public function modificarEmpleado($id, $nombre, $apellido, $usuario, $clave, $estado, $tipo) {
        $empleado = new Empleado();
        $empleado->id = $id;
        $empleado->nombre = $nombre;
        $empleado->apellido = $apellido;
        $empleado->usuario = $usuario;
        $empleado->clave = $clave;
        $empleado->estado = $estado;
        $empleado->tipo = $tipo;
        return $empleado->ModificarEmpleadoParametros();
    }

    public function borrarEmpleado($id) {
        $empleado = new Empleado();
        $empleado->id = $id;
        return $empleado->BorrarEmpleado();
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
    

    public function buscarEmpleadoPorUsuario($usuario) {
       $retorno = Empleado::TraerUnEmpleadoPorUsuario($usuario);
       return $retorno;
    }


    public function buscarEmpleadoPorUsuarioClave($usuario, $clave) {
        $retorno = Empleado::TraerUnEmpleadoPorUsuarioClave($usuario, $clave);
        return $retorno;
    }

    public function buscarEmpleadoPorId($id) {
        $retorno = Empleado::TraerUnEmpleado($id);
        return $retorno;
    }


}