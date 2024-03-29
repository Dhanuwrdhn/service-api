<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AttendanceController;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('sanctum:purge')->daily();

        $schedule->call(function () {
            // Use Laravel's HTTP client to make a PUT request to your API route
            $response = app(AttendanceController::class)->autoCheckOut(request());

            $discordWebhookUrl = 'https://discord.com/api/webhooks/1210505645334335521/Ke4lTZFQypZrHLYYwC2Gbwm_Dv4hwC5UunltvrSzzlb8VsXKK3e8ofrWd8hLIMih2gTP';

            Http::post($discordWebhookUrl, [
                'content' => 'Auto Checkout function executed at ' . now() . ' and ' . $response['message'],
            ]);
            // Log the task execution (optional)
            \Log::info('Auto Checkout API call task executed at ' . now());
        })->DailyAt('18:00');

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
