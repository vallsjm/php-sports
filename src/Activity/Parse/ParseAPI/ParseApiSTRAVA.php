<?php

namespace PhpSports\Activity\Parse\ParseAPI;

use PhpSports\Activity\Parse\ParseReadInterface;
use PhpSports\Activity\Parse\ParseReadArrayInterface;
use PhpSports\Activity\Parse\BaseParseAPI;
use PhpSports\Analyzer\Analysis\ResumeAnalysis;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;
use PhpSports\Model\Source;
use \DateTime;
use \Exception;

class ParseApiSTRAVA extends BaseParseAPI implements ParseReadInterface, ParseReadArrayInterface
{
    const APIFORMAT = 'STRAVA';

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
        if (!$sport) {
            return null;
        }
        $ret = 'OTHER';
        if (isset(self::SPORTS[$sport])) {
            $ret = self::SPORTS[$sport];
        }
        return $ret;
    }

    // [
    //     'info' => información del activity,
    //     'stream' => puntos,
    //     'laps' => laps
    // ]
    public function normalizeOne(array $item)
    {
        $item['_sport'] = $this->normalizeSport($item['info']['type']);
        $points = [];
        foreach ($item['stream'] as $stream) {
            $type = $stream['type'];
            switch ($type) {
                case 'latlng':
                    foreach ($stream['data'] as $timestamp => $values) {
                        if (!isset($points[$timestamp])) {
                            $points[$timestamp] = [];
                        }
                        $points[$timestamp]['lat'] = $stream['data'][$timestamp][0];
                        $points[$timestamp]['lng'] = $stream['data'][$timestamp][1];
                    }
                break;
                default:
                    foreach ($stream['data'] as $timestamp => $values) {
                        if (!isset($points[$timestamp])) {
                            $points[$timestamp] = [];
                        }
                        $points[$timestamp][$type] = $stream['data'][$timestamp];
                    }
                break;
            }
        }
        $item['_points'] = $points;
        unset($item['stream']);

        return $item;
    }

    public function normalize(array $data)
    {
        foreach ($data as &$item) {
            $item = $this->normalizeOne($item);
        }
        return $data;
    }

    public function createActivity(
        Source $source,
        array $item
    ) : Activity {
        $itemInfo = $item['info'];

        $newSource = clone $source;
        $newSource->setId($itemInfo['id']);

        $activity = new Activity();
        $activity->setAthleteStatus($this->athleteStatus);
        $activity->setSource($newSource);
        $activity->setSport($item['_sport']);

        if (isset($item['_id'])) {
            $activity->setId($item['_id']);
        }

        $offsetTimestamp = 0;
        if (isset($itemInfo['start_date_local'])) {
            $startDate = new \DateTime($itemInfo['start_date_local']);
            $activity->setStartedAt($startDate);
            $offsetTimestamp = $startDate->getTimestamp();
        }
        if (isset($item['_points'])) {
            foreach ($item['_points'] as $strava) {
                $timestamp = $strava['time'];
                $point     = new Point($timestamp + $offsetTimestamp);
                if (isset($strava['lat'])) {
                    $point->setLatitude((float) $strava['lat']);
                    $point->setLongitude((float) $strava['lng']);
                }
                if (isset($strava['distance'])) {
                    $point->setDistanceMeters((float) $strava['distance']);
                }
                if (isset($strava['altitude'])) {
                    $point->setAltitudeMeters((float) $strava['altitude']);
                }
                if (isset($strava['heartrate'])) {
                    $point->setHrBPM((int) $strava['heartrate']);
                }
                if (isset($strava['cadence'])) {
                    $point->setCadenceRPM((int) $strava['cadence']);
                }
                if (isset($strava['watts'])) {
                    $point->setPowerWatts((int) $strava['watts']);
                }
                $activity->addPoint($point);
            }

            if ((count($item['_points']) > 1) && isset($item['laps'])) {
                $nlap = 1;
                foreach ($item['laps'] as $strava) {
                    $lap = new Lap(
                        $nlap,
                        $strava['name'],
                        $strava['start_index'] + $offsetTimestamp,
                        $strava['end_index'] + $offsetTimestamp
                    );
                    $activity->addLap($lap);
                    $nlap++;
                }
            }
        }

        $resume = [];
        if (isset($itemInfo['distance'])) {
            $resume['distanceMeters'] = $itemInfo['distance'];
        }
        if (isset($itemInfo['moving_time'])) {
            $resume['durationSeconds'] = round($itemInfo['moving_time']);
        }
        if (isset($itemInfo['total_elevation_gain'])) {
            $resume['elevationGainMeters'] = $itemInfo['total_elevation_gain'];
        }
        if (isset($itemInfo['calories'])) {
            $resume['caloriesKcal'] = $itemInfo['calories'];
        }
        if (count($resume)) {
            $analysis = new ResumeAnalysis($resume);
            $activity->addAnalysis($analysis);
        }

        return $this->analyze($activity);
    }

    public function createActivities(
        Source $source,
        ActivityCollection $activities,
        array $data
    ) : ActivityCollection {
        foreach ($data as $item) {
            $activity = $this->createActivity($source, $item);
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
