<?php

namespace ImguBox\Console\Commands;

use Illuminate\Console\Command;
use ImguBox\User;
use ImguBox\Jobs\FetchImages;
use Illuminate\Foundation\Bus\DispatchesJobs;

class FetchUserFavs extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imgubox:fetchFavs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches User Favs from Imgur (Get the favorited images).';

    /**
     * User Instance
     * @var ImguBox\User
     */
    protected $user;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        parent::__construct();
        $this->user = $user;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Start fetching');

        $this->user->hasDropboxToken()->hasImgurToken()->chunk(10, function ($users) {

            foreach ($users as $user) {
                $this->dispatch(new FetchImages($user));
            }

        });

        $this->info('Done');
    }
}
