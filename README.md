# PHP-FrequencyTable

## Example Image

- Frequency Table Example:

    <a href="#the-most-simple-usage"><img src="img/FrequencyTableExample.png" width="400"></a>

## Contents
- [Overview](#overview)
- [Prerequisite](#prerequisite)
- [Installation](#installation)
- [Usage](#usage)
    - [The Most Simple Usage](#the-most-simple-usage)
    - [Other Usage](#other-usage)
    - [Saving data into CSV](#saving-data-into-csv)
    - [Saving data into TSV](#saving-data-into-tsv)
    - [Retrieving Parsed Data](#retrieving-parsed-data)
- [Methods](#methods)
- [Examples](#examples)
- [LICENSE](#license)

## Overview

`PHP-FrequencyTable` is single object class file written in PHP in order to operate frequency table easily.

You can create Frequency Tables easily just by setting data in array and Class Range.

You can save them into some formats, Markdown Table, CSV and TSV.

You can also get parsed data as hash array of PHP.

Let's create an instance of FrequencyTable and operate it!

## Prerequisite

- `PHP-FrequencyTable` is written and tested in `PHP 8.1.2 CLI` environment. 
- `PHPUnit 10.1.3` is used in testing.

## Installation

```bash
composer require macocci7/php-frequency-table
```

## Usage

### The Most Simple Usage

You can use FrequencyTable class as follows.

**PHP Code: [Example.php](src/Example.php)**

```php
<?php
require('../vendor/autoload.php');

use Macocci7\PhpFrequencyTable\FrequencyTable;

$ft = new FrequencyTable(['data'=>[0,5,10,15,20],'classRange'=>10]);
$ft->show();
```

**Command to Excute**

```bash
cd src
php -f Example.php
```

**Standard Output**

```bash
|Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
|:---:|:---:|:---:|:---:|---:|
|0 ~ 10|2|0.40|5.0|10.0|
|10 ~ 20|2|0.40|15.0|30.0|
|20 ~ 30|1|0.20|25.0|25.0|
|Total|5|1.00|---|65.0|
|Mean|---|---|---|13.0|
```

**Output Preview On VSCode**

|Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
|:---:|:---:|:---:|:---:|---:|
|0 ~ 10|2|0.40|5.0|10.0|
|10 ~ 20|2|0.40|15.0|30.0|
|20 ~ 30|1|0.20|25.0|25.0|
|Total|5|1.00|---|65.0|
|Mean|---|---|---|13.0|

### Other Usage

Let's create the PHP code to show a Frequency Table.

The name of new PHP file is `examples/Example.php`.

1. Require `autoload.php` and declare use statement.

    Require `autoload.php` as follows in your PHP code (Example.php).

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    ```

2. Create an instance

    Then create an instance of FrequencyTable in your PHP code as follows.

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();
    ```

3. Set the class range

    Then set the class range you as follows.

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();
    $ft->setClassRange(10);
    ```

4. Set the data

    Then set the data to collect statistics as follows.

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();
    $ft->setClassRange(10);

    $data = [0,5,10,15,20];
    $ft->setData($data);
    ```

5. Show the Frequency Table

    Now you can show the Frequency Table of the data you gave before as follows.

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();
    $ft->setClassRange(10);

    $data = [0,5,10,15,20];
    $ft->setData($data);

    $ft->show();
    ```

    Or you can set both the class range and the data when you create an instance of FrequencyTable as follows.

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $data = [0,5,10,15,20];
    $ft = new FrequencyTable(['data' => $data, 'classRange' => 10]);

    $ft->show();
    ```
    This is more simple. You can choose the way you like.

    By using the former way, you can set other data or class range after some operations.

6. Execute the PHP file `Example.php` you made

    Then the Frequency Table will be shown as text in Mark Down table format on the standard output in your console.

    Excecute the PHP code in you console as follows.

    ```bash
    php -f Example.php
    ```

    Standard Output

    ```bash
    |Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
    |:---:|:---:|:---:|:---:|---:|
    |0 ~ 10|2|0.40|5.0|10.0|
    |10 ~ 20|2|0.40|15.0|30.0|
    |20 ~ 30|1|0.20|25.0|25.0|
    |Total|5|1.00|---|65.0|
    |Mean|---|---|---|13.0|
    ```
    You can make the output file as follows.

    ```bash
    php -f Example.php > Example.md
    ```

    Then the output will be written in [Example.md](examples/Example.md).

    When you open [Example.md](examples/Example.md) in your tool like `VSCode Preview` (or on Github),

    the frequency table will be shown as follows.

    |Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
    |:---:|:---:|:---:|:---:|---:|
    |0 ~ 10|2|0.40|5.0|10.0|
    |10 ~ 20|2|0.40|15.0|30.0|
    |20 ~ 30|1|0.20|25.0|25.0|
    |Total|5|1.00|---|65.0|
    |Mean|---|---|---|13.0|

### Saving data into CSV

- PHP

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $data = [0,5,10,15,20];
    $ft = new FrequencyTable(['data' => $data, 'classRange' => 10]);

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

### Saving data into TSV

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

### Retrieving Parsed Data

You can also retrieve parsed data without showing Frequency Table.

Use `parse()` method. `parse()` method returns Hash Array as follows.

- PHP

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();
    $ft->setClassRange(10);

    $data = [0,5,10,15,20];
    $ft->setData($data);

    print_r($ft->parse());
    ```

- Result

    ```bash
    Array
    (
        [classRange] => 10
        [data] => Array
            (
                [0] => 0
                [1] => 5
                [2] => 10
                [3] => 15
                [4] => 20
            )

        [Max] => 20
        [Min] => 0
        [DataRange] => 20
        [Mode] => 5
        [Total] => 5
        [Mean] => 13
        [Median] => 10
        [MedianClass] => Array
            (
                [index] => 1
                [bottom] => 10
                [top] => 20
            )

        [FirstQuartile] => 2.5
        [ThirdQuartile] => 17.5
        [InterQuartileRange] => 15
        [QuartileDeviation] => 7.5
        [Classes] => Array
            (
                [0] => Array
                    (
                        [bottom] => 0
                        [top] => 10
                    )

                [1] => Array
                    (
                        [bottom] => 10
                        [top] => 20
                    )

                [2] => Array
                    (
                        [bottom] => 20
                        [top] => 30
                    )

            )

        [Frequencies] => Array
            (
                [0] => 2
                [1] => 2
                [2] => 1
            )

        [FrequencyTable] => |Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
    |:---:|:---:|:---:|:---:|---:|
    |0 ~ 10|2|0.40|5.0|10.0|
    |10 ~ 20|2|0.40|15.0|30.0|
    |20 ~ 30|1|0.20|25.0|25.0|
    |Total|5|1.00|---|65.0|
    |Mean|---|---|---|13.0|

    )
    ```

You can use the parsed data like this:

- PHP

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PhpFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();
    $ft->setClassRange(10);

    $data = [0,5,10,15,20];
    $ft->setData($data);

    $parsed = $ft->parse();
    echo "Data:[" . implode(', ', $parsed['data']) . "]\n";
    echo "Max:" . $parsed['Max'] . "\n";
    echo "Min:" . $parsed['Min'] . "\n";
    echo "Median:" . $parsed['Median'] . "\n";
    echo "Median is in the class of "
         . $parsed['MedianClass']['bottom']
         . " ~ "
         . $parsed['MedianClass']['top'] . "\n";
    echo "Total:" . $parsed['Total'] . "\n";
    echo "Mean:" . $parsed['Mean'] . "\n";
    echo "Q1:" . $parsed['FirstQuartile'] . "\n";
    echo "Q2:" . $parsed['ThirdQuartile'] . "\n";
    echo "IQR:" . $parsed['InterQuartileRange'] . "\n";
    echo "QD:" . $parsed['QuartileDeviation'] . "\n";
    ```

- Output

    ```bash
    Data:[0, 5, 10, 15, 20]
    Max:20
    Min:0
    Median:10
    Median is in the class of 10 ~ 20
    Total:5
    Mean:13
    Q1:2.5
    Q2:17.5
    IQR:15
    QD:7.5
    ```

## Methods

Learn more: [Methods](Methods.md)

## Examples

preparing.
- [ExampleCases.php](examples/ExampleCases.php) >> results in [ExampleCases.md](examples/ExampleCases.md)
- [PopulationInJapan2022.php](examples/PopulationInJapan2022.php) >> results in [PopulationInJapan2022.md](examples/PopulationInJapan2022.md)
- [OhtaniShohei2023.php](examples/OhtaniShohei2023.php) >> results in [OhtaniShohei2023.md](examples/OhtaniShohei2023.md)
- [OutlierDetection.php](examples/OutlierDetection.php) >> results in [OutlierDetection.md](examples/OutlierDetection.md)
- [FrequencyTableTest.php](tests/FrequencyTableTest.php) : all usage is written in this code.

## LICENSE

[MIT](LICENSE)


Thanks for reading.

Have a happy coding!


*Document written: 2023/05/18*

*Last updated: 2023/05/27*

Copyright (c) 2023 macocci7
