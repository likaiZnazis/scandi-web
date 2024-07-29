<?php

require_once __DIR__ . '/../vendor/autoload.php';
// Require entityManager
require_once __DIR__ . '/../app/Config/bootstrap.php';

use FastRoute\RouteCollector;
use App\Graphql\Controller;
use Doctrine\ORM\EntityManagerInterface;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

// Create an instance of the GraphQL controller with the entity manager
$graphQLController = new Controller($entityManager);

// Routes
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($graphQLController) {
    $r->addRoute(['POST','OPTIONS'],'/category/{category_name}', [$graphQLController, 'mainPage']);
    // $r->post('/category/{category_name}/{product_id}', [$graphQLController, 'pdp']);
});

$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        print_r($allowedMethods);
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $handler($vars);
        break;
}
