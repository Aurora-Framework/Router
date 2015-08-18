<?php

/* Include autoloader */
include "../vendor/autoload.php";

/**
 * Start router
 */
use Aurora\Router;
use Aurora\Helper\Url;
use Aurora\Router\Route;

$Route = new Route($baseUri);
$Route->setNamespace("App\Controller\\");
$Router = new Router("/home", $Route);
//
// $Router->get('/{presenter}/{action}/{parameters?}', null, [
//     "before" => function($Route) {
//         return $Route;
//     },
//     "after" => function($Route) {
//         return $Route;
//     },
// ])->where(["parameters" => "(.*)"]);

$Router->get('/{id}', 'HomeController@index', ["name" => "profile"])
    ->where(["id" => "(@[0-9A-Za-z]++)"]);
//
$Router->get('/user/{id}', 'UserController@show');
// $Router->post('/user/{id}', 'UserController@save');
//
// $Router->get('/user/{id}/{name?}', 'UserController@show');
// $Router->get('/user/{id}/messages/{id}', 'UserController@show');
// $Router->get('/user/{id}/messages/delete/{id}', 'UserController@show');

// $Router->findRoute('GET', '/home/profile/Tom/dasdasd/dsaads/sdfasdf');
var_dump($Router->findRoute('GET', '/home/@John/'));

$UrlHelper = new Url($Router);
echo $UrlHelper->get("profile", ["id" => "@John"]); #/user/3
