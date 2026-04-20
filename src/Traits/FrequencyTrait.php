<?php

namespace Macocci7\PhpFrequencyTable\Traits;

trait FrequencyTrait
{
    /**
     * returns frequencies
     * @return  array<int|null>
     */
    public function getFrequencies(): array
    {
        $frequencies = [];
        foreach ($this->getClasses() as $class) {
            $frequencies[] = $this->getFrequency($this->data, $class);
        }
        return $frequencies;
    }

    /**
     * returns frequency of the specified class
     */
    public function getFrequency(mixed $data, mixed $class): int|null
    {
        if (
            !$this->isSettableData($data)
            || !$this->isSettableClass($class)
        ) {
            return null;
        }
        $count = 0;
        foreach ($data as $d) { // @phpstan-ignore-line
            if ($d >= $class['bottom'] && $d < $class['top']) { // @phpstan-ignore-line
                $count++;
            }
        }
        return $count;
    }

    /**
     * returns cumulative frequency of the specified (array) index
     */
    public function getCumulativeFrequency(mixed $frequencies, mixed $index): int|null
    {
        if (
            !$this->isSettableData($frequencies)
            || !is_int($index)
        ) {
            return null;
        }
        if (
            $index < 0
            || $index >= count($frequencies) // @phpstan-ignore-line
        ) {
            return null;
        }
        return array_sum(array_slice($frequencies, 0, $index + 1)); // @phpstan-ignore-line
    }

    /**
     * returns relative frequency
     */
    public function getRelativeFrequency(mixed $frequency): int|float|null
    {
        if (!$this->getTotal() || !(is_int($frequency))) {
            return null;
        }
        if ($frequency < 0 || $frequency > $this->getTotal()) {
            return null;
        }
        return $frequency / $this->getTotal();
    }

    /**
     * returns cumulative relative frequency
     */
    public function getCumulativeRelativeFrequency(
        mixed $frequencies,
        mixed $index,
    ): int|float|null {
        if (!$this->isSettableData($frequencies) || !is_int($index)) {
            return null;
        }
        if ($index < 0 || $index >= count($frequencies)) { // @phpstan-ignore-line
            return null;
        }
        $rf = [];
        foreach (array_slice($frequencies, 0, $index + 1) as $frequency) { // @phpstan-ignore-line
            $rf[] = $this->getRelativeFrequency($frequency);
        }
        return array_sum($rf);
    }
}
