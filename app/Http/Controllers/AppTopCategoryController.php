<?php

namespace App\Http\Controllers;

use App\DTO\GetTopCategoryPositionsDTO;
use App\Http\Requests\GetTopCategoryPositionsRequest;
use App\Services\TopCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppTopCategoryController extends Controller
{
    public function __construct(
        private readonly TopCategoryService $topCategoryService
    ) {}

    public function getTopCategoryPositions(GetTopCategoryPositionsRequest $request): JsonResponse
    {
        $dto = GetTopCategoryPositionsDTO::fromRequest($request);

        $result = $this->topCategoryService->getPositions($dto);

        return response()->json($result);
    }
}
