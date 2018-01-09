<?php

namespace Subito\Helpers;

class SubitoDateHelper
{
    public const MONTH_DAYS_LIST = [31,28,31,30,31,30,31,31,30,31,30,31];

    public static function isLeapYear(int $year): bool {
        return is_numeric($year) && (($year % 4 === 0) && ($year % 100 !== 0)) || ($year % 400 === 0);
    }

    public static function getMonthDays(int $month, int $year): int {
        return $month === 2 && self::isLeapYear($year)? 29:self::MONTH_DAYS_LIST[$month - 1];
    }

    public static function getPreviousMonth(int $month, int $year) {
        return [$month > 1? --$month: 12, $month > 1? $year: --$year];
    }

    public static function getNextMonth(int $month, int $year) {
        return [$month === 12? 1: ++$month, $month === 12? ++$year:$year];
    }

    public static function getNumberOfLeapYears(int $startYear, int $endYear): int{

        if($startYear > $endYear){
            list($startYear,$endYear) = [$endYear,$startYear];
        }

        $startYearLeapYearsCounter = intval($startYear / 4) - intval($startYear / 100) + intval($startYear / 400);
        $endYearLeapYearsCounter = intval($endYear / 4) - intval($endYear / 100) + intval($endYear / 400);

        $counter = $endYearLeapYearsCounter - $startYearLeapYearsCounter;

        return SubitoDateHelper::isLeapYear($startYear)? ++$counter:$counter;
    }

    public static function getNumberOfNormalYears(int $startYear, int $endYear): int{

        if ($startYear > $endYear) {
            list($startYear,$endYear) = [$endYear,$startYear];
        }

        return $endYear - $startYear + 1 - self::getNumberOfLeapYears($startYear, $endYear);

    }

    public static function getDaysToTheEndOfTheYearByMonth(int $month, int $year): int {
        $days = array_sum(array_slice(self::MONTH_DAYS_LIST,$month - 1));
        // Nel caso sia gennaio e l'anno sia bisestile, devo aggiungere il 29/02
        if(self::isLeapYear($year) && $month === 2 ) {
            $days++;
        }
        return $days;
    }

    public static function getDaysFromTheBeginningOfTheYearByMonth(int $month, int $year): int {
        $days = array_sum(array_slice(self::MONTH_DAYS_LIST,0, $month));
        // Nel caso venisse incluso febbraio e l'anno sia bisestile, aggiungo il 29/02
        if($month > 1 && self::isLeapYear($year)){
            $days++;
        }
        return $days;
    }
}