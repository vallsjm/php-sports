<?php
namespace PhpSports\Activity;

use PhpSports\Model\AthleteStatus;
use PhpSports\Analyzer\AnalyzerInterface;
use PhpSports\Activity\Parse\BaseParseAPI;
use InvalidArgumentException;

abstract class BaseAPI implements AnalyzerInterface
{
    public static function createInstanceFromAPI(
        string $apiName = null,
        AthleteStatus $athleteStatus = null,
        int $options = self::ANALYZER_RESUME | self::ANALYZER_PARAMETER | self::ANALYZER_ZONE | self::ANALYZER_INTERVAL
    ) : BaseParseAPI
    {
        $instance  = null;
        $className = 'PhpSports\Activity\Parse\ParseAPI\ParseApi' . strtoupper($apiName);
        try {
            $instance = $className::createInstance($athleteStatus, $options);
        } catch (\Exception $e) {
            throw new InvalidArgumentException("API {$apiName} not suported.");
        }

        return $instance;
    }

}
