<?php

namespace PhpSports\Activity\Parse\ParseFile;

use PhpSports\Activity\Parse\BaseParseFile;
use PhpSports\Activity\Parse\ParseFileInterface;
use PhpSports\Activity\Parse\ParseBinaryInterface;
use PhpSports\Analyzer\Analysis\Zone;
use PhpSports\Analyzer\Analysis\Parameter;
use PhpSports\Analyzer\Analysis\Interval;
use PhpSports\Analyzer\Analysis\ResumeAnalysis;
use PhpSports\Analyzer\Analysis\ParameterAnalysis;
use PhpSports\Analyzer\Analysis\IntervalAnalysis;
use PhpSports\Analyzer\Analysis\ZoneAnalysis;
use PhpSports\Model\AnalysisCollection;
use PhpSports\Model\ActivityCollection;
use PhpSports\Model\Activity;
use PhpSports\Model\Lap;
use PhpSports\Model\Point;
use PhpSports\Model\Source;
use PhpSports\Model\AthleteStatus;
use \SimpleXMLElement;
// https://app.trainerer.com/data/athletes/d7f5f8df454117a1ef73fe136d4ad78c/resume/event-resume-255443.json

class ParseFileKNH extends BaseParseFile implements ParseFileInterface, ParseBinaryInterface
{
    const FILEFORMAT = 'KNH';

    private function generateAnalysis(array $data) : AnalysisCollection
    {
        $analysisCollection = new AnalysisCollection();

        if (isset($data['resume'])) {
            $analysis = new ResumeAnalysis($data['resume']);
            $analysisCollection->addAnalysis($analysis);
        }
        if (isset($data['parameters'])) {
            $analysis = new ParameterAnalysis();
            foreach ($data['parameters'] as $value) {
                $parameter = new Parameter(
                    $value['parameter'],
                    $value['minValue'],
                    $value['avgValue'],
                    $value['maxValue']
                );
                $analysis->addParameter($parameter);
            }
            $analysisCollection->addAnalysis($analysis);
        }
        if (isset($data['intervals'])) {
            $analysis = new IntervalAnalysis();
            foreach ($data['intervals'] as $value) {
                $parameter = new Interval(
                    $value['parameter'],
                    $value['timeIntervalSeconds'],
                    $value['minAvg'],
                    $value['maxAvg']
                );
                $analysis->addInterval($parameter);
            }
            $analysisCollection->addAnalysis($analysis);
        }
        foreach (['zonesHR','zonesPOWER'] as $zoneName) {
            if (isset($data[$zoneName])) {
                $analysis = new ZoneAnalysis($zoneName);
                foreach ($data[$zoneName] as $value) {
                    $parameter = new Zone(
                        $value['name'],
                        $value['minPercent'],
                        $value['maxPercent'],
                        $value['durationSeconds'],
                        $value['avgPowerWatts'],
                        $value['avgSpeedMetersPerSecond']
                    );
                    $analysis->addZone($parameter);
                }
                $analysisCollection->addAnalysis($analysis);
            }
        }

        return $analysisCollection;
    }

    private function createActivities(
        ActivityCollection $activities,
        array $data
    ) : ActivityCollection
    {
        foreach ($data as $act) {
            $activity = new Activity($act['title']);
            $activity->setId($act['id']);
            $activity->setSport($act['sport']);
            $activity->setTimestampOffset((int) $act['timestampOffset']);

            if (isset($this->athleteStatus)) {
                $activity->setAthleteStatus($this->athleteStatus);
            } elseif (isset($act['athleteStatus'])) {
                $athlete = new AthleteStatus(
                    $act['athleteStatus']['id'],
                    $act['athleteStatus']['maxHrBPM'],
                    $act['athleteStatus']['ftpPowerWatts'],
                    $act['athleteStatus']['gender'],
                    $act['athleteStatus']['ageYears'],
                    $act['athleteStatus']['weightKg'],
                    $act['athleteStatus']['heightMetters']
                );
                $activity->setAthleteStatus($athlete);
            }

            if (isset($act['source'])) {
                $source = new Source(
                    $act['source']['id'],
                    $act['source']['type'],
                    $act['source']['format'],
                    $act['source']['fileName']
                );
                $activity->setSource($source);
            }
            if (isset($act['analysis'])) {
                $analysisCollection = $this->generateAnalysis($act['analysis']);
                $activity->setAnalysis($analysisCollection);
            }

            foreach ($act['laps'] as $lp) {
                $lap = new Lap(
                    $lp['id'],
                    $lp['name'],
                    $lp['timestampFrom'],
                    $lp['timestampTo']
                );
                if (isset($lp['analysis'])) {
                    $analysisCollection = $this->generateAnalysis($lp['analysis']);
                    $lap->setAnalysis($analysisCollection);
                }
                $activity->addLap($lap);
            }

            foreach ($act['points'] as $p) {
                $point = new Point($p['timestamp']);
                foreach ($p as $key => $value) {
                    $point->setParameter($key, $value);
                }
                $activity->addPoint($point);
            }

            $activities->addActivity($activity);
        }

        return $activities;
    }

    public function readFromFile(string $fileName) : ActivityCollection
    {
        $activities = new ActivityCollection();
        $json = file_get_contents($fileName, true);
        $data = json_decode($json, true);
        return $this->createActivities($activities, $data);
    }

    public function saveToFile(ActivityCollection $activities, string $fileName, bool $pretty = false)
    {
        $json = json_encode($activities, ($pretty) ? JSON_PRETTY_PRINT : null);
        return file_put_contents($fileName, $json);
    }

    public function readFromBinary(string $data) : ActivityCollection
    {
        $activities = new ActivityCollection();
        $data = json_decode($data, true);
        return $this->createActivities($activities, $data);
    }

    public function saveToBinary(ActivityCollection $activities, bool $pretty = false) : string
    {
        $json = json_encode($activities, ($pretty) ? JSON_PRETTY_PRINT : null);
        return file_put_contents($fileName, $json);
    }
}
