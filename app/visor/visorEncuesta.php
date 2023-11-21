<?php
require_once __DIR__ . "/../controladores/comandaController.php";
require_once __DIR__ . "/../controladores/encuestaController.php";
require_once __DIR__ . "/../models/encuesta.php";

function altaEncuesta($datos)
{
    if(isset($datos["datos"]["idUsuario"]))
    {
        if(isset($datos["id"]) && isset($datos["puntosRestaurante"]) && isset($datos["puntosMesa"]) && isset($datos["puntosMozo"]) && isset($datos["puntosCocinero"]) && isset($datos["experiencia"]))
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
                            $encuesta = new Encuesta($comandaRetornada->nombreCliente, $datos["id"], $datos["puntosRestaurante"], $datos["puntosMesa"], $datos["puntosMozo"], $datos["puntosCocinero"], $datos["experiencia"]);
                            $controladorEncuesta->guardarEncuesta($encuesta);
                            echo json_encode(array("OK" => "Encuesta guardada con exito"));  
                        }
                        else
                        {                    
                            echo json_encode(array("ERROR" => "La comanda ingresada no corresponde a su usuario"));  
                        }
                    }
                    else
                    {
                        echo json_encode(array("ERROR" => "Ya existe una encuesta con ese id"));  
                    }
               }
               else
               {
                    echo json_encode(array("ERROR" => "La comanda no existe o no esta concluida"));
               }
            }
            else
            {
                echo json_encode(array("ERROR" => "Datos invalidos. Los puntajes deben ser del 1 al 10 y la experiencia no debe superar los 66 caracteres"));
            }
        }
        else
        {
            echo json_encode(array("ERROR" => "faltan datos necesarios para el alta de la encuesta. Se requiere id, puntosRestaurante, puntosMesa, puntosMozo, puntosCocinero y experiencia"));
        }
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un cliente"));  
    }
}

function listarMejoresComentarios($datos)
{
    if(isset($datos["datos"]["tipoEmpleado"]))
    {
        if($datos["datos"]["tipoEmpleado"] == "socio")
        {
            $controladorEncuesta = new EncuestaController();
            $encuestasRetornadas = $controladorEncuesta->retornarTodasLasEncuestas();
            $arrayEncuestasobj = array();
            if(!empty($encuestasRetornadas))
            {
                $arrayEncuestasobj;
                for($i=0 ; $i<count($encuestasRetornadas) ; $i++)
                {
                    $encuesta = new Encuesta($encuestasRetornadas[$i][0], $encuestasRetornadas[$i][1], $encuestasRetornadas[$i][2], $encuestasRetornadas[$i][3], $encuestasRetornadas[$i][4], $encuestasRetornadas[$i][5], $encuestasRetornadas[$i][6]);
                    array_push($arrayEncuestasobj, $encuesta);
                }
    
                usort($arrayEncuestasobj, "compararPorSumaDePuntos");
    
                foreach($arrayEncuestasobj as $encuesta)
                {
                    echo $encuesta->mostrarDatos();
                    echo "\n";
                }
            }
            else
            {
                echo json_encode(array("ERROR" => "No hay encuestas"));
            }
    
        }
        else
        {
            echo json_encode(array("ERROR" => "Debe ser socio para listar los mejores comentarios"));
        }
    }
    else
    {
        echo json_encode(array("ERROR" => "El JWT debe corresponder a un empleado"));  
    }
}

function compararPorSumaDePuntos($a, $b) {
    $sumaA = $a->puntosRestaurante + $a->puntosCocinero + $a->puntosMozo + $a->puntosMesa;
    $sumaB = $b->puntosRestaurante + $b->puntosCocinero + $b->puntosMozo + $b->puntosMesa;
    return $sumaB - $sumaA;
}
?>