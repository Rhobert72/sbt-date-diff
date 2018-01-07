<?php
/**
 * Created by PhpStorm.
 * User: robertogarella
 * Date: 03/01/18
 * Time: 12:56
 */

namespace Subito\Tests\Helpers;

use Subito\Helpers\SubitoDateHelper;

class SubitoDateHelperTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider yearProvider
     */
    public function testIsLeapYear(int $year)
    {
        $this->assertEquals(
            (bool)date('L',mktime(0,0,0,1,1,$year)),
            SubitoDateHelper::isLeapYear($year)
        );
    }

    public function yearProvider(){
        $years=[];
        for($i=0;$i<1000;$i++){
            $years[]=[mt_rand(0,9999)];
        }

        return $years;
    }

    /**
     * @dataProvider monthsProvider
     */
    public function testGetMonthDays($month,$year){

        $this->assertEquals(SubitoDateHelper::getMonthDays($month,$year),cal_days_in_month(CAL_GREGORIAN, $month, $year));

    }

    public function monthsProvider() {
        $months = [];
        for($i=0;$i<1000;$i++){
            $months[]=[mt_rand(1,12),mt_rand(1900,2300)];
        }

        return $months;

    }
}
