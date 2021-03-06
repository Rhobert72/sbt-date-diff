<?php

namespace Subito\Models;

use Subito\Helpers\SubitoDateHelper;
use Subito\Interfaces\SubitoDateInterface;
use \Exception;

class SubitoDateModel implements SubitoDateInterface
{
    private $startDate;
    private $endDate;

    private $startDateDescr;
    private $endDateDescr;

    public $invert = false;

    public function __construct($startDate, $endDate)
    {
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
    }

    public function setStartDate(string $date)
    {
        if (!$this->isValidDate($date)) {
            throw new Exception('Start date is not a valid date');
        }

        $this->startDate = $date;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function setEndDate(string $date)
    {
        if (!$this->isValidDate($date)) {
            throw new Exception('End date is not a valid date');
        }

        $this->endDate = $date;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public static function isValidDate(string $date): bool
    {
        try {
            $dateDescr = new DateDescr($date);
            return true;
        } catch (\Exception $exc) {
            return false;
        }

    }

    public function diff(): \stdClass
    {

        $diff_arr = array(
            'years' => null,
            'months' => null,
            'days' => null,
            'total_days' => null,
            'invert' => null
        );

        $this->startDateDescr = new DateDescr($this->startDate);
        $this->endDateDescr = new DateDescr($this->endDate);

        $diff_arr['invert'] = $this->getInvert();

        if ($diff_arr['invert']) {
            $this->invert = $diff_arr['invert'];
            list($this->startDate, $this->endDate) = [$this->endDate, $this->startDate];
            $this->startDateDescr = new DateDescr($this->startDate);
            $this->endDateDescr = new DateDescr($this->endDate);
        }

        $diff_arr['years'] = $this->getDiffYears();
        $diff_arr['months'] = $this->getDiffMonths();
        $diff_arr['days'] = $this->getDiffDays();
        $diff_arr['total_days'] = $this->getTotalDays();

        return (object)$diff_arr;
    }

    private function getInvert(): int
    {
        return (int)!($this->startDate <= $this->endDate);
    }

    private function getDiffYears(): int
    {
        $years = $this->endDateDescr->yearIntValue - $this->startDateDescr->yearIntValue;
        if ($years > 0 && $this->endDateDescr->getMonthAndDayAsString() < $this->startDateDescr->getMonthAndDayAsString()) {
            $years--;
        }
        return $years;
    }

    private function getDiffMonths(): int
    {

        $months = 0;

        if ($this->endDateDescr->getMonthAndDayAsString() < $this->startDateDescr->getMonthAndDayAsString()) {
            $months = 12 - $this->startDateDescr->monthIntValue + $this->endDateDescr->monthIntValue;

        } else {
            $months = $this->endDateDescr->monthIntValue - $this->startDateDescr->monthIntValue;
        }

        if ($this->startDateDescr->dayIntValue > $this->endDateDescr->dayIntValue) {

            $months--;

            // Mi sposto al mese precedente
            list($prevMonth, $prevYear) = SubitoDateHelper::getPreviousMonth($this->endDateDescr->monthIntValue, $this->endDateDescr->yearIntValue);
            $endDatePrevMonthDays = SubitoDateHelper::getMonthDays($prevMonth, $prevYear);

            if ($this->startDateDescr->dayIntValue > $endDatePrevMonthDays) {
                $days = $this->getSpecialTotalDays();
                //echo "------->DDDDDDDAYS: $days\n";
                if($days < 31){
                   if(!$this->invert) $months--;
                }

            }

        }



        //$this->getSpecialTotalDays()

        return $months;

    }

    public function getDiffDays(): int
    {

        $days = 0;

        if ($this->invert) {

            // Ristabilisco l'ordine

            list($sDate, $eDate) = [$this->endDateDescr, $this->startDateDescr];

            if ($sDate->dayIntValue < $eDate->dayIntValue) {
                $days = SubitoDateHelper::getMonthDays($eDate->monthIntValue, $eDate->yearIntValue) - $eDate->dayIntValue;
                $days += $sDate->dayIntValue;
            } else {
                $days += $sDate->dayIntValue - $eDate->dayIntValue;
            }
        } else {

            // Considero i giorni trascorsi nel mese della endDate
            $days = $this->endDateDescr->dayIntValue;

            if ($this->startDateDescr->dayIntValue > $this->endDateDescr->dayIntValue) {

                // Mi sposto al mese precedente
                list($prevMonth, $prevYear) = SubitoDateHelper::getPreviousMonth($this->endDateDescr->monthIntValue, $this->endDateDescr->yearIntValue);
                $endDatePrevMonthDays = SubitoDateHelper::getMonthDays($prevMonth, $prevYear);

                if ($this->startDateDescr->dayIntValue <= $endDatePrevMonthDays) {
                    // Calcolo quanti gg mancano alla fine del mese partendo dal giorno specificato nella startDate
                    $days += $endDatePrevMonthDays - $this->startDateDescr->dayIntValue;
                }
                else {

                    // Mi sposto al mese precedente a quello precedentemente considerato
                    // ES. endDateDescr->monthIntValue = Marzo -> Febbraio -> Gennaio

                    $days = $this->getSpecialTotalDays();
                    //echo "DDDDDDDAYS: $days\n";
                    if($days > 31){
                        $days -= 31; // Verrà considerato un mese in più
                    }
                    else if($days === 31){
                        $days = 0; // Verrà considerato un mese in più
                    }

                }

            } else {
                $days -= $this->startDateDescr->dayIntValue;
            }

        }
        return $days;
    }

    private function getTotalDays(): int
    {

        $total_days = 0;

        if ($this->startDateDescr->yearIntValue !== $this->endDateDescr->yearIntValue) {

            // anni differenti
            $total_days += $this->startDateDescr->getDaysToTheEndOfTheYear();
            $total_days += $this->endDateDescr->getDaysFromTheBeginningOfTheYear();

            if ($this->startDateDescr->yearIntValue + 1 < $this->endDateDescr->yearIntValue) {
                $leapYears = SubitoDateHelper::getNumberOfLeapYears($this->startDateDescr->yearIntValue + 1, $this->endDateDescr->yearIntValue - 1);
                $normalYears = SubitoDateHelper::getNumberOfNormalYears($this->startDateDescr->yearIntValue + 1, $this->endDateDescr->yearIntValue - 1);
                $total_days += 366 * $leapYears;
                $total_days += 365 * $normalYears;
            }

        } else {
            // stesso anno
            $total_days += $this->endDateDescr->getDaysFromTheBeginningOfTheYear();
            $total_days -= $this->startDateDescr->getDaysFromTheBeginningOfTheYear();
        }

        return $total_days;

    }

    private function getSpecialTotalDays(): int {

        list($prevMonth, $prevYear) = SubitoDateHelper::getPreviousMonth($this->endDateDescr->monthIntValue, $this->endDateDescr->yearIntValue);
        $endDatePrevMonthDays = SubitoDateHelper::getMonthDays($prevMonth, $prevYear);

        list($prevPrevMonth, $prevPrevYear) = SubitoDateHelper::getPreviousMonth($prevMonth, $prevYear);
        $endDatePrevPrevMonthDays = SubitoDateHelper::getMonthDays($prevPrevMonth, $prevPrevYear);

        $prevPrevMonthDaysTotheEndOfTheMonth = $endDatePrevPrevMonthDays - $this->startDateDescr->dayIntValue;

        return $prevPrevMonthDaysTotheEndOfTheMonth + $this->endDateDescr->dayIntValue + $endDatePrevMonthDays;

    }

}
