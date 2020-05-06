<?php declare(strict_types = 1);

namespace Happysir\Enum\Test\Enum;

use Happysir\Enum\BaseEnum;
use Happysir\Enum\Test\TestCase;

class TEnum extends BaseEnum
{
    public const TEST1 = '1';
    
    public const TEST2 = '2';
    
    protected string $name = '';
    
    public static function TEST1() : TEnum
    {
        $init = static function (TEnum $enum) {
            $enum->setName('TEST1');
        };
        
        return self::newStatic(
            self::TEST1, $init
        );
    }
    
    public static function TEST2() : TEnum
    {
        $init = static function (TEnum $enum) {
            $enum->setName('TEST2');
        };
        
        return self::newStatic(
            self::TEST2, $init
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
class TestEnum extends TestCase
{
    public function testEnum() : void
    {
        $this->assertEquals(TEnum::TEST1, '1');
        $this->assertEquals(TEnum::TEST2, '2');
        
        $this->assertEquals(
            [
                'TEST1',
                'TEST2'
            ],
            TEnum::keys()
        );
        $this->assertEquals(
            [
                '1',
                '2',
            ],
            TEnum::values()
        );
        
        $this->assertEquals(
            [
                'TEST1' => '1',
                'TEST2' => '2'
            ],
            TEnum::toArray()
        );
        
        $this->assertTrue(TEnum::isValidKey('TEST1'));
        $this->assertTrue(TEnum::isValid('2'));
        
        $this->assertTrue(TEnum::TEST2()->equals(TEnum::TEST2()));
        $this->assertFalse(TEnum::TEST1()->equals(TEnum::TEST2()));
        
        $this->assertEquals(
            [
                ['value' => '1', 'key' => 'TEST1'],
                ['value' => '2', 'key' => 'TEST2'],
            ],
            TEnum::toList()
        );
    }
}
