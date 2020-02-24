<?php

namespace PhpSports\Model;

use \JsonSerializable;

abstract class Analysis implements JsonSerializable
{
    private $values;

    public function __construct(
        $values = null
    ) {
        $this->values = $values;
    }

    public function setValues($values = null)
    {
        $this->values = $values;
    }

    public function getValues()
    {
        return $this->values;
    }

    abstract function getAnalyzerName() : string;
    abstract function merge(Analysis $anlysis) : Analysis;

    public function jsonSerialize() {
        return $this->getValues();
    }
}
