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

    public function __construct($startDate, $endDate) {
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
    }

    public function setStartDate(string $date) {
        if (!$this->isValidDate($date)) {
            throw new Exception('Start date is not a valid date');
        }

        $this->startDate = $date;
    }

    public function getStartDate(): string {
        return $this->startDate;
    }

    public function setEndDate(string $date) {
        if (!$this->isValidDate($date)) {
            throw new Exception('End date is not a valid date');
        }

        $this->endDate = $date;
    }

    public function getEndDate(): string {
        return $this->endDate;
    }

    public static function isValidDate(string $date): bool {
        try{
            $dateDescr = new DateDescr($date);
            return true;
        }
        catch(\Exception $exc){
            return false;
        }

    }

    public function diff(): \stdClass {

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

        if($diff_arr['invert']){
            $this->invert = $diff_arr['invert'];
            list($this->startDate,$this->endDate) = [$this->endDate,$this->startDate];
            $this->startDateDescr = new DateDescr($this->startDate);
            $this->endDateDescr = new DateDescr($this->endDate);
        }

        $diff_arr['years'] = $this->getDiffYears();
        $diff_arr['months'] = $this->getDiffMonths();
        $diff_arr['days'] = $this->getDiffDays();
        $diff_arr['total_days'] = $this->getTotalDays();

        return (object)$diff_arr;
    }

    private function getInvert(): int {
        return (int)!($this->startDate <= $this->endDate);
    }

    private function getDiffYears(): int{
        $years = $this->endDateDescr->yearIntValue - $this->startDateDescr->yearIntValue;
        if($years > 0 && $this->endDateDescr->getMonthAndDayAsString() < $this->startDateDescr->getMonthAndDayAsString()) {
            $years--;
        }
        return $years;
    }

    private function getDiffMonths(): int {

        $months = 0;

        if($this->endDateDescr->getMonthAndDayAsString() < $this->startDateDescr->getMonthAndDayAsString()){
            $months = 12 - $this->startDateDescr->monthIntValue + $this->endDateDescr->monthIntValue;
        }
        else{
            $months = $this->endDateDescr->monthIntValue - $this->startDateDescr->monthIntValue;
        }

        if($this->startDateDescr->dayIntValue > $this->endDateDescr->dayIntValue){
            $months--;
        }

        /*if($this->endDateDescr->monthIntValue === 3 && $this->startDateDescr->dayIntValue > $this->endDateDescr->dayIntValue){
            echo ">>>>>>>>>>>>>>>>>>>>>>>";
            $months--;
        }*/

        return $months;

    }

    public function getDiffDays(): int{

        // Considero i giorni trascorsi nel mese della endDate
        $days = $this->endDateDescr->dayIntValue;

        if($this->startDateDescr->dayIntValue > $this->endDateDescr->dayIntValue) {
            // Mi sposto al mese precedente
            list($prevMonth,$prevYear) = SubitoDateHelper::getPreviousMonth($this->endDateDescr->monthIntValue, $this->endDateDescr->yearIntValue);
            $endDatePrevMonthDays = SubitoDateHelper::getMonthDays($prevMonth, $prevYear);
            // Se è un giorno che NON appartiene a quel mese non faccio nulla
            if($this->startDateDescr->dayIntValue <= $endDatePrevMonthDays){
                // Calcolo quanti gg mancano alla fine del mese partendo dal giorno specificato nella startDate
                $days += $endDatePrevMonthDays - $this->startDateDescr->dayIntValue;
            }
        }
        else {
            $days -= $this->startDateDescr->dayIntValue;
        }

        $startDate = \DateTime::createFromFormat('Y/m/d', $this->startDate);
        $endDate = \DateTime::createFromFormat('Y/m/d', $this->endDate);

        $diff = $startDate->diff($endDate);

        /*echo "-----------------------------\n";
        echo "SUBITO: ".$days." -- PHP: ".$diff->d."\n";
        echo "INVERT: ".($this->invert?'YES':'NO')."\n";

        echo "--- START DATE ---"."\n";
        echo "START DATE: ".$this->startDate."\n";


        echo "--- END DATE ---"."\n";
        echo "DATE: ".$this->endDate."\n";
        echo "LEAP YEAR: ".(SubitoDateHelper::isLeapYear($this->endDateDescr->yearIntValue)?'LEAP':'NOT LEAP')."\n";

        echo "-----------------------------\n";*/

        return $days;
    }

    private function getTotalDays(): int{

        $total_days = 0;

        if($this->startDateDescr->yearIntValue !== $this->endDateDescr->yearIntValue){

            // anni differenti
            $total_days += $this->startDateDescr->getDaysToTheEndOfTheYear();
            $total_days += $this->endDateDescr->getDaysFromTheBeginningOfTheYear();

            if($this->startDateDescr->yearIntValue + 1 < $this->endDateDescr->yearIntValue){
                $leapYears = SubitoDateHelper::getNumberOfLeapYears($this->startDateDescr->yearIntValue + 1, $this->endDateDescr->yearIntValue - 1);
                $normalYears = SubitoDateHelper::getNumberOfNormalYears($this->startDateDescr->yearIntValue + 1, $this->endDateDescr->yearIntValue - 1);
                $total_days += 366 * $leapYears;
                $total_days += 365 * $normalYears;
            }

        }
        else{
            // stesso anno
            $total_days += $this->endDateDescr->getDaysFromTheBeginningOfTheYear();
            $total_days -= $this->startDateDescr->getDaysFromTheBeginningOfTheYear();
        }

        return $total_days;

    }

}
