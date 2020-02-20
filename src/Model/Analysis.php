<?php

namespace PhpSports\Model;

use \JsonSerializable;

class Analysis implements JsonSerializable
{
    private $parameter;
    private $value;

    public function __construct(
        string $parameter = null,
        float $value = null
    ) {
        $this->parameter        = $parameter;
        $this->value            = $value;
    }

    private static function setValue(float $value = null)
    {
        $this->value = $value;
    }

    private static function getValue(float $value = null)
    {
        return $this->value;
    }

    public function getParameter()
    {
        return $this->parameter;
    }

    public function setParameter(string $parameter = null)
    {
        $this->parameter = $parameter;
    }

    public function jsonSerialize() {
        return [
            $this->getParameter() => $this->getValue()
        ];
    }
}
