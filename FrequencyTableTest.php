<?php

require('./class/FrequencyTable.php');

$data = [
    //null, // This results in Exception
    [], // empty
    [50],   // 1 element
    [90, 20, 80, 30, 70, 40, 60],    // 7 elements
    [100, 10, 65, 40, 55, 90, 72, 84],   // 8 elements
];

$classRange = 20;
$ft = new FrequencyTable([
    'data' => [],
    'classRange' => $classRange,
]);
echo "# Test Results: FrequencyTableTest\n";
echo "\n";
foreach($data as $index => $d) {
    $ft->setData($d);
    asort($d);
    echo "## CASE:" . $index . ":\n";
    echo "- Data: [" . implode(',',$d) . "]\n";
    echo "- ClassRange: " . $ft->getClassRange() . "\n";
    echo "- Max: " . $ft->getMax($d) . "\n";
    echo "- Min: " . $ft->getMin($d) . "\n";
    echo "- Mode: " . $ft->getMode() . "\n";
    echo "- Average: " . $ft->getAverage() . "\n";
    echo "- Median: " . $ft->getMedian($d) . "\n";
    echo "- FirstQuartile: " . $ft->getFirstQuartile($d) . "\n";
    echo "- ThirdQuartile: " . $ft->getThirdQuartile($d) . "\n";
    echo "\n";
    $ft->show();
    echo "\n";
}
