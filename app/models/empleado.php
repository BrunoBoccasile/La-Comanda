<?php
include_once __DIR__ . "/../capa_de_datos/AccesoDatos.php";
class Empleado
{
    public $id;
    public $nombre;
    public $apellido;
    public $usuario;
    public $clave;
    public $estado;//activo/suspendido/borrado
    public $tipo; //socio/bartender/cervecero/cocinero/mozo

    public function mostrarDatos()
    {
        return $this->id . "  " . $this->nombre . "  " . $this->apellido . "  " . $this->usuario . " " . $this->clave . " " . $this->estado . " " . $this->tipo;
    }

    public static function ValidarDatos($nombre, $apellido, $usuario, $clave, $tipo)
    {
        if( (strlen($nombre) > 0 && strlen($nombre)  <= 50) && 
        (strlen($apellido) > 0 && strlen($apellido) <= 50) &&
        (strlen($usuario) > 0 && strlen($usuario) <= 50) &&
        (strlen($clave) > 0 && strlen($clave) <= 50) &&
        ($tipo == "socio" || $tipo == "bartender" || $tipo == "cervecero" || $tipo == "cocinero" || $tipo == "mozo" ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    public function InsertarElEmpleado()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into empleados (nombre,apellido,usuario,clave,estado,tipo)values('$this->nombre','$this->apellido','$this->usuario','$this->clave','$this->estado','$this->tipo')");
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public function InsertarElEmpleadoParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into empleados (nombre,apellido,usuario,clave,estado,tipo)values(:nombre,:apellido,:usuario,:clave,:estado,:tipo)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_INT);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerTodosLosEmpleados()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id, nombre,apellido,usuario,clave,estado,tipo from empleados where estado != 'borrado'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "empleado");
    }

    public static function TraerTodosLosEmpleadosPorTipo($tipo)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id, nombre,apellido,usuario,clave,estado,tipo from empleados where tipo = '$tipo' and estado != 'borrado'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "empleado");
    }

    public static function TraerUnEmpleado($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,nombre as nombre, apellido as apellido,usuario as usuario, clave as clave, estado as estado, tipo as tipo from empleados where id = $id and estado != 'borrado'");
        $consulta->execute();
        $empleadoBuscado = $consulta->fetchObject('empleado');
        return $empleadoBuscado;
    }

    public static function TraerUnEmpleadoPorUsuario($usuario)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select usuario as usuario,nombre as nombre, apellido as apellido, clave as clave, estado as estado, tipo as tipo from empleados where usuario = :usuario and estado != 'borrado'");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();   
        $empleadoBuscado = $consulta->fetchObject('empleado');
        return $empleadoBuscado;
    }



    public static function TraerUnEmpleadoPorUsuarioClave($usuario, $clave)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select usuario as usuario, nombre as nombre, apellido as apellido, clave as clave, estado as estado, tipo as tipo from empleados where usuario = :usuario and clave = :clave and estado != 'borrado'");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->execute();   
        $empleadoBuscado = $consulta->fetchObject('empleado');
        return $empleadoBuscado;
    }

    public function ModificarEmpleado()
    {

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				update empleados 
				set nombre='$this->nombre',
				apellido='$this->apellido',
				usuario='$this->usuario',
                clave='$this->clave',
                estado='$this->estado',
                tipo='$this->tipo'
				WHERE id='$this->id'");
        return $consulta->execute();
    }

    public function ModificarEmpleadoParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				update empleados 
				set nombre=:nombre,
				apellido=:apellido,
				usuario=:usuario,
                clave=:clave,
                estado=:estado,
                tipo=:tipo
				WHERE id=:id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public function BorrarEmpleado()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				delete 
				from empleados				
				WHERE id=:id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }

}
?>