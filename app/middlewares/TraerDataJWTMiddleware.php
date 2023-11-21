<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class TraerDataJWTMiddleware
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
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        
        try 
        {
            $existingData = $request->getParsedBody();
            $newData = array('datos' => AutentificadorJWT::ObtenerData($token));
            $payload = json_encode(array_merge($existingData, $newData));
            $request = $request->withParsedBody(json_decode($payload, true));
            $response = $handler->handle($request);
        } 
        catch (Exception $e) 
        {
            $existingData = $request->getParsedBody();
            $newData = array('error' => $e->getMessage());
            $payload = json_encode(array_merge($existingData, $newData));
            $request = $request->withParsedBody(json_decode($payload, true));
            $response = $handler->handle($request);
        }
        
        return $response->withHeader('Content-Type', 'application/json');
    }




}