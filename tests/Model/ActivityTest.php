<?php

use PHPUnit\Framework\TestCase;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;

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

        // $obj = json_encode($activity, JSON_PRETTY_PRINT);

        print_r($obj);
    }

}
