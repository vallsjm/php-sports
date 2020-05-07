<?php

namespace PhpSports\Analyzer\Analysis;

use PhpSports\Analyzer\Analysis\Parameter;
use PhpSports\Model\Analysis;
use PhpSports\Model\AnalysisInterface;
use \JsonSerializable;

class ParameterAnalysis extends Analysis implements JsonSerializable, AnalysisInterface
{
    public function __construct(
        $data = null
    ) {
        $this->data = [];
    }

    public function getName() : string
    {
        return 'parameters';
    }

    public function addParameter(Parameter $parameter = null)
    {
        $pos = $parameter->getParameter();
        $this->data[$pos] = $parameter;
    }

    public function merge(Analysis $analysis) : Analysis
    {
        return new static(array_replace_recursive($analysis->getData(), $this->data));
    }
}
