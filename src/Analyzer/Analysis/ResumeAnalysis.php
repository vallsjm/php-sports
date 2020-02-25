<?php

namespace PhpSports\Analyzer\Analysis;

use PhpSports\Model\AnalysisInterface;
use PhpSports\Model\Analysis;
use PhpSports\Model\Type;
use \JsonSerializable;

class ResumeAnalysis extends Analysis implements JsonSerializable, AnalysisInterface
{
    public function __construct(
        $data = null
    ) {
        $this->setData($data);
    }

    public function getName() : string
    {
        return 'resume';
    }

    public function setData($data = null)
    {
        if (!is_array($data)) {
            throw new \Exception('data values is not valid analysis resume');
        }

        foreach ($data as $key => $value) {
            if (!in_array($key, Type::PARAMETERS)) {
                throw new \Exception('data "' . $key . '" is not valid analysis resume');
            }
        }

        parent::setData($data);
    }

    public function merge(Analysis $analysis) : Analysis
    {
        return new static(array_merge($analysis->getData(), $this->data));
    }
}
