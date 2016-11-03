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
 * @package   TextParser\V1\Tokens
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-text-parser
 */

namespace GanbaroDigitalTest\TextParser\V1\Tokens\Lazy;

use GanbaroDigital\TextParser\V1\Tokens\Lazy\T_PERIOD;
use GanbaroDigital\TextParser\V1\Grammars\TerminalRule;
use GanbaroDigital\TextParser\V1\Lexer\Lexeme;
use GanbaroDigital\TextParser\V1\Lexer\NoopAdjuster;
use GanbaroDigital\TextParser\V1\Scanners\ScannerPosition;
use GanbaroDigital\TextParser\V1\Scanners\StreamScanner;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\TextParser\V1\Tokens\Lazy\T_PERIOD
 */
class T_PERIOD_Test extends PHPUnit_Framework_TestCase
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

        $unit = new T_PERIOD;

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue(is_object($unit));
    }

    /**
     * @covers ::__construct
     */
    public function test_is_TerminalRule()
    {
        // ----------------------------------------------------------------
        // setup your test


        // ----------------------------------------------------------------
        // perform the change

        $unit = new T_PERIOD;

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(TerminalRule::class, $unit);
    }

    /**
     * @coversNothing
     * @dataProvider provideMatches
     */
    public function test_matches_a_period_character($text)
    {
        // ----------------------------------------------------------------
        // setup your test

        $language = [
            'unit' => new T_PERIOD
        ];

        $expectedMatch = [
            "matched" => true,
            "hasValue" => true,
            "value" => new Lexeme('unit', '.'),
            "position" => new ScannerPosition(1,0,0)
        ];
        $expectedRemainder = substr($text, 1) . '100';
        $scanner = StreamScanner::newFrom($text . '100', 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        $actualMatch = $language['unit']->matchAgainst($language, 'unit', $scanner, new NoopAdjuster);
        $actualRemainder = $scanner->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedMatch, $actualMatch);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    /**
     * @coversNothing
     * @dataProvider provideNonMatches
     */
    public function test_does_not_match_anything_else($text)
    {
        // ----------------------------------------------------------------
        // setup your test

        $language = [
            'unit' => new T_PERIOD
        ];

        $expectedMatch = [
            "matched" => false,
            "position" => new ScannerPosition(1,0,0),
            "expected" => $language['unit']
        ];
        $expectedRemainder = $text . '100';
        $scanner = StreamScanner::newFrom($text . '100', 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        $actualMatch = $language['unit']->matchAgainst($language, 'unit', $scanner, new NoopAdjuster);
        $actualRemainder = $scanner->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedMatch, $actualMatch);
        $this->assertEquals($expectedRemainder, $actualRemainder);
    }

    public function provideMatches()
    {
        // reuse our standard test set
        $dataset = getTerminalDataset();

        // send back the items that are supposed to match!
        $retval = [
            '1_period' => $dataset['1_period'],
            '2_period' => $dataset['2_period'],
            '3_period' => $dataset['3_period'],
            '4_period' => $dataset['4_period'],
            '10_period' => $dataset['10_period'],
        ];

        // all done
        return $retval;
    }

    public function provideNonMatches()
    {
        // reuse our standard test set
        $retval = getTerminalDataset();

        // strip out the things that are supposed to match!
        unset($retval['1_period']);
        unset($retval['2_period']);
        unset($retval['3_period']);
        unset($retval['4_period']);
        unset($retval['10_period']);

        // all done
        return $retval;
    }

}
