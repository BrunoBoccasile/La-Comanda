<?php
class Encuesta
{
    public $puntosMesa;
    public $puntosRestaurante;
    public $puntosMozo;
    public $puntosCocinero;
    //todas las puntuaciones del 1 al 10
    public $experiencia;//66 caracteres

    public function __construct($puntosMesa, $puntosRestaurante, $puntosMozo, $puntosCocinero, $experiencia)
    {
        $this->puntosMesa = $puntosMesa;
        $this->puntosRestaurante = $puntosRestaurante;
        $this->puntosMozo = $puntosMozo;
        $this->experiencia = $experiencia;
    }
}
?>