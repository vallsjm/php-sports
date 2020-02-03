 PHP (>= v7.0) library for analysing GPX, TCX, FIT files created by devices like Garmin GPS, Polar, etc.

## What is a PHP-SPORTS import library?

This library is used for import / export GPX, TCX, and FIT files and convert that in a simple POO model with Activities, Laps and TrackPoints.


```php
<?php
use PhpSports\Activity\ImportFile;

$filePath = './samples/cycling_mountain_03.fit';
$activities = ImportFile::readFromFile($filePath);

/*each file return N activities (daily exercices)*/
foreach ($activities as $activity) {

    /*each activity return time intervals or laps */
    foreach ($activity->getLaps() as $lap) {

        /*each lap have trackpoints with latitude, longitude, heart rate, altitude, etc..*/
        foreach ($lap->getPoints() as $point) {
            ...
        }
    }
}

# Note that u can convert activities in a JSON format
$jsonPrintable = json_encode($activities, JSON_PRETTY_PRINT);
print_r($jsonPrintable);
?>
```
