<?php

namespace Macocci7\PhpFrequencyTable\Traits;

trait QuartileTrait
{
    /**
     * returns the first quartile
     */
    public function getFirstQuartile(mixed $data): int|float|null
    {
        if (!$this->isSettableData($data)) {
            return null;
        }
        $a = array_merge($data); // @phpstan-ignore-line
        sort($a);
        $count = count($a);
        if (1 === $count) {
            return $data[0]; // @phpstan-ignore-line
        }
        $chunkLength = (0 === $count % 2)
                     ? (int) ($count / 2)       // in case even
                     : (int) (($count - 1) / 2) // in case odd
                     ;
        $forward = array_slice($a, 0, $chunkLength);
        return $this->getMedian($forward);
    }

    /**
     * returns the third quartile
     */
    public function getThirdQuartile(mixed $data): int|float|null
    {
        if (!$this->isSettableData($data)) {
            return null;
        }
        $a = array_merge($data); // @phpstan-ignore-line
        sort($a);
        $count = count($a);
        if (1 === $count) {
            return $data[0]; // @phpstan-ignore-line
        }
        $offset = (0 === $count % 2)
                  ? (int) ($count / 2)          // in case even
                  : (int) (($count + 1) / 2)    // in case odd
                  ;
        $backward = array_slice($a, $offset);
        return $this->getMedian($backward);
    }

    /**
     * returns the inter quartile range
     */
    public function getInterQuartileRange(mixed $data): int|float|null
    {
        if (!$this->isSettableData($data)) {
            return null;
        }
        return $this->getThirdQuartile($data) - $this->getFirstQuartile($data);
    }

    /**
     * returns the quartile deviation
     */
    public function getQuartileDeviation(mixed $data): int|float|null
    {
        if (!$this->isSettableData($data)) {
            return null;
        }
        return $this->getInterQuartileRange($data) / 2;
    }
}
