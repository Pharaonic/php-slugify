<?php

namespace Pharaonic\Slugify\Facades;

abstract class Facade
{
    protected static $resolvedInstance;

    /**
     * Get the facade accessor for the underlying instance.
     *
     * @return void
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        throw new \RuntimeException('Facade does not implement getFacadeAccessor.');
    }

    /**
     * Resolve the instance for the facade.
     *
     * @return object
     */
    protected static function resolveInstance()
    {
        $class = static::getFacadeAccessor();

        if (!static::$resolvedInstance) {
            static::$resolvedInstance = new $class();
        }

        return static::$resolvedInstance;
    }

    /**
     * Get the facade accessor for the underlying instance.
     *
     * @param  string $method
     * @param  mixed  $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::resolveInstance();

        return $instance->$method(...$args);
    }
}
