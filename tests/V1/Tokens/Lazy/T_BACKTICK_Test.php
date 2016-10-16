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

use GanbaroDigital\TextParser\V1\Tokens\Lazy\T_BACKTICK;
use GanbaroDigital\TextParser\V1\Grammars\Token;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass GanbaroDigital\TextParser\V1\Tokens\Lazy\T_BACKTICK
 */
class T_BACKTICK_Test extends PHPUnit_Framework_TestCase
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

        $unit = new T_BACKTICK;

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue(is_object($unit));
    }

    /**
     * @covers ::__construct
     */
    public function test_is_Token()
    {
        // ----------------------------------------------------------------
        // setup your test


        // ----------------------------------------------------------------
        // perform the change

        $unit = new T_BACKTICK;

        // ----------------------------------------------------------------
        // test the results

        $this->assertInstanceOf(Token::class, $unit);
    }

    /**
     * @covers ::getName
     */
    public function test_can_get_token_name()
    {
        // ----------------------------------------------------------------
        // setup your test

        $expectedName = 'T_BACKTICK';
        $unit = new T_BACKTICK;

        // ----------------------------------------------------------------
        // perform the change

        $actualName = $unit->getName();

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedName, $actualName);
    }

    /**
     * @covers ::getRegex
     */
    public function test_can_get_token_regex()
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new T_BACKTICK;

        // ----------------------------------------------------------------
        // perform the change

        $actualRegex = $unit->getRegex();

        // ----------------------------------------------------------------
        // test the results

        $this->assertTrue(is_string($actualRegex));
        $this->assertTrue(strlen(trim($actualRegex)) > 0);
    }

    /**
     * @covers ::__construct
     * @covers ::getRegex
     */
    public function test_defines_a_valid_regex()
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new T_BACKTICK;
        $actualRegex = $unit->getRegex();

        // ----------------------------------------------------------------
        // perform the change

        $isRegex = @preg_match($actualRegex, '');

        // ----------------------------------------------------------------
        // test the results

        $this->assertNotFalse($isRegex);
    }

    /**
     * @coversNothing
     * @dataProvider provideMatches
     */
    public function test_regex_matches_a_backtick($text)
    {
        // ----------------------------------------------------------------
        // setup your test

        $text .= '100';
        $unit = new T_BACKTICK;
        $expectedMatches = [ '`' ];

        // ----------------------------------------------------------------
        // perform the change

        $actualMatches = [];
        preg_match($unit->getRegex(), $text, $actualMatches);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedMatches, $actualMatches);
    }

    /**
     * @coversNothing
     * @dataProvider provideNonMatches
     */
    public function test_regex_does_not_match_anything_else($text)
    {
        // ----------------------------------------------------------------
        // setup your test

        $unit = new T_BACKTICK;
        $expectedMatches = [ ];

        // ----------------------------------------------------------------
        // perform the change

        $actualMatches = [];
        preg_match($unit->getRegex(), $text, $actualMatches);

        // ----------------------------------------------------------------
        // test the results

        $this->assertEquals($expectedMatches, $actualMatches);
    }

    public function provideMatches()
    {
        // reuse our standard test set
        $dataset = getTokenDataset();

        // send back the items that are supposed to match!
        $retval = [
            '1_backtick' => $dataset['1_backtick'],
            '2_backtick' => $dataset['2_backtick'],
            '3_backtick' => $dataset['3_backtick'],
            '4_backtick' => $dataset['4_backtick'],
            '10_backtick' => $dataset['10_backtick'],
        ];

        // all done
        return $retval;
    }

    public function provideNonMatches()
    {
        // reuse our standard test set
        $retval = getTokenDataset();

        // strip out the things that are supposed to match!
        unset($retval['1_backtick']);
        unset($retval['2_backtick']);
        unset($retval['3_backtick']);
        unset($retval['4_backtick']);
        unset($retval['10_backtick']);

        // all done
        return $retval;
    }

}
