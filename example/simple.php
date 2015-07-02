<?php

/* Include autoloader */
include "../vendor/autoload.php";

/**
 * Start router
 */
$Router = new Aurora\Router("/home");

$Router->post('/', 'HomeController@index');

$Router->addRoute("GET", '/user/{id}/?{name}', 'UserController@show', "getUser", [
   "id" => "([0-9]++)",
   "name" => "alnum"
]);

$Router->addRoute("GET", '/user/messages/{id}/?{toId}', 'UserController@show', "getMessage");

$Router->findRoute('GET', '/home/user/1');


$UrlHelper = new UrlHelper($routes);
$UrlHelper->get("getUser", ["user" => 3]); #/user/3
$UrlHelper->get("getMessage", ["id" => 3]);
