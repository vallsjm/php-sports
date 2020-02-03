<?php

namespace PhpSports\Model;

class Type
{
    const PARAMETERS = [
        'timestamp',
        'latitude',
        'longitude',
        'elevationMeters',
        'altitudeMeters',
        'distanceMeters',
        'durationSeconds',
        'elevationGainMeters',
        'speedMetersPerSecond',
        'cadenceRPM',
        'powerWatts',
        'hrBPM',
        'caloriesKcal',
        'tss'
    ];

    const SPORTS = [
        'RUNNING',
        'RUNNING_INDOOR',
        'RUNNING_STREET',
        'RUNNING_MOUNTAIN',
        'CYCLING',
        'CYCLING_INDOOR',
        'CYCLING_STREET',
        'CYCLING_MOUNTAIN',
        'SWIMMING',
        'FITNESS',
        'OTHER'
    ];
}
