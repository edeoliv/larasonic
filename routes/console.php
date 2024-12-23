<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::daily()
    ->onOneServer()
    ->group(fn () => [
        Schedule::command('sitemap:generate'),
    ]);
