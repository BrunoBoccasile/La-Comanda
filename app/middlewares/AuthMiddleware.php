<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once __DIR__ . '/../visor/visorEmpleado.php';

class AuthMiddleware
{
    /**
     * Example middleware invokable class
     *
     * @param  ServerRequest  $request PSR-7 request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {   
        $datos = $request->getQueryParams();


        if (loginEmpleado($datos)) {
            $response = $handler->handle($request);
        } else {
            $response = new Response();
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}