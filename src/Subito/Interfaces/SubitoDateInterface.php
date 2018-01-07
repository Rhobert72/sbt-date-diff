<?php

namespace Subito\Interfaces;

interface SubitoDateInterface
{
    public function setStartDate(string $date);

    public function setEndDate(string $date);

    public static function isValidDate(string $date): bool;

    public function diff(): \stdClass;
}
