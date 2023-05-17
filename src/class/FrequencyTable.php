<?php

class FrequencyTable {

    private $data = null;
    private $classRange = null;
    private $sum = null;
    private $tableSeparator = '|';
    private $validColumns2Show = [
        'Class',
        'Frequency',
        'RelativeFrequency',
        'ClassValue',
        'ClassValue * Frequency',
    ];
    private $columns2Show = [];

    public function __construct($param = []) {
        if (array_key_exists('data', $param)) {
            $this->data = $param['data'];
        }
        if (array_key_exists('classRange', $param)) {
            $this->classRange = $param['classRange'];
        }
        if (array_key_exists('data', $param) && array_key_exists('classRange', $param)) {
            $this->setSum($this->getFrequencies());
        }
        $this->setColumns2Show($this->getValidColumns2Show());
        if (array_key_exists('columns2Show', $param)) {
            $this->setColumns2Show($param['columns2Show']);
        }
    }

    public function isSettableData($data) {
        if (!is_array($data)) return false;
        if (empty($data)) return false;
        foreach($data as $value) {
            if (!(is_int($value) || is_float($value))) return false;
        }
        return true;
    }

    public function setData($data = null) {
        if ($this->isSettableData($data)) {
            $this->data = $data;
            $this->setSum($this->getFrequencies());
        } else {
            $this->data = null;
            $this->sum = null;
        }
    }

    public function getData($key = null) {
        return (null === $key) ? $this->data : (array_key_exists($key,$this->data) ? $this->data[$key] : null);
    }

    public function isSettableClassRange($classRange) {
        if (!(is_int($classRange) || is_float($classRange))) return false;
        if ($classRange > 0) return true;
        return false;
    }

    public function setClassRange($classRange = null) {
        if ($this->isSettableClassRange($classRange)) {
            $this->classRange = $classRange;
        } else {
            $this->classRange = null;
        }
    }

    public function getClassRange() {
        return $this->classRange;
    }

    public function getFrequencies() {
        $frequencies = [];
        foreach ($this->getClasses($this->data, $this->classRange) as $class) {
            $frequencies[] = $this->getFrequency($this->data, $class);
        }
        return $frequencies;
    }

    public function getClasses() {
        if (!($this->isSettableData($this->data) && $this->isSettableClassRange($this->classRange))) return [];
        $min = min($this->data);
        $max = max($this->data);
        $bottomStart = ((int) ($min / $this->classRange)) * $this->classRange;
        $topEnd = (1 + (int) ($max / $this->classRange)) * $this->classRange;
        $class = [];
        for ($bottom = $bottomStart; $bottom < $topEnd; $bottom += $this->classRange) {
            $class[] = [
                'bottom' => $bottom,
                'top' => $bottom + $this->classRange,
            ];
        }
        return $class;
    }

    public function isSettableClass($class) {
        if (!is_array($class)) return false;
        if (empty($class)) return false;
        if (!(array_key_exists('bottom',$class) && array_key_exists('top',$class))) return false;
        if (!(is_int($class['bottom']) || is_float($class['bottom']))) return false;
        if (!(is_int($class['top']) || is_float($class['top']))) return false;
        if (!($class['bottom'] < $class['top'])) return false;

        return true;
    }

    public function getFrequency($data, $class) {
        if (!($this->isSettableData($data) && $this->isSettableClass($class))) return;
        $count = 0;
        foreach($data as $d) {
            if ($d >= $class['bottom'] && $d < $class['top']) {
                $count++;
            }
        }
        return $count;
    }

    public function getMin($data) {
        return is_array($data) ? (empty($data) ? null : min($data)) : null;
    }

    public function getMax($data) {
        return is_array($data) ? (empty($data) ? null : max($data)) : null;
    }

    public function setSum($data) {
        if (!$this->isSettableData($data)) {
            $this->sum = null;
            return;
        }
        $this->sum = array_sum($data);
    }

    public function getSum() {
        if (null == $this->sum && null != $this->getData()) {
            $this->setSum($this->getFrequencies());
        }
        return $this->sum;
    }

    public function getClassValue($class) {
        return ($class['bottom'] + $class['top']) / 2;
    }

    public function getRelativeFrequency($frequency) {
        return $frequency / $this->getSum();
    }

    public function getAverage() {
        if (!count($this->getData()) > 0) return;
        $fc = [];
        $classes = $this->getClasses();
        foreach ($this->getFrequencies() as $index => $frequency) {
            $fc[] = $this->getClassValue($classes[$index]) * $frequency;
        }
        return $this->getSum() > 0 ? array_sum($fc) / $this->getSum() : 0;
    }

    public function getMode() {
        $frequency = $this->getFrequencies();
        if (!count($frequency) > 0) return;
        $classes = $this->getClasses();
        $index = array_search(max($frequency), $frequency);
        return $this->getClassValue($classes[$index]);
    }

