<?php
include_once __DIR__ . "/../capa_de_datos/AccesoDatos.php";

class Encuesta
{
    public $nombreCliente;
    public $id;
    public $puntosRestaurante;
    public $puntosMesa;
    public $puntosMozo;
    public $puntosCocinero;
    //todas las puntuaciones del 1 al 10
    public $experiencia;//66 caracteres

    // public function __construct($nombreCliente, $id, $puntosRestaurante, $puntosMesa, $puntosMozo, $puntosCocinero, $experiencia)
    // {
    //     $this->nombreCliente = $nombreCliente;
    //     $this->id = $id;
    //     $this->puntosMesa = $puntosMesa;
    //     $this->puntosRestaurante = $puntosRestaurante;
    //     $this->puntosCocinero = $puntosCocinero;
    //     $this->puntosMozo = $puntosMozo;
    //     $this->experiencia = $experiencia;
    // }

    public function mostrarDatos()
    {
        return array("nombreCliente" => $this->nombreCliente, "idComanda" => $this->id, "puntosRestaurante" => $this->puntosRestaurante, "puntosMesa" => $this->puntosMesa,"puntosMesa" => $this->puntosMozo,"puntosCocinero" => $this->puntosCocinero,"experiencia" => $this->experiencia);
    }

    public static function validarEncuesta($puntosMesa, $puntosRestaurante, $puntosMozo, $puntosCocinero, $experiencia)
    {
        $retorno = false;
        if(($puntosMesa >= 1 && $puntosMesa <= 10) && 
        ($puntosRestaurante >= 1 && $puntosRestaurante <= 10) && 
        ($puntosMozo >= 1 && $puntosMozo <= 10) && 
        ($puntosCocinero >= 1 && $puntosCocinero <= 10))
        {
            if(strlen($experiencia) > 0 && strlen($experiencia) <= 66)
            {
                $retorno = true;
            }
        }
        return $retorno;
    }

    
    public function InsertarLaEncuestaParametros()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("INSERT into encuestas (nombre_cliente,id,puntos_mesa,puntos_restaurante,puntos_cocinero,puntos_mozo,experiencia)values(:nombreCliente,:id,:puntosMesa,:puntosRestaurante,:puntosCocinero,:puntosMozo,:experiencia)");
        $consulta->bindValue(':id', $this->id, PDO::PARAM_STR);
        $consulta->bindValue(':nombreCliente', $this->nombreCliente, PDO::PARAM_STR);
        $consulta->bindValue(':puntosRestaurante', $this->puntosRestaurante, PDO::PARAM_INT);
        $consulta->bindValue(':puntosMesa', $this->puntosMesa, PDO::PARAM_INT);
        $consulta->bindValue(':puntosMozo', $this->puntosMozo, PDO::PARAM_INT);
        $consulta->bindValue(':puntosCocinero', $this->puntosCocinero, PDO::PARAM_INT);
        $consulta->bindValue(':experiencia', $this->experiencia, PDO::PARAM_STR);
        $consulta->execute();
        
        return $this->id;
    }

    public static function TraerTodasLasEncuestas()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,nombre_cliente as nombreCliente,puntos_restaurante as puntosRestaurante,puntos_cocinero as puntosCocinero,puntos_mesa as puntosMesa,puntos_mozo as puntosMozo, experiencia from encuestas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "encuesta");
    }


    public static function TraerUnaEncuesta($id)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("select id,nombre_cliente as nombreCliente,puntos_restaurante as puntosRestaurante,puntos_cocinero as puntosCocinero,puntos_mesa as puntosMesa,puntos_mozo as puntosMozo,experiencia from encuestas where id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        $mesaBuscada = $consulta->fetchObject('encuesta');
        return $mesaBuscada;
    }

    public static function LimpiarTabla()
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDato->RetornarConsulta("DELETE FROM encuestas");
        return $consulta->execute();
    }
}
?>