<?php

chdir(realpath(dirname(__FILE__) . '/../web'));
require_once 'config.php';

use \AmiLabs\Chainy\TX;

class Chainy_TXTest extends PHPUnit_Framework_TestCase{
    /**
     * @covers \AmiLabs\Chainy\TX::testGetBlockDate
     */
    public function testGetBlockDate(){
        // Ordinary block timestamp
        $blockDate = TX::getBlockDate(350000, FALSE);
        $this->assertEquals(1427753834, $blockDate);
        // Ordinary block date
        $blockDate = TX::getBlockDate(350000);
        $this->assertEquals(date('Y-m-d H:i:s', 1427753834), $blockDate);
        // Ordinary block date in custom format
        $format = 'd-i:s H:Y-m';
        $blockDate = TX::getBlockDate(350000, $format);
        $this->assertEquals(date($format, 1427753834), $blockDate);
        // Zero block
        $blockDate = TX::getBlockDate(0);
        $this->assertEquals(FALSE, $blockDate);
        // Unexisting block
        $blockDate = TX::getBlockDate(100000000);
        $this->assertEquals(FALSE, $blockDate);
        // Not a block number
        $blockDate = TX::getBlockDate('test');
        $this->assertEquals(FALSE, $blockDate);
    }
}