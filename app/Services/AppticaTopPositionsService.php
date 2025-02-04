<?php

namespace App\Services;

use App\DTO\FetchTopPositionsParamsDTO;
use App\Interfaces\TopPositionsInterface;
use App\Models\AppTopCategoryPosition;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class AppticaTopPositionsService implements TopPositionsInterface
{
    private Client $httpClient;
    private LoggerInterface $logger;
    private string $apiKey;

    public function __construct(Client $httpClient, LoggerInterface $logger, string $apiKey)
    {
        $this->httpClient = $httpClient;
        $this->logger = $logger;
        $this->apiKey = $apiKey;
    }

    /**
     * Метод для получения позиций в топе через Apptica API
     *
     * @param FetchTopPositionsParamsDTO $paramsDTO
     * @return array|null
     */
    public function fetchTopPositions(FetchTopPositionsParamsDTO $paramsDTO): ?array
    {
        $apiURL = $this->generateTopPositionsURL($paramsDTO);

        try {
            $response = $this->httpClient->request('GET', $apiURL);

            $statusCode = $response->getStatusCode();

            if ($statusCode !== 200) {
                $this->logger->channel('apptica_top_positions')->error('Apptica API request failed', [
                    'status_code' => $statusCode,
                    'response_body' => $response->getBody()->getContents(),
                    'api_url' => $apiURL,
                ]);
                return null;
            }

            $data = json_decode($response->getBody()->getContents(), true);

            if ($data === null) {
                $this->logger->channel('apptica_top_positions')->error('Failed to decode API response', [
                    'status_code' => $statusCode,
                    'api_url' => $apiURL,
                ]);
                return null;
            }

            return $data;
        } catch (GuzzleException $e) {
            $this->logger->channel('apptica_top_positions')->error('API request failed', [
                'exception_message' => $e->getMessage(),
                'api_url' => $apiURL,
            ]);
            return null;
        }
    }

    private function generateTopPositionsURL(FetchTopPositionsParamsDTO $paramsDTO): string
    {
        $baseURL = 'https://api.apptica.com/package/top_history';

        return $baseURL . '/' . $paramsDTO->additionalParams['applicationId'] . '/' .
            $paramsDTO->additionalParams['countryId'] . '?date_from=' . $paramsDTO->dateFrom .
            '&date_to=' . $paramsDTO->dateTo . '&B4NKGg=' . $this->apiKey;
    }

    /**
     * Обработка ответа от Apptica API
     *
     * @param array $apiData
     * @return array
     */
    private function processRawApiResponse(array $apiData): array
    {
        $processedDataForDb = [];

        foreach ($apiData as $categoryId => $categoryData) {
            $datesInCategory = [];
            foreach ($categoryData as $subCategoryData) {
                $datesInCategory = array_merge($datesInCategory, array_keys($subCategoryData));
            }

            $uniqueDatesInCategory = array_unique($datesInCategory);

            foreach ($uniqueDatesInCategory as $dateString) {
                $minPosition = null;

                foreach ($categoryData as $subCategoryData) {
                    if (isset($subCategoryData[$dateString])) {
                        $position = $subCategoryData[$dateString];
                        if (is_numeric($position)) {
                            if ($minPosition === null || $position < $minPosition) {
                                $minPosition = $position;
                            }
                        }
                    }
                }

                if ($minPosition !== null) {
                    $processedDataForDb[] = [
                        'date' => $dateString,
                        'category_id' => $categoryId,
                        'position' => $minPosition,
                    ];
                }
            }
        }

        return $processedDataForDb;
    }

    /**
     * Заполняет базу данных обработанными данными из необработанного ответа API.
     *
     * @param array|null $rawData
     * @return void
     */
    public function populateDatabase(?array $rawData): void
    {
        if (empty($rawData) || !isset($rawData['data']) || !is_array($rawData['data'])) {
            $this->logger->channel('apptica_top_positions')
                ->warning('Invalid or empty raw data received for database population.');
            return;
        }

        $dataToSave = $this->processRawApiResponse($rawData['data']);

        if (empty($dataToSave)) {
            $this->logger->channel('apptica_top_positions')->info('No data to save to database after processing.');
            return;
        }

        foreach ($dataToSave as $item) {
            AppTopCategoryPosition::updateOrCreate(
                [
                    'date' => $item['date'],
                    'category_id' => $item['category_id'],
                ],
                [
                    'position' => $item['position'],
                ]
            );
        }

        $this->logger->channel('apptica_top_positions')
            ->info('Successfully populated database with top positions data.', ['record_count' => count($dataToSave)]);
    }
}
