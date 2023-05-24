<?php
require('./class/Boxplot.php');

$faker = Faker\Factory::create();
$bp = new Boxplot();
$filePath = 'img/BoxplotExample.png';

$keys = [
    /*
    '5/10',
    '5/11',
    '5/12',
    '5/13',
    '5/14',
    '5/15',
    '5/16',
    '5/17',
    '5/18',
    '5/19',
    '5/20',
    */
    '5/21',
    '5/22',
    '5/23',
    '5/24',
    '5/25',
    /*
    '5/26',
    '5/27',
    '5/28',
    '5/29',
    '5/30',
    '5/31',
    */
];

foreach ($keys as $index => $key) {
    $data = [];
    for ($i = 0; $i < $faker->numberBetween(50, 600); $i++) {
        $data[] = $faker->numberBetween(600, 1100) / 100;
    }
    $bp->setData($data);
}
$bp->setSize(600, 400)
   ->setBoxWidth(20)
   ->outlierOn()
   ->jitterOn()
   ->meanOn()
   ->setGridHeightPitch(2)
   ->setLabels($keys)
   ->setLabelX('Index')
   ->setLabelY('Value')
   ->setCaption('Random Data')
   ->create()
   ->save($filePath);
