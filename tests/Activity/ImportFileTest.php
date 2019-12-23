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
            $filePath = $this->base_dir . '/source/' . $testFile['fileName'];
            $activities = ImportFile::readFromFile($filePath);

            // $this->renderActivities(
            //     $filePath,
            //     $activities
            // );

            $this->generateAnalysisAsserts(
                $filePath,
                $activities,
                $testFile
            );
        }
    }
}
