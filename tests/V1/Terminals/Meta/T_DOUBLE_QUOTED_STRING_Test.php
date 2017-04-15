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

use GanbaroDigital\TextParser\V1\Terminals\Meta\T_DOUBLE_QUOTED_STRING;
use GanbaroDigitalTest\TextParser\V1\Terminals\BaseTestCase;

/**
 * @coversDefaultClass GanbaroDigital\TextParser\V1\Terminals\Meta\T_DOUBLE_QUOTED_STRING
 */
class T_DOUBLE_QUOTED_STRING_Test extends BaseTestCase
{
    protected function getUnitUnderTest()
    {
        return new T_DOUBLE_QUOTED_STRING;
    }

    protected function getExpectedPseudoBNF()
    {
        return 'regex /^"(?:[^"]|\|\")*"/';
    }

    protected function getDatasetKeysToMatch()
    {
        return [
            'double_quoted_string',
            '2_double_quote',
            '3_double_quote',
            '4_double_quote',
            '10_double_quote',
        ];
    }

    /**
     * @covers ::matchAgainst
     */
    public function test_matches_a_double_quoted_string()
    {
        $text = '"@100" 150';

        $this->checkForMatches($text, true, '"@100"', '"@100"');
    }

    /**
     * @covers ::matchAgainst
     */
    public function test_ignores_a_second_double_quoted_string()
    {
        $text = '"@100", "@101"';
        $this->checkForMatches($text, true, '"@100"', '"@100"');
    }

    /**
     * @covers ::matchAgainst
     * @dataProvider provideNonMatches
     */
    public function test_does_not_match_anything_else($text)
    {
        $this->checkForNonMatches($text);
    }
}
