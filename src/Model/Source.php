<?php

namespace PhpSports\Model;

use \JsonSerializable;

final class Source implements JsonSerializable
{
    private $id;
    private $type;
    private $format;
    private $fileName;

    public function __construct(
        string $id = null,
        string $type = null,
        string $format = null,
        string $fileName = null
    ) {
        $this->id       = $id;
        $this->type     = $type;
        $this->format   = $format;
        $this->fileName = $fileName;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(string $id = null)
    {
        $this->id = $id;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type = null)
    {
        $this->type = $type;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat(string $format = null)
    {
        $this->format = $format;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName = null)
    {
        $this->fileName = $fileName;
    }

    public function getFileExtension()
    {
        switch ($this->format) {
            case 'GPX':
                return 'gpx';
            break;
            case 'TCX':
                return 'tcx';
            break;
            case 'FIT':
                return 'fit';
            break;
            default:
                return 'json';
            break;
        }
    }

    public function getMineType()
    {
        switch ($this->getFileExtension()) {
            case 'gpx':
            case 'tcx':
            case 'xml':
                return 'application/xml';
            break;
            case 'fit':
                return 'application/octet-stream';
            break;
            case 'json':
                return 'application/json';
            break;
        }
    }

    public function jsonSerialize()
    {
        return [
            'id'       => $this->id,
            'type'     => $this->type,
            'format'   => $this->format,
            'fileName' => $this->fileName
        ];
    }
}
