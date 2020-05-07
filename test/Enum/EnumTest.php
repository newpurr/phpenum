<?php

declare(strict_types = 1);

namespace Happysir\Enum\Test\Enum;

use Happysir\Enum\BaseEnum;
use Happysir\Enum\Test\TestCase;

class TEnum extends BaseEnum
{
    public const TEST1      = '1';
    
    public const TEST_DEMO2 = '2';
    
    protected string $name = '';
    
    public static function TEST1() : TEnum
    {
        return self::newOnce(
            self::TEST1,
            static function (TEnum $enum) {
                $enum->setName('TEST1');
            }
        );
    }
    
    public static function testDemo2() : TEnum
    {
        return self::newOnce(
            self::TEST_DEMO2,
            static function (TEnum $enum) {
                $enum->setName('TEST2');
            }
        );
    }
    
    /**
     * getName
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }
}

/**
 * Class TestEnum
 */
class EnumTest extends TestCase
{
    public function testEnum() : void
    {
        $this->assertEquals(
            [
                'TEST1'      => '1',
                'TEST_DEMO2' => '2'
            ],
            TEnum::toArray()
        );
        
        $this->assertNotEquals(
            [
                'TEST1' => TEnum::TEST1(),
            ],
            TEnum::values()
        );
        
        $this->assertEquals(
            [
                'TEST1'      => TEnum::TEST1(),
                'TEST_DEMO2' => TEnum::testDemo2(),
            ],
            TEnum::values()
        );
        
        $this->assertEquals(TEnum::TEST1, '1');
        $this->assertEquals(TEnum::TEST_DEMO2, '2');
        
        $this->assertEquals(
            [
                'TEST1',
                'TEST_DEMO2'
            ],
            TEnum::keys()
        );
        
        $this->assertTrue(TEnum::isValidKey('TEST1'));
        $this->assertTrue(TEnum::isValid('2'));
        
        $this->assertTrue(TEnum::testDemo2()->equals(TEnum::testDemo2()));
        $this->assertFalse(TEnum::TEST1()->equals(TEnum::testDemo2()));
        
        $this->assertEquals(
            [
                ['value' => '1', 'key' => 'TEST1'],
                ['value' => '2', 'key' => 'TEST_DEMO2'],
            ],
            TEnum::toList()
        );
        
        $this->assertEquals(
            [
                ['value' => '1', 'key' => 'TEST1', 'name' => 'TEST1'],
                ['value' => '2', 'key' => 'TEST_DEMO2', 'name' => 'TEST2'],
            ],
            TEnum::toList(
                static function (TEnum $enum) {
                    return [
                        'key'   => $enum->getKey(),
                        'value' => $enum->getValue(),
                        'name'  => $enum->getName(),
                    ];
                }
            )
        );
    }
}
