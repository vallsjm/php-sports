<?php

namespace PhpSports\Model;

use \JsonSerializable;

final class Schema implements JsonSerializable
{
    private $parameters;

    public function __construct(
        array $parameters = []
    )
    {
        $this->parameters = [];
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function hasParameter(string $parameter = null)
    {
        return isset($this->parameters[$parameter]);
    }

    public function addParameter(string $parameter = null, int $accurency = 5)
    {
        $this->parameters[$parameter] = $accurency;
    }

    public function hasMap()
    {
        return isset($this->parameters['latitude']) && isset($this->parameters['longitude']);
    }

    public function jsonSerialize() {
        return array_keys($this->parameters);
    }
}
