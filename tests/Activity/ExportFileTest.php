<?php
namespace Tests\Activity;

use Tests\Activity\FileCase;
use PHPUnit\Framework\TestCase;
use PhpSports\Activity\ExportFile;
use PhpSports\Activity\ImportFile;
use PhpSports\Model\ActivityCollection;

final class ExportFileTest extends FileCase
{
    public function testExportFiles()
    {
        foreach ($this->testFiles as $testFile) {
            $filePath = $this->base_dir . '/source/' . $testFile['fileName'];
            $activities = ImportFile::readFromFile($filePath);

            $this->generateAnalysisAsserts(
                $filePath,
                $activities,
                $testFile
            );

            foreach ($testFile['generate'] as $ouputFile) {
                $timeStart = microtime(true);
                $filePath = $this->base_dir . '/destination/' . $ouputFile;
                ExportFile::saveToFile($activities, $filePath, true);
                $duration = microtime(true) - $timeStart;

                $this->renderActivities(
                    $duration,
                    $filePath,
                    $activities
                );
            }
        }
    }

    public function testDataExportFiles()
    {
        foreach ($this->testFiles as $testFile) {
            foreach ($testFile['generate'] as $ouputFile) {
                $timeStart = microtime(true);
                $filePath = $this->base_dir . '/destination/' . $ouputFile;
                $activitiesGenerated = ImportFile::readFromFile($filePath);
                $duration = microtime(true) - $timeStart;

                $this->renderActivities(
                    $duration,
                    $filePath,
                    $activitiesGenerated
                );

                $this->generateAnalysisAsserts(
                    $filePath,
                    $activitiesGenerated,
                    $testFile
                );
            }
        }
    }

}
