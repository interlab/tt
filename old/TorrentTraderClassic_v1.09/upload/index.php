<?php

require_once __DIR__ . '/libs/vendor/autoload.php';

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

$context = new RequestContext();
$context->fromRequest(Request::createFromGlobals());

$route_index = new Route('/', ['_controller' => 'MyController::index']);
$route_any = new Route(
    '/{action}.{ext}',
    ['_controller' => 'MyController::anyaction'],
    ['action' => '[\w-]+', 'ext' => 'php',]
);

$routes = new RouteCollection();
$routes->add('route_index', $route_index);
$routes->add('route_any', $route_any);

$context = new RequestContext();
$context->fromRequest(Request::createFromGlobals());

// $context = new RequestContext('/');

$matcher = new UrlMatcher($routes, $context);

try {
    $parameters = $matcher->match($context->getPathInfo());
    $control = $parameters['_controller'];
    $args = [];
    foreach ($parameters as $key => $row) {
        if (strpos($key, '_') === 0) {
            continue;
        } 
        $args[] = $row;
    }

    $file = call_user_func_array($control, $args);
    require_once __DIR__ . '/backend/functions.php';

    // dump($_REQUEST, $_SERVER);

    require_once $file;


} catch (ResourceNotFoundException $e) {
    die('$route <b>'. $context->getPathInfo() .'</b> not found.');
}

// dump($parameters);

class MyController
{
    public static function index()
    {
        return __DIR__.'/controllers/index.php';
    }

    public static function anyaction($file, $ext)
    {
        $m = ['account', 'admin', 'torrents', 'forum'];
        $path = '';
        foreach ($m as $row) {
            if (strpos($file, $row) === 0) {
                $path = __DIR__ .'/controllers/'.$row.'/' . $file . '.php';
                break;
            }
        }
        if ($path === '') {
            $path = __DIR__.'/controllers/' . $file . '.php';
            if (!file_exists($path)) {
                $path = __DIR__.'/controllers/others/' . $file . '.php';
                // try others variant ...
                if (!file_exists($path)) {
                    throw new ResourceNotFoundException('File '.$file.'.php not found');
                }
            }
        }

        if (!file_exists($path)) {
            throw new ResourceNotFoundException('File '.$file.'.php not found');
        }

        return $path;
    }
}

