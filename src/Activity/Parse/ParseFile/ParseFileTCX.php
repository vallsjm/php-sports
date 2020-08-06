<?php

namespace PhpSports\Activity\Parse\ParseFile;

use PhpSports\Activity\Parse\BaseParseFile;
use PhpSports\Activity\Parse\ParseFileInterface;
use PhpSports\Activity\Parse\ParseBinaryInterface;
use PhpSports\Analyzer\Analysis\ResumeAnalysis;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;
use PhpSports\Model\Source;
use \SimpleXMLElement;

class ParseFileTCX extends BaseParseFile implements ParseFileInterface, ParseBinaryInterface
{
    const FILEFORMAT = 'TCX';

    const SPORTS = [
        'RUNNING_MOUNTAIN' => 'Running',
        'RUNNING_STREET'   => 'Running',
        'RUNNING_INDOOR'   => 'Running',
        'RUNNING'          => 'Running',
        'CYCLING_MOUNTAIN' => 'Biking',
        'CYCLING_STREET'   => 'Biking',
        'CYCLING_INDOOR'   => 'Biking',
        'CYCLING'          => 'Biking',
        'SWIMMING'         => 'Other',
        'FITNESS'          => 'Other',
        'OTHER'            => 'Other'
    ];

    public function normalizeSport(string $sport = null)
    {
        if (!$sport) {
            return null;
        }
        $key = ucfirst(strtolower($sport));
        $mapSports = array_flip(self::SPORTS);
        if (isset($mapSports[$key])) {
            return $mapSports[$key];
        }
        return null;
    }

    public function denormalizeSport(string $sport = null)
    {
        if (!$sport) {
            return null;
        }
        $key = strtoupper($sport);
        if (isset(self::SPORTS[$key])) {
            return self::SPORTS[$key];
        }
        return null;
    }

    private function createActivities(
        Source $source,
        ActivityCollection $activities,
        SimpleXMLElement $data
    ) : ActivityCollection {
        foreach ($data->Activities->Activity as $act) {
            $activity = new Activity();
            $activity->setAthleteStatus($this->athleteStatus);
            $activity->setSource(clone $source);
            $activity->setId((string) $act->Id);
            $activity->setSport(
                $this->normalizeSport($act->attributes()->Sport)
            );

            $nlap = 1;
            foreach ($act->Lap as $lp) {
                $lap = new Lap($nlap, "L{$nlap}");
                foreach ($lp->Track->Trackpoint as $pt) {
                    $time  = new \DateTime((string) $pt->Time);
                    $point = new Point($time->getTimestamp());
                    if ($pt->Position) {
                        $point->setLatitude((float) $pt->Position->LatitudeDegrees);
                        $point->setLongitude((float) $pt->Position->LongitudeDegrees);
                    }
                    if ($pt->AltitudeMeters) {
                        $point->setAltitudeMeters((float) $pt->AltitudeMeters);
                    }
                    if ($pt->DistanceMeters) {
                        $point->setDistanceMeters((float) $pt->DistanceMeters);
                    }
                    if ($pt->HeartRateBpm) {
                        $point->setHrBPM((int) $pt->HeartRateBpm->Value);
                    }
                    if ($pt->Cadence) {
                        $point->setCadenceRPM((int) $pt->Cadence);
                    }

                    if ($extensions = $pt->Extensions) {
                        $extensions = $extensions->children('http://www.garmin.com/xmlschemas/ActivityExtension/v2');
                        if (count($extensions)) {
                            if ($extensions[0]->RunCadence) {
                                $point->setCadenceRPM((int) $extensions[0]->RunCadence);
                            }
                            if ($extensions[0]->Speed) {
                                $point->setSpeedMetersPerSecond((float) $extensions[0]->Speed);
                            }
                            if ($extensions[0]->Watts) {
                                $point->setPowerWatts((int) $extensions[0]->Watts);
                            }
                        }
                    }

                    $activity->addPoint($point);
                    $lap->addPoint($point);
                }

                $resume = [];
                if ($lp->DistanceMeters) {
                    $resume['distanceMeters'] = (float) $lp->DistanceMeters;
                }
                if ($lp->TotalTimeSeconds) {
                    $resume['durationSeconds'] = (float) $lp->TotalTimeSeconds;
                }
                if ($lp->Calories) {
                    $resume['caloriesKcal'] = (float) $lp->Calories;
                }
                if (count($resume)) {
                    $analysis = new ResumeAnalysis($resume);
                    $lap->addAnalysis($analysis);
                }
                $activity->addLap($lap);
                $nlap++;
            }

            $activity = $this->analyze($activity);
            $activities->addActivity($activity);
        }

        return $activities;
    }

