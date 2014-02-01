<?php

namespace Riimu\Kit\NumberConversion\Method;

use Riimu\Kit\NumberConversion\NumberBase;

/**
 * Abstract conversion strategy that implements basic functionality.
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2013, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class AbstractConverter implements Converter
{
    protected $source;
    protected $target;

    public function __construct(NumberBase $sourceBase, NumberBase $targetBase)
    {
        $this->source = $sourceBase;
        $this->target = $targetBase;
    }

    public function convertNumber(array $number)
    {
        throw new ConversionException("This converter does not support number conversion");
    }

    public function convertFractions(array $number)
    {
        throw new ConversionException("This converter does not support fraction conversion");
    }

    /**
     * Canonizes the number and returns it's decimal values in source base.
     * @param array $number Digits of the number
     * @return array Decimal values for the digits in the number
     */
    protected function getDecimals(array $number)
    {
        return empty($number) ? [0] : $this->source->getDecimals($number);
    }

    /**
     * Canonizes the number and returns the digits for the decimal values in target base.
     * @param array $number Decimal values to convert into digits
     * @return array Digits of the number based on the decimal values.
     */
    protected function getDigits(array $number)
    {
        return $this->target->getDigits(empty($number) ? [0] : $number);
    }
}
