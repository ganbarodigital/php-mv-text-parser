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
class RegexToken implements TerminalRule
{
    /**
     * how many bytes should we read into our buffer before applying
     * the regex?
     *
     * this is the recommended default value
     *
     * @var integer
     */
    const DEFAULT_SCAN_LENGTH = 8;

    /**
     * the name of this token, according to our grammar
     *
     * @var string
     */
    private $tokenName;

    /**
     * the regex used to match this token
     *
     * @var string
     */
    private $tokenRegex;

    /**
     * how many bytes do we ask the scanner for?
     *
     * @var int
     */
    private $scanLength;

    /**
     * how do we evaluate our final value?
     *
     * @var callable|null
     */
    private $evaluator;

    /**
     * build a new token
     *
     * @param string $tokenRegex
     *        how do we find this token in the text that we are parsing?
     * @param int $scanLength
     *        how many characters should we ask the scanner for?
     * @param callable|null $evaluator
     *        how do we turn our result into a usable value?
     */
    public function __construct($tokenRegex, $scanLength = self::DEFAULT_SCAN_LENGTH, callable $evaluator = null)
    {
        $this->tokenRegex = $tokenRegex;
        $this->scanLength = $scanLength;
        $this->evaluator = $evaluator;
    }

    /**
     * what is the regex that we are going to attempt to match against?
     *
     * this is handy for testing that your token has set the correct regex
     *
     * @return string
     */
    public function getRegex()
    {
        return $this->tokenRegex;
    }

    /**
     * return a (possibly empty) list of the grammars that this grammer
     * is built upon
     *
     * @return GrammarRule[]
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
        return "regex " . $this->tokenRegex;
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
        // make any necessary changes to the input stream
        $adjuster->adjustBeforeStartPosition($scanner);

        // remember where we started from
        $startPos = $scanner->getPosition();

        // go and get some text
        $text = $scanner->readBytesAhead($this->scanLength);

        $matches = [];
        if (preg_match($this->tokenRegex, $text, $matches)) {
            // have we consumed anything from the scanner?
            if (empty($matches[0])) {
                // make any necessary changes to the input stream
                $adjuster->adjustAfterMatch($scanner);

                return [
                    'matched' => true,
                    'hasValue' => false,
                    'value' => null,
                    'position' => $startPos
                ];
            }

            // comsume from the stream
            $scanner->moveBytes(strlen($matches[0]));

            // make any necessary changes to the input stream
            $adjuster->adjustAfterMatch($scanner);

            return [
                'matched' => true,
                'hasValue' => true,
                'value' => new Lexeme($lexemeName, $matches[0], $this->evaluator),
                'position' => $startPos
            ];
        }

        // if we get here, then nothing matched
        return [
            'matched' => false,
            'position' => $startPos,
            'expected' => $this
        ];
    }

}
