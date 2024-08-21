<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../app/Config/bootstrap.php';

use FastRoute\RouteCollector;
use App\Graphql\Controller;
use Doctrine\ORM\EntityManagerInterface;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

$graphQLController = new Controller($entityManager);

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($graphQLController) {
    $r->addRoute(['POST', 'OPTIONS'], '/graphql', [$graphQLController, 'mainPage']);
});

$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo $handler($vars);
        break;
}
