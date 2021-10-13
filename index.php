<?php

use App\Controller\HelloController;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

require __DIR__ . '/vendor/autoload.php';

//ROUTES
$listRoute = new Route('/');

$createRoute = new Route('/create', [], [], [], 'localhost', ['http', 'https'], ['POST', 'GET']); // /index.php?page=create => /create

$showRoute = new Route('/show/{id<\d+>?100}', []); // revient à écrire '/show/{id}', ['id' => 100], ['id' => '\d+']);

$helloRoute = new Route(
    '/hello/{name}', 
    ['name' => 'World', 'toto' => 42],
);

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
    
    $page = $currentRoute['_route'];
    require_once "pages/$page.php";
} catch(ResourceNotFoundException $e){
    require 'pages/404.php';
    return;
}





