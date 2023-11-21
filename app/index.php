<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
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
    $retornoLogin = loginCliente($datosLogin);
      if($retornoLogin != false){ 
        $datos = array('idUsuario' => $retornoLogin);
  
        $token = AutentificadorJWT::CrearToken($datos);
        $payload = json_encode(array('JWT' => $token));
        $response->getBody()->write($payload);
      }
  
      return $response
        ->withHeader('Content-Type', 'application/json');
    });

    $group->post('/loginEmpleado', function (Request $request, Response $response) { 
        include_once "visor/visorEmpleado.php";  
        $parametros = $request->getParsedBody();

    $datosLogin = $request->getParsedBody();
    $retornoLogin = loginEmpleado($datosLogin);
      if($retornoLogin != false){ 
        $datos = array('tipoEmpleado' => $retornoLogin);
  
        $token = AutentificadorJWT::CrearToken($datos);
        $payload = json_encode(array('JWT' => $token));
        $response->getBody()->write($payload);
      }
  
      return $response
        ->withHeader('Content-Type', 'application/json');
    });
  });



$app->group('/cliente', function (RouteCollectorProxy $group)
{
    include_once "visor/visorCliente.php";
    $group->post('/registro', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        registroCliente($datos);
        return $response->withHeader('Content-Type', 'application/json');
    });
    $group->delete('/baja', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        bajaCliente($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    $group->put('/modificacion', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        modificarCliente($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    $group->post('/cuenta', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        pedirCuenta($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    $group->post('/verEstado', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        verEstadoComanda($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    $group->post('/encuesta', function (Request $request, Response $response, $args) {
        include_once "visor/visorEncuesta.php";
        $datos = $request->getParsedBody();
        altaEncuesta($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
});

$app->group("/empleado", function(RouteCollectorProxy $group)
{
    include_once "visor/visorEmpleado.php";
    $group->post('/registro', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        registroEmpleado($datos);
        return $response->withHeader('Content-Type', 'application/json');
    });
    
    $group->delete('/baja', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        bajaEmpleado($datos);
        return $response->withHeader('Content-Type', 'application/json');
    });
    
    
    $group->put('/modificacion', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        modificarEmpleado($datos);
        return $response->withHeader('Content-Type', 'application/json');
    });
    
    $group->get('/listado', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        listadoEmpleado($datos);
        return $response->withHeader('Content-Type', 'application/json');
    });
})->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());






$app->group("/producto", function(RouteCollectorProxy $group)
{
    include_once "visor/visorProducto.php";
    $group->get('/alta', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        altaProducto($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->delete('/baja', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        bajaProducto($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->put('/modificacion', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        modificarProducto($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->get('/listado', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        listadoProducto($datos);
        return $response->withHeader('Content-Type', 'application/json');
    });
});

$app->group("/mesa", function(RouteCollectorProxy $group)
{
    include_once "visor/visorMesa.php";
    $group->get('/alta', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        altaMesa($datos);
        return $response->withHeader('Content-Type', 'application/json');
    });
    
    $group->delete('/baja', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        cierreMesa($datos);
        return $response->withHeader('Content-Type', 'application/json');
    });
    
    $group->put('/modificacion', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        modificarMesa($datos);
        return $response->withHeader('Content-Type', 'application/json');
    });
    
    $group->get('/listado', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        listadoMesa($datos);
        return $response->withHeader('Content-Type', 'application/json');
    });
    
    $group->get('/listadomasusada', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        listarMesaMasUsada($datos);
        return $response->withHeader('Content-Type', 'application/json');
    });
})->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());

$app->group("/comanda", function(RouteCollectorProxy $group)
{
    include_once "visor/visorComanda.php";
    $group->post('/alta', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $archivo = $request->getUploadedFiles();
        altaComanda($datos, $archivo);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->post('/alta/foto', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        $archivo = $request->getUploadedFiles();
        altaFotoComanda($datos, $archivo);
        return $response->withHeader('Content-Type', 'application/json');
    });
    
    $group->delete('/baja', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        bajaComanda($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->put('/modificacion', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        modificarComanda($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->get('/listado', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        listadoComanda($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->get('/preparacion', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        preparacionComanda($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
    
    $group->get('/cobrar', function (Request $request, Response $response, $args) {
        $datos = $request->getParsedBody();
        cobrarComanda($datos);
        return $response->withHeader('Content-Type', 'application/json');
    })->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());
});



$app->get('/listado/mejorescomentarios', function (Request $request, Response $response, $args) {
    $datos = $request->getParsedBody();
    include_once "visor/visorEncuesta.php";
    listarMejoresComentarios($datos);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TraerDataJWTMiddleware())->add(new AuthJWTMiddleware());

$app->run();