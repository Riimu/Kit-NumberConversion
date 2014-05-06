<?php

namespace Riimu\Kit\NumberConversion\Converter\Replace;

use Riimu\Kit\NumberConversion\Converter\IntegerConverter;
use Riimu\Kit\NumberConversion\Converter\FractionConverter;
use Riimu\Kit\NumberConversion\Converter\AbstractConverter;
use Riimu\Kit\NumberConversion\Converter\ConversionException;
use Riimu\Kit\NumberConversion\NumberBase;

/**
 * @author Riikka Kalliomäki <riikka.kalliomaki@gmail.com>
 * @copyright Copyright (c) 2013, Riikka Kalliomäki
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
abstract class AbstractReplaceConverter extends AbstractConverter
    implements IntegerConverter, FractionConverter
{
    private $root;
    private $sourceConverter;
    private $targetConverter;

    public function __construct(NumberBase $sourceBase, NumberBase $targetBase)
    {
        parent::__construct($sourceBase, $targetBase);

        $this->root = $sourceBase->findCommonRadixRoot($targetBase);
        $this->sourceConverter = null;
        $this->targetConverter = null;
    }

    public function setPrecision($precision)
    {
        ;
    }

    public function convertInteger(array $number)
    {
        return $this->convert($number, false);
    }

    public function convertFractions(array $number)
    {
        return $this->convert($number, true);
    }

    /**
     * Converts the number by replacing numbers via a common radix root.
     *
     * If a common root exists for both the source and target radix, then the
     * number can be converted by using convertByReplace() by converting it via
     * a number base with radix equal to the common root. Doing two replacement
     * conversion should still be faster in most cases than any other conversion
     * method. If no common root exists between the two number bases,
     * an exception will be thrown.
     *
     * @param array $number Number to covert with most significant digit last
     * @param boolean $fractions True if converting fractions, false if not
     * @return array The converted number with most significant digit last
     * @throws \InvalidArgumentException If no common root exists
     */
    public function convert(array $number, $fractions = false)
    {
        if (!$this->root) {
            throw new ConversionException('No common root exists');
        } elseif ($number === []) {
            return [$this->target->getDigit(0)];
        } elseif ($this->source->getRadix() == $this->target->getRadix()) {
            return $this->getDigits($this->getValues($number));
        } elseif ($this->root == min($this->source->getRadix(), $this->target->getRadix())) {
            return $this->replace($number, $fractions);
        }

        if ($this->sourceConverter === null) {
            $class = get_class($this);
            $rootBase = new NumberBase($this->root);

            $this->sourceConverter = new $class($this->source, $rootBase);
            $this->targetConverter = new $class($rootBase, $this->target);
        }

        return $this->targetConverter->replace(
            $this->sourceConverter->replace($number, $fractions),
            $fractions
        );
    }

    abstract protected function replace(array $number, $fractions);

    protected function zeroPad(array $number, $right, $zero = null)
    {
        $log = (int) log($this->target->getRadix(), $this->source->getRadix());

        if ($log > 1 && $pad = count($number) % $log) {
            $zero = $zero === null ? $this->source->getDigit(0) : $zero;
            $pad = count($number) + ($log - $pad);
            $number = array_pad($number, $pad * ($right ? +1: -1), $zero);
        }

        return $number;
    }

    protected function zeroTrim(array $number, $right, $zero = null)
    {
        $zero = $zero === null ? $this->target->getDigit(0) : $zero;

        while (($right ? end($number) : reset($number)) === $zero) {
            unset($number[key($number)]);
        }

        return empty($number) ? [$zero] : array_values($number);
    }
}