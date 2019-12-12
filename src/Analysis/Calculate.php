<?php

namespace PhpSports\Analysis;

use PhpSports\Model\Point;

class Calculate
{

	public static function calculateDistanceMeters(
		Point $from,
		Point $to
	) : int
	{
		$theta = $from->getLongitude() - $to->getLongitude();
		$distance = (sin(deg2rad($from->getLatitude())) * sin(deg2rad($to->getLatitude())))
            + (cos(deg2rad($from->getLatitude())) * cos(deg2rad($to->getLatitude())) * cos(deg2rad($theta)))
        ;
		$distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
		$distance = $distance * 1.609344 * 1000;

		return  ($distance) ? $distance : 0;
	}

	public static function calculateDurationSeconds(
		Point $from,
		Point $to
	) : int
	{
		return $to->getTimestamp() - $from->getTimestamp();
	}

    public static function calculateInclineMeters(
		Point $from,
		Point $to
    ) : int
	{
		$ini      = $from->getAltitudeMeters();
		$fin      = $to->getAltitudeMeters();
		if ($ini && $fin) {
			return ($fin > $ini) ? $fin - $ini : 0;
		}
		return null;
    }
}
