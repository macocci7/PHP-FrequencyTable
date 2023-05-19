# Class: FrequencyTable

## Methods

<details><summary>list</summary>

- [__construct()](#__construct)
- [isSettableData()](#issettabledata)
- [setData()](#setdata)
- [getData()](#getdata)
- [getDataRange()](#getdatarange)
- [isSettableClassRange()](#issettableclassrange)
- [setClassRange()](#setclassrange)
- [getClassRange()](#getclassrange)
- [getFrequencies()](#getfrequencies)
- [getClasses()](#getclasses)
- [isSettableClass()](#issettableclass)
- [getFrequency()](#getfrequency)
- [getCumulativeFrequency()](#getcumulativefrequency)
- [getMin()](#getmin)
- [getMax()](#getmax)
- [setSum()](#setsum)
- [getSum()](#getsum)
- [getClassValue()](#getclassvalue)
- [getRelativeFrequency()](#getrelativefrequency)
- [getCumulativeRelativeFrequency()](#getcumulativerelativefrequency)
- [getMean()](#getmean)
- [getMode()](#getmode)
- [getMedian()](#getmedian)
- [getMedianClass()](#getmedianclass)
- [getFirstQuartile()](#getfirstquartile)
- [getThirdQuartile()](#getthirdquartile)
- [getInterQuartileRange()](#getinterquartilerange)
- [getQuartileDeviation()](#getquartiledeviation)
- [setTableSeparator()](#settableseparator)
- [getTableSeparator()](#gettableseparator)
- [setDefaultTableSeparator()](#setdefaulttableseparator)
- [getColumns2Show()](#getcolumns2show)
- [getValidColumns2Show()](#getvalidcolumns2show)
- [isSettableColumns2Show()](#issettablecolumns2show)
- [setColumns2Show()](#setcolumns2show)
- [getData2Show()](#getdata2show)
- [filterData2Show()](#filterdata2show)
- [show()](#show)
</details>

### __construct

```php
__construct($param = [])
```
Constructor of this class, supposed to be called at instance creation.

#### Parameter

- Default: [] (Empty Array)
- Type: Hash Array
- Acceptable Keys:
    - `data`: 1 Dimensional (Hash) Array, Values must be Integer or Float.
    - `classRange`: Positive Integer or Positive Float.
    - `columns2Show`: 1 Dimensional (Hash) Array, acceptable values are as follows.
        - `Class`
        - `Frequency`
        - `CumulativeFrequency`
        - `RelativeFrequency`
        - `CumulativeRelativeFrequency`
        - `ClassValue`
        - `ClassValue * Frequency`

#### Example

```php
<?php
require('./class/FrequencyTable.php');
$ft = new FrequencyTable([
    'data' => [0,5,10,15,20],
    'classRange' => 10,
    'columns2Show' => [
        'Class',
        'Frequency',
    ],
]);
```

### isSettableData

```php
isSettableData($data)
```

#### Parameter

#### Example

### setData

```php
setData($data = null)
```

#### Parameter

#### Example

### getData

```php
getData($key = null)
```

#### Parameter

#### Example

### getDataRange

```php
getDataRange($data)
```

#### Parameter

#### Example

### isSettableClassRange

```php
isSettableClassRange($classRange)
```

#### Parameter

#### Example

### setClassRange

```php
setClassRange($classRange = null)
```

#### Parameter

#### Example

### getClassRange

```php
getClassRange()
```

#### Parameter

#### Example

### getFrequencies

```php
getFrequencies()
```

#### Parameter

#### Example

### getClasses

```php
getClasses()
```

#### Parameter

#### Example

### isSettableClass

```php
isSettableClass($class)
```

#### Parameter

#### Example

### getFrequency

```php
getFrequency($data, $class)
```

#### Parameter

#### Example

### getCumulativeFrequency

```php
getCumulativeFrequency($frequencies, $index)
```

#### Parameter

#### Example

### getMin

```php
getMin($data)
```

#### Parameter

#### Example

### getMax

```php
getMax($data)
```

#### Parameter

#### Example

### setSum

```php
setSum($data)
```

#### Parameter

#### Example

### getSum

```php
getSum()
```

#### Parameter

#### Example

### getClassValue

```php
getClassValue($class)
```

#### Parameter

#### Example

### getRelativeFrequency

```php
getRelativeFrequency($frequency)
```

#### Parameter

#### Example

### getCumulativeRelativeFrequency

```php
getCumulativeRelativeFrequency($frequencies, $index)
```

#### Parameter

#### Example

### getMean

```php
getMean()
```

#### Parameter

#### Example

### getMode

```php
getMode()
```

#### Parameter

#### Example

### getMedian

```php
getMedian($param)
```

#### Parameter

#### Example

### getMedianClass

```php
getMedianClass()
```

#### Parameter

#### Example

### getFirstQuartile

```php
getFirstQuartile($data)
```

#### Parameter

#### Example

### getThirdQuartile

```php
getThirdQuartile($data)
```

#### Parameter

#### Example

### getInterQuartileRange

```php
getInterQuartileRange($data)
```

#### Parameter

#### Example

### getQuartileDeviation

```php
getQuartileDeviation($data)
```

#### Parameter

#### Example

### setTableSeparator

```php
setTableSeparator($separator)
```

#### Parameter

#### Example

### getTableSeparator

```php
getTableSeparator()
```

#### Parameter

#### Example

### setDefaultTableSeparator

```php
setDefaultTableSeparator()
```

#### Parameter

#### Example

### getColumns2Show

```php
getColumns2Show()
```

#### Parameter

#### Example

### getValidColumns2Show

```php
getValidColumns2Show()
```

#### Parameter

#### Example

### isSettableColumns2Show

```php
isSettableColumns2Show($columns)
```

#### Parameter

#### Example

### setColumns2Show

```php
setColumns2Show($columns)
```

#### Parameter

#### Example

### getData2Show

```php
getData2Show($option = ['Mean' => true, ])
```

#### Parameter

#### Example

### filterData2Show

```php
filterData2Show($data)
```

#### Parameter

#### Example

### show

```php
show($option = ['Mean' => true, ])
```

#### Parameter

#### Example

