<?php

namespace PhpSports\Activity\Parse\ParseFile;

use adriangibbons\phpFITFileAnalysis;
use PhpSports\Activity\Parse\BaseParseFile;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;

class ParseFileFIT extends BaseParseFile
{
    const FILETYPE = 'FIT';

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

        return $data;
    }

    private function read(array $data) : ActivityCollection
    {
        $activities = new ActivityCollection();
        $activity = new Activity('activity');
        $nlap = 1;
        foreach ($data['points'] as $lapId => $points) {
            $lap = new Lap("L{$nlap}");
            foreach ($points as $timestamp => $values) {
                $point = new Point($timestamp);
                if (isset($values['position_lat'])) {
                    $point->setLatitude($values['position_lat']);
                    $point->setLongitude($values['position_long']);
                }
                if (isset($values['distance'])) {
                    $point->setDistanceMeters($values['distance']);
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

                $lap->addPoint($point);
            }
            $activity->addLap($lap);
            $nlap++;
        }
        if (isset($data['analysis']['total_distance'])) {
            $activity->setDistanceMeters($data['analysis']['total_distance'] * 1000);
        }
        if (isset($data['analysis']['total_elapsed_time'])) {
            $activity->setDurationSeconds(round($data['analysis']['total_elapsed_time']));
        }
        $activities->addActivity($activity);

        return $activities;

        // $ids = array();
        // $timeTrack = new DateTime();
        // $timeTrack->setTimestamp(key($this->data['points'][0]));
        //
        // $intervals = 1;
        // $ids[] = $this->service->startTrack($timeTrack);
        // foreach ($this->data['points'] as $lapId => $points) {
        //     $lastDistance = 0;
        //     $this->service->startTrackInterval('L' . $intervals);
        //     foreach ($points as $timestamp => $values) {
        //         $data = array(
        //             'time'         => $timestamp
        //         );
        //
        //         if (isset($values['position_lat'])) {
        //             $data['lat']       = (float) $values['position_lat'];
        //             $data['lng']       = (float) $values['position_long'];
        //         } else {
        //             $data['distance']  = (float) ($values['distance'] - $lastDistance) * 1000;
        //             $lastDistance = (float) $values['distance'];
        //         }
        //         $data['elevation'] = (isset($values['altitude'])) ? (float) $values['altitude'] : null;
        //         $data['cadence']   = (isset($values['cadence'])) ? (float) $values['cadence'] : null;
        //         $data['power']     = (isset($values['power'])) ? (float) $values['power'] : null;
        //         $data['hr']        = (isset($values['heart_rate'])) ? (float) $values['heart_rate'] : null;
        //
        //         if (!empty($data['lat']) || !empty($data['distance'])) {
        //             $this->service->addTrackPoint($data);
        //         }
        //     }
        //
        //     $this->service->endTrackInterval();
        //     $intervals++;
        // }
        //
        // if (isset($this->data['analysis']['total_calories'])) {
        //     $this->service->addInfoToTrack('caloriesCompleted', (float) $this->data['analysis']['total_calories']);
        //     $this->service->addAnalysisItemToTrack(
        //         'calories',
        //         null,
        //         null,
        //         null,
        //         (float) $this->data['analysis']['total_calories']
        //     );
        // }
        //
        // $this->service->endTrack();
        //
        // return $ids;
    }


    public function readFromFile(string $fileName) : ActivityCollection
    {
        $parse = new phpFITFileAnalysis($fileName);
        $data  = $this->normalize($parse);
        return $this->read($data);
    }

    public function saveToFile(ActivityCollection $activities, string $fileName, bool $pretty = false)
    {
    }

    public function readFromBinary(string $data) : ActivityCollection
    {
        $parse = new phpFITFileAnalysis($data, ['input_is_data' => true]);
        $data  = $this->normalize($parse);
        return $this->read($data);
    }

    public function saveToBinary(ActivityCollection $activities, bool $pretty = false) : string
    {

    }
}
