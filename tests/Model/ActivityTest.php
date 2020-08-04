<?php
namespace Tests\Model;

use PHPUnit\Framework\TestCase;
use PhpSports\Model\Activity;
use PhpSports\Model\Analysis;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;

final class ActivityTest extends TestCase
{
    public function testTitle()
    {
        $activity = new Activity();
        $activity->setTitle('test');

        $this->assertEquals(
            'test',
            $activity->getTitle()
        );
    }

    public function testAddLap()
    {
        $activity = new Activity('Activity');
        $activity->setId('1234');

        $p1 = new Point();
        $p1->setTimestamp(1575990461);
        $p1->setLatitude(41.31734714181024);
        $p1->setLongitude(2.094990443212767);
        $p1->setHrBPM(120);
        $activity->addPoint($p1);

        $p2 = new Point();
        $p2->setTimestamp(1575990464);
        $p2->setLatitude(41.32033364596318);
        $p2->setLongitude(2.0984703316472952);
        $p2->setHrBPM(125);
        $p2->setCadenceRPM(400);
        $activity->addPoint($p2);

        $p3 = new Point();
        $p3->setTimestamp(1575990466);
        $p3->setLatitude(41.3203336459789);
        $p3->setLongitude(2.0984703316472678);
        $p3->setHrBPM(122);
        $p3->setCadenceRPM(400);
        $activity->addPoint($p3);

        $lap = new Lap(
            null,
            'L1',
            1575990461,
            1575990464
        );
        $activity->addLap($lap);

        $this->assertEquals(
            '1234',
            $activity->getId()
        );

        $this->assertEquals(
            3,
            count($activity->getPoints())
        );

        $this->assertEquals(
            1,
            count($activity->getLaps())
        );

        $this->assertEquals(
            'Activity',
            $activity->getTitle()
        );

        $points = $activity->getPoints();
        $filtered = $points->filterByLap($lap);

        $this->assertEquals(
            2,
            count($filtered)
        );



        // $this->assertEquals(
        //     447,
        //     round($activity->getDistanceMeters())
        // );
        //
        // $this->assertEquals(
        //     3,
        //     $lap1->getDurationSeconds()
        // );
        //
        // if ($hr = $lap1->getAnalysisOrNull('HR')) {
        //     $this->assertEquals(
        //         120,
        //         $hr->getMin()
        //     );
        //     $this->assertEquals(
        //         125,
        //         $hr->getMax()
        //     );
        //     $this->assertEquals(
        //         122.5,
        //         $hr->getAvg()
        //     );
        //     $this->assertEquals(
        //         245,
        //         $hr->getTotal()
        //     );
        // }

        // $obj = json_encode($activity, JSON_PRETTY_PRINT);
        // print_r($obj);

    }

}
