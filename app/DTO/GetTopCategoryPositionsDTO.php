<?php

namespace App\DTO;

use Illuminate\Http\Request;

class GetTopCategoryPositionsDTO
{
    public function __construct(
        public readonly string $date
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            date: $request->input('date')
        );
    }
}
