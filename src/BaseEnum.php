<?php

declare(strict_types = 1);

namespace Happysir\Enum;

use BadMethodCallException;
use MyCLabs\Enum\Enum;

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
     * @var bool
     */
    protected static bool $initialized = false;
    
    /**
     * @param $value
     * @return \Happysir\Enum\BaseEnum|null
     */
    public static function for($value) : ?BaseEnum
    {
        return self::newOnce($value);
    }
    
    /**
     * @param string                  $key
     * @param \Happysir\Enum\BaseEnum $enum
     */
    protected static function cacheEnum(string $key, BaseEnum $enum) : void
    {
        static::$enumCacheMapping[static::class][$key] = $enum;
    }
    
    /**
     * Returns instances of the Enum class of all Enum constants
     *
     * @psalm-pure
     * @psalm-return array<string, static>
     * @return static[] Constant name in key, Enum instance in value
     */
    public static function values()
    {
        if (!self::$initialized) {
            self::init();
        }
        
        return static::$enumCacheMapping[static::class];
    }
    
    /**
     * @param string $name
     * @param array  $arguments
     * @return \Happysir\Enum\BaseEnum
     */
    public static function __callStatic($name, $arguments)
    {
        $array = static::toArray();
        if (isset($array[$name]) || array_key_exists($name, $array)) {
            return static::newOnce($array[$name]);
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
     * toList
     *
     * @param callable|null $transform
     * @return array
     */
    public static function toList(callable $transform = null) : array
    {
        $list = [];
        foreach (self::values() as $enum) {
            $list[] = $transform === null ? $enum->toFields() : $transform($enum);
        }
        
        return $list;
    }
    
    /**
     * init
     */
    protected static function init() : void
    {
        if (self::$initialized) {
            return;
        }
        
        $class = static::class;
        foreach (static::toArray() as $constant => $value) {
            if (isset(static::$enumCacheMapping[$class][$constant])) {
                continue;
            }
            
            // 函数名是常量名
            if (method_exists($class, $constant)) {
                static::cacheEnum(
                    $constant,
                    static::$constant()
                );
                continue;
            }
            
            // 函数名是常量名转小驼峰
            $name = convert_to_camel($constant);
            if (method_exists($class, $name)) {
                static::cacheEnum(
                    $constant,
                    static::$name()
                );
                continue;
            }
            
            // 无匹配的自定义函数,生成默认的枚举类
            static::newOnce($value);
        }
        
        self::$initialized = true;
    }
    
    /**
     * @param               $value
     * @param callable|null $userCallable
     * @return \Happysir\Enum\BaseEnum
     */
    protected static function newOnce($value, callable $userCallable = null) : self
    {
        $key   = self::search($value);
        $class = static::class;
        if (isset(static::$enumCacheMapping[$class][$key])) {
            return static::$enumCacheMapping[$class][$key];
        }
        
        static::cacheEnum(
            $key,
            $enum = new static($value)
        );
        
        $userCallable($enum);
        
        return $enum;
    }
}

