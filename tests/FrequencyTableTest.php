<?php declare(strict_types=1);

require('src/FrequencyTable.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpFrequencyTable\FrequencyTable;

final class FrequencyTableTest extends TestCase
{
    private $validColumns2Show = [
        'Class',
        'Frequency',
        'CumulativeFrequency',
        'RelativeFrequency',
        'CumulativeRelativeFrequency',
        'ClassValue',
        'ClassValue * Frequency',
    ];
    private $defaultColumns2Show = [
        'Class',
        'Frequency',
        'RelativeFrequency',
        'ClassValue',
        'ClassValue * Frequency',
    ];
    private $defaultTableSeparator = '|';
    private $classSeparator = ' ~ ';

    private function getAllCombinations($paramItems) {
        if (!is_array($paramItems)) return;
        if (empty($paramItems)) return;
        $items = array_values($paramItems);  // To avoid troubles caused by hash, and not to make any change to referrer array.
        $count = count($items);
        $numberOfAllPatterns = 2 ** $count;
        $bitPatterns = [];
        $format = '%0' . $count . 'b';
        for ($i = 0; $i < $numberOfAllPatterns; $i++) {
            $bitPatterns[] = sprintf($format, $i);
        }
        $combinations = [];
        foreach($bitPatterns as $bits) {
            $combination = [];
            foreach(str_split($bits) as $index => $bit) {
                if ((bool) $bit) $combination[] = $items[$index];
            }
            $combinations[] = $combination;
        }
        return $combinations;
    }

    private function clearStorage(): void
    {
        array_map('unlink', glob("storage/test*"));
    }

    public function test_constructor_can_create_instance_without_params(): void
    {
        $ft = new FrequencyTable();
        $this->assertNull($ft->getData());
        $this->assertNull($ft->getClassRange());
        $this->assertNull($ft->getTotal());
        $this->assertSame($this->defaultColumns2Show,$ft->getColumns2Show());
    }

    public function test_constructor_can_set_only_data(): void
    {
        $data = [10,30,40,45,50,52];
        $ft = new FrequencyTable(['data' => $data]);
        $this->assertSame($data, $ft->getData());
        $this->assertNull($ft->getClassRange());
        $this->assertEquals(0,$ft->getTotal());
        $this->assertSame($this->defaultColumns2Show,$ft->getColumns2Show());
    }

    public function test_constructor_can_set_data(): void
    {
        $data = [10,30,40,45,50,52];
        $classRange = 20;
        $ft = new FrequencyTable(['data' => $data, 'classRange' => $classRange]);

        $this->assertSame($data, $ft->getData());
        $this->assertSame($classRange, $ft->getClassRange());
        $this->assertIsNumeric($ft->getTotal());
        $this->assertSame($this->defaultColumns2Show,$ft->getColumns2Show());
    }

    public function test_constructor_can_set_columns2Show(): void
    {
        $columns2Show = [...$this->validColumns2Show];
        $pop = array_pop($columns2Show);
        $ft = new FrequencyTable(['columns2Show' => $columns2Show]);
        $this->assertSame($columns2Show, $ft->getColumns2Show());
        $this->assertFalse(in_array($pop,$ft->getColumns2Show()));
    }

    public function test_isSettableData_can_judge_correctly(): void
    {
        $cases = [
            ['data' => null, 'expected' => false, ],
            ['data' => true, 'expected' => false, ],
            ['data' => false, 'expected' => false, ],
            ['data' => 0, 'expected' => false, ],
            ['data' => 1.2, 'expected' => false, ],
            ['data' => 'hoge', 'expected' => false, ],
            ['data' => "1", 'expected' => false, ],
            ['data' => [], 'expected' => false, ],
            ['data' => [null], 'expected' => false, ],
            ['data' => [true], 'expected' => false, ],
            ['data' => [false], 'expected' => false, ],
            ['data' => ['hoge'], 'expected' => false, ],
            ['data' => ["1"], 'expected' => false, ],
            ['data' => [[]], 'expected' => false, ],
            ['data' => [0,null], 'expected' => false, ],
            ['data' => [0,true], 'expected' => false, ],
            ['data' => [0,false], 'expected' => false, ],
            ['data' => [0,'hoge'], 'expected' => false, ],
            ['data' => [0,"1"], 'expected' => false, ],
            ['data' => [0,[]], 'expected' => false, ],
            ['data' => [0,[1]], 'expected' => false, ],
            ['data' => [0,1], 'expected' => true, ],
            ['data' => [0,1.2], 'expected' => true, ],
            ['data' => [0,-1], 'expected' => true, ],
            ['data' => [0,-1.2], 'expected' => true, ],
            ['data' => [-1,0], 'expected' => true, ],
            ['data' => [-1,0.5], 'expected' => true, ],
            ['data' => [-1.2,0], 'expected' => true, ],
            ['data' => [-1.2,0.5], 'expected' => true, ],
            ['data' => [0.1,1,2.3,3,4.5,5], 'expected' => true, ],
        ];
        $ft = new FrequencyTable();
        
        foreach($cases as $index => $case) {
            $this->assertSame($case['expected'],$ft->isSettableData($case['data']));
        }
    }

    public function test_setData_can_set_correct_data(): void
    {
        $cases = [
            ['data' => [0],],
            ['data' => [1.2],],
            ['data' => [-1],],
            ['data' => [-1.5],],
            ['data' => [0,1,2],],
            ['data' => [0,1.5,2],],
            ['data' => [-1,-1.5,-1,-2],],
            ['data' => [-1.9,-1.5,-1.2,-2.5],],
            ['data' => ['Donald'=>1],],
            ['data' => ['Donald'=>1,'Joe'=>2],],
            ['data' => ['Donald'=>1.5],],
            ['data' => ['Donald'=>1.5,'Joe'=>2.5],],
        ];
        $ft = new FrequencyTable();

        foreach ($cases as $index => $case) {
            $this->assertTrue($ft->setData($case['data']));
            $this->assertSame($case['data'], $ft->getData());
        }
    }

    public function test_setData_can_set_null_by_invalid_data(): void
    {
        $cases = [
            ['data' => null, ],
            ['data' => true, ],
            ['data' => false, ],
            ['data' => 'hoge', ],
            ['data' => 0, ],
            ['data' => 1.2, ],
            ['data' => [], ],
            ['data' => [null], ],
            ['data' => [true], ],
            ['data' => [false], ],
            ['data' => ['hoge'], ],
            ['data' => [[]], ],
            ['data' => [0,1,2,'hoge'], ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $this->assertTrue($ft->setData([0,1,2]));
            $this->assertFalse(null===$ft->getData());
            $this->assertFalse($ft->setData($case['data']));
            $this->assertNull($ft->getData());
            $this->assertNull($ft->getTotal());
        }
    }

    public function test_getData_can_get_correct_data(): void
    {
        $dataNumericIndex = [10,20,30];
        $dataHashArray = [
            'hoge' => 1,
            'huga' => 2,
            'hugo' => 3,
        ];

        $ft = new FrequencyTable();

        $ft->setData(null);
        $this->assertNull($ft->getData());

        $ft->setData([]);
        $this->assertEmpty($ft->getData());

        $ft->setData($dataNumericIndex);
        $this->assertSame($dataNumericIndex, $ft->getData());
        foreach($dataNumericIndex as $index => $value) {
            $this->assertEquals($value, $ft->getData($index));
        }
        $this->assertNull($ft->getData(1234567890));    // This index does not exist in the array.

        $ft->setData($dataHashArray);
        $this->assertSame($dataHashArray, $ft->getData());
        foreach($dataHashArray as $key => $value) {
            $this->assertSame($value, $ft->getData($key));
        }
        $this->assertNull($ft->getData(''));
        $this->assertNull($ft->getData('keyWhichDoesNotExist'));    // This key does not exist in the array.
    }

    public function test_getDataRange_can_get_data_range_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => true, 'expect' => null, ],
            ['data' => false, 'expect' => null, ],
            ['data' => 0, 'expect' => null, ],
            ['data' => 1.2, 'expect' => null, ],
            ['data' => 'hoge', 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => [null], 'expect' => null, ],
            ['data' => [true], 'expect' => null, ],
            ['data' => [false], 'expect' => null, ],
            ['data' => ['hoge'], 'expect' => null, ],
            ['data' => [[]], 'expect' => null, ],
            ['data' => [[5]], 'expect' => null, ],
            ['data' => [5], 'expect' => 0, ],
            ['data' => [0,5], 'expect' => 5, ],
            ['data' => [0,5,9], 'expect' => 9, ],
            ['data' => [-5,-2,0,3], 'expect' => 8, ],
            ['data' => [1.5,10.5,90], 'expect' => 88.5, ],
            ['data' => [-9.5,-1.2,0.8], 'expect' => 10.3, ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->getDataRange($case['data']));
        }
    }

    public function test_isSettableClassRange_can_judge_correctly(): void
    {
        $cases = [
            ['classRange' => null, 'expect' => false, ],
            ['classRange' => 'hoge', 'expect' => false, ],
            ['classRange' => [], 'expect' => false, ],
            ['classRange' => [1], 'expect' => false, ],
            ['classRange' => '1', 'expect' => false, ],
            ['classRange' => '1.5', 'expect' => false, ],
            ['classRange' => false, 'expect' => false, ],
            ['classRange' => true, 'expect' => false, ],
            ['classRange' => 0, 'expect' => false, ],
            ['classRange' => 0.0, 'expect' => false, ],
            ['classRange' => -1, 'expect' => false, ],
            ['classRange' => -1.5, 'expect' => false, ],
            ['classRange' => 0.1, 'expect' => true, ],
            ['classRange' => 1, 'expect' => true, ],
            ['classRange' => 1.5, 'expect' => true, ],
            ['classRange' => PHP_INT_MAX, 'expect' => true, ],
            ['classRange' => PHP_FLOAT_MAX, 'expect' => true, ],
            ['classRange' => PHP_INT_MAX + 1, 'expect' => true, ],
            ['classRange' => PHP_FLOAT_MAX + 1, 'expect' => true, ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $this->assertEquals($case['expect'],$ft->isSettableClassRange($case['classRange']));
        }
    }

    public function test_setClassRange_can_set_valid_classRange(): void
    {
        // Only positive integer or positive float can be excepted.
        // Null is set when parameter in other types is specified.
        $cases = [
            [ 'classRange' => null, 'expect' => ['return' => false, 'get' => null, ], ],
            [ 'classRange' => -1, 'expect' => ['return' => false, 'get' => null, ], ],
            [ 'classRange' => 0, 'expect' => ['return' => false, 'get' => null, ], ],
            [ 'classRange' => 0.1, 'expect' => ['return' => true, 'get' => 0.1, ], ],
            [ 'classRange' => 1, 'expect' => ['return' => true, 'get' => 1, ], ],
            [ 'classRange' => 0x539, 'expect' => ['return' => true, 'get' => 1337, ], ],
            [ 'classRange' => 0b10100111001, 'expect' => ['return' => true, 'get' => 1337, ], ],
            [ 'classRange' => 1337e0, 'expect' => ['return' => true, 'get' => 1337.0, ], ],
            [ 'classRange' => '0.1', 'expect' => ['return' => false, 'get' => null, ], ],
            [ 'classRange' => '1', 'expect' => ['return' => false, 'get' => null, ], ],
            [ 'classRange' => '0x539', 'expect' => ['return' => false, 'get' => null, ], ],
            [ 'classRange' => '0b10100111001', 'expect' => ['return' => false, 'get' => null, ], ],
            [ 'classRange' => '1337e0', 'expect' => ['return' => false, 'get' => null, ], ],
            [ 'classRange' => 'hoge', 'expect' => ['return' => false, 'get' => null, ], ],
            [ 'classRange' => [], 'expect' => ['return' => false, 'get' => null, ], ],
            [ 'classRange' => [1], 'expect' => ['return' => false, 'get' => null, ], ],
            [ 'classRange' => PHP_INT_MAX, 'expect' => ['return' => true, 'get' => PHP_INT_MAX, ], ],
            [ 'classRange' => PHP_FLOAT_MAX, 'expect' => ['return' => true, 'get' => PHP_FLOAT_MAX, ], ],
            [ 'classRange' => PHP_INT_MAX + 1, 'expect' => ['return' => true, 'get' => PHP_INT_MAX + 1, ], ],
            [ 'classRange' => PHP_FLOAT_MAX + 1, 'expect' => ['return' => true, 'get' => PHP_FLOAT_MAX + 1, ], ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $this->assertSame($case['expect']['return'],$ft->setClassRange($case['classRange']));
            $this->assertSame($case['expect']['get'], $ft->getClassRange());
        }
    }

    public function test_getFrequencies_can_get_frequencies_correctly(): void
    {
        $cases = [
            [ 'classRange' => null, 'data' => null, 'expect' => [], ],
            [ 'classRange' => null, 'data' => [], 'expect' => [], ],
            [ 'classRange' => null, 'data' => [10,15,20,25,30,], 'expect' => [], ],
            [ 'classRange' => 20, 'data' => null, 'expect' => [], ],
            [ 'classRange' => 20, 'data' => [], 'expect' => [], ],
            [ 'classRange' => 20, 'data' => [10], 'expect' => [1], ],
            [ 'classRange' => 20, 'data' => [-20,], 'expect' => [1], ],
            [ 'classRange' => 20, 'data' => [10,15,20,45,51,55,58,74,78,93], 'expect' => [2,1,4,2,1], ],
            [ 'classRange' => 20, 'data' => [10,10.02,15,20,20.01,25,30,39.99,40,100,], 'expect' => [3,5,1,0,0,1], ],
            [ 'classRange' => 20, 'data' => [-100,-99,-20,0,10,15,20,25,30,], 'expect' => [2,0,0,0,1,3,3], ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $ft->setClassRange($case['classRange']);
            $ft->setData($case['data']);
            $this->assertSame($case['expect'],$ft->getFrequencies());
        }
    }

    public function test_getClasses_can_get_classes_correctly(): void
    {
        $cases = [
            [ 'classRange' => null, 'data' => null, 'expect' => [], ],
            [ 'classRange' => null, 'data' => [], 'expect' => [], ],
            [ 'classRange' => null, 'data' => [10], 'expect' => [], ],
            [ 'classRange' => null, 'data' => [10,15,20,25,30,], 'expect' => [], ],
            [ 'classRange' => 20, 'data' => null, 'expect' => [], ],
            [ 'classRange' => 20, 'data' => [], 'expect' => [], ],
            [ 'classRange' => 20, 'data' => [10], 'expect' => [['bottom' => 0, 'top' => 20, ], ], ],
            [
                'classRange' => 20,
                'data' => [-20,],
                'expect' => [
                    ['bottom' => -20, 'top' => 0, ],
                ],
            ],
            [
                'classRange' => 20,
                'data' => [20,], 
                'expect' => [
                    ['bottom' => 20, 'top' => 40, ],
                ], 
            ],
            [
                'classRange' => 20,
                'data' => [10,15,20,45,51,55,58,74,78,93],
                'expect' => [
                    ['bottom' => 0, 'top' => 20, ],
                    ['bottom' => 20, 'top' => 40, ],
                    ['bottom' => 40, 'top' => 60, ],
                    ['bottom' => 60, 'top' => 80, ],
                    ['bottom' => 80, 'top' => 100, ],
                ],
            ],
            [
                'classRange' => 20,
                'data' => [10,10.02,15,20,20.01,25,30,39.99,40,100,],
                'expect' => [
                    ['bottom' => 0, 'top' => 20, ],
                    ['bottom' => 20, 'top' => 40, ],
                    ['bottom' => 40, 'top' => 60, ],
                    ['bottom' => 60, 'top' => 80, ],
                    ['bottom' => 80, 'top' => 100, ],
                    ['bottom' => 100, 'top' => 120, ],
                ],
            ],
            [
                'classRange' => 20,
                'data' => [-100,-99,-20,0,10,15,20,25,30,],
                'expect' => [
                    ['bottom' => -100, 'top' => -80, ],
                    ['bottom' => -80, 'top' => -60, ],
                    ['bottom' => -60, 'top' => -40, ],
                    ['bottom' => -40, 'top' => -20, ],
                    ['bottom' => -20, 'top' => 0, ],
                    ['bottom' => 0, 'top' => 20, ],
                    ['bottom' => 20, 'top' => 40, ],
                ],
            ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $ft->setClassRange($case['classRange']);
            $ft->setData($case['data']);
            $this->assertSame($case['expect'],$ft->getClasses());
        }
    }

    public function test_isSettableClass_can_judge_correctly(): void
    {
        $cases = [
            ['class' => null, 'expect' => false, ],
            ['class' => true, 'expect' => false, ],
            ['class' => false, 'expect' => false, ],
            ['class' => 0, 'expect' => false, ],
            ['class' => 1, 'expect' => false, ],
            ['class' => 1.5, 'expect' => false, ],
            ['class' => 'hoge', 'expect' => false, ],
            ['class' => [], 'expect' => false, ],
            ['class' => [null], 'expect' => false, ],
            ['class' => [true], 'expect' => false, ],
            ['class' => [false], 'expect' => false, ],
            ['class' => [0], 'expect' => false, ],
            ['class' => [1], 'expect' => false, ],
            ['class' => [1.5], 'expect' => false, ],
            ['class' => ['bottom' => 0], 'expect' => false, ],
            ['class' => ['top' => 20], 'expect' => false, ],
            ['class' => [['bottom' => 20, 'top' => 40]], 'expect' => false, ],
            ['class' => ['bottom' => null, 'top' => null], 'expect' => false, ],
            ['class' => ['bottom' => 20, 'top' => null], 'expect' => false, ],
            ['class' => ['bottom' => null, 'top' => 40], 'expect' => false, ],
            ['class' => ['bottom' => true, 'top' => true], 'expect' => false, ],
            ['class' => ['bottom' => 20, 'top' => true], 'expect' => false, ],
            ['class' => ['bottom' => true, 'top' => 40], 'expect' => false, ],
            ['class' => ['bottom' => false, 'top' => false], 'expect' => false, ],
            ['class' => ['bottom' => 20, 'top' => false], 'expect' => false, ],
            ['class' => ['bottom' => false, 'top' => 40], 'expect' => false, ],
            ['class' => ['bottom' => [], 'top' => []], 'expect' => false, ],
            ['class' => ['bottom' => 20, 'top' => []], 'expect' => false, ],
            ['class' => ['bottom' => [], 'top' => 40], 'expect' => false, ],
            ['class' => ['bottom' => ['bottom' => 20, 'top' => 40], 'top' => ['bottom' => 20, 'top' => 40]], 'expect' => false, ],
            ['class' => ['bottom' => 20, 'top' => ['bottom' => 20, 'top' => 40]], 'expect' => false, ],
            ['class' => ['bottom' => ['bottom' => 20, 'top' => 40], 'top' => 40], 'expect' => false, ],
            ['class' => ['bottom' => 20, 'top' => "40"], 'expect' => false, ],
            ['class' => ['bottom' => "20", 'top' => 40], 'expect' => false, ],
            ['class' => ['bottom' => "20", 'top' => "40"], 'expect' => false, ],
            ['class' => ['bottom' => 40, 'top' => 20], 'expect' => false, ],
            ['class' => ['bottom' => 20, 'top' => 20], 'expect' => false, ],
            ['class' => ['bottom' => 0, 'top' => 20], 'expect' => true, ],
            ['class' => ['bottom' => 20, 'top' => 40], 'expect' => true, ],
            ['class' => ['bottom' => -40, 'top' => 20], 'expect' => true, ],
            ['class' => ['bottom' => -40, 'top' => -20], 'expect' => true, ],
            ['class' => ['bottom' => 40.5, 'top' => 20.1], 'expect' => false, ],
            ['class' => ['bottom' => 20.5, 'top' => 20.5], 'expect' => false, ],
            ['class' => ['bottom' => 0.5, 'top' => 20.2], 'expect' => true, ],
            ['class' => ['bottom' => 20.8, 'top' => 40.9], 'expect' => true, ],
            ['class' => ['bottom' => -40.2, 'top' => 20.5], 'expect' => true, ],
            ['class' => ['bottom' => -40.2, 'top' => -20.5], 'expect' => true, ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->isSettableClass($case['class']));
        }
    }

    public function test_getFrequency_can_get_frequency_correctly(): void
    {
        $cases = [
            ['data' => null, 'class' => null, 'expect' => null, ],
            ['data' => null, 'class' => [], 'expect' => null, ],
            ['data' => [], 'class' => ['bottom' => 0, 'top' => 20], 'expect' => null, ],
            ['data' => 'hoge', 'class' => ['bottom' => 0, 'top' => 20], 'expect' => null, ],
            ['data' => [10,20,30], 'class' => 'hoge', 'expect' => null, ],
            ['data' => [], 'class' => null, 'expect' => null, ],
            ['data' => [], 'class' => [], 'expect' => null, ],
            ['data' => [], 'class' => ['bottom' => 0,], 'expect' => null, ],
            ['data' => [], 'class' => ['bottom' => 0, 'top' => 20], 'expect' => null, ],
            ['data' => [], 'class' => [1], 'expect' => null, ],
            ['data' => [10], 'class' => null, 'expect' => null, ],
            ['data' => [10], 'class' => [], 'expect' => null, ],
            ['data' => [10], 'class' => [10], 'expect' => null, ],
            ['data' => [10], 'class' => ['bottom' => 0,], 'expect' => null, ],
            ['data' => [10], 'class' => ['bottom' => 0, 'top' => 20], 'expect' => 1, ],
            ['data' => [10], 'class' => ['top' => 20], 'expect' => null, ],
            ['data' => [5,9,10,15,20,25,30,35,40], 'class' => ['bottom' => 10, 'top' => 30], 'expect' => 4, ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->getFrequency($case['data'],$case['class']));
        }
    }

    public function test_getCumulativeFrequency_can_get_cumulative_frequency_correctly(): void
    {
        $cases = [
            ['frequencies' => null, 'index' => null, 'expect' => null, ],
            ['frequencies' => true, 'index' => null, 'expect' => null, ],
            ['frequencies' => false, 'index' => null, 'expect' => null, ],
            ['frequencies' => 0, 'index' => null, 'expect' => null, ],
            ['frequencies' => 1.2, 'index' => null, 'expect' => null, ],
            ['frequencies' => "0", 'index' => null, 'expect' => null, ],
            ['frequencies' => "1.2", 'index' => null, 'expect' => null, ],
            ['frequencies' => [], 'index' => null, 'expect' => null, ],
            ['frequencies' => [null], 'index' => null, 'expect' => null, ],
            ['frequencies' => [true], 'index' => null, 'expect' => null, ],
            ['frequencies' => [false], 'index' => null, 'expect' => null, ],
            ['frequencies' => [0], 'index' => null, 'expect' => null, ],
            ['frequencies' => [1.2], 'index' => null, 'expect' => null, ],
            ['frequencies' => ["0"], 'index' => null, 'expect' => null, ],
            ['frequencies' => ["1.2"], 'index' => null, 'expect' => null, ],
            ['frequencies' => [[]], 'index' => null, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => null, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => true, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => false, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 1.0, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => "1", 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => [], 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => [1], 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => -1, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 5, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 0, 'expect' => 0, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 1, 'expect' => 1, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 2, 'expect' => 3, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 3, 'expect' => 6, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 4, 'expect' => 10, ],
            ['frequencies' => [0 => 0, 1 => 1, 3 => 3, 4 => 4, ], 'index' => 0, 'expect' => 0, ],
            ['frequencies' => [0 => 0, 1 => 1, 3 => 3, 4 => 4, ], 'index' => 1, 'expect' => 1, ],
            ['frequencies' => [0 => 0, 1 => 1, 3 => 3, 4 => 4, ], 'index' => 2, 'expect' => 4, ],
            ['frequencies' => [0 => 0, 1 => 1, 3 => 3, 4 => 4, ], 'index' => 3, 'expect' => 8, ], // array index is renumbered, [0=>0,1=>1,2=>3,3=>4]
            ['frequencies' => [0 => 0, 1 => 1, 3 => 3, 4 => 4, ], 'index' => 4, 'expect' => null, ], // array index is renumbered. [0=>0,1=>1,2=>3,3=>4], index of 4 does not exist.
        ];
        $ft = new FrequencyTable();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->getCumulativeFrequency($case['frequencies'],$case['index']));
        }
    }

    public function test_getMin_can_get_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => 'hoge', 'expect' => null, ],
            ['data' => 10, 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => ['hoge','huga'], 'expect' => null, ],
            ['data' => [10], 'expect' => 10, ],
            ['data' => [-10], 'expect' => -10, ],
            ['data' => [-10,0,5], 'expect' => -10, ],
        ];
        $ft = new FrequencyTable();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->getMin($case['data']));
        }
    }

    public function test_getMax_can_get_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => 'hoge', 'expect' => null, ],
            ['data' => 10, 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => ['hoge','huga'], 'expect' => null, ],
            ['data' => [10], 'expect' => 10, ],
            ['data' => [-10], 'expect' => -10, ],
            ['data' => [-10,0,5], 'expect' => 5, ],
            ['data' => [100,-15,30,-21,148,45], 'expect' => 148, ],
        ];
        $ft = new FrequencyTable();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->getMax($case['data']));
        }
    }

    public function test_setTotal_and_getTotal_can_work_correctly(): void
    {
        $cases = [
            ['classRange' => 2, 'data' => null, 'setTotal' => null, 'expect' => ['setTotal' => false, 'getTotal' => null, ], ],
            ['classRange' => 2, 'data' => null, 'setTotal' => [], 'expect' => ['setTotal' => false, 'getTotal' => null, ], ],
            ['classRange' => 2, 'data' => null, 'setTotal' => 0, 'expect' => ['setTotal' => false, 'getTotal' => null, ], ],
            ['classRange' => 2, 'data' => null, 'setTotal' => true, 'expect' => ['setTotal' => false, 'getTotal' => null, ], ],
            ['classRange' => 2, 'data' => null, 'setTotal' => false, 'expect' => ['setTotal' => false, 'getTotal' => null, ], ],
            ['classRange' => 2, 'data' => null, 'setTotal' => [0,1,2,3,4,], 'expect' => ['setTotal' => true, 'getTotal' => 10, ], ], // This returns the total of array values.
            ['classRange' => 2, 'data' => [0,1,2,3,4,], 'setTotal' => null, 'expect' => ['setTotal' => false, 'getTotal' => 5, ], ], // This returns the total of Frequencies, ie the number of elements.
        ];
        $ft = new FrequencyTable();
        
        foreach($cases as $index => $case) {
            $ft->setClassRange($case['classRange']);
            $ft->setData($case['data']);
            $this->assertSame($case['expect']['setTotal'],$ft->setTotal($case['setTotal']));
            $this->assertSame($case['expect']['getTotal'],$ft->getTotal());
        }
    }

    public function test_getClassValue_can_get_class_value_correctly(): void
    {
        $cases = [
            ['class' => null, 'expect' => null, ],
            ['class' => [], 'expect' => null, ],
            ['class' => '', 'expect' => null, ],
            ['class' => true, 'expect' => null, ],
            ['class' => false, 'expect' => null, ],
            ['class' => 0, 'expect' => null, ],
            ['class' => 'hoge', 'expect' => null, ],
            ['class' => 12.3, 'expect' => null, ],
            ['class' => ['hoge'], 'expect' => null, ],
            ['class' => ['bottom','top'], 'expect' => null, ],
            ['class' => ['bottom' => 20, ], 'expect' => null, ],
            ['class' => ['top' => 20, ], 'expect' => null, ],
            ['class' => ['bottom' => 40, 'top' => 30, ], 'expect' => null, ],
            ['class' => ['bottom' => 30, 'top' => 30, ], 'expect' => null, ],
            ['class' => ['bottom' => 20, 'top' => 30, ], 'expect' => 25, ],
            ['class' => ['bottom' => "20", 'top' => 30, ], 'expect' => null, ],
            ['class' => ['bottom' => 20, 'top' => "30", ], 'expect' => null, ],
            ['class' => ['bottom' => "20", 'top' => "30", ], 'expect' => null, ],
            ['class' => ['bottom' => 20.5, 'top' => 30.6, ], 'expect' => 25.55, ],
            ['class' => ['bottom' => -20, 'top' => -30, ], 'expect' => null, ],
            ['class' => ['bottom' => -20, 'top' => 30, ], 'expect' => 5, ],
            ['class' => ['bottom' => -20.5, 'top' => -10.4, ], 'expect' => -15.45, ],
            ['class' => ['bottom' => 20, 'top' => 30, 'middle' => 28, ], 'expect' => 25, ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->getClassValue($case['class']));
        }
    }

    public function test_getRelativeFrequency_can_get_relative_frequency_correctly(): void
    {
        $cases = [
            ['frequencies' => null, 'frequency' => null, 'expect' => null, ],
            ['frequencies' => [], 'frequency' => null, 'expect' => null, ],
            ['frequencies' => [1,2,3,4,], 'frequency' => null, 'expect' => null, ],
            ['frequencies' => [1,2,3,4,], 'frequency' => true, 'expect' => null, ],
            ['frequencies' => [1,2,3,4,], 'frequency' => false, 'expect' => null, ],
            ['frequencies' => [1,2,3,4,], 'frequency' => [], 'expect' => null, ],
            ['frequencies' => [1,2,3,4,], 'frequency' => [0], 'expect' => null, ],
            ['frequencies' => [1,2,3,4,], 'frequency' => [2], 'expect' => null, ],
            ['frequencies' => [1,2,3,4,], 'frequency' => 0, 'expect' => 0, ],
            ['frequencies' => [1,2,3,4,], 'frequency' => 2, 'expect' => 0.2, ],
            ['frequencies' => [1,2,3,4,], 'frequency' => -2, 'expect' => null, ],   // Frequency must be a positive integer or zero.
            ['frequencies' => [1,2,3,4,], 'frequency' => 0.0, 'expect' => null, ],  // Frequency must be a positive integer or zero.
            ['frequencies' => [1,2,3,4,], 'frequency' => 2.0, 'expect' => null, ],  // Frequency must be a positive integer or zero.
            ['frequencies' => [1,2,3,4,], 'frequency' => 10, 'expect' => 1, ],
            ['frequencies' => [1,2,3,4,], 'frequency' => 11, 'expect' => null, ],   // Frequency must be less than or equal to total.
            ['frequencies' => null, 'frequency' => 0, 'expect' => null, ],
            ['frequencies' => [], 'frequency' => 0, 'expect' => null, ],
            ['frequencies' => [], 'frequency' => 2, 'expect' => null, ],
            ['frequencies' => [0], 'frequency' => 0, 'expect' => null, ], // Total of frequencies must be a positive integer.
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $ft->setTotal($case['frequencies']);
            $this->assertSame($case['expect'],$ft->getRelativeFrequency($case['frequency']));
        }
    }

    public function test_getCumulativeRelativeFrequency_can_get_cumulative_relative_frequency_correctly(): void
    {
        $cases = [
            ['frequencies' => null, 'index' => null, 'expect' => null, ],
            ['frequencies' => true, 'index' => null, 'expect' => null, ],
            ['frequencies' => false, 'index' => null, 'expect' => null, ],
            ['frequencies' => 0, 'index' => null, 'expect' => null, ],
            ['frequencies' => 1.2, 'index' => null, 'expect' => null, ],
            ['frequencies' => "0", 'index' => null, 'expect' => null, ],
            ['frequencies' => "1.2", 'index' => null, 'expect' => null, ],
            ['frequencies' => [], 'index' => null, 'expect' => null, ],
            ['frequencies' => [null], 'index' => null, 'expect' => null, ],
            ['frequencies' => [true], 'index' => null, 'expect' => null, ],
            ['frequencies' => [false], 'index' => null, 'expect' => null, ],
            ['frequencies' => [0], 'index' => null, 'expect' => null, ],
            ['frequencies' => [1.2], 'index' => null, 'expect' => null, ],
            ['frequencies' => ["0"], 'index' => null, 'expect' => null, ],
            ['frequencies' => ["1.2"], 'index' => null, 'expect' => null, ],
            ['frequencies' => [[]], 'index' => null, 'expect' => null, ],
            ['frequencies' => [[0]], 'index' => null, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => null, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => true, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => false, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 1.2, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => "0", 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => "1.2", 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => [], 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => [0], 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => [1.2], 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 0, 'expect' => 0/10, ], // total=10, rf=[0,0.1,0.2,0.3,0.4]
            ['frequencies' => [0,1,2,3,4], 'index' => 1, 'expect' => 0/10 + 1/10, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 2, 'expect' => 0/10 + 1/10 + 2/10, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 3, 'expect' => 0/10 + 1/10 + 2/10 + 3/10, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 4, 'expect' => 0/10 + 1/10 + 2/10 + 3/10 + 4/10, ],
            ['frequencies' => [0,1,2,3,4], 'index' => -1, 'expect' => null, ],
            ['frequencies' => [0,1,2,3,4], 'index' => 5, 'expect' => null, ],
            ['frequencies' => [0=>0,1=>1,3=>3,4=>4], 'index' => 0, 'expect' => 0/8, ], // array index is renumbered. [0=>0,1=>1,2=>3,3=>4]
            ['frequencies' => [0=>0,1=>1,3=>3,4=>4], 'index' => 1, 'expect' => 0/8 + 1/8, ], // total=8, rf=[0,0.125,0.375,0.5]
            ['frequencies' => [0=>0,1=>1,3=>3,4=>4], 'index' => 2, 'expect' => 0/8 + 1/8 + 3/8, ],
            ['frequencies' => [0=>0,1=>1,3=>3,4=>4], 'index' => 3, 'expect' => 0/8 + 1/8 + 3/8 + 4/8, ],
            ['frequencies' => [0=>0,1=>1,3=>3,4=>4], 'index' => 4, 'expect' => null, ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $ft->setTotal($case['frequencies']);
            $this->assertSame($case['expect'],$ft->getCumulativeRelativeFrequency($case['frequencies'],$case['index']));
        }
    }

    public function test_getMean_can_get_mean_correctly(): void
    {
        $cases = [
            ['classRange' => 10, 'data' => null, 'expect' => null, ],
            ['classRange' => 10, 'data' => [], 'expect' => null, ],
            ['classRange' => 10, 'data' => [0], 'expect' => 5, ], // Frequencies=[1], ClassValue=5, Mean=5
            ['classRange' => 10, 'data' => [10], 'expect' => 15, ], // Frequencies=[1], ClassValue=15, Mean=15
            ['classRange' => 10, 'data' => [0,5,10,15,20], 'expect' => 13, ], // Frequencies=[2,2,1], ClassValues=[5,15,25], Mean=13
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $ft->setClassRange($case['classRange']);
            $ft->setData($case['data']);
            $this->assertSame($case['expect'],$ft->getMean());
        }
    }

    public function test_getMode_can_get_mode_correctly(): void
    {
        $cases = [
            ['classRange' => null, 'data' => null, 'expect' => null, ],
            ['classRange' => true, 'data' => null, 'expect' => null, ],
            ['classRange' => false, 'data' => null, 'expect' => null, ],
            ['classRange' => [], 'data' => null, 'expect' => null, ],
            ['classRange' => 0, 'data' => null, 'expect' => null, ],
            ['classRange' => 10, 'data' => null, 'expect' => null, ],
            ['classRange' => 'hoge', 'data' => null, 'expect' => null, ],
            ['classRange' => null, 'data' => true, 'expect' => null, ],
            ['classRange' => null, 'data' => false, 'expect' => null, ],
            ['classRange' => null, 'data' => [], 'expect' => null, ],
            ['classRange' => null, 'data' => 0, 'expect' => null, ],
            ['classRange' => null, 'data' => 10, 'expect' => null, ],
            ['classRange' => null, 'data' => 'hoge', 'expect' => null, ],
            ['classRange' => 10, 'data' => 0, 'expect' => null, ],
            ['classRange' => 10, 'data' => [], 'expect' => null, ],
            ['classRange' => 10, 'data' => 'hoge', 'expect' => null, ],
            ['classRange' => 10, 'data' => [0], 'expect' => 5, ], // Frequencies=[1], ClassValues=[5], Mode=5
            ['classRange' => 10, 'data' => [0,10,12,20,24,28], 'expect' => 25, ], // Frequencise=[1,2,3], ClassValues=[5,15,25], Mode=25
            ['classRange' => -10, 'data' => [0,10,12,20,24,28], 'expect' => null, ], // ClassRange must be a positive number.
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $ft->setClassRange($case['classRange']);
            $ft->setData($case['data']);
            $this->assertSame($case['expect'],$ft->getMode());
        }
    }

    public function test_getMedian_can_get_median_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => true, 'expect' => null, ],
            ['data' => false, 'expect' => null, ],
            ['data' => 0, 'expect' => null, ],
            ['data' => 'hoge', 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => [[0]], 'expect' => null, ],
            ['data' => [null], 'expect' => null, ],
            ['data' => [0], 'expect' => 0, ],
            ['data' => [0,1], 'expect' => 0.5, ],
            ['data' => [0,1,2], 'expect' => 1, ],
            ['data' => [0,1,2,3], 'expect' => 1.5, ],
            ['data' => [0,1,2,3,4], 'expect' => 2, ],
            ['data' => [3,0,4,2,1], 'expect' => 2, ],
            ['data' => [1,4,0,2], 'expect' => 1.5, ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->getMedian($case['data']));
        }
    }

    public function test_getMedianClass_can_get_median_class(): void
    {
        $cases = [
            ['classRange' => null, 'data' => null, 'expect' => null, ],
            ['classRange' => 10, 'data' => null, 'expect' => null, ],
            ['classRange' => null, 'data' => [10,20,30], 'expect' => null, ],
            ['classRange' => 10, 'data' => [10,20,30], 'expect' => ['index' => 1, 'bottom' => 20, 'top' => 30, ], ],
            ['classRange' => 10, 'data' => [10], 'expect' => ['index' => 0, 'bottom' => 10, 'top' => 20], ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $ft->setClassRange($case['classRange']);
            $ft->setData($case['data']);
            $this->assertSame($case['expect'],$ft->getMedianClass());
        }
    }

    public function test_getFirstQuartile_can_get_fist_quartile_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => true, 'expect' => null, ],
            ['data' => false, 'expect' => null, ],
            ['data' => 0, 'expect' => null, ],
            ['data' => 'hoge', 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => [[0]], 'expect' => null, ],
            ['data' => ['hoge'], 'expect' => null, ],
            ['data' => [1,2,'hoge'], 'expect' => null, ],
            ['data' => [1,2], 'expect' => 1, ],
            ['data' => [1,2,3], 'expect' => 1, ],
            ['data' => [1,2,3,4], 'expect' => 1.5, ],
            ['data' => [1,2,3,4,5], 'expect' => 1.5, ],
            ['data' => [3,1,5,2,4], 'expect' => 1.5, ],
        ];
        $ft = new FrequencyTable();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->getFirstQuartile($case['data']));
        }
    }

    public function test_getThirdQuartile_can_get_fist_quartile_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => true, 'expect' => null, ],
            ['data' => false, 'expect' => null, ],
            ['data' => 0, 'expect' => null, ],
            ['data' => 'hoge', 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => [[0]], 'expect' => null, ],
            ['data' => ['hoge'], 'expect' => null, ],
            ['data' => [1,2,'hoge'], 'expect' => null, ],
            ['data' => [1,2], 'expect' => 2, ],
            ['data' => [1,2,3], 'expect' => 3, ],
            ['data' => [1,2,3,4], 'expect' => 3.5, ],
            ['data' => [1,2,3,4,5], 'expect' => 4.5, ],
            ['data' => [3,1,5,2,4], 'expect' => 4.5, ],
        ];
        $ft = new FrequencyTable();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->getThirdQuartile($case['data']));
        }
    }

    public function test_getInterQuartileRange_can_get_inter_quartile_range_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => true, 'expect' => null, ],
            ['data' => false, 'expect' => null, ],
            ['data' => 0, 'expect' => null, ],
            ['data' => 1.2, 'expect' => null, ],
            ['data' => 'hoge', 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => [null], 'expect' => null, ],
            ['data' => [true], 'expect' => null, ],
            ['data' => [false], 'expect' => null, ],
            ['data' => ['hoge'], 'expect' => null, ],
            ['data' => [[]], 'expect' => null, ],
            ['data' => [[0,5,10]], 'expect' => null, ],
            ['data' => [0], 'expect' => 0, ],
            ['data' => [0,1], 'expect' => 1, ],
            ['data' => [1,3,5], 'expect' => 4, ],
            ['data' => [0.5, 2.5, 3.5, 4.5], 'expect' => 2.5, ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->getInterQuartileRange($case['data']));
        }
    }

    public function test_getQuartileDeviation_can_get_quartile_deviation_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null],
            ['data' => true, 'expect' => null],
            ['data' => false, 'expect' => null],
            ['data' => 0, 'expect' => null],
            ['data' => 1.2, 'expect' => null],
            ['data' => '0', 'expect' => null],
            ['data' => [], 'expect' => null],
            ['data' => [null], 'expect' => null],
            ['data' => [true], 'expect' => null],
            ['data' => [false], 'expect' => null],
            ['data' => ['0'], 'expect' => null],
            ['data' => ['1.2'], 'expect' => null],
            ['data' => [[]], 'expect' => null],
            ['data' => [[0]], 'expect' => null],
            ['data' => [[0,1]], 'expect' => null],
            ['data' => [0], 'expect' => 0],
            ['data' => [1], 'expect' => 0],
            ['data' => [0,10], 'expect' => 5],
            ['data' => [0,10,30], 'expect' => 15],
            ['data' => [0.5,10.2,30.5], 'expect' => 15.0],
            ['data' => [0,10,20,30], 'expect' => 10],
            ['data' => [0.5,10.5,20.5,30.5], 'expect' => 10.0], // Q1=5.5, Q3=25.5, IQR=20.0, QD=10.0
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->getQuartileDeviation($case['data']));
        }
    }

    public function test_setTableSeparator_and_getTableSeparator_can_work_correctly(): void
    {
        $defaultSeparator = $this->defaultTableSeparator;
        $cases = [
            ['separator' => null, 'expect' => [ 'return' => false, 'separator' => $defaultSeparator, ], ],
            ['separator' => true, 'expect' => [ 'return' => false, 'separator' => $defaultSeparator, ], ],
            ['separator' => false, 'expect' => [ 'return' => false, 'separator' => $defaultSeparator, ], ],
            ['separator' => 0, 'expect' => [ 'return' => false, 'separator' => $defaultSeparator, ], ],
            ['separator' => 1.5, 'expect' => [ 'return' => false, 'separator' => $defaultSeparator, ], ],
            ['separator' => [], 'expect' => [ 'return' => false, 'separator' => $defaultSeparator, ], ],
            ['separator' => '', 'expect' => [ 'return' => true, 'separator' => '', ], ],
            ['separator' => 'hoge', 'expect' => [ 'return' => true, 'separator' => 'hoge', ], ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $ft->setTableSeparator($defaultSeparator);
            $this->assertSame($case['expect']['return'],$ft->setTableSeparator($case['separator']));
            $this->assertSame($case['expect']['separator'],$ft->getTableSeparator());
        }
    }

    public function test_setDefaultTableSeparator_can_set_default_table_separator(): void
    {
        $ft = new FrequencyTable();
        $ft->setTableSeparator('');
        $ft->setDefaultTableSeparator();
        $this->assertSame($this->defaultTableSeparator, $ft->getTableSeparator());
    }

    public function test_isSettableColumns2Show_can_judge_columns_2_show_correctly(): void
    {
        $cases = [
            ['columns' => null, 'expect' => false],
            ['columns' => true, 'expect' => false],
            ['columns' => false, 'expect' => false],
            ['columns' => '', 'expect' => false],
            ['columns' => 'hoge', 'expect' => false],
            ['columns' => 0, 'expect' => false],
            ['columns' => 1.2, 'expect' => false],
            ['columns' => [], 'expect' => false],
            ['columns' => [0], 'expect' => false],
            ['columns' => ['hoge'], 'expect' => false],
            ['columns' => [...$this->validColumns2Show, 'hoge'], 'expect' => false],
            ['columns' => [...$this->validColumns2Show], 'expect' => true],
            ['columns' => ['Class'], 'expect' => true],
            ['columns' => ['Class','Class','Class','Class','Class'], 'expect' => true],
            ['columns' => ['Frequency'], 'expect' => true],
            ['columns' => ['RelativeFrequency'], 'expect' => true],
            ['columns' => ['ClassValue'], 'expect' => true],
            ['columns' => ['ClassValue * Frequency'], 'expect' => true],
            ['columns' => ['Class','Frequency'], 'expect' => true],
            ['columns' => ['Class','Frequency','RelativeFrequency'], 'expect' => true],
            ['columns' => ['Class','Frequency','RelativeFrequency','ClassValue'], 'expect' => true],
            ['columns' => ['Class','Frequency','RelativeFrequency','ClassValue * Frequency'], 'expect' => true],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->isSettableColumns2Show($case['columns']));
        }
    }

    public function test_getValidColumns2Show_can_get_valid_columns_2_show(): void
    {
        $ft = new FrequencyTable();
        $this->assertSame($this->validColumns2Show,$ft->getValidColumns2Show());
    }

    public function test_setColumns2Show_can_set_columns_2_show_correctly(): void
    {
        $cases = [
            ['columns' => null, 'expect' => ['return' => false, 'columns' => $this->validColumns2Show ], ],
            ['columns' => true, 'expect' => ['return' => false, 'columns' => $this->validColumns2Show ], ],
            ['columns' => false, 'expect' => ['return' => false, 'columns' => $this->validColumns2Show ], ],
            ['columns' => '', 'expect' => ['return' => false, 'columns' => $this->validColumns2Show ], ],
            ['columns' => 'hoge', 'expect' => ['return' => false, 'columns' => $this->validColumns2Show ], ],
            ['columns' => 0, 'expect' => ['return' => false, 'columns' => $this->validColumns2Show ], ],
            ['columns' => 1.2, 'expect' => ['return' => false, 'columns' => $this->validColumns2Show ], ],
            ['columns' => [], 'expect' => ['return' => false, 'columns' => $this->validColumns2Show ], ],
            ['columns' => [0], 'expect' => ['return' => false, 'columns' => $this->validColumns2Show ], ],
            ['columns' => ['hoge'], 'expect' => ['return' => false, 'columns' => $this->validColumns2Show ], ],
            ['columns' => [...$this->validColumns2Show,'hoge'], 'expect' => ['return' => false, 'columns' => $this->validColumns2Show ], ],
            ['columns' => $this->validColumns2Show, 'expect' => ['return' => true, 'columns' => $this->validColumns2Show ], ],
            ['columns' => ['Class'], 'expect' => ['return' => true, 'columns' => ['Class'] ], ],
            ['columns' => ['Frequency'], 'expect' => ['return' => true, 'columns' => ['Frequency'] ], ],
            ['columns' => ['RelativeFrequency'], 'expect' => ['return' => true, 'columns' => ['RelativeFrequency'] ], ],
            ['columns' => ['ClassValue'], 'expect' => ['return' => true, 'columns' => ['ClassValue'] ], ],
            ['columns' => ['ClassValue * Frequency'], 'expect' => ['return' => true, 'columns' => ['ClassValue * Frequency'] ], ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $ft->setColumns2Show($ft->getValidColumns2Show());
            $this->assertSame($case['expect']['return'],$ft->setColumns2Show($case['columns']));
            $this->assertSame($case['expect']['columns'],$ft->getColumns2Show());
        }
    }

    public function test_getData2Show_return_empty_array_with_unsettable_data(): void
    {
        $cases = [
            ['classRange' => null, 'data' => null, 'expect' => [] ],
            ['classRange' => 10, 'data' => null, 'expect' => [] ],
            ['classRange' => 10, 'data' => true, 'expect' => [] ],
            ['classRange' => 10, 'data' => false, 'expect' => [] ],
            ['classRange' => 10, 'data' => 0, 'expect' => [] ],
            ['classRange' => 10, 'data' => 1.2, 'expect' => [] ],
            ['classRange' => 10, 'data' => 'hoge', 'expect' => [] ],
            ['classRange' => 10, 'data' => [], 'expect' => [] ],
            ['classRange' => 10, 'data' => [null], 'expect' => [] ],
            ['classRange' => 10, 'data' => [true], 'expect' => [] ],
            ['classRange' => 10, 'data' => [false], 'expect' => [] ],
            ['classRange' => 10, 'data' => ['hoge'], 'expect' => [] ],
            ['classRange' => 10, 'data' => [[]], 'expect' => [] ],
            ['classRange' => 10, 'data' => [[0]], 'expect' => [] ],
        ];
        $ft = new FrequencyTable();

        foreach ($cases as $index => $case) {
            $ft->setClassRange($case['classRange']);
            $ft->setData($case['data']);
            $this->assertSame($case['expect'], $ft->getData2Show());
        }
    }

    public function test_getData2Show_can_get_data_2_show(): void
    {
        $ft = new FrequencyTable();
        $classRange = 10;
        $ft->setClassRange($classRange);
        $data = [0,5,10,15,20];
        $ft->setData($data);
        $data2Show = $ft->getData2Show();
        $this->assertIsArray($data2Show);
        $this->assertTrue(!empty($data2Show));
        foreach($ft->getColumns2Show() as $column) {
            $this->assertTrue(in_array($column, $data2Show[0]));
        }
        $classes = $ft->getClasses();
        $this->assertFalse(empty($classes));
        foreach($classes as $index => $class) {
            $s = number_format($class['bottom']) . $this->classSeparator . number_format($class['top']);
            $this->assertTrue(in_array($s, array_column($data2Show,'Class')));
        }
        $this->assertContains('Total', array_column($data2Show,'Class'));
    }

    public function test_getData2Show_can_switch_visibility_of_mean(): void
    {
        $ft = new FrequencyTable();
        $classRange = 10;
        $ft->setClassRange($classRange);
        $data = [0,5,10,15,20];
        $ft->setData($data);
        $ft->meanOn();
        $data2Show = $ft->getData2Show();
        $this->assertContains('Mean', array_column($data2Show,'Class'));
        $ft->meanOff();
        $data2Show = $ft->getData2Show();
        $this->assertFalse(in_array('Mean', array_column($data2Show,'Class')));
    }

    public function test_getDataOfEachClass_can_get_data_4_each_class_correctly(): void
    {
        $cases = [
            ['classRange' => null, 'data' => null, 'expect' => [], ],
            ['classRange' => 10, 'data' => null, 'expect' => [], ],
            ['classRange' => null, 'data' => [0], 'expect' => [], ],
            ['classRange' => 10, 'data' => [0], 'expect' => [
                    [
                        'Class' => '0 ~ 10',
                        'Frequency' => 1,
                        'CumulativeFrequency' => 1,
                        'RelativeFrequency' => '1.00',
                        'CumulativeRelativeFrequency' => '1.00',
                        'ClassValue' => '5.0',
                        'ClassValue * Frequency' => '5.0',
                    ],
                ], 
            ],
            ['classRange' => 10, 'data' => [0, 5, 10, 15, 20], 'expect' => [
                    [
                        'Class' => '0 ~ 10',
                        'Frequency' => 2,
                        'CumulativeFrequency' => 2,
                        'RelativeFrequency' => '0.40',
                        'CumulativeRelativeFrequency' => '0.40',
                        'ClassValue' => '5.0',
                        'ClassValue * Frequency' => '10.0',
                    ],
                    [
                        'Class' => '10 ~ 20',
                        'Frequency' => 2,
                        'CumulativeFrequency' => 4,
                        'RelativeFrequency' => '0.40',
                        'CumulativeRelativeFrequency' => '0.80',
                        'ClassValue' => '15.0',
                        'ClassValue * Frequency' => '30.0',
                    ],
                    [
                        'Class' => '20 ~ 30',
                        'Frequency' => 1,
                        'CumulativeFrequency' => 5,
                        'RelativeFrequency' => '0.20',
                        'CumulativeRelativeFrequency' => '1.00',
                        'ClassValue' => '25.0',
                        'ClassValue * Frequency' => '25.0',
                    ],
                ], 
            ],
        ];
        $ft = new FrequencyTable();
        $ft->setColumns2Show(['Class', 'Frequency']);
        foreach ($cases as $index => $case) {
            $ft->setClassRange($case['classRange']);
            $ft->setData($case['data']);
            $this->assertSame($case['expect'], $ft->getDataOfEachClass());
        }
    }

    public function test_filterData2Show_can_filter_data_2_show_correctly(): void
    {
        $ft = new FrequencyTable();
        $classRange = 10;
        $ft->setClassRange($classRange);
        $data = [0,5,10,15,20];
        $ft->setData($data);
        $combinations = $this->getAllCombinations($this->validColumns2Show);
        foreach($combinations as $index => $combination) {
            if (empty($combination)) continue;
            $ft->setColumns2Show($combination);
            $filtered = $ft->filterData2Show($ft->getData2Show());
            foreach($this->validColumns2Show as $key) {
                if (in_array($key,$combination)) {
                    $this->assertTrue(array_key_exists($key,$filtered[0]));
                } else {
                    $this->assertFalse(array_key_exists($key,$filtered[0]));
                }
            }
        }
    }

    public function test_parse_return_null_under_invalid_condition(): void
    {
        $cases = [
            ['classRange' => null, 'data' => null, ],
            ['classRange' => true, 'data' => null, ],
            ['classRange' => false, 'data' => null, ],
            ['classRange' => 0, 'data' => null, ],
            ['classRange' => 1.2, 'data' => null, ],
            ['classRange' => -1, 'data' => null, ],
            ['classRange' => "10", 'data' => null, ],
            ['classRange' => 10, 'data' => null, ],
            ['classRange' => [], 'data' => null, ],
            ['classRange' => 10, 'data' => true, ],
            ['classRange' => 10, 'data' => false, ],
            ['classRange' => 10, 'data' => 0, ],
            ['classRange' => 10, 'data' => 1.2, ],
            ['classRange' => 10, 'data' => "10", ],
            ['classRange' => 10, 'data' => [], ],
            ['classRange' => 10, 'data' => [[10]], ],
            ['classRange' => 10, 'data' => [null], ],
            ['classRange' => 10, 'data' => [true], ],
            ['classRange' => 10, 'data' => [false], ],
            ['classRange' => 10, 'data' => ["10"], ],
            ['classRange' => 10, 'data' => [10,20,"30"], ],
            ['classRange' => 10, 'data' => [1.2,3.4,"4.6"], ],
        ];

        foreach($cases as $index => $case) {
            $ft = new FrequencyTable();
            $ft->setClassRange($case['classRange']);
            $ft->setData($case['data']);
            $this->assertNull($ft->parse());
            unset($ft);
        }
    }

    public function test_parse_can_return_valid_data(): void
    {
        $ft = new FrequencyTable();
        $ft->setClassRange(10);
        $data = [0,5,10,15,20];
        $ft->setData($data);
        $expect = [
            'classRange' => 10,
            'data' => $data,
            'Max' => 20,
            'Min' => 0,
            'DataRange' => 20,
            'Mode' => 5,
            'Total' => 5,
            'Mean' => 13,
            'Median' => 10,
            'MedianClass' => ['index'=>1,'bottom'=>10,'top'=>20],
            'FirstQuartile' => 2.5,
            'ThirdQuartile' => 17.5,
            'InterQuartileRange' => 15.0,
            'QuartileDeviation' => 7.5,
            'Classes' => [['bottom'=>0,'top'=>10],['bottom'=>10,'top'=>20],['bottom'=>20,'top'=>30]],
            'Frequencies' => [2,2,1],
            'FrequencyTable' => $ft->show(['Mean'=>true,'STDOUT'=>false,'ReturnValue'=>true]),
        ];
        $this->assertSame($expect,$ft->parse());
    }

    public function test_xsv_can_return_null_with_invalid_parameters(): void
    {
        $cases = [
            ['path' => null, 'separator' => null, 'quatation' => null, ],
            ['path' => true, 'separator' => null, 'quatation' => null, ],
            ['path' => false, 'separator' => null, 'quatation' => null, ],
            ['path' => '', 'separator' => null, 'quatation' => null, ],
            ['path' => 0, 'separator' => null, 'quatation' => null, ],
            ['path' => 1.2, 'separator' => null, 'quatation' => null, ],
            ['path' => [], 'separator' => null, 'quatation' => null, ],
            ['path' => ['path'], 'separator' => null, 'quatation' => null, ],
            ['path' => 'path', 'separator' => null, 'quatation' => null, ],
            ['path' => 'path', 'separator' => null, 'quatation' => 0, ],
            ['path' => 'path', 'separator' => null, 'quatation' => 1, ],
            ['path' => 'path', 'separator' => null, 'quatation' => 0.0, ],
            ['path' => 'path', 'separator' => null, 'quatation' => 1.0, ],
            ['path' => 'path', 'separator' => null, 'quatation' => '', ],
            ['path' => 'path', 'separator' => null, 'quatation' => [], ],
            ['path' => 'path', 'separator' => null, 'quatation' => [true], ],
            ['path' => 'path', 'separator' => null, 'quatation' => [false], ],

            ['path' => null, 'separator' => '', 'quatation' => null, ],
            ['path' => true, 'separator' => '', 'quatation' => null, ],
            ['path' => false, 'separator' => '', 'quatation' => null, ],
            ['path' => '', 'separator' => '', 'quatation' => null, ],
            ['path' => 0, 'separator' => '', 'quatation' => null, ],
            ['path' => 1.2, 'separator' => '', 'quatation' => null, ],
            ['path' => [], 'separator' => '', 'quatation' => null, ],
            ['path' => ['path'], 'separator' => '', 'quatation' => null, ],
            ['path' => 'path', 'separator' => '', 'quatation' => null, ],
            ['path' => 'path', 'separator' => '', 'quatation' => 0, ],
            ['path' => 'path', 'separator' => '', 'quatation' => 1, ],
            ['path' => 'path', 'separator' => '', 'quatation' => 0.0, ],
            ['path' => 'path', 'separator' => '', 'quatation' => 1.0, ],
            ['path' => 'path', 'separator' => '', 'quatation' => '', ],
            ['path' => 'path', 'separator' => '', 'quatation' => [], ],
            ['path' => 'path', 'separator' => '', 'quatation' => [true], ],
            ['path' => 'path', 'separator' => '', 'quatation' => [false], ],
        ];
        $ft = new FrequencyTable();
        foreach ($cases as $index => $case) {
            $this->assertNull($ft->xsv($case['path'], ',', $case['quatation']));
        }
    }

    public function test_csv_can_save_csv(): void
    {
        $data = [0, 5, 10, 15, 20];
        $columns2Show = ['Class', 'Frequency', ];
        $expect = [
            ['Class', 'Frequency', ],
            ['0 ~ 10', '2', ],
            ['10 ~ 20', '2', ],
            ['20 ~ 30', '1', ],
            ['Total', '5',],
        ];
        $path = 'storage/test.csv';
        $splitter = ',';
        $ft = new FrequencyTable();
        $ft->setClassRange(10);
        $ft->setData($data);
        $ft->setColumns2Show($columns2Show);
        $this->clearStorage();
        $this->assertIsInt($ft->csv($path));
        $this->assertTrue(file_exists($path));
        $csv = array_map(fn($value): array => str_getcsv($value, $splitter), file($path, FILE_IGNORE_NEW_LINES));
        $this->assertSame($expect, $csv);
    }


    public function test_csv_can_return_csv(): void
    {
        $data = [0, 5, 10, 15, 20];
        $columns2Show = ['Class', 'Frequency', ];
        $expect = [
            ['Class', 'Frequency', ],
            ['0 ~ 10', '2', ],
            ['10 ~ 20', '2', ],
            ['20 ~ 30', '1', ],
            ['Total', '5',],
        ];
        $cases = [
            ['path' => null, ],
            ['path' => '', ],
            ['path' => '0', ],
        ];
        $splitter = ',';
        $eol = "\n";
        $ft = new FrequencyTable();
        $ft->setClassRange(10);
        $ft->setData($data);
        $ft->setColumns2Show($columns2Show);
        foreach ($cases as $index => $case) {
            $return = $ft->csv($case['path']);
            $this->assertIsString($return);
            $csv = array_map(fn($value): array => str_getcsv($value, $splitter), explode($eol, $return));
            array_pop($csv);
            $this->assertSame($expect, $csv);
        }
    }

    public function test_tsv_can_save_tsv(): void
    {
        $data = [0, 5, 10, 15, 20];
        $columns2Show = ['Class', 'Frequency', ];
        $expect = [
            ['Class', 'Frequency', ],
            ['0 ~ 10', '2', ],
            ['10 ~ 20', '2', ],
            ['20 ~ 30', '1', ],
            ['Total', '5',],
        ];
        $path = 'storage/test.tsv';
        $splitter = "\t";
        $ft = new FrequencyTable();
        $ft->setClassRange(10);
        $ft->setData($data);
        $ft->setColumns2Show($columns2Show);
        $this->clearStorage();
        $this->assertIsInt($ft->tsv($path));
        $this->assertTrue(file_exists($path));
        $csv = array_map(fn($value): array => str_getcsv($value, $splitter), file($path, FILE_IGNORE_NEW_LINES));
        $this->assertSame($expect, $csv);
    }

    public function test_csv_can_return_tsv(): void
    {
        $data = [0, 5, 10, 15, 20];
        $columns2Show = ['Class', 'Frequency', ];
        $expect = [
            ['Class', 'Frequency', ],
            ['0 ~ 10', '2', ],
            ['10 ~ 20', '2', ],
            ['20 ~ 30', '1', ],
            ['Total', '5',],
        ];
        $cases = [
            ['path' => null, ],
            ['path' => '', ],
            ['path' => '0', ],
        ];
        $splitter = "\t";
        $eol = "\n";
        $ft = new FrequencyTable();
        $ft->setClassRange(10);
        $ft->setData($data);
        $ft->setColumns2Show($columns2Show);
        foreach ($cases as $index => $case) {
            $return = $ft->tsv($case['path']);
            $this->assertIsString($return);
            $csv = array_map(fn($value): array => str_getcsv($value, $splitter), explode($eol, $return));
            array_pop($csv);
            $this->assertSame($expect, $csv);
        }
    }

    public function test_html_can_save_html(): void
    {
        $data = [0, 5, 10, 15, 20];
        $columns2Show = ['Class', 'Frequency', ];
        $expect = "<table>
<tr><td>Class</td><td>Frequency</td></tr>
<tr><td>0 ~ 10</td><td>2</td></tr>
<tr><td>10 ~ 20</td><td>2</td></tr>
<tr><td>20 ~ 30</td><td>1</td></tr>
<tr><td>Total</td><td>5</td></tr>
</table>
";
        $path = 'storage/test.html';
        if (file_exists($path)) unlink($path);
        $splitter = "</td><td>";
        $ft = new FrequencyTable();
        $ft->setClassRange(10);
        $ft->setData($data);
        $ft->setColumns2Show($columns2Show);
        $this->clearStorage();
        $this->assertIsInt($ft->html($path));
        $this->assertTrue(file_exists($path));
        $this->assertSame($expect, file_get_contents($path));
    }

    public function test_html_can_return_html(): void
    {
        $data = [0, 5, 10, 15, 20];
        $columns2Show = ['Class', 'Frequency', ];
        $expect = "<table>
<tr><td>Class</td><td>Frequency</td></tr>
<tr><td>0 ~ 10</td><td>2</td></tr>
<tr><td>10 ~ 20</td><td>2</td></tr>
<tr><td>20 ~ 30</td><td>1</td></tr>
<tr><td>Total</td><td>5</td></tr>
</table>
";
        $path = null;
        $ft = new FrequencyTable();
        $ft->setClassRange(10);
        $ft->setData($data);
        $ft->setColumns2Show($columns2Show);
        $this->clearStorage();
        $html = $ft->html($path);
        $this->assertSame($expect, $html);
    }

    public function test_markdown_can_return_null_with_invalid_param(): void
    {
        $cases = [
            ['path' => true, ],
            ['path' => false, ],
            ['path' => 0, ],
            ['path' => 1.2, ],
            ['path' => [], ],
        ];
        $ft = new FrequencyTable();

        foreach ($cases as $index => $case) {
            $this->assertNull($ft->markdown($case['path']));
        }
    }

    public function test_markdown_can_save_markdown(): void
    {
        $data = [0, 5, 10, 15, 20];
        $columns2Show = ['Class', 'Frequency', ];
        $expect = "|Class|Frequency|
|:---:|:---:|
|0 ~ 10|2|
|10 ~ 20|2|
|20 ~ 30|1|
|Total|5|
";
        $path = 'storage/test.md';
        if (file_exists($path)) unlink($path);
        $ft = new FrequencyTable();
        $ft->setClassRange(10);
        $ft->setData($data);
        $ft->setColumns2Show($columns2Show);
        $this->clearStorage();
        $this->assertIsInt($ft->markdown($path));
        $this->assertTrue(file_exists($path));
        $this->assertSame($expect, file_get_contents($path));
    }

    public function test_markdown_can_return_markdown(): void
    {
        $data = [0, 5, 10, 15, 20];
        $columns2Show = ['Class', 'Frequency', ];
        $expect = "|Class|Frequency|
|:---:|:---:|
|0 ~ 10|2|
|10 ~ 20|2|
|20 ~ 30|1|
|Total|5|
";
        $path = null;
        $ft = new FrequencyTable();
        $ft->setClassRange(10);
        $ft->setData($data);
        $ft->setColumns2Show($columns2Show);
        $this->assertSame($expect, $ft->markdown($path));
    }

    public function test_save_can_return_null_with_invalid_parameter(): void
    {
        $cases = [
            ['path' => null, ],
            ['path' => true, ],
            ['path' => false, ],
            ['path' => 0, ],
            ['path' => 1.2, ],
            ['path' => [], ],
            ['path' => ['hoge.md'], ],
            ['path' => '', ],
            ['path' => 'hoge.txt', ],
            ['path' => 'hoge.php', ],
            ['path' => 'hoge.png', ],
        ];
        $ft = new FrequencyTable();

        foreach ($cases as $index => $case) {
            $this->assertNull($ft->save($case['path']));
        }
    }

    public function test_save_can_save_in_specified_format(): void
    {
        $cases = [
            ['path' => 'storage/test.csv', ],
            ['path' => 'storage/test.CSV', ],
            ['path' => 'storage/test.Csv', ],
            ['path' => 'storage/test.CSv', ],
            ['path' => 'storage/test.CsV', ],
            ['path' => 'storage/test.cSv', ],
            ['path' => 'storage/test.cSV', ],
            ['path' => 'storage/test.csV', ],

            ['path' => 'storage/test.tsv', ],
            ['path' => 'storage/test.TSV', ],
            ['path' => 'storage/test.Tsv', ],
            ['path' => 'storage/test.Tsv', ],
            ['path' => 'storage/test.TSv', ],
            ['path' => 'storage/test.TsV', ],
            ['path' => 'storage/test.tSv', ],
            ['path' => 'storage/test.tSV', ],
            ['path' => 'storage/test.tsV', ],

            ['path' => 'storage/test.html', ],
            ['path' => 'storage/test.HTML', ],
            ['path' => 'storage/test.Html', ],
            ['path' => 'storage/test.hTml', ],
            ['path' => 'storage/test.htMl', ],
            ['path' => 'storage/test.htmL', ],
            ['path' => 'storage/test.HTml', ],
            ['path' => 'storage/test.HtMl', ],
            ['path' => 'storage/test.HtmL', ],
            ['path' => 'storage/test.hTMl', ],
            ['path' => 'storage/test.hTmL', ],
            ['path' => 'storage/test.htML', ],
            ['path' => 'storage/test.HTMl', ],
            ['path' => 'storage/test.HTmL', ],
            ['path' => 'storage/test.HtML', ],
            ['path' => 'storage/test.hTML', ],

            ['path' => 'storage/test.md', ],
            ['path' => 'storage/test.MD', ],
            ['path' => 'storage/test.Md', ],
            ['path' => 'storage/test.mD', ],
        ];
        $ft = new FrequencyTable();
        $ft->setClassRange(10);
        $ft->setData([0,5,10,15,20]);

        foreach ($cases as $index => $case) {
            $this->clearStorage();
            $this->assertIsInt($ft->save($case['path']));
            $this->assertTrue(file_exists($case['path']));
        }
    }
}
