<?php

namespace Aurora;

/**
 * Aurora.Router
 *
 * @category   Router
 * @package    Router
 * @author     Ivan Vankov - gatakka
 * @author     VeeeneX <veeenex@gmail.com>
 * @link       https://github.com/gatakka/PGF-Router
 * @copyright  2015 Caroon
 * @license    MIT
 * @version    1.0
 *
 * Aurora.Router is implementation of PGF Router, made by gatakka, with one small,
 * feature to validate parameters with regex.
 * Fast PHP router without regular expressions and support for optional parameters.
 * Routes are converted to tree-like structure so later searches are very effective because
 * complexity is related to the count of request URL segments, not to count of added routes.
 * Usually number of segments in request URL is lower than the count of all routes.
 *
*/

use Aurora\Router\Exception\InvalidMethodException;
use Aurora\Router\Exception\MethodNotAllowedException;
use Aurora\Router\Exception\RouteNotFoundException;
use Aurora\Router\Exception\MatchTypeNotFoundException;

class Router
{
	/**
	 * BaseURI
	 * @var string
	 */
	public $baseUri = "/";

	/**
	 * CaseSensitive?
	 * @var bool
	 */
	public $lowerCase = false;

	/**
	 * Callable for notfound
	 * @var array|callback
	 */
	protected $notFound;

	/**
	 * Current method for dispatching
	 * @var string
	 */
	protected $method;

	/**
	 * Searches used to search match type
	 * @var array
	 */
	protected $matchTypes = [
		'any' => "([^\/]++)",
		'num' => "([0-9]++)",
		'int' => "([0-9]++)",
		'all' => "(.*)",
		'alphanum' => "([0-9A-Za-z]++)",
	];

	/**
	 * Unprocessed routes
	 *
	 * @var array
	 */
	private $rawRoutes = [];

	/**
	 * Tree of all routes
	 *
	 * @var array
	 */
	private $routesTree = null;

	/**
	 * List of allowed Methods
	 *
	 * @var array
	 */
	private $allowedMethods = [
		'GET' => true,
		'POST' => true,
		'PUT' => true,
		'PATCH' => true,
		'OPTIONS' => true,
		'DELETE' => true,
		'ANY' => true
	];

	/**
	 * Constructor
	 * @param string $method  Request method
	 * @param string $baseUri Base uri
	 */
	public function __construct($baseUri = "/")
	{
		$this->baseUri = (string) $baseUri;
	}

	/**
	 * Match types are used to replace given search with replace
	 * @param array|string $matchTypes Array containg search and replace
	 */
	public function setMatchTypes($matchTypes)
	{
		if (is_array($matchTypes)) {
			$this->matchTypes = $matchTypes;
		} else {
			foreach ( (array) $matchTypes as $key => $matchType) {
				$this->matchTypes[$key] = $matchType;
			}
		}
	}

	/**
	 * Adds Match types
	 * @method addMatchTypes
	 * @param  array        $matchTypes Array of match types
	 */
	public function addMatchTypes($matchTypes)
	{
		$this->matchTypes = $matchTypes;
	}

	/**
	 * Match types are used to replace given search with replace
	 * @param array|string $matchTypes Array containg search and replace
	 */
	public function setMatchType($key, $matchType)
	{
		$this->matchTypes[$key] = $matchType;
	}

	/**
	 * Set the base uri to given string
	 * @param string $baseUri Base uri used to trim from currentUri
	 */
	public function setBaseUri($baseUri)
	{
		$this->baseUri = (string) $baseUri;
	}

	/**
	 * Shorthand for addRoute, adds GET route
	 * @param  string $pattern  Patter used to match the route
	 * @param  array|callable $callable Callable for route
	 */
	public function get($pattern, $callable, $name = null)
	{
		$this->addRoute("GET", $pattern, $callable, $name = null);
	}

	/**
	 * Shorthand for addRoute, adds POST route
	 * @param  string $pattern  Patter used to match the route
	 * @param  array|callable $callable Callable for route
	 */
	public function post($pattern, $callable, $name = null)
	{
		$this->addRoute('POST', $pattern, $callable, $name = null);
	}

	/**
	 * Shorthand for addRoute, adds PATCH route
	 * @param  string $pattern  Patter used to match the route
	 * @param  array|callable $callable Callable for route
	 */
	public function patch($pattern, $callable, $name = null)
	{
		$this->addRoute('PATCH', $pattern, $callable, $name = null);
	}

