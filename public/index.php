<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Config/bootstrap.php';
use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use App\Controller\GraphQL;
use Doctrine\ORM\EntityManagerInterface;

// Instantiate the entity manager
// $entityManager = 
// Create an instance of the GraphQL controller with the entity manager
$graphQLController = new GraphQL($entityManager);

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) use ($graphQLController) {
    $r->post('/graphql', [$graphQLController, 'handle']);
});

$routeInfo = $dispatcher->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI']
);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        http_response_code(404);
        echo '404 Not Found';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        echo call_user_func($handler, $vars);
        break;
}
