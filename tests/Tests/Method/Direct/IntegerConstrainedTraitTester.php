<?php

namespace Tests\Method\Direct;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2013, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
trait IntegerConstrainedTraitTester
{
    /**
     * @expectedException Riimu\Kit\NumberConversion\Method\Direct\PossibleOverflowException
     */
    public function testIntegerConstraintment()
    {
        $conv = $this->getConverter(46341, 46342);
        $conv->convertNumber([1]);
    }
}