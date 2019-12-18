<?php

use PHPUnit\Framework\TestCase;
use PhpSports\Model\Activity;
use PhpSports\Model\Analysis;
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
        $activity = new Activity('Activity');
        $activity->setId('1234');

        $lap1 = new Lap('L1');

        $analysis1 = new Analysis('HR');
        $lap1->addAnalysis($analysis1);

        $analysis2 = new Analysis('CADENCE');
        $lap1->addAnalysis($analysis2);

        $p1 = new Point();
        $p1->setTimestamp(1575990461);
        $p1->setLatitude(41.31734714181024);
        $p1->setLongitude(2.094990443212767);
        $p1->setHrBPM(120);
        $lap1->addPoint($p1);

        $p2 = new Point();
        $p2->setTimestamp(1575990464);
        $p2->setLatitude(41.32033364596318);
        $p2->setLongitude(2.0984703316472952);
        $p2->setHrBPM(125);
        $p2->setCadenceRPM(400);
        $lap1->addPoint($p2);

        $activity->addLap($lap1);

        $lap2 = new Lap('L2');
        $lap2->setDistanceMeters(6);
        $activity->addLap($lap2);

        $this->assertEquals(
            '1234',
            $activity->getId()
        );

        $this->assertEquals(
            447,
            $activity->getDistanceMeters()
        );

        $this->assertEquals(
            3,
            $lap1->getDurationSeconds()
        );

        if ($hr = $lap1->getAnalysisOrNull('HR')) {
            $this->assertEquals(
                120,
                $hr->getMin()
            );
            $this->assertEquals(
                125,
                $hr->getMax()
            );
            $this->assertEquals(
                122.5,
                $hr->getAvg()
            );
            $this->assertEquals(
                245,
                $hr->getTotal()
            );
        }

        $obj = json_encode($activity, JSON_PRETTY_PRINT);
        print_r($obj);

    }

}
