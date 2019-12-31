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
    }

}
