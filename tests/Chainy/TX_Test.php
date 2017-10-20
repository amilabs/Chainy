<?php

/**
 * Copyright 2016 Everex https://everex.io
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace AmiLabs\Chainy\UnitTests;

use PHPUnit_Framework_TestCase;
use \AmiLabs\Chainy\TX;

$appName = 'ut.chainy';
require_once dirname(__FILE__) . '/../../app/init.php';

/**
 * Unit tests.
 *
 * @package \AmiLabs\Chainy\UnitTests
 */
class TX_Test extends PHPUnit_Framework_TestCase{
    /**
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
    */
}
