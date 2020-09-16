<?php

namespace PhpSports\Analyzer\Calculate;

use PhpSports\Model\Point;
use PhpSports\Model\AthleteStatus;

class Calculate
{
    public static function calculateDistanceMeters(
        Point $from = null,
        Point $to
    ) {
        if (!$from) {
            return 0;
        }
        if (!is_null($to->getDistanceMeters()) && !is_null($from->getDistanceMeters())) {
            return $to->getDistanceMeters() - $from->getDistanceMeters();
        }
        if (!$from->getLatitude() || !$to->getLatitude()) {
            return null;
        }

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
        Point $from = null,
        Point $to
    ) {
        return (!$from) ? 0 : $to->getTimestamp() - $from->getTimestamp();
    }

    public static function calculateElevationGainMeters(
        Point $from = null,
        Point $to
    ) {
        if (!$from) {
            return 0;
        }
        $ini      = $from->getAltitudeMeters();
        $fin      = $to->getAltitudeMeters();
        if (is_null($ini) || is_null($fin)) {
            $ini      = $from->getElevationMeters();
            $fin      = $to->getElevationMeters();
        }
        if (!is_null($ini) && !is_null($fin)) {
            return (($fin > $ini) && ($ini > 0)) ? ($fin - $ini) : 0;
        }
        return null;
    }

    public static function calculateKcal(
        float $weightKg,
        float $durationSeconds,
        int $met = 9
    ) {
        return $met * 0.0175 * $weightKg * ($durationSeconds / 60);
    }

    public static function calculateTssFromHR(
        float $durationSeconds,
        float $avgHrBPM,
        AthleteStatus $athleteStatus
    ) {

        $intervalos = [20,30,40,50,60,70,80,100,120,140];
        $base       = ($athleteStatus->getMaxHrBPM() - 60) / count($intervalos);
        $ini        = 60;
        $fin        = 0;
        $value      = 0;
        foreach ($intervalos as $intervalo) {
            $fin = $ini + $base;
            if (($avgHrBPM >= $ini) && ($avgHrBPM <= $fin)) {
                $value = $intervalo;
            }
            $ini = $fin;
        }
        return $value * ($durationSeconds / 3600);
    }

    public static function calculateActivityIntensity(
        float $avgPowerWatts,
        AthleteStatus $athleteSatus
    ) {
        return $avgPowerWatts / $athleteSatus->getFtpPowerWatts();
    }

    public static function calculateTssFromDuration(
        float $durationSeconds
    ) {
        return ($durationSeconds / 3600) * 70;
    }

    public static function calculateTssFromFTP(
        float $durationSeconds,
        float $avgPowerWatts,
        AthleteStatus $athleteSatus
    ) {
        return ($durationSeconds * ($avgPowerWatts + 15) * self::calculateActivityIntensity($avgPowerWatts, $athleteSatus)) / ($athleteSatus->getFtpPowerWatts() * 3600) * 100;
    }

    public static function calculateTssFromLevel(
        float $durationSeconds,
        string $level = 'NORMAL'
    ) {
        switch ($level) {
            case 'MIN':
                $factor = 30;
            break;
            case 'SOFT':
                $factor = 55;
            break;
            case 'HARD':
                $factor = 80;
            break;
            case 'MAX':
                $factor = 90;
            break;
            case 'NORMAL':
            default:
                $factor = 70;
            break;
        }

        return $factor * ($durationSeconds / 3600);
    }
}
