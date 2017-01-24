<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */
namespace oat\tao\helpers\test;

use oat\tao\test\TaoPhpUnitTestRunner;


class ArrayHelperTest extends TaoPhpUnitTestRunner
{
    /**
     * @dataProvider arrayProvider
     */
    public function testArrayUnique($testArray, $expectedArray)
    {
        $result = \tao_helpers_Array::array_unique($testArray);
        $this->assertEquals($expectedArray, $result);
    }

    public function arrayProvider()
    {
        $objectA = new myFakeObject(1,2,3);
        $objectB = new myFakeObject(4,5,6);
        $objectC = new myFakeObject('abc','def','ghi');
        $objectD = new myFakeObject('plop','test','foo');
        $objectDPrime = new myFakeObject('plop','test','foo');
        return [
            [[$objectA, $objectB, $objectA, $objectD], [$objectA, $objectB, 3=>$objectD]],
            [[$objectA, 3=>$objectB, $objectD, $objectDPrime], [$objectA, 3=>$objectB, $objectD]],
            [[$objectA, $objectB, $objectC, $objectD, $objectA, $objectB, $objectC, $objectD], [$objectA, $objectB, $objectC, $objectD]],
            [[], []],
            [[$objectC, $objectC, $objectC, $objectC], [$objectC]],
            [[2=>$objectC, 3=>$objectC, 56=>$objectC, 42=>$objectC], [2=>$objectC]],
            [['aaa'=>$objectA, 'bbb'=>$objectB, 'ccc'=>$objectC, 42=>$objectC], ['aaa'=>$objectA, 'bbb'=>$objectB, 'ccc'=>$objectC]],
        ];
    }
    
    /**
     * @dataProvider containsOnlyValueProvider
     */
    public function testContainsOnlyValue($value, array $container, $strict, $exceptAtIndex, $expectedValue)
    {
        $this->assertSame($expectedValue, \tao_helpers_Array::containsOnlyValue($value, $container, $strict, $exceptAtIndex));
    }
    
    public function containsOnlyValueProvider()
    {
        return [
            [1, [1, 1, 1], true, array(), true],
            [1, [1, 1, '1'], true, array(), false],
            [1, [1, 1, '1'], false, array(), true],
            [1, [1, 1, '1'], true, array(2), true],
            [1, ['1', 1, '1'], true, array(2), false],
            [1, ['1', 1, '1'], true, array(0, 2), true],
            [1, [], true, array(), false],
            [0, [1, 2, 3], true, array(), false],
            [0, [1, 2, 3], false, array(), false],
            [0, [1, 2, 3], false, array(0, 1, 2), false],
            [[1, 2, 3], [1, 2, 3], false, array(), false],
            [[1, 2, 3], [1, 2, 3], true, array(), false],
            [[1, 2, 3], [1, 2, 3], false, array(0, 1, 2), false]
        ];
    }
    
    /**
     * @dataProvider arrayContainsOnlyValueProvider
     */
    public function testArraysContainOnlyValue(array $containers, $value, $exceptNContainers, array $exceptAtIndex, $expectedInvalidContainers, $expected)
    {
        $invalidContainers = [];
        $this->assertSame($expected, \tao_helpers_Array::arraysContainOnlyValue($containers, $value, $exceptNContainers, $exceptAtIndex, $invalidContainers));
        $this->assertEquals($expectedInvalidContainers, $invalidContainers);
    }
    
    public function arrayContainsOnlyValueProvider()
    {
        return [
            [
                [
                    ['1', '1', '1'],
                    ['1', '1', '1']
                ], '1', 0, [], [], true
            ],
            
            [
                [
                    ['1', '1', '1'],
                    ['1', '1', '2']
                ], '1', 0, [], [1], false
            ],
            
            [
                [
                    ['1', '1', '2'],
                    ['1', '1', '1'],
                    ['2', '1', '1']
                ], '1', 0, [], [0, 2], false
            ],
            
            [
                [
                    ['1', '2', '1'],
                    ['1', '1', '1']
                ], '1', 0, [], [0], false
            ],
            
            [
                [
                    ['1', '2', '1'],
                    ['1', '1', '1']
                ], '1', 0, [1], [], true
            ],
            
            [
                [
                    ['4', '5', '6'],
                    ['1', '8', '8'],
                    ['2', '8', '8']
                ], '8', 1, [0], [0], true
            ],
            
            [
                [
                    ['1', '8', '8'],
                    ['4', '5', '6'],
                    ['2', '8', '8']
                ], '8', 1, [0], [1], true
            ],
            
            [
                [
                    ['1', '8', '8'],
                    ['2', '8', '8'],
                    ['4', '5', '6'],
                ], '8', 0, [0], [2], false
            ],
            
            [
                [
                    ['1', '8', '8'],
                    ['2', '8', '8'],
                    ['4', '5', '6'],
                    ['4', '5', '6'],
                ], '8', 2, [0], [2, 3], true
            ],
            
            [
                [
                    ['1', '2', '8', '8'],
                    ['2', '3', '8', '8'],
                    ['4', '0', '5', '6'],
                    ['4', '0', '5', '6'],
                ], '8', 2, [0, 1], [2, 3], true
            ],
            
            [
                [
                    ['1', '2', '8', '8'],
                    ['2', '3', '4', '8'],
                    ['4', '0', '5', '6'],
                    ['4', '0', '5', '6'],
                ], '8', 2, [0, 1], [1, 2, 3], false
            ],
            
            [
                [], '8', 2, [0, 1], [], false
            ],
            
            [
                [
                    ['1', '2', '8', '8']
                ], '7', 1, [], [0], true
            ],
            
            [
                [
                    ['8', '8', '8', '8'],
                    ['8', '8', '8', '8']
                ], '8', 1, [], [], false
            ],
            
            [
                [
                    ['8', '8', '8', '8'],
                    ['8', '8', '8', '8']
                ], '8', 0, [], [], true
            ],
            
            [
                [
                    ['8', '8', '8', '8'],
                    ['8', '8', '8', '8']
                ], '8', -1, [], [], true
            ],
            
            [
                [
                    ['8', '8', '8', '8'],
                    ['8', '8', '8', '8']
                ], '8', 2, [], [], false
            ],
            
            [
                [
                    ['8', '8', '8', '8'],
                    ['8', '8', '8', '8']
                ], '8', 0, [0, 1, 2, 3], [0, 1], false
            ],
            
            [
                [
                    ['8', '8', '8', '8'],
                    ['8', '8', '8', '8']
                ], '8', 1, [], [], false
            ],
            
            [
                [
                    ['1', '2', '8', '8', '8', '8'],
                    ['1', '2', '8', '8', '8', '8']
                ], '8', 1, [0, 1], [], false
            ],
        ];
    }
}

class myFakeObject{
    private $a;
    private $b;
    private $c;

    public function __construct($a, $b, $c)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    public function __equals($object){

        if($object instanceof myFakeObject){
            if($this->a === $object->getA()
            && $this->b === $object->getB()
            && $this->c === $object->getC()
            ){
                return true;
            }
        }
        return false;
    }

    public function getA(){
        return $this->a;
    }

    public function getB(){
        return $this->b;
    }

    public function getC(){
        return $this->c;
    }
}
