<?php

namespace PhpSports\Model;

use \JsonSerializable;

abstract class Analysis implements JsonSerializable
{
    protected $data;

    public function __construct(
        $data = null
    ) {
        $this->data = $data;
    }

    public function setData($data = null)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    abstract public function getName() : string;
    abstract public function merge(Analysis $analysis) : Analysis;

    public function jsonSerialize()
    {
        return $this->getData();
    }
}
