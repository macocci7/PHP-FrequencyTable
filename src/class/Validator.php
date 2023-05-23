<?php

/**
 * Note: This Validator class is only for use of Histogram.php & Boxplot.php.
 */

class Validator
{
    private $warning;
    private $error;
    private $rules;
    private $data;

    public function __construct($data = []) {
        $this->setData($data);
    }

    public function setData($data) {
        if (!is_array($data)) throw "Validator::__construct: Parameter must be hash array.";
        if (empty($data)) return false;
        $this->data = $data;
        return $this;
    }

    public function rule($rules) {
        if (!is_array($rules)) return throw "Validator::rule: Parameter must be hash array.";
        $this->rules = [...$this->rules, ...$rules];
        return $this;
    }

    public function validate() {
        $this->warning = null;
        $this->error = null;
        foreach($rules as $key => $rule) {
            $this->validateEach($key, $rule);
        }
        return !empty($this->errors());
    }

    public function getData($key) {
        if (array_key_exists($key, $this->data)) return $this->data[$key];
        return null;
    }

    public function validateEach($key, $rule) {
        if (!strlen($key)) return false;
        $value = $this->getData($key);
        $conditions = explode('|', $rule);
        foreach($conditions as $condition) {
            if (strcmp('file',$condition)===0) {
                if (!file_exists($value)) {
                    $this->setError($key, $condition, $value.' does not exist.');
                    return false;
                }
                continue;
            }
            if (strcmp('integer',$condition)===0) {
                if (!is_int($value)) {
                    $this->setError($key, $condition, $value.' is not integer.');
                    return false;
                }
                continue;
            }
            if (strcmp('float',$condition)===0) {
                if (!is_float($value)) {
                    $this->setError($key, $condition, $value.' is not float.');
                    return false;
                }
                continue;
            }
            if (strcmp('string',$condition)===0) {
                if (!is_string($value)) {
                    $this->setError($key, $condition, $value.' is not string.');
                    return false;
                }
                continue;
            }
            if (strcmp('colorcode',$condition)===0) {
                if (!preg_match('/^#[A-Fa-f0-9]{3}$|^#[A-Fa-f0-9]{6}$/', $value)) {
                    $this->setError($key, $condition, $value.' is not colorcode.');
                    return false;
                }
                continue;
            }
            if (str_starts_with($condition, 'min:')) {
                $min = substr($condition, 4);
                if (!is_numeric($min)) {
                    $this->setWarning($key, $condition, 'specified min condition ' . $min .' is not numeric.');
                    continue;
                }
                if ($value < (float) $min) {
                    $this->setError($key, $condition, $value . ' is less than ' . $min . '.');
                    return false;
                }
                continue;
            }
            if (str_starts_with($condition, 'max:')) {
                $max = substr($condition, 4);
                if (!is_numeric($max)) {
                    $this->setError($key, $condition, 'specified max condition ' . $max . ' is not numeric.');
                    continue;
                }
                if ($value > (float) $max) {
                    $this->setError($key, $condition, $value.' is greater than ' . $max . '.');
                    return false;
                }
                continue;
            }
        }
        return true;
    }

    private function setWarning($key, $rule, $message) {
        if (!array_key_exists($this->warning)) $this->warning[$key] = [];
        $this->warning[$key][$rule] = $message;
        return $this;
    }

    private function setError($key, $rule, $message) {
        if (!array_key_exists($this->error)) $this->error[$key] = [];
        $this->error[$key][$rule] = $message;
        return $this;
    }

    public function warnings() {
        return $this->warning;
    }

    public function errors() {
        return $this->error;
    }
}