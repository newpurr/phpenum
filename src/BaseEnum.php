<?php declare(strict_types = 1);

namespace Happysir\Enum;

use BadMethodCallException;
use MyCLabs\Enum\Enum;
use function array_key_exists;

/**
 * 基本枚举
 * Class BaseEnum
 */
abstract class BaseEnum extends Enum
{
    /**
     * @var BaseEnum[]
     */
    protected static array $enumCacheMapping = [];
    
    /**
     * @param mixed    $value
     * @param callable $userCallback
     * @return \Happysir\Lib\BaseEnum
     */
    protected static function newStatic(
        $value,
        callable $userCallback = null
    ) : self {
        if (isset(self::$enumCacheMapping[$value])) {
            return self::$enumCacheMapping[$value];
        }
        
        $enum = new static($value);
        
        if ($userCallback) {
            $userCallback($enum);
        }
        
        self::$enumCacheMapping[$value] = $enum;
        
        return $enum;
    }
    
    /**
     * @param string $name
     * @param array  $arguments
     * @return \Happysir\Lib\BaseEnum
     */
    public static function __callStatic($name, $arguments)
    {
        $array = static::toArray();
        if (isset($array[$name]) || array_key_exists($name, $array)) {
            return static::newStatic($array[$name]);
        }
        
        throw new BadMethodCallException(
            "No static method or enum constant '$name' in class "
            . static::class
        );
    }
    
    /**
     * @return array
     */
    protected function toFields() : array
    {
        return [
            'key'   => $this->getKey(),
            'value' => $this->getValue()
        ];
    }
    
    /**
     * getTranslations
     *
     * @return array
     */
    public static function toList() : array
    {
        static::init();
        
        $list = [];
        foreach (self::$enumCacheMapping as $enum) {
            $list[] = $enum->toFields();
        }
        
        return $list;
    }
    
    /**
     * @return void
     */
    protected static function init() : void
    {
        if (empty(static::$enumCacheMapping)) {
            foreach (static::toArray() as $constant => $value) {
                // 函数名是常量名
                if (method_exists(static::class, $constant)) {
                    static::$constant();
                }
                // 函数名是常量名转小驼峰
                $name = convert_to_camel($constant);
                if (method_exists(static::class, $constant)) {
                    static::$name();
                }
            }
        }
    }
}

