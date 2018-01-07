<?php

namespace Subito\Models;

use \Exception;
use Subito\Helpers\SubitoDateHelper;

class DateDescr
{
    const DELIMITER = '/';

    const DATE_FORMAT_REGEX = '/^\d{4}\/(0[1-9]|1[0-2])\/\d{2}$/';
    const YEAR_REGEX = '/^\d{4}$/';
    const MONTH_REGEX = '/^(0[1-9]|1[0-2])$/';
    const DAY_REGEX_BASE = '/^(0[1-9]|1[0-9]|__DAY_REGEX_AUS__)$/';

    const MONTH_DAYS_LIST = [31,28,31,30,31,30,31,31,30,31,30,31];

    private $date = '';

    public $year = '';
    public $month = '';
    public $day = '';

    public $yearIntValue = '';
    public $monthIntValue = '';
    public $dayIntValue = '';

    public function __construct($date){
        $this->setDate($date);
        $this->init();
    }

    private function setDate($date){
        if(!$this->isValid($date)){
            throw new Exception('invalid date provided');
        }
        $this->date = $date;
    }

    public function getDate(){
        //var_dump(implode(self::DELIMITER,[$this->year,$this->month,$this->day]));
        return implode(self::DELIMITER,[$this->year,$this->month,$this->day]);
    }

    public function isValid($date) {

        /* check date format*/
        if(!$this->isValidFormat($date)) return false;

        // check date value
        list($y,$m,$d) = explode(self::DELIMITER, $date);
        return $this->isValidYear($y) && $this->isValidMonth($m) && $this->isValidDay($d, $m,$y);

    }

    private function isValidFormat($date): bool {
        return preg_match(self::DATE_FORMAT_REGEX,$date);
    }

    private function isValidYear($year): bool {
        return preg_match(self::YEAR_REGEX,$year);
    }

    private function isValidMonth($month): bool {
        return preg_match(self::MONTH_REGEX,$month);
    }

    private function isValidDay($day,$month,$year): bool {

        $regex = self::DAY_REGEX_BASE;

        $monthDays = SubitoDateHelper::getMonthDays(intval($month),intval($year));

        switch($monthDays){
            case 28: $regex = str_replace('__DAY_REGEX_AUS__','2[0-8]', $regex); break;
            case 29: $regex = str_replace('__DAY_REGEX_AUS__','2[0-9]', $regex); break;
            case 30: $regex = str_replace('__DAY_REGEX_AUS__','2[0-9]' . '|' . '3[0]', $regex); break;
            case 31: $regex = str_replace('__DAY_REGEX_AUS__','2[0-9]' . '|' . '3[0-1]', $regex); break;
        }

        return preg_match($regex, $day);

    }

    private function init(): void{

        list($this->year,$this->month,$this->day) = explode(self::DELIMITER,$this->date);

        $this->yearIntValue = intval($this->year);
        $this->monthIntValue = intval($this->month);
        $this->dayIntValue = intval($this->day);

    }

    public function getDaysToEndOfMonth(): int {
        return SubitoDateHelper::getMonthDays($this->monthIntValue,$this->yearIntValue) - $this->dayIntValue;
    }

    public function getDaysToTheEndOfTheYear(): int {
        $total_days = $this->getDaysToEndOfMonth();
        // Calcolo i giorni che mancano alla fine dell'anno partendo dal mese successivo a quello della data
        if($this->monthIntValue < 12){
            $total_days += SubitoDateHelper::getDaysToTheEndOfTheYearByMonth($this->monthIntValue + 1, $this->yearIntValue);
        }
        return $total_days;
    }

    public function getDaysFromTheBeginningOfTheYear(): int {
        $total_days = $this->dayIntValue;
        if($this->monthIntValue > 1) {
            // Calcolo i giorni trascorsi dall'inizio dell'anno al mese precendete a quello della data
            $total_days += SubitoDateHelper::getDaysFromTheBeginningOfTheYearByMonth($this->monthIntValue - 1, $this->yearIntValue);
        }
        return $total_days;
    }

    public function isLastDayOfMonth(): bool {
        return SubitoDateHelper::getMonthDays($this->monthIntValue, $this->yearIntValue) === $this->dayIntValue;
    }

    public function getMonthAndDayAsString(): string {
        return $this->month.$this->day;
    }

}