<?php

namespace Macocci7\PhpFrequencyTable;

/**
 * Creates A Frequency Distribution Table
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
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

    /**
     * constructor
     */
    public function __construct(array $param = [])
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

    /**
     * sets visibility of mean on
     * @param
     * @return  self
     */
    public function meanOn()
    {
        $this->showMean = true;
        return $this;
    }

    /**
     * sets visibility of mean off
     * @param
     * @return  self
     */
    public function meanOff()
    {
        $this->showMean = false;
        return $this;
    }

    /**
     * judges if the param is number or not
     * @param   mixed
     * @return  bool
     */
    public function isNumber($value)
    {
        return is_int($value) || is_float($value);
    }

    /**
     * judges if the param is valid or not
     * @param   mixed  $data
     * @return  bool
     */
    public function isSettableData(mixed $data)
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
     * sets data
     * @param   array   $data   default = null
     * @return  self
     */
    public function setData(array $data)
    {
        if (!$this->isSettableData($data)) {
            throw new \Exception("Invalid data set.");
        }
        $this->data = $data;
        $this->setTotal($this->getFrequencies());
        return $this;
    }

    /**
     * returns data
     * @param   string  $key
     * @return  array|int|float
     */
    public function getData(string $key = null)
    {
        return is_null($key) ? $this->data : ($this->data[$key] ?? null);
    }

    /**
     * returns data range
     * @param   array   $data
     * @return  int|float
     */
    public function getDataRange(array $data)
    {
        if (!$this->isSettableData($data)) {
            throw new \Exception("Invalid class range.");
        }
        return $this->getMax($data) - $this->getMin($data);
    }

    /**
     * judges if the param is valid class range or not
     * @param   int|float
     * @return  bool
     */
    public function isSettableClassRange($classRange)
    {
        if (!$this->isNumber($classRange)) {
            return false;
        }
        return $classRange > 0;
    }

    /**
     * sets class range
     * @param   int|float
     * @return  self
     */
    public function setClassRange($classRange = null)
    {
        if (!$this->isSettableClassRange($classRange)) {
            throw new \Exception("Invalid class range.");
        }
        $this->classRange = $classRange;
        return $this;
    }

    /**
     * returns class range
     * @param
     * @return  int|float
     */
    public function getClassRange()
    {
        return $this->classRange;
    }

    /**
     * returns frequencies
     * @param
     * @return  array
     */
    public function getFrequencies()
    {
        $frequencies = [];
        foreach ($this->getClasses($this->data, $this->classRange) as $class) {
            $frequencies[] = $this->getFrequency($this->data, $class);
        }
        return $frequencies;
    }

    /**
     * returns classes
     * @param
     * @return  array
     */
    public function getClasses()
    {
        if (
            !$this->isSettableData($this->data)
            || !$this->isSettableClassRange($this->classRange)
        ) {
            return [];
        }
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

    /**
     * judges if the param is valid class or not
     * @param   array   $class
     * @return  bool
     */
    public function isSettableClass(array $class)
    {
        if (empty($class)) {
            return false;
        }
        if (
            !array_key_exists('bottom', $class)
            || !array_key_exists('top', $class)
        ) {
            return false;
        }
        if (!$this->isNumber($class['bottom'])) {
            return false;
        }
        if (!$this->isNumber($class['top'])) {
            return false;
        }
        if (!($class['bottom'] < $class['top'])) {
            return false;
        }
        return true;
    }

    /**
     * returns frequency of the specified class
     * @param   array   $data
     * @param   int|float   $class
     * @return  int
     */
    public function getFrequency(array $data, $class)
    {
        if (!$this->isSettableData($data) || !$this->isSettableClass($class)) {
            throw new \Exception("Invalid data or class.");
        }
        $count = 0;
        foreach ($data as $d) {
            if ($d >= $class['bottom'] && $d < $class['top']) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * returns cumulative frequency of the specified (array) index
     * @param   array   $frequencies
     * @param   int     $index
     * @return  int
     */
    public function getCumulativeFrequency(array $frequencies, int $index)
    {
        if (!$this->isSettableData($frequencies)) {
            throw new \Exception("Invalid frequencies.");
        }
        if ($index < 0 || $index >= count($frequencies)) {
            throw new \Exception("Invalid index specified.");
        }
        return array_sum(array_slice($frequencies, 0, $index + 1));
    }

    /**
     * returns min value of the specified array
     * @param   array   $data
     * @return  int|float
     */
    public function getMin(array $data)
    {
        if (!$this->settableData($data)) {
            throw new \Exception("Invalid data specified.");
        }
        return min($data);
    }

    /**
     * returns max value of the specified array
     * @param   array   $data
     * @return  int|float
     */
    public function getMax(array $data)
    {
        if ($this->isSettableData($data)) {
            throw new \Exception("Invalid data specified.");
        }
        return max($data);
    }

    /**
     * sets total of the specified data
     * @param   array   $data
     * @return  self
     */
    public function setTotal(array $data)
    {
        if (!$this->isSettableData($data)) {
            throw new \Exception("Invalid data specified.");
        }
        $this->total = array_sum($data);
        return $this;
    }

    /**
     * returns total
     * @return  int|float
     */
    public function getTotal()
    {
        if (is_null($this->total) && $this->isSettableData($this->getData())) {
            $this->setTotal($this->getFrequencies());
        }
        return $this->total;
    }

    /**
     * returns class value
     * @param   array   $class
     * @return  int|float
     */
    public function getClassValue(array $class)
    {
        if (!$this->isSettableClass($class)) {
            throw new \Exception("Invalid class specified.");
        }
        return ($class['bottom'] + $class['top']) / 2;
    }

    /**
     * returns relative frequency
     * @param   int $frequency
     * @return  float
     */
    public function getRelativeFrequency(int $frequency)
    {
        if (!$this->getTotal()) {   // in case: null or zero
            throw new \Exception("Total is not set.");
        }
        if ($frequency < 0 || $frequency > $this->getTotal()) {
            throw new \Exception("Invalid frequency specified.");
        }
        return $frequency / $this->getTotal();
    }

    /**
     * returns cumulative relative frequency
     * @param   array   $frequencies
     * @param   int     $index
     * @return  float
     */
    public function getCumulativeRelativeFrequency(array $frequencies, int $index)
    {
        if (!$this->isSettableData($frequencies)) {
            throw new \Exception("Invalid frequencies specified.");
        }
        if ($index < 0 || $index >= count($frequencies)) {
            throw new \Exception("Invalid array index specified.");
        }
        $rf = [];
        foreach (array_slice($frequencies, 0, $index + 1) as $frequency) {
            $rf[] = $this->getRelativeFrequency($frequency);
        }
        return array_sum($rf);
    }

    /**
     * returns mean value
     * @param
     * @return  int|float
     */
    public function getMean()
    {
        if (!$this->isSettableData($this->getData()) || !$this->getTotal()) {
            throw new \Exception("Invalid data or total not set.");
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
     * @param
     * @return  int
     */
    public function getMode()
    {
        $frequencies = $this->getFrequencies();
        if (0 === count($frequencies)) {
            return;
        }
        $classes = $this->getClasses();
        $index = array_search(max($frequencies), $frequencies);
        return $this->getClassValue($classes[$index]);
    }

    /**
     * returns median
     * @param   array   $param
     */
    public function getMedian(array $param)
    {
        if (!$this->isSettableData($param)) {
            throw new \Exception("Invalid data specified.");
        }
        $data = [...$param];
        $count = count($data);
        sort($data);
        if (1 === $count % 2) {
            return $data[(int) (($count + 1) / 2) - 1];
        }
        return ($data[(int) ($count / 2) - 1] + $data[(int) ($count / 2)]) / 2;
    }

    /**
     * returns the class the median belongs
     * @param
     * @return  array
     */
    public function getMedianClass()
    {
        if (!$this->isSettableData($this->getData())) {
            throw new \Exception("Invalid data set.");
        }
        $median = $this->getMedian($this->getData());
        foreach ($this->getClasses() as $index => $class) {
            if ($median >= $class['bottom'] && $median < $class['top']) {
                return ['index' => $index, ...$class];
            }
        }
        return [];
    }

    /**
     * returns the first quartile
     * @param   array   $data
     * @return  int|float
     */
    public function getFirstQuartile(array $data)
    {
        if (!$this->isSettableData($data)) {
            throw new \Exception("Invalid data set.");
        }
        $a = [...$data];
        sort($a);
        $count = count($a);
        if (1 === $count) {
            return $data[0];
        }
        $chunkLength = (0 === $count % 2)
                     ? (int) $count / 2         // in case even
                     : (int) (($count - 1) / 2) // in case odd
                     ;
        $forward = array_slice($a, 0, $chunkLength);
        return $this->getMedian($forward);
    }

    /**
     * returns the third quartile
     * @param   array   $data
     * @return  int|float
     */
    public function getThirdQuartile(array $data)
    {
        if (!$this->isSettableData($data)) {
            throw new \Exception("Invalid data set.");
        }
        $a = [...$data];
        sort($a);
        $count = count($a);
        if (1 === $count) {
            return $data[0];
        }
        $offset = (0 === $count % 2)
                  ? (int) $count / 2            // in case even
                  : (int) (($count + 1) / 2)    // in case odd
                  ;
        $backward = array_slice($a, $offset);
        return $this->getMedian($backward);
    }

    /**
     * returns the inter quartile range
     * @param   array   $data
     * @return  int|float
     */
    public function getInterQuartileRange(array $data)
    {
        if (!$this->isSettableData($data)) {
            throw new \Exception("Invalid data set.");
        }
        return $this->getThirdQuartile($data) - $this->getFirstQuartile($data);
    }

    /**
     * returns the quartile deviation
     * @param   array   $data
     * @return  int|float
     */
    public function getQuartileDeviation(array $data)
    {
        if (!$this->isSettableData($data)) {
            throw new \Exception("Invalid data set.");
        }
        return $this->getInterQuartileRange($data) / 2;
    }

    /**
     * sets the table separator to show frequency table
     * @param   string  $separator
     * @return  self
     */
    public function setTableSeparator(string $separator)
    {
        $this->tableSeparator = $separator;
        return $this;
    }

    /**
     * returns the current table separator
     * @param
     * @return  string
     */
    public function getTableSeparator()
    {
        return $this->tableSeparator;
    }

    /**
     * sets the default table separator
     * @param
     * @return  self
     */
    public function setDefaultTableSeparator()
    {
        $this->setTableSeparator($this->defaultTableSeparator);
        return $this;
    }

    /**
     * returns table column aligns to show
     * @param
     * @return  array
     */
    public function getTableColumnAligns2Show()
    {
        $aligns = [];
        foreach ($this->getColumns2Show() as $column) {
            $aligns[$column] = $this->defaultTableColumnAligns[$column];
        }
        return $aligns;
    }

    /**
     * returns columns to show
     * @param
     * @return  array
     */
    public function getColumns2Show()
    {
        return $this->columns2Show;
    }

    /**
     * returns valid columns to show
     * @param
     * @return  array
     */
    public function getValidColumns2Show()
    {
        return $this->validColumns2Show;
    }

    /**
     * judges if the param is valid for columns to show or not
     * @param   array   $columns
     * @return  bool
     */
    public function isSettableColumns2Show(array $columns)
    {
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

    /**
     * sets columns to show
     * @param   array   $columns
     * @return  self
     */
    public function setColumns2Show(array $columns)
    {
        if (!$this->isSettableColumns2Show($columns)) {
            throw new \Exception("Invalid columns set.");
        }
        $this->columns2Show = $columns;
        return $this;
    }

    /**
     * returns totals in the table to show
     * @param   array   $data
     * @return  array
     */
    public function getTableTotal2Show(array $data)
    {
        if (!$this->isSettableData($data)) {
            throw new \Exception("Invalid data set.");
        }
        return [
            'Class' => 'Total',
            'Frequency' => $this->getTotal(),
            'CumulativeFrequency' => $this->getTotal(),
            'RelativeFrequency' => number_format(
                array_sum(array_column($data, 'RelativeFrequency')),
                2,
                '.',
                ','
            ),
            'CumulativeRelativeFrequency' => number_format(
                array_sum(array_column($data, 'RelativeFrequency')),
                2,
                '.',
                ','
            ),
            'ClassValue' => '---',
            'ClassValue * Frequency' => number_format(
                array_sum(array_column($data, 'ClassValue * Frequency')),
                1,
                '.',
                ','
            ),
        ];
    }

    /**
     * returns mean row in the table to show
     * @param
     * @return  array
     */
    public function getMean2Show()
    {
        return [
            'Class' => 'Mean',
            'Frequency' => '---',
            'CumulativeFrequency' => '---',
            'RelativeFrequency' => '---',
            'CumulativeRelativeFrequency' => '---',
            'ClassValue' => '---',
            'ClassValue * Frequency' => number_format(
                $this->getMean(),
                1,
                '.',
                ','
            ),
        ];
    }

    /**
     * returns table data to show
     * @param
     * @return  array
     */
    public function getData2Show()
    {
        if (!$this->isSettableData($this->getData())) {
            throw new \Exception("Invalid data set.");
        }
        $data2Show = [];
        $data2Show[] = $this->defaultTableHead;
        $data2Show[] = $this->defaultTableColumnAligns;
        $data = $this->getDataOfEachClass();
        $data2Show = array_merge_recursive($data2Show, $data);
        $data2Show[] = $this->getTableTotal2Show($data);
        if ($this->showMean) {
            $data2Show[] = $this->getMean2Show();
        }
        return $data2Show;
    }

    /**
     * returns the data of each class
     * @param
     * @return  array
     */
    public function getDataOfEachClass()
    {
        if (!$this->isSettableData($this->getData())) {
            throw new \Exception("Invalid data set.");
        }
        $data = [];
        $classes = $this->getClasses();
        $frequencies = $this->getFrequencies();
        $fc = [];
        $rf = [];
        foreach ($frequencies as $index => $frequency) {
            $fc[] = $frequency * $this->getClassValue($classes[$index]);
            $rf[] = $this->getRelativeFrequency($frequency);
            $data[] = [
                'Class' => number_format($classes[$index]['bottom'])
                           . $this->classSeparator
                           . number_format($classes[$index]['top']),
                'Frequency' => $frequency,
                'CumulativeFrequency' => $this->getCumulativeFrequency(
                    $frequencies,
                    $index
                ),
                'RelativeFrequency' => number_format($rf[$index], 2, '.', ','),
                'CumulativeRelativeFrequency' => number_format(
                    $this->getCumulativeRelativeFrequency($frequencies, $index),
                    2,
                    '.',
                    ','
                ),
                'ClassValue' => number_format(
                    $this->getClassValue($classes[$index]),
                    1,
                    '.',
                    ','
                ),
                'ClassValue * Frequency' => number_format(
                    $fc[$index],
                    1,
                    '.',
                    ','
                ),
            ];
        }
        return $data;
    }

    /**
     * filters data to show
     * @param   array   $data
     * @return  array
     */
    public function filterData2Show(array $data)
    {
        $columns2Show = $this->getColumns2Show();
        $filtered = [];
        foreach ($data as $index => $row) {
            $filtered[$index] = [];
            foreach ($columns2Show as $c) {
                $filtered[$index][$c] = array_key_exists($c, $row)
                                      ? $row[$c]
                                      : null
                                      ;
            }
        }
        return $filtered;
    }

    /**
     * puts out the frequency table in markdown format to STDOUT
     * @param
     * @return  self
     */
    public function show()
    {
        echo $this->markdown();
        return $this;
    }

    /**
     * returns parsed data
     * @param
     * @return  array
     */
    public function parse()
    {
        if (!$this->isSettableClassRange($this->getClassRange())) {
            throw new \Exception("Invalid class range set.");
        }
        if (!$this->isSettableData($this->getData())) {
            throw new \Exception("Invalid data set.");
        }
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
            'FrequencyTable' => $this->meanOn()->markdown(),
        ];
    }

    /**
     * saves the frequency table into xsv format
     * @param   string  $path
     * @param   string  $separator
     * @param   bool    $quatation = true
     * @param   string  $eol = "\n"
     * @return  null|string|int|bool
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function xsv(
        string $path,
        string $separator,
        bool $quatation = true,
        string $eol = "\n"
    ) {
        if (strlen($separator) === 0) {
            return;
        }
        // no check for $eol : it's at the user's own risk.
        $qm = $quatation ? '"' : '';
        $splitter = $qm . $separator . $qm;
        $buffer = null;
        $buffer .= $qm . implode($splitter, $this->getColumns2Show()) . $qm . $eol;
        $data4EachClass = $this->filterData2Show($this->getDataOfEachClass());
        if (empty($data4EachClass)) {
            return empty($path) ? 'no data' : file_put_contents($path, 'no data');
        }
        foreach ($data4EachClass as $data) {
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

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function csv($path = null, $quatation = true, $eol = "\n")
    {
        return $this->xsv($path, ',', $quatation, $eol);
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function tsv($path = null, $quatation = true, $eol = "\n")
    {
        return $this->xsv($path, "\t", $quatation, $eol);
    }

    public function html($path = null)
    {
        if (null !== $path && !is_string($path)) {
            return;
        }
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
        foreach ($data4EachClass as $data) {
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
        if (null !== $path && !is_string($path)) {
            return;
        }
        $separator = $this->getTableSeparator();
        $eol = "\n";
        $buffer = null;
        $buffer .= $separator . implode($separator, $this->getColumns2Show()) . $separator . $eol;
        $buffer .= $separator . implode($separator, $this->getTableColumnAligns2Show()) . $separator . $eol;
        $data4EachClass = $this->filterData2Show($this->getDataOfEachClass());
        if (empty($data4EachClass)) {
            return empty($path) ? 'no data' : file_put_contents($path, 'no data');
        }
        foreach ($data4EachClass as $data) {
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
        if (!is_string($path)) {
            return;
        }
        if (strlen($path) === 0) {
            return;
        }
        $pathParts = pathinfo($path);
        $extension = strtolower($pathParts['extension']);
        if (!array_key_exists($extension, $this->supportedFormats)) {
            return;
        }
        return $this->{$this->supportedFormats[$extension]}($path);
    }
}
