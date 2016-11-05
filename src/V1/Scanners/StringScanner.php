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

use InvalidArgumentException;

/**
 * a scanner that reads from a PHP string
 */
class StringScanner implements Scanner
{
    /**
     * our string that we're working along
     *
     * @var resource
     */
    private $input;

    /**
     * how long is our input?
     *
     * this saves us calculating it over and over
     *
     * @var int
     */
    private $inputLen;

    /**
     * what is this input called?
     *
     * @var string
     */
    private $label;

    /**
     * keep track of which input line number we're currently on
     *
     * @var integer
     */
    protected $lineNo = 1;

    /**
     * keep track of where on $this->lineNo we currently are
     *
     * @var integer
     */
    protected $lineOffset = 0;

    /**
     * keep track of where in the input we currently are
     *
     * @var integer
     */
    protected $inputPosition = 0;

    /**
     * which line did we start scanning from?
     *
     * @var int
     */
    private $startLinePosition;

    /**
     * how far alone $this->startLinePosition did we start scanning from?
     *
     * @var int
     */
    private $startLineOffset;

    /**
     * how far in this string did we start scanning from?
     *
     * @var int
     */
    private $startInputPosition;

    /**
     * how many spaces does a tab character take up?
     *
     * we use this to calculate the line position when we encounter tab
     * characters in the stream
     *
     * @var integer
     */
    private $tabSize = 8;

    /**
     * our constructor
     *
     * `$lineNo` and `$lineOffset` are used to tell us where `$input`
     * currently is. We make no attempt to move the position of `$input`.
     *
     * @param string $input
     *        the string that we're going to use as input to our lexer
     * @param string $label
     *        what is this input called?
     * @param int $lineNo
     *        what line number are we starting from?
     * @param int $lineOffset
     *        how far along $lineNo are we starting from?
     */
    public function __construct($input, $label, $lineNo = 1, $lineOffset = 0, $tabSize = 8)
    {
        if (!is_string($input)) {
            throw new InvalidArgumentException('$input must be a PHP resource');
        }

        $this->input = $input;
        $this->inputLen = strlen($input);
        $this->lineNo = $lineNo;
        $this->lineOffset = $lineOffset;
        $this->inputPosition = 0;

        // remember where we started from, in case anyone wants to know
        $this->startLineNo = $lineNo;
        $this->startLineOffset = $lineOffset;
        $this->startInputPosition = $this->inputPosition;

        $this->label = $label;
        $this->tabSize = $tabSize;
    }

    /**
     * read $size amount of bytes
     *
     * a read will change your position in the input stream
     *
     * @param  int $size
     *         the amount of bytes to read from the scanner
     * @return string
     *         the bytes that have been read
     */
    public function readBytes($size)
    {
        // make sure we are not going beyond the end of the input
        if ($this->inputPosition >= $this->inputLen) {
            return "";
        }

        // get the bytes
        $retval = substr($this->input, $this->inputPosition, $size);

        // workout where we now are
        $this->updatePositionFrom($retval);

        // all done
        return $retval;
    }

    /**
     * read all of the bytes that are left in the input stream
     *
     * @return string
     *         the bytes that have been read
     */
    public function readRemainingBytes()
    {
        // make sure we are not going beyond the end of the input
        if ($this->inputPosition >= $this->inputLen) {
            return "";
        }

        // here we go
        $retval = substr($this->input, $this->inputPosition);

        // workout where we now are
        // is this pointless?
        $this->updatePositionFrom($retval);

        // all done
        return $retval;
    }

    /**
     * read $size amount of bytes, but do NOT move our current position
     * in the input stream
     *
     * @param  int $size
     *         the amount of bytes to read from the scanner
     * @return string
     *         the bytes that have been read
     */
    public function readBytesAhead($size)
    {
        // make sure we are not going beyond the end of the input
        if ($this->inputPosition >= $this->inputLen) {
            return "";
        }

        // get the bytes
        $retval = substr($this->input, $this->inputPosition, $size);

        // all done
        return $retval;
    }

    /**
     * move $amount bytes in the input stream
     *
     * @param  int $amount
     *         the amount to move (can be positive or negative)
     * @return void
     */
    public function moveBytes($amount)
    {
        $this->readBytes($amount);
    }

