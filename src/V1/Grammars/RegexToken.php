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

use GanbaroDigital\TextParser\V1\Lexer\Lexeme;

/**
 * an individual token in our overall grammar
 *
 * tokens are ultimately how we move through the text
 */
class RegexToken implements Grammar
{
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
     * how do we evaluate our final value?
     *
     * @var callable|null
     */
    private $evaluator;

    /**
     * build a new token
     *
     * @param string $tokenName
     *        what does our grammar call this token?
     * @param string $tokenRegex
     *        how do we find this token in the text that we are parsing?
     */
    public function __construct($tokenName, $tokenRegex, callable $evaluator = null)
    {
        $this->tokenName = $tokenName;
        $this->tokenRegex = $tokenRegex;
        $this->evaluator = $evaluator;
    }

    public function getRegex()
    {
        return $this->tokenRegex;
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
        return "regex " . $this->tokenRegex;
    }

    /**
     * does this grammar match against the provided text?
     *
     * @param  Grammars[] $grammars
     *         our dictionary of grammars
     * @param  string $lexemeName
     *         the name to assign to any lexeme we create
     * @param  string $text
     *         the text to match
     * @return array
     *         details about what happened
     */
    public function matchAgainst($grammars, $lexemeName, $text)
    {
        $matches = [];
        if (preg_match($this->tokenRegex, $text, $matches)) {
            if (empty($matches[0])) {
                return [
                    'matched' => true,
                    'hasValue' => false,
                    'value' => null,
                    'remaining' => $text,
                ];
            }

            return [
                'matched' => true,
                'hasValue' => true,
                'value' => new Lexeme($lexemeName, $matches[0], $this->evaluator),
                'remaining' => substr($text, strlen($matches[0]))
            ];
        }

        return [
            'matched' => false
        ];
    }

}
