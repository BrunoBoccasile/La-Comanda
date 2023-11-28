<?php
require_once __DIR__ . "/../controladores/comandaController.php";
require_once __DIR__ . "/../controladores/encuestaController.php";
require_once __DIR__ . "/../models/encuesta.php";

class VisorEncuesta
{
    static function altaEncuesta($datos)
    {
        if(isset($datos["datos"]["idUsuario"]))
        {
            if(Encuesta::ValidarEncuesta($datos["puntosRestaurante"], $datos["puntosMesa"], $datos["puntosMozo"], $datos["puntosCocinero"], $datos["experiencia"]))
            {
               $controladorComanda = new ComandaController();
               $comandaRetornada = $controladorComanda->buscarComandaConcluidaPorId($datos["id"]);
               if($comandaRetornada != false)
               {
                    $controladorEncuesta = new EncuestaController();
                    if(!$controladorEncuesta->retornarEncuesta($datos["id"]))
                    {
                        if($comandaRetornada->idCliente == $datos["datos"]["idUsuario"])
                        {
                            $controladorEncuesta->guardarEncuesta( $comandaRetornada->nombreCliente, $datos["id"], $datos["puntosRestaurante"], $datos["puntosMesa"], $datos["puntosMozo"], $datos["puntosCocinero"], $datos["experiencia"]);
                            $arrayRespuesta["status"] = "OK";
                            $arrayRespuesta["message"] = "Encuesta guardada con exito";
                        }
                        else
                        {                    
                            $arrayRespuesta["status"] = "ERROR";
                            $arrayRespuesta["message"] = "La comanda ingresada no corresponde a su usuario";
                        }
                    }
                    else
                    {
                        $arrayRespuesta["status"] = "ERROR";
                        $arrayRespuesta["message"] = "Ya existe una encuesta con ese id";
                    }
                }
                else
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "La comanda no existe o no esta concluida";
                }
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Datos invalidos. Los puntajes deben ser del 1 al 10 y la experiencia no debe superar los 66 caracteres";
            }

        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un cliente";
        }
        return $arrayRespuesta;
    }
    
    static function listarMejoresComentarios($datos)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $controladorEncuesta = new EncuestaController();
                $encuestasRetornadas = $controladorEncuesta->listarMejoresEncuestas();
                if($encuestasRetornadas != false)
                {
                    $arrayRespuesta["status"] = "OK";
                    $arrayRespuesta["message"] = "Listado realizado con exito";  
                    $arrayListado = array();
                    foreach($encuestasRetornadas as $encuesta)
                    {
                        array_push($arrayListado, $encuesta->mostrarDatos());
                    }
                    $arrayRespuesta["listado"] = $arrayListado;
                }
                else
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "No hay encuestas";        
                }
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser socio para listar los mejores comentarios";
            }
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }

    static function cargaCsv($datos, $archivo)
    {
        if(isset($datos["datos"]["tipoEmpleado"]))
        {
            if($datos["datos"]["tipoEmpleado"] == "socio")
            {
                $archivo = $archivo["csv"];
                $tipoArchivo = $archivo->getClientMediaType();

                if ($tipoArchivo == "text/csv") 
                {
                    $controladorEncuesta = new EncuestaController();
                    if($controladorEncuesta->cargaCsvABaseDeDatos($archivo))
                    {
                        $arrayRespuesta["status"] = "OK";
                        $arrayRespuesta["message"] = "Archivo cargado con exito";
                    }
                    else
                    {
                        $arrayRespuesta["status"] = "ERROR";
                        $arrayRespuesta["message"] = "Formato incorrecto (nombreCliente,id,puntosRestaurante,puntosMesa,puntosMozo,puntosCocinero,experiencia) y/o datos invalidos";
                    }

                } 
                else 
                {
                    $arrayRespuesta["status"] = "ERROR";
                    $arrayRespuesta["message"] = "El archivo debe ser un archivo CSV";
                }
            }
            else
            {
                $arrayRespuesta["status"] = "ERROR";
                $arrayRespuesta["message"] = "Debe ser socio para cargar un CSV";
            } 

        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "El JWT debe corresponder a un empleado";
        }
        return $arrayRespuesta;
    }

    static function descargaCsv($datos)
    {
        $controladorEncuesta = new EncuestaController();
        $encuestasRetornadas = $controladorEncuesta->listarEncuestas();
        if($encuestasRetornadas)
        {
            $cadenaCsv = "";
            foreach($encuestasRetornadas as $encuesta)
            {
                $cadenaCsv .= implode(',', (array)$encuesta) . "\n";
            }   
            $arrayRespuesta["status"] = "OK";
            $arrayRespuesta["message"] = "Csv descargado";
            $arrayRespuesta["csv"] = $cadenaCsv;
        }
        else
        {
            $arrayRespuesta["status"] = "ERROR";
            $arrayRespuesta["message"] = "No hay encuestas";
        }
        return $arrayRespuesta;
    }

}

?>