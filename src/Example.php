<?php
require('./class/FrequencyTable.php');

$ft = new FrequencyTable(['data'=>[0,5,10,15,20],'classRange'=>10]);
$ft->show();
