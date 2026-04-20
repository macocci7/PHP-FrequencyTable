<?php

namespace Macocci7\PhpFrequencyTable\Traits;

trait DataTrait
{
    protected mixed $data = null;

    protected mixed $total = null;

    /**
     * sets data
     */
    public function setData(mixed $data): self
    {
        if ($this->isSettableData($data)) {
            $this->data = $data;
            $this->setTotal($this->getFrequencies());
        } else {
            $this->data = null;
            $this->total = null;
        }
        return $this;
    }

    /**
     * returns data
     * @return  array<int|string, int|float>|int|float|null
     */
    public function getData(int|string|null $key = null): array|int|float|null
    {
        // @phpstan-ignore-next-line
        return is_null($key) ? $this->data : ($this->data[$key] ?? null);
    }

    /**
     * returns data range
     */
    public function getDataRange(mixed $data): int|float|null
    {
        if (!$this->isSettableData($data)) {
            return null;
        }
        return $this->getMax($data) - $this->getMin($data);
    }

    /**
     * returns min value of the specified array
     */
    public function getMin(mixed $data): int|float|null
    {
        return $this->isSettableData($data) ? min($data) : null; // @phpstan-ignore-line
    }

    /**
     * returns max value of the specified array
     */
    public function getMax(mixed $data): int|float|null
    {
        return $this->isSettableData($data) ? max($data) : null; // @phpstan-ignore-line
    }

    /**
     * sets total of the specified data
     */
    public function setTotal(mixed $data): self
    {
        if ($this->isSettableData($data)) {
            $this->total = array_sum($data); // @phpstan-ignore-line
        } else {
            $this->total = null;
        }
        return $this;
    }

    /**
     * returns total
     */
    public function getTotal(): int|float|null
    {
        if (is_null($this->total) && $this->isSettableData($this->getData())) {
            $this->setTotal($this->getFrequencies());
        }
        return $this->total;
    }

    /**
     * returns mean value
     */
    public function getMean(): int|float|null
    {
        if (!$this->isSettableData($this->getData()) || !$this->getTotal()) {
            return null;
        }
        $fc = [];
        $classes = $this->getClasses();
        foreach ($this->getFrequencies() as $index => $frequency) {
            $fc[] = $this->getClassValue($classes[$index]) * $frequency;
        }
        return array_sum($fc) / $this->getTotal();
    }

    /**
     * returns mode
     */
    public function getMode(): int|float|null
    {
        $frequencies = $this->getFrequencies();
        if (0 === count($frequencies)) {
            return null;
        }
        $classes = $this->getClasses();
        $index = array_search(max($frequencies), $frequencies);
        return $this->getClassValue($classes[$index]);
    }

    /**
     * returns median
     */
    public function getMedian(mixed $param): int|float|null
    {
        if (!$this->isSettableData($param)) {
            return null;
        }
        $data = array_merge($param); // @phpstan-ignore-line
        $count = count($data);
        sort($data);
        if (1 === $count % 2) {
            return $data[(int) (($count + 1) / 2) - 1]; // @phpstan-ignore-line
        }
        return ($data[(int) ($count / 2) - 1] + $data[(int) ($count / 2)]) / 2;
    }
}
