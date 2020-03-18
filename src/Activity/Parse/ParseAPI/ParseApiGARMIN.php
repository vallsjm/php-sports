<?php

namespace PhpSports\Activity\Parse\ParseAPI;

use PhpSports\Activity\Parse\ParseReadInterface;
use PhpSports\Activity\Parse\BaseParseAPI;
use PhpSports\Analyzer\Analysis\ResumeAnalysis;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;
use PhpSports\Model\Source;
use \DateTime;
use \Exception;

class ParseApiGARMIN extends BaseParseAPI implements ParseReadInterface
{
    const APIFORMAT = 'GARMIN';

    const SPORTS = [
        'ALL'                             => 'OTHER',
        'UNCATEGORIZED'                   => 'OTHER',
        'SEDENTARY'                       => 'OTHER',
        'SLEEP'                           => 'OTHER',
        'RUNNING'                         => 'RUNNING',
        'STREET_RUNNING'                  => 'RUNNING_STREET',
        'TRACK_RUNNING'                   => 'RUNNING_INDOOR',
        'TRAIL_RUNNING'                   => 'RUNNING_MOUNTAIN',
        'TREADMILL_RUNNING'               => 'RUNNING_INDOOR',
        'CYCLING'                         => 'CYCLING',
        'CYCLOCROSS'                      => 'CYCLING',
        'DOWNHILL_BIKING'                 => 'CYCLING_MOUNTAIN',
        'INDOOR_CYCLING'                  => 'CYCLING_INDOOR',
        'MOUNTAIN_BIKING'                 => 'CYCLING_MOUNTAIN',
        'RECUMBENT_CYCLING'               => 'CYCLING',
        'ROAD_BIKING'                     => 'CYCLING',
        'TRACK_CYCLING'                   => 'CYCLING',
        'FITNESS_EQUIPMENT'               => 'FITNESS',
        'ELLIPTICAL'                      => 'OTHER',
        'INDOOR_CARDIO'                   => 'OTHER',
        'INDOOR_ROWING'                   => 'OTHER',
        'STAIR_CLIMBING'                  => 'OTHER',
        'STRENGTH_TRAINING'               => 'FITNESS',
        'HIKING'                          => 'OTHER',
        'SWIMMING'                        => 'SWIMMING',
        'LAP_SWIMMING'                    => 'SWIMMING',
        'OPEN_WATER_SWIMMING'             => 'SWIMMING',
        'WALKING'                         => 'RUNNING',
        'CASUAL_WALKING'                  => 'RUNNING',
        'SPEED_WALKING'                   => 'RUNNING',
        'TRANSITION'                      => 'OTHER',
        'SWIMTOBIKETRANSITION'            => 'OTHER',
        'BIKETORUNTRANSITION'             => 'OTHER',
        'RUNTOBIKETRANSITION'             => 'OTHER',
        'MOTORCYCLING'                    => 'OTHER',
        'OTHER'                           => 'OTHER',
        'BACKCOUNTRY_SKIING_SNOWBOARDING' => 'OTHER',
        'BOATING'                         => 'OTHER',
        'CROSS_COUNTRY_SKIING'            => 'OTHER',
        'DRIVING_GENERAL'                 => 'OTHER',
        'FLYING'                          => 'OTHER',
        'GOLF'                            => 'OTHER',
        'HORSEBACK_RIDING'                => 'OTHER',
        'INLINE_SKATING'                  => 'OTHER',
        'MOUNTAINEERING'                  => 'OTHER',
        'PADDLING'                        => 'OTHER',
        'RESORT_SKIING_SNOWBOARDING'      => 'OTHER',
        'ROWING'                          => 'OTHER',
        'SAILING'                         => 'OTHER',
        'SKATE_SKIING'                    => 'OTHER',
        'SKATING'                         => 'OTHER',
        'SNOWMOBILING'                    => 'OTHER',
        'SNOW_SHOE'                       => 'OTHER',
        'STAND_UP_PADDLEBOARDING'         => 'OTHER',
        'WHITEWATER_RAFTING_KAYAKING'     => 'OTHER',
        'WIND_KITE_SURFING'               => 'OTHER'
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
            $item['sport'] = $this->normalizeSport($item['summary']['activityType']);
        }
        return $data;
    }

    public function createActivities(
        Source $source,
        ActivityCollection $activities,
        array $data
    ) : ActivityCollection
    {
        foreach ($data as $item) {
            $itemInfo = $item['summary'];

            $newSource = clone $source;
            $newSource->setId($item['summaryId']);

            $activity = new Activity();
            $activity->setAthleteStatus($this->athleteStatus);
            $activity->setSource($newSource);
            $activity->setSport($item['sport']);
            if (isset($item['_id'])) {
                $activity->setId($item['_id']);
            }
            if (isset($itemInfo['startTimeOffsetInSeconds'])) {
                $activity->setTimestampOffset((int) $itemInfo['startTimeOffsetInSeconds']);
            }

            foreach ($item['samples'] as $garmin) {
                $timestamp = $garmin['startTimeInSeconds'];

                $point     = new Point($timestamp);
                if (isset($garmin['latitudeInDegree'])) {
                    $point->setLatitude((float) $garmin['latitudeInDegree']);
                    $point->setLongitude((float) $garmin['longitudeInDegree']);
                }
                if (isset($garmin['totalDistanceInMeters'])) {
                    $point->setDistanceMeters((float) $garmin['totalDistanceInMeters']);
                }
                if (isset($garmin['elevationInMeters'])) {
                    $point->setElevationMeters((float) $garmin['elevationInMeters']);
                }
                if (isset($garmin['heartRate'])) {
                    $point->setHrBPM((int) $garmin['heartRate']);
                }
                if (isset($garmin['bikeCadenceInRPM'])) {
                    $point->setCadenceRPM((int) $garmin['bikeCadenceInRPM']);
                }
                if (isset($garmin['powerInWatts'])) {
                    $point->setPowerWatts((int) $garmin['powerInWatts']);
                }
                $activity->addPoint($point);
            }

            if (count($item['samples']) > 1) {
                $laps = array_column($item['laps'], 'startTimeInSeconds');
                for ($n=count($laps), $i=0; $i<$n; $i++) {
                    $name       = 'L' . ($i+1);
                    $lap = new Lap(
                        $name,
                        $laps[$i],
                        (isset($laps[$i+1])) ? $laps[$i+1] : $timestamp
                    );
                    $activity->addLap($lap);
                }
            }

            $resume = [];
            if (isset($itemInfo['distanceInMeters'])) {
                $resume['distanceMeters'] = $itemInfo['distanceInMeters'];
            }
            if (isset($itemInfo['durationInSeconds'])) {
                $resume['durationSeconds'] = round($itemInfo['durationInSeconds']);
            }
            if (isset($itemInfo['totalElevationGainInMeters'])) {
                $resume['elevationGainMeters'] = $itemInfo['totalElevationGainInMeters'];
            }
            if (isset($itemInfo['activeKilocalories'])) {
                $resume['caloriesKcal'] = $itemInfo['activeKilocalories'];
            }
            if (count($resume)) {
                $analysis = new ResumeAnalysis($resume);
                $activity->addAnalysis($analysis);
            }

            $activity = $this->analyze($activity);
            $activities->addActivity($activity);
        }

        return $activities;
    }

    public function readFromFile(string $fileName) : ActivityCollection
    {
        $pathInfo = pathinfo($fileName);

        $source = new Source(
            null,
            $this->getType(),
            $this->getFormat(),
            $pathInfo['basename']
        );

        $activities = new ActivityCollection();
        $data       = file_get_contents($fileName, true);
        $data       = json_decode($data, true);
        $data       = $this->normalize($data);
        return $this->createActivities($source, $activities, $data);
    }

    public function readFromArray(array $data) : ActivityCollection
    {
        $source = new Source(
            null,
            $this->getType(),
            $this->getFormat()
        );

        $activities = new ActivityCollection();
        $data  = $this->normalize($data);
        return $this->createActivities($source, $activities, $data);
    }

    public function readFromBinary(string $data) : ActivityCollection
    {
        $source = new Source(
            null,
            $this->getType(),
            $this->getFormat()
        );

        $activities = new ActivityCollection();
        $data  = json_decode($data, true);
        $data  = $this->normalize($data);
        return $this->createActivities($source, $activities, $data);
    }

}
