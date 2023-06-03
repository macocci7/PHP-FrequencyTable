<?php
namespace Macocci7\PhpFrequencyTable;

/**
 * Created by: macocci7
 * Date: 2023/05/18
 */

class FrequencyTable
{

    private $data = null;
    private $classRange = null;
    private $total = null;
    private $defaultTableSeparator = '|';
    private $classSeparator = ' ~ ';
    private $tableSeparator = null;
    private $validColumns2Show = [
        'Class',
        'Frequency',
        'CumulativeFrequency',
        'RelativeFrequency',
        'CumulativeRelativeFrequency',
        'ClassValue',
        'ClassValue * Frequency',
    ];
    private $defaultColumns2Show = [
        'Class',
        'Frequency',
        'RelativeFrequency',
        'ClassValue',
        'ClassValue * Frequency',
    ];
    private $columns2Show = [];
    private $defaultTableHead = [
        'Class' => 'Class',
        'Frequency' => 'Frequency',
        'CumulativeFrequency' => 'CumulativeFrequency',
        'RelativeFrequency' => 'RelativeFrequency',
        'CumulativeRelativeFrequency' => 'CumulativeRelativeFrequency',
        'ClassValue' => 'ClassValue',
        'ClassValue * Frequency' => 'ClassValue * Frequency',
    ];
    private $defaultTableColumnAligns = [
        'Class' => ':---:',
        'Frequency' => ':---:',
        'CumulativeFrequency' => ':---:',
        'RelativeFrequency' => ':---:',
        'CumulativeRelativeFrequency' => ':---:',
        'ClassValue' => ':---:',
        'ClassValue * Frequency' => '---:',
    ];
    private $supportedFormats = [
        'md' => 'markdown',
        'csv' => 'csv',
        'tsv' => 'tsv',
        'html' => 'html',
    ];
    private $showMean = false;

    public function __construct($param = [])
    {
        if (array_key_exists('classRange', $param)) {
            $this->setClassRange($param['classRange']);
        }
        if (array_key_exists('data', $param)) {
            $this->setData($param['data']);
        }
        $this->setColumns2Show($this->defaultColumns2Show);
        if (array_key_exists('columns2Show', $param)) {
            $this->setColumns2Show($param['columns2Show']);
        }
        $this->setDefaultTableSeparator();
    }

    public function meanOn()
    {
        $this->showMean = true;
        return $this;
    }

    public function meanOff()
    {
        $this->showMean = false;
        return $this;
    }

    public function isSettableData($data) {
        if (!is_array($data)) return false;
        if (empty($data)) return false;
        foreach ($data as $value) {
            if (!(is_int($value) || is_float($value))) return false;
        }
        return true;
    }

    public function setData($data = null)
    {
        if ($this->isSettableData($data)) {
            $this->data = $data;
            $this->setTotal($this->getFrequencies());
            return true;
        }
        $this->data = null;
        $this->total = null;
        return false;
    }

    public function getData($key = null)
    {
        return (null === $key) ? $this->data : (array_key_exists($key,$this->data) ? $this->data[$key] : null);
    }

    public function getDataRange($data)
    {
        if (!$this->isSettableData($data)) return;
        return $this->getMax($data) - $this->getMin($data);
    }

    public function isSettableClassRange($classRange)
    {
        if (!(is_int($classRange) || is_float($classRange))) return false;
        if ($classRange > 0) return true;
        return false;
    }

    public function setClassRange($classRange = null)
    {
        if ($this->isSettableClassRange($classRange)) {
            $this->classRange = $classRange;
            return true;
        }
        $this->classRange = null;
        return false;
    }

    public function getClassRange()
    {
        return $this->classRange;
    }

    public function getFrequencies()
    {
        $frequencies = [];
        foreach ($this->getClasses($this->data, $this->classRange) as $class) {
            $frequencies[] = $this->getFrequency($this->data, $class);
        }
        return $frequencies;
    }

