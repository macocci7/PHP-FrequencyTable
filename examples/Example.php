<?php

require('../vendor/autoload.php');

use Macocci7\PhpFrequencyTable\FrequencyTable;

$ft = new FrequencyTable(['data'=>[0,5,10,15,20],'classRange'=>10]);
$ft->meanOn()->show();