	/**
	 * Shorthand for addRoute, adds DELETE route
	 * @param  string $pattern  Patter used to match the route
	 * @param  array|callable $callable Callable for route
	 */
	public function delete($pattern, $callable, $name = null)
	{
		$this->addRoute('DELETE', $pattern, $callable, $name = null);
	}

	/**
	 * Shorthand for addRoute, adds PUT route
	 * @param  string $pattern  Patter used to match the route
	 * @param  array|callable $callable Callable for route
	 */
	public function put($pattern, $callable, $name = null)
	{
		$this->addRoute('PUT', $pattern, $callable, $name = null);
	}

	/**
	 * Shorthand for addRoute, adds OPTIONS route
	 * @param  string $pattern  Patter used to match the route
	 * @param  array|callable $callable Callable for route
	 */
	public function options($pattern, $callable, $name = null)
	{
		$this->addRoute('OPTIONS', $pattern, $callable, $name = null);
	}

	/**
	 * Shorthand for addRoute, adds ANY route
	 * @param  string $pattern  Patter used to match the route
	 * @param  array|callable $callable Callable for route
	 */
	public function any($pattern, $callable, $name = null)
	{
		$this->addRoute('ANY', $pattern, $callable, $name = null);
	}

	/**
	 * Mount multiple routes via callabck
	 * @param  string $baseroute Base route for all routes in given callback
	 * @param  callable $callable  Callable to execute
	 */
	public function mount($baseroute, $callable)
	{

		// Track current baseroute
		$curBaseroute = $this->baseUri;

		$this->baseUri .= $baseroute;
		// Call the callable
		call_user_func($callable);

		// Restore original baseroute
		$this->baseUri = $curBaseroute;

	}

	/**
	 * Automatically find the request method
	 * @return string Found method
	 */
	public function findRequestMethod()
	{
		$method = $_SERVER['REQUEST_METHOD'];

		if ($method == 'POST') {
			if (isset($_SERVER['X-HTTP-Method-Override'])) {
				$method = getenv('X-HTTP-Method-Override');
			}
		}

		return $this->method = $method;
	}

	/**
	 * Returns request method stored in router
	 * @return string Returns set request method
	 */
	public function getRequestMethod()
	{
		return $this->method;
	}

	/**
	 * Set the request method, automatically uppercased
	 * @param string $method Request method
	 */
	public function setRequestMethod($method)
	{
		return $this->method = strtoupper($method);
	}

	/**
	 * Lowecase all routes?
	 */
	public function useLowerCase()
	{
		$this->lowerCase = true;
	}

	/**
	 * Automatically find the current uri using baseuri
	 * @return string Returns found uri
	 */
	public function findUri()
	{
		$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		return ($this->lowerCase) ? strtolower($uri) : $uri;
	}


	/**
	 * Add new route to list of available routes
	 *
	 * @param $method
	 * @param $route
	 * @param $action
	 * @throws InvalidMethodException
	 */
	public function addRoute($method, $route, $action, $name = null, $definitions = null)
	{
		if ('ANY' === $method) {
			$methods = [
				'GET' => $action,
				'POST' => $action,
				'PUT' => $action
			];
		} else {

			$methods = (array) $method;
			$count = count($methods);

			for ($i = 0; $i < $count; $i++) {
				if (!isset($this->allowedMethods[$methods[$i]])) {
					throw new InvalidMethodException('Method: ' . $methods[$i] . ' is not valid');
				}
				$methods[$methods[$i]] = $action;
			}
		}
		if ($name === null) {
			$this->rawRoutes[] = [
				'route' => $this->baseUri.$route,
				'method' => $methods,
				"definitions" => $definitions
			];
		} else {
			$this->rawRoutes[$name] = [
				'route' => $this->baseUri.$route,
				'method' => $methods,
				"definitions" => $definitions
			];
		}

	}

