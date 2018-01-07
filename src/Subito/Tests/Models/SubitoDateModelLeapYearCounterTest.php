<?php
/**
 * Created by PhpStorm.
 * User: robertogarella
 * Date: 03/01/18
 * Time: 13:08
 */

namespace Subito\Tests\Models;

use Subito\Models\SubitoDateModel;

class SubitoDateModelLeapYearCounterTest extends \PHPUnit_Framework_TestCase
{

    private $subitoDate;

    public function setUp(){
        $this->subitoDate = new SubitoDateModel('2015/04/04','2015/04/04');
    }

    public function yearsProvider(){

        $years=[];
        for($i=0;$i<1000;$i++){

            $startYear = mt_rand(1000,9999);
            $endYear = mt_rand(1000,9999);

            if($startYear > $endYear){
                list($startYear, $endYear)=[$endYear,$startYear];
            }

            $years[$startYear.' => '.$endYear]=[$startYear, $endYear];
        }

        return $years;

    }

    /**
     * @dataProvider yearsProvider
     */
    public function testLeapYearsCounter($startYear,$endYear)
    {

        $leapYears = 0;
        for($i=$startYear;$i<=$endYear;$i++){
            $leapYears += date('L', mktime(0, 0, 0, 1, 1, $i));
        }

        $this->assertEquals($leapYears, $this->subitoDate->leapYearsCounter($startYear, $endYear));

    }

}
