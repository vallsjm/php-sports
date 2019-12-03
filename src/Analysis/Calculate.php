<?php

namespace PhpSports\Analysis;

class Calculate
{
	public static function calculateDistance(
		array $from,
		array $to
	) {
		if (empty($from['lat']) || empty($to['lat'])) {
			return $to['distance'];
		}

		$latitude1  = (float) $from['lat'];
		$latitude2  = (float) $to['lat'];
		$longitude1 = (float) $from['lng'];
		$longitude2 = (float) $to['lng'];

        $theta = $longitude1 - $longitude2;
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)))
            + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)))
        ;
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
	  	$distance = (is_nan($distance)) ? 0 : $distance * 1.609344 * 1000;

        return  ($distance) ? $distance : 0; // Retorna m
    }

    public static function calculateDuration(
    	array $from,
    	array $to
    ) {
		$ini      = $from['time'];
		$fin      = $to['time'];
		$time = ($fin - $ini) / 60; // Retorna m
		return ($time) ? $time : null;
    }

    public static function calculateIncline(
    	array $from,
    	array $to
    ) {
		$ini      = $from['elevation'];
		$fin      = $to['elevation'];
		if ($ini && $fin) {
			return ($fin > $ini) ? $fin - $ini : 0;
		}
		return null;
    }

    public static function calculateCalories(
    	$met,
    	$weight,
    	$duration
    ) {
    	return $met * 0.0175 * $weight * $duration;
    }

    public static function calculateTss(
    	$maxHR,
    	$avgHR,
    	$duration
    ) {
    	$intervalos = array(20,30,40,50,60,70,80,100,120,140);
    	$base = ($maxHR - 60) / count($intervalos);
    	$ini = 60;
    	$fin = 0;
    	$value = 0;
    	foreach ($intervalos as $intervalo) {
    		$fin = $ini + $base;
    		if (($avgHR >= $ini) && ($avgHR <= $fin)) {
    			$value = $intervalo;
    		}
    		$ini = $fin;
    	}
    	return $value * ($duration / 60);
    }

    public static function calculateTssFromLevel(
        $level,
        $duration
    ) {
        switch ($level) {
            case 'SOFT':
                $factor = 50;
            break;
            case 'NORMAL':
                $factor = 70;
            break;
            case 'HARD':
                $factor = 80;
            break;
            case 'MAX':
                $factor = 90;
            break;
        }

        return $factor * ($duration / 60);
    }

    public static function calculateSpeed(
    	array $from
    ) {
  		return ($from['duration'] && $from['distance']) ? (($from['distance'] / 1000) / ($from['duration'] / 60)): 0;
	}

    // https://stackoverflow.com/questions/24705011/how-to-optimise-a-exponential-moving-average-algorithm-in-php
    // http://php.net/manual/en/function.trader-ema.php
    // https://github.com/markrogoyski/math-php/blob/master/src/Statistics/Average.php
    public static function calculateEMA(
        $data,
        $paramEMA = 'ema',
        $paramTSS = 'tss',
        $n = 42
    )
    {
        if (!count($data))
            return $data;

        // https://github.com/markrogoyski/math-php
        // Average::exponentialMovingAverage($numbers, $n);
        reset($data);
        $first = key($data);
        $data[$first][$paramEMA] = $data[$first][$paramTSS];

        $j = 1;
        $i = $first;
        foreach ($data as $pos => &$values) {
            // Each day after: EMAtoday = α⋅xtoday + (1-α)EMAyesterday
            $a = ($j < $n) ? (2 / ($j +1)) : (2 / ($n +1));
            $values[$paramEMA] = ($a * $values[$paramTSS]) + ((1 - $a) * $data[$i][$paramEMA]);
            $i = $pos;
            $j++;
        }

        return $data;
    }

    public static function calculateFS(
        $data,
        $paramCTL = 'ctl',
        $paramATL = 'atl',
        $paramTSB = 'tsb',
        $paramTSS = 'tss',
        $n = 75,
        $m = 13
    )
    {
        if (!count($data))
            return $data;

        // https://github.com/markrogoyski/math-php
        // Average::exponentialMovingAverage($numbers, $n);
        reset($data);
        $first = key($data);
        $data[$first][$paramCTL] = $data[$first][$paramTSS];
        $data[$first][$paramATL] = $data[$first][$paramTSS];
        $data[$first][$paramTSB] = $data[$first][$paramTSS];

        $j = 1;
        $i = $first;
        foreach ($data as $pos => &$values) {
            // Each day after: EMAtoday = α⋅xtoday + (1-α)EMAyesterday
            $a = ($j < $n) ? (2 / ($j +1)) : (2 / ($n +1));
            $values[$paramCTL] = ($a * $values[$paramTSS]) + ((1 - $a) * $data[$i][$paramCTL]);

            $b = ($j < $m) ? (2 / ($j +1)) : (2 / ($m +1));
            $values[$paramATL] = ($b * $values[$paramTSS]) + ((1 - $b) * $data[$i][$paramATL]);

            $values[$paramTSB] = $data[$i][$paramCTL] - $data[$i][$paramATL];

            $i = $pos;
            $j++;
        }

        return $data;
    }

	public static function calculateFitnessStatus(
		$trendTSB
	)
	{
		if (($trendTSB < -50) || ($trendTSB > 80)) {
			return  0;
		}
		if ((-50 <= $trendTSB) && ($trendTSB < 15)) {
			return 98 * (($trendTSB+50) / (15+50));
		}
		if ((15 <= $trendTSB) && ($trendTSB <= 25)) {
			return 98;
		}
		if ((25 < $trendTSB) && ($trendTSB <= 80)) {
			return 98 - (98 * (($trendTSB-25) / (80-25)));
		}
		return 0;
	}

	public static function calculateTrendTSB(
		$data,
		$paramTSB = 'tsb',
		$maxData = 10
	)
	{
		// https://richardathome.wordpress.com/2006/01/25/a-php-linear-regression-function/
		$maxData = ($maxData > count($data)) ? count($data) : $maxData;
		$tsb     = array_column($data, $paramTSB);
		$tsb     = array_slice($tsb, count($tsb)-$maxData, $maxData);
		$n       = count($tsb);

		$x_sum  = array_sum(array_keys($tsb)) + $n;
		$y_sum  = array_sum($tsb);
		$xx_sum = 0;
		$xy_sum = 0;

		for($i = 0; $i < $n; $i++) {
		    $xy_sum+=(($i+1)*$tsb[$i]);
		    $xx_sum+=(($i+1)*($i+1));
		}

		return (($n * $xy_sum) - ($x_sum * $y_sum)) / (($n * $xx_sum) - ($x_sum * $x_sum));
	}

    public static function calculateRecoveryTimeFromTss(
        $tss
    ) {
        if (in_array(round($tss), range(0,200))) {
           return 'LOW';
        }
        if (in_array(round($tss), range(200, 350))) {
           return 'MEDIUM';
        }
        if (in_array(round($tss), range(350,500))) {
           return 'HIGH';
        }
        return 'VERYHIGH';
    }

    public static function calculateIntensityFromHR(
        $maxHR,
        $avgHR
    ) {
        $ret = ($maxHR > 0) ? (($avgHR / $maxHR) * 100) : 0;
		$ret = ($ret > 100) ? 100 : $ret;
		$ret = ($ret < 0) ? 0 : $ret;
        return $ret;
    }

    public static function calculateIntensityFromFTP(
        $ftp,
        $avgPower
    ) {
        $ret = ($ftp > 0) ? (($avgPower / $ftp) * 100) : 0;
		$ret = ($ret > 100) ? 100 : $ret;
		$ret = ($ret < 0) ? 0 : $ret;
        return $ret;
    }

	public static function calculateIntensityFromTSS(
        $avgTss,
		$duration
    ) {
		$ret = $avgTss / ($duration / 60);
		$ret = ($ret > 90) ? 90 : $ret;
		$ret = ($ret < 50) ? 50 : $ret;
        return $ret;
    }

}
