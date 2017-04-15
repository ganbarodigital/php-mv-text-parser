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

namespace GanbaroDigitalTest\TextParser\V1\Terminals\Meta;

use GanbaroDigital\TextParser\V1\Evaluators\CastToString;
use GanbaroDigital\TextParser\V1\Terminals\Meta\T_HEX_NUMBER;
use GanbaroDigitalTest\TextParser\V1\Terminals\BaseTestCase;

/**
 * @coversDefaultClass GanbaroDigital\TextParser\V1\Terminals\Meta\T_HEX_NUMBER
 */
class T_HEX_NUMBER_Test extends BaseTestCase
{
    protected function getUnitUnderTest()
    {
        return new T_HEX_NUMBER;
    }

    protected function getExpectedPseudoBNF()
    {
        return 'regex /^([-+]{0,1}([A-Fa-f0-9]{2})+)(?![0-9%])/';
    }

    protected function getDatasetKeysToMatch()
    {
        return [
            "hex_zero",
            "hex_15_lower",
            "hex_15_upper",
            "hex_255_lower",
            "hex_255_upper",
        ];
    }

    /**
     * @covers ::matchAgainst
     * @dataProvider provideMatches
     */
    public function test_matches_an_8bit_integer($text)
    {
        $this->checkForMatches($text, true, $text, $text, " not part of a number", new CastToString);
    }

    /**
     * @covers ::matchAgainst
     * @dataProvider provideNonMatches
     */
    public function test_does_not_match_anything_else($text)
    {
        $this->checkForNonMatches($text, " not part of a number");
    }
}
