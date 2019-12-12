<?php

use PHPUnit\Framework\TestCase;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;

final class ActivityTest extends TestCase
{
    public function testName()
    {
        $activity = new Activity();
        $activity->setName('test');

        $this->assertEquals(
            'test',
            $activity->getName()
        );
    }

    public function testAddLap()
    {
        $activity = new Activity();
        $activity->setName('test');

        $lap1 = new Lap();
        $lap1->setName('L1');
        $lap1->setDistanceMeters(14);

        $activity->addLap($lap1);
        $this->assertEquals(
            14,
            $activity->getDistanceMeters()
        );

        $lap2 = new Lap();
        $lap2->setName('L2');
        $lap2->setDistanceMeters(6);

        $activity->addLap($lap2);
        $this->assertEquals(
            20,
            $activity->getDistanceMeters()
        );

        $p1 = new Point();
        $p1->setTimestamp(1575990461);
        $p1->setLatitude(41.31734714181024);
        $p1->setLongitude(2.094990443212767);

        $p2 = new Point();
        $p2->setTimestamp(1575990464);
        $p2->setLatitude(41.32033364596318);
        $p2->setLongitude(2.0984703316472952);

        $lap1->setDistanceMeters(0);
        $lap1->addPoint($p1);
        $lap1->addPoint($p2);
        $this->assertEquals(
            3,
            $lap1->getDurationSeconds()
        );
        $this->assertEquals(
            441,
            $lap1->getDistanceMeters()
        );

        //   $obj = json_encode($activity, JSON_PRETTY_PRINT);
        //   print_r($obj);
    }

}
