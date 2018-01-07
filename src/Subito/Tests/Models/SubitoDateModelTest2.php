<?php

namespace Subito\Tests\Models;

use \DateTime;
use PHPUnit_Framework_TestCase;
use Subito\Helpers\SubitoDateHelper;
use Subito\Models\SubitoDateModel;

class SubitoDateModelTest2 extends PHPUnit_Framework_TestCase {

    private $subitoDate;

    public function datesProvider(){

        $dates = [];

        for($i = 1;$i<= 1000; $i++){
            $startDate = date('Y/m/d',mt_rand());
            $endDate = date('Y/m/d',mt_rand());
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

        $this->assertSame($subitoDateDiff->months, $dateDiff->m);
    }

    /**
     * @dataProvider datesProvider
     */
    public function testDiffDays($startDate,$endDate) {
        $subitoDate = $this->getSubitoDateModel($startDate,$endDate);
        $subitoDateDiff = $subitoDate->diff();
        $dateDiff = $this->diff($startDate, $endDate);
        if($dateDiff->invert)
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