    private function createXML(ActivityCollection $data) : SimpleXMLElement
    {
        $str = <<<'EOD'
<TrainingCenterDatabase
    xsi:schemaLocation="http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2 http://www.garmin.com/xmlschemas/TrainingCenterDatabasev2.xsd"
    xmlns:ns5="http://www.garmin.com/xmlschemas/ActivityGoals/v1"
    xmlns:ns3="http://www.garmin.com/xmlschemas/ActivityExtension/v2"
    xmlns:ns2="http://www.garmin.com/xmlschemas/UserProfile/v2"
    xmlns="http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:ns4="http://www.garmin.com/xmlschemas/ProfileExtension/v1" />
EOD;

        $sxml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>' . $str);
        $sxml->addChild('Activities');

        $nactivity = 0;
        foreach ($data as $activity) {
            $sxml->Activities->addChild('Activity');
            if ($activity->getSport()) {
                $sxml->Activities->Activity[$nactivity]->addAttribute('Sport', $this->denormalizeSport($activity->getSport()));
            }
            $sxml->Activities->Activity[$nactivity]->addChild('Id', $activity->getId());
            $points = $activity->getPoints();
            $nlap = 0;
            foreach ($activity->getLaps() as $lap) {
                $sxml->Activities->Activity[$nactivity]->addChild('Lap');
                $sxml->Activities->Activity[$nactivity]->Lap[$nlap]->addAttribute('StartTime', date("Y-m-d\TH:i:s\Z", $lap->getTimestampFrom()));

                if ($resume = $lap->getAnalysis()->filterByName('resume')) {
                    $resume = $resume->jsonSerialize();
                    if (isset($resume['distanceMeters'])) {
                        $sxml->Activities->Activity[$nactivity]->Lap[$nlap]->addChild('DistanceMeters', $resume['distanceMeters']);
                    }
                    if (isset($resume['durationSeconds'])) {
                        $sxml->Activities->Activity[$nactivity]->Lap[$nlap]->addChild('TotalTimeSeconds', $resume['durationSeconds']);
                    }
                    if (isset($resume['caloriesKcal'])) {
                        $sxml->Activities->Activity[$nactivity]->Lap[$nlap]->addChild('Calories', $resume['caloriesKcal']);
                    }
                }

                $track = $sxml->Activities->Activity[$nactivity]->Lap[$nlap]->addChild('Track');
                $ntrkpt = 0;
                foreach ($points->filterByLap($lap) as $point) {
                    $track->addChild('Trackpoint');
                    $track->Trackpoint[$ntrkpt]->addChild('Time', date("Y-m-d\TH:i:s\Z", $point->getTimestamp()));
                    if ($point->getLatitude()) {
                        $track->Trackpoint[$ntrkpt]->addChild('Position');
                        $track->Trackpoint[$ntrkpt]->Position->addChild('LatitudeDegrees', $point->getLatitude());
                        $track->Trackpoint[$ntrkpt]->Position->addChild('LongitudeDegrees', $point->getLongitude());
                    }
                    if ($point->getAltitudeMeters()) {
                        $track->Trackpoint[$ntrkpt]->addChild('AltitudeMeters', $point->getAltitudeMeters());
                    }
                    if ($point->getDistanceMeters()) {
                        $track->Trackpoint[$ntrkpt]->addChild('DistanceMeters', $point->getDistanceMeters());
                    }
                    if ($point->getHrBPM()) {
                        $track->Trackpoint[$ntrkpt]->addChild('HeartRateBpm');
                        $track->Trackpoint[$ntrkpt]->HeartRateBpm->addAttribute('xsi:type', 'HeartRateInBeatsPerMinute_t');
                        $track->Trackpoint[$ntrkpt]->HeartRateBpm->addChild('Value', $point->getHrBPM());
                    }

                    $track->Trackpoint[$ntrkpt]->addChild('Extensions');
                    $tpx = $track->Trackpoint[$ntrkpt]->Extensions->addChild('TPX', null, "http://www.garmin.com/xmlschemas/ActivityExtension/v2");
                    if ($point->getSpeedMetersPerSecond()) {
                        $tpx->addChild('Speed', $point->getSpeedMetersPerSecond());
                    }
                    if ($point->getCadenceRPM()) {
                        $tpx->addChild('RunCadence', $point->getCadenceRPM());
                    }
                    if ($point->getPowerWatts()) {
                        $tpx->addChild('Watts', $point->getPowerWatts());
                    }
                    $ntrkpt++;
                }
                $nlap++;
            }
            $nactivity++;
        }

        return $sxml;
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
        $data = file_get_contents($fileName, true);
        $sxml = new SimpleXMLElement($data);
        return $this->createActivities($source, $activities, $sxml);
    }

    public function saveToFile(ActivityCollection $activities, string $fileName, bool $pretty = false)
    {
        $data   = $this->createXML($activities);
        if ($pretty) {
            $dom = new \DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($data->asXML());
            return $dom->save($fileName);
        } else {
            return $data->asXML($fileName);
        }
    }

    public function readFromBinary(string $data) : ActivityCollection
    {
        $source = new Source(
            null,
            $this->getType(),
            $this->getFormat()
        );

        $activities = new ActivityCollection();
        $sxml = new SimpleXMLElement($data);
        return $this->createActivities($source, $activities, $sxml);
    }

    public function saveToBinary(ActivityCollection $activities, bool $pretty = false) : string
    {
        $data = $this->createXML($activities);
        if ($pretty) {
            $dom = new \DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($data->asXML());
            return $dom->saveXML();
        } else {
            return $data->asXML();
        }
    }
}
