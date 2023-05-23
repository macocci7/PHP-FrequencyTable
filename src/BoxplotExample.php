<?php
require('./class/Boxplot.php');
require('./class/CsvUtil.php');

$bp = new Boxplot();
$csvUtil = new CsvUtil();
$csvFileName = 'csv/672282_data.csv';
$dailyData = $csvUtil->getDailyData($csvFileName);
if (!$dailyData) {
    echo "Failed to load CSV data.\n\n";
}

$filePath = 'img/BoxplotExample672282.png';
foreach($dailyData as $date => $data) {
    $bp->setData($date, $data);
}
$bp->create()->save($filePath);
