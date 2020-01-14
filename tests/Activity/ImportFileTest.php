<?php
namespace Tests\Activity;

use Tests\Activity\FileCase;
use PHPUnit\Framework\TestCase;
use PhpSports\Activity\ImportFile;

final class ImportFileTest extends FileCase
{

    public function testImportFiles()
    {
        foreach ($this->testFiles as $testFile) {
            // if ($testFile['fileName'] == 'cycling_indoor_01.fit') {
                $timeStart = microtime(true);
                $filePath = $this->base_dir . '/source/' . $testFile['fileName'];
                $activities = ImportFile::readFromFile($filePath);
                $duration = microtime(true) - $timeStart;

                $this->renderActivities(
                    $duration,
                    $filePath,
                    $activities
                );

                // $this->generateAnalysisAsserts(
                //     $filePath,
                //     $activities,
                //     $testFile
                // );
            // }
        }
    }
}
