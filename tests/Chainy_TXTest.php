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
    /**
     * @covers \AmiLabs\Chainy\TX::getPositionInBlockByTransaction
     */
    public function testGetPositionInBlockByTransaction(){
        // Valid transaction
        $aPosition = TX::getPositionInBlockByTransaction('a716ab62a35baa0aa75bb675f8e479b212fc45b1b5320faadd6b0b0ed74e426e');
        $this->assertEquals(TRUE, is_array($aPosition));
        $this->assertEquals(349633, $aPosition['block']);
        $this->assertEquals(634, $aPosition['position']);
        // Valid transaction
        $aPosition = TX::getPositionInBlockByTransaction('93b79d48ebcf34e176ee6a785cef329c45e70b9a47ae66284b8fc42ca297570b');
        $this->assertEquals(TRUE, is_array($aPosition));
        $this->assertEquals(347417, $aPosition['block']);
        $this->assertEquals(1191, $aPosition['position']);
        // Invalid transaction
        $aPosition = TX::getPositionInBlockByTransaction('invalid_transaction_hash');
        $this->assertEquals(TRUE, is_array($aPosition));
        $this->assertEquals(NULL, $aPosition['block']);
        $this->assertEquals(NULL, $aPosition['position']);
        // Invalid transaction
        $aPosition = TX::getPositionInBlockByTransaction(123);
        $this->assertEquals(TRUE, is_array($aPosition));
        $this->assertEquals(NULL, $aPosition['block']);
        $this->assertEquals(NULL, $aPosition['position']);
        // Zero transaction
        $aPosition = TX::getPositionInBlockByTransaction(false);
        $this->assertEquals(TRUE, is_array($aPosition));
        $this->assertEquals(NULL, $aPosition['block']);
        $this->assertEquals(NULL, $aPosition['position']);
    }
    /**
     * @covers \AmiLabs\Chainy\TX::getTransactionByPositionInBlock
     */
    public function testGetTransactionByPositionInBlock(){
        // Valid transaction
        $txn = TX::getTransactionByPositionInBlock(349633, 634);
        $this->assertEquals('a716ab62a35baa0aa75bb675f8e479b212fc45b1b5320faadd6b0b0ed74e426e', $txn);
        // Valid transaction
        $txn = TX::getTransactionByPositionInBlock(347417, 1191);
        $this->assertEquals('93b79d48ebcf34e176ee6a785cef329c45e70b9a47ae66284b8fc42ca297570b', $txn);
        // Zero position
        $txn = TX::getTransactionByPositionInBlock(350000, 0);
        $this->assertEquals(NULL, $txn);
        // Invalid position
        $txn = TX::getTransactionByPositionInBlock(350000, -1);
        $this->assertEquals(NULL, $txn);
        // Invalid position
        $txn = TX::getTransactionByPositionInBlock(350000, 100000);
        $this->assertEquals(NULL, $txn);
        // Zero block
        $txn = TX::getTransactionByPositionInBlock(0, 1);
        $this->assertEquals(NULL, $txn);
        // Invalid block
        $txn = TX::getTransactionByPositionInBlock(-1, 1);
        $this->assertEquals(NULL, $txn);
        // Invalid block
        $txn = TX::getTransactionByPositionInBlock(100000000, 1);
        $this->assertEquals(NULL, $txn);
    }
}