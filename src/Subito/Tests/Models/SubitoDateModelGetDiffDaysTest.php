<?php
/**
 * Created by PhpStorm.
 * User: robertogarella
 * Date: 04/01/18
 * Time: 05:26
 */

namespace Subito\Tests\Models;

use Subito\Models\SubitoDateModel;

class SubitoDateModelGetDiffDaysTest extends \PHPUnit_Framework_TestCase
{

    private $subitoDate;

    public function datesProvider(){
        $dates = [];
        for($i=0;$i<100;$i++){


            $startDateArr = [
                mt_rand(1900,2300),
                str_pad(mt_rand(1,12),2,'0',STR_PAD_LEFT),
                str_pad(mt_rand(1,31),2,'0',STR_PAD_LEFT),
                ];
            $endDateArr = [
                mt_rand(1900,9999),
                str_pad(mt_rand(1,12),2,'0',STR_PAD_LEFT),
                str_pad(mt_rand(1,31),2,'0',STR_PAD_LEFT),
                ];

            $startDate = implode('/',$startDateArr);
            $endDate = implode('/',$endDateArr);

            $dates[$startDate.' => '.$endDate]=[$startDate, $endDate];
        }
//var_dump($dates);
        return dates;
    }

    /**
     * @dataProvider datesProvider
     */
    public function testGetDiffDays($startDate,$endDate)
    {

        $this->subitoDate = new SubitoDateModel($startDate,$endDate);
        $subitoDaysDiff = $this->subitoDate->getDiffDays();
        $daysDiff = $this->diff($startDate,$endDate)->d;

        $this->assertEquals($subitoDaysDiff,$daysDiff);

    }

    private function diff($startDate, $endDate) {
        $startDate = DateTime::createFromFormat('Y/m/d', $startDate);
        $endDate = DateTime::createFromFormat('Y/m/d', $endDate);

        return $startDate->diff($endDate);
    }
}
