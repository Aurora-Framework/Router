<?php

namespace Aurora\Router;

use Aurora\Router;

class Loader
{
    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function load()
    {
        $Router = new Router();
        $type = (
            isset($this->data["type"])
            && ($this->data["type"] === "uri" || $this->data["type"] === "method")
        ) ? $this->data["type"] : "uri";

        if (isset($this->data["matchTypes"])) {
            $Router->addMatchTypes($this->data["matchTypes"]);
        }

        if (isset($this->data["baseUri"])) {
            $Router->setBaseUri($this->data["baseUri"]);
        }

        $routes = $this->data["routes"];

        if ($type === "uri") {
            $this->parseUriRoute($routes, $Router);
        } else {
            $this->parseMethodRoute($routes, $Router);
        }

        return $Router;
    }

    protected function parseUriRoute($routes, &$Router, $base = "")
    {
        foreach ($routes as $pattern => $data) {
            if (key($data) === 0) {
                $Router->addRoute($data[0], $base.$pattern, $data[1]);
            } else {
                $this->parseUriRoute($data, $Router, $pattern);
            }
        }
    }

    protected function parseMethodRoute($routes, &$Router)
    {
        foreach ($routes as $method => $routesInMethod) {
            foreach ($routesInMethod as $route) {
                $name = null;
                if (isset($route[2])) {
                    $name = $route[2];
                }
                $Router->addRoute($method, $route[0], $route[1], $name);
            }
        }
    }
}
