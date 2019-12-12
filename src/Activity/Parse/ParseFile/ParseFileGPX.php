<?php

namespace PhpSports\Activity\Parse\ParseFile;

use PhpSports\Activity\Parse\BaseParseFile;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;
use \DOMDocument;

// https://github.com/spatie/array-to-xml
class ParseFileGPX extends BaseParseFile
{
    const FILETYPE = 'GPX';

    private function load(DOMDocument $data) : ActivityCollection
    {
        $activities = new ActivityCollection();
        foreach ($data->getElementsByTagName('trk') as $domTrk) {
            $activity = new Activity();
            $activity->setName('activity');

            $nlap = 1;
            foreach ($domTrk->getElementsByTagName('trkseg') as $domTrkseg) {
                $lap = new Lap();
                $lap->setName("L{$nlap}");
                foreach ($domTrkseg->getElementsByTagName('trkpt') as $domTrkpt) {
                    $item = simplexml_import_dom($domTrkpt);
                    $time = new \DateTime((string) $item->time);

                    $point = new Point();
                    $point->setTimestamp($time->getTimestamp());
                    $point->setLatitude((float) $item->attributes()->lat);
                    $point->setLongitude((float) $item->attributes()->lon);
                    $point->setAlitudeMeters((int) $item->ele);

                    if ($extensions = $item->extensions) {
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

    public function loadFromFile(string $fileName) : ActivityCollection
    {
        $data = file_get_contents($fileName, true);
        $dom = new DOMDocument();
        $dom->loadXml($data);
        return $this->load($dom);
    }

    public function saveToFile(ActivityCollection $activities, string $fileName)
    {

    }

    public function loadFromBinary(string $data) : ActivityCollection
    {
        $dom = new DOMDocument();
        $dom->loadXml($data);
        return $this->load($dom);
    }

    public function saveToBinary() : string
    {

    }

    // public function load($fileName)
    // {
    //     $data = file_get_contents($fileName, true);
    //     $this->setData($data);
    // }
    //
    // public function setData($data)
    // {
    //     $dom = new DOMDocument();
    //     $dom->loadXml($data);
    //     parent::setData($dom);
    // }
    //
    // public function parse()
    // {
    //     $ids = array();
    //     foreach ($this->data->getElementsByTagName('trk') as $domTrk) {
    //         $domTime = $domTrk->getElementsByTagName('time')->item(0);
    //         $ids[] = $this->service->startTrack(new DateTime((string) $domTime->textContent));
    //
    //         $intervals = 1;
    //         foreach ($domTrk->getElementsByTagName('trkseg') as $domTrkseg) {
    //             $this->service->startTrackInterval('L' . $intervals);
    //     		foreach ($domTrkseg->getElementsByTagName('trkpt') as $domTrkpt) {
    //     			$item = simplexml_import_dom($domTrkpt);
    //                 $time = new DateTime((string) $item->time);
    //
    //     			$data = array(
    //                     'lat'          => (float) $item->attributes()->lat,
    //                     'lng'          => (float) $item->attributes()->lon,
    //                     'elevation'    => (float) $item->ele,
    //                     'time'         => $time->getTimestamp()
    //     			);
    //
    //     			if ($extensions = $item->extensions) {
    //                     //$nameSpaces = $extensions->getNamespaces(true);
    // 					$extensions = $extensions->children('http://www.garmin.com/xmlschemas/TrackPointExtension/v1');
    //                     if (count($extensions)) {
    //                         if ($extensions[0]->hr) {
    //         				    $data['hr'] = (int) $extensions[0]->hr;
    //                         }
    //                         if ($extensions[0]->cad) {
    //                             $data['cadence'] = (int) $extensions[0]->cad;
    //                         }
    //                         if ($extensions[0]->power) {
    //                             $data['power'] = (int) $extensions[0]->power;
    //                         }
    //                     }
    //     			}
    //
    //     			$this->service->addTrackPoint($data);
    //     		}
    //             $this->service->endTrackInterval();
    //             $intervals++;
    //         }
    //
    // 		$this->service->endTrack();
    // 	}
    //
    //     return $ids;
    // }
}
