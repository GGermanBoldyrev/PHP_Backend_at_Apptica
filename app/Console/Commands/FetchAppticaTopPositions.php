<?php

namespace App\Console\Commands;

use App\DTO\FetchTopPositionsParamsDTO;
use App\Interfaces\TopPositionsInterface;
use Illuminate\Console\Command;

class FetchAppticaTopPositions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-apptica-top-positions {dateFrom} {dateTo} {applicationId?} {countryId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store top category positions from Apptica API';

    private TopPositionsInterface $appticaService;

    public function __construct(TopPositionsInterface $appticaService)
    {
        parent::__construct();
        $this->appticaService = $appticaService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dateFrom = $this->argument('dateFrom') ?: now()->toDateString();
        $dateTo = $this->argument('dateTo') ?: now()->toDateString();

        // тут по умолчанию для тестового задания
        $applicationId = $this->argument('applicationId') ?: 1421444;
        $countryId = $this->argument('countryId') ?: 1;

        $this->info('Fetching data from Apptica API: date: form ' . $dateFrom . ' to ' . $dateTo . ', countryId: ' .
            $countryId . ', applicationId: ' . $applicationId);

        $DTO = new FetchTopPositionsParamsDTO($dateFrom, $dateTo, [
            'applicationId' => $applicationId,
            'countryId' => $countryId,
        ]);

        $topPositionsRawData = $this->appticaService->fetchTopPositions($DTO);

        if ($topPositionsRawData === null) {
            $this->error('Failed to fetch data from Apptica API. See logs for details.');
            return 1;
        }

        $this->info('Successfully fetched data from Apptica API. Populating database...');


        $this->appticaService->populateDatabase($topPositionsRawData);

        return 0;
    }
}
