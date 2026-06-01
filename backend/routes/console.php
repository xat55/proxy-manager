<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('proxies:check-status')->everyFiveMinutes();
