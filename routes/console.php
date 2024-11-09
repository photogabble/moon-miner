<?php

use App\Jobs\TurnsScheduler;
use Illuminate\Support\Facades\Schedule;

Schedule::job(TurnsScheduler::class)
    ->everyTwoMinutes()
    ->withoutOverlapping();
