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
 * a reference to another grammar
 */
class Reference implements GrammarRule
{
    /**
     * the grammar that we are an alias for
     * @var string
     */
    private $buildingBlock;

    /**
     * create a new instance
     *
     * @param string $name
     *        the grammars that we are a reference for
     */
    public function __construct($name)
    {
        $this->buildingBlock = $name;
    }

    /**
     * return a (possibly empty) list of the grammars that this grammer
     * is built upon
     *
     * @return string[]
     */
    public function getBuildingBlocks()
    {
        return [$this->buildingBlock];
    }

    /**
     * describe this grammar using BNF-like syntax
     *
     * @return string
     */
    public function getPseudoBNF()
    {
        return $this->buildingBlock;
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
        $grammar = $grammars[$this->buildingBlock];
        return $grammar->matchAgainst($grammars, $lexemeName, $scanner, $adjuster);
    }
}
