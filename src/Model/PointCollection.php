<?php

namespace PhpSports\Model;

use PhpSports\Model\Point;

class PointCollection extends \ArrayObject implements \JsonSerializable
{
    private $structure;

    public function __construct()
    {
        $this->clearStructure();
    }

    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Point) {
            throw new \Exception('value must be an instance of Point');
        }

        parent::offsetSet($offset, $value);
    }

    public function addPoint(Point $point) : PointCollection
    {
        $this->structure = Point::getStructure();
        parent::append($point);
        return $this;
    }

    public function clearStructure() : PointCollection
    {
        $this->structure = [];
        Point::clearStructure();
        return $this;
    }

    public function loadStructure() : PointCollection
    {
        Point::setStructure($this->structure);
        return $this;
    }

    public function getStructure() : array
    {
        return $this->structure;
    }

    public function setStructure(array $structure) : PointCollection
    {
        $this->structure = $structure;
        return $this;
    }

    public function hasMap() : bool
    {
        return in_array('latitude', $this->structure);
    }

    public function jsonSerialize() {
        $this->loadStructure();
        return [
            'structure' => $this->getStructure(),
            'points' => (array) $this
        ];
    }
}
