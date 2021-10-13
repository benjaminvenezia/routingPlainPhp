<?php

use App\Controller\HelloController;
use App\Controller\TaskController;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

require __DIR__ . '/vendor/autoload.php';


//ROUTES
$helloRoute = new Route(
    '/hello/{name}', 
    ['name' => 'World', 'controller' => [new HelloController, 'sayHello']],
);

$listRoute = new Route('/', ['controller' => [new TaskController, 'index']]); //1.object 2. nom de la méthode 

$createRoute = new Route(
    '/create', 
    ['controller' => [new TaskController, 'create']], 
    [], 
    [], 
    'localhost', 
    ['http', 'https'], 
    ['POST', 'GET']
); 

$showRoute = new Route('/show/{id<\d+>?100}', 
['controller' => [new TaskController, 'show']]
); // revient à écrire '/show/{id}', ['id' => 100], ['id' => '\d+']);

//AJOUT A LA COLLECTION 
$collection = new RouteCollection();
$collection->add('list', $listRoute);
$collection->add('create', $createRoute);
$collection->add('show', $showRoute);
$collection->add('hello', $helloRoute);

//DECLARATION DU MATCHER ET GENERATOR
$matcher = new UrlMatcher($collection, new RequestContext('', $_SERVER['REQUEST_METHOD']));
$generator = new UrlGenerator($collection, new RequestContext());

//PATH INFO
$pathInfo = $_SERVER['PATH_INFO'] ?? '/';


try {
    $currentRoute = $matcher->match($pathInfo);

    dump($currentRoute); //['_route => 'hello', 'name' => 'World', 'controller' => '$callable']

    $controller = $currentRoute['controller'];

    call_user_func($controller, $currentRoute);
    
    $page = $currentRoute['_route'];
    require_once "pages/$page.php";
} catch(ResourceNotFoundException $e){
    require 'pages/404.php';
    return;
}





