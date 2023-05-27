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
- [Methods](#methods)
- [Examples](#examples)
- [LICENSE](#license)

## Overview

`PHP-FrequencyTable` is single object class file written in PHP in order to operate frequency table easily.

ChatGTP and Google Bard cannot take statistics correctly at present, so I made this to teach them how to make a frequency table.

There seems to be some tools to make a Frequency Table in the world.

However, this FrequencyTable class is the most easiest tool to use, I think. (just I think so)

Let's create an instance of FrequencyTable and operate it!

## Prerequisite

- `PHP-FrequencyTable` is written and tested in `PHP 8.1.2 CLI` environment. 
- `PHPUnit 10.1.3` is used in testing.
- You are expected to have known what frequency table is, and mathmatical terms used in this code:

    <details>
    <summary>Mathmatical Terms used in this code</summary>

    - Frequency Table
    - Class
    - Class Range
    - Class Value
    - Frequency
    - Cumulative Frequency
    - Relative Frequency
    - Cumulative Relative Frequency
    - Total
    - Mean
    - Max(imum)
    - Min(imum)
    - Data Range
    - Mode
    - Median
    - First Quartile
    - Third Quartile
    - Inter Quartile Range
    - Quartile Deviation
    </details>

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

use Macocci7\PHPFrequencyTable\FrequencyTable;

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

    use Macocci7\PHPFrequencyTable\FrequencyTable;

    ```

2. Create an instance

    Then create an instance of FrequencyTable in your PHP code as follows.

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PHPFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();
    ```

3. Set the class range

    Then set the class range you as follows.

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PHPFrequencyTable\FrequencyTable;

    $ft = new FrequencyTable();
    $ft->setClassRange(10);
    ```

4. Set the data

    Then set the data to collect statistics as follows.

    ```php
    <?php
    require('../vendor/autoload.php');

    use Macocci7\PHPFrequencyTable\FrequencyTable;

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

    use Macocci7\PHPFrequencyTable\FrequencyTable;

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

    use Macocci7\PHPFrequencyTable\FrequencyTable;

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
