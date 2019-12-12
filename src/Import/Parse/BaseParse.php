<?php

namespace PhpSports\Import;

abstract class BaseParse
{
    private $data;

    abstract public function parse();

    public function setData($data)
    {
		$this->data = $data;
    }

    protected function getData()
    {
        return $this->data;
    }
}
