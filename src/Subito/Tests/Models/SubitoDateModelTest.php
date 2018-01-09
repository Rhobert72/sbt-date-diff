<?php

namespace Subito\Tests\Models;

use \DateTime;
use PHPUnit_Framework_TestCase;
use Subito\Models\SubitoDateModel;

class SubitoDateModelTest extends PHPUnit_Framework_TestCase {

    private $subitoDate;
    private $startDate = '1986/08/29';
    private $endDate = '2025/03/01';

    protected function setUp() {
        $this->subitoDate = new SubitoDateModel($this->startDate, $this->endDate);
    }

    protected function tearDown() {
        unset($this->subitoDate);
    }

    public function testSetterGetterDates() {
        $this->assertSame($this->subitoDate->getStartDate(), $this->startDate);
        $this->assertSame($this->subitoDate->getEndDate(), $this->endDate);
    }

    public function testIsValidDate() {
        $this->assertSame($this->subitoDate->isValidDate('2015/12/25'), true);
        $this->assertSame($this->subitoDate->isValidDate('FAKE/12/25'), false);
    }

    public function testDiffYears() {
        $subitoDateDiff = $this->subitoDate->diff();
        $dateDiff = $this->diff($this->startDate, $this->endDate);

        $this->assertSame($subitoDateDiff->years, $dateDiff->y);
    }

    public function testDiffMonths() {
        $subitoDateDiff = $this->subitoDate->diff();
        $dateDiff = $this->diff($this->startDate, $this->endDate);
        $this->assertSame($subitoDateDiff->months, $dateDiff->m,"SUBITO: $subitoDateDiff->months PHP: $dateDiff->m");
    }

    public function testDiffDays() {
        $subitoDateDiff = $this->subitoDate->diff();
        $dateDiff = $this->diff($this->startDate, $this->endDate);
echo "DAYS SUBITO: $subitoDateDiff->days PHP: $dateDiff->d";
        $this->assertSame($subitoDateDiff->days, $dateDiff->d, "SUBITO: $subitoDateDiff->days PHP: $dateDiff->d");
    }

    public function testDiffTotalDays() {
        $subitoDateDiff = $this->subitoDate->diff();
        $dateDiff = $this->diff($this->startDate, $this->endDate);

        $this->assertSame($subitoDateDiff->total_days, $dateDiff->days);
    }

    public function testDiffInvert() {
        $subitoDateDiff = $this->subitoDate->diff();
        $dateDiff = $this->diff($this->startDate, $this->endDate);

        $this->assertSame($subitoDateDiff->invert, $dateDiff->invert);
    }

    private function diff($startDate, $endDate) {
        $startDate = DateTime::createFromFormat('Y/m/d', $startDate);
        $endDate = DateTime::createFromFormat('Y/m/d', $endDate);

        return $startDate->diff($endDate);
    }
}
