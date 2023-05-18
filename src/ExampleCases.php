<?php

require('./class/FrequencyTable.php');

$data = [
    //null, // This results in Exception
    [], // empty
    [50],   // 1 element
    [90, 20, 80, 30, 70, 40, 60],    // 7 elements
    [100, 10, 65, 40, 55, 90, 72, 84],   // 8 elements
    // HASH ARRAY
    [
        'Maria' => 168.2,
        'John' => 198.4,
        'Ashley' => 204.3,
        'Bob' => 172.5,
        'Kate' => 154.8,
        'Jake' => 138.1,
        'Susan' => 184.0,
        'Alex' => 124.9,
        'Ichiro' => 183.3,
        'Fei' => 164.7,
        'Elen' => 148.6,
        'Hoi' => 175.1,
        'Juan' => 179.6,
        'Mario' => 183.2,
    ],
];

$classRange = 20;
$ft = new FrequencyTable([
    'data' => [],
    'classRange' => $classRange,
]);
echo "# Results: Using FrequencyTable\n\n";
foreach($data as $index => $d) {
    $ft->setData($d);
    asort($d);
    echo "## CASE:" . $index . ":\n";
    echo "- Data: [" . implode(',',$d) . "]\n";
    echo "- ClassRange: " . $ft->getClassRange() . "\n";
    echo "- Max: " . $ft->getMax($d) . "\n";
    echo "- Min: " . $ft->getMin($d) . "\n";
    echo "- DataRange: " . $ft->getDataRange($d) . "\n";
    echo "- Mode: " . $ft->getMode() . "\n";
    echo "- Average: " . $ft->getAverage() . "\n";
    echo "- Median: " . $ft->getMedian($d) . "\n";
    echo "- FirstQuartile: " . $ft->getFirstQuartile($d) . "\n";
    echo "- ThirdQuartile: " . $ft->getThirdQuartile($d) . "\n";
    echo "- InterQuartileRange: " . $ft->getInterQuartileRange($d) . "\n";
    echo "\n";
    $ft->show();
    echo "\n";
}
