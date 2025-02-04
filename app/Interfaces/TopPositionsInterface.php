<?php

namespace App\Interfaces;

use App\DTO\FetchTopPositionsParamsDTO;

interface TopPositionsInterface
{
    /**
     * Метод для получения позиций в топе
     *
     * @param FetchTopPositionsParamsDTO $paramsDTO
     * @return array|null
     */
    public function fetchTopPositions(FetchTopPositionsParamsDTO $paramsDTO): ?array;

    /**
     * Заполняет базу данных обработанными данными из необработанного ответа API.
     *
     * @param array|null $rawData
     * @return void
     */
    public function populateDatabase(?array $rawData): void;
}