    public function getMedian($data) {
        $count = count($data);
        if (!$count>0) return;
        sort($data);
        if (1 === $count % 2) return $data[(int) (($count + 1) / 2) - 1];
        return ($data[(int) ($count / 2) - 1] + $data[(int) ($count / 2)]) / 2;
    }

    public function getMedianClass() {
        $median = $this->getMedian($this->getData);
        foreach($this->getClasses() as $index => $class) {
            if ($median >= $class['bottom'] && $median < $class['top']) {
                return [$index => $class];
            }
        }
    }

    public function getFirstQuartile($data) {
        $a = [...$data];
        sort($a);
        $count = count($a);
        if (0 === $count) return;
        if (1 === $count) return $data[0];
        $chunkLength = (0 === $count % 2) ? (int) $count / 2 : (int) (($count - 1) / 2);
        $forward = array_slice($a, 0, $chunkLength);
        return $this->getMedian($forward);
    }

    public function getThirdQuartile($data) {
        $a = [...$data];
        sort($a);
        $count = count($a);
        if (0 === $count) return;
        if (1 === $count) return $data[0];
        $offset = (0 === $count % 2) ? (int) $count / 2 : (int) (($count + 1) / 2);
        $backward = array_slice($a, $offset);
        return $this->getMedian($backward);
    }

    public function setTableSeparator($separator) {
        $this->tableSeparator = $separator;
    }

    public function getTableSeparator() {
        return $this->tableSeparator;
    }

    public function setColumns2Show($columns) {
        if ($this->validateColumns2Show($columns)){
            $this->columns2Show = $columns;
        }
    }

    public function getColumns2Show() {
        return $this->columns2Show;
    }

    public function getValidColumns2Show() {
        return $this->validColumns2Show;
    }

    public function validateColumns2Show($columns) {
        if (!is_array($columns)) return false;
        if (!(count($columns)>0)) return false;
        foreach($columns as $c) {
            if (!in_array($c, $this->getValidColumns2Show())) return false;
        }
        return true;
    }

    public function getData2Show() {
        $data = [];
        $data[] = [
                    'Class' => 'Class',
                    'Frequency' => 'Frequency',
                    'RelativeFrequency' => 'RelativeFrequency',
                    'ClassValue' => 'ClassValue',
                    'ClassValue * Frequency' => 'ClassValue * Frequency',
                  ];
        $data[] = [
                    'Class' => ':---:',
                    'Frequency' => ':---:',
                    'RelativeFrequency' => ':---:',
                    'ClassValue' => ':---:',
                    'ClassValue * Frequency' => '---:',
                  ];
        $classes = $this->getClasses();
        $frequencies = $this->getFrequencies();
        $fc = [];
        $rf = [];
        foreach($frequencies as $index => $frequency) {
            $fc[] = $frequency * $this->getClassValue($classes[$index]);
            $rf[] = $this->getRelativeFrequency($frequency);
            $data[] = [
                    'Class' => number_format($classes[$index]['bottom']) . '-' . number_format($classes[$index]['top']),
                    'Frequency' => $frequency,
                    'RelativeFrequency' => number_format($rf[$index],2,'.',','),
                    'ClassValue' => number_format($this->getClassValue($classes[$index]),1,'.',','),
                    'ClassValue * Frequency' => number_format($fc[$index],1,'.',','),
            ];
        }
        $data[] = [
                    'Class' => 'Total',
                    'Frequency' => $this->getSum(),
                    'RelativeFrequency' => number_format(array_sum($rf),2,'.',','),
                    'ClassValue' => '---',
                    'ClassValue * Frequency' => number_format(array_sum($fc),1,'.',','),
                  ];
        $data[] = [
                    'Class' => 'Average',
                    'Frequency' => '---',
                    'RelativeFrequency' => '---',
                    'ClassValue' => '---',
                    'ClassValue * Frequency' => number_format($this->getAverage(),1,'.',','),
                  ];
        $data[] = [
                    'Class' => 'Mode',
                    'Frequency' => '---',
                    'RelativeFrequency' => '---',
                    'ClassValue' => number_format($this->getMode(),1,'.',','),
                    'ClassValue * Frequency' => '---',
                  ];
        return $data;
    }

    public function filterData2Show($data) {
        $columns2Show = $this->getColumns2Show();
        $filtered = [];
        foreach ($data as $index => $row) {
            $filtered[$index] = [];
            foreach($columns2Show as $c) {
                $filtered[$index][$c] = array_key_exists($c, $row) ? $row[$c] : null;
            }
        }
        return $filtered;
    }

    public function show() {
        if (!(count($this->data)>0)) {
            echo "no data to show\n";
            return;
        }
        $data = $this->getData2Show();
        $separator = $this->getTableSeparator();
        foreach($this->filterData2Show($data) as $row) {
            echo $separator . implode($separator, $row) . $separator . "\n";
        }
    }
}