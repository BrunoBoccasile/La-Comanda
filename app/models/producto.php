<?php
include_once __DIR__ . "/../capa_de_datos/AccesoDatos.php";
class Producto
{
    public $id;
    public $nombre;
    public $tipo;
    public $precio;
    public $estado;

    public function mostrarDatos()
    {
        return json_encode(array("ID" => $this->id, "NOMBRE" => $this->nombre, "TIPO" => $this->tipo, "PRECIO" => $this->estado));
    }

    public static function ValidarDatos($nombre, $tipo, $precio)
    {
        if( (strlen($nombre) > 0 && strlen($nombre) <= 50) && 
        ($precio > 0 && $precio < 10000000) &&
        ($tipo == "trago" || $tipo == "cerveza" || $tipo == "comida" || $tipo == "postre"))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function InsertarElProducto()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into menu (nombre,tipo,precio,estado)values('$this->nombre','$this->tipo','$this->precio','$this->estado')");
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public function InsertarElProductoParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into menu (nombre,tipo,precio,estado)values(:nombre,:tipo,:precio,:estado)");
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerTodosLosProductos()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,nombre,tipo,precio,estado from menu where estado != 'borrado'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "producto");
    }


    public static function TraerUnProducto($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,nombre as nombre, tipo as tipo,precio as precio,estado as estado from menu where id = $id and estado != 'borrado'");
        $consulta->execute();
        $productoBuscado = $consulta->fetchObject('producto');
        return $productoBuscado;
    }

    public static function TraerUnProductoPorNombre($nombre)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,nombre as nombre, tipo as tipo,precio as precio,estado as estado from menu where nombre = '" . $nombre . "'");
        $consulta->execute();
        $productoBuscado = $consulta->fetchObject('producto');
        return $productoBuscado;
    }
    public function ModificarProducto()
    {

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				update menu 
				set nombre='$this->nombre',
				tipo='$this->tipo',
				precio='$this->precio',
                estado='$this->estado'
                WHERE id='$this->id'");
        return $consulta->execute();
    }

    public function ModificarProductoParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				update menu 
				set nombre=:nombre,
				tipo=:tipo,
				precio=:precio,
                estado=:estado
                WHERE id=:id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_STR);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public function BorrarProducto()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				delete 
				from menu				
				WHERE id=:id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }
}
?>