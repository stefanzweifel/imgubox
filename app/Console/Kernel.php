<?php

namespace ImguBox\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \ImguBox\Console\Commands\FetchUserFavs::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $url = config('services.envoyer.pings.fetch_favs');

        $schedule->command('imgubox:fetchFavs')->everyThirtyMinutes()->withoutOverlapping()->thenPing($url);
    }
}
