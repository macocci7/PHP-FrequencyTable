<?php
require('./class/FrequencyTable.php');
require('./class/Histogram.php');

$ft = new FrequencyTable();
$ft->setClassRange(10);
$ft->setData([0,5,10,15,20,22,24,26,28,30,33,36,39,40,45,50]);

$hg = new Histogram();
$hg->create($ft);
//var_dump($ft->getFrequencies());
