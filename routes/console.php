<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('orders:cancel-expired')->everyMinute();
