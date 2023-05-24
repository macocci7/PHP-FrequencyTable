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

$filePath01 = 'img/BoxplotDetmersReid2023_01.png';
$filePath02 = 'img/BoxplotDetmersReid2023_02.png';
$labels = [];
foreach($dailyData as $date => $data) {
    $labels[] = preg_replace('/\d+\-(\d+)\-(\d+)/', '$1/$2', $date);
    //$bp->setData($date, $data);
    $bp->setData($data);
}
$bp->setLabels($labels);
$bp->setLabelX('Game Date')
   ->setLabelY('MPH')
   ->setCaption('Release Speed: Detmers, Reid')
   ->meanOn()
   ->outlierOn()
   ->jitterOn()
   ->create()
   ->save($filePath01)
   ->outlierOff()
   ->jitterOff()
   ->create()
   ->save($filePath02);
