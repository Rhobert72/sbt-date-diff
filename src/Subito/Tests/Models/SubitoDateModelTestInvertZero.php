<?php

namespace Subito\Tests\Models;

use \DateTime;
use PHPUnit_Framework_TestCase;
use Subito\Helpers\SubitoDateHelper;
use Subito\Models\SubitoDateModel;

class SubitoDateModelTestInvertZero extends PHPUnit_Framework_TestCase {

    private $subitoDate;

    public function datesProvider(){

        $dates = [];

        for($i = 1;$i<= 3000; $i++){

            $d1 = mt_rand();
            $d2 = mt_rand();

            if($d1 > $d2){
                list($d1,$d2) = [$d2,$d1];
            }

            $startDate = date('Y/m/d',$d1);
            $endDate = date('Y/m/d',$d2);
            $dates[$startDate.' -- '.$endDate] = [$startDate,$endDate];
        }

        return $dates;
    }


    private function getSubitoDateModel($startDate,$endDate){
        return new SubitoDateModel($startDate,$endDate);
    }

    protected function tearDown() {
        unset($this->subitoDate);
    }


    /**
     * @dataProvider datesProvider
     */
    public function testSetterGetterDates($startDate,$endDate) {

        $subitoDate = $this->getSubitoDateModel($startDate,$endDate);
        $this->assertSame($subitoDate->getStartDate(), $startDate);
        $this->assertSame($subitoDate->getEndDate(), $endDate);
    }

    /**
     * @dataProvider datesProvider
     */
    public function testIsValidDate($startDate,$endDate) {
        $this->assertSame(SubitoDateModel::isValidDate($startDate), DateTime::createFromFormat('Y/m/d', $startDate) === false? false: true);
        $this->assertSame(SubitoDateModel::isValidDate($endDate), DateTime::createFromFormat('Y/m/d', $endDate) === false? false: true);
    }

    /**
     * @dataProvider datesProvider
     */
    public function testDiffYears($startDate,$endDate) {
        $subitoDate = $this->getSubitoDateModel($startDate,$endDate);
        $subitoDateDiff = $subitoDate->diff();
        $dateDiff = $this->diff($startDate, $endDate);

        $this->assertSame($subitoDateDiff->years, $dateDiff->y);
    }

    /**
     * @dataProvider datesProvider
     */
    public function testDiffMonths($startDate,$endDate) {
        $subitoDate = $this->getSubitoDateModel($startDate,$endDate);
        $subitoDateDiff = $subitoDate->diff();
        $dateDiff = $this->diff($startDate, $endDate);

        $this->assertSame($subitoDateDiff->months, $dateDiff->m,"SUBITO: $subitoDateDiff->months PHP: $dateDiff->m");
    }

    /**
     * @dataProvider datesProvider
     */
    public function testDiffDays($startDate,$endDate) {
        $subitoDate = $this->getSubitoDateModel($startDate,$endDate);
        $subitoDateDiff = $subitoDate->diff();
        $dateDiff = $this->diff($startDate, $endDate);

        /*if($subitoDateDiff->days !== $dateDiff->d){

            echo "array('".$startDate."','".$endDate."')\n";
        }*/

        $this->assertSame($subitoDateDiff->days, $dateDiff->d, "Subito: $subitoDateDiff->days PHP: ".$dateDiff->d);
    }

    /**
     * @dataProvider datesProvider
     */
    public function testDiffTotalDays($startDate,$endDate) {
        $subitoDate = $this->getSubitoDateModel($startDate,$endDate);
        $subitoDateDiff = $subitoDate->diff();

        $dateDiff = $this->diff($startDate, $endDate);

        $this->assertSame($subitoDateDiff->total_days, $dateDiff->days);
    }

    /**
     * @dataProvider datesProvider
     */
    public function testDiffInvert($startDate,$endDate) {
        $subitoDate = $this->getSubitoDateModel($startDate,$endDate);
        $subitoDateDiff = $subitoDate->diff();
        $dateDiff = $this->diff($startDate, $endDate);

        $this->assertSame($subitoDateDiff->invert, $dateDiff->invert);
    }

    private function diff($startDate, $endDate) {
        $startDate = DateTime::createFromFormat('Y/m/d', $startDate);
        $endDate = DateTime::createFromFormat('Y/m/d', $endDate);

        return $startDate->diff($endDate);
    }
}