    /**
     * calculate our new position in the input stream, by looking at what
     * has been read from the stream
     *
     * @param  string $bytesRead
     *         the bytes that have been read
     * @return void
     */
    protected function updatePositionFrom($bytesRead)
    {
        // shorthand
        $bytesCount = strlen($bytesRead);

        // move our input position
        $this->inputPosition += $bytesCount;

        // have we moved across any lines?
        $matches = [];
        if (preg_match_all("/\n/", $bytesRead, $matches, PREG_OFFSET_CAPTURE)) {
            // yes we have
            // this is the number of lines we have crossed
            $this->lineNo += count($matches[0]);
            $this->lineOffset = 0;

            $bytesRead = substr($bytesRead, end($matches[0])[1] + 1);
        }

        // expand any tab stops that we've read on the line we're now on
        if (preg_match_all("/\t/", $bytesRead, $matches, PREG_OFFSET_CAPTURE)) {
            // we need to keep track of where we saw the tabs on the current line
            $lastTabStop = 0;

            // we need to expand each tab stop one at a time, to be accurate
            // in our calculation
            foreach($matches[0] as $match) {
                $lenToTab = $match[1] - $lastTabStop;
                $this->lineOffset += $lenToTab;
                $this->lineOffset += ($this->tabSize - ($this->lineOffset % $this->tabSize));
                $lastTabStop = $match[1];
            }

            // don't forget to add on any remaining bytes after the last
            // tab stop!
            $this->lineOffset += strlen($bytesRead) - $match[1] - 1;
        }
        else {
            // no tab stops to worry about
            $this->lineOffset += strlen($bytesRead);
        }
    }

    /**
     * consume whitespace characters
     *
     * this is a separate method because it needs to be line-aware
     * we don't want to do that anywhere else, because it's CPU-intensive
     *
     * @return bool
     *         TRUE if the input stream position has moved
     *         FALSE otherwise
     */
    public function movePastWhitespaceOnCurrentLine()
    {
        // we need to keep track of whether the input has moved or not
        $startPos = $this->inputPosition;

        // read from the stream until we hit EOF
        while ($this->inputPosition < $this->inputLen) {
            $c = substr($this->input, $this->inputPosition, 1);

            // do we have whitespace?
            if ($c === ' ') {
                $this->lineOffset++;
                $this->inputPosition++;
            }
            // is this a tab character?
            else if ($c === "\t") {
                $this->lineOffset += ($this->tabSize - ($this->lineOffset % $this->tabSize));
                $this->inputPosition++;
            }
            else {
                // no, so we're done here
                break;
            }
        }

        // all done
        return ($startPos <> $this->inputPosition);
    }

    /**
     * consume whitespace characters
     *
     * this is a separate method because it needs to be line-aware
     * we don't want to do that anywhere else, because it's CPU-intensive
     *
     * @return bool
     *         TRUE if the input stream position has moved
     *         FALSE otherwise
     */
    public function movePastWhitespace()
    {
        // we need to keep track of whether the stream has moved or not
        $startPos = $this->inputPosition;

        // read from the stream until we hit EOF
        while ($this->inputPosition < $this->inputLen) {
            $c = substr($this->input, $this->inputPosition, 1);

            // do we have whitespace?
            if ($c === ' ' || $c === "\r") {
                $this->lineOffset++;
                $this->inputPosition++;
            }
            // is this a tab character?
            else if ($c === "\t") {
                $this->lineOffset += ($this->tabSize - ($this->lineOffset % $this->tabSize));
                $this->inputPosition++;
            }
            // have we moved onto the next line?
            else if ($c === "\n") {
                $this->lineNo++;
                $this->lineOffset = 0;
                $this->inputPosition++;
            }
            else {
                // no, so we're done here
                break;
            }
        }

        // all done
        return ($startPos <> $this->inputPosition);
    }

    /**
     * are we at the end of the input?
     *
     * @return bool
     *         TRUE if we are at the end
     *         FALSE otherwise
     */
    public function isAtEndOfInput()
    {
        return ($this->inputPosition >= $this->inputLen);
    }

    /**
     * where are we in the current input stream?
     *
     * @return ScannerPosition
     */
    public function getPosition()
    {
        return new ScannerPosition($this->lineNo, $this->lineOffset, $this->inputPosition);
    }

    /**
     * move to a given position in the input stream
     *
     * @param Position $position
     *        the position to move to
     */
    public function setPosition(ScannerPosition $position)
    {
        // keep track of where we now are
        $this->lineNo = $position->getLineNumber();
        $this->lineOffset = $position->getLineOffset();
        $this->inputPosition = min($position->getStreamPosition(), $this->inputLen);
    }

    /**
     * return the whole input we're scanning as a string
     *
     * this will not change the scanner's position in the input stream
     *
     * @return string
     */
    public function __toString()
    {
        return $this->input;
    }

    /**
     * return all of the input from the current scanning position
     *
     * this will not change the scanner's position in the input stream
     *
     * @return string
     */
    public function readAheadRemainingBytes()
    {
        return substr($this->input, $this->inputPosition);
    }

    /**
     * what is this scanner (or its underlying stream) called?
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * returns a ScannerPosition that represents the start position of
     * this stream
     *
     * @return ScannerPosition
     */
    public function getStartPosition()
    {
        return new ScannerPosition(
            $this->startLineNo,
            $this->startLineOffset,
            $this->startInputPosition
        );
    }
}
