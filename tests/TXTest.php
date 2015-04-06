<?php

chdir(realpath(dirname(__FILE__) . '/../web'));
require_once 'config.php';

use \AmiLabs\Chainy\TX;

class TXTest extends PHPUnit_Framework_TestCase{
    /**
     * @covers \AmiLabs\Chainy\TX::testGetBlockDate
     */
    public function testGetBlockDate(){
        // Ordinary block
        $blockDate = TX::getBlockDate(350000);
        $this->assertEquals('2015-03-31 05:17:14', $blockDate);
        // Zero block
        $blockDate = TX::getBlockDate(0);
        $this->assertEquals(FALSE, $blockDate);
        // Unexisting block
        $blockDate = TX::getBlockDate(100000000);
        $this->assertEquals('1970-01-01 07:00:00', $blockDate);
    }
}