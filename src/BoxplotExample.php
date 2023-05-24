<?php
require('./class/Boxplot.php');

$faker = Faker\Factory::create();
$bp = new Boxplot();
$filePath = 'img/BoxplotExample.png';

$keys = [
    '5/21',
    '5/22',
    '5/23',
    '5/24',
    '5/25',
];
$players = [
    'John',
    'Jake',
    'Hugo',
];
$dataset = [];
foreach ($players as $playre => $name) {
    $waightP = $faker->numberBetween(7, 13) * 10 / 100;
    $data = [];
    foreach ($keys as $index => $key) {
        $waightD = $faker->numberBetween(7, 13) * 10 / 100;
        $data[$index] = [];
        for ($i = 0; $i < $faker->numberBetween(50, 600); $i++) {
            $data[$index][] = $waightD * $waightP * $faker->numberBetween(600, 1100) / 100;
        }
    }
    $dataset[] = $data;
}
$bp->setDataset($dataset)
   ->setSize(600, 400)
   ->setBoxWidth(20)
   ->gridVerticalOn()
   ->outlierOn()
   ->jitterOn()
   ->meanOn()
   ->legendOn()
   ->setGridHeightPitch(2)
   ->setLabels($keys)
   ->setLabelX('Index')
   ->setLabelY('Value')
   ->setCaption('Random Data')
   ->setLegends($players)
   ->create()
   ->save($filePath);
