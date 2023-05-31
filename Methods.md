# Class: Macocci7\PhpFrequencyTable\FrequencyTable


[README](README.md)

## Methods

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
- [setTotal()](#settotal)
- [getTotal()](#gettotal)
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
- [getColumns2Show()](#getcolumns2show)
- [getValidColumns2Show()](#getvalidcolumns2show)
- [setColumns2Show()](#setcolumns2show)
- [show()](#show)
- [parse()](#parse)
- [csv()](#csv)
- [tsv()](#tsv)
- [html()](#html)

***

### __construct

```php
__construct($param = [])
```
Constructor of this class, supposed to be called at instance creation.

#### Parameters

> $param
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

#### Return

void

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable([
        'data' => [0,5,10,15,20],
        'classRange' => 10,
        'columns2Show' => [
            'Class',
            'Frequency',
        ],
    ]);
    ```

***

### isSettableData

```php
isSettableData($data)
```

Judges whether `$data` is valid or not for FrequencyTable operation.

#### Parameter
> $data
- Type: 1 Dimensional (Hash) Array.
- Values: must be integer or float.

#### Return

- Type: bool
- Value:
    - true: `$data` is valid.
    - false: `$data` is invalid.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $data = [0,1,2];
    var_dump($ft->isSettableData($data));

    $data = ['Donald'=>62979636,'Hillary'=>65844610];
    var_dump($ft->isSettableData($data));

    $data = ['Donald'=>'74,223,975','Joe'=>'81,283,501'];
    var_dump($ft->isSettableData($data));
    ```

- Result
    ```bash
    bool(true)
    bool(true)
    bool(false)
    ```

***

### setData

```php
setData($data = null)
```

Sets `$data` as a data set for FrequencyTable's operation.

FrequencyTable keeps holding the data set until other data is given.

FrequencyTable release the data set when invalid data set is given.

#### Parameter

> $data

- Default: `null`
- Type: 1 Dimensional (Hash) Array.
- Values: must be integer or float.

#### Return

- Type: bool
- Value:
    - true: `$data` is valid and set.
    - false: `$data` is invalid and `null` is set.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $data = [0,1,2];
    var_dump($ft->setData($data));
    var_dump($ft->getData());

    $data = ['Donald'=>62979636,'Hillary'=>65844610];
    var_dump($ft->setData($data));
    var_dump($ft->getData());

    $data = ['Donald'=>'74,223,975','Joe'=>'81,283,501'];
    var_dump($ft->setData($data));
    var_dump($ft->getData());
    ```

- Result

    ```bash
    bool(true)
    array(3) {
    [0]=>
    int(0)
    [1]=>
    int(1)
    [2]=>
    int(2)
    }
    bool(true)
    array(2) {
    ["Donald"]=>
    int(62979636)
    ["Hillary"]=>
    int(65844610)
    }
    bool(false)
    NULL
    ```

***

### getData

```php
getData($key = null)
```

Returns array data set FrequencyTable holds with no prameter.

It retruns the value specified by `$key` in the data set FrequencyTable holds

when `$key` is given as a parameter.

#### Parameter

> $key
- Default: null
- Type: Integer or String

#### Return

- Type: Array or Integer or Float

    getData() returns Array when parameter is not given.

    It returns Integer or Float when parameter `$key` is specified.

    It returns `null` if `$key` does not exist in the data set.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $data = ['Donald'=>62979636,'Hillary'=>65844610];
    $ft->setData($data);
    var_dump($ft->getData());
    var_dump($ft->getData('Donald'));
    var_dump($ft->getData('Joe'));
    ```

- Result
    ```bash
    array(2) {
    ["Donald"]=>
    int(62979636)
    ["Hillary"]=>
    int(65844610)
    }
    int(62979636)
    NULL
    ```

***

### getDataRange

```php
getDataRange($data)
```

Returns Data Range of `$data`.

Data Range is culculated as:

`MAX - MIN`

#### Parameter

> $data
- Type: 1 Dimensional (Hash) Array
- Values: must be Integer or Float.

#### Return

- Type: Integer or Float or null

getDataRange() returns null when `$data` is invalid.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $data = [ 20, -4, 0, -10.5, 5, ];
    var_dump($ft->getDataRange($data));
    ```

- Result

    ```bash
    float(30.5)
    ```

***

### isSettableClassRange

```php
isSettableClassRange($classRange)
```

Judges whether `$classRange` is valid or not.

#### Parameter

> $classRange
- Type: Integer or Float.
- Value: must be Positive.

#### Return

- Type: bool
- Value:
    - true: `$classRange` is valid.
    - false: `$classRange` is invalid.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    var_dump($ft->isSettableClassRange("10"));
    var_dump($ft->isSettableClassRange(10));
    var_dump($ft->isSettableClassRange(0.5));
    var_dump($ft->isSettableClassRange(-10));
    var_dump($ft->isSettableClassRange(-0.5));
    ```

- Result
    ```bash
    bool(false)
    bool(true)
    bool(true)
    bool(false)
    bool(false)
    ```

***

### setClassRange

```php
setClassRange($classRange = null)
```

Sets `$classRange` as the Class Range of FrequencyTable.

FrequencyTable holds the Class Range Value until other Value is set.

#### Parameter

> $classRange
- Default: `null`
- Type: Integer or Float
- Value: must be Positive.

#### Return

- Type: bool
- Value:
    - true: `$classRange` is valid and set.
    - false: `$classRange` is invalid and `null` is set.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    var_dump($ft->setClassRange(10.5));
    var_dump($ft->getClassRange());
    var_dump($ft->setClassRange(-10.5));
    var_dump($ft->getClassRange());
    ```

- Result
    ```bash
    bool(true)
    float(10.5)
    bool(false)
    NULL
    ```

***

### getClassRange

```php
getClassRange()
```

Returns the Class Range FrequencyTable holds.

#### Return

- Type: Integer or Float

***

### getFrequencies

```php
getFrequencies()
```

Returns Frequencies of the data set FrequencyTable holds.

Before using this method, you should set Class Range and data.

See Example.

#### Return

- Type: 1 Dimensional Array

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $ft->setClassRange(10);
    $ft->setData([0,5,10,15,20]);
    var_dump($ft->getFrequencies());
    ```

- Result
    ```bash
    array(3) {
    [0]=>
    int(2)
    [1]=>
    int(2)
    [2]=>
    int(1)
    }
    ```

***

### getClasses

```php
getClasses()
```

Returns Classes as 2 Dimensional Array.

Before using this method, you should set Class Range and data.

See Example.

#### Return

- Type: 2 Dimensional Array
- Value: structures are like as follows.

    ```php
    [
        0 => [ 'bottom' => 0, 'top' => 10, ],
        1 => [ 'bottom' => 10, 'top' => 20, ],
        2 => [ 'bottom' => 20, 'top' => 30, ],
    ]
    ```
- Note: getClasses() returns empty array `[]` if valid Class Range and data are not set.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    var_dump($ft->getClasses());
    $ft->setClassRange(10);
    $ft->setData([0,5,10,15,20]);
    var_dump($ft->getClasses());
    ```

- Result
    ```bash
    array(0) {
    }
    array(3) {
    [0]=>
    array(2) {
        ["bottom"]=>
        int(0)
        ["top"]=>
        int(10)
    }
    [1]=>
    array(2) {
        ["bottom"]=>
        int(10)
        ["top"]=>
        int(20)
    }
    [2]=>
    array(2) {
        ["bottom"]=>
        int(20)
        ["top"]=>
        int(30)
    }
    }
    ```

***

### isSettableClass

```php
isSettableClass($class)
```

Judges whether `$class` is valid or not.

#### Parameter

> $class
- Type: Hash Array
- Structure: The keys `bottom` and `top` must be included. Any other keys are ignored.
    ```php
    $class = [
        'bottom' => 10,     // 'bottom' must be less than 'top'.
        'top' => 20,        // 'top' must be greater than 'bottom'.
        'classValue' => 15, // This key is ignored.
    ];
    ```
- Values:
    - must be Integer or Float.
    - `bottom` must be less than `top`.

#### Return

- Type: bool
- Value:
    - true: `$class` is valid.
    - false: `$class` is invalid.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $class = ['bottom' => 20, 'top' => 10,];
    var_dump($ft->isSettableClass($class));

    $class = ['bottom' => 10, 'top' => 20, 'classValue' => 15,];
    var_dump($ft->isSettableClass($class));
    ```

- Result
    ```bash
    bool(false)
    bool(true)
    ```

***

### getFrequency

```php
getFrequency($data, $class)
```

Returns the Frequency belongs to the specified `$class` from the specified `$data`,

not form the data set FrequencyTable holds.

#### Parameters

> $data
- Type: 1 Dimensional (Hash) Array.
- Values: must be Integer or Float.

> $class
- Type: 1 Dimensional Hash Array.
- Structure: The keys `bottom` and `top` must be included. Any other keys are ignored.
    ```php
    $class = [
        'bottom' => 10,     // 'bottom' must be less than 'top'.
        'top' => 20,        // 'top' must be greater than 'bottom'.
        'classValue' => 15, // This key is ignored.
    ];
    ```
- Values:
    - must be Integer or Float.
    - `bottom` must be less than `top`.

#### Return

- Type: Integer or `null`
- Value:
    - `null`: `$data` or `$Class` is invalid.
    - Integer: is the Frequency belongs to the specified `$class`.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $data = [0,10,15,20,25,29,30];

    $class = ['bottom' => 20, 'top' => 10,];
    var_dump($ft->getFrequency($data, $class));

    $class = ['bottom' => 10, 'top' => 20, 'classValue' => 15,];
    var_dump($ft->getFrequency($data, $class));

    $class = ['bottom' => 20, 'top' => 30, 'classValue' => 25,];
    var_dump($ft->getFrequency($data, $class));
    ```

- Result
    ```bash
    NULL
    int(2)
    int(3)
    ```

***

### getCumulativeFrequency

```php
getCumulativeFrequency($frequencies, $index)
```

Returns the Cumulative Frequency of the Class specified by Array `$index` of Array `$frequencies`.

#### Parameters

> $frequencies
- Type: 1 Dimensional Array
- Values: must be Integer.

> $index
- Type: Integer
- Value:
    - Zero or More than zero.
    - Less than the length of Array `$frequencies`.

#### Return

- Type: Integer or `null`
- Value:
    - `null`: `$frequencies` or `$index` is invalid.
    - Integer: Cumulative Frequency

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $frequencies = [1,2,3,4];

    var_dump($ft->getCumulativeFrequency($frequencies, -1)); // null
    var_dump($ft->getCumulativeFrequency($frequencies, 0));  // 1
    var_dump($ft->getCumulativeFrequency($frequencies, 1));  // 3
    var_dump($ft->getCumulativeFrequency($frequencies, 2));  // 6
    var_dump($ft->getCumulativeFrequency($frequencies, 3));  // 10
    var_dump($ft->getCumulativeFrequency($frequencies, 4));  // null
    ```

- Result
    ```bash
    NULL
    int(1)
    int(3)
    int(6)
    int(10)
    NULL
    ```

***

### getMin

```php
getMin($data)
```

Returns the Min(imum) Value of the data set `$data`.

#### Parameter

> $data
- Type: 1 Dimensional (Hash) Array
- Values: must be Integer or Float.

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: `$data` is invalid.
    - Integer or Float: Min(imum) Value of the data set `$data`.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $data = [1,"2",3,4];
    var_dump($ft->getMin($data)); // null
    $data = [1,3,4,2];
    var_dump($ft->getMin($data)); // 1
    ```

- Result
    ```bash
    NULL
    int(1)
    ```

***

### getMax

```php
getMax($data)
```

Returns the Max(imum) Value of the data set `$data`.

#### Parameter

> $data
- Type: (Hash) Array
- Values: must be Integer or Float.

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: `$data` is invalid.
    - Integer or Float: the Max(imum) Value of the data set `$data`.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $data = [1,"2",3,4];
    var_dump($ft->getMax($data)); // null
    $data = [1,3,4,2];
    var_dump($ft->getMax($data)); // 4
    ```

- Result
    ```bash
    NULL
    int(4)
    ```

***

### setTotal

```php
setTotal($data)
```

Sets the Total of `$data`.

The Value of Total FrequencyTable holds is overwritten when setData() is called,

or if the Value of Total is `null` when getTotal() is called.

#### Parameter

> $data
- Type: 1 Dimensional (Hash) Array
- Values: must be Integer or Float.

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: `$data` is invalid.
    - Integer or Float: Total of `$data`.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $ft->setClassRange(2);

    $data = [1,"2",3,4];
    var_dump($ft->setTotal($data)); // false
    var_dump($ft->getTotal());     // null

    $data = [1,3,4,2];
    var_dump($ft->setTotal($data)); // true
    var_dump($ft->getTotal());      // 10

    $data = [1.5,3.5,4.5,2.0];
    var_dump($ft->setTotal($data)); // true
    var_dump($ft->getTotal());      // 11.5
    ```

- Result
    ```bash
    bool(false)
    NULL
    bool(true)
    int(10)
    bool(true)
    float(11.5)
    ```

***

### getTotal

```php
getTotal()
```

Returns the Value of Total FrequencyTable holds.

The Value of Total is reculculated with the data set FrequencyTable holds,

if the Value of Total is `null` when getTotal() is called.

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: the data set FrequencyTable holds is `null`.
    - Integer or Float: Total of data set FrequencyTable holds.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $ft->setClassRange(2);

    $data = [1,"2",3,4];
    var_dump($ft->setData($data)); // false
    var_dump($ft->getTotal());     // null

    $data = [1,3,4,2];
    var_dump($ft->setData($data)); // true
    var_dump($ft->getTotal());      // 4 : The total of Frequencies, i.e., the number of elements.

    $data = [1.5,3.5,4.5,2.0];
    var_dump($ft->setData($data)); // true
    var_dump($ft->getTotal());      // 4 : The total of Frequencies, i.e., the number of elements.
    ```

- Result
    ```bash
    bool(false)
    NULL
    bool(true)
    int(4)
    bool(true)
    int(4)
    ```

***

### getClassValue

```php
getClassValue($class)
```

Returns the Class Value of the `$class`.

#### Parameter

> $classs
- Type: 1 Dimensional Hash Array
- Structure: The key `bottom` and `top` must be included. Any other key is ignored.
    ```php
    $class = [
        'bottom' => 10,     // 'bottom' must be less than 'top'.
        'top' => 20,        // 'top' must be greater than 'bottom'.
        'classValue' => 15, // This key is ignored.
    ];
    ```
- Values:
    - must be Integer or Float.
    - `bottom` must be less than `top`.

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: `$class` is invalid.
    - Integer or Float: The Class Value

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $class = ['bottom' => 20, 'top' => 10, ];
    var_dump($ft->getClassValue($class)); // null

    $class = ['bottom' => 10, 'top' => 20, ];
    var_dump($ft->getClassValue($class)); // 15

    $class = ['bottom' => 20, 'top' => 30, 'classValue' => null, ];
    var_dump($ft->getClassValue($class)); // 25: 'classValue' is ignored.
    ```

- Result
    ```bash
    NULL
    int(15)
    int(25)
    ```

***

### getRelativeFrequency

```php
getRelativeFrequency($frequency)
```

Returns Relative Frequency of `$frequency`.

Before using this method, you should set Total by calling setData() or setTotal().

#### Parameter

> $frequency
- Type: Integer
- Value:
    - must be a Positive Integer or Zero.
    - must be Less than or Equal to Total.

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: Total or `$frequency` is invalid.
    - Integer or Float: Relative Frequency

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $data = [0,10,15,20,];
    $ft->setClassRange(10);
    $ft->setData($data);

    var_dump($ft->getRelativeFrequency(-1));
    var_dump($ft->getRelativeFrequency(0));
    var_dump($ft->getRelativeFrequency(1));
    var_dump($ft->getRelativeFrequency(2));
    var_dump($ft->getRelativeFrequency(3));
    var_dump($ft->getRelativeFrequency(4));
    var_dump($ft->getRelativeFrequency(5));
    ```

- Result
    ```bash
    NULL
    int(0)
    float(0.25)
    float(0.5)
    float(0.75)
    int(1)
    NULL
    ```

### getCumulativeRelativeFrequency

```php
getCumulativeRelativeFrequency($frequencies, $index)
```

Returns the Cumulative Relative Frequency of  the Class specified by Array `$index` of Array `$frequencies`.

Before using this method, you should set Total using setTotal() or setData().

#### Parameters

> $frequencies
- Type: 1 Dimensional (Hash) Array
- Values: must be Integer or Float.

> $index
- Type: Integer
- Value:
    - Zero or More than Zero
    - Less than the Length of Array `$frequencies`

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: `$frequencies` or `$index` is invalid.
    - Integer or Float: Cumulative Relative Frequency

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $frequencies = [0,1,2,3,4,];

    $ft->setTotal($frequencies);
    var_dump($ft->getCumulativeRelativeFrequency($frequencies, -1));
    var_dump($ft->getCumulativeRelativeFrequency($frequencies, 0));
    var_dump($ft->getCumulativeRelativeFrequency($frequencies, 1));
    var_dump($ft->getCumulativeRelativeFrequency($frequencies, 2));
    var_dump($ft->getCumulativeRelativeFrequency($frequencies, 3));
    var_dump($ft->getCumulativeRelativeFrequency($frequencies, 4));
    var_dump($ft->getCumulativeRelativeFrequency($frequencies, 5));
    ```

- Result
    ```bash
    NULL
    int(0)
    float(0.1)
    float(0.30000000000000004)
    float(0.6000000000000001)
    float(1)
    NULL
    ```

***

### getMean

```php
getMean()
```

Returns the Mean Value of the data set FrequencyTable holds.

Before using this method, you should set Class Range and data.

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: the data set FrequencyTable holds or Total is invalid.
    - Integer or Float: Mean Value

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $ft->setClassRange(10);

    var_dump($ft->getMean());

    $ft->setData([0]);
    var_dump($ft->getMean());

    $ft->setData([0,10]);
    var_dump($ft->getMean());

    $ft->setData([0,5,10,15,20]);
    var_dump($ft->getMean());
    ```

- Result
    ```bash
    NULL
    int(5)
    int(10)
    int(13)
    ```

***

### getMode

```php
getMode()
```

Returns Mode of the data set FrequencyTable holds.

Mode is the Class Value of the Class which has the most greeatest Frequency.

Before using this method, you should set Class Range and data.

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: the data set FrequencyTable holds is invalid.
    - Integer or Float: Mode

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $ft->setClassRange(10);

    var_dump($ft->getMode());

    $ft->setData([0]);
    var_dump($ft->getMode());

    $ft->setData([0,10,15]);
    var_dump($ft->getMode());

    $ft->setData([0,5,10,15,20,24,28]);
    var_dump($ft->getMode());
    ```

- PHP
    ```bash
    NULL
    int(5)
    int(15)
    int(25)
    ```

***

### getMedian

```php
getMedian($param)
```

Returns Median of `$param`.

#### Parameter

> $param
- Type: 1 Dimensional (Hash) Array
- Values: must be Integer or Float

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: `$param` is invalid.
    - Integer or Float: Median

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    var_dump($ft->getMedian(["0"]));
    var_dump($ft->getMedian([0]));
    var_dump($ft->getMedian([0,1]));
    var_dump($ft->getMedian([0,1,2,3]));
    var_dump($ft->getMedian([0,1,2,3,4]));
    var_dump($ft->getMedian([0,1,2,3,4,5]));
    ```

- Result
    ```bash
    NULL
    int(0)
    float(0.5)
    float(1.5)
    int(2)
    float(2.5)
    ```

***

### getMedianClass

```php
getMedianClass()
```

Returns the Class which Median belongs to.

Before using this class, you should set Class Range and data.

#### Return
- Type: 1 Dimensional Hash Array
- Structure: is like as follows.
    ```php
    [
        'index' => 2,
        'bottom' => 20,
        'top' => 30,
    ]
    ```
- Note: `index` is the index number of Class Array. Class Array Structure:
    ```php
    $classes = [
        0 => [ 'bottom' => 0, 'top' => 10, ],
        1 => [ 'bottom' => 10, 'top' => 20, ],
        2 => [ 'bottom' => 20, 'top' => 30, ],
        3 => [ 'bottom' => 30, 'top' => 40, ],
        4 => [ 'bottom' => 40, 'top' => 50, ],
    ];
    ```

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $ft->setClassRange(10);

    var_dump($ft->getMedianClass());    // null

    $ft->setData([0,5,10,15,20]);
    var_dump($ft->getClasses());      // Class Array
    var_dump($ft->getMedianClass());  // Median Class
    ```

- Result
    ```bash
    NULL
    array(3) {
        [0]=>
        array(2) {
            ["bottom"]=>
            int(0)
            ["top"]=>
            int(10)
        }
        [1]=>
        array(2) {
            ["bottom"]=>
            int(10)
            ["top"]=>
            int(20)
        }
        [2]=>
        array(2) {
            ["bottom"]=>
            int(20)
            ["top"]=>
            int(30)
        }
    }
    array(3) {
        ["index"]=>
        int(1)
        ["bottom"]=>
        int(10)
        ["top"]=>
        int(20)
    }
    ```

***

### getFirstQuartile

```php
getFirstQuartile($data)
```

Returns First Quartile of `$data`.

#### Parameter

> $data
- Type: 1 Dimensional (Hash) Array
- Values: must be Integer or Float.

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: `$data` is invalid.
    - Integer or Float: First Quartile

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    var_dump($ft->getFirstQuartile([]));
    var_dump($ft->getFirstQuartile([1]));
    var_dump($ft->getFirstQuartile([1,2]));
    var_dump($ft->getFirstQuartile([1,2,3]));
    var_dump($ft->getFirstQuartile([1,2,3,4]));
    var_dump($ft->getFirstQuartile([1,2,3,4,5]));
    var_dump($ft->getFirstQuartile(['Amy'=>170,'John'=>180,'Jake'=>190]));
    ```

- Result
    ```bash
    NULL
    int(1)
    int(1)
    int(1)
    float(1.5)
    float(1.5)
    int(170)
    ```

***

### getThirdQuartile

```php
getThirdQuartile($data)
```

Returns Third Quartile of `$data`.

#### Parameter

> $data
- Type: 1 Dimensional (Hash) Array
- Values: must be Integer or Float.

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: `$data` is invalid.
    - Integer or Float: Third Quartile

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    var_dump($ft->getThirdQuartile([]));
    var_dump($ft->getThirdQuartile([1]));
    var_dump($ft->getThirdQuartile([1,2]));
    var_dump($ft->getThirdQuartile([1,2,3]));
    var_dump($ft->getThirdQuartile([1,2,3,4]));
    var_dump($ft->getThirdQuartile([1,2,3,4,5]));
    var_dump($ft->getThirdQuartile(['Amy'=>170,'John'=>180,'Jake'=>190]));
    ```

- Result
    ```bash
    NULL
    int(1)
    int(2)
    int(3)
    float(3.5)
    float(4.5)
    int(190)
    ```

***

### getInterQuartileRange

```php
getInterQuartileRange($data)
```

Returns Inter Quartile Range of `$data`.

#### Parameter

> $data
- Type: 1 Dimensional (Hash) Array
- Values: must be Integer or Float.

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: `$data` is invalid.
    - Integer or Float: Inter Quartile Range

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    var_dump($ft->getInterQuartileRange([]));
    var_dump($ft->getInterQuartileRange([1]));
    var_dump($ft->getInterQuartileRange([1,2]));
    var_dump($ft->getInterQuartileRange([1,2,3]));
    var_dump($ft->getInterQuartileRange([1,2,3,4]));
    var_dump($ft->getInterQuartileRange([1,2,3,4,5]));
    var_dump($ft->getInterQuartileRange(['Amy'=>170,'John'=>180,'Jake'=>190]));
    ```

- Result
    ```bash
    NULL
    int(0)
    int(1)
    int(2)
    float(2)
    float(3)
    int(20)
    ```

***

### getQuartileDeviation

```php
getQuartileDeviation($data)
```

Returns Quartile Deviation of `$data`.

#### Parameter

> $data
- Type: 1 Dimensional (Hash) Array.
- Values: must be Integer or Float.

#### Return

- Type: Integer or Float or `null`
- Value:
    - `null`: `$data` is invalid.
    - Integer or Float: Quartile Deviation

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    var_dump($ft->getQuartileDeviation([]));
    var_dump($ft->getQuartileDeviation([1]));
    var_dump($ft->getQuartileDeviation([1,2]));
    var_dump($ft->getQuartileDeviation([1,2,3]));
    var_dump($ft->getQuartileDeviation([1,2,3,4]));
    var_dump($ft->getQuartileDeviation([1,2,3,4,5]));
    var_dump($ft->getQuartileDeviation(['Amy'=>170,'John'=>180,'Jake'=>190]));
    ```

- Result
    ```bash
    NULL
    int(0)
    float(0.5)
    int(1)
    float(1)
    float(1.5)
    int(10)
    ```

***

### getColumns2Show

```php
getColumns2Show()
```

Returns Columns to show in the Frequency Table.

#### Return

- Type: 1 Dimensional Array
- Values: Strings

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    var_dump($ft->getColumns2Show());
    $ft->setColumns2Show(['Class','Frequency']);
    var_dump($ft->getColumns2Show());

    $ft->setClassRange(10);
    $ft->setData([0,5,10,15,20]);
    $ft->show(['Mean' => false]);
    ```

- Result
    ```bash
    array(5) {
    [0]=>
    string(5) "Class"
    [1]=>
    string(9) "Frequency"
    [2]=>
    string(17) "RelativeFrequency"
    [3]=>
    string(10) "ClassValue"
    [4]=>
    string(22) "ClassValue * Frequency"
    }
    array(2) {
    [0]=>
    string(5) "Class"
    [1]=>
    string(9) "Frequency"
    }
    |Class|Frequency|
    |:---:|:---:|
    |0 ~ 10|2|
    |10 ~ 20|2|
    |20 ~ 30|1|
    |Total|5|
    ```

***

### getValidColumns2Show

```php
getValidColumns2Show()
```

Returns Settable Columns to show in Frequency Table.

You can check Column Names to set by using this method.

#### Return

- Type: 1 Dimensional Array
- Values: Strings

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    var_dump($ft->getValidColumns2Show());
    ```

- Result
    ```bash
    array(7) {
    [0]=>
    string(5) "Class"
    [1]=>
    string(9) "Frequency"
    [2]=>
    string(19) "CumulativeFrequency"
    [3]=>
    string(17) "RelativeFrequency"
    [4]=>
    string(27) "CumulativeRelativeFrequency"
    [5]=>
    string(10) "ClassValue"
    [6]=>
    string(22) "ClassValue * Frequency"
    }
    ```

***

### isSettableColumns2Show

```php
isSettableColumns2Show($columns)
```

Judges whether `$columns` is valid or not for setColumns2Show().

#### Parameter

> $columns
- Type: 1 Dimensional (Hash) Array
- Values: Any

#### Return

- Type: bool
- Value:
    - `true`: `$columns` is valid.
    - `false`: `$columns` is invalid.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    $columns = ['Class','Frequency','Hoge'];
    var_dump($ft->isSettableColumns2Show($columns));

    $columns = ['Class','Frequency','RelativeFrequency'];
    var_dump($ft->isSettableColumns2Show($columns));
    ```

- Result
    ```bash
    bool(false)
    bool(true)
    ```

***

### setColumns2Show

```php
setColumns2Show($columns)
```

Sets Columns to show in Frequency Table.

#### Parameter

> $columns
- Type: 1 Dimensional (Hash) Array
- Values: must be members of elements getValidColumns2Show() returns.

#### Return

- Type: bool
- Value:
    - `true`: `$columns` is valid and set.
    - `false`: `$columns` is invalid and not set.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();

    var_dump($ft->getColumns2Show());

    $columns = ['Class','Frequency'];
    var_dump($ft->setColumns2Show($columns));
    var_dump($ft->getColumns2Show());

    $columns = ['Class','Hoge'];
    var_dump($ft->setColumns2Show($columns));
    var_dump($ft->getColumns2Show());
    ```

- Result
    ```bash
    array(5) {
    [0]=>
    string(5) "Class"
    [1]=>
    string(9) "Frequency"
    [2]=>
    string(17) "RelativeFrequency"
    [3]=>
    string(10) "ClassValue"
    [4]=>
    string(22) "ClassValue * Frequency"
    }
    bool(true)
    array(2) {
    [0]=>
    string(5) "Class"
    [1]=>
    string(9) "Frequency"
    }
    bool(false)
    array(2) {
    [0]=>
    string(5) "Class"
    [1]=>
    string(9) "Frequency"
    }
    ```

***

### show

```php
show($option = ['Mean' => true, 'STDOUT' => true, 'ReturnValue' => true])
```

Prints the Frequency Table in markdown format to standard output.

#### Parameter

> $option
- Default: `['Mean' => true, ]`
- Type: 1 Dimensional Hash Array
- Structure:
    ```php
    $option = [
        'Mean' => true,
        'STDOUT' => true,
        'ReturnValue' => true,
    ];
    ```
- Values:
    - `Mean`: bool
        - `true`: Mean is shown at the bottom line of the Frequency Table.
        - `false`: Mean is not shown at the bottom line of the Frequency Table.
    - `STDOUT`: bool
        - `true`: Frequency Table is printed to standard output.
        - `false`: no output to standard output.
    - `ReturnValue`: bool
        - `true`: Frequency Table in markdown format is returned as a return value.
        - `false`: no return value

#### Return

- Type: string
- Value: Same strings as standard output.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable(['classRange'=>10,'data'=>[0,5,10,15,20]]);

    echo "CASE1: STDOUT: ON / RETURN VALUE: OFF\n";
    $option = ['Mean' => false, 'STDOUT' => true, 'ReturnValue' => false];
    var_dump($ft->show($option));

    echo "CASE2: STDOUT: Off / RETURN VALUE: On\n";
    $option = ['Mean' => false, 'STDOUT' => false, 'ReturnValue' => true];
    var_dump($ft->show($option));

    echo "CASE3: STDOUT: OFF / RETURN VALUE: OFF\n";
    $option = ['Mean' => false, 'STDOUT' => false, 'ReturnValue' => false];
    var_dump($ft->show($option));
    ```

- Result
    ```bash
    CASE1: STDOUT: ON / RETURN VALUE: OFF
    |Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
    |:---:|:---:|:---:|:---:|---:|
    |0 ~ 10|2|0.40|5.0|10.0|
    |10 ~ 20|2|0.40|15.0|30.0|
    |20 ~ 30|1|0.20|25.0|25.0|
    |Total|5|1.00|---|65.0|
    NULL
    CASE2: STDOUT: Off / RETURN VALUE: On
    string(204) "|Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
    |:---:|:---:|:---:|:---:|---:|
    |0 ~ 10|2|0.40|5.0|10.0|
    |10 ~ 20|2|0.40|15.0|30.0|
    |20 ~ 30|1|0.20|25.0|25.0|
    |Total|5|1.00|---|65.0|
    "
    CASE3: STDOUT: OFF / RETURN VALUE: OFF
    NULL
    ```

***

### parse

```php
parse()
```

Parses the data set and returns parsed data.

Before using this method, you should set Class Range and data.

#### Return

- Type: 2 Dimensional Hash Array
- Structure: is like as follows.
    ```php
    [
        'classRange' => 10,
        'data' => [0,5,10,15,20],
        'Max' => 20,
        'Min' => 0,
        'DataRange' => 20,
        'Mode' => 15,
        'Total' => 5,
        'Mean' => 13,
        'Median' => 10,
        'MedianClass' => ['index' => 1, 'bottom' => 10, 'top' => 20 ],
        'FirstQuartile' => 2.5,
        'ThirdQuartile' => 17.5,
        'InterQuartileRange' => 15,
        'QuartileDeviation' => 7.5,
        'Classes' => [
            0 => ['bottom' => 0, 'top' => 10],
            1 => ['bottom' => 10, 'top' => 20],
            2 => ['bottom' => 20, 'top' => 30],
        ],
        'Frequencies' => [2,2,1],
        'FrequencyTable' => "...(strings)...",
    ]
    ```

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;


    $ft = new FrequencyTable();

    $ft->setClassRange(10);
    $ft->setData([0,5,10]);

    var_dump($ft->parse());
    ```

- Result
    ```bash
    array(17) {
    ["classRange"]=>
    int(10)
    ["data"]=>
    array(3) {
        [0]=>
        int(0)
        [1]=>
        int(5)
        [2]=>
        int(10)
    }
    ["Max"]=>
    int(10)
    ["Min"]=>
    int(0)
    ["DataRange"]=>
    int(10)
    ["Mode"]=>
    int(5)
    ["Total"]=>
    int(3)
    ["Mean"]=>
    float(8.333333333333334)
    ["Median"]=>
    int(5)
    ["MedianClass"]=>
    array(3) {
        ["index"]=>
        int(0)
        ["bottom"]=>
        int(0)
        ["top"]=>
        int(10)
    }
    ["FirstQuartile"]=>
    int(0)
    ["ThirdQuartile"]=>
    int(10)
    ["InterQuartileRange"]=>
    int(10)
    ["QuartileDeviation"]=>
    int(5)
    ["Classes"]=>
    array(2) {
        [0]=>
        array(2) {
        ["bottom"]=>
        int(0)
        ["top"]=>
        int(10)
        }
        [1]=>
        array(2) {
        ["bottom"]=>
        int(10)
        ["top"]=>
        int(20)
        }
    }
    ["Frequencies"]=>
    array(2) {
        [0]=>
        int(2)
        [1]=>
        int(1)
    }
    ["FrequencyTable"]=>
    string(200) "|Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
    |:---:|:---:|:---:|:---:|---:|
    |0 ~ 10|2|0.67|5.0|10.0|
    |10 ~ 20|1|0.33|15.0|15.0|
    |Total|3|1.00|---|25.0|
    |Mean|---|---|---|8.3|
    "
    }
    ```

***

### csv

```php
csv($path, $quatation = true, $eol = "\n")
```

Output Frequency Table into the CSV file specified by `$path`.

Before using this method, you should set Class Range and data.

#### Parameters

> $path

- Type: string
- Value: file path of the CSV file to save data.

> $quatation

- Default: `true`
- Type: bool
- Value:
    - `true`: each data is quated by double quatation mark.
    - `false`: each data is not quated.

> $eol

The character code appended at end of each lines.

- Default: "\n"
- Type: string
- Value: any (at your own risk)

#### Return

- Type: integer or `false` or `null`
- Value:
    - integer: byte length of data saved.
    - `false`: failed to save data into the file.
    - `null`: parameters are invalid.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;


    $ft = new FrequencyTable();

    $ft->setClassRange(10);
    $ft->setData([0,5,10,15,20]);

    $ft->csv('test.csv');
    ```

- Result: `test.csv`
    ```
    "Class","Frequency"
    "0 ~ 10","2"
    "10 ~ 20","2"
    "20 ~ 30","1"
    "Total","5"
    ```

***

### tsv

```php
tsv($path, $quatation = true, $eol = "\n")
```

Output Frequency Table into the TSV file specified by `$path`.

Before using this method, you should set Class Range and data.

#### Parameters

> $path

- Type: string
- Value: file path of the TSV file to save data.

> $quatation

- Default: `true`
- Type: bool
- Value:
    - `true`: each data is quated by double quatation mark.
    - `false`: each data is not quated.

> $eol

The character code appended at end of each lines.

- Default: "\n"
- Type: string
- Value: any (at your own risk)

#### Return

- Type: integer or `false` or `null`
- Value:
    - integer: byte length of data saved.
    - `false`: failed to save data into the file.
    - `null`: parameters are invalid.

#### Example

- PHP
    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;


    $ft = new FrequencyTable();

    $ft->setClassRange(10);
    $ft->setData([0,5,10,15,20]);

    $ft->tsv('test.tsv');
    ```

- Result: `test.tsv`
    ```
    "Class"	"Frequency"
    "0 ~ 10"	"2"
    "10 ~ 20"	"2"
    "20 ~ 30"	"1"
    "Total"	"5"
    ```

***

### html

```php
html($path = null)
```

Output Frequency Table into the html file specified by `$path`.

If `empty($path)` is `true`, this method returns Frequency Table html.

Before using this method, you should set Class Range and data.

#### Parameters

> $path = null

- Default: `null`
- Type: string
- Value: file path of the TSV file to save data.

#### Return

- Type: integer or string or `false` or `null`
- Value:
    - integer: byte length of data saved.
    - string: Frequency Table html.
    - `false`: failed to save data into the file.
    - `null`: parameters are invalid.

#### Example

- PHP

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;


    $ft = new FrequencyTable();

    $ft->setClassRange(10);
    $ft->setData([0,5,10,15,20]);

    $ft->html('test.html');
    ```

- Result: `test.html`

    ```html
    <table>
    <tr><td>Class</td><td>Frequency</td><td>RelativeFrequency</td><td>ClassValue</td><td>ClassValue * Frequency</td></tr>
    <tr><td>0 ~ 10</td><td>2</td><td>0.40</td><td>5.0</td><td>10.0</td></tr>
    <tr><td>10 ~ 20</td><td>2</td><td>0.40</td><td>15.0</td><td>30.0</td></tr>
    <tr><td>20 ~ 30</td><td>1</td><td>0.20</td><td>25.0</td><td>25.0</td></tr>
    <tr><td>Total</td><td>5</td><td>1.00</td><td>---</td><td>65.0</td></tr>
    </table>
    ```

***

*Document written: 2023/05/20*

*Last Updated: 2023/05/31*
