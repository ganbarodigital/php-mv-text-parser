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
 * @package   TextParser\V1\Grammars
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-text-parser
 */

namespace GanbaroDigital\TextParser\V1\Grammars;

use GanbaroDigital\TextParser\V1\Lexer\LexAdjuster;
use GanbaroDigital\TextParser\V1\Lexer\Lexeme;
use GanbaroDigital\TextParser\V1\Scanners\Scanner;

/**
 * an individual token in our overall grammar
 *
 * tokens are ultimately how we move through the text
 */
class PrefixToken implements TerminalRule
{
    /**
     * the string we're looking for
     *
     * @var string
     */
    private $prefix;

    /**
     * how many bytes we're looking for
     *
     * this saves us calculating it multiple times
     *
     * @var int
     */
    private $prefixLen;

    /**
     * how do we evaluate our final value?
     *
     * @var callable|null
     */
    private $evaluator;

    /**
     * build a new token
     *
     * @param string $prefix
     *        how do we find this token in the text that we are parsing?
     */
    public function __construct($prefix, callable $evaluator = null)
    {
        $this->prefix = $prefix;
        $this->prefixLen = strlen($prefix);
        $this->evaluator = $evaluator;
    }

    /**
     * what is the prefix that we are going to attempt to match against?
     *
     * this is handy for testing that your token has set the correct prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * return a (possibly empty) list of the grammars that this grammer
     * is built upon
     *
     * @return Grammar[]
     */
    public function getBuildingBlocks()
    {
        // tokens are *always* terminal symbols
        return [];
    }

    /**
     * describe this grammar using BNF-like syntax
     *
     * @return string
     */
    public function getPseudoBNF()
    {
        return $this->prefix;
    }

    /**
     * does this grammar match against the provided text?
     *
     * @param  GrammarRule[] $grammars
     *         our dictionary of grammars
     * @param  string $lexemeName
     *         the name to assign to any lexeme we create
     * @param  Scanner $scanner
     *         the text to match
     * @param  LexAdjuster $adjuster
     *         modify the lexer behaviour to suit
     * @return array
     *         details about what happened
     */
    public function matchAgainst($grammars, $lexemeName, Scanner $scanner, LexAdjuster $adjuster)
    {
        // keep track of where we are
        $startPos = $scanner->getPosition();

        // the text we want to check
        $text = $scanner->readBytesAhead($this->prefixLen);

        // did we get a match?
        //
        // this is faster than using PHP's !== operator on two strings
        if (strcmp($text, $this->prefix) != 0) {
            // better luck next time
            return [
                'matched' => false,
                'position' => $startPos,
                'expected' => $this
            ];
        }

        // a match!
        $scanner->moveBytes($this->prefixLen);

        // make any necessary changes to the input stream
        $adjuster->adjustAfterMatch($scanner, $this, true, $this->prefix);

        // tell the caller what we found :)
        return [
            'matched' => true,
            'hasValue' => true,
            'value' => new Lexeme($lexemeName, $this->prefix, $this->evaluator),
            'position' => $startPos
        ];
    }
}
