<?php

namespace Macocci7\PhpFrequencyTable\Traits;

trait ClassTrait
{
    protected mixed $classRange = null;

    protected bool $isReverseClasses = false;

    /**
     * sets class range
     */
    public function setClassRange(mixed $classRange = null): self
    {
        if ($this->isSettableClassRange($classRange)) {
            $this->classRange = $classRange;
        } else {
            $this->classRange = null;
        }
        return $this;
    }

    /**
     * sets list of classes in reverse order
     */
    public function reverseClasses(): self
    {
        $this->isReverseClasses = !$this->isReverseClasses;
        return $this;
    }

    /**
     * returns class range
     */
    public function getClassRange(): mixed
    {
        return $this->classRange;
    }

    /**
     * returns classes
     * @return  list<array<string, int|float>>
     */
    public function getClasses(): array
    {
        if (
            !$this->isSettableData($this->data)
            || !$this->isSettableClassRange($this->classRange)
        ) {
            return [];
        }
        $min = min($this->data); // @phpstan-ignore-line
        $max = max($this->data); // @phpstan-ignore-line
        $bottomStart = ((int) ($min / $this->classRange)) * $this->classRange;
        $topEnd = (1 + (int) ($max / $this->classRange)) * $this->classRange;
        $class = [];
        for ($bottom = $bottomStart; $bottom < $topEnd; $bottom += $this->classRange) {
            $class[] = [
                'bottom' => $bottom,
                'top' => $bottom + $this->classRange,
            ];
        }
        if ($this->isReverseClasses) {
            rsort($class);
        }
        return $class;
    }

    /**
     * returns class value
     */
    public function getClassValue(mixed $class): int|float|null
    {
        if (!$this->isSettableClass($class)) {
            return null;
        }
        return ($class['bottom'] + $class['top']) / 2; // @phpstan-ignore-line
    }

    /**
     * returns the class the median belongs
     * @return  array<string, int|float>|null
     */
    public function getMedianClass(): ?array
    {
        if (!$this->isSettableData($this->getData())) {
            return null;
        }
        $median = $this->getMedian($this->getData());
        foreach ($this->getClasses() as $index => $class) {
            if ($median >= $class['bottom'] && $median < $class['top']) {
                return array_merge(['index' => $index], $class);
            }
        }
        return null;
    }
}
