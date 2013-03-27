<?php

namespace Tests\Method\Direct;

use Riimu\Kit\NumberConversion\Method\Direct\NoveltyConverter;
use Tests\Method\ConverterTestBase;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2013, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class NoveltyConverterTest extends ConverterTestBase
{
    use IntegerConstrainedTraitTester;

    protected $className = 'Riimu\Kit\NumberConversion\Method\Direct\NoveltyConverter';

    public function testInvalidBase()
    {
        $this->assertFalse(NoveltyConverter::convert('0', '0', '01'));
    }

    public function testEmptyConvertParamater()
    {
        $this->assertSame('0', NoveltyConverter::convert('', '01', '0124567'));
    }

    public function testMissingNumberValue()
    {
        $this->assertFalse(NoveltyConverter::convert('2', '01', '0124567'));
    }
}
