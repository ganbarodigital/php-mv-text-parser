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

use ArrayAccess;

class Lexeme implements ArrayAccess
{
    public $name;
    public $value;
    public $evaluator;

    public function __construct($name, $value, callable $evaluator = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->evaluator = $evaluator;
    }

    public function evaluate()
    {
        // shorthand
        $evaluator = $this->evaluator;

        // step 1 - do we have a value that needs evaluating?
        $value = $this->value;
        if ($value instanceof Lexeme || $value instanceof Lexemes) {
            $value = $value->evaluate();
        }

        // step 2 - do we need to evaluate further?
        if (is_callable($evaluator)) {
            return $evaluator($value);
        }

        // if we get here, then there's nothing left to do
        return $value;
    }

    public function offsetExists($offset)
    {
        if (!$this->canUseValueAsArray()) {
            return false;
        }

        return isset($this->value[$offset]);
    }

    public function offsetGet($offset)
    {
        if (!$this->canUseValueAsArray()) {
            return null;
        }

        return $this->value[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (!$this->canUseValueAsArray()) {
            $this->value = $value;
            return false;
        }

        $this->value[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        if (!$this->canUseValueAsArray()) {
            $this->value = null;
            return false;
        }

        unset($this->value[$offset]);
    }

    private function canUseValueAsArray()
    {
        if (is_array($this->value)) {
            return true;
        }
        if ($this->value instanceof ArrayAccess) {
            return true;
        }

        return false;
    }
}
