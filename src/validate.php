<?php
require('../vendor/autoload.php');
require('./class/FrequencyTable.php');
require('./class/Histogram.php');

$ft = new FrequencyTable();
$ft->setClassRange(10);
$ft->setData([5,10,15,20,24,28,30]);

$config = [
    'canvasWidth' => 2048,
    'canvasHeight' => 2048,
];
$hg = new Histogram();
//var_dump($hg->getValidConfig($config));
//var_dump($hg->validateConfig('canvasWidth', 2048));
//exit;
$hg->configure($config);
$histogramPath = 'img/HistogramConfigTest001.png';
$hg->create($ft, $histogramPath);
/*
$v = new Valitron\Validator($config);
var_dump($v->validate());
*/
