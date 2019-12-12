<?php

namespace PhpSports\Import\Parse\ParseFile;

use adriangibbons\phpFITFileAnalysis;
use PhpSports\Import\Parse\BaseParseFile;
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

    public function load(array $data) : ActivityCollection
    {
        $activities = new ActivityCollection();
        $activity = new Activity();
        $nlap = 1;
        foreach ($data['points'] as $lapId => $points) {
            $lap = new Lap();
            $lap->setName("L{$nlap}");
            foreach ($points as $timestamp => $values) {
                $point = new Point();
                $point->setTimestamp($timestamp);
                if (isset($values['position_lat'])) {
                    $point->setLatitude($values['position_lat']);
                    $point->setLongitude($values['position_long']);
                }
                if (isset($values['altitude'])) {
                    $point->setAlitudeMeters($values['altitude']);
                }
                $lap->addPoint($point);
            }
            $activity->addLap($lap);
            $nlap++;
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


    public function loadFromFile(string $fileName) : ActivityCollection
    {
        $parse = new phpFITFileAnalysis($fileName);
        $data  = $this->normalize($parse);
        return $this->load($data);
    }

    public function saveToFile(ActivityCollection $activities, string $fileName)
    {

    }

    public function loadFromBinary(string $data) : ActivityCollection
    {

    }

    public function saveToBinary() : string
    {

    }
    // public function load($fileName)
    // {
    //     $parse = new phpFITFileAnalysis($fileName);
    //     $data  = $this->normalize($parse);
    //     parent::setData($data);
    // }
    //
    // public function setData($data)
    // {
    //     $parse = new phpFITFileAnalysis($data, ['input_is_data' => true]);
    //     $data  = $this->normalize($parse);
    //     parent::setData($data);
    // }
    //
    // protected function normalize(phpFITFileAnalysis $parse)
    // {
    //     $points = array();
    //     foreach ($parse->data_mesgs['record']['timestamp'] as $timestamp) {
    //         $points[$timestamp] = array();
    //     }
    //
    //     unset($parse->data_mesgs['record']['timestamp']);
    //     foreach ($parse->data_mesgs['record'] as $key => $values) {
    //         foreach ($values as $timestamp => $value) {
    //              $points[$timestamp][$key] = $value;
    //         }
    //     }
    //
    //     $data = [
    //         'analysis' => [],
    //         'points'   => []
    //     ];
    //
    //     $data['points'][0] = $points;
    //     if (isset($parse->data_mesgs['lap']['timestamp'])) {
    //         if (is_array($parse->data_mesgs['lap']['timestamp'])) {
    //             foreach ($parse->data_mesgs['lap']['timestamp'] as $key => $timeEnd) {
    //                 $timeStart = $parse->data_mesgs['lap']['start_time'][$key];
    //                 reset($points);
    //                 $data['points'][$key] = array_filter($points, function($point) use (&$points, $timeStart, $timeEnd) {
    //                     $time = key($points);
    //                     next($points);
    //                     return (($time >= $timeStart) && ($time <= $timeEnd));
    //                 });
    //             }
    //         }
    //     }
    //
    //     $data['analysis'] = $parse->data_mesgs['session'];
    //
    //     return $data;
    // }
    //
    // public function parse()
    // {
    //     $ids = array();
    //     $timeTrack = new DateTime();
    //     $timeTrack->setTimestamp(key($this->data['points'][0]));
    //
    //     $intervals = 1;
    //     $ids[] = $this->service->startTrack($timeTrack);
    //     foreach ($this->data['points'] as $lapId => $points) {
    //         $lastDistance = 0;
    //         $this->service->startTrackInterval('L' . $intervals);
    //         foreach ($points as $timestamp => $values) {
    //             $data = array(
    //                 'time'         => $timestamp
    //             );
    //
    //             if (isset($values['position_lat'])) {
    //                 $data['lat']       = (float) $values['position_lat'];
    //                 $data['lng']       = (float) $values['position_long'];
    //             } else {
    //                 $data['distance']  = (float) ($values['distance'] - $lastDistance) * 1000;
    //                 $lastDistance = (float) $values['distance'];
    //             }
    //             $data['elevation'] = (isset($values['altitude'])) ? (float) $values['altitude'] : null;
    //             $data['cadence']   = (isset($values['cadence'])) ? (float) $values['cadence'] : null;
    //             $data['power']     = (isset($values['power'])) ? (float) $values['power'] : null;
    //             $data['hr']        = (isset($values['heart_rate'])) ? (float) $values['heart_rate'] : null;
    //
    //             if (!empty($data['lat']) || !empty($data['distance'])) {
    //                 $this->service->addTrackPoint($data);
    //             }
    //         }
    //
    //         $this->service->endTrackInterval();
    //         $intervals++;
    //     }
    //
    //     if (isset($this->data['analysis']['total_calories'])) {
    //         $this->service->addInfoToTrack('caloriesCompleted', (float) $this->data['analysis']['total_calories']);
    //         $this->service->addAnalysisItemToTrack(
    //             'calories',
    //             null,
    //             null,
    //             null,
    //             (float) $this->data['analysis']['total_calories']
    //         );
    //     }
    //
    //     $this->service->endTrack();
    //
    //     return $ids;
    // }
}
