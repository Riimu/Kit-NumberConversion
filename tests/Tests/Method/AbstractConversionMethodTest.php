<?php

namespace Tests\Method;

use Riimu\Kit\NumberConversion\NumberBase;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2013, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class AbstractConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException Riimu\Kit\NumberConversion\Method\ConversionException
     */
    public function testNumberConversionException()
    {
        $conv = $this->getMock('Riimu\Kit\NumberConversion\Method\AbstractConverter',
            null, [new NumberBase(2), new NumberBase(16)]);
        $conv->convertNumber(['1']);
    }

    /**
     * @expectedException Riimu\Kit\NumberConversion\Method\ConversionException
     */
    public function testFractionConversionException()
    {
        $conv = $this->getMock('Riimu\Kit\NumberConversion\Method\AbstractConverter',
            null, [new NumberBase(2), new NumberBase(16)]);
        $conv->convertFractions(['1']);
    }
}
