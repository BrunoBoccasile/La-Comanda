<?php
require_once __DIR__ . '/../models/encuesta.php';
require_once __DIR__ . '/../capa_de_datos/ManejadorArchivos.php';

class EncuestaController
{
    private static $rutaEncuesta =__DIR__ . '/../csv/encuestas.csv';
    


    public function guardarEncuesta($encuesta)
    {
        $manejadorArchivos = new ManejadorArchivos(self::$rutaEncuesta);
        $arrayEncuestas = $manejadorArchivos->leer();
        array_push($arrayEncuestas, get_object_vars($encuesta));
        $manejadorArchivos->guardar($arrayEncuestas);
    }

    public function retornarEncuesta($id)
    {
        $retorno = false;
        $manejadorArchivos = new ManejadorArchivos(self::$rutaEncuesta);
        $arrayEncuestas = $manejadorArchivos->leer();
        if(!empty($arrayEncuestas))
        {  
            foreach($arrayEncuestas as $e)
            {
                if(($id == $e[1]))
                {
                    $retorno = $e;
                    break;
                }
            }
        }

        return $retorno;
    }

    public function retornarTodasLasEncuestas()
    {
        $retorno = false;
        $manejadorArchivos = new ManejadorArchivos(self::$rutaEncuesta);
        $arrayEncuestas = $manejadorArchivos->leer();

        return $arrayEncuestas;
    }
}
?>