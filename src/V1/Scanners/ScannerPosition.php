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
 * @package   TextParser\V1\Scanners
 * @author    Stuart Herbert <stuherbert@ganbarodigital.com>
 * @copyright 2016-present Ganbaro Digital Ltd www.ganbarodigital.com
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://ganbarodigital.github.io/php-mv-text-parser
 */

namespace GanbaroDigital\TextParser\V1\Scanners;

/**
 * value object to track where we are in the input stream
 */
class ScannerPosition
{
    /**
     * which line of input are we on?
     *
     * the first line is line 1
     *
     * @var int
     */
    private $lineNo;

    /**
     * how far along $this->lineNo are we?
     *
     * the first character on the line is at offset 0
     *
     * @var int
     */
    private $lineOffset;

    /**
     * where is this position in the input stream?
     *
     * the first character of the input stream is at position 0
     *
     * @var int
     */
    private $streamPos;

    /**
     * create a new instance, to remember where we are
     *
     * @param int $lineNo
     *        which line of input are we on?
     * @param int $offset
     *        how far along $lineNo are we?
     * @param int $streamPos
     *        where is this position in the input stream?
     */
    public function __construct($lineNo, $offset, $streamPos)
    {
        $this->lineNo = $lineNo;
        $this->lineOffset = $offset;
        $this->streamPos = $streamPos;
    }

    /**
     * which line of input are we on?
     *
     * the first line of the input is line 1
     *
     * @return int
     */
    public function getLineNumber()
    {
        return $this->lineNo;
    }

    /**
     * how far along $this->getLineOffset() are we?
     *
     * the first character on the line is at offset 0
     *
     * @return in
     */
    public function getLineOffset()
    {
        return $this->lineOffset;
    }

    /**
     * where is this position in our input stream?
     *
     * the first character of the input stream is at position 0
     *
     * @return int
     */
    public function getStreamPosition()
    {
        return $this->streamPos;
    }
}
