<?php

use App\Jobs\SendLiveClassReminder;
use App\Jobs\SendTrialExpiryReminder;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| VidyaMission Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Send trial expiry reminder emails daily at 9 AM
Schedule::job(new SendTrialExpiryReminder)->dailyAt('09:00')->name('trial-expiry-reminder')->withoutOverlapping();

// Check for live classes starting in 30 minutes — runs every minute
Schedule::job(new SendLiveClassReminder)->everyMinute()->name('live-class-reminder')->withoutOverlapping();

// Reconcile pending payments and refunds against Razorpay every 15 minutes
Schedule::command('payment:reconcile')->everyFifteenMinutes()->name('payment-reconcile')->withoutOverlapping();

// Manage live class lifecycle and dispatch student notifications every 5 minutes
Schedule::command('live-classes:lifecycle')->everyFiveMinutes()->name('live-classes-lifecycle')->withoutOverlapping();
