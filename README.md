# Aurora.Router

[![Latest version][ico-version]][link-packagist]
[![Software License][ico-license]][link-license]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/65fd53d1-220a-438e-9c80-e61011db14fe/small.png)](https://insight.sensiolabs.com/projects/65fd53d1-220a-438e-9c80-e61011db14fe)

## Requirements

`Router` requires PHP 5.4+

## Installation

The supported way of installing this is via `composer`.

```sh
$ composer require --prefer-dist aurora-framework/router
```

##Basic usage:


```php
$Router = new Aurora\Router();
$Router->setBaseUri("/"); //By default /

$Router->get("/", ["App\Presenter\Home", "index"]);
$Router->get("/([0-9]+)", ["App\Presenter\Post", "view"]);
$Router->post("/([0-9]+)", ["App\Controller\Post", "add"]);
$Router->delete("/([0-9]+)", ["App\Controller\Post", "delete"]);
$Router->map(["GET", "POST"], "/article", function($method, /* parameters */){
	echo $method;
});

$callback = $Router->dispatch(); // returns array
```

This router uses regex, you can have optional parameters too.
By default routes aren't lower cased, you can achieve this with


```php
$Router->useLowerCase();
#or
$Router->lowerCase = true;
```
## Mounting routes (nesting)

```php
/* Example of mount */
$Router->mount("/api", function() use ($Router) {
	$Router->get("/article", ["App\Controller\Article", "overview"]);
	$Router->post("/article", ["App\Controller\Article", "add"]);
	/* Another Mount */
});

```

## API

```php
	/**
	* Constructor
	* @param string $method  Request method
	* @param string $baseUri Base uri
	*/
	public function __construct($method = null, $baseUri = null, $currentUri = null);

	/**
	* Show all all routes
	* @return array Returns array of routes
	*/
	public function getRoutes();

	/**
	* Match types are used to replace given search with replace
	* @param array|string $matchTypes Array containg search and replace
	*/
	public function setMatchTypes($matchTypes);

	/**
	* Set the base uri to given string
	* @param string $baseUri Base uri used to trim from currentUri
	*/
	public function setBaseUri($baseUri);

	/**
	* Mapper for routes
	* @throws \Exception If $methods variable is not an array
	* @param  array 				$methods Methods for given route
	* @param  string 			$pattern Patter for matching route
	* @param  callable|array 	$callback Callback for route
	*/
	public function map($methods, $pattern, $callback);

	/**
	* Add multiple routes at once
	* @throws \Exception Throws exception if parameter $routes is not an array
	* @param array $routes Routes to be added
	*/
	public function addRoutes($routes);

	/**
	* Add a single route
	* @param string $method   Method for route
	* @param string $pattern  Patter for route
	* @param array|callable $callable Callable for route
	*/
	public function addRoute($method, $pattern, $callable);

	/**
	* Shorthand for addRoute, adds GET route
	* @param  string $pattern  Patter used to match the route
	* @param  array|callable $callable Callable for route
	*/
	public function get($pattern, $callable);

	/**
	* Shorthand for addRoute, adds POST route
	* @param  string $pattern  Patter used to match the route
	* @param  array|callable $callable Callable for route
	*/
	public function post($pattern, $callable);

	/**
	* Shorthand for addRoute, adds PATCH route
	* @param  string $pattern  Patter used to match the route
	* @param  array|callable $callable Callable for route
	*/
	public function patch($pattern, $callable);

	/**
	* Shorthand for addRoute, adds DELETE route
	* @param  string $pattern  Patter used to match the route
	* @param  array|callable $callable Callable for route
	*/
	public function delete($pattern, $callable);

	/**
	* Shorthand for addRoute, adds PUT route
	* @param  string $pattern  Patter used to match the route
	* @param  array|callable $callable Callable for route
	*/
	public function put($pattern, $callable);

	/**
	* Shorthand for addRoute, adds OPTIONS route
	* @param  string $pattern  Patter used to match the route
	* @param  array|callable $callable Callable for route
	*/
	public function options($pattern, $callable);

	/**
	* Mount multiple routes via callabck
	* @param  string $baseroute Base route for all routes in given callback
	* @param  callable $callable  Callable to execute
	*/
	public function mount($baseroute, $callable);

	/**
	* Automatically find the request method
	* @return string Found method
	*/
	public function findRequestMethod();

	/**
	* Returns request method stored in router
	* @return string Returns set request method
	*/
	public function getRequestMethod();

	/**
	* Set the request method, automatically uppercased
	* @param string $method Request method
	*/
	public function setRequestMethod($method);

	/**
	* Run found route with befores
	* @return mixed Who knows?
	*/
	public function run();

	/**
	* Dispatch, found the route
	* @return array Matched route
	*/
	public function dispatch();

	/**
	* Set the callable for not found
	* @param  callable|array $callable Callable
	*/
	public function set404($callable);

	/**
	* Lowecase all routes?
	*/
	public function useLowerCase();

	/**
	* Automatically find the current uri using baseuri
	* @return string Returns found uri
	*/
	public function findCurrentUri();

	/**
	* Set current uri
	* @param string $uri Uri
	* @return string Returns uri
	*/
	public function setCurrentUri($uri);

	/**
	* Returns URI
	* @return string Uri
	*/
	public function getCurrentUri();
```

## License

MIT LICENSE
Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

[ico-version]: https://img.shields.io/packagist/v/aurora-framework/router.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-scrutinizer]: https://scrutinizer-ci.com/g/Aurora-Framework/Router/badges/quality-score.png?b=master&style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/aurora-framework/router.svg?style=flat-square
[ico-downloads]:https://img.shields.io/packagist/dt/aurora-framework/router.svg?style=flat-square
[ico-travis]:https://travis-ci.org/Aurora-Framework/Router.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/aurora-framework/router
[link-license]: #License
[link-travis]: https://travis-ci.org/Aurora-Framework/Router
[link-scrutinizer]: https://scrutinizer-ci.com/g/aurora-framework/router/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/aurora-framework/router/
[link-downloads]: https://packagist.org/packages/aurora-framework/router
