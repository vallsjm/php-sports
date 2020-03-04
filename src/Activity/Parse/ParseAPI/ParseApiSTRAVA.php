<?php

namespace Core\Application\Import\Parse\ParseAPI;

use PhpSports\Activity\Parse\ParseAPIReadInterface;
use PhpSports\Activity\Parse\BaseParseAPI;
use PhpSports\Analyzer\Analysis\ResumeAnalysis;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;
use PhpSports\Model\Source;
use \DateTime;
use \Exception;

class ParseApiSTRAVA extends BaseParseAPI implements ParseAPIReadInterface
{
    const APITYPE = 'STRAVA';

    const SPORTS = [
        'AlpineSki'       => 'OTHER',
        'BackcountrySki'  => 'OTHER',
        'Canoeing'        => 'OTHER',
        'Crossfit'        => 'FITNESS',
        'EBikeRide'       => 'CYCLING_MOUNTAIN',
        'Elliptical'      => 'RUNNING_INDOOR',
        'Hike'            => 'OTHER',
        'IceSkate'        => 'OTHER',
        'InlineSkate'     => 'OTHER',
        'Kayaking'        => 'OTHER',
        'Kitesurf'        => 'OTHER',
        'Kayaking'        => 'OTHER',
        'NordicSki'       => 'OTHER',
        'Ride'            => 'CYCLING_STREET',
        'RockClimbing'    => 'OTHER',
        'RollerSki'       => 'OTHER',
        'Rowing'          => 'OTHER',
        'Run'             => 'RUNNING',
        'Snowboard'       => 'OTHER',
        'Snowshoe'        => 'RUNNING_INDOOR',
        'StairStepper'    => 'RUNNING_INDOOR',
        'StandUpPaddling' => 'OTHER',
        'Surfing'         => 'OTHER',
        'Swim'            => 'SWIMMING',
        'VirtualRide'     => 'CYCLING_INDOOR',
        'Walk'            => 'RUNNING_INDOOR',
        'WeightTraining'  => 'FITNESS',
        'Windsurf'        => 'OTHER',
        'Workout'         => 'FITNESS',
        'Yoga'            => 'OTHER'
    ];

    public function normalizeSport(string $sport = null)
    {
        if (!$sport) return null;
        $ret = 'OTHER';
        if (isset(self::SPORTS[$sport])) {
            $ret = self::SPORTS[$sport];
        }
        return $ret;
    }

    public function normalize(array $data)
    {
        foreach ($data as &$item) {
            $item['summary']['activityType'] = $this->normalizeSport($item['summary']['activityType']);
        }
        return $data;
    }

    public function createActivities(ActivityCollection $activities, array $data) : ActivityCollection
    {
        // foreach ($data as $item) {
        //     $itemInfo = $item['summary'];
        //
        //     $source = new Source(
        //         $item['summaryId'],
        //         self::getType(),
        //         self::getFormat()
        //     );
        //
        //     $activity = new Activity();
        //     $activity->setSource($source);
        //
        //     if (isset($itemInfo['activityType'])) {
        //         $activity->setSport($itemInfo['activityType']);
        //     }
        //
        //     foreach ($item['samples'] as $garmin) {
        //         $timestamp = $garmin['startTimeInSeconds'] + $itemInfo['startTimeOffsetInSeconds'];
        //         $point     = new Point($timestamp);
        //         if (isset($garmin['latitudeInDegree'])) {
        //             $point->setLatitude((float) $garmin['latitudeInDegree']);
        //             $point->setLongitude((float) $garmin['longitudeInDegree']);
        //         }
        //         if (isset($garmin['totalDistanceInMeters'])) {
        //             $point->setDistanceMeters((float) $garmin['totalDistanceInMeters']);
        //         }
        //         if isset($garmin['elevationInMeters']) {
        //             $point->setElevationMeters((float) $garmin['elevationInMeters']);
        //         }
        //         if (isset($garmin['heartRate'])) {
        //             $point->setHrBPM((int) $garmin['heartRate']);
        //         }
        //         if (isset($garmin['bikeCadenceInRPM'])) {
        //             $point->setCadenceRPM((int) $garmin['bikeCadenceInRPM']);
        //         }
        //         if (isset($garmin['powerInWatts'])) {
        //             $point->setPowerWatts((int) $garmin['powerInWatts']);
        //         }
        //         $activity->addPoint($point);
        //     }
        //
        //     $laps = array_column($item['laps'], 'startTimeInSeconds');
        //     for ($n=count($laps), $i=0; $i<$n; $i++) {
        //         $name       = 'L' . ($i+1);
        //         $lap = new Lap(
        //             $name,
        //             $laps[$i],
        //             (isset($laps[$i+1])) ? $laps[$i+1] : $timestamp
        //         );
        //         $activity->addLap($lap);
        //     }
        //
        //     $resume = [];
        //     if (isset($itemInfo['distanceInMeters'])) {
        //         $resume['distanceMeters'] = $itemInfo['distanceInMeters'];
        //     }
        //     if (isset($itemInfo['durationInSeconds'])) {
        //         $resume['durationSeconds'] = round($itemInfo['durationInSeconds']);
        //     }
        //     if (isset($itemInfo['totalElevationGainInMeters'])) {
        //         $resume['elevationGainMeters'] = $itemInfo['totalElevationGainInMeters'];
        //     }
        //     if (isset($itemInfo['activeKilocalories'])) {
        //         $resume['caloriesKcal'] = $itemInfo['activeKilocalories'];
        //     }
        //     if (count($resume)) {
        //         $analysis = new ResumeAnalysis($resume);
        //         $activity->addAnalysis($analysis);
        //     }
        //
        //     $activity = $this->analyze($activity);
        //     $activities->addActivity($activity);
        // }
        //
        // return $activities;
    }

    public function readFromAPI(array $data) : ActivityCollection
    {
        $activities = new ActivityCollection();
        $data  = $this->normalize($data);
        return $this->createActivities($activities, $data);
    }

}
