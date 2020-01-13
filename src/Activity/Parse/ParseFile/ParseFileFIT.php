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

    // sport={
    //    0: 'generic',
    //    1: 'running',
    //    2: 'cycling',
    //    3: 'transition',  # Mulitsport transition
    //    4: 'fitness_equipment',
    //    5: 'swimming',
    //    6: 'basketball',
    //    7: 'soccer',
    //    8: 'tennis',
    //    9: 'american_football',
    //    10: 'training',
    //    11: 'walking',
    //    12: 'cross_country_skiing',
    //    13: 'alpine_skiing',
    //    14: 'snowboarding',
    //    15: 'rowing',
    //    16: 'mountaineering',
    //    17: 'hiking',
    //    18: 'multisport',
    //    19: 'paddling',
    //    20: 'flying',
    //    21: 'e_biking',
    //    22: 'motorcycling',
    //    23: 'boating',
    //    24: 'driving',
    //    25: 'golf',
    //    26: 'hang_gliding',
    //    27: 'horseback_riding',
    //    28: 'hunting',
    //    29: 'fishing',
    //    30: 'inline_skating',
    //    31: 'rock_climbing',
    //    32: 'sailing',
    //    33: 'ice_skating',
    //    34: 'sky_diving',
    //    35: 'snowshoeing',
    //    36: 'snowmobiling',
    //    37: 'stand_up_paddleboarding',
    //    38: 'surfing',
    //    39: 'wakeboarding',
    //    40: 'water_skiing',
    //    41: 'kayaking',
    //    42: 'rafting',
    //    43: 'windsurfing',
    //    44: 'kitesurfing',
    //    45: 'tactical',
    //    46: 'jumpmaster',
    //    47: 'boxing',
    //    48: 'floor_climbing',
    //    254: 'all',  # All is for goals only to include all sports.
    // },
    //
    // sub_sport={
    //     0: 'generic',
    //     1: 'treadmill',  # Run/Fitness Equipment
    //     2: 'street',  # Run
    //     3: 'trail',  # Run
    //     4: 'track',  # Run
    //     5: 'spin',  # Cycling
    //     6: 'indoor_cycling',  # Cycling/Fitness Equipment
    //     7: 'road',  # Cycling
    //     8: 'mountain',  # Cycling
    //     9: 'downhill',  # Cycling
    //     10: 'recumbent',  # Cycling
    //     11: 'cyclocross',  # Cycling
    //     12: 'hand_cycling',  # Cycling
    //     13: 'track_cycling',  # Cycling
    //     14: 'indoor_rowing',  # Fitness Equipment
    //     15: 'elliptical',  # Fitness Equipment
    //     16: 'stair_climbing',  # Fitness Equipment
    //     17: 'lap_swimming',  # Swimming
    //     18: 'open_water',  # Swimming
    //     19: 'flexibility_training',  # Training
    //     20: 'strength_training',  # Training
    //     21: 'warm_up',  # Tennis
    //     22: 'match',  # Tennis
    //     23: 'exercise',  # Tennis
    //     24: 'challenge',
    //     25: 'indoor_skiing',  # Fitness Equipment
    //     26: 'cardio_training',  # Training
    //     27: 'indoor_walking',  # Walking/Fitness Equipment
    //     28: 'e_bike_fitness',  # E-Biking
    //     29: 'bmx',  # Cycling
    //     30: 'casual_walking',  # Walking
    //     31: 'speed_walking',  # Walking
    //     32: 'bike_to_run_transition',  # Transition
    //     33: 'run_to_bike_transition',  # Transition
    //     34: 'swim_to_bike_transition',  # Transition
    //     35: 'atv',  # Motorcycling
    //     36: 'motocross',  # Motorcycling
    //     37: 'backcountry',  # Alpine Skiing/Snowboarding
    //     38: 'resort',  # Alpine Skiing/Snowboarding
    //     39: 'rc_drone',  # Flying
    //     40: 'wingsuit',  # Flying
    //     41: 'whitewater',  # Kayaking/Rafting
    //     42: 'skate_skiing',  # Cross Country Skiing
    //     43: 'yoga',  # Training
    //     44: 'pilates',  # Training
    //     45: 'indoor_running',  # Run
    //     46: 'gravel_cycling',  # Cycling
    //     47: 'e_bike_mountain',  # Cycling
    //     48: 'commuting',  # Cycling
    //     49: 'mixed_surface',  # Cycling
    //     50: 'navigate',
    //     51: 'track_me',
    //     52: 'map',
    //     53: 'single_gas_diving',  # Diving
    //     54: 'multi_gas_diving',  # Diving
    //     55: 'gauge_diving',  # Diving
    //     56: 'apnea_diving',  # Diving
    //     57: 'apnea_hunting',  # Diving
    //     58: 'virtual_activity',
    //     59: 'obstacle',  # Used for events where participants run, crawl through mud, climb over walls, etc.
    //     254: 'all',
    // }

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

    private function read(ActivityCollection $activities, array $data) : ActivityCollection
    {
        $activity = new Activity();
        $nlap = 1;

        if (isset($data['analysis']['sport'])) {
            $activity->setSport($data['analysis']['sport']);
        }

        foreach ($data['points'] as $lapId => $points) {
            $lap = $activity->createLap("L{$nlap}");
            foreach ($points as $timestamp => $values) {
                $point = $lap->createPoint($timestamp);
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
        if (isset($data['analysis']['total_calories'])) {
            $activity->getAnalysisOrCreate('caloriesKcal')->setTotal($data['analysis']['total_calories']);
        }
        $activities->addActivity($activity);

        return $activities;
    }


    public function readFromFile(string $fileName, ActivityCollection $activities) : ActivityCollection
    {
        $this->startTimer();
        $parse = new phpFITFileAnalysis($fileName);
        $data  = $this->normalize($parse);
        return $this->stopTimerAndReturn(
            $this->read($activities, $data)
        );
    }

    public function saveToFile(ActivityCollection $activities, string $fileName, bool $pretty = false)
    {
    }

    public function readFromBinary(string $data, ActivityCollection $activities) : ActivityCollection
    {
        $this->startTimer();
        $parse = new phpFITFileAnalysis($data, ['input_is_data' => true]);
        $data  = $this->normalize($parse);
        return $this->stopTimerAndReturn(
            $this->read($activities, $data)
        );
    }

    public function saveToBinary(ActivityCollection $activities, bool $pretty = false) : string
    {

    }
}
