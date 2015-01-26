<?php

namespace Riimu\Kit\BaseConversion\DigitList;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2015, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class AbstractDigitList implements DigitList
{
    /** @var array List of digits */
    protected $digits;

    /** @var integer[] List of digit values */
    protected $valueMap;

    /** @var boolean Tells if the digits are case sensitive or not */
    protected $caseSensitive;

    /** @var boolean Tells if the numeral system can be written using strings */
    protected $stringConflict;

    public function hasStringConflict()
    {
        return $this->stringConflict;
    }

    public function isCaseSensitive()
    {
        return $this->caseSensitive;
    }

    protected function setValueMap(array $map)
    {
        $lower = array_change_key_case($map);
        $this->caseSensitive = count($lower) !== count($map);
        $this->valueMap = $this->caseSensitive ? $map : $lower;
    }

    public function getDigits()
    {
        return $this->digits;
    }

    public function getDigit($value)
    {
        $value = (int) $value;

        if (!isset($this->digits[$value])) {
            throw new \InvalidArgumentException('Invalid digit value');
        }

        return $this->digits[$value];
    }

    public function getValue($digit)
    {
        if (!is_scalar($digit)) {
            throw new \InvalidArgumentException('Invalid digit');
        }

        $digit = $this->caseSensitive ? (string) $digit : strtolower($digit);

        if (!isset($this->valueMap[$digit])) {
            throw new \InvalidArgumentException('Invalid digit');
        }

        return $this->valueMap[$digit];
    }

    public function count()
    {
        return count($this->digits);
    }
}