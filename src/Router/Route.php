<?php

namespace Aurora\Router;

use Aurora\Helper\DataObject;

class Route extends DataObject
{
    public $namespace;

    public function where(array $definitions = [])
    {
        $this->definitions = $definitions;
        return $this;
    }

    protected function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    protected function name($name)
    {
        return $this->setName($name);
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    public function addExtra(array $extra)
    {
        $this->replace($extra);
        return $this;
    }
}
