<?php
include_once __DIR__ . "/../capa_de_datos/AccesoDatos.php";
class Cliente
{
    public $id;
    public $nombre;
    public $usuario;
    public $clave;
    public $estado;//activo/borrado

    public function mostrarDatos()
    {
        return json_encode(array("ID" => $this->id, "NOMBRE" => $this->nombre, "USUARIO" => $this->usuario, "CLAVE" => $this->clave, "ESTADO" => $this->estado));
    }

    public static function ValidarDatos($nombre, $usuario, $clave)
    {
        if( (strlen($nombre) > 0 && strlen($nombre)  <= 50) && 
        (strlen($usuario) > 0 && strlen($usuario) <= 50) &&
        (strlen($clave) > 0 && strlen($clave) <= 50))
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    public function InsertarElClienteParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into clientes (nombre,usuario,clave,estado)values(:nombre,:usuario,:clave,:estado)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_INT);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerTodosLosClientes()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id, nombre,apellido,usuario,clave,estado from clientes where estado != 'borrado'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "cliente");
    }


    public static function TraerUnCliente($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,nombre,usuario,clave,estado from clientes where id = :id and estado != 'borrado'");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        $clienteBuscado = $consulta->fetchObject('cliente');
        return $clienteBuscado;
    }

    public static function TraerUnClientePorUsuario($usuario)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,usuario,nombre,clave,estado from clientes where usuario = :usuario and estado != 'borrado'");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();   
        $clienteBuscado = $consulta->fetchObject('cliente');
        return $clienteBuscado;
    }

    public static function TraerUnClientePorUsuarioClave($usuario, $clave)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,usuario,nombre,clave,estado from clientes where usuario = :usuario and clave = :clave and estado != 'borrado'");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->execute();   
        $clienteBuscado = $consulta->fetchObject('cliente');
        return $clienteBuscado;
    }


    public function ModificarClienteParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				update clientes 
				set nombre=:nombre,
				usuario=:usuario,
                clave=:clave,
                estado=:estado
				WHERE id=:id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        return $consulta->execute();
    }
}
?>