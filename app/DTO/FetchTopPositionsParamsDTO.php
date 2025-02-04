<?php

namespace App\DTO;

class FetchTopPositionsParamsDTO
{
    public ?string $dateFrom;
    public ?string $dateTo;
    public ?array $additionalParams;

    public function __construct(?string $dateFrom = null, ?string $dateTo = null, ?array $additionalParams = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->additionalParams = $additionalParams;
    }
}
