<?php

require('../vendor/autoload.php');

use Macocci7\PHPFrequencyTable\FrequencyTable;

$ft = new FrequencyTable(['data'=>[0,5,10,15,20],'classRange'=>10]);
$ft->show();
