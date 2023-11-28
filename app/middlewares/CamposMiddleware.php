<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class CamposMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */

    public $camposRequeridos;
    
    public function __construct($camposRequeridos)
    {
        $this->camposRequeridos = $camposRequeridos;
    }

    public function __invoke(Request $request, RequestHandler $handler)
    {
        $datos = $request->getParsedBody();

        foreach ($this->camposRequeridos as $campo) {
            if (!isset($datos[$campo])) {
                $mensajeError = array("status" => "ERROR", "message" => "Faltan campos necesarios");
                $mensajeError["camposNecesarios"] = implode(",", $this->camposRequeridos);
                $response = new Response();
                $response->getBody()->write(json_encode($mensajeError));
                return $response->withHeader('Content-Type', 'application/json');
            }
        }

        return $handler->handle($request);
        
    }

}