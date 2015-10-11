<?php namespace ImguBox\Console\Commands;

use Illuminate\Console\Command;
use ImguBox\Jobs\StoreImages;
use ImguBox\User;
use ImguBox\Jobs\FetchImages;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Foundation\Bus\DispatchesJobs;

class FetchUserFavs extends Command
{
    use DispatchesJobs;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'imgubox:fetchFavs';

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
    public function fire()
    {
        $this->info('Start fetching');

        $this->user->hasDropboxToken()->hasImgurToken()->chunk(10, function ($users) {

            foreach ($users as $user) {
                $this->dispatch(new FetchImages($user));
            }

        });

        $this->info('Done');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            // ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            // ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
