<?php

namespace App\Services;

use App\DTO\GetTopCategoryPositionsDTO;
use App\Models\AppTopCategoryPosition;
use App\Repositories\CategoryRepository;
use Psr\Log\LoggerInterface;

class TopCategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly LoggerInterface $logger
    ) {}

    public function getPositions(GetTopCategoryPositionsDTO $dto): array
    {
        $this->logger->channel('top_category')->info('Запрос позиций категории', ['date' => $dto->date]);

        $positions = $this->categoryRepository->getPositionsByDate($dto->date);

        $formattedResponse = $this->formatResponse($positions, $dto->date);

        $this->logger->channel('top_category')->info('Результат форматирования позиций', [
            'date' => $dto->date,
            'positions' => $formattedResponse['data']
        ]);

        return $formattedResponse;
    }

    private function formatResponse(array $categoryPositionsArray, string $date): array
    {
        $formattedPositions = array_reduce($categoryPositionsArray, function ($result, $categoryPositions) {
            $firstPosition = is_array($categoryPositions) && !empty($categoryPositions) ? $categoryPositions[0] : null;
            if ($firstPosition) {
                $categoryId = $firstPosition['category_id'] ?? 'unknown';
                $position = $firstPosition['position'] ?? null;

                $result[$categoryId] = $position;

                $this->logger->channel('top_category')->debug('Обработана категория', [
                    'category_id' => $categoryId,
                    'position'    => $position
                ]);
            }
            return $result;
        }, []);

        return [
            'date' => $date,
            'data' => $formattedPositions
        ];
    }
}