	/**
	 * Find route in route tree structure.
	 *
	 * @param $method
	 * @param $uri
	 * @return array
	 * @throws MethodNotAllowedException
	 * @throws RouteNotFoundException
	 */
	public function findRoute($method, $uri)
	{
		if ($this->routesTree === null) {
			$this->routesTree = $this->parseRoutes($this->rawRoutes);
		}

		$search = $this->normalize($uri);

		$node = $this->routesTree;
		$params = [];
		//loop every segment in request url, compare it, collect parameters names and values
		foreach ($search as $v) {

			if (isset($node[$v['use']])) {
				$node = $node[$v['use']];

			} elseif (isset($node['*'])) {

				$subnode = $node["*"];
				if (isset($node["definitions"])) {

					if (isset($node["definitions"][$subnode['name']])) {

						$regex = ($node["definitions"][$subnode['name']][0] === "(") ? $node["definitions"][$subnode['name']] : $this->getMatchType($node["definitions"][$subnode['name']], "all");

						if (!preg_match($regex, $v['name'])) {
							throw new RouteNotFoundException('Route for uri: ' . $uri . ' was not found');
						}
					}

					$params[$subnode['name']] = $v['name'];
				} else {
					$params[$subnode['name']] = $v['name'];
				}

				$node = $subnode;

			} elseif (isset($node['?'])) {
				$node = $node['?'];
				$params[$node['name']] = $v['name'];

			} else {
				throw new RouteNotFoundException('Route for uri: ' . $uri . ' was not found');
			}
		}
		//check for route with optional parameters that are not in request url until valid action is found
		while (!isset($node['exec']) && isset($node['?'])) {
			$node = $node['?'];
		}

		if (isset($node['exec'])) {
			if (!isset($node['exec']['method'][$method])
				&& !isset($node['exec']['method']['any'])
			) {
				throw new MethodNotAllowedException('Method: ' . $method . ' is not allowed for this route');
			}
			return [
				'route' => $node['exec']['route'],
				'method' => $method,
				'action' => $node['exec']['method'][$method],
				'params' => $params
			];
		}
		throw new RouteNotFoundException('Route for uri: ' . $uri . ' was not found');
	}

	/**
	 * Return regex if type is defined otherwise,
	 * returns default value.
	 *
	 * @method getMatchType
	 * @throws MatchTypeNotFoundExeption If regex of type wasn't found
	 *
	 * @param  string       $type    Type of allowed regex
	 * @param  string       $default Default return value if type is not found
	 *
	 * @return string       Found Regex
	 */
	public function getMatchType($type, $default = null)
	{
		if (isset($this->matchTypes[$type])) {
			return $this->matchTypes[$type];
		} else if ($default !== null) {
			return $default;
		} else {
			throw new MatchTypeNotFoundExeption("Unknow match type");
		}
	}

	/**
	 * Get routes tree structure. Can be cashed and later loaded using load() method
	 * @return array|null
	 */
	public function dump()
	{
		if ($this->routesTree == null) {
			$this->routesTree = $this->parseRoutes($this->rawRoutes);
		}
		return $this->routesTree;
	}

	/**
	 * Load routes tree structure that was taken from dump() method
	 * This method will overwrite anny previously added routes.
	 * @param array $arr
	 */
	public function load(array $arr)
	{
		$this->routesTree = $arr;
	}

	/**
	 * Normalize route structure and extract dynamic and optional parts
	 *
	 * @param $route
	 * @return array
	 */
	protected function normalize($route)
	{
		//make sure that all urls have the same structure
		/*if ($route[0] !== '/') {
			$route = '/' . $route;
		}*/

		/* Fix trailling shash */
		if (mb_substr($route, -1, 1) == '/') {
			$route = substr($route, 0, -1);
		}

		$result = explode('/', $route);
		$result[0] = $this->baseUri;
		$ret = [];
		//check for dynamic and optional parameters
		foreach ($result as $v) {
			if (!$v) {
				continue;
			}
			if ($v[0] === "?") {
				$ret[] = [
					'name' => explode('}', mb_substr($v, 2))[0],
					'use' => '?'
				];
			} elseif (($v[0]) === '{') {
				$ret[] = [
					'name' => explode('}', mb_substr($v, 1))[0],
					'use' => "*"
				];
			} else {
				$ret[] = [
					'name' => $v,
					'use' => $v
				];
			}
		}
		return $ret;
	}

	/**
	 * Build tree structure from all routes.
	 *
	 * @param $routes
	 * @return array
	 */
	protected function parseRoutes($routes)
	{
		$tree = [];

		foreach ($routes as $route) {
			$node = &$tree;

			$definitions = $route['definitions'];
			unset($route['definitions']);

			foreach ($this->normalize($route['route']) as $segment) {
				if (!isset($node[$segment['use']])) {
					$node[$segment['use']] = ['name' => $segment['name']];
				}
				$node['definitions'] = $definitions;
				$node = &$node[$segment['use']];
			}


			//node exec can exists only if a route is already added.
			//This happens when a route is added more than once with different methods.
			if (isset($node['exec'])) {
				$node['exec']['method'] = array_merge($node['exec']['method'], $route['method']);
			} else {
				$node['exec'] = $route;
			}
			$node['name'] = $segment['name'];
		}
		return $tree;
	}

	public function getRawRoutes()
	{
		return $this->rawRoutes;
	}

	public function getMatchTypes()
	{
		return $this->matchTypes;
	}
}
