<?php

namespace PhpSports\Activity\Parse\ParseFile;

use PhpSports\Activity\Parse\BaseParseFile;
use PhpSports\Activity\Parse\ParseFileInterface;
use PhpSports\Activity\Parse\ParseBinaryInterface;
use PhpSports\Activity\Parse\ParseArrayInterface;
use PhpSports\Analyzer\Analysis\Zone;
use PhpSports\Analyzer\Analysis\Parameter;
use PhpSports\Analyzer\Analysis\Interval;
use PhpSports\Analyzer\Analysis\ResumeAnalysis;
use PhpSports\Analyzer\Analysis\ParameterAnalysis;
use PhpSports\Analyzer\Analysis\IntervalAnalysis;
use PhpSports\Analyzer\Analysis\ZoneAnalysis;
use PhpSports\Model\AnalysisCollection;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;
use PhpSports\Model\Source;
use PhpSports\Model\AthleteStatus;
use \SimpleXMLElement;

class ParseFileKNH extends BaseParseFile implements ParseFileInterface, ParseBinaryInterface, ParseArrayInterface
{
    const FILEFORMAT = 'KNH';

    // {
    //     "structure": [
    //         "time",
    //         "lat",
    //         "lng",
    //         "elevation",
    //         "hr",
    //         "cadence"
    //     ],
    //     "laps": [
    //         [
    //             [123454, 41.23, 22.34, 34, 54, 45],
    //             [123455, 41.23, 22.34, 34, 54, 45],
    //             [123456, 41.23, 22.34, 34, 54, 45]
    //         ],
    //         [
    //             [123464, 41.23, 22.34, 34, 54, 45],
    //             [123465, 41.23, 22.34, 34, 54, 45],
    //             [123466, 41.23, 22.34, 34, 54, 45]
    //         ]
    //     ]
    // }

    public function normalizeParameters(array $parameters)
    {
        $mapParameters = [
            'time'      => 'timestamp',
            'lat'       => 'latitude',
            'lng'       => 'longitude',
            'elevation' => 'elevationMeters',
            'altitude'  => 'altitudeMeters',
            'distance'  => 'distanceMeters',
            'speed'     => 'speedMetersPerSecond',
            'cadence'   => 'cadenceRPM',
            'power'     => 'powerWatts',
            'hr'        => 'hrBPM'
        ];

        $ret = [];
        foreach ($parameters as $key) {
            $ret[] = $mapParameters[$key];
        }
        return $ret;
    }

    public function createActivity(
        Source $source,
        array $item
    ) : Activity
    {

        $newSource = clone $source;

        $activity = new Activity();
        $activity->setAthleteStatus($this->athleteStatus);
        $activity->setSource($newSource);

        if (isset($item['_id'])) {
            $activity->setId($item['_id']);
        }

        $nlap        = 1;
        $structure   = $this->normalizeParameters($item['structure']);
        $tparameter  = array_flip($structure)['timestamp'];
        $nparameters = count($structure);

        foreach ($item['laps'] as $lp) {
            $minTime = 99999999999;
            $maxTime = -99999999999;

            foreach ($lp as $pt) {
                $minTime = min($minTime, $pt[$tparameter]);
                $maxTime = max($maxTime, $pt[$tparameter]);

                $point = new Point($pt[$tparameter]);
                for ($i=0; $i < $nparameters; $i++) {
                    $param = $structure[$i];
                    switch ($param) {
                        case 'speedMetersPerSecond':
                            $point->setParameter($param, $pt[$i] / 3.6);
                        break;
                        default:
                            $point->setParameter($param, $pt[$i]);
                        break;
                    }
                }
                $activity->addPoint($point);
            }

            if (count($lp) > 1) {
                $lap = new Lap(
                    $nlap,
                    "L{$nlap}",
                    $minTime,
                    $maxTime
                );
                $activity->addLap($lap);
                $nlap++;
            }
        }

        $activity = $this->analyze($activity);

        return $activity;
    }

    private function createActivities(
        Source $source,
        ActivityCollection $activities,
        array $data
    ) : ActivityCollection
    {
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
        $json = file_get_contents($fileName, true);
        $data = json_decode($json, true);
        return $this->createActivities($source, $activities, $data);
    }

    public function readOneFromFile(string $fileName) : Activity
    {
        $activities = $this->readFromFile($fileName);
        if (count($activities) == 1) {
            return $activities[0];
        }
        return null;
    }

    public function readFromBinary(string $data) : ActivityCollection
    {
        $source = new Source(
            null,
            $this->getType(),
            $this->getFormat()
        );

        $activities = new ActivityCollection();
        $data = json_decode($data, true);
        return $this->createActivities($source, $activities, $data);
    }

    public function readOneFromBinary(string $data) : Activity
    {
        $activities = $this->readFromBinary($data);
        if (count($activities) == 1) {
            return $activities[0];
        }
        return null;
    }

    public function saveToFile(ActivityCollection $activities, string $fileName, bool $pretty = false)
    {
        $json = json_encode($activities, ($pretty) ? JSON_PRETTY_PRINT : null);
        return file_put_contents($fileName, $json);
    }

    public function saveOneToFile(Activity $activity, string $fileName, bool $pretty = false)
    {
        $json = json_encode($activity, ($pretty) ? JSON_PRETTY_PRINT : null);
        return file_put_contents($fileName, $json);
    }


    public function saveToBinary(ActivityCollection $activities, bool $pretty = false) : string
    {
        $json = json_encode($activities, ($pretty) ? JSON_PRETTY_PRINT : null);
        return $json;
    }

    public function saveOneToBinary(Activity $activity, bool $pretty = false) : string
    {
        $json = json_encode($activity, ($pretty) ? JSON_PRETTY_PRINT : null);
        return $json;
    }

    public function readFromArray(array $data) : ActivityCollection
    {
        $source = new Source(
            null,
            $this->getType(),
            $this->getFormat()
        );

        $activities = new ActivityCollection();
        return $this->createActivities($source, $activities, $data);
    }

    public function readOneFromArray(array $data) : Activity
    {
        $activities = $this->readFromArray([$data]);
        if (count($activities) == 1) {
            return $activities[0];
        }
        return null;
    }

    public function saveToArray(ActivityCollection $activities) : array
    {
        return json_decode(json_encode($activities), true);
    }

    public function saveOneToArray(Activity $activity) : array
    {
        return json_decode(json_encode($activity), true);
    }

}
