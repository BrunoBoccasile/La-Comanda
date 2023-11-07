<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// $app->setBasePath('/slim-php-deployment-main/app/');
// Routes
// $app->get('[/]', function (Request $request, Response $response) {
//     $payload = json_encode(array('metodo' => 'GET', 'msg' => "Bienvenido a SlimFramework 2023"));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });


// $app->post('[/]', function (Request $request, Response $response) {
//     $payload = json_encode(array('metodo' => 'POST', 'msg' => "Bienvenido a SlimFramework 2023"));
//     $response->getBody()->write($payload);
//     return $response->withHeader('Content-Type', 'application/json');
// });

$app->post('/empleado/registro', function (Request $request, Response $response, $args) {
    $datos = $request->getParsedBody();
    include_once "visor/visorEmpleado.php";
    registroEmpleado($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/empleado/login', function (Request $request, Response $response, $args) {
    $datos = $request->getParsedBody();
    include_once "visor/visorEmpleado.php";
    loginEmpleado($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->delete('/empleado/baja', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    include_once "visor/visorEmpleado.php";
    bajaEmpleado($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/empleado/modificacion', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    include_once "visor/visorEmpleado.php";
    modificarEmpleado($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/empleado/listado', function (Request $request, Response $response, $args) {
    $datos = $request->getQueryParams();
    include_once "visor/visorEmpleado.php";
    listadoEmpleado($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/producto/alta', function (Request $request, Response $response, $args) {
    $datos = $request->getQueryParams();
    include_once "visor/visorProducto.php";
    altaProducto($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->delete('/producto/baja', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    include_once "visor/visorProducto.php";
    bajaProducto($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/producto/modificacion', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    include_once "visor/visorProducto.php";
    modificarProducto($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/producto/listado', function (Request $request, Response $response, $args) {
    $datos = $request->getQueryParams();
    include_once "visor/visorProducto.php";
    listadoProducto($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/mesa/alta', function (Request $request, Response $response, $args) {
    $datos = $request->getQueryParams();
    include_once "visor/visorMesa.php";
    altaMesa($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->delete('/mesa/baja', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    include_once "visor/visorMesa.php";
    cierreMesa($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/mesa/modificacion', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    include_once "visor/visorMesa.php";
    modificarMesa($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/mesa/listado', function (Request $request, Response $response, $args) {
    $datos = $request->getQueryParams();
    include_once "visor/visorMesa.php";
    listadoMesa($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/comanda/alta', function (Request $request, Response $response, $args) {
    $datos = $request->getQueryParams();
    include_once "visor/visorComanda.php";
    altaComanda($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->delete('/comanda/baja', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    include_once "visor/visorComanda.php";
    bajaComanda($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->put('/comanda/modificacion', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    include_once "visor/visorComanda.php";
    modificarComanda($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->get('/comanda/listado', function (Request $request, Response $response, $args) {
    $datos = $request->getQueryParams();
    include_once "visor/visorComanda.php";
    listadoComanda($datos);
    return $response->withHeader('Content-Type', 'application/json');
});
$app->run();
