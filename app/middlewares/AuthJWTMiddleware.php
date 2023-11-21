<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class AuthJWTMiddleware
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
        try {
            if (!$request->hasHeader('Authorization')) {
                throw new Exception();
            }

            $header = $request->getHeaderLine('Authorization');
    
            if (empty($header)) {
                throw new Exception();
            }
    
            $token = trim(explode("Bearer", $header)[1]);
    
            AutentificadorJWT::VerificarToken($token);
    
            $response = $handler->handle($request);
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('ERROR' => "Hubo un error con el token"));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public static function verificarToken(Request $request, RequestHandler $handler): Response
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        try {
            AutentificadorJWT::VerificarToken($token);
            $response = $handler->handle($request);
        } catch (Exception $e) {
            $response = new Response();
            $payload = json_encode(array('mensaje' => 'ERROR: Hubo un error con el TOKEN'));
            $response->getBody()->write($payload);
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}