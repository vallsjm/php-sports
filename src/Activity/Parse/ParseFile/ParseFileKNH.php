<?php

namespace PhpSports\Activity\Parse\ParseFile;

use PhpSports\Activity\Parse\BaseParseFile;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Activity;
use PhpSports\Model\Analysis;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;
use \SimpleXMLElement;

class ParseFileKNH extends BaseParseFile
{
    const FILETYPE = 'KNH';

    private function read(ActivityCollection $activities, array $data) : ActivityCollection
    {
        foreach ($data as $act) {
            $activity = new Activity($act['name']);
            $activity->setId($act['id']);
            $activity->setSport($act['sport']);

            // procesar laps...
            foreach ($act['laps'] as $lp) {
                $lap = $activity->createLap($lp['name']);

                $structure = array_flip($lp['track']['structure']);
                foreach ($lp['track']['points'] as $p) {
                    $point = $lap->createPoint();
                    foreach ($structure as $key => $pos) {
                        $point->setParameter($key, $p[$pos]);
                    }
                    $lap->addPoint($point);
                }

                if (isset($lp['analysis'])) {
                    $structure = array_flip($lp['analysis']['structure']);
                    foreach ($lp['analysis']['parameters'] as $values) {
                        $pos1 = $structure['parameter'];
                        $pos2 = $structure['intervalTimeSeconds'];
                        $parameter = $lap->getAnalysisOrCreate($values[$pos1], $values[$pos2]);
                        $pos = $structure['valueMin'];
                        $parameter->setMin($values[$pos]);
                        $pos = $structure['valueMax'];
                        $parameter->setMax($values[$pos]);
                        $pos = $structure['valueAvg'];
                        $parameter->setAvg($values[$pos]);
                        $pos = $structure['valueTotal'];
                        $parameter->setTotal($values[$pos]);
                    }
                }

                $lap->setDistanceMeters($lp['resume']['distanceMeters']);
                $lap->setDurationSeconds($lp['resume']['durationSeconds']);

                $activity->addLap($lap);
            }

            if (isset($act['analysis'])) {
                $structure = array_flip($act['analysis']['structure']);
                foreach ($act['analysis']['parameters'] as $values) {
                    $pos1 = $structure['parameter'];
                    $pos2 = $structure['intervalTimeSeconds'];
                    $parameter = $activity->getAnalysisOrCreate($values[$pos1], $values[$pos2]);
                    $pos = $structure['valueMin'];
                    $parameter->setMin($values[$pos]);
                    $pos = $structure['valueMax'];
                    $parameter->setMax($values[$pos]);
                    $pos = $structure['valueAvg'];
                    $parameter->setAvg($values[$pos]);
                    $pos = $structure['valueTotal'];
                    $parameter->setTotal($values[$pos]);
                }
            }

            $activity->setDistanceMeters($act['resume']['distanceMeters']);
            $activity->setDurationSeconds($act['resume']['durationSeconds']);

            $activities->addActivity($activity);
        }

        return $activities;
    }

    private function save(ActivityCollection $data) : ActivityCollection
    {
        return $data;
    }

    public function readFromFile(string $fileName, ActivityCollection $activities) : ActivityCollection
    {
        $this->startTimer();
        $json = file_get_contents($fileName, true);
        $data = json_decode($json, true);
        return $this->stopTimerAndReturn(
            $this->read($activities, $data)
        );
    }

    public function saveToFile(ActivityCollection $activities, string $fileName, bool $pretty = false)
    {
        $this->startTimer();
        $data = $this->save($activities);
        $json = json_encode($data, ($pretty) ? JSON_PRETTY_PRINT : null);
        return $this->stopTimerAndReturn(
            file_put_contents($fileName, $json)
        );
    }

    public function readFromBinary(string $data, ActivityCollection $activities) : ActivityCollection
    {
        $this->startTimer();
        $data = json_decode($data, true);
        return $this->stopTimerAndReturn(
            $this->read($activities, $data)
        );
    }

    public function saveToBinary(ActivityCollection $activities, bool $pretty = false) : string
    {
        $this->startTimer();
        $data = $this->save($activities);
        $json = json_encode($data, ($pretty) ? JSON_PRETTY_PRINT : null);
        return $this->stopTimerAndReturn(
            file_put_contents($fileName, $json)
        );
    }
}
