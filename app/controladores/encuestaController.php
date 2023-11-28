<?php
require_once __DIR__ . '/../models/encuesta.php';
require_once __DIR__ . '/../capa_de_datos/ManejadorArchivos.php';
require_once __DIR__ . '/../controladores/clienteController.php';

class EncuestaController
{
    
    public function guardarEncuesta($nombreCliente, $id, $puntosRestaurante, $puntosMesa, $puntosMozo, $puntosCocinero, $experiencia)
    {
        $encuesta = new Encuesta();
        $encuesta->nombreCliente = $nombreCliente;
        $encuesta->id = $id;
        $encuesta->puntosRestaurante = $puntosRestaurante;
        $encuesta->puntosMesa = $puntosMesa;
        $encuesta->puntosMozo = $puntosMozo;
        $encuesta->puntosCocinero = $puntosCocinero;
        $encuesta->experiencia = $experiencia;
        return $encuesta->InsertarLaEncuestaParametros();
    }

    public function retornarEncuesta($id)
    {
        return Encuesta::TraerUnaEncuesta($id);
    }

    public function listarMejoresEncuestas()
    {
        $retorno = false;
        $encuestasRetornadas = Encuesta::TraerTodasLasEncuestas();
        $arrayEncuestasobj = array();
        if($encuestasRetornadas != false)
        {
            usort($encuestasRetornadas, "compararPorSumaDePuntos");
            $retorno = $encuestasRetornadas;
        }

        return $retorno;
    }

    public function listarEncuestas()
    {
        return Encuesta::TraerTodasLasEncuestas();
    }

    // llamar a metodo que guarde el archivo en la ruta csv, lo lea, valide la informacion, limpie la bdd y cargue a la bdd
    public function cargaCsvABaseDeDatos($csv)
    {
        $retorno = true;
        //guardar
        $rutaDestino = __DIR__ . '/../csv/encuestas.csv';
        {  
            $csv->moveTo($rutaDestino);
        }
        //leer
        $manejadorArchivos = new ManejadorArchivos($rutaDestino);
        $arrayEncuestas = $manejadorArchivos->leer();
        foreach($arrayEncuestas as $e)
        {
            if(!self::validarCsvEncuesta($e))
            {
                $retorno = false;
                break;
            }
        }
        //subir a BDD
        if($retorno)
        {
            Encuesta::LimpiarTabla();
            foreach($arrayEncuestas as $e)
            {
                self::guardarEncuesta($e[0], $e[1], $e[2], $e[3], $e[4], $e[5], $e[6]);
            }
        }
        
        return $retorno;
    }

    private static function validarCsvEncuesta($arrayEncuesta)
    {
        $controladorCliente = new ClienteController();
        // 0 nombre_cliente
        // 1 id
        // 2 puntos_restaurante
        // 3 puntos_mesa
        // 4 puntos_mozo
        // 5 puntos_cocinero
        // 6 experiencia



        if(count($arrayEncuesta) == 7 && 
        (strlen($arrayEncuesta[0]) > 0 && strlen($arrayEncuesta[0]) <= 50) && 
            (strlen($arrayEncuesta[1]) > 0 && strlen($arrayEncuesta[1]) <= 5) &&
            Encuesta::validarEncuesta($arrayEncuesta[3], $arrayEncuesta[2], $arrayEncuesta[4], $arrayEncuesta[5], $arrayEncuesta[6]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    // public function guardarEncuesta($encuesta)
    // {
    //     $manejadorArchivos = new ManejadorArchivos(self::$rutaEncuesta);
    //     $arrayEncuestas = $manejadorArchivos->leer();
    //     array_push($arrayEncuestas, get_object_vars($encuesta));
    //     $manejadorArchivos->guardar($arrayEncuestas);
    // }

    // public function retornarEncuesta($id)
    // {
    //     $retorno = false;
    //     $manejadorArchivos = new ManejadorArchivos(self::$rutaEncuesta);
    //     $arrayEncuestas = $manejadorArchivos->leer();
    //     if(!empty($arrayEncuestas))
    //     {  
    //         foreach($arrayEncuestas as $e)
    //         {
    //             if(($id == $e[1]))
    //             {
    //                 $retorno = $e;
    //                 break;
    //             }
    //         }
    //     }

    //     return $retorno;
    // }

    // public function retornarTodasLasEncuestas()
    // {
    //     $retorno = false;
    //     $manejadorArchivos = new ManejadorArchivos(self::$rutaEncuesta);
    //     $arrayEncuestas = $manejadorArchivos->leer();

    //     return $arrayEncuestas;
    // }
}
function compararPorSumaDePuntos($a, $b) 
{
    $sumaA = $a->puntosRestaurante + $a->puntosCocinero + $a->puntosMozo + $a->puntosMesa;
    $sumaB = $b->puntosRestaurante + $b->puntosCocinero + $b->puntosMozo + $b->puntosMesa;
    return $sumaB - $sumaA;
}
?>