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

class ParseApiSTRAVA extends BaseParseAPI implements ParseReadInterface
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


    // [[
    //     'info' => información del activity,
    //     'stream' => puntos,
    //     'laps' => laps
    // ],[
    //     'info' => información del activity,
    //     'stream' => puntos,
    //     'laps' => laps
    // ]]
    public function normalize(array $data)
    {
        foreach ($data as &$item) {
            $item['info']['type'] = $this->normalizeSport($item['info']['type']);
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
            $item['points'] = $points;
            unset($item['stream']);
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
            $itemInfo = $item['info'];

            $newSource = clone $source;
            $newSource->setId($item['id']);

            $activity = new Activity();
            $activity->setSource($newSource);

            if (isset($itemInfo['type'])) {
                $activity->setSport($itemInfo['type']);
            }
            if (isset($itemInfo['start_date_local'])) {
                $activity->setTimestampOffset($itemInfo['start_date_local']);
            }
            foreach ($item['points'] as $strava) {
                $timestamp = $strava['time'];
                $point     = new Point($timestamp);
                if (isset($strava['lat'])) {
                    $point->setLatitude((float) $strava['lat']);
                    $point->setLongitude((float) $strava['lng']);
                }
                if (isset($strava['distance'])) {
                    $point->setDistanceMeters((float) $strava['distance']);
                }
                if isset($strava['altitude']) {
                    $point->setAltitudeMeters((float) $garmin['altitude']);
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

            foreach ($item['laps'] as $strava) {
                $lap = new Lap(
                    $strava['name'],
                    $strava['start_index'],
                    $strava['end_index']
                );
                $activity->addLap($lap);
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

            $activity = $this->analyze($activity);
            $activities->addActivity($activity);
        }

        return $activities;
    }

    public function readFromArray(array $data) : ActivityCollection
    {
        $activities = new ActivityCollection();
        $data  = $this->normalize($data);
        return $this->createActivities($activities, $data);
    }

    public function readFromBinary(string $data) : ActivityCollection
    {
        $source = new Source(
            null,
            self::getType(),
            self::getFormat()
        );

        $activities = new ActivityCollection();
        $data  = json_decode($data, true);
        $data  = $this->normalize($data);
        return $this->createActivities($source, $activities, $data);
    }

}