    public function getClasses()
    {
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

    public function isSettableClass($class)
    {
        if (!is_array($class)) return false;
        if (empty($class)) return false;
        if (!(array_key_exists('bottom',$class) && array_key_exists('top',$class))) return false;
        if (!(is_int($class['bottom']) || is_float($class['bottom']))) return false;
        if (!(is_int($class['top']) || is_float($class['top']))) return false;
        if (!($class['bottom'] < $class['top'])) return false;

        return true;
    }

    public function getFrequency($data, $class)
    {
        if (!($this->isSettableData($data) && $this->isSettableClass($class))) return;
        $count = 0;
        foreach ($data as $d) {
            if ($d >= $class['bottom'] && $d < $class['top']) {
                $count++;
            }
        }
        return $count;
    }

    public function getCumulativeFrequency($frequencies, $index)
    {
        if (!$this->isSettableData($frequencies) || !is_int($index)) return;
        if (!($index >= 0) || $index >= count($frequencies)) return;
        return array_sum(array_slice($frequencies,0,$index + 1));
    }

    public function getMin($data)
    {
        return $this->isSettableData($data) ? min($data) : null;
    }

    public function getMax($data)
    {
        return $this->isSettableData($data) ? max($data) : null;
    }

    public function setTotal($data)
    {
        if (!$this->isSettableData($data)) {
            $this->total = null;
            return false;
        }
        $this->total = array_sum($data);
        return true;
    }

    public function getTotal()
    {
        if (null == $this->total && null != $this->getData()) {
            $this->setTotal($this->getFrequencies());
        }
        return $this->total;
    }

    public function getClassValue($class)
    {
        if (!$this->isSettableClass($class)) return;
        return ($class['bottom'] + $class['top']) / 2;
    }

    public function getRelativeFrequency($frequency)
    {
        if (!$this->getTotal() || !(is_int($frequency))) return;
        if ($frequency < 0 || $frequency > $this->getTotal()) return;
        return $frequency / $this->getTotal();
    }

    public function getCumulativeRelativeFrequency($frequencies, $index)
    {
        if (!$this->isSettableData($frequencies) || !is_int($index)) return;
        if (!($index >= 0) || $index >= count($frequencies)) return;
        $rf = [];
        foreach (array_slice($frequencies,0,$index + 1) as $frequency) {
            $rf[] = $this->getRelativeFrequency($frequency);
        }
        return array_sum($rf);
    }

    public function getMean()
    {
        if (!$this->isSettableData($this->getData()) || !$this->getTotal()) return;
        $fc = [];
        $classes = $this->getClasses();
        foreach ($this->getFrequencies() as $index => $frequency) {
            $fc[] = $this->getClassValue($classes[$index]) * $frequency;
        }
        return array_sum($fc) / $this->getTotal();
    }

    public function getMode()
    {
        $frequencies = $this->getFrequencies();
        if (!count($frequencies) > 0) return;
        $classes = $this->getClasses();
        $index = array_search(max($frequencies), $frequencies);
        return $this->getClassValue($classes[$index]);
    }

    public function getMedian($param)
    {
        if (!$this->isSettableData($param)) return;
        $data = [...$param];
        $count = count($data);
        sort($data);
        if (1 === $count % 2) return $data[(int) (($count + 1) / 2) - 1];
        return ($data[(int) ($count / 2) - 1] + $data[(int) ($count / 2)]) / 2;
    }

    public function getMedianClass()
    {
        if (!$this->isSettableData($this->getData())) return;
        $median = $this->getMedian($this->getData());
        foreach ($this->getClasses() as $index => $class) {
            if ($median >= $class['bottom'] && $median < $class['top']) {
                return ['index' => $index, ...$class];
            }
        }
    }

    public function getFirstQuartile($data)
    {
        if (!$this->isSettableData($data)) return;
        $a = [...$data];
        sort($a);
        $count = count($a);
        if (1 === $count) return $data[0];
        $chunkLength = (0 === $count % 2) ? (int) $count / 2 : (int) (($count - 1) / 2);
        $forward = array_slice($a, 0, $chunkLength);
        return $this->getMedian($forward);
    }

    public function getThirdQuartile($data)
    {
        if (!$this->isSettableData($data)) return;
        $a = [...$data];
        sort($a);
        $count = count($a);
        if (1 === $count) return $data[0];
        $offset = (0 === $count % 2) ? (int) $count / 2 : (int) (($count + 1) / 2);
        $backward = array_slice($a, $offset);
        return $this->getMedian($backward);
    }

    public function getInterQuartileRange($data)
    {
        if (!$this->isSettableData($data)) return;
        return $this->getThirdQuartile($data) - $this->getFirstQuartile($data);
    }

    public function getQuartileDeviation($data)
    {
        if (!$this->isSettableData($data)) return;
        return $this->getInterQuartileRange($data) / 2;
    }

    public function setTableSeparator($separator)
    {
        if (!is_string($separator)) return false;
        $this->tableSeparator = $separator;
        return true;
    }

    public function getTableSeparator()
    {
        return $this->tableSeparator;
    }

    public function setDefaultTableSeparator()
    {
        return $this->setTableSeparator($this->defaultTableSeparator);
    }

    public function getTableColumnAligns2Show()
    {
        $aligns = [];
        foreach ($this->getColumns2Show() as $column) {
            $aligns[$column] = $this->defaultTableColumnAligns[$column];
        }
        return $aligns;
    }

    public function getColumns2Show()
    {
        return $this->columns2Show;
    }

    public function getValidColumns2Show()
    {
        return $this->validColumns2Show;
    }

    public function isSettableColumns2Show($columns)
    {
        if (!is_array($columns)) return false;
        if (empty($columns)) return false;
        foreach ($columns as $c) {
            if (!in_array($c, $this->getValidColumns2Show())) return false;
        }
        return true;
    }

    public function setColumns2Show($columns)
    {
        if ($this->isSettableColumns2Show($columns)){
            $this->columns2Show = $columns;
            return true;
        }
        return false;
    }

    public function getTableTotal2Show($data)
    {
        return [
            'Class' => 'Total',
            'Frequency' => $this->getTotal(),
            'CumulativeFrequency' => $this->getTotal(),
            'RelativeFrequency' => number_format(array_sum(array_column($data, 'RelativeFrequency')),2,'.',','),
            'CumulativeRelativeFrequency' => number_format(array_sum(array_column($data, 'RelativeFrequency')),2,'.',','),
            'ClassValue' => '---',
            'ClassValue * Frequency' => number_format(array_sum(array_column($data, 'ClassValue * Frequency')),1,'.',','),
        ];
    }

    public function getMean2Show()
    {
        return [
            'Class' => 'Mean',
            'Frequency' => '---',
            'CumulativeFrequency' => '---',
            'RelativeFrequency' => '---',
            'CumulativeRelativeFrequency' => '---',
            'ClassValue' => '---',
            'ClassValue * Frequency' => number_format($this->getMean(),1,'.',','),
        ];

    }

    public function getData2Show()
    {
        if (!$this->isSettableData($this->getData())) return [];
        $data2Show = [];
        $data2Show[] = $this->defaultTableHead;
        $data2Show[] = $this->defaultTableColumnAligns;
        $data = $this->getDataOfEachClass();
        $data2Show = array_merge_recursive($data2Show, $data);
        $data2Show[] = $this->getTableTotal2Show($data);
        if ($this->showMean) $data2Show[] = $this->getMean2Show();
        return $data2Show;
    }

    public function getDataOfEachClass()
    {
        if (!$this->isSettableData($this->getData())) return [];
        $data = [];
        $classes = $this->getClasses();
        $frequencies = $this->getFrequencies();
        $fc = [];
        $rf = [];
        foreach ($frequencies as $index => $frequency) {
            $fc[] = $frequency * $this->getClassValue($classes[$index]);
            $rf[] = $this->getRelativeFrequency($frequency);
            $data[] = [
                'Class' =>
                    number_format(
                        $classes[$index]['bottom'])
                        . $this->classSeparator
                        . number_format($classes[$index]['top']
                    ),
                'Frequency' => $frequency,
                'CumulativeFrequency' => $this->getCumulativeFrequency($frequencies, $index),
                'RelativeFrequency' =>number_format($rf[$index],2,'.',','),
                'CumulativeRelativeFrequency' =>
                    number_format($this->getCumulativeRelativeFrequency($frequencies, $index),2,'.',','),
                'ClassValue' => number_format($this->getClassValue($classes[$index]),1,'.',','),
                'ClassValue * Frequency' => number_format($fc[$index],1,'.',','),
            ];
        }
        return $data;
    }

    public function filterData2Show($data)
    {
        $columns2Show = $this->getColumns2Show();
        $filtered = [];
        foreach ($data as $index => $row) {
            $filtered[$index] = [];
            foreach ($columns2Show as $c) {
                $filtered[$index][$c] = array_key_exists($c, $row) ? $row[$c] : null;
            }
        }
        return $filtered;
    }

    public function show()
    {
        echo $this->markdown();
        return $this->markdown();
    }

    public function parse()
    {
        if (!$this->isSettableClassRange($this->getClassRange())) return;
        if (!$this->isSettableData($this->getData())) return;
        return [
            'classRange' => $this->getClassRange(),
            'data' => $this->getData(),
            'Max' => $this->getMax($this->getData()),
            'Min' => $this->getMin($this->getData()),
            'DataRange' => $this->getDataRange($this->getData()),
            'Mode' => $this->getMode(),
            'Total' => $this->getTotal($this->getFrequencies()),
            'Mean' => $this->getMean(),
            'Median' => $this->getMedian($this->getData()),
            'MedianClass' => $this->getMedianClass(),
            'FirstQuartile' => $this->getFirstQuartile($this->getData()),
            'ThirdQuartile' => $this->getThirdQuartile($this->getData()),
            'InterQuartileRange' => $this->getInterQuartileRange($this->getData()),
            'QuartileDeviation' => $this->getQuartileDeviation($this->getData()),
            'Classes' => $this->getClasses(),
            'Frequencies' => $this->getFrequencies(),
            'FrequencyTable' => $this->show(['Mean'=>true,'STDOUT'=>false,'ReturnValue'=>true]),
        ];
    }

    public function xsv($path, $separator, $quatation = true, $eol = "\n")
    {
        if (null !== $path && !is_string($path)) return;
        if (!is_string($separator)) return;
        if (strlen($separator) === 0) return;
        if (!is_bool($quatation)) return;
        // no check for $eol : it's at the user's own risk.
        $qm = $quatation ? '"' : '';
        $splitter = $qm . $separator . $qm;
        $buffer = null;
        $buffer .= $qm . implode($splitter, $this->getColumns2Show()) . $qm . $eol;
        $data4EachClass = $this->filterData2Show($this->getDataOfEachClass());
        if (empty($data4EachClass)) {
            return empty($path) ? 'no data' : file_put_contents($path, 'no data');
        }
        foreach ($data4EachClass as $index => $data) {
            $buffer .= $qm . implode($splitter, $data) . $qm . $eol;
        }
        $totals = $this->filterData2Show([$this->getTableTotal2Show($data4EachClass)]);
        $buffer .= $qm . implode($splitter, $totals[0]) . $qm . $eol;
        if ($this->showMean) {
            $means = $this->filterData2Show([$this->getMean2Show()]);
            $buffer .= $qm . implode($splitter, $means[0]) . $qm . $eol;
        }
        return empty($path) ? $buffer : file_put_contents($path, $buffer);
    }

    public function csv($path = null, $quatation = true, $eol = "\n")
    {
        return $this->xsv($path, ',', $quatation, $eol);
    }

    public function tsv($path = null, $quatation = true, $eol = "\n")
    {
        return $this->xsv($path, "\t", $quatation, $eol);
    }

    public function html($path = null)
    {
        if (null !== $path && !is_string($path)) return;
        $pre = '<tr><td>';
        $pro = '</td></tr>';
        $eol = "\n";
        $splitter = '</td><td>';
        $data4EachClass = $this->filterData2Show($this->getDataOfEachClass());
        if (empty($data4EachClass)) {
            return empty($path) ? 'no data' : file_put_contents($path, 'no data');
        }
        $buffer = "<table>" . $eol;
        $buffer .= $pre . implode($splitter, $this->getColumns2Show()) . $pro . $eol;
        foreach ($data4EachClass as $index => $data) {
            $buffer .= $pre . implode($splitter, $data) . $pro . $eol;
        }
        $totals = $this->filterData2Show([$this->getTableTotal2Show($data4EachClass)]);
        $buffer .= $pre . implode($splitter, $totals[0]) . $pro . $eol;
        if ($this->showMean) {
            $means = $this->filterData2Show([$this->getMean2Show()]);
            $buffer .= $pre . implode($splitter, $means[0]) . $pro . $eol;
        }
        $buffer .= "</table>" . $eol;
        return empty($path) ? $buffer : file_put_contents($path, $buffer);
    }

    public function markdown($path = null)
    {
        if (null !== $path && !is_string($path)) return;
        $separator = $this->getTableSeparator();
        $eol = "\n";
        $buffer = null;
        $buffer .= $separator . implode($separator, $this->getColumns2Show()) . $separator . $eol;
        $buffer .= $separator . implode($separator, $this->getTableColumnAligns2Show()) . $separator . $eol;
        $data4EachClass = $this->filterData2Show($this->getDataOfEachClass());
        if (empty($data4EachClass)) {
            return empty($path) ? 'no data' : file_put_contents($path, 'no data');
        }
        foreach ($data4EachClass as $index => $data) {
            $buffer .= $separator . implode($separator, $data) . $separator . $eol;
        }
        $totals = $this->filterData2Show([$this->getTableTotal2Show($data4EachClass)]);
        $buffer .= $separator . implode($separator, $totals[0]) . $separator . $eol;
        if ($this->showMean) {
            $means = $this->filterData2Show([$this->getMean2Show()]);
            $buffer .= $separator . implode($separator, $means[0]) . $separator . $eol;
        }
        return empty($path) ? $buffer : file_put_contents($path, $buffer);
    }

    public function save($path)
    {
        if (!is_string($path)) return;
        if (strlen($path) === 0) return;
        $pathParts = pathinfo($path);
        $extension = strtolower($pathParts['extension']);
        if (!array_key_exists($extension, $this->supportedFormats)) return;
        return $this->{$this->supportedFormats[$extension]}($path);
    }
}
