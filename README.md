# FrequencyTable.php

*Document written: 2023/05/18*

## Overview

`FrequencyTable.php` is single object class file written in PHP in order to operate frequency table easily.

ChatGTP and Google Bard cannot take statistics correctly at present, so I made this to teach them how to make a frequency table.

There seems to be some tools to make a Frequency Table in the world.

However, this FrequencyTable class is the most easiest tool to use, I think. (just I think so)

You can use it easily by just requiring the file `FrequencyTable.php`.

Locate `FrequencyTable.php` wherever you like.

Let's create an instance of FrequencyTable and operate it!

## Prerequisite

- `FrequencyTable.php` is written and tested in `PHP 8.1.2 CLI` environment. 
- `PHPUnit 10.1.3` is used in testing.
- You are expected to have known what frequency table is, and mathmatical terms used in this code:

    <details>
    <summary>Mathmatical Terms used in this code</summary>

    - Frequency Table
    - Class
    - Class Range
    - Class Value
    - Frequency
    - Relative Frequency
    - Total
    - Average
    - Max(imum)
    - Min(imum)
    - Data Range
    - Mode
    - Median
    - First Quartile
    - Third Quartile
    - Inter Quartile Range
    </details>

## Installation

Locate `FrequencyTable.php` wherever you like.

## The Most Simple Usage

You can use FrequencyTable class as follows.

**PHP Code: [Example.php](src/Example.php)**

```
<?php
require('./class/FrequencyTable.php');

$ft = new FrequencyTable(['data'=>[0,5,10,15,20],'classRange'=>10]);
$ft->show();
```

**Command to Excute**

```
cd src
php -f Example.php
```

**Standard Output**

```
|Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
|:---:|:---:|:---:|:---:|---:|
|0 ~ 10|2|0.40|5.0|10.0|
|10 ~ 20|2|0.40|15.0|30.0|
|20 ~ 30|1|0.20|25.0|25.0|
|Total|5|1.00|---|65.0|
|Average|---|---|---|13.0|
```

**Output Preview On VSCode**

|Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
|:---:|:---:|:---:|:---:|---:|
|0 ~ 10|2|0.40|5.0|10.0|
|10 ~ 20|2|0.40|15.0|30.0|
|20 ~ 30|1|0.20|25.0|25.0|
|Total|5|1.00|---|65.0|
|Average|---|---|---|13.0|

## Usage

Let's create the PHP code to show a Frequency Table.

The name of new PHP file is `Example.php`.

1. Require `FrequencyTable.php`

    Require `FrequencyTable.php` as follows in your PHP code (Example.php).

    ```
    <?php
    require('./class/FrequencyTable.php');
    ```

    Rewirte the path to the correct path which you located `FrequencyTable.php`.

2. Create an instance

    Then create an instance of FrequencyTable in your PHP code as follows.

    ```
    <?php
    require('./class/FrequencyTable.php');

    $ft = new FrequencyTable();
    ```

3. Set the class range

    Then set the class range you as follows.

    ```
    <?php
    require('./class/FrequencyTable.php');

    $ft = new FrequencyTable();
    $ft->setClassRange(10);
    ```

4. Set the data

    Then set the data to collect statistics as follows.

    ```
    <?php
    require('./class/FrequencyTable.php');

    $ft = new FrequencyTable();
    $ft->setClassRange(10);

    $data = [0,5,10,15,20];
    $ft->setData($data);
    ```

5. Show the Frequency Table

    Now you can show the Frequency Table of the data you gave before as follows.

    ```
    <?php
    require('./class/FrequencyTable.php');

    $ft = new FrequencyTable();
    $ft->setClassRange(10);

    $data = [0,5,10,15,20];
    $ft->setData($data);

    $ft->show();
    ```

    Or you can set both the class range and the data when you create an instance of FrequencyTable as follows.

    ```
    <?php
    require('./class/FrequencyTable.php');

    $data = [0,5,10,15,20];
    $ft = new FrequencyTable(['data' => $data, 'classRange' => 10]);

    $ft->show();
    ```
    This is more simple. You can choose the way you like.

    By using the former way, you can set other data or class range after some operations.

6. Execute the PHP file `Example.php` you made

    Then the Frequency Table will be shown as text in Mark Down table format on the standard output in your console.

    Excecute the PHP code in you console as follows.

    ```
    php -f Example.php
    ```

    Standard Output

    ```
    $ php -f Example.php
    |Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
    |:---:|:---:|:---:|:---:|---:|
    |0 ~ 10|2|0.40|5.0|10.0|
    |10 ~ 20|2|0.40|15.0|30.0|
    |20 ~ 30|1|0.20|25.0|25.0|
    |Total|5|1.00|---|65.0|
    |Average|---|---|---|13.0|
    ```
    You can make the output file as follows.

    ```
    php -f Example.php > Example.md
    ```

    Then the output will be written in [Example.md](src/Example.md).

    When you open [Example.md](src/Example.md) in your tool like `VSCode Preview` (or on Github),

    the frequency table will be shown as follows.

    |Class|Frequency|RelativeFrequency|ClassValue|ClassValue * Frequency|
    |:---:|:---:|:---:|:---:|---:|
    |0 ~ 10|2|0.40|5.0|10.0|
    |10 ~ 20|2|0.40|15.0|30.0|
    |20 ~ 30|1|0.20|25.0|25.0|
    |Total|5|1.00|---|65.0|
    |Average|---|---|---|13.0|

## Example Codes

- [ExampleCases.php](src/ExampleCases.php) >> results in [ExampleCases.md](src/ExampleCases.md)
- [PopulationInJapan2022.php](src/PopulationInJapan2022.php) >> results in [PopulationInJapan2022.md](src/PopulationInJapan2022.md)
- [FrequencyTableTest.php](tests/FrequencyTableTest.php) : all usage is written in this code.

## Appendix

You can also get all data to draw a boxplot by using this FrequencyTable class.
- Max Value
- Min Value
- First Quartile
- Third Quartile
- Median
- Average 
- Data Range
- Inter Quartile Range

Thanks for reading.

Have a happy coding!
