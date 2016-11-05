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
use GanbaroDigital\TextParser\V1\Scanners\StreamScanner;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\TextParser\V1\Scanners\StreamScanner
 */
class StreamScannerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::newFromString
     */
    public function test_has_static_factory_for_strings()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedContents = "hello, world!";

        // ----------------------------------------------------------------
        // perform the change

        $unit = StreamScanner::newFromString($expectedContents, 'unit test');
        $this->assertInstanceOf(StreamScanner::class, $unit);

        $actualContents = $unit->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedContents, $actualContents);
    }

    /**
     * @covers ::newFrom
     */
    public function test_has_static_factory_for_mixed_types()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedContents = "hello, world!";

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedContents);
        fseek($streamResource, 0);

        // ----------------------------------------------------------------
        // perform the change

        $unit1 = StreamScanner::newFrom($expectedContents, 'unit test');
        $this->assertInstanceOf(StreamScanner::class, $unit1);
        $actualContents1 = $unit1->readRemainingBytes();

        $unit2 = StreamScanner::newFrom($streamResource, 'unit test');
        $this->assertInstanceOf(StreamScanner::class, $unit2);
        $actualContents2 = $unit2->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedContents, $actualContents1);
        $this->assertEquals($expectedContents, $actualContents2);
    }

    /**
     * @covers ::newFrom
     * @expectedException InvalidArgumentException
     * @dataProvider provideNonStreams
     */
    public function test_newFrom_throws_InvalidArgumentException_if_non_stream_supplied($input)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        StreamScanner::newFrom($input, "unit test");

        // ----------------------------------------------------------------
        // test the results

        $this->markTestIncomplete("not implemented yet");
    }

    /**
     * @covers ::__construct
     */
    public function test_can_instantiate()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedContents = "hello, world!";

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedContents);
        fseek($streamResource, 0);

        // ----------------------------------------------------------------
        // perform the change

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(StreamScanner::class, $unit);
    }

    /**
     * @covers ::__construct
     * @expectedException InvalidArgumentException
     * @dataProvider provideNonStreams
     */
    public function test_constructor_throws_InvalidArgumentException_if_non_stream_supplied($input)
    {
        // ----------------------------------------------------------------
        // setup your test

        // ----------------------------------------------------------------
        // perform the change

        StreamScanner::newFrom($input, "unit test");

        // ----------------------------------------------------------------
        // test the results

        $this->markTestIncomplete("not implemented yet");
    }

    /**
     * @covers ::__construct
     * @covers ::getLabel
     */
    public function test_StreamScanner_has_a_label()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedLabel = "this is a label";

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, "hello, world");
        fseek($streamResource, 0);

        // ----------------------------------------------------------------
        // perform the change

        $unit = new StreamScanner($streamResource, $expectedLabel);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedContents);
        fseek($streamResource, 0);

        $expectedPosition = new ScannerPosition(1,0,0);

        // ----------------------------------------------------------------
        // perform the change

        $unit = new StreamScanner($streamResource, 'unit test');
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
        $expectedStreamPosition = 19;

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedContents);
        fseek($streamResource, $expectedStreamPosition);

        $expectedPosition = new ScannerPosition(
            $expectedLineNo,
            $expectedLineOffset,
            $expectedStreamPosition
        );

        // ----------------------------------------------------------------
        // perform the change

        $unit = new StreamScanner($streamResource, 'unit test', $expectedLineNo, $expectedLineOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

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
        $expectedPosition = new ScannerPosition(1, 13, 13);

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // what's left in the PHP stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = fread($streamResource, strlen($expectedRemainder));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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
        $expectedPosition = new ScannerPosition(3, 29, strlen($expectedBytes));

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // what's left in the PHP stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = fread($streamResource, strlen($expectedRemainder));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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
        $expectedPosition = new ScannerPosition(3, 6, strlen($expectedBytes));

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // what's left in the PHP stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = fread($streamResource, strlen($expectedRemainder));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytes(strlen($expectedBytes) * 2);
        $this->assertEquals($expectedBytes, $actualBytes);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $inputData);

        $unit = new StreamScanner($streamResource, 'unit test', 1, strlen($inputData));
        $this->assertTrue($unit->isAtEndOfInput());

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytes(100);
        $this->assertEquals('', $actualBytes);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');
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
        $expectedPosition = new ScannerPosition(2, 17, 31);

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // ----------------------------------------------------------------
        // perform the change

        // what's left in the PHP stream?
        $actualRemainder = $unit->readRemainingBytes();

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedRemainder, $actualRemainder);

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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
        $expectedPosition = new ScannerPosition(3, 16, 48);

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // ----------------------------------------------------------------
        // perform the change

        // what's left in the PHP stream?
        $actualRemainder = $unit->readRemainingBytes();

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedRemainder, $actualRemainder);

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $inputData = "hello, world!";
        $expectedPosition = new ScannerPosition(1, 13, 13);

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $inputData);

        $unit = new StreamScanner($streamResource, 'unit test', 1, strlen($inputData));
        $this->assertTrue($unit->isAtEndOfInput());

        // ----------------------------------------------------------------
        // perform the change

        // read the remaining bytes
        // there shouldn't be any
        $actualRemainder = $unit->readRemainingBytes();

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals('', $actualRemainder);

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytesAhead(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // what's left in the PHP stream?
        // this will prove that the stream didn't actually move
        $actualRemainder = fread($streamResource, strlen($expectedBytes . $expectedRemainder));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $inputData);

        $unit = new StreamScanner($streamResource, 'unit test', 1, strlen($inputData));
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
        $expectedPosition = new ScannerPosition(1, 13, 13);

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $unit->moveBytes(strlen($expectedBytes));

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // what's left in the PHP stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = fread($streamResource, strlen($expectedRemainder));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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
        $expectedPosition = new ScannerPosition(3, 6, strlen($expectedBytes));

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $unit->moveBytes(strlen($expectedBytes));

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // what's left in the PHP stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = fread($streamResource, strlen($expectedRemainder));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $unit->moveBytes(strlen($expectedBytes) * 2);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $inputData);

        $unit = new StreamScanner($streamResource, 'unit test', 1, strlen($inputData));
        $this->assertTrue($unit->isAtEndOfInput());

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->moveBytes(100);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, str_repeat("\t ", 4096) . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

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
        // the expectedPosition expects a tabSize of 8
        $expectedPosition = new ScannerPosition(1, 8193, 2048);

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, str_repeat("\t ", 1024) . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $hasMoved = $unit->movePastWhitespaceOnCurrentLine();
        $this->assertTrue($hasMoved);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // what's left in the PHP stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = fread($streamResource, strlen($expectedRemainder));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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
        $expectedPosition = new ScannerPosition(1, 10, 10);

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, str_repeat(" ", 10) . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $hasMoved = $unit->movePastWhitespaceOnCurrentLine();
        $this->assertTrue($hasMoved);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // what's left in the PHP stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = fread($streamResource, strlen($expectedRemainder));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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
        // the expectedPosition expects a tabSize of 8
        $expectedPosition = new ScannerPosition(1, 88, 23);

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . str_repeat("\t", 10));
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // consume available horizontal whitespace
        $hasMoved = $unit->movePastWhitespaceOnCurrentLine();
        $this->assertTrue($hasMoved);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $inputData);

        $unit = new StreamScanner($streamResource, 'unit test', 1, strlen($inputData));
        $this->assertTrue($unit->isAtEndOfInput());

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $hasMoved = $unit->movePastWhitespaceOnCurrentLine();
        $this->assertFalse($hasMoved);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
    }

    /**
     * @covers ::movePastWhitespace
     */
    public function test_movePastWhitespace_will_read_horizontal_whitespace()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedRemainder = "\nwhat a lovely day";

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, str_repeat("\t ", 4096) . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        $hasMoved = $unit->movePastWhitespace();

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($hasMoved);
    }

    /**
     * @covers ::movePastWhitespace
     */
    public function test_movePastWhitespace_will_read_vertical_whitespace_too()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedRemainder = "\nwhat a lovely day";

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, str_repeat("\t \n", 4096) . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        $hasMoved = $unit->movePastWhitespace();

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue($hasMoved);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, str_repeat("\r\n", 1024) . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $hasMoved = $unit->movePastWhitespace();
        $this->assertTrue($hasMoved);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // what's left in the PHP stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = fread($streamResource, strlen($expectedRemainder));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, str_repeat(" ", 10) . "\n" . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $hasMoved = $unit->movePastWhitespace();
        $this->assertTrue($hasMoved);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // what's left in the PHP stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = fread($streamResource, strlen($expectedRemainder));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . str_repeat("\n", 10));
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // consume available whitespace
        $hasMoved = $unit->movePastWhitespace();
        $this->assertTrue($hasMoved);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $inputData);

        $unit = new StreamScanner($streamResource, 'unit test', 1, strlen($inputData));
        $this->assertTrue($unit->isAtEndOfInput());

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $hasMoved = $unit->movePastWhitespace();
        $this->assertFalse($hasMoved);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
        $this->assertTrue($unit->isAtEndOfInput());
    }

    /**
     * @covers ::getPosition
     */
    public function test_getPosition_returns_a_ScannerPosition()
    {
        // ----------------------------------------------------------------
        // setup your test

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, "hello, world!");
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // read the bytes
        // - this moves the stream along
        $actualBytes = $unit->readBytes(strlen($expectedBytes));
        $this->assertEquals($expectedBytes, $actualBytes);

        // where have we ended up?
        // - what does our unit think?
        // - and what does the underlying PHP stream think? :)
        $actualPosition = $unit->getPosition();
        $actualStreamOffset = ftell($streamResource);

        // what's left in the PHP stream?
        // this will prove that we have actually moved to the right point
        $actualRemainder = fread($streamResource, strlen($expectedRemainder));

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
        $this->assertEquals($actualPosition->getStreamPosition(), $actualStreamOffset);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes1 . $expectedBytes2 . $expectedBytes3 . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        // grab all of our expected bytes first
        // and see where we are
        $unit->readBytes(strlen($expectedBytes1 . $expectedBytes2 . $expectedBytes3));

        // what's left in the PHP stream?
        // this will prove that our read above grabbed what we needed
        $actualRemainder = fread($streamResource, strlen($expectedRemainder));
        $this->assertEquals($expectedRemainder, $actualRemainder);

        // let's try and grab just the first part of the third line now
        $unit->setPosition($expectedPosition2);
        $this->assertEquals($expectedPosition2, $unit->getPosition());
        $actualBytes3 = $unit->readBytes(strlen($expectedBytes3));
        $actualPosition3 = $unit->getPosition();
        $actualStreamOffset3 = ftell($streamResource);

        // let's try and grab just the second line now
        $unit->setPosition($expectedPosition1);
        $this->assertEquals($expectedPosition1, $unit->getPosition());
        $actualBytes2 = $unit->readBytes(strlen($expectedBytes2));
        $actualPosition2 = $unit->getPosition();
        $actualStreamOffset2 = ftell($streamResource);

        // finally, let's try and grab just the first line now
        $unit->setPosition($unit->getStartPosition());
        $this->assertEquals(0, ftell($streamResource));

        $actualBytes1 = $unit->readBytes(strlen($expectedBytes1));
        $actualPosition1 = $unit->getPosition();
        $actualStreamOffset1 = ftell($streamResource);

        // ----------------------------------------------------------------
        // test the results
        //
        // we're testing the results in the order we got them.
        // that way, if there is a problem, we fail at the point that went
        // wrong.

        // did we grab the third line correctly?
        $this->assertEquals($expectedBytes3, $actualBytes3);
        $this->assertEquals($expectedPosition3, $actualPosition3);
        $this->assertEquals($actualPosition3->getStreamPosition(), $actualStreamOffset3);

        // did we grab the second line correctly?
        $this->assertEquals($expectedBytes2, $actualBytes2);
        $this->assertEquals($expectedPosition2, $actualPosition2);
        $this->assertEquals($actualPosition2->getStreamPosition(), $actualStreamOffset2);

        // did we grab the first line correctly?
        $this->assertEquals($expectedBytes1, $actualBytes1);
        $this->assertEquals($expectedPosition1, $actualPosition1);
        $this->assertEquals($actualPosition1->getStreamPosition(), $actualStreamOffset1);
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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $expectedBytes);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $inputBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

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

        $streamResource = fopen("php://memory", "wb+");
        fwrite($streamResource, $inputBytes . $expectedRemainder);
        fseek($streamResource, 0);

        $unit = new StreamScanner($streamResource, 'unit test');

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

    /**
     * @covers ::getStartPosition
     */
    public function test_getStartPosition_returns_location_we_started_scanning_from()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBytes = "hello, world!\n";
        $expectedRemainder = "what a lovely day today";

        $unit = StreamScanner::newfromString($expectedBytes . $expectedRemainder, 'unit test');
        $expectedPosition = $unit->getPosition();

        // ----------------------------------------------------------------
        // perform the change

        // move along, to change the position in the input stream
        $unit->readBytes(strlen($expectedBytes));
        $intermediatePosition = $unit->getPosition();
        $this->assertNotEquals($expectedPosition, $intermediatePosition);

        // now perform a read, to prove the position moved
        $actualRemainder = $unit->readRemainingBytes();
        $this->assertEquals($expectedRemainder, $actualRemainder);

        // finally, ask the scanner where it started from
        $actualPosition = $unit->getStartPosition();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedPosition, $actualPosition);
    }

    public function provideNonStreams()
    {
        return [
            "null" => [ null ],
            "array" => [ [ "hello, world" ] ],
            "true" => [ true ],
            "false" => [ false ],
            "callable" => [ function() { return "hello, world"; } ],
            "double" => [ 3.1415927 ],
            "integer" => [ 100 ],
            "object" => [ (object)[ "hello, world" ] ],
        ];
    }
}
