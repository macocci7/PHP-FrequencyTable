<?php
require('./class/FrequencyTable.php');
require('./class/Histogram.php');

$ft = new FrequencyTable();
$ft->setClassRange(10);
$ft->setData([0,5,10,15,20,22,24,26,28,30,33,36,39,40,45,50]);

$hg = new Histogram();
$config = [
    'canvasWidth' => 600,
    'canvasHeight' => 500,
];
$hg->configure($config);
$hg->create($ft, 'img/HistogramExample01.png');
$hg->create($ft, 'img/HistogramExample02.png', ['bar' => true, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => false, 'frequency' => false, ]);
$hg->create($ft, 'img/HistogramExample03.png', ['bar' => true, 'frequencyPolygon' => false, 'cumulativeFrequencyPolygon' => true, 'frequency' => false, ]);
$hg->create($ft, 'img/HistogramExample04.png', ['bar' => true, 'frequencyPolygon' => false, 'cumulativeFrequencyPolygon' => false, 'frequency' => true, ]);
$hg->create($ft, 'img/HistogramExample05.png', ['bar' => true, 'frequencyPolygon' => false, 'cumulativeFrequencyPolygon' => true, 'frequency' => true, ]);
$hg->create($ft, 'img/HistogramExample06.png', ['bar' => true, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => true, 'frequency' => false, ]);
$hg->create($ft, 'img/HistogramExample07.png', ['bar' => true, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => false, 'frequency' => true, ]);
$hg->create($ft, 'img/HistogramExample08.png', ['bar' => true, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => true, 'frequency' => true, ]);
$hg->create($ft, 'img/HistogramExample09.png', ['bar' => false, 'frequencyPolygon' => false, 'cumulativeFrequencyPolygon' => false, 'frequency' => false, ]);
$hg->create($ft, 'img/HistogramExample10.png', ['bar' => false, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => false, 'frequency' => false, ]);
$hg->create($ft, 'img/HistogramExample11.png', ['bar' => false, 'frequencyPolygon' => false, 'cumulativeFrequencyPolygon' => true, 'frequency' => false, ]);
$hg->create($ft, 'img/HistogramExample12.png', ['bar' => false, 'frequencyPolygon' => false, 'cumulativeFrequencyPolygon' => false, 'frequency' => true, ]);
$hg->create($ft, 'img/HistogramExample13.png', ['bar' => false, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => true, 'frequency' => false, ]);
$hg->create($ft, 'img/HistogramExample14.png', ['bar' => false, 'frequencyPolygon' => true, 'cumulativeFrequencyPolygon' => true, 'frequency' => true, ]);
$hg->create($ft, 'img/HistogramExample15.png', ['bar' => true, 'frequencyPolygon' => true]);
$hg->create($ft, 'img/HistogramExample16.png', ['bar' => true, 'cumulativeFrequencyPolygon' => true]);
$hg->create($ft, 'img/HistogramExample17.png', ['bar' => true, 'frequency' => true]);
$hg->create($ft, 'img/HistogramExample18.png', ['bar' => true]);
$hg->create($ft, 'img/HistogramExample19.png', ['frequencyPolygon' => true]);
$hg->create($ft, 'img/HistogramExample20.png', ['cumulativeFrequencyPolygon' => true]);
$hg->create($ft, 'img/HistogramExample21.png', ['frequency' => true]);
