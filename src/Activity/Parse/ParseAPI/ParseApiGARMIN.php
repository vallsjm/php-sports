<?php

namespace PhpSports\Activity\Parse\ParseApi;

use PhpSports\Activity\Parse\BaseParseApi;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;
use \SimpleXMLElement;

class ParseApiGARMIN extends BaseParseApi
{
    const APITYPE = 'GARMIN';

    const SPORTS = [
        'ALL' => 5,
        'UNCATEGORIZED' => 5,
        'SEDENTARY' => 5,
        'SLEEP' => 5,
        'RUNNING' => 3,
        'STREET_RUNNING' => 3,
        'TRACK_RUNNING' => 3,
        'TRAIL_RUNNING' => 3,
        'TREADMILL_RUNNING' => 3,
        'CYCLING' => 8,
        'CYCLOCROSS' => 8,
        'DOWNHILL_BIKING' => 9,
        'INDOOR_CYCLING' => 10,
        'MOUNTAIN_BIKING' => 9,
        'RECUMBENT_CYCLING' => 8,
        'ROAD_BIKING' => 8,
        'TRACK_CYCLING' => 8,
        'FITNESS_EQUIPMENT' => 6,
        'ELLIPTICAL' => 5,
        'INDOOR_CARDIO' => 5,
        'INDOOR_ROWING' => 5,
        'STAIR_CLIMBING' => 5,
        'STRENGTH_TRAINING' => 6,
        'HIKING' => 3,
        'SWIMMING' => 1,
        'LAP_SWIMMING' => 1,
        'OPEN_WATER_SWIMMING' => 1,
        'WALKING' => 3,
        'CASUAL_WALKING' => 3,
        'SPEED_WALKING' => 3,
        'TRANSITION' => 5,
        'SWIMTOBIKETRANSITION' => 5,
        'BIKETORUNTRANSITION' => 5,
        'RUNTOBIKETRANSITION' => 5,
        'MOTORCYCLING' => 5,
        'OTHER' => 5,
        'BACKCOUNTRY_SKIING_SNOWBOARDING' => 5,
        'BOATING' => 5,
        'CROSS_COUNTRY_SKIING' => 5,
        'DRIVING_GENERAL' => 5,
        'FLYING' => 5,
        'GOLF' => 5,
        'HORSEBACK_RIDING' => 5,
        'INLINE_SKATING' => 5,
        'MOUNTAINEERING' => 5,
        'PADDLING' => 5,
        'RESORT_SKIING_SNOWBOARDING' => 5,
        'ROWING' => 5,
        'SAILING' => 5,
        'SKATE_SKIING' => 5,
        'SKATING' => 5,
        'SNOWMOBILING' => 5,
        'SNOW_SHOE' => 5,
        'STAND_UP_PADDLEBOARDING' => 5,
        'WHITEWATER_RAFTING_KAYAKING' => 5,
        'WIND_KITE_SURFING' => 5
    ];


