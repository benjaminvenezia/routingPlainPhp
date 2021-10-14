<?php


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
    ['name' => 'World', 'controller' => 'App\Controller\HelloController@sayHello'],
);

$listRoute = new Route('/', ['controller' => 'App\Controller\TaskController@index']); //1.object 2. nom de la méthode 

$createRoute = new Route(
    '/create', 
    ['controller' => 'App\Controller\TaskController@create'], 
    [], 
    [], 
    'localhost', 
    ['http', 'https'], 
    ['POST', 'GET']
); 

$showRoute = new Route('/show/{id<\d+>?100}', 
['controller' => 'App\Controller\TaskController@show']
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

    $controller = $currentRoute['controller'];

    $currentRoute['generator'] = $generator;

    $className = substr($controller, 0, strpos($controller, '@'));
    
    $methodName = substr($controller, strpos($controller, '@') + 1);

    $instance = new $className();

    call_user_func([$instance, $methodName], $currentRoute);
    
} catch(ResourceNotFoundException $e){
    require 'pages/404.html.php';
    return;
}





