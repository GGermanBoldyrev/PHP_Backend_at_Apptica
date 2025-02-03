<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FetchAppticaTopPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-apptica-top-positions {dateFrom} {dateTo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store top category positions from Apptica API';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Fetching data from Apptica API...');
        //
    }
}
