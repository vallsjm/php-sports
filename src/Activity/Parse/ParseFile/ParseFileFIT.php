<?php

namespace PhpSports\Activity\Parse\ParseFile;

use adriangibbons\phpFITFileAnalysis;
use PhpSports\Activity\Parse\BaseParseFile;
use PhpSports\Activity\Parse\ParseFileReadInterface;
use PhpSports\Analyzer\Analysis\ResumeAnalysis;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;
use PhpSports\Model\Source;

class ParseFileFIT extends BaseParseFile implements ParseFileReadInterface
{
    const FILETYPE = 'FIT';

    const SPORTS = [
        'RUNNING'  => 1,
        'CYCLING'  => 2,
        'SWIMMING' => 5,
        'FITNESS'  => 4,
        'OTHER'    => 0
    ];

    const SUB_SPORTS = [
        'RUNNING_MOUNTAIN' => 4,
        'RUNNING_STREET'   => 2,
        'RUNNING_INDOOR'   => 45,
        'CYCLING_MOUNTAIN' => 8,
        'CYCLING_STREET'   => 7,
        'CYCLING_INDOOR'   => 6
    ];

    public function normalizeSport(int $sport = null, int $sub_sport = null)
    {
        if (!$sport) return null;
        $ret = null;
        $mapSports = array_flip(self::SPORTS);
        if (isset($mapSports[$sport])) {
            $ret = $mapSports[$sport];
        }
        $mapSubSports = array_flip(self::SUB_SPORTS);
        if (isset($mapSubSports[$sub_sport])) {
            $ret = $mapSubSports[$sub_sport];
        }
        return $ret;
    }

    private function normalize(phpFITFileAnalysis $parse) : array
    {
        $points = array();
        foreach ($parse->data_mesgs['record']['timestamp'] as $timestamp) {
            $points[$timestamp] = array();
        }

        unset($parse->data_mesgs['record']['timestamp']);
        foreach ($parse->data_mesgs['record'] as $key => $values) {
            foreach ($values as $timestamp => $value) {
                 $points[$timestamp][$key] = $value;
            }
        }

        $data = [
            'analysis' => [],
            'points'   => []
        ];

        $data['points'][0] = $points;
        if (isset($parse->data_mesgs['lap']['timestamp'])) {
            if (is_array($parse->data_mesgs['lap']['timestamp'])) {
                foreach ($parse->data_mesgs['lap']['timestamp'] as $key => $timeEnd) {
                    $timeStart = $parse->data_mesgs['lap']['start_time'][$key];
                    reset($points);
                    $data['points'][$key] = array_filter($points, function($point) use (&$points, $timeStart, $timeEnd) {
                        $time = key($points);
                        next($points);
                        return (($time >= $timeStart) && ($time <= $timeEnd));
                    });
                }
            }
        }

        $data['analysis'] = $parse->data_mesgs['session'];

        if (isset($data['analysis']['sport'])) {
            $data['analysis']['sport'] = $this->normalizeSport(
                $data['analysis']['sport'],
                $data['analysis']['sub_sport']
            );
        }

        return $data;
    }

    private function createActivities(
        Source $source,
        ActivityCollection $activities,
        array $data
    ) : ActivityCollection
    {
        $activity = new Activity();
        $activity->setSource($source);
        $nlap = 1;

        if (isset($data['analysis']['sport'])) {
            $activity->setSport($data['analysis']['sport']);
        }

        foreach ($data['points'] as $lapId => $points) {
            $times = array_keys($points);
            $lap = new Lap(
                "L{$nlap}",
                min($times),
                max($times)
            );
            $activity->addLap($lap);
            foreach ($points as $timestamp => $values) {
                $point = new Point($timestamp);
                if (isset($values['position_lat'])) {
                    $point->setLatitude($values['position_lat']);
                    $point->setLongitude($values['position_long']);
                }
                if (isset($values['distance'])) {
                    $point->setDistanceMeters($values['distance']*1000);
                }
                if (isset($values['altitude'])) {
                    $point->setAltitudeMeters($values['altitude']);
                }
                if (isset($values['cadence'])) {
                    $point->setCadenceRPM($values['cadence']);
                }
                if (isset($values['power'])) {
                    $point->setPowerWatts($values['power']);
                }
                if (isset($values['heart_rate'])) {
                    $point->setHrBPM($values['heart_rate']);
                }
                if (isset($values['speed'])) {
                    $point->setSpeedMetersPerSecond($values['speed']);
                }

                $activity->addPoint($point);
            }
            $nlap++;
        }

        $resume = [];
        if (isset($data['analysis']['total_distance'])) {
            $resume['distanceMeters'] = $data['analysis']['total_distance'] * 1000;
        }
        if (isset($data['analysis']['total_elapsed_time'])) {
            $resume['durationSeconds'] = round($data['analysis']['total_elapsed_time']);
        }
        if (isset($data['analysis']['total_calories'])) {
            $resume['caloriesKcal'] = $data['analysis']['total_calories'];
        }
        if (count($resume)) {
            $analysis = new ResumeAnalysis($resume);
            $activity->addAnalysis($analysis);
        }

        $activity = $this->analyze($activity);
        $activities->addActivity($activity);

        return $activities;
    }

    public function readFromFile(string $fileName) : ActivityCollection
    {
        $pathInfo = pathinfo($fileName);

        $source = new Source(
            null,
            self::getSource(),
            self::getFormat(),
            $pathInfo['basename']
        );

        $activities = new ActivityCollection();
        $parse = new phpFITFileAnalysis($fileName);
        $data  = $this->normalize($parse);
        return $this->createActivities($source, $activities, $data);
    }

    public function readFromBinary(string $data) : ActivityCollection
    {
        $source = new Source(
            null,
            self::getSource(),
            self::getFormat()
        );

        $activities = new ActivityCollection();
        $parse = new phpFITFileAnalysis($data, ['input_is_data' => true]);
        $data  = $this->normalize($parse);
        return $this->createActivities($source, $activities, $data);
    }

}
