<?php

namespace PhpSports\Activity\Parse;

use PhpSports\Model\ActivityCollection;

abstract class BaseParseFile extends BaseParse
{
    protected $fileName;

    public function setFileName(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function getFileName() : string
    {
        return $this->fileName;
    }

    public function read() : ActivityCollection
    {
        return $this->readFromFile($this->fileName);
    }

    public function save(ActivityCollection $activities, bool $pretty = false) : ActivityCollection
    {
        return $this->saveToFile($activities, $this->fileName, $pretty);
    }

    static public function getFormat()
    {
        return $this::FILETYPE;
    }

    static public function getSource()
    {
        return 'FILE';
    }
}
