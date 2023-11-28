<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
require __DIR__ . '/../app/middlewares/CamposMiddleware.php';
require __DIR__ . '/../app/middlewares/AuthJWTMiddleware.php';
require __DIR__ . '/../app/middlewares/TraerDataJWTMiddleware.php';
require __DIR__ . '/../app/utils/AutentificadorJWT.php';
require __DIR__ . '/../vendor/autoload.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// $app->setBasePath('/slim-php-deployment-main/app/');

// Routes

$app->group('/auth', function (RouteCollectorProxy $group) {
    
    $group->post('/loginCliente', function (Request $request, Response $response) { 
        include_once "visor/visorCliente.php";  
        $parametros = $request->getParsedBody();

    $datosLogin = $request->getParsedBody();
    $mensajeRetorno = VisorCliente::loginCliente($datosLogin);
    if($mensajeRetorno["status"] == "OK")
    { 
        $datos = array('idUsuario' => $mensajeRetorno["idCliente"]);
  
        $token = AutentificadorJWT::CrearToken($datos);
        $mensajeRetorno["jwt"] = $token;
    }

    $payload = json_encode($mensajeRetorno); 
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('/loginEmpleado', function (Request $request, Response $response) { 
        include_once "visor/visorEmpleado.php";  
        $parametros = $request->getParsedBody();

    $datosLogin = $request->getParsedBody();
    $mensajeRetorno = VisorEmpleado::loginEmpleado($datosLogin);
    if($mensajeRetorno["status"] == "OK")
    { 
        $datos = array('tipoEmpleado' => $mensajeRetorno["tipoEmpleado"]);
  
        $token = AutentificadorJWT::CrearToken($datos);
        $mensajeRetorno["jwt"] = $token;
    }
    
    $payload = json_encode($mensajeRetorno);
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
    });
  })->add(new CamposMiddleware(["usuarioLogin", "claveLogin"]));



$app->group('/cliente', function (RouteCollectorProxy $group)
{
    include_once "visor/visorCliente.php";
    $group->post('/registro', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorCliente::registroCliente($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["nombre", "usuario", "clave"]));

    $group->delete('/baja', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorCliente::bajaCliente($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());

    $group->put('/modificacion', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorCliente::modificarCliente($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());

    $group->post('/cuenta', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorCliente::pedirCuenta($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->post('/verEstado', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorCliente::verEstadoComanda($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id", "idMesa"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());

});

$app->group("/empleado", function(RouteCollectorProxy $group)
{
    include_once "visor/visorEmpleado.php";
    $group->post('/registro', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorEmpleado::registroEmpleado($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["nombre", "apellido", "usuario", "clave", "tipo"]));
    
    $group->delete('/baja', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorEmpleado::bajaEmpleado($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]));
    
    
    $group->put('/modificacion', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorEmpleado::modificarEmpleado($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]));
    
    $group->get('/listado', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorEmpleado::listadoEmpleado($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["parametro"]));
})->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());


$app->group("/producto", function(RouteCollectorProxy $group)
{
    include_once "visor/visorProducto.php";
    $group->get('/alta', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorProducto::altaProducto($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["nombre", "tipo", "precio"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->delete('/baja', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorProducto::bajaProducto($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->put('/modificacion', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorProducto::modificarProducto($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->get('/listado', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorProducto::listadoProducto($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["parametro"]));
});

$app->group("/mesa", function(RouteCollectorProxy $group)
{
    include_once "visor/visorMesa.php";
    $group->get('/alta', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorMesa::altaMesa($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["estado"]));
    
    $group->delete('/baja', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorMesa::cierreMesa($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]));
    
    $group->put('/modificacion', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorMesa::modificarMesa($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]));

    $group->get('/listado', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorMesa::listadoMesa($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["parametro"]));
    
    $group->get('/listadoMasUsada', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorMesa::listarMesaMasUsada($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    });
})->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());

$app->group("/comanda", function(RouteCollectorProxy $group)
{
    include_once "visor/visorComanda.php";
    $group->post('/alta', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $archivo = $request->getUploadedFiles();
        $payload = VisorComanda::altaComanda($datos, $archivo);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["detalle", "idMesa", "idCliente"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->post('/alta/foto', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $archivo = $request->getUploadedFiles();
        $payload = VisorComanda::altaFotoComanda($datos, $archivo);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->delete('/baja', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorComanda::bajaComanda($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->put('/modificacion', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorComanda::modificarComanda($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->get('/listado', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorComanda::listadoComanda($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["parametro"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->get('/preparacion', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorComanda::preparacionComanda($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["idProducto", "estado"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->get('/cobrar', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorComanda::cobrarComanda($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
});

$app->group("/encuesta", function(RouteCollectorProxy $group)
{
    include_once "visor/visorEncuesta.php";
    $group->post('/alta', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorEncuesta::altaEncuesta($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["id", "puntosRestaurante", "puntosMesa", "puntosMozo", "puntosCocinero", "experiencia"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());

    $group->get('/mejoresComentarios', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $payload = VisorEncuesta::listarMejoresComentarios($datos);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());

    $group->post('/cargaCsv', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $archivo = $request->getUploadedFiles();
        $payload = VisorEncuesta::cargaCsv($datos, $archivo);
        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new CamposMiddleware(["csv"]))->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());

    $group->get('/descargaCsv', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $archivo = $request->getUploadedFiles();
        $payload = VisorEncuesta::descargaCsv($datos, $archivo);
        if($payload["status"] == "OK")
        {
            $response = $response->withHeader('Content-Type', 'text/csv');
            $response = $response->withHeader('Content-Disposition', 'attachment; filename=archivo.csv');
            $response->getBody()->write(($payload["csv"]));
        }
        else
        {
            $response->getBody()->write(json_encode($payload));
            $response = $response->withHeader('Content-Type', 'application/json');
        }
        return $response;
    });
});


$app->run();