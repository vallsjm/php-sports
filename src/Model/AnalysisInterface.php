<?php

namespace PhpSports\Model;

use PhpSports\Model\Analysis;

interface AnalysisInterface {

    public function __construct($data = null);
    public function setData($data = null);
    public function getData();
    public function getName() : string;
    public function merge(Analysis $analysis) : Analysis;

}
