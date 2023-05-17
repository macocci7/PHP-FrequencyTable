<?php declare(strict_types=1);

require('src/class/FrequencyTable.php');

use PHPUnit\Framework\TestCase;

final class FrequencyTableTest extends TestCase
{
    private $validColumns2Show = [
        'Class',
        'Frequency',
        'RelativeFrequency',
        'ClassValue',
        'ClassValue * Frequency',
    ];

    public function test_constructor_can_create_instance_without_params(): void
    {
        $ft = new FrequencyTable();
        $this->assertNull($ft->getData());
        $this->assertNull($ft->getClassRange());
        $this->assertNull($ft->getSum());
        $this->assertSame($this->validColumns2Show,$ft->getColumns2Show());
    }

    public function test_constructor_can_set_only_data(): void
    {
        $data = [10,30,40,45,50,52];
        $ft = new FrequencyTable(['data' => $data]);
        $this->assertSame($data, $ft->getData());
        $this->assertNull($ft->getClassRange());
        $this->assertEquals(0,$ft->getSum());
        $this->assertSame($this->validColumns2Show,$ft->getColumns2Show());
    }

    public function test_constructor_can_set_data(): void
    {
        $data = [10,30,40,45,50,52];
        $classRange = 20;
        $ft = new FrequencyTable(['data' => $data, 'classRange' => $classRange]);

        $this->assertSame($data, $ft->getData());
        $this->assertSame($classRange, $ft->getClassRange());
        $this->assertIsNumeric($ft->getSum());
        $this->assertSame($this->validColumns2Show,$ft->getColumns2Show());
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
            ['data' => null, 'expected' => false, ],
            ['data' => null, 'expected' => false, ],
            ['data' => null, 'expected' => false, ],
            ['data' => null, 'expected' => false, ],
            ['data' => null, 'expected' => false, ],
        ];
        $ft = new FrequencyTable();
        
        foreach($cases as $index => $case) {
            $this->assertSame($case['expected'],$ft->isSettableData($case['data']));
        }
    }

    public function test_setData_can_set_null(): void
    {
        $ft = new FrequencyTable();
        $ft->setData(null);
        $this->assertNull($ft->getData());
        $this->assertNull($ft->getSum());
    }

    public function test_setData_can_set_empty_data(): void
    {
        $ft = new FrequencyTable();
        $ft->setData([]);
        $this->assertEmpty($ft->getData());
        $this->assertNull($ft->getSum());
    }

    public function test_setData_can_set_correct_data(): void
    {
        $data = [10,20,30,40,50];
        $ft = new FrequencyTable();
        $ft->setData($data);
        $this->assertSame($data, $ft->getData());
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
            [ 'classRange' => null, 'expect' => null, ],
            [ 'classRange' => -1, 'expect' => null, ],
            [ 'classRange' => 0, 'expect' => null, ],
            [ 'classRange' => 0.1, 'expect' => 0.1, ],
            [ 'classRange' => 1, 'expect' => 1, ],
            [ 'classRange' => 0x539, 'expect' => 1337, ],
            [ 'classRange' => 0b10100111001, 'expect' => 1337, ],
            [ 'classRange' => 1337e0, 'expect' => 1337, ],
            [ 'classRange' => '0.1', 'expect' => null, ],
            [ 'classRange' => '1', 'expect' => null, ],
            [ 'classRange' => '0x539', 'expect' => null, ],
            [ 'classRange' => '0b10100111001', 'expect' => null, ],
            [ 'classRange' => '1337e0', 'expect' => null, ],
            [ 'classRange' => 'hoge', 'expect' => null, ],
            [ 'classRange' => [], 'expect' => null, ],
            [ 'classRange' => [1], 'expect' => null, ],
            ['classRange' => PHP_INT_MAX, 'expect' => PHP_INT_MAX, ],
            ['classRange' => PHP_FLOAT_MAX, 'expect' => PHP_FLOAT_MAX, ],
            ['classRange' => PHP_INT_MAX + 1, 'expect' => PHP_INT_MAX + 1, ],
            ['classRange' => PHP_FLOAT_MAX + 1, 'expect' => PHP_FLOAT_MAX + 1, ],
        ];
        $ft = new FrequencyTable();

        foreach($cases as $index => $case) {
            $ft->setClassRange($case['classRange']);
            $this->assertEquals($case['expect'], $ft->getClassRange());
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

    public function test_getMin_can_get_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => 'hoge', 'expect' => null, ],
            ['data' => 10, 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => ['hoge','huga'], 'expect' => 'hoge', ],
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
            ['data' => ['hoge','huga'], 'expect' => 'huga', ],
            ['data' => [10], 'expect' => 10, ],
            ['data' => [-10], 'expect' => -10, ],
            ['data' => [-10,0,5], 'expect' => 5, ],
        ];
        $ft = new FrequencyTable();

        foreach ($cases as $index => $case) {
            $this->assertSame($case['expect'],$ft->getMax($case['data']));
        }
    }

    public function test_setSum_and_getSum_can_work_correctly(): void
    {
        $cases = [
            ['classRange' => 2, 'data' => null, 'setSumParam' => null, 'expect' => null, ],
            ['classRange' => 2, 'data' => null, 'setSumParam' => [], 'expect' => null, ],
            ['classRange' => 2, 'data' => null, 'setSumParam' => 0, 'expect' => null, ],
            ['classRange' => 2, 'data' => null, 'setSumParam' => true, 'expect' => null, ],
            ['classRange' => 2, 'data' => null, 'setSumParam' => false, 'expect' => null, ],
            ['classRange' => 2, 'data' => null, 'setSumParam' => [0,1,2,3,4,], 'expect' => 10, ],   // This returns the sum of values in array.
            ['classRange' => 2, 'data' => [0,1,2,3,4,], 'setSumParam' => null, 'expect' => 5, ],    // This returns the sum of Frequencies, ie the number of elements.
        ];
        $ft = new FrequencyTable();
        
        foreach($cases as $index => $case) {
            $ft->setClassRange($case['classRange']);
            $ft->setData($case['data']);
            $ft->setSum($case['setSumParam']);
            $this->assertSame($case['expect'],$ft->getSum());
        }
    }

    // Test Codes Must Follows.
}
