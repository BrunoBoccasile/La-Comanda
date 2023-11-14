<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
require __DIR__ . '/../app/middlewares/AuthMiddleware.php';
require __DIR__ . '/../app/middlewares/TipoLogeadoMiddleware.php';
require __DIR__ . '/../vendor/autoload.php';

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// $app->setBasePath('/slim-php-deployment-main/app/');

// Routes

$app->post('/empleado/registro', function (Request $request, Response $response, $args) {
    $datos = $request->getParsedBody();
    $login = $request->getQueryParams();
    include_once "visor/visorEmpleado.php";
    registroEmpleado($datos, $login);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->delete('/empleado/baja', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    $login = $request->getQueryParams();
    include_once "visor/visorEmpleado.php";
    bajaEmpleado($datos, $login);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->put('/empleado/modificacion', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    $login = $request->getQueryParams();
    include_once "visor/visorEmpleado.php";
    modificarEmpleado($datos, $login);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->get('/empleado/listado', function (Request $request, Response $response, $args) {
    $datos = $request->getQueryParams();
    include_once "visor/visorEmpleado.php";
    listadoEmpleado($datos);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->get('/producto/alta', function (Request $request, Response $response, $args) {
    $datos = $request->getQueryParams();
    include_once "visor/visorProducto.php";
    altaProducto($datos);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->delete('/producto/baja', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    $login = $request->getQueryParams();
    include_once "visor/visorProducto.php";
    bajaProducto($datos, $login);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->put('/producto/modificacion', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    $login = $request->getQueryParams();
    include_once "visor/visorProducto.php";
    modificarProducto($datos, $login);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

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
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->delete('/mesa/baja', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    $login = $request->getQueryParams();
    include_once "visor/visorMesa.php";
    cierreMesa($datos, $login);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->put('/mesa/modificacion', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    $login = $request->getQueryParams();
    include_once "visor/visorMesa.php";
    modificarMesa($datos, $login);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->get('/mesa/listado', function (Request $request, Response $response, $args) {
    $datos = $request->getQueryParams();
    include_once "visor/visorMesa.php";
    listadoMesa($datos);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/comanda/alta', function (Request $request, Response $response, $args) {
    $datos = $request->getParsedBody();
    $login = $request->getQueryParams();
    $archivo = $request->getUploadedFiles();
    include_once "visor/visorComanda.php";
    altaComanda($datos, $login, $archivo);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->delete('/comanda/baja', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    $login = $request->getQueryParams();
    include_once "visor/visorComanda.php";
    bajaComanda($datos, $login);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->put('/comanda/modificacion', function (Request $request, Response $response, $args) {
    $datos = $request->getBody();
    $datos = json_decode($datos, true);
    $login = $request->getQueryParams();
    include_once "visor/visorComanda.php";
    modificarComanda($datos, $login);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->get('/comanda/listado', function (Request $request, Response $response, $args) {
    $datos = $request->getQueryParams();
    include_once "visor/visorComanda.php";
    listadoComanda($datos);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->get('/comanda/preparacion', function (Request $request, Response $response, $args) {
    $datos = $request->getQueryParams();
    include_once "visor/visorComanda.php";
    preparacionComanda($datos);
    return $response->withHeader('Content-Type', 'application/json');
})->add(new TipoLogeadoMiddleware())->add(new AuthMiddleware());

$app->run();
