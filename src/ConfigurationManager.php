<?php

namespace Drewlabs\Packages\Http;

use Drewlabs\Core\Helpers\Arr;

class ConfigurationManager
{

    /**
     * Static class instance.
     *
     * @var self
     */
    private static $instance;

    /**
     * Configurations cache property.
     *
     * @var array
     */
    private $config;

    /**
     * Private constructor to prevent users from calling new on the current class.
     */
    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (null === static::$instance) {
            $self = new static();
            $self = drewlabs_core_create_attribute_setter('config', $self->config ?? [])($self);
            static::$instance = $self;
        }

        return static::$instance;
    }

    public function get($key = null, $default = null)
    {
        if (null === $key) {
            return array_merge($this->config, []);
        }
        $value = Arr::get($this->config, $key, $default);
        return null === $value ? ($default instanceof \Closure ? $default() : $default) : $value;
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return null !== Arr::get($offset, null);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->get($offset, null);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('Configuration manager class is a readonly class, operations changing the class state are not allowed');
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        throw new \RuntimeException('Configuration manager class is a readonly class, operations changing the class state are not allowed');
    }

    /**
     * Create and initialize the configuration from an array
     *
     * @param array $config
     * @return static
     */
    public static function configure(array $config)
    {
        $self = drewlabs_core_create_attribute_setter(
            'config',
            $config ?? []
        )(new static);
        static::$instance = $self;
        return $self;
    }
}
