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
		$earthRadius = 6371000;

		// convert from degrees to radians
		$latFrom  = deg2rad($from->getLatitude());
		$lonFrom  = deg2rad($from->getLongitude());
		$latTo    = deg2rad($to->getLatitude());
		$lonTo    = deg2rad($to->getLongitude());

		$latDelta = $latTo - $latFrom;
		$lonDelta = $lonTo - $lonFrom;

		$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
			cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

	  	return $angle * $earthRadius;
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
