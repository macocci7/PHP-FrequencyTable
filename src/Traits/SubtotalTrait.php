<?php

namespace Macocci7\PhpFrequencyTable\Traits;

trait SubtotalTrait
{
    /**
     * returns all subtotals of each class
     * @return  array<int, int|float|null>
     */
    public function getSubtotals(): array
    {
        $subtotals = [];
        foreach ($this->getClasses() as $class) {
            $subtotals[] = $this->getSubtotal($class);
        }
        return $subtotals;
    }

    /**
     * returns subtotal of the class
     * @param   array<string, int|float>    $class
     */
    protected function getSubtotal(array $class): int|float|null
    {
        if (!$this->isSettableClass($class)) {
            return null;
        }
        $subtotal = 0;
        foreach ($this->getData() as $value) {
            if ($class['bottom'] <= $value and $value < $class['top']) {
                $subtotal += $value;
            }
        }
        return $subtotal;
    }

    /**
     * returns relative subtotals
     * @return  array<int, int|float|null>
     */
    public function getRelativeSubtotals(): array
    {
        $relativeSubtotals = [];
        $total = array_sum($this->getData());
        foreach ($this->getSubtotals() as $subtotal) {
            $relativeSubtotals[] = $this->isNumber($total)
                ? ($total === 0 ? 0 : $subtotal / $total)
                : null;
        }
        return $relativeSubtotals;
    }

    /**
     * returns relative cululative subtotals
     * @return  array<int, int|float|null>
     */
    public function getCumulativeRelativeSubtotals(): array
    {
        $relativeCumulativeSubtotals = [];
        $sum = 0;
        foreach ($this->getRelativeSubtotals() as $relativeSubtotal) {
            $sum += $relativeSubtotal;
            $relativeCumulativeSubtotals[] = $sum;
        }
        return $relativeCumulativeSubtotals;
    }
}
