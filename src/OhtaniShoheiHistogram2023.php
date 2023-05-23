<?php

require('./class/FrequencyTable.php');
require('./class/Histogram.php');

function groupBy($csv, $keyColumn, $valueColumn) {
    // CSV MUST INCLUDES COLUMN NAMES IN HEAD LINE
    $data = [...$csv];
    $head = array_shift($data);
    $indexKeyColumn = array_search($keyColumn, $head);
    $indexValueColumn = array_search($valueColumn, $head);
    if (!$indexKeyColumn || !$indexValueColumn) return;
    $groupBy = [];
    foreach($data as $index => $row) {
        $key = $row[$indexKeyColumn];
        $groupBy[$key][] = $row[$indexValueColumn];
    }
    ksort($groupBy);
    return $groupBy;
}

function convertString2IntegerInArray($strings) {
    if (!is_array($strings)) return;
    foreach($strings as $value) {
        if (!is_numeric($value)) return;
    }
    $integers = [];
    foreach($strings as $key => $value) {
        $integers[$key] = (int) $value;
    }
    return $integers;
}

$csv = array_map('str_getcsv', file('csv/660271_data.csv'));

$ft = new FrequencyTable();
$ft->setClassRange(5);

$hg = new Histogram();

$groupBy = groupBy($csv, "game_date", "release_speed");
echo "# Pitching speed (MPH)\n\n";
echo "## Pitcher:\n\n";
echo "[Ohtani Shohei](https://www.mlb.com/player/shohei-ohtani-660271)\n\n";
echo "## Data Source\n\n[savant](https://baseballsavant.mlb.com/)\n\n";
echo "## Dates\n\n";
foreach(array_keys($groupBy) as $key) {
    echo "- [" . $key . "](#" . $key . ")\n";
}
foreach ($groupBy as $key => $data) {
    $d = convertString2IntegerInArray($data);
    echo "\n## " . $key . "\n\n";
    $ft->setData($d);
    $histogramPath = 'img/HistogramOhtaniShohei'.$key.'.png';
    $hg->create($ft, $histogramPath, ['bar' => true, 'frequency' => true]);
    echo "<details><summary>Properties</summary>\n\n";
    echo "|Property|Value|\n";
    echo "|:---|---:|\n";
    echo "|ClassRange|" . $ft->getClassRange() . "|\n";
    echo "|Max|" . $ft->getMax($d) . "|\n";
    echo "|Min|" . $ft->getMin($d) . "|\n";
    echo "|DataRange|" . $ft->getDataRange($d) . "|\n";
    echo "|Mode|" . $ft->getMode() . "|\n";
    echo "|Mean|" . number_format($ft->getMean(),1,'.',',') . "|\n";
    echo "|Median|" . $ft->getMedian($d) . "|\n";
    echo "|FirstQuartile|" . $ft->getFirstQuartile($d) . "|\n";
    echo "|ThirdQuartile|" . $ft->getThirdQuartile($d) . "|\n";
    echo "|InterQuartileRange|" . $ft->getInterQuartileRange($d) . "|\n";
    echo "|QuartileDeviation|" . $ft->getQuartileDeviation($d) . "|\n";
    echo "</details>\n\n";
    echo "<details><summary>Frequency Table</summary>\n\n";
    $ft->show();
    echo "</details>\n\n";
    echo "\n\n";
    echo "![Histogram:".$key."](".$histogramPath.")";
    echo "\n\n";
}