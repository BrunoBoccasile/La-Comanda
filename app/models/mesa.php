<?php
include_once __DIR__ . "/../capa_de_datos/AccesoDatos.php";
class Mesa
{
    public $id;//5 caracteres
    public $estado;//con cliente esperando pedido/con cliente comiendo/con cliente pagando/cerrada

    public function mostrarDatos()
    {
        return $this->id . "  " . $this->estado;
    }

    public static function ValidarDatos($estado)
    {
        if( ($estado == "con cliente esperando pedido" || $estado == "con cliente comiendo" || $estado == "con cliente pagando" || $estado == "disponible"))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function InsertarLaMesa()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into mesas (estado)values('$this->estado')");
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public function InsertarLaMesaParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into mesas (estado)values(:estado)");
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        $consulta->execute();
        return $objetoAccesoDato->RetornarUltimoIdInsertado();
    }

    public static function TraerTodasLasMesas()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,estado from mesas where estado != 'cerrada'");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "mesa");
    }


    public static function TraerUnaMesa($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,estado as estado from mesas where id = $id and estado != 'cerrada'");
        $consulta->execute();
        $mesaBuscada = $consulta->fetchObject('mesa');
        return $mesaBuscada;
    }


    public function ModificarMesa()
    {

        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				update mesas 
				set estado='$this->estado'
                WHERE id='$this->id'");
        return $consulta->execute();
    }

    public function ModificarMesaParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				update mesas 
                set estado=:estado
                WHERE id=:id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
        return $consulta->execute();
    }

    public function BorrarMesa()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("
				delete 
				from mesas				
				WHERE id=:id");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->rowCount();
    }
}
?>