<?php

namespace PhpSports\Activity\Parse\ParseFile;

use PhpSports\Activity\Parse\BaseParseFile;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;
use \SimpleXMLElement;

class ParseFileGPX extends BaseParseFile
{
    const FILETYPE = 'GPX';

    private function load(SimpleXMLElement $data) : ActivityCollection
    {
        $activities = new ActivityCollection();
        foreach ($data->trk as $trk) {
            $activity = new Activity();
            $activity->setName($trk->name);

            $nlap = 1;
            foreach ($trk->trkseg as $trkseg) {
                $lap = new Lap();
                $lap->setName("L{$nlap}");
                foreach ($trkseg->trkpt as $trkpt) {
                    $time = new \DateTime((string) $trkpt->time);

                    $point = new Point();
                    $point->setTimestamp($time->getTimestamp());
                    $point->setLatitude((float) $trkpt->attributes()->lat);
                    $point->setLongitude((float) $trkpt->attributes()->lon);
                    $point->setAlitudeMeters((int) $trkpt->ele);

                    if ($extensions = $trkpt->extensions) {
    					$extensions = $extensions->children('http://www.garmin.com/xmlschemas/TrackPointExtension/v1');
                        if (count($extensions)) {
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
                    $lap->addPoint($point);
                }
                $activity->addLap($lap);
                $nlap++;
            }
            $activities->addActivity($activity);
        }

        return $activities;
    }

    private function save(ActivityCollection $data) : SimpleXMLElement
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

        $sxml = new SimpleXMLElement($str);
        $ntrk = 0;
        foreach ($data as $activity) {
            $sxml->addChild('trk');
            $sxml->trk[$ntrk]->addChild('name', $activity->getName());
            $ntrkseg = 0;
            foreach ($activity->getLaps() as $lap) {
                $sxml->trk[$ntrk]->addChild('trkseg');
                $ntrkpt = 0;
                foreach ($lap->getPoints() as $point) {
                    $sxml->trk[$ntrk]->trkseg[$ntrkseg]->addChild('trkpt');
                    $sxml->trk[$ntrk]->trkseg[$ntrkseg]->trkpt[$ntrkpt]->addAttribute('lat', $point->getLatitude());
                    $sxml->trk[$ntrk]->trkseg[$ntrkseg]->trkpt[$ntrkpt]->addAttribute('lon', $point->getLongitude());
                }
            }
        }

        return $sxml;
    }

    public function loadFromFile(string $fileName) : ActivityCollection
    {
        $data = file_get_contents($fileName, true);
        $sxml = new SimpleXMLElement($data);
        return $this->load($sxml);
    }

    public function saveToFile(ActivityCollection $activities, string $fileName)
    {
        $data = $this->save($activities);
        return $data->asXML($fileName);
    }

    public function loadFromBinary(string $data) : ActivityCollection
    {
        $sxml = new SimpleXMLElement($data);
        return $this->load($sxml);
    }

    public function saveToBinary() : string
    {
        $data = $this->save($activities);
        return $data->asXML();
    }
}
