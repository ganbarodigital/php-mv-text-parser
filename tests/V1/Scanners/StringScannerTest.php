<?php

/**
 * Copyright (c) 2016-present Ganbaro Digital Ltd
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
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-text-parser
 */

namespace GanbaroDigitalTest\TextParser\V1\Scanners;

use GanbaroDigital\TextParser\V1\Scanners\ScannerPosition;
use GanbaroDigital\TextParser\V1\Scanners\StringScanner;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\TextParser\V1\Scanners\StringScanner
 */
class StringScannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function test_can_instantiate()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedContents = "hello, world!";

        // ----------------------------------------------------------------
        // perform the change

        $unit = new StringScanner($expectedContents, 'unit test');

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(StringScanner::class, $unit);
    }

    /**
     * @covers ::__construct
     * @covers ::getLabel
     */
    public function test_StringScanner_has_a_label()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedLabel = "this is a label";

        // ----------------------------------------------------------------
        // perform the change

        $unit = new StringScanner("hello, world!", $expectedLabel);
        $actualLabel = $unit->getLabel();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedLabel, $actualLabel);
    }

    /**
     * @covers ::__construct
     */
    public function test_defaults_to_position_of_line_1_offset_0()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedContents = "hello, world!";
        $expectedPosition = new ScannerPosition(1,0,0);

        // ----------------------------------------------------------------
        // perform the change

        $unit = new StringScanner($expectedContents, 'unit test');
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
    }

    /**
     * @covers ::__construct
     */
    public function test_can_start_with_a_given_position()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedContents = "hello, world!\nwhat a lovely day";
        $expectedLineNo = 2;
        $expectedLineOffset = 5;
        $expectedStreamPosition = 0;

        $expectedPosition = new ScannerPosition(
            $expectedLineNo,
            $expectedLineOffset,
            $expectedStreamPosition
        );

        // ----------------------------------------------------------------
        // perform the change

        $unit = new StringScanner($expectedContents, 'unit test', $expectedLineNo, $expectedLineOffset);
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
    }

    /**
     * @covers ::readBytes
     */
    public function test_readBytes_can_read_requested_number_of_bytes()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!";
        $expectedRemainder = "\nwhat a lovely day";
        $input = $expectedBytes . $expectedRemainder;

        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        $actualBytes = $unit->readBytes(strlen($expectedBytes));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedBytes, $actualBytes);
    }

    /**
     * @covers ::readBytes
     * @covers ::updatePositionFrom
     */
    public function test_stream_moves_after_calling_readBytes()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!";
        $expectedRemainder = "\nwhat a lovely day";
        $input = $expectedBytes . $expectedRemainder;
        $expectedPosition = new ScannerPosition(1, 13, 13);

        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // what's left in the input?
        // this will prove that we have actually moved to the right point
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::readBytes
     * @covers ::updatePositionFrom
     */
    public function test_readBytes_expands_tab_stops_when_tracking_position()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!\nwhat a lovely day\n\tdon't\tyou\tthink";
        $expectedRemainder = "?";
        $input = $expectedBytes . $expectedRemainder;
        $expectedPosition = new ScannerPosition(3, 29, strlen($expectedBytes));

        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // what's left in the input?
        // this will prove that we have actually moved to the right point
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::readBytes
     * @covers ::updatePositionFrom
     */
    public function test_readBytes_tracks_position_across_input_lines()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!\nwhat a lovely day\ndon't ";
        $expectedRemainder = "you think?";
        $input = $expectedBytes . $expectedRemainder;
        $expectedPosition = new ScannerPosition(3, 6, strlen($expectedBytes));

        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // what's left in the input?
        // this will prove that we have actually moved to the right point
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::readBytes
     * @covers ::updatePositionFrom
     * @covers ::isAtEndOfInput
     */
    public function test_readBytes_returns_whats_available_if_less_than_requested_bytes_left()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!";
        $expectedPosition = new ScannerPosition(1, 13, 13);

        $unit = new StringScanner($expectedBytes, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $actualBytes = $unit->readBytes(strlen($expectedBytes) * 2);
        $this->assertEquals($expectedBytes, $actualBytes);

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertTrue($unit->isAtEndOfInput());
    }

    /**
     * @covers ::readBytes
     * @covers ::updatePositionFrom
     * @covers ::isAtEndOfInput
     */
    public function test_readBytes_returns_empty_string_if_at_end_of_stream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $inputData = "hello, world!";
        $expectedPosition = new ScannerPosition(1, 13, 13);

        $unit = new StringScanner($inputData, 'unit test');
        $unit->moveBytes(strlen($inputData));
        $this->assertTrue($unit->isAtEndOfInput());

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $actualBytes = $unit->readBytes(100);
        $this->assertEquals('', $actualBytes);

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertTrue($unit->isAtEndOfInput());
    }

    /**
     * @covers ::readRemainingBytes
     * @covers ::isAtEndOfInput
     */
    public function test_readRemainingBytes_reads_whatevers_left_in_the_stream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!";
        $expectedRemainder = "\nwhat a lovely day";
        $input = $expectedBytes . $expectedRemainder;

        $unit = new StringScanner($input, 'unit test');
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // ----------------------------------------------------------------
        // perform the change

        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedRemainder, $actualRemainder);
        $this->assertTrue($unit->isAtEndOfInput());
    }

    /**
     * @covers ::readRemainingBytes
     * @covers ::updatePositionFrom
     */
    public function test_stream_moves_after_calling_readRemainingBytes()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!";
        $expectedRemainder = "\nwhat a lovely day";
        $input = $expectedBytes . $expectedRemainder;
        $expectedPosition = new ScannerPosition(2, 17, 31);

        $unit = new StringScanner($input, 'unit test');

        // read the bytes
        $actualBytes = $unit->readBytes(strlen($expectedBytes));

        // ----------------------------------------------------------------
        // perform the change

        // what's left in the input stream?
        $actualRemainder = $unit->readRemainingBytes();

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedRemainder, $actualRemainder);
        $this->assertEquals($expectedPosition, $actualPosition);
    }

    /**
     * @covers ::readRemainingBytes
     * @covers ::updatePositionFrom
     */
    public function test_readRemainingBytes_tracks_position_across_input_lines()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!\nwhat a lovely day\ndon't ";
        $expectedRemainder = "you think?";
        $input = $expectedBytes . $expectedRemainder;
        $expectedPosition = new ScannerPosition(3, 16, 48);

        $unit = new StringScanner($input, 'unit test');

        // read the bytes
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // ----------------------------------------------------------------
        // perform the change

        // what's left in the input stream?
        $actualRemainder = $unit->readRemainingBytes();

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedRemainder, $actualRemainder);
        $this->assertEquals($expectedPosition, $actualPosition);
    }

    /**
     * @covers ::readRemainingBytes
     * @covers ::updatePositionFrom
     * @covers ::isAtEndOfInput
     */
    public function test_readRemainingBytes_returns_empty_string_if_at_end_of_stream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $input = "hello, world!";
        $expectedPosition = new ScannerPosition(1, 13, 13);

        $unit = new StringScanner($input, 'unit test');
        $unit->moveBytes(strlen($input));
        $this->assertTrue($unit->isAtEndOfInput());

        // ----------------------------------------------------------------
        // perform the change

        // read the remaining bytes
        // there shouldn't be any
        $actualRemainder = $unit->readRemainingBytes();

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals('', $actualRemainder);
        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertTrue($unit->isAtEndOfInput());
    }

    /**
     * @covers ::readBytesAhead
     */
    public function test_readBytesAhead_can_read_requested_number_of_bytes()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!";
        $expectedRemainder = "\nwhat a lovely day";
        $input = $expectedBytes . $expectedRemainder;

        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        $actualBytes = $unit->readBytesAhead(strlen($expectedBytes));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedBytes, $actualBytes);
    }

    /**
     * @covers ::readBytesAhead
     */
    public function test_stream_does_not_move_after_calling_readBytesAhead()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!";
        $expectedRemainder = "\nwhat a lovely day";

        $expectedPosition = new ScannerPosition(1, 0, 0);

        $input = $expectedBytes . $expectedRemainder;
        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $actualBytes = $unit->readBytesAhead(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // what's left in the input stream?
        // this will prove that the stream didn't actually move
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedBytes . $expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::readBytesAhead
     * @covers ::isAtEndOfInput
     */
    public function test_readBytesAhead_returns_whats_available_if_less_than_requested_bytes_left()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!";

        $unit = new StringScanner($expectedBytes, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $actualBytes = $unit->readBytesAhead(strlen($expectedBytes) * 2);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedBytes, $actualBytes);
        $this->assertFalse($unit->isAtEndOfInput());
    }

    /**
     * @covers ::readBytesAhead
     * @covers ::isAtEndOfInput
     */
    public function test_readBytesAhead_returns_empty_string_if_at_end_of_stream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $inputData = "hello, world!";

        $unit = new StringScanner($inputData, 'unit test');
        $unit->moveBytes(strlen($inputData));
        $this->assertTrue($unit->isAtEndOfInput());

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytesAhead(100);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals('', $actualBytes);
        $this->assertTrue($unit->isAtEndOfInput());
    }

    /**
     * @covers ::moveBytes
     * @covers ::updatePositionFrom
     */
    public function test_movesBytes_can_move_position_by_requested_number_of_bytes()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!";
        $expectedRemainder = "\nwhat a lovely day";
        $input = $expectedBytes . $expectedRemainder;
        $expectedPosition = new ScannerPosition(1, 13, 13);

        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $unit->moveBytes(strlen($expectedBytes));

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // what's left in the input stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::moveBytes
     * @covers ::updatePositionFrom
     */
    public function test_moveBytes_tracks_position_across_input_lines()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!\nwhat a lovely day\ndon't ";
        $expectedRemainder = "you think?";
        $input = $expectedBytes . $expectedRemainder;
        $expectedPosition = new ScannerPosition(3, 6, strlen($expectedBytes));

        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $unit->moveBytes(strlen($expectedBytes));

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // what's left in the input stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::moveBytes
     * @covers ::updatePositionFrom
     * @covers ::isAtEndOfInput
     */
    public function test_movesBytes_moves_position_by_whats_available_if_less_than_requested_bytes_left()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!";
        $expectedPosition = new ScannerPosition(1, 13, 13);

        $unit = new StringScanner($expectedBytes, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $unit->moveBytes(strlen($expectedBytes) * 2);

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertTrue($unit->isAtEndOfInput());
    }

    /**
     * @covers ::moveBytes
     * @covers ::updatePositionFrom
     * @covers ::isAtEndOfInput
     */
    public function test_moveBytes_does_not_move_position_if_at_end_of_stream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $inputData = "hello, world!";
        $expectedPosition = new ScannerPosition(1, 13, 13);

        $unit = new StringScanner($inputData, 'unit test');
        $unit->readBytes(strlen($inputData));
        $this->assertTrue($unit->isAtEndOfInput());

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->moveBytes(100);

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertTrue($unit->isAtEndOfInput());
    }

    /**
     * @covers ::movePastWhitespaceOnCurrentLine
     */
    public function test_movePastWhitespaceOnCurrentLine_will_read_horizontal_whitespace_only()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedRemainder = "\nwhat a lovely day";
        $input = str_repeat("\t ", 4096) . $expectedRemainder;

        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        $hasMoved = $unit->movePastWhitespaceOnCurrentLine();

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($hasMoved);
    }

    /**
     * @covers ::movePastWhitespaceOnCurrentLine
     */
    public function test_stream_moves_after_calling_movePastWhitespaceOnCurrentLine()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedRemainder = "\nwhat a lovely day";
        $input = str_repeat("\t ", 1024) . $expectedRemainder;
        // the expectedPosition expects a tabSize of 8
        $expectedPosition = new ScannerPosition(1, 8193, 2048);

        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $hasMoved = $unit->movePastWhitespaceOnCurrentLine();
        $this->assertTrue($hasMoved);

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // what's left in the input stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::movePastWhitespaceOnCurrentLine
     */
    public function test_movePastWhitespaceOnCurrentLine_does_not_consume_eol_character()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedRemainder = "\nyou think?";
        $input = str_repeat(" ", 10) . $expectedRemainder;
        $expectedPosition = new ScannerPosition(1, 10, 10);

        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $hasMoved = $unit->movePastWhitespaceOnCurrentLine();
        $this->assertTrue($hasMoved);

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // what's left in the input stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::movePastWhitespaceOnCurrentLine
     * @covers ::updatePositionFrom
     */
    public function test_movePastWhitespaceOnCurrentLine_will_consume_trailing_horizontal_whitespace()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!";
        $input = $expectedBytes . str_repeat("\t", 10);
        // the expectedPosition expects a tabSize of 8
        $expectedPosition = new ScannerPosition(1, 88, 23);

        $unit = new StringScanner($input, 'unit test');

        // move to the start of the whitespace
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // ----------------------------------------------------------------
        // perform the change

        // consume available horizontal whitespace
        $hasMoved = $unit->movePastWhitespaceOnCurrentLine();
        $this->assertTrue($hasMoved);

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
    }

    /**
     * @covers ::movePastWhitespaceOnCurrentLine
     */
    public function test_movePastWhitespaceOnCurrentLine_returns_false_if_at_end_of_stream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $inputData = "hello, world!";
        $expectedPosition = new ScannerPosition(1, 13, 13);

        $unit = new StringScanner($inputData, 'unit test');
        $unit->readBytes(strlen($inputData));
        $this->assertTrue($unit->isAtEndOfInput());

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $hasMoved = $unit->movePastWhitespaceOnCurrentLine();

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertFalse($hasMoved);
        $this->assertEquals($expectedPosition, $actualPosition);
    }

    /**
     * @covers ::movePastWhitespace
     */
    public function test_movePastWhitespace_will_read_horizontal_whitespace()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedRemainder = "what a lovely day";
        $input = str_repeat("\t ", 4096) . "\n" . $expectedRemainder;

        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        $hasMoved = $unit->movePastWhitespace();
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($hasMoved);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::movePastWhitespace
     */
    public function test_movePastWhitespace_will_read_vertical_whitespace_too()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedRemainder = "what a lovely day";
        $input = str_repeat("\t \n", 4096) . "\n" . $expectedRemainder;

        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        $hasMoved = $unit->movePastWhitespace();
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($hasMoved);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::movePastWhitespace
     */
    public function test_stream_moves_after_calling_movePastWhitespace()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedRemainder = "what a lovely day";
        $expectedPosition = new ScannerPosition(1025, 0, 2048);

        $input = str_repeat("\r\n", 1024) . $expectedRemainder;
        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $hasMoved = $unit->movePastWhitespace();

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // what's left in the input stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($hasMoved);
        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::movePastWhitespace
     */
    public function test_movePastWhitespace_does_consume_eol_character()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedRemainder = "you think?";
        $expectedPosition = new ScannerPosition(2, 0, 11);

        $input = str_repeat(" ", 10) . "\n" . $expectedRemainder;
        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $hasMoved = $unit->movePastWhitespace();

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // what's left in the input stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($hasMoved);
        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::movePastWhitespace
     */
    public function test_movePastWhitespace_will_consume_trailing_whitespace()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!";
        $expectedPosition = new ScannerPosition(11, 0, 23);

        $input = $expectedBytes . str_repeat("\n", 10);
        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // consume available whitespace
        $hasMoved = $unit->movePastWhitespace();

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($hasMoved);
        $this->assertEquals($expectedPosition, $actualPosition);
    }

    /**
     * @covers ::movePastWhitespace
     * @covers ::isAtEndOfInput
     */
    public function test_movePastWhitespace_returns_false_if_at_end_of_stream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $inputData = "hello, world!";
        $expectedPosition = new ScannerPosition(1, 13, 13);

        $unit = new StringScanner($inputData, 'unit test');

        $unit->moveBytes(strlen($inputData));
        $this->assertTrue($unit->isAtEndOfInput());

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $hasMoved = $unit->movePastWhitespace();

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertFalse($hasMoved);
        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertTrue($unit->isAtEndOfInput());
    }

    /**
     * @covers ::getPosition
     */
    public function test_getPosition_returns_a_ScannerPosition()
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new StringScanner("hello, world!", 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(ScannerPosition::class, $actualPosition);
    }

    /**
     * @covers ::getPosition
     */
    public function test_getPosition_returns_current_position_in_input_stream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!\nwhat a lovely day\ndon't ";
        $expectedRemainder = "you think?";
        $expectedPosition = new ScannerPosition(3, 6, strlen($expectedBytes));

        $input = $expectedBytes . $expectedRemainder;
        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // where have we ended up?
        $actualPosition = $unit->getPosition();

        // what's left in the input stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::getPosition
     * @covers ::setPosition
     */
    public function test_setPosition_changes_where_the_scanner_looks_next()
    {
        // ----------------------------------------------------------------
        // setup your test
        //
        // the best way to prove that `setPosition()` does its job is to do
        // some reads after moving around in the stream

        $expectedBytes1 = "hello, world!\n";
        $expectedBytes2 = "what a lovely day\n";
        $expectedBytes3 = "don't ";
        $expectedRemainder = "you think?";

        $expectedPosition1 = new ScannerPosition(2, 0, strlen($expectedBytes1));
        $expectedPosition2 = new ScannerPosition(3, 0, strlen($expectedBytes1 . $expectedBytes2));
        $expectedPosition3 = new ScannerPosition(3, 6, strlen($expectedBytes1 . $expectedBytes2 . $expectedBytes3));

        $input = $expectedBytes1 . $expectedBytes2 . $expectedBytes3 . $expectedRemainder;
        $unit = new StringScanner($input, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // grab all of our expected bytes first
        // and see where we are
        $unit->readBytes(strlen($expectedBytes1 . $expectedBytes2 . $expectedBytes3));

        // what's left in the input stream?
        // this will prove that our read above grabbed what we needed
        $actualRemainder = $unit->readRemainingBytes();
        $this->assertEquals($expectedRemainder, $actualRemainder);

        // let's try and grab just the first part of the third line now
        $unit->setPosition($expectedPosition2);
        $this->assertEquals($expectedPosition2, $unit->getPosition());
        $actualBytes3 = $unit->readBytes(strlen($expectedBytes3));
        $actualPosition3 = $unit->getPosition();

        // let's try and grab just the second line now
        $unit->setPosition($expectedPosition1);
        $this->assertEquals($expectedPosition1, $unit->getPosition());
        $actualBytes2 = $unit->readBytes(strlen($expectedBytes2));
        $actualPosition2 = $unit->getPosition();

        // finally, let's try and grab just the first line now
        $unit->setPosition(ScannerPosition::newStartOfStream());

        $actualBytes1 = $unit->readBytes(strlen($expectedBytes1));
        $actualPosition1 = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results
        //
        // we're testing the results in the order we got them.
        // that way, if there is a problem, we fail at the point that went
        // wrong.

        // did we grab the third line correctly?
        $this->assertEquals($expectedBytes3, $actualBytes3);
        $this->assertEquals($expectedPosition3, $actualPosition3);

        // did we grab the second line correctly?
        $this->assertEquals($expectedBytes2, $actualBytes2);
        $this->assertEquals($expectedPosition2, $actualPosition2);

        // did we grab the first line correctly?
        $this->assertEquals($expectedBytes1, $actualBytes1);
        $this->assertEquals($expectedPosition1, $actualPosition1);
    }

    /**
     * @covers ::__toString
     */
    public function test_toString_returns_the_whole_input_stream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $inputBytes = "hello, world\n";
        $expectedRemainder = "what a lovely day\n";
        $expectedBytes = $inputBytes . $expectedRemainder;

        $unit = new StringScanner($expectedBytes, 'unit test');

        // to make this test valid, we don't want to be at the start of
        // the stream when we call `__toString()`
        $unit->readBytes(strlen($inputBytes));
        $expectedPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // perform the change

        $actualBytes = (string)$unit;
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedBytes, $actualBytes);
    }

    /**
     * @covers ::__toString
     */
    public function test_toString_does_not_move_the_stream_position()
    {
        // ----------------------------------------------------------------
        // setup your test

        $inputBytes = "hello, world\n";
        $expectedRemainder = "what a lovely day\n";
        $expectedBytes = $inputBytes . $expectedRemainder;

        $unit = new StringScanner($expectedBytes, 'unit test');

        // to make this test valid, we don't want to be at the start of
        // the stream when we call `__toString()`
        $unit->readBytes(strlen($inputBytes));
        $expectedPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // perform the change

        $actualBytes = (string)$unit;
        $actualPosition = $unit->getPosition();
        $actualRemainder = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::readAheadRemainingBytes
     */
    public function test_readAheadRemainingBytes_returns_the_rest_of_the_input_stream()
    {
        // ----------------------------------------------------------------
        // setup your test

        $inputBytes = "hello, world\n";
        $expectedRemainder = "what a lovely day\n";

        $input = $inputBytes . $expectedRemainder;
        $unit = new StringScanner($input, 'unit test');

        // to make this test valid, we don't want to be at the start of
        // the stream when we call `readAheadRemainingBytes()`
        $unit->readBytes(strlen($inputBytes));

        // ----------------------------------------------------------------
        // perform the change

        $actualRemainder = $unit->readAheadRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @covers ::readAheadRemainingBytes
     */
    public function test_readAheadRemainingBytes_does_not_move_the_stream_position()
    {
        // ----------------------------------------------------------------
        // setup your test

        $inputBytes = "hello, world\n";
        $expectedRemainder = "what a lovely day\n";

        $input = $inputBytes . $expectedRemainder;
        $unit = new StringScanner($input, 'unit test');

        $unit->readBytes(strlen($inputBytes));
        $expectedPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // perform the change

        $actualRemainder = $unit->readAheadRemainingBytes();
        $actualPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }
}
