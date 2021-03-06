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
 * @package   TextParser\V1\Terminals
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2015-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-text-parser
 */

namespace GanbaroDigitalTest\TextParser\V1\Terminals;

use GanbaroDigital\TextParser\V1\Grammars\TerminalRule;
use GanbaroDigital\TextParser\V1\Lexer\Lexeme;
use GanbaroDigital\TextParser\V1\Lexer\NoopAdjuster;
use GanbaroDigital\TextParser\V1\Scanners\ScannerPosition;
use GanbaroDigital\TextParser\V1\Scanners\StreamScanner;
use GanbaroDigital\TextParser\V1\Scanners\StringScanner;
use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
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

        $unit = $this->getUnitUnderTest();

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue(is_object($unit));
    }

    /**
     * @coversNothing
     */
    public function test_is_TerminalRule()
    {
        // ----------------------------------------------------------------
        // setup your test


        // ----------------------------------------------------------------
        // perform the change

        $unit = $this->getUnitUnderTest();

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(TerminalRule::class, $unit);
    }

    /**
     * @covers ::getBuildingBlocks
     */
    public function test_has_no_child_rules()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedBlocks = [];

        $unit = $this->getUnitUnderTest();

        // ----------------------------------------------------------------
        // perform the change

        $actualBlocks = $unit->getBuildingBlocks();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedBlocks, $actualBlocks);
    }

    /**
     * @covers ::getPseudoBNF
     */
    public function test_returns_pseudo_BNF()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedResult = $this->getExpectedPseudoBNF();
        $unit = $this->getUnitUnderTest();

        // ----------------------------------------------------------------
        // perform the change

        $actualResult = $unit->getPseudoBNF();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedResult, $actualResult);
    }

    protected function checkForMatches($text, $hasExpectedValue, $expectedParserMatch, $expectedValue, $remainingBytes = '100', $expectedEvaluator = null)
    {
        // ----------------------------------------------------------------
        // setup your test

        $language = [
            "unit" => $this->getUnitUnderTest()
        ];

        $expectedMatch = [
            "matched" => true,
            "hasValue" => true,
            "value" => new Lexeme("unit", $expectedParserMatch, $expectedEvaluator),
            "position" => new ScannerPosition(1, 0, 0),
        ];
        if (!$hasExpectedValue) {
            $expectedMatch['hasValue'] = false;
            $expectedMatch['value'] = null;
        }

        $expectedRemaining = substr($text, strlen($expectedParserMatch)) . $remainingBytes;
        $scanner = StreamScanner::newFromString($text . $remainingBytes, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        $actualMatch = $language['unit']->matchAgainst($language, 'unit', $scanner, new NoopAdjuster);
        $this->assertEquals($expectedMatch, $actualMatch);

        if ($hasExpectedValue) {
            $actualValue = $actualMatch['value']->evaluate();
        }
        if (strlen($remainingBytes) > 0) {
            $actualRemaining = $scanner->readRemainingBytes();
        }

        // ----------------------------------------------------------------
        // test the results

        if ($hasExpectedValue) {
            $this->assertEquals($expectedValue, $actualValue);
        }
        if (strlen($remainingBytes) > 0) {
            $this->assertEquals($expectedRemaining, $actualRemaining);
        }
    }

    /**
     * @covers ::matchAgainst
     * @dataProvider provideNonMatches
     */
    protected function checkForNonMatches($text, $remainingBytes = '100')
    {
        $language = [
            "unit" => $this->getUnitUnderTest()
        ];
        $expectedMatch = [
            "matched" => false,
            "position" => new ScannerPosition(1,0,0),
            "expected" => $language["unit"]
        ];

        $expectedRemaining = $text . $remainingBytes;
        $scanner = StreamScanner::newFromString($expectedRemaining, 'unit test');

        // ----------------------------------------------------------------
        // perform the change

        $actualMatch = $language['unit']->matchAgainst($language, 'unit', $scanner, new NoopAdjuster);
        $actualRemaining = $scanner->readRemainingBytes();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedMatch, $actualMatch);
        $this->assertEquals($expectedRemaining, $actualRemaining);
    }

    /**
     * @covers ::matchAgainst
     */
    public function test_matchAgainst_calls_adjuster_adjustAfterMatch()
    {
        // ----------------------------------------------------------------
        // setup your test

        // we only need one value to test against
        $matches = $this->provideMatches();
        $firstMatch = current($matches);
        $text = current($firstMatch);

        $language = [
            "unit" => $this->getUnitUnderTest()
        ];

        $scanner = StreamScanner::newFromString($text, 'unit test');
        $adjuster = new CallTrackingAdjuster();
        $expectedAdjustments = [
            "adjustAfterMatch"
        ];

        // ----------------------------------------------------------------
        // perform the change

        $language['unit']->matchAgainst($language, 'unit', $scanner, $adjuster);

        // ----------------------------------------------------------------
        // test the results

        $actualAdjustments = $adjuster->getCallsList();
        $this->assertEquals($expectedAdjustments, $actualAdjustments);
    }

    public function provideMatches()
    {
        // reuse our standard test set
        $dataset = getTerminalDataset();

        // here's the list of things that are supposed to match
        $requiredKeys = $this->getDatasetKeysToMatch();

        // send back the items that are supposed to match!
        $retval = [];
        foreach ($requiredKeys as $requiredKey) {
            $retval[$requiredKey] = $dataset[$requiredKey];
        }

        // all done
        return $retval;
    }

    public function provideNonMatches()
    {
        // reuse our standard test set
        $retval = getTerminalDataset();

        // here's the list of things that are supposed to match
        $keysToAvoid = $this->getDatasetKeysToMatch();

        // strip out the things that are supposed to match!
        foreach($keysToAvoid as $keyToAvoid) {
            unset($retval[$keyToAvoid]);
        }

        // all done
        return $retval;
    }

    abstract protected function getUnitUnderTest();
    abstract protected function getExpectedPseudoBNF();
    abstract protected function getDatasetKeysToMatch();
}
