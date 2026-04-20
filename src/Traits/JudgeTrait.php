<?php

namespace Macocci7\PhpFrequencyTable\Traits;

trait JudgeTrait
{
    /**
     * judges if the param is number or not
     */
    public function isNumber(mixed $value): bool
    {
        return is_int($value) || is_float($value);
    }

    /**
     * judges if the param is valid or not
     */
    public function isSettableData(mixed $data): bool
    {
        if (!is_array($data)) {
            return false;
        }
        if (empty($data)) {
            return false;
        }
        foreach ($data as $value) {
            if (!$this->isNumber($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * judges if the param is valid class range or not
     */
    public function isSettableClassRange(mixed $classRange): bool
    {
        if (!$this->isNumber($classRange)) {
            return false;
        }
        return $classRange > 0;
    }

    /**
     * judges if the param is valid class or not
     */
    public function isSettableClass(mixed $class): bool
    {
        if (!is_array($class)) {
            return false;
        }
        if (empty($class)) {
            return false;
        }
        if (
            !array_key_exists('bottom', $class)
            || !array_key_exists('top', $class)
        ) {
            return false;
        }
        if (
            !$this->isNumber($class['bottom'])
            || !$this->isNumber($class['top'])
        ) {
            return false;
        }
        if (!($class['bottom'] < $class['top'])) {
            return false;
        }
        return true;
    }

    /**
     * judges if the param is valid for columns to show or not
     */
    public function isSettableColumns2Show(mixed $columns): bool
    {
        if (!is_array($columns)) {
            return false;
        }
        if (empty($columns)) {
            return false;
        }
        foreach ($columns as $c) {
            if (!in_array($c, $this->getValidColumns2Show())) {
                return false;
            }
        }
        return true;
    }
}
