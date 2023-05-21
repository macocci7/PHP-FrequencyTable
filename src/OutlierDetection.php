<?php
require('./class/FrequencyTable.php');
require('./class/CsvUtil.php');

function getUcl($parsed) {
    if (!is_array($parsed)) return;
    if (!array_key_exists('ThirdQuartile',$parsed)) return;
    if (!array_key_exists('InterQuartileRange',$parsed)) return;
    return $parsed['ThirdQuartile'] + 1.5 * $parsed['InterQuartileRange'];
}

function getLcl($parsed) {
    if (!is_array($parsed)) return;
    if (!array_key_exists('FirstQuartile',$parsed)) return;
    if (!array_key_exists('InterQuartileRange',$parsed)) return;
    return $parsed['FirstQuartile'] - 1.5 * $parsed['InterQuartileRange'];
}

function getOutliers($data) {
    $ft = new FrequencyTable();
    if (!$ft->isSettableData($data)) return;

    $ft->setClassRange(10);
    $ft->setData($data);
    $parsed = $ft->parse();
   
    $ucl = getUcl($parsed);
    $lcl = getLcl($parsed);
    if (!$ucl || !$lcl) return;

    $outliers = [];
    foreach($data as $value) {
        if ($value > $ucl || $value < $lcl) $outliers[] = $value;
    }
    unset($ft);
    return $outliers;
}

$players = [
    [ 'id' => 660271, 'name' => 'Ohtani, Shohei', ],
    [ 'id' => 663776, 'name' => 'Sandoval Patrik', ],
    [ 'id' => 672282, 'name' => 'Detmers, Reid', ],
];
$csvUtil = new CsvUtil();
$csvFileName = 'csv/672282_data.csv';

$ft = new FrequencyTable();
$ft->setClassRange(10);

$propertyKeys = [
    'classRange',
    'Max',
    'Min',
    'DataRange',
    'Mode',
    'Mean',
    'Median',
    'FirstQuartile',
    'ThirdQuartile',
    'InterQuartileRange',
    'QuartileDeviation',
];

echo "# Pitching speed (MPH)\n\n";
echo "## Data Source\n\n[savant](https://baseballsavant.mlb.com/)\n\n";
echo "## Pitcher:\n\n";

echo " Detmers, Reid (672282)\n\n";

$dailyData = $csvUtil->getDailyData($csvFileName);
if (!$dailyData) {
    echo "Failed to load CSV data.\n\n";
}

echo "## Dates\n\n";

foreach(array_keys($dailyData) as $key) {
    echo "- [" . $key . "](#" . $key . ")\n";
}
foreach ($dailyData as $date => $data) {
    echo "\n## " . $date . "\n\n";
    $ft->setData($data);
    $parsed = $ft->parse();
    echo "<details><summary>Data</summary>\n\n";
    echo "|#|release_speed|\n";
    echo "|---|---|\n";
    foreach($parsed['data'] as $index => $value) {
        echo "|".($index+1)."|".$value."|\n";
    }
    echo "</details>\n\n";
    echo "<details><summary>Properties</summary>\n\n";
    echo "|Property|Value|\n";
    echo "|---|---|\n";
    foreach($propertyKeys as $key) {
        echo "|" . $key . "|" . $parsed[$key] . "|\n";
    }
    echo "|UCL|". getUcl($parsed) ."|\n";
    echo "|LCL|". getLcl($parsed) ."|\n";
    echo "</details>\n\n";
    $outliers = getOutliers($data);
    if ($outliers) {
        echo "<details><summary>Outliers</summary>\n\n";
        echo "|#|Value|\n";
        echo "|---|---|\n";
        foreach($outliers as $index => $value) {
            echo "|".($index+1)."|".$value."|\n";
        }
        echo "</details>\n\n";
    } else {
        echo "no outliers.\n\n";
    }
}
echo "\n\n";
