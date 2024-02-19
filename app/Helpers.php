<?php

use Carbon\Carbon;

function getRandomTimestamps($backwardDays = -800)
{

    $backwardCreatedDays = rand($backwardDays, 0);
    $backwardUpdatedDays = rand($backwardCreatedDays + 1, 0);

    return [
        'created_at' => Carbon::now()->addDays($backwardCreatedDays)->addMinutes(rand(
            0,
            60 * 23
        ))->addSeconds(rand(0, 60)),
        'updated_at' => Carbon::now()->addDays($backwardUpdatedDays)->addMinutes(rand(
            0,
            60 * 23
        ))->addSeconds(rand(0, 60))
    ];
}