    public function readFromBinary(array $data, ActivityCollection $activities) : ActivityCollection
    {
        $ids = array();

        foreach ($data as $activityId => $act) {
            $activity = new Activity();
            $activity->setId($activityId);
            $activity->setSport($act['info']['sport']);

            // procesar laps...
            foreach ($act['data'] as $name => $lp) {
                $lap = $activity->createLap($name);

                foreach ($lp as $pos => $pt) {
                    $point = $lap->createPoint($pt['startTimeInSeconds'] + $act['info']['startTimeOffsetInSeconds']);
                    if (isset($pt['latitudeInDegree'])) {
                        $point->setLatitude((float) $pt['latitudeInDegree']);
                        $point->setLongitude((float) $pt['longitudeInDegree']);
                    } elseif (isset($pt['totalDistanceInMeters'])) {
                        $point->setDistanceMeters($pt['totalDistanceInMeters']*1000);
                    }
                    if (isset($pt['elevationInMeters'])) {
                        $point->setAltitudeMeters($pt['elevationInMeters']);
                    }
                    if (isset($pt['heartRate'])) {
                        $point->setHrBPM($pt['heartRate']);
                    }
                    if (isset($pt['bikeCadenceInRPM'])) {
                        $point->setCadenceRPM($pt['bikeCadenceInRPM']);
                    }
                    if (isset($pt['powerInWatts'])) {
                        $point->setPowerWatts($pt['powerInWatts']);
                    }
                    $lap->addPoint($point);
                }

                // $incline  = (isset($activity['info']['totalElevationGainInMeters'])) ? (float) ($activity['info']['totalElevationGainInMeters']) : 0;

                // if (isset($lp['analysis'])) {
                //     $structure = array_flip($lp['analysis']['structure']);
                //     foreach ($lp['analysis']['parameters'] as $values) {
                //         $pos1 = $structure['parameter'];
                //         $pos2 = $structure['intervalTimeSeconds'];
                //         $parameter = $lap->getAnalysisOrCreate($values[$pos1], $values[$pos2]);
                //         $pos = $structure['valueMin'];
                //         $parameter->setMin($values[$pos]);
                //         $pos = $structure['valueMax'];
                //         $parameter->setMax($values[$pos]);
                //         $pos = $structure['valueAvg'];
                //         $parameter->setAvg($values[$pos]);
                //         $pos = $structure['valueTotal'];
                //         $parameter->setTotal($values[$pos]);
                //     }
                // }
                //
                // $lap->setDistanceMeters($lp['resume']['distanceMeters']);
                // $lap->setDurationSeconds($lp['resume']['durationSeconds']);

                $activity->addLap($lap);
            }

            if (isset($act['info']['distanceInMeters'])) {
                $activity->setDistanceMeters($act['info']['distanceInMeters']);
            }
            if (isset($act['info']['durationInSeconds'])) {
                $activity->setDurationSeconds($act['info']['durationInSeconds']);
            }

            // if (isset($act['analysis'])) {
            //     $structure = array_flip($act['analysis']['structure']);
            //     foreach ($act['analysis']['parameters'] as $values) {
            //         $pos1 = $structure['parameter'];
            //         $pos2 = $structure['intervalTimeSeconds'];
            //         $parameter = $activity->getAnalysisOrCreate($values[$pos1], $values[$pos2]);
            //         $pos = $structure['valueMin'];
            //         $parameter->setMin($values[$pos]);
            //         $pos = $structure['valueMax'];
            //         $parameter->setMax($values[$pos]);
            //         $pos = $structure['valueAvg'];
            //         $parameter->setAvg($values[$pos]);
            //         $pos = $structure['valueTotal'];
            //         $parameter->setTotal($values[$pos]);
            //     }
            // }
            //
            // $activity->setDistanceMeters($act['resume']['distanceMeters']);
            // $activity->setDurationSeconds($act['resume']['durationSeconds']);

            $activities->addActivity($activity);
        }

        // foreach ($data  as $activityId => $activity) {
        //     $timeBase = new DateTime();
        //     $timeBase->setTimestamp($activity['info']['startTimeInSeconds'] + $activity['info']['startTimeOffsetInSeconds']);
        //
        //     $ids[] = $this->service->startTrack($timeBase, array(
        //         'garmin_id' => $activityId,
        //         'sport_id'  => $activity['info']['sport_id']
        //     ));
        //
        //     foreach ($activity['data'] as $name => $data) {
        //         $lastDistance = 0;
        //         $this->service->startTrackInterval($name);
        //         foreach ($data as $pos => $garmin) {
        //             $point = array(
        //                 'time' => $garmin['startTimeInSeconds'] + $activity['info']['startTimeOffsetInSeconds']
        //             );
        //
        //             if (isset($garmin['latitudeInDegree'])) {
        //                 $point['lat']       = (float) $garmin['latitudeInDegree'];
        //                 $point['lng']       = (float) $garmin['longitudeInDegree'];
        //             } elseif (isset($garmin['totalDistanceInMeters'])) {
        //                 $point['distance']  = (float) ($garmin['totalDistanceInMeters'] - $lastDistance);
        //                 $lastDistance = (float) $garmin['totalDistanceInMeters'];
        //             }
        //
        //             $point['elevation'] = (isset($garmin['elevationInMeters'])) ? $garmin['elevationInMeters'] : null;
        //             $point['hr']        = (isset($garmin['heartRate'])) ? $garmin['heartRate'] : null;
        //             $point['cadence']   = (isset($garmin['bikeCadenceInRPM'])) ? $garmin['bikeCadenceInRPM'] : null;
        //             $point['power']     = (isset($garmin['powerInWatts'])) ? $garmin['powerInWatts'] : null;
        //
        //             $this->service->addTrackPoint($point);
        //         }
        //         $this->service->endTrackInterval();
        //     }
        //
        //     $distance = (isset($activity['info']['distanceInMeters'])) ? (float) ($activity['info']['distanceInMeters']) : 0;
        //     $duration = (isset($activity['info']['durationInSeconds'])) ? (float) ($activity['info']['durationInSeconds'] / 60) : 0;
        //     $incline  = (isset($activity['info']['totalElevationGainInMeters'])) ? (float) ($activity['info']['totalElevationGainInMeters']) : 0;
        //     $tss = Calculate::calculateTssFromLevel('NORMAL', $duration);
        //
        //     $this->service->addInfoToTrack('distanceCompleted', $distance);
        //     $this->service->addInfoToTrack('durationCompleted', $duration);
        //     $this->service->addInfoToTrack('inclineCompleted', $incline);
        //     $this->service->addInfoToTrack('tssCompleted', $tss);
        //     if (isset($activity['info']['activeKilocalories'])) {
        //         $this->service->addInfoToTrack('caloriesCompleted', (float) $activity['info']['activeKilocalories']);
        //         $this->service->addAnalysisItemToTrack(
        //             'calories',
        //             null,
        //             null,
        //             null,
        //             (float) $activity['info']['activeKilocalories']
        //         );
        //     }
        //
        //     $this->service->endTrack();
        // }

    }

}
