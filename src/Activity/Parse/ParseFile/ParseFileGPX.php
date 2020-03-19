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

class ParseFileGPX extends BaseParseFile implements ParseFileInterface, ParseBinaryInterface
{
    const FILEFORMAT = 'GPX';

    private function createActivities(
        Source $source,
        ActivityCollection $activities,
        SimpleXMLElement $data
    ) : ActivityCollection
    {
        foreach ($data->trk as $trk) {
            $activity = new Activity((string) $trk->name);
            $activity->setAthleteStatus($this->athleteStatus);
            $activity->setSource(clone $source);

            $nlap = 1;
            foreach ($trk->trkseg as $trkseg) {
                $lap = new Lap($nlap, "L{$nlap}");
                foreach ($trkseg->trkpt as $trkpt) {
                    $time    = new \DateTime((string) $trkpt->time);
                    $point   = new Point($time->getTimestamp());
                    $point->setLatitude((float) $trkpt->attributes()->lat);
                    $point->setLongitude((float) $trkpt->attributes()->lon);
                    $point->setElevationMeters((float) $trkpt->ele);

                    if ($extensions = $trkpt->extensions) {
    					$extensions = $extensions->children('http://www.garmin.com/xmlschemas/TrackPointExtension/v1');
                        if (count($extensions)) {
                            if ($extensions[0]->speed) {
                                $point->setSpeedMetersPerSecond((float) $extensions[0]->speed);
                            }
                            if ($extensions[0]->hr) {
                                $point->setHrBPM((int) $extensions[0]->hr);
                            }
                            if ($extensions[0]->cad) {
                                $point->setCadenceRPM((int) $extensions[0]->cad);
                            }
                            if ($extensions[0]->power) {
                                $point->setPowerWatts((int) $extensions[0]->power);
                            }
                        }
        			}
                    $activity->addPoint($point);
                    $lap->addPoint($point);
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
<gpx xmlns="http://www.topografix.com/GPX/1/1"
    xmlns:gpxx="http://www.garmin.com/xmlschemas/GpxExtensions/v3"
    xmlns:gpxtpx="http://www.garmin.com/xmlschemas/TrackPointExtension/v1"
    creator="Trainerer.com"
    version="1.0"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd http://www.garmin.com/xmlschemas/GpxExtensions/v3 http://www.garmin.com/xmlschemas/GpxExtensions/v3/GpxExtensionsv3.xsd http://www.garmin.com/xmlschemas/TrackPointExtension/v1 http://www.garmin.com/xmlschemas/TrackPointExtension/v1/TrackPointExtensionv1.xsd" />
EOD;

        $sxml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . $str);
        $ntrk = 0;
        foreach ($data as $activity) {
            $sxml->addChild('trk');
            $sxml->trk[$ntrk]->addChild('name', $activity->getTitle());
            $points = $activity->getPoints();

            $ntrkseg = 0;
            foreach ($activity->getLaps() as $lap) {
                $sxml->trk[$ntrk]->addChild('trkseg');
                $ntrkpt = 0;
                foreach ($points->filterByLap($lap) as $point) {
                    $sxml->trk[$ntrk]->trkseg[$ntrkseg]->addChild('trkpt');
                    if ($point->getLatitude()) {
                        $sxml->trk[$ntrk]->trkseg[$ntrkseg]->trkpt[$ntrkpt]->addAttribute('lat', $point->getLatitude());
                        $sxml->trk[$ntrk]->trkseg[$ntrkseg]->trkpt[$ntrkpt]->addAttribute('lon', $point->getLongitude());
                    }
                    $sxml->trk[$ntrk]->trkseg[$ntrkseg]->trkpt[$ntrkpt]->addChild('time', date("Y-m-d\TH:i:s\Z", $point->getTimestamp()));
                    if ($point->getElevationMeters()) {
                        $sxml->trk[$ntrk]->trkseg[$ntrkseg]->trkpt[$ntrkpt]->addChild('ele', $point->getElevationMeters());
                    }
                    $extensions = $sxml->trk[$ntrk]->trkseg[$ntrkseg]->trkpt[$ntrkpt]->addChild('extensions');
                    $gpxtpx = $extensions[0]->addChild('gpxtpx:TrackPointExtension', null, "http://www.garmin.com/xmlschemas/TrackPointExtension/v1");
                    if ($point->getSpeedMetersPerSecond()) {
                        $gpxtpx->addChild('gpxtpx:speed', $point->getSpeedMetersPerSecond(), "http://www.garmin.com/xmlschemas/TrackPointExtension/v1");
                    }
                    if ($point->getHrBPM()) {
                        $gpxtpx->addChild('gpxtpx:hr', $point->getHrBPM(), "http://www.garmin.com/xmlschemas/TrackPointExtension/v1");
                    }
                    if ($point->getCadenceRPM()) {
                        $gpxtpx->addChild('gpxtpx:cad', $point->getCadenceRPM(), "http://www.garmin.com/xmlschemas/TrackPointExtension/v1");
                    }
                    if ($point->getPowerWatts()) {
                        $gpxtpx->addChild('gpxtpx:power', $point->getPowerWatts(), "http://www.garmin.com/xmlschemas/TrackPointExtension/v1");
                    }
                    $ntrkpt++;
                }
                $ntrkseg++;
            }
            $ntrk++;
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
        $data   = $this->save($activities);
        if ($pretty) {
            $dom = new \DomDocument('1.0');
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
            $dom = new \DomDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($data->asXML());
            return $dom->saveXML();
        } else {
            return $data->asXML();
        }
    }

}
