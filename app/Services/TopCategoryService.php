<?php

namespace App\Services;

use App\DTO\GetTopCategoryPositionsDTO;
use App\Models\AppTopCategoryPosition;
use App\Repositories\CategoryRepository;

class TopCategoryService
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository
    ) {}

    public function getPositions(GetTopCategoryPositionsDTO $dto): array
    {
        $positions = $this->categoryRepository->getPositionsByDate($dto->date);

        return $this->formatResponse($positions, $dto->date);
    }

    private function formatResponse(array $categoryPositionsArray, string $date): array
    {
        $formattedPositions = array_reduce($categoryPositionsArray, function ($result, $categoryPositions) {
            $firstPosition = is_array($categoryPositions) && !empty($categoryPositions) ? $categoryPositions[0] : null;
            if ($firstPosition) {
                $categoryId = $firstPosition['category_id'] ?? 'unknown';
                $position = $firstPosition['position'] ?? null;

                $result[$categoryId] = $position;
            }
            return $result;
        }, []);

        return [
            'date' => $date,
            'data' => $formattedPositions
        ];
    }
}
