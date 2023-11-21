<?php
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

    public function __construct($nombreCliente, $id, $puntosRestaurante, $puntosMesa, $puntosMozo, $puntosCocinero, $experiencia)
    {
        $this->nombreCliente = $nombreCliente;
        $this->id = $id;
        $this->puntosMesa = $puntosMesa;
        $this->puntosRestaurante = $puntosRestaurante;
        $this->puntosCocinero = $puntosCocinero;
        $this->puntosMozo = $puntosMozo;
        $this->experiencia = $experiencia;
    }

    public function mostrarDatos()
    {
        return json_encode(array("Nombre Cliente" => $this->nombreCliente, "ID Comanda" => $this->id, "Pts. Restaurante" => $this->puntosRestaurante, "Pts. Mesa" => $this->puntosMesa,"Pts. Mozo" => $this->puntosMozo,"Pts. Cocinero" => $this->puntosCocinero,"Experiencia" => $this->experiencia));
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

}
?>