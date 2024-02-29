<?php

namespace Macocci7\PhpFrequencyTable;

use Macocci7\PhpFrequencyTable\Helper\Config;

/**
 * Creates A Frequency Distribution Table
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
class FrequencyTable
{
    private mixed $data = null;
    private mixed $classRange = null;
    private mixed $total = null;
    private string $defaultTableSeparator = '|';
    private string $classSeparator = ' ~ ';
    private mixed $tableSeparator = null;
    /**
     * @var string[]    $validColumns2Show
     */
    private array $validColumns2Show;
    /**
     * @var string[]    $defaultColumns2Show
     */
    private array $defaultColumns2Show;
    /**
     * @var string[]    $columns2Show
     */
    private array $columns2Show = [];
    /**
     * @var string[]    $defaultTableColumnAligns
     */
    private array $defaultTableColumnAligns;
    private string $lang;
    /**
     * @var array<string, array<string, string>>
     */
    private array $supportedLangs;
    /**
     * @var string[]    $supportedFormats
     */
    private array $supportedFormats;
    private bool $showMean = false;

    /**
     * constructor
     * @param   mixed   $param = []
     */
    public function __construct(mixed $param = [])
    {
        $this->loadConf();
        if (array_key_exists('classRange', $param)) { // @phpstan-ignore-line
            $this->setClassRange($param['classRange']);
        }
        if (array_key_exists('data', $param)) { // @phpstan-ignore-line
            $this->setData($param['data']);
        }
        $this->setColumns2Show($this->defaultColumns2Show);
        if (array_key_exists('columns2Show', $param)) { // @phpstan-ignore-line
            $this->setColumns2Show($param['columns2Show']);
        }
        $this->setDefaultTableSeparator();
    }

    /**
     * loads config.
     * @return  void
     */
    private function loadConf()
    {
        Config::load();
        $props = [
            'validColumns2Show',
            'defaultColumns2Show',
            'defaultTableColumnAligns',
            'lang',
            'supportedLangs',
            'supportedFormats',
        ];
        foreach ($props as $prop) {
            $this->{$prop} = Config::get($prop); // @phpstan-ignore-line
        }
    }

    /**
     * returns supported langs
     * @return  string[]
     */
    public function langs()
    {
        return array_keys($this->supportedLangs);
    }

    /**
     * sets lang or returns current lang
     * @param   string  $lang = null
     * @return  self|string
     */
    public function lang(string $lang = null)
    {
        if (is_null($lang)) {
            return $this->lang;
        }
        if (isset($this->supportedLangs[$lang])) {
            $this->lang = $lang;
        }
        return $this;
    }

    /**
     * sets visibility of mean on
     * @return  self
     */
    public function meanOn()
    {
        $this->showMean = true;
        return $this;
    }

    /**
     * sets visibility of mean off
     * @return  self
     */
    public function meanOff()
    {
        $this->showMean = false;
        return $this;
    }

    /**
     * judges if the param is number or not
     * @param   mixed   $value
     * @return  bool
     */
    public function isNumber(mixed $value)
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
     * @param   mixed   $data   default = null
     * @return  self
     */
    public function setData(mixed $data)
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
     * @param   int|string  $key = null
     * @return  mixed
     */
    public function getData(int|string $key = null)
    {
        // @phpstan-ignore-next-line
        return is_null($key) ? $this->data : ($this->data[$key] ?? null);
    }

    /**
     * returns data range
     * @param   mixed   $data
     * @return  int|float|null
     */
    public function getDataRange(mixed $data)
    {
        if (!$this->isSettableData($data)) {
            return null;
        }
        return $this->getMax($data) - $this->getMin($data);
    }

    /**
     * judges if the param is valid class range or not
     * @param   mixed   $classRange
     * @return  bool
     */
    public function isSettableClassRange(mixed $classRange)
    {
        if (!$this->isNumber($classRange)) {
            return false;
        }
        return $classRange > 0;
    }

    /**
     * sets class range
     * @param   mixed   $classRange
     * @return  self
     */
    public function setClassRange(mixed $classRange = null)
    {
        if ($this->isSettableClassRange($classRange)) {
            $this->classRange = $classRange;
        } else {
            $this->classRange = null;
        }
        return $this;
    }

    /**
     * returns class range
     * @return  mixed
     */
    public function getClassRange()
    {
        return $this->classRange;
    }

    /**
     * returns frequencies
     * @return  array<int|null>
     */
    public function getFrequencies()
    {
        $frequencies = [];
        foreach ($this->getClasses() as $class) {
            $frequencies[] = $this->getFrequency($this->data, $class);
        }
        return $frequencies;
    }

    /**
     * returns classes
     * @return  list<array<string, int|float>>
     */
    public function getClasses()
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
        return $class;
    }

    /**
     * judges if the param is valid class or not
     * @param   mixed   $class
     * @return  bool
     */
    public function isSettableClass(mixed $class)
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
     * returns frequency of the specified class
     * @param   mixed   $data
     * @param   mixed   $class
     * @return  int|null
     */
    public function getFrequency(mixed $data, mixed $class)
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
     * @param   mixed   $frequencies
     * @param   mixed   $index
     * @return  int|null
     */
    public function getCumulativeFrequency(mixed $frequencies, mixed $index)
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
     * returns min value of the specified array
     * @param   mixed   $data
     * @return  int|float|null
     */
    public function getMin(mixed $data)
    {
        return $this->isSettableData($data) ? min($data) : null; // @phpstan-ignore-line
    }

    /**
     * returns max value of the specified array
     * @param   mixed   $data
     * @return  int|float|null
     */
    public function getMax(mixed $data)
    {
        return $this->isSettableData($data) ? max($data) : null; // @phpstan-ignore-line
    }

    /**
     * sets total of the specified data
     * @param   mixed   $data
     * @return  self
     */
    public function setTotal(mixed $data)
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
     * @return  mixed
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
     * @param   mixed   $class
     * @return  int|float|null
     */
    public function getClassValue(mixed $class)
    {
        if (!$this->isSettableClass($class)) {
            return null;
        }
        return ($class['bottom'] + $class['top']) / 2; // @phpstan-ignore-line
    }

    /**
     * returns relative frequency
     * @param   mixed   $frequency
     * @return  int|float|null
     */
    public function getRelativeFrequency(mixed $frequency)
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
     * @param   mixed   $frequencies
     * @param   mixed   $index
     * @return  int|float|null
     */
    public function getCumulativeRelativeFrequency(mixed $frequencies, mixed $index)
    {
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

    /**
     * returns mean value
     * @return  int|float|null
     */
    public function getMean()
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
     * @return  int|float|null
     */
    public function getMode()
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
     * @param   mixed   $param
     * @return  int|float|null
     */
    public function getMedian(mixed $param)
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

    /**
     * returns the class the median belongs
     * @return  array<string, int|float>|null
     */
    public function getMedianClass()
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

    /**
     * returns the first quartile
     * @param   mixed   $data
     * @return  int|float|null
     */
    public function getFirstQuartile(mixed $data)
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
     * @param   mixed   $data
     * @return  int|float|null
     */
    public function getThirdQuartile(mixed $data)
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
     * @param   mixed   $data
     * @return  int|float|null
     */
    public function getInterQuartileRange(mixed $data)
    {
        if (!$this->isSettableData($data)) {
            return null;
        }
        return $this->getThirdQuartile($data) - $this->getFirstQuartile($data);
    }

    /**
     * returns the quartile deviation
     * @param   mixed   $data
     * @return  int|float|null
     */
    public function getQuartileDeviation(mixed $data)
    {
        if (!$this->isSettableData($data)) {
            return null;
        }
        return $this->getInterQuartileRange($data) / 2;
    }

    /**
     * returns table head
     * @return  string[]
     */
    public function getTableHead()
    {
        $conf = $this->supportedLangs[$this->lang()]; // @phpstan-ignore-line
        $heads = [];
        foreach ($this->getColumns2Show() as $c) {
            $heads[] = $conf[$c] ?? $c;
        }
        return $heads;
    }

    /**
     * sets the table separator to show frequency table
     * @param   mixed   $separator
     * @return  self
     */
    public function setTableSeparator(mixed $separator)
    {
        if (is_string($separator)) {
            $this->tableSeparator = $separator;
        }
        return $this;
    }

    /**
     * returns the current table separator
     * @return  mixed
     */
    public function getTableSeparator()
    {
        return $this->tableSeparator;
    }

    /**
     * sets the default table separator
     * @return  self
     */
    public function setDefaultTableSeparator()
    {
        $this->setTableSeparator($this->defaultTableSeparator);
        return $this;
    }

    /**
     * returns table column aligns to show
     * @return  array<int|string, string>
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
     * @return  string[]
     */
    public function getColumns2Show()
    {
        return $this->columns2Show;
    }

    /**
     * returns valid columns to show
     * @return  string[]
     */
    public function getValidColumns2Show()
    {
        return $this->validColumns2Show;
    }

    /**
     * judges if the param is valid for columns to show or not
     * @param   mixed   $columns
     * @return  bool
     */
    public function isSettableColumns2Show(mixed $columns)
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

    /**
     * sets columns to show
     * @param   mixed   $columns
     * @return  self
     */
    public function setColumns2Show(mixed $columns)
    {
        if ($this->isSettableColumns2Show($columns)) {
            $this->columns2Show = $columns; // @phpstan-ignore-line
        }
        return $this;
    }

    /**
     * returns totals in the table to show
     * @param   mixed   $data
     * @return  array<string, mixed>
     */
    public function getTableTotal2Show(mixed $data)
    {
        return [
            'Class' => $this->supportedLangs[$this->lang()]['Total'] ?? 'Total', // @phpstan-ignore-line
            'Frequency' => $this->getTotal(),
            'CumulativeFrequency' => $this->getTotal(),
            'RelativeFrequency' => array_sum(array_column($data, 'RelativeFrequency')), // @phpstan-ignore-line
            // @phpstan-ignore-next-line
            'CumulativeRelativeFrequency' => array_sum(array_column($data, 'RelativeFrequency')),
            'ClassValue' => '---',
            // @phpstan-ignore-next-line
            'ClassValue * Frequency' => array_sum(array_column($data, 'ClassValue * Frequency')),
        ];
    }

    /**
     * returns mean row in the table to show
     * @return  array<string, string|int|float|null>
     */
    public function getMean2Show()
    {
        return [
            // @phpstan-ignore-next-line
            'Class' => $this->supportedLangs[$this->lang()]['Mean'] ?? 'Mean',
            'Frequency' => '---',
            'CumulativeFrequency' => '---',
            'RelativeFrequency' => '---',
            'CumulativeRelativeFrequency' => '---',
            'ClassValue' => '---',
            'ClassValue * Frequency' => $this->getMean(),
        ];
    }

    /**
     * returns the data of each class
     * @return  list<array<string, int|float|string|null>>
     */
    public function getDataOfEachClass()
    {
        if (!$this->isSettableData($this->getData())) {
            return [];
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
                'RelativeFrequency' => $rf[$index],
                'CumulativeRelativeFrequency' => $this->getCumulativeRelativeFrequency(
                    $frequencies,
                    $index
                ),
                'ClassValue' => $this->getClassValue($classes[$index]),
                'ClassValue * Frequency' => $fc[$index],
            ];
        }
        return $data;
    }

    /**
     * filters data to show
     * @param   list<array<string, mixed>>    $data
     * @return  list<array<string, mixed>>
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
     * formats data to show
     * @param   list<array<string, int|float|string|null>>   $data
     * @return  list<array<string, int|float|string|null>>
     */
    private function formatData2Show(array $data)
    {
        $f = Config::get('columnNumberFormat');
        foreach ($data as $i => $d) {
            foreach ($d as $k => $v) {
                if (isset($f[$k]) && is_numeric($v)) { // @phpstan-ignore-line
                    $data[$i][$k] = number_format(
                        (float) $v,
                        $f[$k]['decimals'] ?? 0, // @phpstan-ignore-line
                        $f[$k]['decimal_separator'] ?? '', // @phpstan-ignore-line
                        $f[$k]['thousands_separator'] ?? '' // @phpstan-ignore-line
                    );
                }
            }
        }
        return $data;
    }

    /**
     * returns table data
     * @return mixed
     */
    public function getTableData()
    {
        return [
            'tableHead' => $this->getTableHead(),
            'classData' => $this->formatData2Show(
                // @phpstan-ignore-next-line
                $this->filterData2Show($this->getDataOfEachClass())
            ),
            'total' => $this->formatData2Show(
                // @phpstan-ignore-next-line
                $this->filterData2Show(
                    [$this->getTableTotal2Show($this->getDataOfEachClass())]
                )
            )[0],
            'mean' => $this->formatData2Show(
                // @phpstan-ignore-next-line
                $this->filterData2Show([$this->getMean2Show()])
            )[0],
        ];
    }

    /**
     * returns parsed data
     * @return  null|array<string, mixed>
     */
    public function parse()
    {
        if (!$this->isSettableClassRange($this->getClassRange())) {
            return null;
        }
        if (!$this->isSettableData($this->getData())) {
            return null;
        }
        return [
            'classRange' => $this->getClassRange(),
            'data' => $this->getData(),
            'Max' => $this->getMax($this->getData()),
            'Min' => $this->getMin($this->getData()),
            'DataRange' => $this->getDataRange($this->getData()),
            'Mode' => $this->getMode(),
            'Total' => $this->getTotal(),
            'Mean' => $this->getMean(),
            'Median' => $this->getMedian($this->getData()),
            'MedianClass' => $this->getMedianClass(),
            'FirstQuartile' => $this->getFirstQuartile($this->getData()),
            'ThirdQuartile' => $this->getThirdQuartile($this->getData()),
            'InterQuartileRange' => $this->getInterQuartileRange($this->getData()),
            'QuartileDeviation' => $this->getQuartileDeviation($this->getData()),
            'Classes' => $this->getClasses(),
            'Frequencies' => $this->getFrequencies(),
            'FrequencyTable' => $this->getTableData(),
        ];
    }

    /**
     * saves or returns the frequency table in xsv format
     * @param   string|null $path
     * @param   string      $separator
     * @param   string      $quotation = '"'
     * @param   string      $eol = "\n"
     * @return  null|string|int|bool
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function xsv(
        string|null $path,
        string $separator,
        string $quotation = '"',
        string $eol = "\n"
    ) {
        if (empty($separator)) {
            return null;
        }
        $qm = $quotation;
        $splitter = $qm . $separator . $qm;
        $buffer = null;
        $buffer .= $qm . implode($splitter, $this->getTableHead()) . $qm . $eol;
        $data4EachClass = $this->filterData2Show($this->getDataOfEachClass());
        foreach ($this->formatData2Show($data4EachClass) as $data) { // @phpstan-ignore-line
            $buffer .= $qm . implode($splitter, $data) . $qm . $eol;
        }
        $totals = $this->formatData2Show(
            $this->filterData2Show( // @phpstan-ignore-line
                [$this->getTableTotal2Show($data4EachClass)]
            )
        );
        $buffer .= $qm . implode($splitter, $totals[0]) . $qm . $eol;
        if ($this->showMean) {
            $means = $this->formatData2Show(
                $this->filterData2Show([$this->getMean2Show()]) // @phpstan-ignore-line
            );
            $buffer .= $qm . implode($splitter, $means[0]) . $qm . $eol;
        }
        return empty($path) ? $buffer : file_put_contents($path, $buffer);
    }

    /**
     * saves or returns the frequency table in csv format
     * @param   string|null     $path = null
     * @param   string          $quotation = '"'
     * @param   string          $eol = "\n"
     * @return  null|string|int|bool
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function csv(
        string|null $path = null,
        string $quotation = '"',
        string $eol = "\n"
    ) {
        return $this->xsv($path, ',', $quotation, $eol);
    }

    /**
     * saves or returns the frequency table in tsv format
     * @param   string|null     $path = null
     * @param   string          $quotation = '"'
     * @param   string          $eol = "\n"
     * @return  null|string|int|bool
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function tsv(
        string|null $path = null,
        string $quotation = '"',
        string $eol = "\n"
    ) {
        return $this->xsv($path, "\t", $quotation, $eol);
    }

    /**
     * saves or returns the frequency table in html format
     * @param   string|null   $path = null
     * @return  null|string|int|bool
     */
    public function html(string|null $path = null)
    {
        $eol = "\n";
        $data4EachClass = $this->filterData2Show($this->getDataOfEachClass());
        if (empty($data4EachClass)) {
            return empty($path) ? 'no data' : file_put_contents($path, 'no data');
        }
        $buffer = "<table>" . $eol;
        $pre = '<tr><th>';
        $pro = '</th></tr>';
        $splitter = '</th><th>';
        $buffer .= $pre . implode($splitter, $this->getTableHead()) . $pro . $eol;
        $pre = '<tr><td>';
        $pro = '</td></tr>';
        $splitter = '</td><td>';
        foreach ($this->formatData2Show($data4EachClass) as $data) { // @phpstan-ignore-line
            $buffer .= $pre . implode($splitter, $data) . $pro . $eol;
        }
        $totals = $this->formatData2Show(
            $this->filterData2Show( // @phpstan-ignore-line
                [$this->getTableTotal2Show($data4EachClass)]
            )
        );
        $buffer .= $pre . implode($splitter, $totals[0]) . $pro . $eol;
        if ($this->showMean) {
            $means = $this->formatData2Show(
                $this->filterData2Show([$this->getMean2Show()]) // @phpstan-ignore-line
            );
            $buffer .= $pre . implode($splitter, $means[0]) . $pro . $eol;
        }
        $buffer .= "</table>" . $eol;
        return empty($path) ? $buffer : file_put_contents($path, $buffer);
    }

    /**
     * saves or returns the frequency table in markdown format
     * @param   string|null   $path = null
     * @return  null|string|int|bool
     */
    public function markdown(string|null $path = null)
    {
        $separator = $this->getTableSeparator();
        $eol = "\n";
        $buffer = null;
        // @phpstan-ignore-next-line
        $buffer .= $separator . implode($separator, $this->getTableHead()) . $separator . $eol;
        // @phpstan-ignore-next-line
        $buffer .= $separator . implode($separator, $this->getTableColumnAligns2Show()) . $separator . $eol;
        $data4EachClass = $this->filterData2Show($this->getDataOfEachClass());
        if (empty($data4EachClass)) {
            return empty($path) ? 'no data' : file_put_contents($path, 'no data');
        }
        foreach ($this->formatData2Show($data4EachClass) as $data) { // @phpstan-ignore-line
            // @phpstan-ignore-next-line
            $buffer .= $separator . implode($separator, $data) . $separator . $eol;
        }
        $totals = $this->formatData2Show(
            $this->filterData2Show( // @phpstan-ignore-line
                [$this->getTableTotal2Show($data4EachClass)]
            )
        );
        // @phpstan-ignore-next-line
        $buffer .= $separator . implode($separator, $totals[0]) . $separator . $eol;
        if ($this->showMean) {
            $means = $this->formatData2Show(
                $this->filterData2Show([$this->getMean2Show()]) // @phpstan-ignore-line
            );
            // @phpstan-ignore-next-line
            $buffer .= $separator . implode($separator, $means[0]) . $separator . $eol;
        }
        return empty($path) ? $buffer : file_put_contents($path, $buffer);
    }

    /**
     * saves the frequency table into a file
     * @param   string  $path
     * @return  int|bool
     */
    public function save(string $path)
    {
        if (strlen($path) === 0) {
            return false;
        }
        $pathParts = pathinfo($path);
        // @phpstan-ignore-next-line
        $extension = strtolower($pathParts['extension']);
        if (!array_key_exists($extension, $this->supportedFormats)) {
            return false;
        }
        return $this->{$this->supportedFormats[$extension]}($path);
    }
}
