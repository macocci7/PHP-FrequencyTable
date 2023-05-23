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

$filePath01 = 'img/BoxplotExample672282_01.png';
$filePath02 = 'img/BoxplotExample672282_02.png';
$labels = [];
foreach($dailyData as $date => $data) {
    $labels[] = preg_replace('/\d+\-(\d+)\-(\d+)/', '$1/$2', $date);
    $bp->setData($date, $data);
}
$bp->setLabels($labels)
   ->setLabelX('hogehoge')
   ->setLabelY('hugahuga')
   ->setCaption('hogehogehugahuga')
   ->outlierOn()
   ->jitterOn()
   ->create()
   ->save($filePath01)
   ->outlierOff()
   ->jitterOff()
   ->create()
   ->save($filePath02);
