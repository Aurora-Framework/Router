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
         $Router->baseUri($this->data["baseUri"]);
      }

      $routes = $this->data["routes"];

      if ($type) {
         foreach ($routes as $pattern => $data) {
            $Router->addRoute($data[0], $pattern, $data[1]);
         }
      } else {
         foreach ($routes as $method => $routesInMethod) {
            foreach ($routesInMethod as $route) {
               $Router->addRoute($method, $route[0], $route[1]);
            }
         }
      }

      return $Router;
   }

}
