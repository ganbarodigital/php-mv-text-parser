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
 * @package   TextParser\V1\Lexer
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-text-parser
 */

namespace GanbaroDigital\TextParser\V1\Lexer;

use GanbaroDigital\TextParser\V1\Grammars\GrammarRule;
use GanbaroDigital\TextParser\V1\Scanners\Scanner;

/**
 * default LexAdjuster when we do not want to adjust anything in the input
 * stream
 *
 * we use this to avoid littering our Grammar::matchAgainst() methods with
 * `if` statements
 */
class NoopAdjuster implements LexAdjuster
{
    /**
     * make any desired changes to the input stream before our grammar rule
     * makes a note of the input stream's current position
     *
     * @param  Scanner $scanner
     *         the scanner we are lexing against
     * @return void
     */
    public function adjustBeforeStartPosition(Scanner $scanner)
    {
        // do nothing
    }

    /**
     * make any desired changes to the input stream after our grammar rule
     * has consumed its match from the input stream
     *
     * @param  Scanner $scanner
     *         the scanner we are lexing against
     * @param  GrammarRule $grammar
     *         the rule that matched
     * @param  bool $hasValue
     *         did the match produce a value?
     * @param  mixed $value
     *         the value that matched (or NULL if $hasValue is false)
     * @return void
     */
    public function adjustAfterMatch(Scanner $scanner, GrammarRule $grammar, $hasValue, $value)
    {
        // do nothing
    }
}
