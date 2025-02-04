<?php

namespace App\Repositories;

use App\Models\AppTopCategoryPosition;

class CategoryRepository
{
    public function getPositionsByDate(string $date): array
    {
        return AppTopCategoryPosition::where('date', $date)
            ->orderBy('category_id')
            ->get()
            ->groupBy('category_id')
            ->toArray();
    }
}
