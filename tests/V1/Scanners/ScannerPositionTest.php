<?php

/**
 * Copyright (c) 2015-present Ganbaro Digital Ltd
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  Libraries
 * @package   TextParser\V1\Scanners
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-text-parser
 */

namespace GanbaroDigitalTest\TextParser\V1\Scanners;

use GanbaroDigital\TextParser\V1\Scanners\ScannerPosition;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\TextParser\V1\Scanners\ScannerPosition
 */
class ScannerPositionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function test_can_instantiate()
    {
        // ----------------------------------------------------------------
        // setup your test


        // ----------------------------------------------------------------
        // perform the change

        $unit = new ScannerPosition(1,0,0);

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue(is_object($unit));
    }

    /**
     * @covers ::getLineNumber
     */
    public function test_can_get_line_number()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedLineNumber = 100;
        $expectedLineOffset = 98765;
        $expectedStreamPosition = 23456;

        $unit = new ScannerPosition($expectedLineNumber, $expectedLineOffset, $expectedStreamPosition);

        // ----------------------------------------------------------------
        // perform the change

        $actualLineNumber = $unit->getLineNumber();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedLineNumber, $actualLineNumber);
    }

    /**
     * @covers ::getLineOffset
     */
    public function test_can_get_line_offset()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedLineNumber = 100;
        $expectedLineOffset = 98765;
        $expectedStreamPosition = 23456;

        $unit = new ScannerPosition($expectedLineNumber, $expectedLineOffset, $expectedStreamPosition);

        // ----------------------------------------------------------------
        // perform the change

        $actualLineOffset = $unit->getLineOffset();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedLineOffset, $actualLineOffset);
    }

    /**
     * @covers ::getStreamPosition
     */
    public function test_can_get_stream_position()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedLineNumber = 100;
        $expectedLineOffset = 98765;
        $expectedStreamPosition = 23456;

        $unit = new ScannerPosition($expectedLineNumber, $expectedLineOffset, $expectedStreamPosition);

        // ----------------------------------------------------------------
        // perform the change

        $actualStreamPosition = $unit->getStreamPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedStreamPosition, $actualStreamPosition);
    }
}
